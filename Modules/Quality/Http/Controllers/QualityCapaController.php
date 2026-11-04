<?php

namespace Modules\Quality\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\HR\Entities\Employee;
use Modules\Quality\Entities\QualityCapa;
use Modules\Quality\Entities\QualityNcr;

class QualityCapaController extends Controller
{
    /**
     * List CAPA Actions
     */
    public function index(Request $request)
    {
        $query = QualityCapa::with(['ncr.item', 'assignee', 'creator'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('effectiveness_status')) {
            $query->where('effectiveness_status', $request->effectiveness_status);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('capa_number', 'like', "%{$s}%")
                  ->orWhere('title', 'like', "%{$s}%")
                  ->orWhere('action_plan', 'like', "%{$s}%");
            });
        }

        $capas = $query->paginate(15)->withQueryString();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $ncrs = QualityNcr::whereIn('status', ['open', 'under_investigation', 'action_taken'])->latest()->get();

        $stats = [
            'total'      => QualityCapa::count(),
            'corrective' => QualityCapa::where('type', 'corrective')->count(),
            'preventive' => QualityCapa::where('type', 'preventive')->count(),
            'verified'   => QualityCapa::where('status', 'verified_closed')->count(),
            'pending'    => QualityCapa::where('status', 'pending')->count(),
        ];

        return view('quality::capas.index', compact('capas', 'employees', 'ncrs', 'stats'));
    }

    /**
     * Store new CAPA linked to an NCR
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'ncr_id'                 => 'required|exists:quality_ncrs,id',
            'type'                   => 'required|in:corrective,preventive',
            'title'                  => 'required|string|max:190',
            'action_plan'            => 'required|string',
            'assigned_to'            => 'nullable|exists:hr_employees,id',
            'due_date'               => 'required|date',
            'effectiveness_criteria' => 'nullable|string',
        ]);

        $capaNumber = QualityCapa::generateNumber($tenantId);

        $capa = QualityCapa::create([
            'tenant_id'              => $tenantId,
            'capa_number'            => $capaNumber,
            'ncr_id'                 => $validated['ncr_id'],
            'type'                   => $validated['type'],
            'title'                  => $validated['title'],
            'action_plan'            => $validated['action_plan'],
            'assigned_to'            => $validated['assigned_to'] ?? null,
            'due_date'               => $validated['due_date'],
            'effectiveness_criteria' => $validated['effectiveness_criteria'] ?? null,
            'effectiveness_status'   => 'pending_evaluation',
            'status'                 => 'pending',
            'created_by'             => Auth::id(),
        ]);

        return back()->with('success', "تم تسجيل الإجراء الوقائي/التصحيحي بنجاح برقم: {$capa->capa_number}.");
    }

    /**
     * Update CAPA Progress
     */
    public function update(Request $request, QualityCapa $capa)
    {
        $validated = $request->validate([
            'status'          => 'required|in:pending,in_progress,completed,verified_closed',
            'completion_date' => 'nullable|date',
        ]);

        $capa->update([
            'status'          => $validated['status'],
            'completion_date' => $validated['completion_date'] ?? ($validated['status'] === 'completed' ? now()->toDateString() : $capa->completion_date),
        ]);

        return back()->with('success', 'تم تحديث حالة تنفيذ الإجراء بنجاح.');
    }

    /**
     * Verify Effectiveness and Close CAPA
     */
    public function verifyEffectiveness(Request $request, QualityCapa $capa)
    {
        $validated = $request->validate([
            'effectiveness_status' => 'required|in:effective,ineffective',
            'notes'                => 'nullable|string',
        ]);

        $newStatus = $validated['effectiveness_status'] === 'effective' ? 'verified_closed' : 'in_progress';

        $capa->update([
            'effectiveness_status' => $validated['effectiveness_status'],
            'status'               => $newStatus,
            'completion_date'      => $capa->completion_date ?? now()->toDateString(),
        ]);

        return back()->with('success', 'تم تسجيل تقييم فاعلية الإجراء وإغلاق الإجراء رسمياً.');
    }

    /**
     * Delete CAPA
     */
    public function destroy(QualityCapa $capa)
    {
        $capa->delete();
        return back()->with('success', 'تم حذف الإجراء بنجاح.');
    }
}
