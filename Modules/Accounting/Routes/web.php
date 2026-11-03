<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\Http\Controllers\ChartOfAccountsController;
use Modules\Accounting\Http\Controllers\CostCenterController;
use Modules\Accounting\Http\Controllers\GeneralLedgerController;
use Modules\Accounting\Http\Controllers\JournalEntryController;
use Modules\Accounting\Http\Controllers\TrialBalanceController;

/*
|--------------------------------------------------------------------------
| Module 7: Accounting & General Ledger Web Routes - نظام العزيزي ERP
|--------------------------------------------------------------------------
*/

// Chart of Accounts (شجرة الحسابات)
Route::prefix('accounts')->name('accounts.')->group(function () {
    Route::get('/', [ChartOfAccountsController::class, 'index'])->name('index');
    Route::post('/', [ChartOfAccountsController::class, 'store'])->name('store');
    Route::put('/{account}', [ChartOfAccountsController::class, 'update'])->name('update');
    Route::delete('/{account}', [ChartOfAccountsController::class, 'destroy'])->name('destroy');
    Route::get('/next-code', [ChartOfAccountsController::class, 'nextCode'])->name('next_code');
});

// Cost Centers (مراكز التكلفة)
Route::prefix('cost-centers')->name('cost_centers.')->group(function () {
    Route::get('/', [CostCenterController::class, 'index'])->name('index');
    Route::post('/', [CostCenterController::class, 'store'])->name('store');
    Route::put('/{costCenter}', [CostCenterController::class, 'update'])->name('update');
    Route::delete('/{costCenter}', [CostCenterController::class, 'destroy'])->name('destroy');
});

// Double-Entry Journal Entries (القيود اليومية المزدوجة)
Route::prefix('journal-entries')->name('journal-entries.')->group(function () {
    Route::get('/', [JournalEntryController::class, 'index'])->name('index');
    Route::get('/create', [JournalEntryController::class, 'create'])->name('create');
    Route::post('/', [JournalEntryController::class, 'store'])->name('store');
    Route::get('/{entry}', [JournalEntryController::class, 'show'])->name('show');
    Route::get('/{entry}/print', [JournalEntryController::class, 'print'])->name('print');
    Route::delete('/{entry}', [JournalEntryController::class, 'destroy'])->name('destroy');
});

// General Ledger (دفتر الأستاذ العام)
Route::get('/ledger', [GeneralLedgerController::class, 'index'])->name('ledger.index');

// Trial Balance (ميزان المراجعة)
Route::get('/trial-balance', [TrialBalanceController::class, 'index'])->name('trial_balance.index');

