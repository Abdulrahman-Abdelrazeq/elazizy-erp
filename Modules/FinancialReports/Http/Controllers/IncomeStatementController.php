<?php

namespace Modules\FinancialReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\CostCenter;
use Modules\FinancialReports\Services\FinancialCalculationService;

class IncomeStatementController extends Controller
{
    protected FinancialCalculationService $finService;

    public function __construct(FinancialCalculationService $finService)
    {
        $this->finService = $finService;
    }

    /**
     * Income Statement View (قائمة الدخل / الأرباح والخسائر)
     */
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $costCenterId = $request->input('cost_center_id');
        $branchId = $request->input('branch_id');

        $data = $this->finService->getIncomeStatement($fromDate, $toDate, $costCenterId, $branchId);
        $costCenters = CostCenter::active()->orderBy('code')->get();

        return view('reports::income-statement.index', compact('data', 'costCenters', 'fromDate', 'toDate', 'costCenterId'));
    }

    /**
     * Printable Income Statement
     */
    public function print(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $costCenterId = $request->input('cost_center_id');
        $branchId = $request->input('branch_id');

        $data = $this->finService->getIncomeStatement($fromDate, $toDate, $costCenterId, $branchId);

        return view('reports::income-statement.print', compact('data', 'fromDate', 'toDate'));
    }
}

