<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\HR\Entities\Department;
use Modules\HR\Entities\Designation;

class DepartmentController extends Controller
{
    /**
     * Departments & Designations Listing
     */
    public function index()
    {
        $departments = Department::withCount('employees')->with('designations')->get();
        $designations = Designation::with('department')->withCount('employees')->get();

        return view('hr::departments.index', compact('departments', 'designations'));
    }

    /**
     * Store Department
     */
    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'nullable|string|max:50',
            'manager_name' => 'nullable|string|max:100',
        ]);

        Department::create($validated);

        return back()->with('success', 'تمت إضافة القسم الإداري بنجاح.');
    }

    /**
     * Update Department
     */
    public function updateDepartment(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'nullable|string|max:50',
            'manager_name' => 'nullable|string|max:100',
            'is_active'    => 'required|boolean',
        ]);

        $department->update($validated);

        return back()->with('success', 'تم تحديث بيانات القسم بنجاح.');
    }

    /**
     * Delete Department
     */
    public function destroyDepartment(Department $department)
    {
        if ($department->employees()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف القسم لوجود موظفين مرتبطين به!');
        }

        $department->delete();
        return back()->with('success', 'تم حذف القسم بنجاح.');
    }

    /**
     * Store Designation (الوظيفة)
     */
    public function storeDesignation(Request $request)
    {
        $validated = $request->validate([
            'department_id' => 'nullable|exists:hr_departments,id',
            'title'         => 'required|string|max:100',
            'description'   => 'nullable|string|max:255',
        ]);

        Designation::create($validated);

        return back()->with('success', 'تمت إضافة المسمى الوظيفي بنجاح.');
    }

    /**
     * Delete Designation
     */
    public function destroyDesignation(Designation $designation)
    {
        if ($designation->employees()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف المسمى الوظيفي لوجود موظفين يشغلونه!');
        }

        $designation->delete();
        return back()->with('success', 'تم حذف المسمى الوظيفي بنجاح.');
    }
}

