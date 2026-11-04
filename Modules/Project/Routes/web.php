<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Http\Controllers\ProjectAnalyticsController;
use Modules\Project\Http\Controllers\ProjectBoqController;
use Modules\Project\Http\Controllers\ProjectChangeOrderController;
use Modules\Project\Http\Controllers\ProjectController;
use Modules\Project\Http\Controllers\ProjectExtractController;
use Modules\Project\Http\Controllers\ProjectMilestoneController;

// 1. Projects Registry & Management
Route::resource('projects', ProjectController::class);

// 2. Project Milestones & Phases
Route::get('projects/{project}/milestones', [ProjectMilestoneController::class, 'index'])->name('milestones.index');
Route::post('projects/{project}/milestones', [ProjectMilestoneController::class, 'store'])->name('milestones.store');
Route::put('milestones/{milestone}', [ProjectMilestoneController::class, 'update'])->name('milestones.update');
Route::delete('milestones/{milestone}', [ProjectMilestoneController::class, 'destroy'])->name('milestones.destroy');

// 3. Bill of Quantities (BOQ)
Route::get('projects/{project}/boq', [ProjectBoqController::class, 'index'])->name('boq.index');
Route::post('projects/{project}/boq', [ProjectBoqController::class, 'store'])->name('boq.store');
Route::put('boq/{boqItem}', [ProjectBoqController::class, 'update'])->name('boq.update');
Route::delete('boq/{boqItem}', [ProjectBoqController::class, 'destroy'])->name('boq.destroy');

// 4. Change Orders
Route::get('projects/{project}/change-orders', [ProjectChangeOrderController::class, 'index'])->name('change-orders.index');
Route::post('projects/{project}/change-orders', [ProjectChangeOrderController::class, 'store'])->name('change-orders.store');
Route::post('change-orders/{changeOrder}/approve', [ProjectChangeOrderController::class, 'approve'])->name('change-orders.approve');
Route::post('change-orders/{changeOrder}/reject', [ProjectChangeOrderController::class, 'reject'])->name('change-orders.reject');

// 5. Progressive Billing & Contractor Extracts
Route::get('extracts', [ProjectExtractController::class, 'index'])->name('extracts.index');
Route::get('projects/{project}/extracts', [ProjectExtractController::class, 'projectExtracts'])->name('extracts.project');
Route::get('projects/{project}/extracts/create', [ProjectExtractController::class, 'create'])->name('extracts.create');
Route::post('projects/{project}/extracts', [ProjectExtractController::class, 'store'])->name('extracts.store');
Route::get('extracts/{extract}', [ProjectExtractController::class, 'show'])->name('extracts.show');
Route::get('extracts/{extract}/print', [ProjectExtractController::class, 'print'])->name('extracts.print');
Route::post('extracts/{extract}/approve', [ProjectExtractController::class, 'approve'])->name('extracts.approve');
Route::post('extracts/{extract}/record-payment', [ProjectExtractController::class, 'recordPayment'])->name('extracts.record-payment');

// 6. Analytics & Financial Performance
Route::get('analytics', [ProjectAnalyticsController::class, 'index'])->name('analytics.index');
