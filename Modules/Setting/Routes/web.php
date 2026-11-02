<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\BarcodeSettingController;
use Modules\Setting\Http\Controllers\CurrencyController;
use Modules\Setting\Http\Controllers\LocationController;
use Modules\Setting\Http\Controllers\PrintTemplateController;
use Modules\Setting\Http\Controllers\SettingController;
use Modules\Setting\Http\Controllers\TaxController;

/*
|--------------------------------------------------------------------------
| Setting Module Web Routes - نظام العزيزي ERP
|--------------------------------------------------------------------------
*/

// Main Settings Hub
Route::get('/', [SettingController::class, 'index'])->name('index');
Route::post('/update', [SettingController::class, 'update'])->name('update');

// Taxes Management
Route::prefix('taxes')->name('taxes.')->group(function () {
    Route::get('/', [TaxController::class, 'index'])->name('index');
    Route::post('/', [TaxController::class, 'store'])->name('store');
    Route::put('/{tax}', [TaxController::class, 'update'])->name('update');
    Route::delete('/{tax}', [TaxController::class, 'destroy'])->name('destroy');
});

// Currencies Management
Route::prefix('currencies')->name('currencies.')->group(function () {
    Route::get('/', [CurrencyController::class, 'index'])->name('index');
    Route::post('/', [CurrencyController::class, 'store'])->name('store');
    Route::put('/{currency}', [CurrencyController::class, 'update'])->name('update');
    Route::delete('/{currency}', [CurrencyController::class, 'destroy'])->name('destroy');
});

// Geographical Locations (Countries, Cities, Delivery Zones)
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/', [LocationController::class, 'index'])->name('index');
    Route::post('/', [LocationController::class, 'store'])->name('store');
    Route::delete('/{location}', [LocationController::class, 'destroy'])->name('destroy');
});

// Invoice Print Templates
Route::prefix('prints')->name('prints.')->group(function () {
    Route::get('/', [PrintTemplateController::class, 'index'])->name('index');
    Route::put('/{template}', [PrintTemplateController::class, 'update'])->name('update');
});

// Barcode Settings
Route::prefix('barcode')->name('barcode.')->group(function () {
    Route::get('/', [BarcodeSettingController::class, 'index'])->name('index');
    Route::post('/', [BarcodeSettingController::class, 'update'])->name('update');
});

