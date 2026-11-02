<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - نظام العزيزي ERP
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Authenticated ERP Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-branch', [AuthController::class, 'switchBranch'])->name('switch.branch');
    Route::post('/theme/update', [DashboardController::class, 'updateTheme'])->name('theme.update');

    // SaaS Subscriptions & Renewals
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscriptions/renew', [SubscriptionController::class, 'renew'])->name('subscription.renew');
});
