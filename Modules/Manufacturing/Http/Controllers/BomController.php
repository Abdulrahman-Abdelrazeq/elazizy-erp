<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\Unit;
use Modules\Manufacturing\Entities\Bom;
use Modules\Manufacturing\Entities\BomItem;
use Modules\Manufacturing\Entities\BomOperation;
use Modules\Manufacturing\Entities\Workstation;

class BomController extends Controller
{
    /**
     * List all Bills of Materials
     */
    public function index(Request $request)
    {
        $query = Bom::with(['product.unit', 'creator', 'items.item', 'operations'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('bom_number', 'like', "%{$s}%")
                  ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $boms = $query->paginate(15)->withQueryString();

        $stats = [
            'total_boms'   => Bom::count(),
            'active_boms'  => Bom::where('is_active', true)->count(),
            'total_cost'   => (float) Bom::where('is_active', true)->sum('total_cost'),
            'orders_count' => DB::table('mfg_orders')->count(),
        ];

        return view('manufacturing::boms.index', compact('boms', 'stats'));
    }

    /**
     * Show form to create new BOM
     */
    public function create()
    {
        $products = Item::where('is_service', false)->orderBy('name')->get();
        $rawMaterials = Item::where('is_service', false)->orderBy('name')->get();
        $workstations = Workstation::active()->orderBy('name')->get();
        $units = Unit::all();

        return view('manufacturing::boms.create', compact('products', 'rawMaterials', 'workstations', 'units'));
    }

    /**
     * Store newly created BOM
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'name'            => 'required|string|max:150',
            'product_id'      => 'required|exists:items,id',
            'output_quantity' => 'required|numeric|min:0.001',
            'unit_id'         => 'nullable|exists:units,id',
            'overhead_cost'   => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
            'is_active'       => 'nullable|boolean',
            'is_default'      => 'nullable|boolean',
            'update_item_cost'=> 'nullable|boolean',
            // Items
            'items'                     => 'required|array|min:1',
            'items.*.item_id'          => 'required|exists:items,id',
            'items.*.quantity'         => 'required|numeric|min:0.001',
            'items.*.unit_cost'        => 'nullable|numeric|min:0',
            'items.*.scrap_percentage' => 'nullable|numeric|min:0|max:100',
            // Operations
            'operations'                         => 'nullable|array',
            'operations.*.operation_name'        => 'required_with:operations|string|max:150',
            'operations.*.workstation_id'        => 'nullable|exists:mfg_workstations,id',
            'operations.*.standard_time_minutes' => 'nullable|numeric|min:0',
            'operations.*.operation_cost'        => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $bomNumber = Bom::generateNumber();

            $bom = Bom::create([
                'tenant_id'         => $tenantId,
                'bom_number'        => $bomNumber,
                'name'              => $validated['name'],
                'product_id'        => $validated['product_id'],
                'output_quantity'   => $validated['output_quantity'],
                'unit_id'           => $validated['unit_id'] ?? null,
                'overhead_cost'     => $validated['overhead_cost'] ?? 0.00,
                'is_active'         => $request->boolean('is_active', true),
                'is_default'        => $request->boolean('is_default', true),
                'notes'             => $validated['notes'] ?? null,
                'created_by'        => Auth::id(),
            ]);

            // Save Raw Material Items
            $materialTotal = 0.00;
            foreach ($validated['items'] as $it) {
                $itemModel = Item::find($it['item_id']);
                $unitCost = isset($it['unit_cost']) && $it['unit_cost'] > 0
                    ? (float) $it['unit_cost']
                    : (float) ($itemModel->avg_cost ?: $itemModel->buy_price);

                $qty = (float) $it['quantity'];
                $scrap = (float) ($it['scrap_percentage'] ?? 0.00);
                $totalCost = $qty * $unitCost * (1 + ($scrap / 100));

                BomItem::create([
                    'tenant_id'        => $tenantId,
                    'bom_id'           => $bom->id,
                    'item_id'          => $it['item_id'],
                    'quantity'         => $qty,
                    'unit_cost'        => $unitCost,
                    'scrap_percentage' => $scrap,
                    'total_cost'       => $totalCost,
                ]);

                $materialTotal += $totalCost;
            }

            // Save Operations
            $laborTotal = 0.00;
            if (!empty($validated['operations'])) {
                foreach ($validated['operations'] as $seq => $op) {
                    if (empty($op['operation_name'])) continue;

                    $opCost = (float) ($op['operation_cost'] ?? 0.00);
                    // If workstation given and opCost is 0, estimate from hourly cost
                    if ($opCost == 0 && !empty($op['workstation_id']) && !empty($op['standard_time_minutes'])) {
                        $ws = Workstation::find($op['workstation_id']);
                        if ($ws && $ws->hourly_cost > 0) {
                            $opCost = ($ws->hourly_cost / 60) * (float) $op['standard_time_minutes'];
                        }
                    }

                    BomOperation::create([
                        'tenant_id'             => $tenantId,
                        'bom_id'                => $bom->id,
                        'workstation_id'        => $op['workstation_id'] ?? null,
                        'operation_name'        => $op['operation_name'],
                        'sequence_order'        => $seq + 1,
                        'standard_time_minutes' => (float) ($op['standard_time_minutes'] ?? 0),
                        'operation_cost'        => $opCost,
                    ]);

                    $laborTotal += $opCost;
                }
            }

            $bom->recalculateCosts();

            // Optionally update finished product cost in items table
            if ($request->boolean('update_item_cost')) {
                $product = Item::find($validated['product_id']);
                if ($product) {
                    $product->update([
                        'buy_price' => $bom->cost_per_unit,
                        'avg_cost'  => $bom->cost_per_unit,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('manufacturing.boms.show', $bom->id)
                ->with('success', 'تم إنشاء قائمة المواد والتراكيب بنجاح! رقم الـ BOM: ' . $bomNumber);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ قائمة المواد: ' . $e->getMessage());
        }
    }

    /**
     * Show BOM details
     */
    public function show(Bom $bom)
    {
        $bom->load(['product.unit', 'unit', 'creator', 'items.item.unit', 'operations.workstation', 'orders' => fn($q) => $q->latest()->take(5)]);

        return view('manufacturing::boms.show', compact('bom'));
    }

    /**
     * Show edit form
     */
    public function edit(Bom $bom)
    {
        $bom->load(['items.item', 'operations.workstation']);
        $products = Item::where('is_service', false)->orderBy('name')->get();
        $rawMaterials = Item::where('is_service', false)->orderBy('name')->get();
        $workstations = Workstation::active()->orderBy('name')->get();
        $units = Unit::all();

        return view('manufacturing::boms.edit', compact('bom', 'products', 'rawMaterials', 'workstations', 'units'));
    }

    /**
     * Update BOM
     */
    public function update(Request $request, Bom $bom)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'name'            => 'required|string|max:150',
            'product_id'      => 'required|exists:items,id',
            'output_quantity' => 'required|numeric|min:0.001',
            'unit_id'         => 'nullable|exists:units,id',
            'overhead_cost'   => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
            'is_active'       => 'nullable|boolean',
            'is_default'      => 'nullable|boolean',
            'update_item_cost'=> 'nullable|boolean',
            // Items
            'items'                     => 'required|array|min:1',
            'items.*.item_id'          => 'required|exists:items,id',
            'items.*.quantity'         => 'required|numeric|min:0.001',
            'items.*.unit_cost'        => 'nullable|numeric|min:0',
            'items.*.scrap_percentage' => 'nullable|numeric|min:0|max:100',
            // Operations
            'operations'                         => 'nullable|array',
            'operations.*.operation_name'        => 'required_with:operations|string|max:150',
            'operations.*.workstation_id'        => 'nullable|exists:mfg_workstations,id',
            'operations.*.standard_time_minutes' => 'nullable|numeric|min:0',
            'operations.*.operation_cost'        => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $bom->update([
                'name'            => $validated['name'],
                'product_id'      => $validated['product_id'],
                'output_quantity' => $validated['output_quantity'],
                'unit_id'         => $validated['unit_id'] ?? null,
                'overhead_cost'   => $validated['overhead_cost'] ?? 0.00,
                'is_active'       => $request->boolean('is_active', true),
                'is_default'      => $request->boolean('is_default', true),
                'notes'           => $validated['notes'] ?? null,
            ]);

            // Recreate Items
            $bom->items()->delete();
            foreach ($validated['items'] as $it) {
                $itemModel = Item::find($it['item_id']);
                $unitCost = isset($it['unit_cost']) && $it['unit_cost'] > 0
                    ? (float) $it['unit_cost']
                    : (float) ($itemModel->avg_cost ?: $itemModel->buy_price);

                $qty = (float) $it['quantity'];
                $scrap = (float) ($it['scrap_percentage'] ?? 0.00);
                $totalCost = $qty * $unitCost * (1 + ($scrap / 100));

                BomItem::create([
                    'tenant_id'        => $tenantId,
                    'bom_id'           => $bom->id,
                    'item_id'          => $it['item_id'],
                    'quantity'         => $qty,
                    'unit_cost'        => $unitCost,
                    'scrap_percentage' => $scrap,
                    'total_cost'       => $totalCost,
                ]);
            }

            // Recreate Operations
            $bom->operations()->delete();
            if (!empty($validated['operations'])) {
                foreach ($validated['operations'] as $seq => $op) {
                    if (empty($op['operation_name'])) continue;

                    $opCost = (float) ($op['operation_cost'] ?? 0.00);
                    if ($opCost == 0 && !empty($op['workstation_id']) && !empty($op['standard_time_minutes'])) {
                        $ws = Workstation::find($op['workstation_id']);
                        if ($ws && $ws->hourly_cost > 0) {
                            $opCost = ($ws->hourly_cost / 60) * (float) $op['standard_time_minutes'];
                        }
                    }

                    BomOperation::create([
                        'tenant_id'             => $tenantId,
                        'bom_id'                => $bom->id,
                        'workstation_id'        => $op['workstation_id'] ?? null,
                        'operation_name'        => $op['operation_name'],
                        'sequence_order'        => $seq + 1,
                        'standard_time_minutes' => (float) ($op['standard_time_minutes'] ?? 0),
                        'operation_cost'        => $opCost,
                    ]);
                }
            }

            $bom->recalculateCosts();

            if ($request->boolean('update_item_cost')) {
                $product = Item::find($validated['product_id']);
                if ($product) {
                    $product->update([
                        'buy_price' => $bom->cost_per_unit,
                        'avg_cost'  => $bom->cost_per_unit,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('manufacturing.boms.show', $bom->id)
                ->with('success', 'تم تحديث قائمة المواد بنجاح! التكلفة الجديدة للوحدة: ' . formatMoney($bom->cost_per_unit));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء التحديث: ' . $e->getMessage());
        }
    }

    /**
     * Duplicate existing BOM
     */
    public function duplicate(Bom $bom)
    {
        DB::beginTransaction();
        try {
            $newBom = $bom->replicate(['bom_number']);
            $newBom->bom_number = Bom::generateNumber();
            $newBom->name = $bom->name . ' (نسخة معدلة)';
            $newBom->save();

            foreach ($bom->items as $it) {
                $newIt = $it->replicate(['bom_id']);
                $newIt->bom_id = $newBom->id;
                $newIt->save();
            }

            foreach ($bom->operations as $op) {
                $newOp = $op->replicate(['bom_id']);
                $newOp->bom_id = $newBom->id;
                $newOp->save();
            }

            $newBom->recalculateCosts();

            DB::commit();

            return redirect()->route('manufacturing.boms.edit', $newBom->id)
                ->with('success', 'تم تكرار قائمة المواد بنجاح كنسخة جديدة برقم ' . $newBom->bom_number);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'فشل في نسخ قائمة المواد: ' . $e->getMessage());
        }
    }

    /**
     * Delete BOM
     */
    public function destroy(Bom $bom)
    {
        if ($bom->orders()->whereIn('status', ['confirmed', 'in_progress', 'completed'])->exists()) {
            return back()->with('error', 'لا يمكن حذف قائمة المواد لأنها مستخدمة في أوامر تصنيع مؤكدة أو منفذة!');
        }

        $bom->delete();
        return redirect()->route('manufacturing.boms.index')->with('success', 'تم حذف قائمة المواد بنجاح.');
    }
}
