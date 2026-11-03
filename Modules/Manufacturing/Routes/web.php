<?php

use Illuminate\Support\Facades\Route;
use Modules\Manufacturing\Http\Controllers\BomController;
use Modules\Manufacturing\Http\Controllers\ManufacturingOrderController;
use Modules\Manufacturing\Http\Controllers\ManufacturingReportController;
use Modules\Manufacturing\Http\Controllers\WorkstationController;

// 1. Workstations & Production Lines
Route::resource('workstations', WorkstationController::class)->except(['create', 'show', 'edit']);

// 2. Bills of Materials (BOM)
Route::resource('boms', BomController::class);
Route::post('boms/{bom}/duplicate', [BomController::class, 'duplicate'])->name('boms.duplicate');

// 3. Manufacturing / Work Orders (MO)
Route::resource('orders', ManufacturingOrderController::class);
Route::post('orders/{order}/confirm', [ManufacturingOrderController::class, 'confirm'])->name('orders.confirm');
Route::post('orders/{order}/start', [ManufacturingOrderController::class, 'start'])->name('orders.start');
Route::post('orders/{order}/complete', [ManufacturingOrderController::class, 'complete'])->name('orders.complete');
Route::post('orders/{order}/cancel', [ManufacturingOrderController::class, 'cancel'])->name('orders.cancel');
Route::get('orders/{order}/print', [ManufacturingOrderController::class, 'print'])->name('orders.print');

// 4. Manufacturing Analytics & Reports
Route::get('reports', [ManufacturingReportController::class, 'index'])->name('reports.index');
