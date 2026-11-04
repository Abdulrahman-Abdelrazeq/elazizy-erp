<?php

use Illuminate\Support\Facades\Route;
use Modules\Asset\Http\Controllers\AssetCategoryController;
use Modules\Asset\Http\Controllers\AssetController;
use Modules\Asset\Http\Controllers\AssetDepreciationController;
use Modules\Asset\Http\Controllers\AssetDisposalController;
use Modules\Asset\Http\Controllers\AssetReportController;

// 1. Asset Categories Management
Route::resource('categories', AssetCategoryController::class);

// 2. Fixed Assets Registry
Route::resource('assets', AssetController::class);
Route::get('assets/{asset}/barcode', [AssetController::class, 'printBarcode'])->name('assets.barcode');

// 3. Periodic Depreciation Engine
Route::get('depreciations', [AssetDepreciationController::class, 'index'])->name('depreciations.index');
Route::get('depreciations/create', [AssetDepreciationController::class, 'create'])->name('depreciations.create');
Route::post('depreciations', [AssetDepreciationController::class, 'store'])->name('depreciations.store');
Route::post('depreciations/run-batch', [AssetDepreciationController::class, 'runBatch'])->name('depreciations.run-batch');
Route::get('depreciations/{depreciation}', [AssetDepreciationController::class, 'show'])->name('depreciations.show');
Route::post('depreciations/{depreciation}/post-gl', [AssetDepreciationController::class, 'postToGl'])->name('depreciations.post-gl');

// 4. Asset Disposals & Sales
Route::get('disposals', [AssetDisposalController::class, 'index'])->name('disposals.index');
Route::get('disposals/create', [AssetDisposalController::class, 'create'])->name('disposals.create');
Route::post('disposals', [AssetDisposalController::class, 'store'])->name('disposals.store');

// 5. Fixed Assets & Depreciation Reports
Route::get('reports', [AssetReportController::class, 'index'])->name('reports.index');
Route::get('reports/print', [AssetReportController::class, 'print'])->name('reports.print');
