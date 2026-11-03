<?php

namespace Modules\FinancialReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FinancialReports\Services\FinancialCalculationService;

class FinancialReportsController extends Controller
{
    protected FinancialCalculationService $finService;

    public function __construct(FinancialCalculationService $finService)
    {
        $this->finService = $finService;
    }

    /**
     * Financial Reports Hub / Dashboard Overview
     */
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $branchId = $request->input('branch_id');

        $income = $this->finService->getIncomeStatement($fromDate, $toDate, null, $branchId);
        $balance = $this->finService->getBalanceSheet($toDate, $branchId);
        $cashFlow = $this->finService->getCashFlowStatement($fromDate, $toDate, $branchId);
        $ratios = $this->finService->getFinancialRatios($fromDate, $toDate, $branchId);

        return view('reports::index', compact('income', 'balance', 'cashFlow', 'ratios', 'fromDate', 'toDate'));
    }
}

