<?php

namespace Modules\FinancialReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FinancialReports\Services\FinancialCalculationService;

class FinancialRatiosController extends Controller
{
    protected FinancialCalculationService $finService;

    public function __construct(FinancialCalculationService $finService)
    {
        $this->finService = $finService;
    }

    /**
     * Financial Ratios & Analytical Indicators Dashboard (النسب والمؤشرات المالية)
     */
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $branchId = $request->input('branch_id');

        $ratios = $this->finService->getFinancialRatios($fromDate, $toDate, $branchId);

        return view('reports::ratios.index', compact('ratios', 'fromDate', 'toDate'));
    }
}
