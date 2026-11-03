<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Designation;
use Modules\HR\Entities\Employee;

class EmployeeController extends Controller
{
    /**
     * Employees Directory Listing
     */
    public function index(Request $request)
    {
        $query = Employee::with(['branch', 'department', 'designation'])->latest();

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('emp_code', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('national_id', 'like', "%{$s}%");
            });
        }

        $employees = $query->paginate(15)->withQueryString();

        $departments = Department::active()->get();
        $branches = Branch::all();

        $stats = [
            'total_employees' => Employee::count(),
            'active_count'    => Employee::where('status', 'active')->count(),
            'on_leave_count'  => Employee::where('status', 'on_leave')->count(),
            'total_salaries'  => (float) Employee::where('status', 'active')->get()->sum('total_salary'),
        ];

        return view('hr::employees.index', compact('employees', 'departments', 'branches', 'stats'));
    }

    /**
     * Show Create Employee Form
     */
    public function create()
    {
        $departments = Department::active()->with('designations')->get();
        $designations = Designation::active()->get();
        $branches = Branch::all();
        $nextCode = Employee::generateCode();

        return view('hr::employees.create', compact('departments', 'designations', 'branches', 'nextCode'));
    }

    /**
     * Store Employee
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'emp_code'            => 'required|string|max:50',
            'name'                => 'required|string|max:150',
            'branch_id'           => 'nullable|exists:branches,id',
            'department_id'       => 'nullable|exists:hr_departments,id',
            'designation_id'      => 'nullable|exists:hr_designations,id',
            'national_id'         => 'nullable|string|max:50',
            'phone'               => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:100',
            'hire_date'           => 'required|date',
            'birth_date'          => 'nullable|date',
            'contract_type'       => 'required|in:full_time,part_time,probation,contract',
            'basic_salary'        => 'required|numeric|min:0',
            'housing_allowance'   => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'other_allowances'    => 'nullable|numeric|min:0',
            'bank_name'           => 'nullable|string|max:100',
            'bank_account'        => 'nullable|string|max:100',
            'iban'                => 'nullable|string|max:100',
            'status'              => 'required|in:active,on_leave,suspended,terminated',
            'notes'               => 'nullable|string',
        ]);

        $employee = Employee::create($validated);

        return redirect()->route('hr.employees.show', $employee->id)
            ->with('success', 'تم تسجيل وإضافة الموظف بنجاح! الرقم الوظيفي: ' . $employee->emp_code);
    }

    /**
     * Employee Profile
     */
    public function show(Employee $employee)
    {
        $employee->load([
            'branch',
            'department',
            'designation',
            'activeLoans',
            'leaves' => fn($q) => $q->latest()->take(5),
            'attendances' => fn($q) => $q->latest('date')->take(10),
            'payrollItems.payroll' => fn($q) => $q->latest()->take(6),
        ]);

        return view('hr::employees.show', compact('employee'));
    }

    /**
     * Show Edit Form
     */
    public function edit(Employee $employee)
    {
        $departments = Department::active()->get();
        $designations = Designation::active()->get();
        $branches = Branch::all();

        return view('hr::employees.edit', compact('employee', 'departments', 'designations', 'branches'));
    }

    /**
     * Update Employee
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:150',
            'branch_id'           => 'nullable|exists:branches,id',
            'department_id'       => 'nullable|exists:hr_departments,id',
            'designation_id'      => 'nullable|exists:hr_designations,id',
            'national_id'         => 'nullable|string|max:50',
            'phone'               => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:100',
            'hire_date'           => 'required|date',
            'birth_date'          => 'nullable|date',
            'contract_type'       => 'required|in:full_time,part_time,probation,contract',
            'basic_salary'        => 'required|numeric|min:0',
            'housing_allowance'   => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'other_allowances'    => 'nullable|numeric|min:0',
            'bank_name'           => 'nullable|string|max:100',
            'bank_account'        => 'nullable|string|max:100',
            'iban'                => 'nullable|string|max:100',
            'status'              => 'required|in:active,on_leave,suspended,terminated',
            'notes'               => 'nullable|string',
        ]);

        $employee->update($validated);

        return redirect()->route('hr.employees.show', $employee->id)
            ->with('success', 'تم تحديث بيانات الموظف بنجاح.');
    }

    /**
     * Delete Employee
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('hr.employees.index')->with('success', 'تم حذف ملف الموظف بنجاح.');
    }
}
