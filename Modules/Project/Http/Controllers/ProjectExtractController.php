<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectBoqItem;
use Modules\Project\Entities\ProjectExtract;
use Modules\Project\Entities\ProjectExtractItem;
use Modules\Treasury\Entities\Treasury;
use Modules\Treasury\Entities\TreasuryTransaction;

class ProjectExtractController extends Controller
{
    /**
     * Global List of Progressive Extracts
     */
    public function index(Request $request)
    {
        $query = ProjectExtract::with(['project.client', 'project.branch', 'creator'])->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('extract_type')) {
            $query->where('extract_type', $request->extract_type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('extract_number', 'like', "%{$s}%")
                  ->orWhere('consultant_engineer_name', 'like', "%{$s}%")
                  ->orWhereHas('project', function ($pq) use ($s) {
                      $pq->where('name', 'like', "%{$s}%")
                         ->orWhere('code', 'like', "%{$s}%");
                  });
            });
        }

        $extracts = $query->paginate(15)->withQueryString();
        $projects = Project::orderBy('name')->get();

        $stats = [
            'total_extracts'    => ProjectExtract::count(),
            'total_gross'       => (float) ProjectExtract::sum('gross_amount'),
            'total_net'         => (float) ProjectExtract::sum('net_amount'),
            'total_paid'        => (float) ProjectExtract::sum('paid_amount'),
            'pending_payment'   => (float) ProjectExtract::where('status', 'approved')->selectRaw('SUM(net_amount - paid_amount) as due')->value('due') ?? 0.0,
        ];

        return view('project::extracts.index', compact('extracts', 'projects', 'stats'));
    }

    /**
     * Extracts for a specific Project
     */
    public function projectExtracts(Project $project)
    {
        $project->load(['extracts.creator', 'client', 'branch']);
        return view('project::extracts.project_extracts', compact('project'));
    }

    /**
     * Show form to create new progressive extract
     */
    public function create(Project $project)
    {
        $project->load(['boqItems' => fn($q) => $q->orderBy('id')]);

        if ($project->boqItems->isEmpty()) {
            return redirect()->route('projects.projects.show', ['project' => $project->id, 'tab' => 'boq'])
                ->with('warning', 'يرجى تسجيل بنود المقايسة وجدول الكميات (BOQ) أولاً قبل إنشاء المستخلص.');
        }

        // Calculate previous cumulative gross from approved extracts
        $prevGrossAmount = (float) $project->extracts()
            ->whereIn('status', ['approved', 'paid'])
            ->latest('id')
            ->value('gross_amount') ?? 0.00;

        $suggestedNumber = ProjectExtract::generateNumber($project);

        return view('project::extracts.create', compact('project', 'prevGrossAmount', 'suggestedNumber'));
    }

    /**
     * Store new progressive extract
     */
    public function store(Request $request, Project $project)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'extract_number'           => 'nullable|string|max:60',
            'extract_type'             => 'required|in:advance,progress,final',
            'period_from'              => 'required|date',
            'period_to'                => 'required|date|after_or_equal:period_from',
            'issue_date'               => 'required|date',
            'other_deductions'         => 'nullable|numeric|min:0',
            'tax_rate'                 => 'nullable|numeric|min:0|max:100',
            'consultant_engineer_name' => 'nullable|string|max:190',
            'notes'                    => 'nullable|string',
            'items'                    => 'required|array|min:1',
            'items.*.boq_item_id'      => 'required|exists:project_boq_items,id',
            'items.*.current_quantity' => 'required|numeric|min:0',
        ]);

        $extractNumber = !empty($validated['extract_number'])
            ? $validated['extract_number']
            : ProjectExtract::generateNumber($project);

        $prevGrossAmount = (float) $project->extracts()
            ->whereIn('status', ['approved', 'paid'])
            ->latest('id')
            ->value('gross_amount') ?? 0.00;

        DB::beginTransaction();
        try {
            $extract = ProjectExtract::create([
                'tenant_id'                 => $tenantId,
                'project_id'                => $project->id,
                'extract_number'            => $extractNumber,
                'extract_type'              => $validated['extract_type'],
                'period_from'               => $validated['period_from'],
                'period_to'                 => $validated['period_to'],
                'issue_date'                => $validated['issue_date'],
                'gross_amount'              => 0.00,
                'previous_gross_amount'     => $prevGrossAmount,
                'current_gross_amount'      => 0.00,
                'advance_payment_deduction' => 0.00,
                'retention_deduction'       => 0.00,
                'other_deductions'          => (float) ($validated['other_deductions'] ?? 0.00),
                'net_subtotal'              => 0.00,
                'tax_rate'                  => (float) ($validated['tax_rate'] ?? 15.00),
                'tax_amount'                => 0.00,
                'net_amount'                => 0.00,
                'paid_amount'               => 0.00,
                'status'                    => 'draft',
                'consultant_engineer_name'  => $validated['consultant_engineer_name'] ?? null,
                'notes'                     => $validated['notes'] ?? null,
                'created_by'                => Auth::id(),
            ]);

            foreach ($validated['items'] as $itemData) {
                $boqItem = ProjectBoqItem::findOrFail($itemData['boq_item_id']);

                $prevQty = (float) $boqItem->executed_quantity;
                $currentQty = (float) $itemData['current_quantity'];
                $totalQty = $prevQty + $currentQty;
                $unitPrice = (float) $boqItem->unit_price;

                $currentAmt = round($currentQty * $unitPrice, 2);
                $totalAmt = round($totalQty * $unitPrice, 2);

                $completion = $boqItem->revised_quantity > 0
                    ? min(100.00, round(($totalQty / $boqItem->revised_quantity) * 100, 2))
                    : 0.00;

                $extract->items()->create([
                    'tenant_id'             => $tenantId,
                    'boq_item_id'           => $boqItem->id,
                    'item_description'      => $boqItem->description,
                    'unit'                  => $boqItem->unit,
                    'unit_price'            => $unitPrice,
                    'previous_quantity'     => $prevQty,
                    'current_quantity'      => $currentQty,
                    'total_quantity'        => $totalQty,
                    'current_amount'        => $currentAmt,
                    'total_amount'          => $totalAmt,
                    'completion_percentage' => $completion,
                ]);
            }

            // Calculate deductions & VAT
            $extract->recalculateTotals();

            DB::commit();

            return redirect()->route('projects.extracts.show', $extract->id)
                ->with('success', 'تم إنشاء المستخلص بنجاح برقم: ' . $extract->extract_number);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ المستخلص: ' . $e->getMessage());
        }
    }

    /**
     * Show Extract Details
     */
    public function show(ProjectExtract $extract)
    {
        $extract->load([
            'project.client', 'project.branch', 'creator',
            'items.boqItem'
        ]);

        $treasuries = class_exists(Treasury::class)
            ? Treasury::where('is_active', true)->get()
            : collect();

        return view('project::extracts.show', compact('extract', 'treasuries'));
    }

    /**
     * Printable Contractor Extract Certificate
     */
    public function print(ProjectExtract $extract)
    {
        $extract->load([
            'project.client', 'project.branch', 'creator',
            'items.boqItem'
        ]);

        return view('project::extracts.print', compact('extract'));
    }

    /**
     * Approve Extract & Update BOQ Executed Quantities
     */
    public function approve(ProjectExtract $extract)
    {
        if ($extract->status === 'approved' || $extract->status === 'paid') {
            return back()->with('info', 'المستخلص معتمد مسبقاً.');
        }

        DB::beginTransaction();
        try {
            $extract->load('items.boqItem');

            foreach ($extract->items as $item) {
                if ($item->boqItem) {
                    $item->boqItem->update([
                        'executed_quantity'     => $item->total_quantity,
                        'completion_percentage' => $item->completion_percentage,
                    ]);
                }
            }

            $extract->update(['status' => 'approved']);

            // Recalculate project overall financials and progress
            $extract->project->recalculateFinancials();

            DB::commit();

            return redirect()->route('projects.extracts.show', $extract->id)
                ->with('success', 'تم اعتماد المستخلص رسمياً وتحديث كميات ونسب إنجاز المقايسة والمشروع بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'تعذر اعتماد المستخلص: ' . $e->getMessage());
        }
    }

    /**
     * Record Client Payment for Extract
     */
    public function recordPayment(Request $request, ProjectExtract $extract)
    {
        $remainingDue = max(0.00, (float) $extract->net_amount - (float) $extract->paid_amount);

        $validated = $request->validate([
            'amount'         => 'required|numeric|min:0.01|max:' . ($remainingDue + 0.01),
            'payment_method' => 'required|in:cash,bank_transfer,cheque',
            'treasury_id'    => 'nullable|exists:treasuries,id',
            'payment_date'   => 'required|date',
            'notes'          => 'nullable|string|max:255',
        ]);

        $amount = (float) $validated['amount'];
        $newPaid = (float) $extract->paid_amount + $amount;
        $status = ($newPaid >= (float) $extract->net_amount - 0.01) ? 'paid' : $extract->status;

        DB::beginTransaction();
        try {
            $extract->update([
                'paid_amount' => $newPaid,
                'status'      => $status,
            ]);

            // Treasury integration
            if (!empty($validated['treasury_id']) && class_exists(Treasury::class)) {
                $treasury = Treasury::find($validated['treasury_id']);
                if ($treasury) {
                    $balanceBefore = (float) $treasury->current_balance;
                    $balanceAfter = $balanceBefore + $amount;

                    TreasuryTransaction::create([
                        'tenant_id'        => $extract->tenant_id,
                        'treasury_id'      => $treasury->id,
                        'type'             => 'in',
                        'amount'           => $amount,
                        'balance_before'   => $balanceBefore,
                        'balance_after'    => $balanceAfter,
                        'source_type'      => 'voucher',
                        'source_id'        => $extract->id,
                        'transaction_date' => $validated['payment_date'],
                        'description'      => "تحصيل مستخلص مقاولة رقم {$extract->extract_number} - مشروع {$extract->project->name}",
                        'created_by'       => Auth::id(),
                    ]);

                    $treasury->update(['current_balance' => $balanceAfter]);
                }
            }

            // Recalculate project totals
            $extract->project->recalculateFinancials();

            DB::commit();

            return redirect()->route('projects.extracts.show', $extract->id)
                ->with('success', "تم تسجيل سداد بقيمة " . number_format($amount, 2) . " ر.س بنجاح.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تسجيل السداد: ' . $e->getMessage());
        }
    }
}
