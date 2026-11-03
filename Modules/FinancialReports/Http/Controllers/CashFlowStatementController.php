<?php

namespace Modules\FinancialReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FinancialReports\Services\FinancialCalculationService;

class CashFlowStatementController extends Controller
{
    protected FinancialCalculationService $finService;

    public function __construct(FinancialCalculationService $finService)
    {
        $this->finService = $finService;
    }

    /**
     * Cash Flow Statement View (قائمة التدفقات النقدية)
     */
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $branchId = $request->input('branch_id');

        $data = $this->finService->getCashFlowStatement($fromDate, $toDate, $branchId);

        return view('reports::cash-flow.index', compact('data', 'fromDate', 'toDate'));
    }

    /**
     * Printable Cash Flow Statement
     */
    public function print(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $branchId = $request->input('branch_id');

        $data = $this->finService->getCashFlowStatement($fromDate, $toDate, $branchId);

        return view('reports::cash-flow.print', compact('data', 'fromDate', 'toDate'));
    }
}
