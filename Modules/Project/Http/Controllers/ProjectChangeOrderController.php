<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectChangeOrder;

class ProjectChangeOrderController extends Controller
{
    /**
     * List Change Orders for a Project
     */
    public function index(Project $project)
    {
        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'change_orders']);
    }

    /**
     * Store new Change Order
     */
    public function store(Request $request, Project $project)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'title'               => 'required|string|max:190',
            'request_date'        => 'required|date',
            'amount_change'       => 'required|numeric',
            'time_extension_days' => 'nullable|integer|min:0',
            'justification'       => 'nullable|string',
        ]);

        $orderNumber = ProjectChangeOrder::generateOrderNumber($project->id);

        $project->changeOrders()->create([
            'tenant_id'           => $tenantId,
            'order_number'        => $orderNumber,
            'title'               => $validated['title'],
            'request_date'        => $validated['request_date'],
            'amount_change'       => (float) $validated['amount_change'],
            'time_extension_days' => (int) ($validated['time_extension_days'] ?? 0),
            'status'              => 'draft',
            'justification'       => $validated['justification'] ?? null,
        ]);

        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'change_orders'])
            ->with('success', 'تم تسجيل طلب أمر التغيير بنجاح برقم: ' . $orderNumber);
    }

    /**
     * Approve Change Order
     */
    public function approve(ProjectChangeOrder $changeOrder)
    {
        if ($changeOrder->status === 'approved') {
            return back()->with('info', 'أمر التغيير معتمد مسبقاً.');
        }

        $changeOrder->update([
            'status'        => 'approved',
            'approval_date' => now()->toDateString(),
            'approved_by'   => Auth::id(),
        ]);

        $project = $changeOrder->project;

        // Apply time extension to expected end date if any
        if ($changeOrder->time_extension_days > 0 && $project->expected_end_date) {
            $newEndDate = Carbon::parse($project->expected_end_date)->addDays($changeOrder->time_extension_days);
            $project->update(['expected_end_date' => $newEndDate]);
        }

        // Recalculate revised value and percentages
        $project->recalculateFinancials();

        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'change_orders'])
            ->with('success', "تم اعتماد أمر التغيير ({$changeOrder->order_number}) وإضافته لقيمة العقد وتاريخ التسليم بنجاح.");
    }

    /**
     * Reject Change Order
     */
    public function reject(ProjectChangeOrder $changeOrder)
    {
        $changeOrder->update([
            'status'        => 'rejected',
            'approval_date' => now()->toDateString(),
            'approved_by'   => Auth::id(),
        ]);

        $changeOrder->project->recalculateFinancials();

        return redirect()->route('projects.projects.show', ['project' => $changeOrder->project_id, 'tab' => 'change_orders'])
            ->with('warning', "تم رفض أمر التغيير ({$changeOrder->order_number}).");
    }
}
