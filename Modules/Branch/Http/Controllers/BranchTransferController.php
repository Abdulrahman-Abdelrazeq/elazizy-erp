<?php

namespace Modules\Branch\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchTransfer;
use Modules\Branch\Entities\BranchTransferItem;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;

class BranchTransferController extends Controller
{
    /**
     * List Inter-Branch Transfers
     */
    public function index(Request $request)
    {
        $query = BranchTransfer::with(['fromBranch', 'toBranch', 'fromStore', 'toStore', 'creator'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_branch_id')) {
            $query->where('from_branch_id', $request->from_branch_id);
        }

        if ($request->filled('to_branch_id')) {
            $query->where('to_branch_id', $request->to_branch_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transfer_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transfer_date', '<=', $request->date_to);
        }

        $transfers = $query->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();

        $stats = [
            'total'       => BranchTransfer::count(),
            'in_transit'  => BranchTransfer::where('status', 'dispatched')->count(),
            'received'    => BranchTransfer::where('status', 'received')->count(),
            'total_value' => (float) BranchTransfer::where('status', 'received')->sum('total_cost'),
        ];

        return view('branch::transfers.index', compact('transfers', 'branches', 'stats'));
    }

    /**
     * Show form to create new transfer
     */
    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();
        $items = Item::where('is_service', false)->orderBy('name')->get();

        return view('branch::transfers.create', compact('branches', 'stores', 'items'));
    }

    /**
     * Store new Inter-Branch Transfer
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'from_branch_id'   => 'required|exists:branches,id',
            'to_branch_id'     => 'required|exists:branches,id|different:from_branch_id',
            'from_store_id'    => 'required|exists:stores,id',
            'to_store_id'      => 'required|exists:stores,id|different:from_store_id',
            'transfer_date'    => 'required|date',
            'shipping_company' => 'nullable|string|max:150',
            'driver_name'      => 'nullable|string|max:150',
            'vehicle_plate'    => 'nullable|string|max:50',
            'tracking_number'  => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
            'items'            => 'required|array|min:1',
            'items.*.item_id'  => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
        ]);

        DB::beginTransaction();
        try {
            $transferNumber = BranchTransfer::generateNumber();

            $transfer = BranchTransfer::create([
                'tenant_id'        => $tenantId,
                'transfer_number'  => $transferNumber,
                'from_branch_id'   => $validated['from_branch_id'],
                'to_branch_id'     => $validated['to_branch_id'],
                'from_store_id'    => $validated['from_store_id'],
                'to_store_id'      => $validated['to_store_id'],
                'status'           => 'draft',
                'transfer_date'    => $validated['transfer_date'],
                'shipping_company' => $validated['shipping_company'] ?? null,
                'driver_name'      => $validated['driver_name'] ?? null,
                'vehicle_plate'    => $validated['vehicle_plate'] ?? null,
                'tracking_number'  => $validated['tracking_number'] ?? null,
                'notes'            => $validated['notes'] ?? null,
                'created_by'       => Auth::id(),
            ]);

            $totalCost = 0.00;
            foreach ($validated['items'] as $it) {
                $itemModel = Item::find($it['item_id']);
                $unitCost = (float) ($itemModel->avg_cost ?: $itemModel->buy_price);
                $qty = (float) $it['quantity'];
                $lineTotal = $qty * $unitCost;

                BranchTransferItem::create([
                    'tenant_id'         => $tenantId,
                    'transfer_id'       => $transfer->id,
                    'item_id'           => $it['item_id'],
                    'sent_quantity'     => $qty,
                    'received_quantity' => 0.000,
                    'unit_cost'         => $unitCost,
                    'total_cost'        => $lineTotal,
                ]);

                $totalCost += $lineTotal;
            }

            $transfer->update([
                'total_items_count' => count($validated['items']),
                'total_cost'        => $totalCost,
            ]);

            DB::commit();

            return redirect()->route('branches.transfers.show', $transfer->id)
                ->with('success', 'تم إنشاء طلب التحويل بنجاح برقم: ' . $transferNumber);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ التحويل: ' . $e->getMessage());
        }
    }

    /**
     * Show Transfer Details
     */
    public function show(BranchTransfer $transfer)
    {
        $transfer->load([
            'fromBranch', 'toBranch', 'fromStore', 'toStore',
            'creator', 'dispatcher', 'receiver', 'items.item.unit'
        ]);

        return view('branch::transfers.show', compact('transfer'));
    }

    /**
     * Dispatch Transfer (In-Transit) -> Deduct stock from source store
     */
    public function dispatch(BranchTransfer $transfer)
    {
        if ($transfer->status !== 'draft') {
            return back()->with('error', 'لا يمكن شحن التحويل إلا إذا كان في حالة مسودة.');
        }

        $tenantId = currentTenant() ? currentTenant()->id : 1;

        DB::beginTransaction();
        try {
            // Deduct stock from source store
            foreach ($transfer->items as $item) {
                $stock = ItemStock::firstOrCreate(
                    ['tenant_id' => $tenantId, 'store_id' => $transfer->from_store_id, 'item_id' => $item->item_id],
                    ['quantity' => 0.00, 'avg_cost' => $item->unit_cost]
                );

                $stock->quantity = max(0.00, (float) $stock->quantity - (float) $item->sent_quantity);
                $stock->save();
            }

            $transfer->update([
                'status'         => 'dispatched',
                'dispatch_date'  => now(),
                'dispatched_by'  => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', 'تم شحن التحويل بنجاح! البضاعة الآن في الطريق (In-Transit) وتم خصمها من المخزن المصدر.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'فشل في عملية الشحن: ' . $e->getMessage());
        }
    }

    /**
     * Receive Transfer -> Add stock to destination store
     */
    public function receive(Request $request, BranchTransfer $transfer)
    {
        if ($transfer->status !== 'dispatched') {
            return back()->with('error', 'لا يمكن استلام الشحنة إلا إذا كانت في حالة شحن (In-Transit).');
        }

        $tenantId = currentTenant() ? currentTenant()->id : 1;

        DB::beginTransaction();
        try {
            // Add stock to destination store
            foreach ($transfer->items as $item) {
                $receivedQty = (float) $item->sent_quantity;

                $stock = ItemStock::firstOrCreate(
                    ['tenant_id' => $tenantId, 'store_id' => $transfer->to_store_id, 'item_id' => $item->item_id],
                    ['quantity' => 0.00, 'avg_cost' => $item->unit_cost]
                );

                $stock->quantity = (float) $stock->quantity + $receivedQty;
                $stock->save();

                $item->update(['received_quantity' => $receivedQty]);
            }

            $transfer->update([
                'status'        => 'received',
                'received_date' => now(),
                'received_by'   => Auth::id(),
            ]);

            DB::commit();

            return back()->with('success', 'تم استلام وتوريد الشحنة بنجاح! تمت إضافة الكميات لمخزن الفرع المستلم (' . $transfer->toStore?->name . ').');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'فشل في عملية الاستلام: ' . $e->getMessage());
        }
    }

    /**
     * Cancel Transfer
     */
    public function cancel(BranchTransfer $transfer)
    {
        if ($transfer->status === 'received') {
            return back()->with('error', 'لا يمكن إلغاء شحنة تم استلامها وتوريدها بالفعل!');
        }

        $tenantId = currentTenant() ? currentTenant()->id : 1;

        DB::beginTransaction();
        try {
            // If it was already dispatched, restore stock back to source store
            if ($transfer->status === 'dispatched') {
                foreach ($transfer->items as $item) {
                    $stock = ItemStock::firstOrCreate(
                        ['tenant_id' => $tenantId, 'store_id' => $transfer->from_store_id, 'item_id' => $item->item_id],
                        ['quantity' => 0.00, 'avg_cost' => $item->unit_cost]
                    );

                    $stock->quantity = (float) $stock->quantity + (float) $item->sent_quantity;
                    $stock->save();
                }
            }

            $transfer->update(['status' => 'cancelled']);

            DB::commit();

            return back()->with('success', 'تم إلغاء سند التحويل واسترجاع البضاعة لمخزن المصدر بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'فشل في إلغاء التحويل: ' . $e->getMessage());
        }
    }

    /**
     * Print Transfer Manifest
     */
    public function print(BranchTransfer $transfer)
    {
        $transfer->load([
            'fromBranch', 'toBranch', 'fromStore', 'toStore',
            'creator', 'dispatcher', 'receiver', 'items.item.unit'
        ]);

        return view('branch::transfers.print', compact('transfer'));
    }

    /**
     * Delete Draft Transfer
     */
    public function destroy(BranchTransfer $transfer)
    {
        if ($transfer->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف إلا التحويلات المسودة فقط.');
        }

        $transfer->delete();
        return redirect()->route('branches.transfers.index')->with('success', 'تم حذف سند التحويل بنجاح.');
    }
}
