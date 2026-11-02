<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoice\Http\Controllers\PurchaseController;
use Modules\Invoice\Http\Controllers\PurchaseOrderController;
use Modules\Invoice\Http\Controllers\QuotationController;
use Modules\Invoice\Http\Controllers\ReturnController;
use Modules\Invoice\Http\Controllers\SaleController;

/*
|--------------------------------------------------------------------------
| Invoice Module Web Routes - نظام العزيزي ERP
|--------------------------------------------------------------------------
*/

// Sales Invoices (فواتير المبيعات)
Route::prefix('sales')->name('sales.')->group(function () {
    Route::get('/', [SaleController::class, 'index'])->name('index');
    Route::get('/create', [SaleController::class, 'create'])->name('create');
    Route::post('/', [SaleController::class, 'store'])->name('store');
    Route::get('/{sale}', [SaleController::class, 'show'])->name('show');
    Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
    Route::delete('/{sale}', [SaleController::class, 'destroy'])->name('destroy');
});

// Purchase Invoices (فواتير المشتريات)
Route::prefix('purchases')->name('purchases.')->group(function () {
    Route::get('/', [PurchaseController::class, 'index'])->name('index');
    Route::get('/create', [PurchaseController::class, 'create'])->name('create');
    Route::post('/', [PurchaseController::class, 'store'])->name('store');
    Route::get('/{purchase}', [PurchaseController::class, 'show'])->name('show');
});

// Quotations / Price Offers (عروض الأسعار)
Route::prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [QuotationController::class, 'index'])->name('index');
    Route::get('/create', [QuotationController::class, 'create'])->name('create');
    Route::post('/', [QuotationController::class, 'store'])->name('store');
    Route::post('/{quotation}/convert', [QuotationController::class, 'convertToSale'])->name('convert');
});

// Return Invoices (المرتجعات - مبيعات ومشتريات)
Route::prefix('returns')->name('returns.')->group(function () {
    Route::get('/', [ReturnController::class, 'index'])->name('index');
    Route::get('/create', [ReturnController::class, 'create'])->name('create');
    Route::post('/', [ReturnController::class, 'store'])->name('store');
});

// Purchase Orders (أوامر الشراء)
Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
    Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
    Route::post('/', [PurchaseOrderController::class, 'store'])->name('store');
    Route::post('/{order}/convert', [PurchaseOrderController::class, 'convertToPurchase'])->name('convert');
});
