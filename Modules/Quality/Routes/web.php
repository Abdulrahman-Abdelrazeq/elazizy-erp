<?php

use Illuminate\Support\Facades\Route;
use Modules\Quality\Http\Controllers\QualityAnalyticsController;
use Modules\Quality\Http\Controllers\QualityCapaController;
use Modules\Quality\Http\Controllers\QualityInspectionController;
use Modules\Quality\Http\Controllers\QualityNcrController;
use Modules\Quality\Http\Controllers\QualityTemplateController;

// 1. Inspection Templates & Checklists
Route::resource('templates', QualityTemplateController::class);
Route::post('templates/{template}/items', [QualityTemplateController::class, 'storeItem'])->name('templates.items.store');
Route::delete('templates/items/{item}', [QualityTemplateController::class, 'destroyItem'])->name('templates.items.destroy');

// 2. Quality Inspections & Tests
Route::resource('inspections', QualityInspectionController::class);
Route::get('inspections/{inspection}/print', [QualityInspectionController::class, 'printCertificate'])->name('inspections.print');
Route::post('inspections/{inspection}/evaluate', [QualityInspectionController::class, 'evaluate'])->name('inspections.evaluate');

// 3. Non-Conformance Reports (NCR)
Route::resource('ncrs', QualityNcrController::class);
Route::post('ncrs/{ncr}/disposition', [QualityNcrController::class, 'updateDisposition'])->name('ncrs.disposition');
Route::post('ncrs/{ncr}/close', [QualityNcrController::class, 'close'])->name('ncrs.close');

// 4. Corrective & Preventive Actions (CAPA)
Route::resource('capas', QualityCapaController::class);
Route::post('capas/{capa}/verify', [QualityCapaController::class, 'verifyEffectiveness'])->name('capas.verify');

// 5. Quality Analytics & COPQ Dashboard
Route::get('analytics', [QualityAnalyticsController::class, 'index'])->name('analytics.index');
