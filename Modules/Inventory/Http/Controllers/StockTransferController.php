<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\StockTransfer;
use Modules\Inventory\Entities\StockTransferItem;
use Modules\Inventory\Entities\Store;

class StockTransferController extends Controller
{
    /**
     * List stock transfers
     */
    public function index()
    {
        $transfers = StockTransfer::with(['fromStore', 'toStore', 'creator', 'items.item'])
            ->latest()
            ->paginate(15);

        return view('inventory::transfers.index', compact('transfers'));
    }

    /**
     * Show create transfer form
     */
    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        $items = Item::where('is_service', false)->where('is_active', true)->get();

        return view('inventory::transfers.create', compact('stores', 'items'));
    }

    /**
     * Execute stock transfer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id'   => 'required|exists:stores,id|different:from_store_id',
            'date'          => 'required|date',
            'items'         => 'required|array|min:1',
            'items.*.item_id'  => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'notes'         => 'nullable|string',
        ], [
            'to_store_id.different' => 'المستودع المحول إليه يجب أن يكون مختلفاً عن المستودع المصدر!',
        ]);

        DB::beginTransaction();
        try {
            $transfer = StockTransfer::create([
                'transfer_number' => 'TR-' . date('Ymd') . '-' . rand(100, 999),
                'from_store_id'   => $validated['from_store_id'],
                'to_store_id'     => $validated['to_store_id'],
                'date'            => $validated['date'],
                'status'          => 'completed',
                'notes'           => $validated['notes'] ?? null,
                'created_by'      => Auth::id(),
            ]);

            foreach ($validated['items'] as $it) {
                $itemId = $it['item_id'];
                $qty = (float) $it['quantity'];

                // 1. Deduct from source store
                $sourceStock = ItemStock::where('store_id', $validated['from_store_id'])
                    ->where('item_id', $itemId)
                    ->first();

                if (!$sourceStock || $sourceStock->quantity < $qty) {
                    $itemName = Item::find($itemId)->name ?? "صنف #$itemId";
                    throw new \Exception("الرصيد المتاح من ($itemName) في المستودع المصدر لا يكفي لإتمام التحويل!");
                }

                $sourceStock->decrement('quantity', $qty);

                // 2. Add to destination store
                $destStock = ItemStock::firstOrCreate(
                    [
                        'tenant_id' => Auth::user()->tenant_id,
                        'item_id'   => $itemId,
                        'store_id'  => $validated['to_store_id'],
                    ],
                    ['quantity' => 0, 'avg_cost' => $sourceStock->avg_cost]
                );
                $destStock->increment('quantity', $qty);

                // 3. Record transfer item line
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'item_id'           => $itemId,
                    'quantity'          => $qty,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.transfers.index')->with('success', 'تم تنفيذ إذن التحويل المخزني بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}

