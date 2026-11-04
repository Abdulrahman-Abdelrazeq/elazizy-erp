<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Accounting\Entities\CostCenter;
use Modules\CRM\Entities\Client;
use Modules\HR\Entities\Employee;
use Modules\Project\Entities\Project;

class ProjectController extends Controller
{
    /**
     * List Projects Registry
     */
    public function index(Request $request)
    {
        $query = Project::with(['branch', 'client', 'projectManager', 'costCenter'])->latest();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('contract_number', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%");
            });
        }

        $projects = $query->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();
        $clients = Client::whereIn('type', ['customer', 'both'])->orderBy('name')->get();

        $stats = [
            'total_projects'    => Project::count(),
            'in_progress_count' => Project::where('status', 'in_progress')->count(),
            'total_contract_val'=> (float) Project::sum('revised_value'),
            'total_billed_val'  => (float) Project::all()->sum('total_billed_amount'),
            'avg_completion'    => round((float) Project::avg('completion_percentage'), 1),
        ];

        return view('project::projects.index', compact('projects', 'branches', 'clients', 'stats'));
    }

    /**
     * Show form to create new Project
     */
    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $clients = Client::whereIn('type', ['customer', 'both'])->orderBy('name')->get();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $costCenters = CostCenter::all();
        $suggestedCode = Project::generateCode();

        return view('project::projects.create', compact(
            'branches', 'clients', 'employees', 'costCenters', 'suggestedCode'
        ));
    }

    /**
     * Store new Project
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'branch_id'                  => 'required|exists:branches,id',
            'client_id'                  => 'required|exists:clients,id',
            'cost_center_id'             => 'nullable|exists:cost_centers,id',
            'project_manager_id'         => 'nullable|exists:hr_employees,id',
            'code'                       => 'nullable|string|max:50',
            'name'                       => 'required|string|max:190',
            'contract_number'            => 'nullable|string|max:100',
            'contract_date'              => 'nullable|date',
            'start_date'                 => 'required|date',
            'expected_end_date'          => 'required|date|after_or_equal:start_date',
            'contract_value'             => 'required|numeric|min:0.01',
            'advance_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'retention_percentage'       => 'nullable|numeric|min:0|max:100',
            'status'                     => 'required|in:planning,in_progress,suspended,delivered_primary,completed,cancelled',
            'location'                   => 'nullable|string|max:190',
            'description'                => 'nullable|string',
        ]);

        $contractValue = (float) $validated['contract_value'];
        $advanceRate = (float) ($validated['advance_payment_percentage'] ?? 10.0);
        $retentionRate = (float) ($validated['retention_percentage'] ?? 5.0);

        $advanceAmount = round($contractValue * ($advanceRate / 100), 2);
        $retentionAmount = round($contractValue * ($retentionRate / 100), 2);

        $code = !empty($validated['code'])
            ? $validated['code']
            : Project::generateCode($tenantId);

        $project = Project::create([
            'tenant_id'                  => $tenantId,
            'branch_id'                  => $validated['branch_id'],
            'client_id'                  => $validated['client_id'],
            'cost_center_id'             => $validated['cost_center_id'] ?? null,
            'project_manager_id'         => $validated['project_manager_id'] ?? null,
            'code'                       => $code,
            'name'                       => $validated['name'],
            'contract_number'            => $validated['contract_number'] ?? null,
            'contract_date'              => $validated['contract_date'] ?? null,
            'start_date'                 => $validated['start_date'],
            'expected_end_date'          => $validated['expected_end_date'],
            'contract_value'             => $contractValue,
            'revised_value'              => $contractValue,
            'advance_payment_percentage' => $advanceRate,
            'advance_payment_amount'     => $advanceAmount,
            'retention_percentage'       => $retentionRate,
            'retention_amount'           => $retentionAmount,
            'completion_percentage'      => 0.00,
            'status'                     => $validated['status'],
            'location'                   => $validated['location'] ?? null,
            'description'                => $validated['description'] ?? null,
            'created_by'                 => Auth::id(),
        ]);

        return redirect()->route('projects.projects.show', $project->id)
            ->with('success', 'تم تسجيل مشروع وعقد المقاولة بنجاح برقم كود: ' . $project->code);
    }

    /**
     * Show 360-degree Project Profile
     */
    public function show(Project $project)
    {
        $project->load([
            'branch', 'client', 'projectManager', 'costCenter', 'creator',
            'milestones' => fn($q) => $q->orderBy('start_date'),
            'boqItems'   => fn($q) => $q->latest(),
            'changeOrders' => fn($q) => $q->latest(),
            'extracts'   => fn($q) => $q->latest(),
        ]);

        return view('project::projects.show', compact('project'));
    }

    /**
     * Show form to edit Project
     */
    public function edit(Project $project)
    {
        $branches = Branch::where('is_active', true)->get();
        $clients = Client::whereIn('type', ['customer', 'both'])->orderBy('name')->get();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $costCenters = CostCenter::all();

        return view('project::projects.edit', compact(
            'project', 'branches', 'clients', 'employees', 'costCenters'
        ));
    }

    /**
     * Update Project
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'branch_id'                  => 'required|exists:branches,id',
            'client_id'                  => 'required|exists:clients,id',
            'cost_center_id'             => 'nullable|exists:cost_centers,id',
            'project_manager_id'         => 'nullable|exists:hr_employees,id',
            'name'                       => 'required|string|max:190',
            'contract_number'            => 'nullable|string|max:100',
            'contract_date'              => 'nullable|date',
            'start_date'                 => 'required|date',
            'expected_end_date'          => 'required|date|after_or_equal:start_date',
            'actual_end_date'            => 'nullable|date',
            'contract_value'             => 'required|numeric|min:0.01',
            'advance_payment_percentage' => 'nullable|numeric|min:0|max:100',
            'retention_percentage'       => 'nullable|numeric|min:0|max:100',
            'status'                     => 'required|in:planning,in_progress,suspended,delivered_primary,completed,cancelled',
            'location'                   => 'nullable|string|max:190',
            'description'                => 'nullable|string',
        ]);

        $project->update($validated);
        $project->recalculateFinancials();

        return redirect()->route('projects.projects.show', $project->id)
            ->with('success', 'تم تحديث بيانات المشروع وعقد المقاولة بنجاح.');
    }

    /**
     * Delete Project
     */
    public function destroy(Project $project)
    {
        if ($project->extracts()->whereIn('status', ['approved', 'paid'])->exists()) {
            return back()->with('error', 'لا يمكن حذف مشروع مسجل عليه مستخلصات معتمدة أو مسددة.');
        }

        $project->delete();
        return redirect()->route('projects.projects.index')->with('success', 'تم حذف ملف المشروع بنجاح.');
    }
}
