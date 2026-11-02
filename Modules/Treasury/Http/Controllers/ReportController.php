<?php

namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Treasury\Entities\ExpenseCategory;
use Modules\Treasury\Entities\Treasury;
use Modules\Treasury\Entities\TreasuryTransaction;
use Modules\Treasury\Entities\TreasuryVoucher;

class ReportController extends Controller
{
    /**
     * Cash Flow & Treasury Overview Report
     */
    public function cashFlow(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', now()->endOfMonth()->toDateString());

        // Transactions in period
        $transactions = TreasuryTransaction::with(['treasury', 'creator'])
            ->whereDate('transaction_date', '>=', $fromDate)
            ->whereDate('transaction_date', '<=', $toDate)
            ->latest('transaction_date')
            ->get();

        $totalIn = $transactions->where('type', 'in')->sum('amount');
        $totalOut = $transactions->where('type', 'out')->sum('amount');
        $netFlow = $totalIn - $totalOut;

        // Current Treasuries Status
        $treasuries = Treasury::where('is_active', true)->withCount('transactions')->get();
        $totalLiquidity = $treasuries->sum('current_balance');

        // Expenses by Category in period
        $categoryExpenses = ExpenseCategory::where('type', 'expense')
            ->withSum(['vouchers' => function ($q) use ($fromDate, $toDate) {
                $q->where('status', 'approved')
                  ->whereDate('date', '>=', $fromDate)
                  ->whereDate('date', '<=', $toDate);
            }], 'amount')
            ->get()
            ->filter(fn($c) => $c->vouchers_sum_amount > 0);

        return view('treasury::reports.cash_flow', compact(
            'fromDate',
            'toDate',
            'transactions',
            'totalIn',
            'totalOut',
            'netFlow',
            'treasuries',
            'totalLiquidity',
            'categoryExpenses'
        ));
    }
}

