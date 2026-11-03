<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;
use Modules\Manufacturing\Entities\Bom;
use Modules\Manufacturing\Entities\ManufacturingOrder;
use Modules\Manufacturing\Entities\ManufacturingOrderItem;

class ManufacturingOrderController extends Controller
{
    /**
     * List all Manufacturing Orders
     */
    public function index(Request $request)
    {
        $query = ManufacturingOrder::with(['product.unit', 'bom', 'fromStore', 'toStore', 'branch', 'creator'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('store_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_store_id', $request->store_id)
                  ->orWhere('to_store_id', $request->store_id);
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('order_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('order_date', '<=', $request->to_date);
        }

        $orders = $query->paginate(15)->withQueryString();
        $products = Item::where('is_service', false)->orderBy('name')->get();
        $stores = Store::where('is_active', true)->get();

        $stats = [
            'total_orders'    => ManufacturingOrder::count(),
            'completed'       => ManufacturingOrder::where('status', 'completed')->count(),
            'in_progress'     => ManufacturingOrder::where('status', 'in_progress')->count(),
            'total_produced'  => (float) ManufacturingOrder::where('status', 'completed')->sum('produced_quantity'),
            'total_cost'      => (float) ManufacturingOrder::where('status', 'completed')->sum('actual_total_cost'),
        ];

        return view('manufacturing::orders.index', compact('orders', 'products', 'stores', 'stats'));
    }

    /**
     * Show form to create new Manufacturing Order
     */
    public function create()
    {
        $boms = Bom::active()->with(['product.unit', 'items.item'])->orderBy('name')->get();
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::all();

        return view('manufacturing::orders.create', compact('boms', 'stores', 'branches'));
    }

    /**
     * Store new Manufacturing Order
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'bom_id'                   => 'required|exists:mfg_boms,id',
            'branch_id'                => 'nullable|exists:branches,id',
            'from_store_id'            => 'required|exists:stores,id',
            'to_store_id'              => 'required|exists:stores,id',
            'planned_quantity'         => 'required|numeric|min:0.001',
            'order_date'               => 'required|date',
            'expected_completion_date' => 'nullable|date|after_or_equal:order_date',
            'notes'                    => 'nullable|string',
        ]);

        $bom = Bom::with(['items.item', 'operations'])->findOrFail($validated['bom_id']);
        $ratio = (float) $validated['planned_quantity'] / max(0.001, (float) $bom->output_quantity);

        $estMaterial = (float) $bom->raw_material_cost * $ratio;
        $estLabor = (float) $bom->labor_cost * $ratio;
        $estOverhead = (float) $bom->overhead_cost * $ratio;
        $estTotal = $estMaterial + $estLabor + $estOverhead;

        DB::beginTransaction();
        try {
            $orderNumber = ManufacturingOrder::generateNumber();

            $order = ManufacturingOrder::create([
                'tenant_id'                => $tenantId,
                'branch_id'                => $validated['branch_id'] ?? (activeBranch() ? activeBranch()->id : null),
                'order_number'             => $orderNumber,
                'bom_id'                   => $bom->id,
                'product_id'               => $bom->product_id,
                'from_store_id'            => $validated['from_store_id'],
                'to_store_id'              => $validated['to_store_id'],
                'planned_quantity'         => $validated['planned_quantity'],
                'produced_quantity'        => 0.000,
                'rejected_quantity'        => 0.000,
                'status'                   => 'draft',
                'order_date'               => $validated['order_date'],
                'expected_completion_date' => $validated['expected_completion_date'] ?? null,
                'estimated_material_cost'  => $estMaterial,
                'estimated_labor_cost'     => $estLabor,
                'estimated_overhead_cost'  => $estOverhead,
                'estimated_total_cost'     => $estTotal,
                'actual_material_cost'     => 0.00,
                'actual_labor_cost'        => $estLabor,
                'actual_overhead_cost'     => $estOverhead,
                'actual_total_cost'        => 0.00,
                'unit_cost'                => $bom->cost_per_unit,
                'notes'                    => $validated['notes'] ?? null,
                'created_by'               => Auth::id(),
            ]);

            // Copy BOM items scaled to planned quantity
            foreach ($bom->items as $bItem) {
                $plannedItemQty = $bItem->quantity * $ratio;
                $itemTotalCost = $plannedItemQty * $bItem->unit_cost * (1 + ($bItem->scrap_percentage / 100));

                ManufacturingOrderItem::create([
                    'tenant_id'        => $tenantId,
                    'order_id'         => $order->id,
                    'item_id'          => $bItem->item_id,
                    'planned_quantity' => $plannedItemQty,
                    'actual_quantity'  => $plannedItemQty, // initially set to planned
                    'unit_cost'        => $bItem->unit_cost,
                    'total_cost'       => $itemTotalCost,
                ]);
            }

            DB::commit();

            return redirect()->route('manufacturing.orders.show', $order->id)
                ->with('success', 'تم تسجيل أمر التصنيع بنجاح كمسودة برقم: ' . $orderNumber);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء إصدار أمر التصنيع: ' . $e->getMessage());
        }
    }

    /**
     * Show Manufacturing Order Details
     */
    public function show(ManufacturingOrder $order)
    {
        $order->load([
            'bom.operations.workstation',
            'product.unit',
            'fromStore',
            'toStore',
            'branch',
            'creator',
            'completedBy',
            'items.item.unit',
        ]);

        // Check stock availability in from_store for each item
        $stockCheck = [];
        foreach ($order->items as $item) {
            $stock = ItemStock::where('store_id', $order->from_store_id)
                ->where('item_id', $item->item_id)
                ->first();
            $available = $stock ? (float) $stock->quantity : 0.00;
            $stockCheck[$item->item_id] = [
                'available'  => $available,
                'sufficient' => $available >= $item->planned_quantity,
            ];
        }

        return view('manufacturing::orders.show', compact('order', 'stockCheck'));
    }

    /**
     * Confirm Manufacturing Order
     */
    public function confirm(ManufacturingOrder $order)
    {
        if ($order->status !== 'draft') {
            return back()->with('error', 'لا يمكن تأكيد الأمر إلا إذا كان في حالة مسودة.');
        }

        $order->update(['status' => 'confirmed']);
        return back()->with('success', 'تم تأكيد واعتماد أمر التشغيل بنجاح! جاهز لبدء مراحل الإنتاج.');
    }

    /**
     * Start Manufacturing Process
     */
    public function start(ManufacturingOrder $order)
    {
        if (!in_array($order->status, ['draft', 'confirmed'])) {
            return back()->with('error', 'أمر التشغيل قيد التنفيذ أو مكتمل بالفعل.');
        }

        $order->update(['status' => 'in_progress']);
        return back()->with('success', 'تم بدء تشغيل خط الإنتاج لأمر التصنيع.');
    }

    /**
     * Complete Manufacturing Process (Consumes raw materials, produces finished goods)
     */
    public function complete(Request $request, ManufacturingOrder $order)
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'أمر التصنيع مكتمل ومنفذ مسبقاً.');
        }

        $validated = $request->validate([
            'produced_quantity'    => 'required|numeric|min:0.001',
            'rejected_quantity'    => 'nullable|numeric|min:0',
            'actual_labor_cost'    => 'nullable|numeric|min:0',
            'actual_overhead_cost' => 'nullable|numeric|min:0',
            'materials'            => 'nullable|array',
            'materials.*.id'       => 'required_with:materials|exists:mfg_order_items,id',
            'materials.*.quantity' => 'required_with:materials|numeric|min:0',
            'update_sell_price'    => 'nullable|boolean',
        ]);

        $tenantId = currentTenant() ? currentTenant()->id : 1;

        DB::beginTransaction();
        try {
            $totalMaterialCost = 0.00;

            // 1. Update actual consumed materials & deduct from source store
            foreach ($order->items as $orderItem) {
                $actualQty = (float) $orderItem->planned_quantity;

                // If customized quantities were passed in request
                if (!empty($validated['materials'])) {
                    foreach ($validated['materials'] as $mat) {
                        if ($mat['id'] == $orderItem->id) {
                            $actualQty = (float) $mat['quantity'];
                            break;
                        }
                    }
                }

                $itemCost = $actualQty * $orderItem->unit_cost;
                $orderItem->update([
                    'actual_quantity' => $actualQty,
                    'total_cost'      => $itemCost,
                ]);

                $totalMaterialCost += $itemCost;

                // Deduct from source store (from_store)
                $sourceStock = ItemStock::firstOrCreate(
                    ['tenant_id' => $tenantId, 'store_id' => $order->from_store_id, 'item_id' => $orderItem->item_id],
                    ['quantity' => 0.00, 'avg_cost' => $orderItem->unit_cost]
                );

                $sourceStock->quantity = max(0.00, (float) $sourceStock->quantity - $actualQty);
                $sourceStock->save();
            }

            // 2. Add produced finished goods to destination store (to_store)
            $producedQty = (float) $validated['produced_quantity'];
            $rejectedQty = (float) ($validated['rejected_quantity'] ?? 0.00);
            $laborCost = isset($validated['actual_labor_cost']) ? (float) $validated['actual_labor_cost'] : (float) $order->estimated_labor_cost;
            $overheadCost = isset($validated['actual_overhead_cost']) ? (float) $validated['actual_overhead_cost'] : (float) $order->estimated_overhead_cost;

            $totalCost = $totalMaterialCost + $laborCost + $overheadCost;
            $unitCost = $producedQty > 0 ? ($totalCost / $producedQty) : 0.00;

            $targetStock = ItemStock::firstOrCreate(
                ['tenant_id' => $tenantId, 'store_id' => $order->to_store_id, 'item_id' => $order->product_id],
                ['quantity' => 0.00, 'avg_cost' => $unitCost]
            );

            // Calculate weighted average cost in target store
            $oldQty = (float) $targetStock->quantity;
            $oldCost = (float) $targetStock->avg_cost;
            $newTotalQty = $oldQty + $producedQty;
            $newAvgCost = $newTotalQty > 0 ? (($oldQty * $oldCost) + ($producedQty * $unitCost)) / $newTotalQty : $unitCost;

            $targetStock->quantity = $newTotalQty;
            $targetStock->avg_cost = $newAvgCost;
            $targetStock->save();

            // Update item cost_price in items table
            $product = Item::find($order->product_id);
            if ($product) {
                $product->update([
                    'buy_price' => $unitCost,
                    'avg_cost'  => $newAvgCost,
                ]);
            }

            // 3. Update Order Status & Financial Totals
            $order->update([
                'status'                 => 'completed',
                'produced_quantity'      => $producedQty,
                'rejected_quantity'      => $rejectedQty,
                'actual_completion_date' => now(),
                'actual_material_cost'   => $totalMaterialCost,
                'actual_labor_cost'      => $laborCost,
                'actual_overhead_cost'   => $overheadCost,
                'actual_total_cost'      => $totalCost,
                'unit_cost'              => $unitCost,
                'completed_by'           => Auth::id(),
            ]);

            DB::commit();

            return redirect()->route('manufacturing.orders.show', $order->id)
                ->with('success', 'تم استكمال عملية التصنيع بنجاح! تم خصم المواد الخام وإيداع ' . $producedQty . ' وحدة في مخزن ' . $order->toStore?->name . ' بتكلفة إجمالية ' . formatMoney($totalCost));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'فشل في استكمال عملية التصنيع: ' . $e->getMessage());
        }
    }

    /**
     * Cancel Manufacturing Order
     */
    public function cancel(ManufacturingOrder $order)
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'لا يمكن إلغاء أمر تصنيع مكتمل تم استهلاك مواده وتوريد إنتاجه!');
        }

        $order->update(['status' => 'cancelled']);
        return back()->with('success', 'تم إلغاء أمر التصنيع بنجاح.');
    }

    /**
     * Official Printable Work Order Sheet
     */
    public function print(ManufacturingOrder $order)
    {
        $order->load([
            'bom.operations.workstation',
            'product.unit',
            'fromStore',
            'toStore',
            'branch',
            'creator',
            'completedBy',
            'items.item.unit',
        ]);

        return view('manufacturing::orders.print', compact('order'));
    }

    /**
     * Delete Manufacturing Order
     */
    public function destroy(ManufacturingOrder $order)
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'لا يمكن حذف أمر تصنيع مكتمل!');
        }

        $order->delete();
        return redirect()->route('manufacturing.orders.index')->with('success', 'تم حذف أمر التصنيع بنجاح.');
    }
}
