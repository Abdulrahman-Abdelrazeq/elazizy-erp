<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\AttendanceController;
use Modules\HR\Http\Controllers\DepartmentController;
use Modules\HR\Http\Controllers\EmployeeController;
use Modules\HR\Http\Controllers\LeaveController;
use Modules\HR\Http\Controllers\LoanController;
use Modules\HR\Http\Controllers\PayrollController;

// 1. Employees Directory
Route::resource('employees', EmployeeController::class);

// 2. Departments & Designations
Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
Route::post('departments', [DepartmentController::class, 'storeDepartment'])->name('departments.store');
Route::put('departments/{department}', [DepartmentController::class, 'updateDepartment'])->name('departments.update');
Route::delete('departments/{department}', [DepartmentController::class, 'destroyDepartment'])->name('departments.destroy');

Route::post('designations', [DepartmentController::class, 'storeDesignation'])->name('designations.store');
Route::delete('designations/{designation}', [DepartmentController::class, 'destroyDesignation'])->name('designations.destroy');

// 3. Employee Loans & Advances
Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
Route::post('loans', [LoanController::class, 'store'])->name('loans.store');
Route::post('loans/{loan}/repay', [LoanController::class, 'repay'])->name('loans.repay');
Route::delete('loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');

// 4. Employee Leaves
Route::get('leaves', [LeaveController::class, 'index'])->name('leaves.index');
Route::post('leaves', [LeaveController::class, 'store'])->name('leaves.store');
Route::put('leaves/{leave}/status', [LeaveController::class, 'updateStatus'])->name('leaves.status');
Route::delete('leaves/{leave}', [LeaveController::class, 'destroy'])->name('leaves.destroy');

// 5. Attendances
Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
Route::post('attendances/batch', [AttendanceController::class, 'storeBatch'])->name('attendances.store');

// 6. Payroll Batches & Payslips
Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
Route::post('payrolls/generate', [PayrollController::class, 'generate'])->name('payrolls.generate');
Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
Route::get('payrolls/{payroll}/print', [PayrollController::class, 'print'])->name('payrolls.print');
Route::get('payroll-items/{item}/slip', [PayrollController::class, 'printSlip'])->name('payrolls.slip');
Route::post('payrolls/{payroll}/approve', [PayrollController::class, 'approve'])->name('payrolls.approve');
Route::post('payrolls/{payroll}/pay', [PayrollController::class, 'pay'])->name('payrolls.pay');
Route::delete('payrolls/{payroll}', [PayrollController::class, 'destroy'])->name('payrolls.destroy');
