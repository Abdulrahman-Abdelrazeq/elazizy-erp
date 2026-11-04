<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectMilestone;

class ProjectMilestoneController extends Controller
{
    /**
     * List Milestones for a Project
     */
    public function index(Project $project)
    {
        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'milestones']);
    }

    /**
     * Store new Milestone
     */
    public function store(Request $request, Project $project)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'title'                 => 'required|string|max:190',
            'description'           => 'nullable|string',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date|after_or_equal:start_date',
            'weight_percentage'     => 'nullable|numeric|min:0|max:100',
            'completion_percentage' => 'nullable|numeric|min:0|max:100',
            'status'                => 'required|in:pending,in_progress,completed,delayed',
            'deliverables'          => 'nullable|string',
        ]);

        $project->milestones()->create([
            'tenant_id'             => $tenantId,
            'title'                 => $validated['title'],
            'description'           => $validated['description'] ?? null,
            'start_date'            => $validated['start_date'],
            'end_date'              => $validated['end_date'],
            'weight_percentage'     => $validated['weight_percentage'] ?? 0.00,
            'completion_percentage' => $validated['completion_percentage'] ?? 0.00,
            'status'                => $validated['status'],
            'deliverables'          => $validated['deliverables'] ?? null,
        ]);

        $project->recalculateFinancials();

        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'milestones'])
            ->with('success', 'تمت إضافة مرحلة المشروع ومؤشر الإنجاز بنجاح.');
    }

    /**
     * Update Milestone
     */
    public function update(Request $request, ProjectMilestone $milestone)
    {
        $validated = $request->validate([
            'title'                 => 'required|string|max:190',
            'description'           => 'nullable|string',
            'start_date'            => 'required|date',
            'end_date'              => 'required|date|after_or_equal:start_date',
            'weight_percentage'     => 'nullable|numeric|min:0|max:100',
            'completion_percentage' => 'nullable|numeric|min:0|max:100',
            'status'                => 'required|in:pending,in_progress,completed,delayed',
            'deliverables'          => 'nullable|string',
        ]);

        $milestone->update($validated);

        $project = $milestone->project;
        $project->recalculateFinancials();

        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'milestones'])
            ->with('success', 'تم تحديث مرحلة الإنجاز بنجاح.');
    }

    /**
     * Delete Milestone
     */
    public function destroy(ProjectMilestone $milestone)
    {
        $project = $milestone->project;
        $milestone->delete();

        $project->recalculateFinancials();

        return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'milestones'])
            ->with('success', 'تم حذف مرحلة المشروع بنجاح.');
    }
}
