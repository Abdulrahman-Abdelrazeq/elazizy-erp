<?php

use Illuminate\Support\Facades\Route;
use Modules\Branch\Http\Controllers\BranchAnalyticsController;
use Modules\Branch\Http\Controllers\BranchController;
use Modules\Branch\Http\Controllers\BranchTransferController;
use Modules\Branch\Http\Controllers\BranchUserController;

// 1. Branches Management
Route::resource('branches', BranchController::class);

// 2. Branch Users & Permissions
Route::get('users', [BranchUserController::class, 'index'])->name('users.index');
Route::post('users', [BranchUserController::class, 'store'])->name('users.store');
Route::delete('users/{branchUser}', [BranchUserController::class, 'destroy'])->name('users.destroy');

// 3. Inter-Branch Logistics Transfers
Route::resource('transfers', BranchTransferController::class);
Route::post('transfers/{transfer}/dispatch', [BranchTransferController::class, 'dispatch'])->name('transfers.dispatch');
Route::post('transfers/{transfer}/receive', [BranchTransferController::class, 'receive'])->name('transfers.receive');
Route::post('transfers/{transfer}/cancel', [BranchTransferController::class, 'cancel'])->name('transfers.cancel');
Route::get('transfers/{transfer}/print', [BranchTransferController::class, 'print'])->name('transfers.print');

// 4. Comparative Analytics & Reports
Route::get('analytics', [BranchAnalyticsController::class, 'index'])->name('analytics.index');
