<?php

namespace Modules\FinancialReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FinancialReports\Services\FinancialCalculationService;

class TaxReportController extends Controller
{
    protected FinancialCalculationService $finService;

    public function __construct(FinancialCalculationService $finService)
    {
        $this->finService = $finService;
    }

    /**
     * VAT Declaration & Tax Report View (تقرير ضريبة القيمة المضافة والإقرار الضريبي)
     */
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $branchId = $request->input('branch_id');

        $data = $this->finService->getTaxReport($fromDate, $toDate, $branchId);

        return view('reports::tax.index', compact('data', 'fromDate', 'toDate'));
    }

    /**
     * Printable VAT Declaration Form
     */
    public function print(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $branchId = $request->input('branch_id');

        $data = $this->finService->getTaxReport($fromDate, $toDate, $branchId);

        return view('reports::tax.print', compact('data', 'fromDate', 'toDate'));
    }
}

