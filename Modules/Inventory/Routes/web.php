<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\CategoryController;
use Modules\Inventory\Http\Controllers\ItemController;
use Modules\Inventory\Http\Controllers\StockAdjustmentController;
use Modules\Inventory\Http\Controllers\StockTransferController;
use Modules\Inventory\Http\Controllers\StoreController;
use Modules\Inventory\Http\Controllers\UnitController;

/*
|--------------------------------------------------------------------------
| Inventory Module Web Routes - نظام العزيزي ERP
|--------------------------------------------------------------------------
*/

// Items Catalog
Route::prefix('items')->name('items.')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('index');
    Route::get('/create', [ItemController::class, 'create'])->name('create');
    Route::post('/', [ItemController::class, 'store'])->name('store');
    Route::get('/{item}/edit', [ItemController::class, 'edit'])->name('edit');
    Route::put('/{item}', [ItemController::class, 'update'])->name('update');
    Route::delete('/{item}', [ItemController::class, 'destroy'])->name('destroy');
    Route::get('/{item}/barcode', [ItemController::class, 'barcode'])->name('barcode');
});

// Units
Route::prefix('units')->name('units.')->group(function () {
    Route::get('/', [UnitController::class, 'index'])->name('index');
    Route::post('/', [UnitController::class, 'store'])->name('store');
    Route::delete('/{unit}', [UnitController::class, 'destroy'])->name('destroy');
});

// Categories & Brands
Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('index');
    Route::post('/', [CategoryController::class, 'store'])->name('store');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    Route::post('/brands', [CategoryController::class, 'storeBrand'])->name('brands.store');
    Route::delete('/brands/{brand}', [CategoryController::class, 'destroyBrand'])->name('brands.destroy');
});

// Warehouses / Stores
Route::prefix('stores')->name('stores.')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('index');
    Route::post('/', [StoreController::class, 'store'])->name('store');
    Route::get('/{store}', [StoreController::class, 'show'])->name('show');
});

// Stock Transfers
Route::prefix('transfers')->name('transfers.')->group(function () {
    Route::get('/', [StockTransferController::class, 'index'])->name('index');
    Route::get('/create', [StockTransferController::class, 'create'])->name('create');
    Route::post('/', [StockTransferController::class, 'store'])->name('store');
});

// Stock Adjustments
Route::prefix('adjustments')->name('adjustments.')->group(function () {
    Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
    Route::get('/create', [StockAdjustmentController::class, 'create'])->name('create');
    Route::post('/', [StockAdjustmentController::class, 'store'])->name('store');
});

