<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\EmployeeLeave;

class LeaveController extends Controller
{
    /**
     * Employee Leaves Listing
     */
    public function index(Request $request)
    {
        $query = EmployeeLeave::with(['employee', 'approver'])->latest();

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->paginate(15)->withQueryString();
        $employees = Employee::active()->orderBy('name')->get();

        $stats = [
            'total_leaves'    => EmployeeLeave::count(),
            'approved_leaves' => EmployeeLeave::where('status', 'approved')->count(),
            'pending_leaves'  => EmployeeLeave::where('status', 'pending')->count(),
            'current_on_leave'=> Employee::where('status', 'on_leave')->count(),
        ];

        return view('hr::leaves.index', compact('leaves', 'employees', 'stats'));
    }

    /**
     * Store Leave Request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hr_employees,id',
            'leave_type'  => 'required|in:annual,sick,emergency,unpaid',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'nullable|string|max:255',
            'status'      => 'required|in:approved,pending',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $daysCount = $start->diffInDays($end) + 1;

        $leave = EmployeeLeave::create([
            'employee_id' => $validated['employee_id'],
            'leave_type'  => $validated['leave_type'],
            'start_date'  => $validated['start_date'],
            'end_date'    => $validated['end_date'],
            'days_count'  => $daysCount,
            'reason'      => $validated['reason'] ?? null,
            'status'      => $validated['status'],
            'approved_by' => $validated['status'] === 'approved' ? Auth::id() : null,
        ]);

        // If currently in effect, update employee status to on_leave
        if ($validated['status'] === 'approved' && now()->between($start, $end)) {
            $emp = Employee::find($validated['employee_id']);
            if ($emp) {
                $emp->update(['status' => 'on_leave']);
            }
        }

        return back()->with('success', 'تم تسجيل طلب الإجازة بنجاح (' . $daysCount . ' يوم).');
    }

    /**
     * Update Leave Status (Approve / Reject)
     */
    public function updateStatus(Request $request, EmployeeLeave $leave)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $leave->update([
            'status'      => $validated['status'],
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم تحديث حالة طلب الإجازة بنجاح.');
    }

    /**
     * Delete Leave Record
     */
    public function destroy(EmployeeLeave $leave)
    {
        $leave->delete();
        return back()->with('success', 'تم حذف سجل الإجازة بنجاح.');
    }
}
