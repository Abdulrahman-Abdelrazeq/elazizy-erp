<?php

use Illuminate\Support\Facades\Route;
use Modules\POS\Http\Controllers\PosController;
use Modules\POS\Http\Controllers\ShiftController;
use Modules\POS\Http\Controllers\TerminalController;

/*
|--------------------------------------------------------------------------
| POS & Cashier Module Web Routes - نظام العزيزي ERP
|--------------------------------------------------------------------------
*/

// Main POS Screen & Checkout
Route::get('/', [PosController::class, 'screen'])->name('screen');
Route::post('/checkout', [PosController::class, 'checkout'])->name('checkout');
Route::post('/hold', [PosController::class, 'holdOrder'])->name('hold');
Route::get('/held/{shift}', [PosController::class, 'getHeldOrders'])->name('held.index');
Route::delete('/held/{order}', [PosController::class, 'recallHeldOrder'])->name('held.recall');
Route::get('/receipt/{invoice}', [PosController::class, 'receipt'])->name('receipt');

// Cashier Shifts & Drawer Log (ورديات الكاشير وإدارة الدرج)
Route::prefix('shifts')->name('shifts.')->group(function () {
    Route::get('/', [ShiftController::class, 'index'])->name('index');
    Route::post('/open', [ShiftController::class, 'open'])->name('open');
    Route::post('/{shift}/close', [ShiftController::class, 'close'])->name('close');
    Route::get('/{shift}', [ShiftController::class, 'show'])->name('show');
    Route::get('/{shift}/print-z', [ShiftController::class, 'printZReport'])->name('print_z');
});

// POS Terminals (أجهزة ونقاط البيع)
Route::prefix('terminals')->name('terminals.')->group(function () {
    Route::get('/', [TerminalController::class, 'index'])->name('index');
    Route::post('/', [TerminalController::class, 'store'])->name('store');
    Route::put('/{terminal}', [TerminalController::class, 'update'])->name('update');
    Route::delete('/{terminal}', [TerminalController::class, 'destroy'])->name('destroy');
});
