<?php

use Illuminate\Support\Facades\Route;
use Modules\FinancialReports\Http\Controllers\BalanceSheetController;
use Modules\FinancialReports\Http\Controllers\BudgetController;
use Modules\FinancialReports\Http\Controllers\CashFlowStatementController;
use Modules\FinancialReports\Http\Controllers\FinancialRatiosController;
use Modules\FinancialReports\Http\Controllers\FinancialReportsController;
use Modules\FinancialReports\Http\Controllers\IncomeStatementController;
use Modules\FinancialReports\Http\Controllers\SalesProfitabilityController;
use Modules\FinancialReports\Http\Controllers\TaxReportController;

Route::get('/', [FinancialReportsController::class, 'index'])->name('index');

// 1. Income Statement (قائمة الدخل / الأرباح والخسائر)
Route::get('/income-statement', [IncomeStatementController::class, 'index'])->name('income_statement');
Route::get('/income-statement/print', [IncomeStatementController::class, 'print'])->name('income_statement.print');

// 2. Balance Sheet / Financial Position (قائمة المركز المالي والميزانية العمومية)
Route::get('/balance-sheet', [BalanceSheetController::class, 'index'])->name('balance_sheet');
Route::get('/balance-sheet/print', [BalanceSheetController::class, 'print'])->name('balance_sheet.print');

// 3. Cash Flow Statement (قائمة التدفقات النقدية)
Route::get('/cash-flow', [CashFlowStatementController::class, 'index'])->name('cash_flow');
Route::get('/cash-flow/print', [CashFlowStatementController::class, 'print'])->name('cash_flow.print');

// 4. Financial Ratios & Performance Indicators (النسب والمؤشرات المالية)
Route::get('/ratios', [FinancialRatiosController::class, 'index'])->name('ratios');

// 5. Tax & VAT Declaration (تقرير القيمة المضافة والإقرار الضريبي)
Route::get('/tax-vat', [TaxReportController::class, 'index'])->name('tax');
Route::get('/tax-vat/print', [TaxReportController::class, 'print'])->name('tax.print');

// 6. Sales & Profitability Analysis (تحليل المبيعات والأرباح التجارية)
Route::get('/sales-profitability', [SalesProfitabilityController::class, 'index'])->name('sales_profitability');

// 7. Financial Budgets & Variance Planning (الموازنات التقديرية والتخطيط المالي)
Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

