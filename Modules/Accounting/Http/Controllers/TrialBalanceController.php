<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\Account;

class TrialBalanceController extends Controller
{
    /**
     * Trial Balance (ميزان المراجعة بالمجاميع والأرصدة)
     */
    public function index(Request $request)
    {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $onlyLeafs = $request->boolean('only_leafs', false);

        $accountsQuery = Account::orderBy('code');
        if ($onlyLeafs) {
            $accountsQuery->leafs();
        }
        $accounts = $accountsQuery->get();

        $rows = [];
        $totalInitialDebit = 0;
        $totalInitialCredit = 0;
        $totalPeriodDebit = 0;
        $totalPeriodCredit = 0;
        $totalEndingDebit = 0;
        $totalEndingCredit = 0;

        foreach ($accounts as $acc) {
            // Initial Balance (prior to from_date)
            $priorDebit = (float) $acc->journalLines()
                ->whereHas('journalEntry', fn($q) => $q->where('status', 'posted')->whereDate('date', '<', $fromDate))
                ->sum('debit');

            $priorCredit = (float) $acc->journalLines()
                ->whereHas('journalEntry', fn($q) => $q->where('status', 'posted')->whereDate('date', '<', $fromDate))
                ->sum('credit');

            $initNet = ($acc->nature === 'debit')
                ? ($acc->opening_balance + $priorDebit - $priorCredit)
                : ($acc->opening_balance + $priorCredit - $priorDebit);

            $initDebit = ($acc->nature === 'debit' && $initNet > 0) ? $initNet : 0;
            $initCredit = ($acc->nature === 'credit' && $initNet > 0) ? $initNet : 0;

            // Period Activity (between from_date and to_date)
            $periodDebit = (float) $acc->journalLines()
                ->whereHas('journalEntry', fn($q) => $q->where('status', 'posted')->whereDate('date', '>=', $fromDate)->whereDate('date', '<=', $toDate))
                ->sum('debit');

            $periodCredit = (float) $acc->journalLines()
                ->whereHas('journalEntry', fn($q) => $q->where('status', 'posted')->whereDate('date', '>=', $fromDate)->whereDate('date', '<=', $toDate))
                ->sum('credit');

            // Ending Balance (after period)
            $endNet = ($acc->nature === 'debit')
                ? ($initNet + $periodDebit - $periodCredit)
                : ($initNet + $periodCredit - $periodDebit);

            $endDebit = ($acc->nature === 'debit' && $endNet > 0) ? $endNet : (($acc->nature === 'credit' && $endNet < 0) ? abs($endNet) : 0);
            $endCredit = ($acc->nature === 'credit' && $endNet > 0) ? $endNet : (($acc->nature === 'debit' && $endNet < 0) ? abs($endNet) : 0);

            // If account is leaf, add to grand totals
            if (!$acc->is_parent) {
                $totalInitialDebit += $initDebit;
                $totalInitialCredit += $initCredit;
                $totalPeriodDebit += $periodDebit;
                $totalPeriodCredit += $periodCredit;
                $totalEndingDebit += $endDebit;
                $totalEndingCredit += $endCredit;
            }

            $rows[] = [
                'account'       => $acc,
                'init_debit'    => $initDebit,
                'init_credit'   => $initCredit,
                'period_debit'  => $periodDebit,
                'period_credit' => $periodCredit,
                'end_debit'     => $endDebit,
                'end_credit'    => $endCredit,
            ];
        }

        $isBalanced = abs($totalEndingDebit - $totalEndingCredit) < 0.05 && abs($totalPeriodDebit - $totalPeriodCredit) < 0.05;

        return view('accounting::trial-balance.index', compact(
            'rows',
            'fromDate',
            'toDate',
            'onlyLeafs',
            'totalInitialDebit',
            'totalInitialCredit',
            'totalPeriodDebit',
            'totalPeriodCredit',
            'totalEndingDebit',
            'totalEndingCredit',
            'isBalanced'
        ));
    }
}
