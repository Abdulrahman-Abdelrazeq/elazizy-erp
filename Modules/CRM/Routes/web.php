<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\BalanceAdjustmentController;
use Modules\CRM\Http\Controllers\ClientGroupController;
use Modules\CRM\Http\Controllers\CustomerController;
use Modules\CRM\Http\Controllers\DebtScheduleController;
use Modules\CRM\Http\Controllers\SupplierController;

/*
|--------------------------------------------------------------------------
| CRM Module Web Routes - نظام العزيزي ERP
|--------------------------------------------------------------------------
*/

// Customers (العملاء)
Route::prefix('customers')->name('customers.')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('/create', [CustomerController::class, 'create'])->name('create');
    Route::post('/', [CustomerController::class, 'store'])->name('store');
    Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
    Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
    Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    Route::get('/{customer}/statement', [CustomerController::class, 'statement'])->name('statement');
});

// Suppliers (الموردين)
Route::prefix('suppliers')->name('suppliers.')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('index');
    Route::get('/create', [SupplierController::class, 'create'])->name('create');
    Route::post('/', [SupplierController::class, 'store'])->name('store');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
    Route::get('/{supplier}/statement', [SupplierController::class, 'statement'])->name('statement');
});

// Client Groups & Pricing Tiers (شرائح تصنيف العملاء)
Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('/', [ClientGroupController::class, 'index'])->name('index');
    Route::post('/', [ClientGroupController::class, 'store'])->name('store');
    Route::put('/{group}', [ClientGroupController::class, 'update'])->name('update');
    Route::delete('/{group}', [ClientGroupController::class, 'destroy'])->name('destroy');
});

// Debt Schedules & Installments (جدولة الديون والأقساط)
Route::prefix('debts')->name('debts.')->group(function () {
    Route::get('/', [DebtScheduleController::class, 'index'])->name('index');
    Route::post('/', [DebtScheduleController::class, 'store'])->name('store');
    Route::post('/{schedule}/pay', [DebtScheduleController::class, 'markPaid'])->name('pay');
});

// Balance Adjustments & Write-Offs (شطب وتسويات الديون)
Route::prefix('adjustments')->name('adjustments.')->group(function () {
    Route::get('/', [BalanceAdjustmentController::class, 'index'])->name('index');
    Route::post('/', [BalanceAdjustmentController::class, 'store'])->name('store');
});
