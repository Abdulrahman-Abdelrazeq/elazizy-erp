<?php

namespace Modules\FinancialReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FinancialReports\Services\FinancialCalculationService;

class SalesProfitabilityController extends Controller
{
    protected FinancialCalculationService $finService;

    public function __construct(FinancialCalculationService $finService)
    {
        $this->finService = $finService;
    }

    /**
     * Commercial Sales & Product Profitability Analysis (تحليل المبيعات والأرباح التجارية)
     */
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $branchId = $request->input('branch_id');

        $data = $this->finService->getSalesProfitability($fromDate, $toDate, $branchId);

        return view('reports::sales-profitability.index', compact('data', 'fromDate', 'toDate'));
    }
}
