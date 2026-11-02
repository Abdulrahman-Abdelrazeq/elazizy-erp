<?php

use Illuminate\Support\Facades\Route;
use Modules\Treasury\Http\Controllers\ExpenseCategoryController;
use Modules\Treasury\Http\Controllers\ReportController;
use Modules\Treasury\Http\Controllers\TransferController;
use Modules\Treasury\Http\Controllers\TreasuryController;
use Modules\Treasury\Http\Controllers\VoucherController;

/*
|--------------------------------------------------------------------------
| Module 6: Treasury & Expenses Web Routes - نظام العزيزي ERP
|--------------------------------------------------------------------------
*/

// Treasuries & Banks (الخزائن والحسابات البنكية)
Route::prefix('accounts')->name('treasuries.')->group(function () {
    Route::get('/', [TreasuryController::class, 'index'])->name('index');
    Route::post('/', [TreasuryController::class, 'store'])->name('store');
    Route::put('/{treasury}', [TreasuryController::class, 'update'])->name('update');
    Route::delete('/{treasury}', [TreasuryController::class, 'destroy'])->name('destroy');
    Route::get('/{treasury}/statement', [TreasuryController::class, 'statement'])->name('statement');
});

// Categories (بنود المصروفات والإيرادات)
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [ExpenseCategoryController::class, 'index'])->name('index');
    Route::post('/', [ExpenseCategoryController::class, 'store'])->name('store');
    Route::put('/{category}', [ExpenseCategoryController::class, 'update'])->name('update');
    Route::delete('/{category}', [ExpenseCategoryController::class, 'destroy'])->name('destroy');
});

// Vouchers (سندات القبض والصرف)
Route::prefix('vouchers')->name('vouchers.')->group(function () {
    Route::get('/', [VoucherController::class, 'index'])->name('index');
    Route::get('/create', [VoucherController::class, 'create'])->name('create');
    Route::post('/', [VoucherController::class, 'store'])->name('store');
    Route::get('/{voucher}', [VoucherController::class, 'show'])->name('show');
    Route::get('/{voucher}/print', [VoucherController::class, 'print'])->name('print');
    Route::delete('/{voucher}', [VoucherController::class, 'destroy'])->name('destroy');
});

// Transfers (التحويلات بين الخزائن والبنوك)
Route::prefix('transfers')->name('transfers.')->group(function () {
    Route::get('/', [TransferController::class, 'index'])->name('index');
    Route::get('/create', [TransferController::class, 'create'])->name('create');
    Route::post('/', [TransferController::class, 'store'])->name('store');
});

// Reports (تقارير التدفق النقدي والسيولة)
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/cash-flow', [ReportController::class, 'cashFlow'])->name('cash_flow');
});
