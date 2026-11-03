<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HR\Entities\Attendance;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Employee;

class AttendanceController extends Controller
{
    /**
     * Daily Attendance Sheet
     */
    public function index(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $departmentId = $request->input('department_id');

        $employeesQuery = Employee::with(['department', 'designation'])->active();
        if ($departmentId) {
            $employeesQuery->where('department_id', $departmentId);
        }
        $employees = $employeesQuery->orderBy('name')->get();

        $attendances = Attendance::where('date', $date)->get()->keyBy('employee_id');

        $departments = Department::active()->get();

        $stats = [
            'present_count' => Attendance::where('date', $date)->where('status', 'present')->count(),
            'absent_count'  => Attendance::where('date', $date)->where('status', 'absent')->count(),
            'late_count'    => Attendance::where('date', $date)->where('status', 'late')->count(),
            'on_leave'      => Attendance::where('date', $date)->where('status', 'on_leave')->count(),
        ];

        return view('hr::attendances.index', compact('employees', 'attendances', 'departments', 'date', 'stats'));
    }

    /**
     * Batch Store Attendance for a Date
     */
    public function storeBatch(Request $request)
    {
        $validated = $request->validate([
            'date'                  => 'required|date',
            'attendances'           => 'required|array',
            'attendances.*.status'  => 'required|in:present,absent,late,on_leave',
            'attendances.*.check_in' => 'nullable|string',
            'attendances.*.check_out'=> 'nullable|string',
            'attendances.*.late_minutes' => 'nullable|integer|min:0',
            'attendances.*.overtime_hours' => 'nullable|numeric|min:0',
        ]);

        $tenantId = currentTenant() ? currentTenant()->id : 1;
        $date = $validated['date'];

        foreach ($validated['attendances'] as $empId => $data) {
            Attendance::updateOrCreate(
                [
                    'tenant_id'   => $tenantId,
                    'employee_id' => $empId,
                    'date'        => $date,
                ],
                [
                    'status'         => $data['status'],
                    'check_in'       => !empty($data['check_in']) ? $data['check_in'] : null,
                    'check_out'      => !empty($data['check_out']) ? $data['check_out'] : null,
                    'late_minutes'   => $data['late_minutes'] ?? 0,
                    'overtime_hours' => $data['overtime_hours'] ?? 0.00,
                ]
            );
        }

        return back()->with('success', 'تم حفظ وتحديث كشف الحضور والانصراف لتاريخ ' . $date . ' بنجاح.');
    }
}
