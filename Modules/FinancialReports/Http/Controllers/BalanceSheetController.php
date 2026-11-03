<?php

namespace Modules\FinancialReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\FinancialReports\Services\FinancialCalculationService;

class BalanceSheetController extends Controller
{
    protected FinancialCalculationService $finService;

    public function __construct(FinancialCalculationService $finService)
    {
        $this->finService = $finService;
    }

    /**
     * Balance Sheet View (قائمة المركز المالي والميزانية العمومية)
     */
    public function index(Request $request)
    {
        $asOfDate = $request->input('as_of_date', now()->toDateString());
        $branchId = $request->input('branch_id');

        $data = $this->finService->getBalanceSheet($asOfDate, $branchId);

        return view('reports::balance-sheet.index', compact('data', 'asOfDate'));
    }

    /**
     * Printable Balance Sheet
     */
    public function print(Request $request)
    {
        $asOfDate = $request->input('as_of_date', now()->toDateString());
        $branchId = $request->input('branch_id');

        $data = $this->finService->getBalanceSheet($asOfDate, $branchId);

        return view('reports::balance-sheet.print', compact('data', 'asOfDate'));
    }
}

