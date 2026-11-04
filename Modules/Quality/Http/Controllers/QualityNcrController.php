<?php

namespace Modules\Quality\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\CRM\Entities\Client;
use Modules\HR\Entities\Employee;
use Modules\Inventory\Entities\Item;
use Modules\Manufacturing\Entities\ManufacturingOrder;
use Modules\Project\Entities\Project;
use Modules\Quality\Entities\QualityNcr;

class QualityNcrController extends Controller
{
    /**
     * List Non-Conformance Reports (NCR)
     */
    public function index(Request $request)
    {
        $query = QualityNcr::with([
            'branch', 'item', 'supplier', 'manufacturingOrder', 'project', 'reportedBy'
        ])->latest();

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('disposition')) {
            $query->where('disposition', $request->disposition);
        }

        if ($request->filled('root_cause_category')) {
            $query->where('root_cause_category', $request->root_cause_category);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('ncr_number', 'like', "%{$s}%")
                  ->orWhere('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $ncrs = $query->paginate(15)->withQueryString();

        $stats = [
            'total'          => QualityNcr::count(),
            'open'           => QualityNcr::where('status', 'open')->count(),
            'under_invest'   => QualityNcr::where('status', 'under_investigation')->count(),
            'closed'         => QualityNcr::where('status', 'closed')->count(),
            'critical'       => QualityNcr::where('severity', 'critical')->count(),
            'total_losses'   => (float) QualityNcr::sum('estimated_loss_amount'),
        ];

        return view('quality::ncrs.index', compact('ncrs', 'stats'));
    }

    /**
     * Show form to create new NCR
     */
    public function create()
    {
        $suggestedNumber = QualityNcr::generateNumber();
        $branches = Branch::where('is_active', true)->get();
        $items = Item::orderBy('name')->get();
        $suppliers = Client::whereIn('type', ['supplier', 'both'])->orderBy('name')->get();
        $manufacturingOrders = class_exists(ManufacturingOrder::class) ? ManufacturingOrder::latest()->take(30)->get() : collect();
        $projects = class_exists(Project::class) ? Project::whereIn('status', ['in_progress', 'planning'])->get() : collect();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        return view('quality::ncrs.create', compact(
            'suggestedNumber', 'branches', 'items', 'suppliers', 'manufacturingOrders', 'projects', 'employees'
        ));
    }

    /**
     * Store new NCR
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'branch_id'              => 'required|exists:branches,id',
            'ncr_number'             => 'nullable|string|max:60',
            'item_id'                => 'nullable|exists:items,id',
            'supplier_id'            => 'nullable|exists:clients,id',
            'manufacturing_order_id' => 'nullable|exists:mfg_orders,id',
            'project_id'             => 'nullable|exists:projects,id',
            'title'                  => 'required|string|max:190',
            'description'            => 'required|string',
            'severity'               => 'required|in:minor,major,critical',
            'root_cause_category'    => 'required|in:raw_material,machinery,workmanship,environment,design,other',
            'root_cause_analysis'    => 'nullable|string',
            'defect_quantity'        => 'required|numeric|min:0',
            'estimated_loss_amount'  => 'nullable|numeric|min:0',
            'disposition'            => 'required|in:pending,scrap,rework,return_to_vendor,use_as_is',
            'disposition_notes'      => 'nullable|string',
            'reported_by'            => 'nullable|exists:hr_employees,id',
        ]);

        $ncrNumber = !empty($validated['ncr_number'])
            ? $validated['ncr_number']
            : QualityNcr::generateNumber($tenantId);

        $ncr = QualityNcr::create([
            'tenant_id'              => $tenantId,
            'branch_id'              => $validated['branch_id'],
            'ncr_number'             => $ncrNumber,
            'item_id'                => $validated['item_id'] ?? null,
            'supplier_id'            => $validated['supplier_id'] ?? null,
            'manufacturing_order_id' => $validated['manufacturing_order_id'] ?? null,
            'project_id'             => $validated['project_id'] ?? null,
            'title'                  => $validated['title'],
            'description'            => $validated['description'],
            'severity'               => $validated['severity'],
            'root_cause_category'    => $validated['root_cause_category'],
            'root_cause_analysis'    => $validated['root_cause_analysis'] ?? null,
            'defect_quantity'        => (float) $validated['defect_quantity'],
            'estimated_loss_amount'  => (float) ($validated['estimated_loss_amount'] ?? 0.00),
            'disposition'            => $validated['disposition'],
            'disposition_notes'      => $validated['disposition_notes'] ?? null,
            'status'                 => 'open',
            'reported_by'            => $validated['reported_by'] ?? null,
            'created_by'             => Auth::id(),
        ]);

        return redirect()->route('quality.ncrs.show', $ncr->id)
            ->with('success', "تم تسجيل تقرير عدم المطابقة بنجاح برقم: {$ncr->ncr_number}.");
    }

    /**
     * Show NCR Details
     */
    public function show(QualityNcr $ncr)
    {
        $ncr->load([
            'branch', 'item', 'supplier', 'manufacturingOrder', 'project',
            'reportedBy', 'creator', 'inspection', 'capas.assignee'
        ]);

        $employees = Employee::where('status', 'active')->orderBy('name')->get();

        return view('quality::ncrs.show', compact('ncr', 'employees'));
    }

    /**
     * Update Disposition Decision
     */
    public function updateDisposition(Request $request, QualityNcr $ncr)
    {
        $validated = $request->validate([
            'disposition'       => 'required|in:scrap,rework,return_to_vendor,use_as_is',
            'disposition_notes' => 'required|string',
            'estimated_loss_amount' => 'nullable|numeric|min:0',
        ]);

        $ncr->update([
            'disposition'           => $validated['disposition'],
            'disposition_notes'     => $validated['disposition_notes'],
            'estimated_loss_amount' => (float) ($validated['estimated_loss_amount'] ?? $ncr->estimated_loss_amount),
            'status'                => 'action_taken',
        ]);

        return redirect()->route('quality.ncrs.show', $ncr->id)
            ->with('success', 'تم اعتماد قرار المعالجة والتصرف في المنتجات غير المطابقة بنجاح.');
    }

    /**
     * Close NCR
     */
    public function close(Request $request, QualityNcr $ncr)
    {
        $ncr->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        return redirect()->route('quality.ncrs.show', $ncr->id)
            ->with('success', "تم إغلاق تقرير عدم المطابقة ({$ncr->ncr_number}) رسمياً.");
    }

    /**
     * Delete NCR
     */
    public function destroy(QualityNcr $ncr)
    {
        $ncr->delete();
        return redirect()->route('quality.ncrs.index')->with('success', 'تم حذف تقرير عدم المطابقة بنجاح.');
    }
}
