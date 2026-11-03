<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\CostCenter;
use Modules\Accounting\Entities\JournalEntryLine;

class GeneralLedgerController extends Controller
{
    /**
     * General Ledger / Account Statement
     */
    public function index(Request $request)
    {
        $accounts = Account::leafs()->active()->orderBy('code')->get();
        $costCenters = CostCenter::active()->orderBy('code')->get();

        $selectedAccountId = $request->input('account_id', $accounts->first()?->id);
        $selectedAccount = $selectedAccountId ? Account::find($selectedAccountId) : null;

        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate = $request->input('to_date', now()->toDateString());
        $costCenterId = $request->input('cost_center_id');

        $lines = collect();
        $openingBalance = 0.00;
        $totalDebit = 0.00;
        $totalCredit = 0.00;
        $closingBalance = 0.00;

        if ($selectedAccount) {
            // 1. Calculate opening balance before from_date
            $priorDebit = (float) JournalEntryLine::where('account_id', $selectedAccount->id)
                ->whereHas('journalEntry', function ($q) use ($fromDate) {
                    $q->where('status', 'posted')->whereDate('date', '<', $fromDate);
                })
                ->when($costCenterId, fn($q) => $q->where('cost_center_id', $costCenterId))
                ->sum('debit');

            $priorCredit = (float) JournalEntryLine::where('account_id', $selectedAccount->id)
                ->whereHas('journalEntry', function ($q) use ($fromDate) {
                    $q->where('status', 'posted')->whereDate('date', '<', $fromDate);
                })
                ->when($costCenterId, fn($q) => $q->where('cost_center_id', $costCenterId))
                ->sum('credit');

            if ($selectedAccount->nature === 'debit') {
                $openingBalance = $selectedAccount->opening_balance + $priorDebit - $priorCredit;
            } else {
                $openingBalance = $selectedAccount->opening_balance + $priorCredit - $priorDebit;
            }

            // 2. Journal lines in period
            $lines = JournalEntryLine::with(['journalEntry', 'costCenter'])
                ->where('account_id', $selectedAccount->id)
                ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate) {
                    $q->where('status', 'posted')
                      ->whereDate('date', '>=', $fromDate)
                      ->whereDate('date', '<=', $toDate);
                })
                ->when($costCenterId, fn($q) => $q->where('cost_center_id', $costCenterId))
                ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
                ->orderBy('journal_entries.date')
                ->orderBy('journal_entries.id')
                ->select('journal_entry_lines.*')
                ->get();

            $totalDebit = (float) $lines->sum('debit');
            $totalCredit = (float) $lines->sum('credit');

            if ($selectedAccount->nature === 'debit') {
                $closingBalance = $openingBalance + $totalDebit - $totalCredit;
            } else {
                $closingBalance = $openingBalance + $totalCredit - $totalDebit;
            }
        }

        return view('accounting::ledger.index', compact(
            'accounts',
            'costCenters',
            'selectedAccount',
            'fromDate',
            'toDate',
            'costCenterId',
            'openingBalance',
            'lines',
            'totalDebit',
            'totalCredit',
            'closingBalance'
        ));
    }
}

