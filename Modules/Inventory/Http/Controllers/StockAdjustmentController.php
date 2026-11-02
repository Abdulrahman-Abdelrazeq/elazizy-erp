<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\StockAdjustment;
use Modules\Inventory\Entities\StockAdjustmentItem;
use Modules\Inventory\Entities\Store;

class StockAdjustmentController extends Controller
{
    /**
     * List stock adjustments
     */
    public function index()
    {
        $adjustments = StockAdjustment::with(['store', 'creator', 'items.item'])
            ->latest()
            ->paginate(15);

        return view('inventory::adjustments.index', compact('adjustments'));
    }

    /**
     * Show create adjustment form
     */
    public function create()
    {
        $stores = Store::where('is_active', true)->get();
        $items = Item::where('is_service', false)->where('is_active', true)->get();

        return view('inventory::adjustments.create', compact('stores', 'items'));
    }

    /**
     * Execute stock adjustment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'store_id' => 'required|exists:stores,id',
            'type'     => 'required|in:addition,deficit,damage',
            'date'     => 'required|date',
            'items'    => 'required|array|min:1',
            'items.*.item_id'  => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'notes'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $adjustment = StockAdjustment::create([
                'adjustment_number' => 'ADJ-' . date('Ymd') . '-' . rand(100, 999),
                'store_id'          => $validated['store_id'],
                'type'              => $validated['type'],
                'date'              => $validated['date'],
                'notes'             => $validated['notes'] ?? null,
                'created_by'        => Auth::id(),
            ]);

            foreach ($validated['items'] as $it) {
                $itemId = $it['item_id'];
                $qty = (float) $it['quantity'];

                $stock = ItemStock::firstOrCreate(
                    [
                        'tenant_id' => Auth::user()->tenant_id,
                        'item_id'   => $itemId,
                        'store_id'  => $validated['store_id'],
                    ],
                    ['quantity' => 0, 'avg_cost' => Item::find($itemId)->buy_price ?? 0]
                );

                if ($validated['type'] === 'addition') {
                    // زيادة رصيد
                    $stock->increment('quantity', $qty);
                } else {
                    // عجز أو هالك (خصم رصيد)
                    $stock->decrement('quantity', $qty);
                }

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'item_id'             => $itemId,
                    'quantity'            => $qty,
                    'cost'                => $stock->avg_cost,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.adjustments.index')->with('success', 'تم تسجيل محضر تسوية المخزون بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}

