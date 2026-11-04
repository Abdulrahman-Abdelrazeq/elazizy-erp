<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectBoqItem;
use Modules\Project\Entities\ProjectExtractItem;

class ProjectBoqController extends Controller
{
    /**
     * List BOQ Items for a Project
     */
    public function index(Project $project)
    {
        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'boq']);
    }

    /**
     * Store new BOQ Item
     */
    public function store(Request $request, Project $project)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'item_code'         => 'nullable|string|max:50',
            'description'       => 'required|string|max:255',
            'unit'              => 'required|string|max:30',
            'contract_quantity' => 'required|numeric|min:0.001',
            'unit_price'        => 'required|numeric|min:0.01',
        ]);

        $contractQty = (float) $validated['contract_quantity'];
        $unitPrice = (float) $validated['unit_price'];
        $totalPrice = round($contractQty * $unitPrice, 2);

        $itemCode = !empty($validated['item_code'])
            ? $validated['item_code']
            : 'BOQ-' . str_pad($project->boqItems()->count() + 1, 3, '0', STR_PAD_LEFT);

        $project->boqItems()->create([
            'tenant_id'             => $tenantId,
            'item_code'             => $itemCode,
            'description'           => $validated['description'],
            'unit'                  => $validated['unit'],
            'contract_quantity'     => $contractQty,
            'revised_quantity'      => $contractQty,
            'unit_price'            => $unitPrice,
            'total_price'           => $totalPrice,
            'executed_quantity'     => 0.00,
            'completion_percentage' => 0.00,
        ]);

        $project->recalculateFinancials();

        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'boq'])
            ->with('success', 'تم إضافة بند المقايسة وجدول الكميات بنجاح.');
    }

    /**
     * Update BOQ Item
     */
    public function update(Request $request, ProjectBoqItem $boqItem)
    {
        $validated = $request->validate([
            'item_code'         => 'nullable|string|max:50',
            'description'       => 'required|string|max:255',
            'unit'              => 'required|string|max:30',
            'contract_quantity' => 'required|numeric|min:0.001',
            'revised_quantity'  => 'nullable|numeric|min:0.001',
            'unit_price'        => 'required|numeric|min:0.01',
            'executed_quantity' => 'nullable|numeric|min:0',
        ]);

        $contractQty = (float) $validated['contract_quantity'];
        $revisedQty = isset($validated['revised_quantity']) && (float) $validated['revised_quantity'] > 0
            ? (float) $validated['revised_quantity']
            : $contractQty;
        $unitPrice = (float) $validated['unit_price'];
        $executedQty = isset($validated['executed_quantity']) ? (float) $validated['executed_quantity'] : (float) $boqItem->executed_quantity;

        $totalPrice = round($revisedQty * $unitPrice, 2);
        $completion = $revisedQty > 0 ? min(100.00, round(($executedQty / $revisedQty) * 100, 2)) : 0.00;

        $boqItem->update([
            'item_code'             => $validated['item_code'] ?? $boqItem->item_code,
            'description'           => $validated['description'],
            'unit'                  => $validated['unit'],
            'contract_quantity'     => $contractQty,
            'revised_quantity'      => $revisedQty,
            'unit_price'            => $unitPrice,
            'total_price'           => $totalPrice,
            'executed_quantity'     => $executedQty,
            'completion_percentage' => $completion,
        ]);

        $project = $boqItem->project;
        $project->recalculateFinancials();

        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'boq'])
            ->with('success', 'تم تحديث بند المقايسة بنجاح.');
    }

    /**
     * Delete BOQ Item
     */
    public function destroy(ProjectBoqItem $boqItem)
    {
        if ($boqItem->executed_quantity > 0 || ProjectExtractItem::where('boq_item_id', $boqItem->id)->exists()) {
            return back()->with('error', 'لا يمكن حذف بند مقايسة مسجل عليه كميات منفذة أو مستخلصات.');
        }

        $project = $boqItem->project;
        $boqItem->delete();

        $project->recalculateFinancials();

        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'boq'])
            ->with('success', 'تم حذف بند المقايسة بنجاح.');
    }
}
