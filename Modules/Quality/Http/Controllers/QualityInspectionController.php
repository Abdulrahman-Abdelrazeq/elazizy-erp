<?php

namespace Modules\Quality\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Entities\Client;
use Modules\HR\Entities\Employee;
use Modules\Inventory\Entities\Item;
use Modules\Manufacturing\Entities\ManufacturingOrder;
use Modules\Project\Entities\Project;
use Modules\Quality\Entities\QualityInspection;
use Modules\Quality\Entities\QualityInspectionResult;
use Modules\Quality\Entities\QualityNcr;
use Modules\Quality\Entities\QualityTemplate;

class QualityInspectionController extends Controller
{
    /**
     * List Quality Inspections
     */
    public function index(Request $request)
    {
        $query = QualityInspection::with([
            'branch', 'item', 'supplier', 'manufacturingOrder', 'project', 'inspector', 'template'
        ])->latest('inspection_date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('inspector_id')) {
            $query->where('inspector_id', $request->inspector_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('inspection_number', 'like', "%{$s}%")
                  ->orWhere('batch_number', 'like', "%{$s}%")
                  ->orWhereHas('item', fn($iq) => $iq->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
            });
        }

        $inspections = $query->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();
        $inspectors = Employee::where('status', 'active')->orderBy('name')->get();

        $stats = [
            'total'         => QualityInspection::count(),
            'passed'        => QualityInspection::where('status', 'passed')->count(),
            'rejected'      => QualityInspection::where('status', 'rejected')->count(),
            'conditional'   => QualityInspection::where('status', 'conditionally_passed')->count(),
            'pending'       => QualityInspection::where('status', 'pending')->count(),
        ];

        return view('quality::inspections.index', compact('inspections', 'branches', 'inspectors', 'stats'));
    }

    /**
     * Show form to create new Inspection Order
     */
    public function create(Request $request)
    {
        $suggestedNumber = QualityInspection::generateNumber();
        $branches = Branch::where('is_active', true)->get();
        $templates = QualityTemplate::where('is_active', true)->with('items')->get();
        $items = Item::orderBy('name')->get();
        $suppliers = Client::whereIn('type', ['supplier', 'both'])->orderBy('name')->get();
        $manufacturingOrders = class_exists(ManufacturingOrder::class) ? ManufacturingOrder::latest()->take(30)->get() : collect();
        $projects = class_exists(Project::class) ? Project::whereIn('status', ['in_progress', 'planning'])->get() : collect();
        $inspectors = Employee::where('status', 'active')->orderBy('name')->get();

        $preselectedType = $request->get('type', 'incoming');
        $preselectedItem = $request->get('item_id');

        return view('quality::inspections.create', compact(
            'suggestedNumber', 'branches', 'templates', 'items', 'suppliers',
            'manufacturingOrders', 'projects', 'inspectors', 'preselectedType', 'preselectedItem'
        ));
    }

    /**
     * Store new Inspection Order
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'branch_id'              => 'required|exists:branches,id',
            'inspection_number'      => 'nullable|string|max:60',
            'type'                   => 'required|in:incoming,in_process,final_product,project_site',
            'template_id'            => 'nullable|exists:quality_templates,id',
            'item_id'                => 'nullable|exists:items,id',
            'supplier_id'            => 'nullable|exists:clients,id',
            'manufacturing_order_id' => 'nullable|exists:mfg_orders,id',
            'project_id'             => 'nullable|exists:projects,id',
            'batch_number'           => 'nullable|string|max:100',
            'total_quantity'         => 'required|numeric|min:0.001',
            'sample_size'            => 'required|numeric|min:0.001',
            'inspector_id'           => 'required|exists:hr_employees,id',
            'inspection_date'        => 'required|date',
            'notes'                  => 'nullable|string',
        ]);

        $inspectionNumber = !empty($validated['inspection_number'])
            ? $validated['inspection_number']
            : QualityInspection::generateNumber($tenantId);

        DB::beginTransaction();
        try {
            $inspection = QualityInspection::create([
                'tenant_id'              => $tenantId,
                'branch_id'              => $validated['branch_id'],
                'inspection_number'      => $inspectionNumber,
                'type'                   => $validated['type'],
                'template_id'            => $validated['template_id'] ?? null,
                'item_id'                => $validated['item_id'] ?? null,
                'supplier_id'            => $validated['supplier_id'] ?? null,
                'manufacturing_order_id' => $validated['manufacturing_order_id'] ?? null,
                'project_id'             => $validated['project_id'] ?? null,
                'batch_number'           => $validated['batch_number'] ?? null,
                'total_quantity'         => (float) $validated['total_quantity'],
                'sample_size'            => (float) $validated['sample_size'],
                'passed_quantity'        => 0.000,
                'failed_quantity'        => 0.000,
                'status'                 => 'pending',
                'inspector_id'           => $validated['inspector_id'],
                'inspection_date'        => $validated['inspection_date'],
                'notes'                  => $validated['notes'] ?? null,
                'created_by'             => Auth::id(),
            ]);

            // If a template was selected, initialize checkpoints
            if (!empty($validated['template_id'])) {
                $template = QualityTemplate::with('items')->find($validated['template_id']);
                if ($template) {
                    foreach ($template->items as $tItem) {
                        $inspection->results()->create([
                            'tenant_id'         => $tenantId,
                            'template_item_id'  => $tItem->id,
                            'checkpoint_name'   => $tItem->checkpoint_name,
                            'standard_criteria' => $tItem->formatted_criteria,
                            'observed_value'    => null,
                            'is_passed'         => true,
                            'defect_notes'      => null,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('quality.inspections.show', $inspection->id)
                ->with('success', "تم تسجيل أمر الفحص بنجاح برقم: {$inspection->inspection_number}. يرجى إدخال نتائج الفحص الميداني.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ أمر الفحص: ' . $e->getMessage());
        }
    }

    /**
     * Show Inspection Details & Checkpoints
     */
    public function show(QualityInspection $inspection)
    {
        $inspection->load([
            'branch', 'item', 'supplier', 'manufacturingOrder', 'project',
            'inspector', 'creator', 'template', 'results.templateItem', 'ncrs'
        ]);

        return view('quality::inspections.show', compact('inspection'));
    }

    /**
     * Record Observations and Evaluate Inspection
     */
    public function evaluate(Request $request, QualityInspection $inspection)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'passed_quantity' => 'required|numeric|min:0|max:' . $inspection->total_quantity,
            'failed_quantity' => 'required|numeric|min:0|max:' . $inspection->total_quantity,
            'status'          => 'required|in:passed,rejected,conditionally_passed',
            'notes'           => 'nullable|string',
            'results'         => 'nullable|array',
            'results.*.id'    => 'required|exists:quality_inspection_results,id',
            'results.*.value' => 'nullable|string|max:255',
            'results.*.is_passed' => 'required|in:0,1',
            'results.*.defect_notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            if (!empty($validated['results'])) {
                foreach ($validated['results'] as $rData) {
                    QualityInspectionResult::where('id', $rData['id'])
                        ->where('inspection_id', $inspection->id)
                        ->update([
                            'observed_value' => $rData['value'] ?? null,
                            'is_passed'      => (bool) $rData['is_passed'],
                            'defect_notes'   => $rData['defect_notes'] ?? null,
                        ]);
                }
            }

            $inspection->update([
                'passed_quantity' => (float) $validated['passed_quantity'],
                'failed_quantity' => (float) $validated['failed_quantity'],
                'status'          => $validated['status'],
                'notes'           => $validated['notes'] ?? $inspection->notes,
            ]);

            // Auto-create NCR if rejected or conditionally passed with defect quantity
            if (($validated['status'] === 'rejected' || $validated['status'] === 'conditionally_passed') && (float) $validated['failed_quantity'] > 0) {
                if (!$inspection->ncrs()->where('status', 'open')->exists()) {
                    QualityNcr::create([
                        'tenant_id'              => $tenantId,
                        'branch_id'              => $inspection->branch_id,
                        'ncr_number'             => QualityNcr::generateNumber($tenantId),
                        'inspection_id'          => $inspection->id,
                        'item_id'                => $inspection->item_id,
                        'supplier_id'            => $inspection->supplier_id,
                        'manufacturing_order_id' => $inspection->manufacturing_order_id,
                        'project_id'             => $inspection->project_id,
                        'title'                  => "تقرير عدم مطابقة ناتج عن فحص: {$inspection->inspection_number}",
                        'description'            => "تم رصد عيوب بنتيجة الفحص للمادة/الدفعة ({$inspection->batch_number}). الكمية المرفوضة: {$validated['failed_quantity']}.",
                        'severity'               => $validated['status'] === 'rejected' ? 'major' : 'minor',
                        'root_cause_category'    => $inspection->type === 'incoming' ? 'raw_material' : ($inspection->type === 'project_site' ? 'workmanship' : 'machinery'),
                        'defect_quantity'        => (float) $validated['failed_quantity'],
                        'estimated_loss_amount'  => 0.00,
                        'disposition'            => $validated['status'] === 'rejected' ? 'return_to_vendor' : 'use_as_is',
                        'status'                 => 'open',
                        'reported_by'            => $inspection->inspector_id,
                        'created_by'             => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('quality.inspections.show', $inspection->id)
                ->with('success', 'تم تسجيل واعتماد نتائج الفحص وتحديث حالة الجودة بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء اعتماد نتائج الفحص: ' . $e->getMessage());
        }
    }

    /**
     * Printable Quality Certificate
     */
    public function printCertificate(QualityInspection $inspection)
    {
        $inspection->load([
            'branch', 'item', 'supplier', 'manufacturingOrder', 'project',
            'inspector', 'creator', 'template', 'results'
        ]);

        return view('quality::inspections.print', compact('inspection'));
    }

    /**
     * Delete Inspection
     */
    public function destroy(QualityInspection $inspection)
    {
        $inspection->delete();
        return redirect()->route('quality.inspections.index')->with('success', 'تم حذف سجل الفحص بنجاح.');
    }
}
