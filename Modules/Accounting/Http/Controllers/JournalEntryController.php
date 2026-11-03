<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\CostCenter;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\JournalEntryLine;

class JournalEntryController extends Controller
{
    /**
     * Journal Entries Log Listing
     */
    public function index(Request $request)
    {
        $query = JournalEntry::with(['creator', 'lines.account'])->latest('date')->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('entry_number', 'like', "%{$s}%")
                  ->orWhere('reference', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $entries = $query->paginate(15)->withQueryString();

        $stats = [
            'total_entries' => JournalEntry::count(),
            'total_debit'   => (float) JournalEntry::where('status', 'posted')->sum('total_debit'),
            'total_credit'  => (float) JournalEntry::where('status', 'posted')->sum('total_credit'),
            'posted_count'  => JournalEntry::where('status', 'posted')->count(),
            'draft_count'   => JournalEntry::where('status', 'draft')->count(),
        ];

        return view('accounting::journal-entries.index', compact('entries', 'stats'));
    }

    /**
     * Show Create Journal Entry Form
     */
    public function create()
    {
        $leafAccounts = Account::leafs()->active()->orderBy('code')->get();
        $costCenters = CostCenter::active()->orderBy('code')->get();
        $entryNumber = JournalEntry::generateNumber();

        return view('accounting::journal-entries.create', compact('leafAccounts', 'costCenters', 'entryNumber'));
    }

    /**
     * Store Journal Entry & Validate Double-Entry Balance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'                  => 'required|date',
            'reference'             => 'nullable|string|max:100',
            'description'           => 'required|string',
            'status'                => 'required|in:posted,draft',
            'lines'                 => 'required|array|min:2',
            'lines.*.account_id'    => 'required|exists:chart_of_accounts,id',
            'lines.*.cost_center_id'=> 'nullable|exists:cost_centers,id',
            'lines.*.debit'         => 'required|numeric|min:0',
            'lines.*.credit'        => 'required|numeric|min:0',
            'lines.*.description'   => 'nullable|string',
        ]);

        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($validated['lines'] as $line) {
            $d = (float) $line['debit'];
            $c = (float) $line['credit'];
            $totalDebit += $d;
            $totalCredit += $c;
        }

        // Double-entry validation: Total Debit MUST equal Total Credit
        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withInput()->with('error', 'القيد غير متوازن محاسبياً! إجمالي المدين (' . formatMoney($totalDebit) . ') لا يساوي إجمالي الدائن (' . formatMoney($totalCredit) . '). الفارق: ' . formatMoney(abs($totalDebit - $totalCredit)));
        }

        if ($totalDebit <= 0) {
            return back()->withInput()->with('error', 'لا يمكن تسجيل قيد بقيمة صفرية!');
        }

        DB::beginTransaction();
        try {
            $entryNumber = JournalEntry::generateNumber();
            $isPosted = $validated['status'] === 'posted';

            $entry = JournalEntry::create([
                'branch_id'    => activeBranch() ? activeBranch()->id : null,
                'entry_number' => $entryNumber,
                'date'         => $validated['date'],
                'reference'    => $validated['reference'] ?? null,
                'description'  => $validated['description'],
                'total_debit'  => $totalDebit,
                'total_credit' => $totalCredit,
                'status'       => $validated['status'],
                'source_type'  => 'manual',
                'created_by'   => Auth::id(),
                'posted_by'    => $isPosted ? Auth::id() : null,
                'posted_at'    => $isPosted ? now() : null,
            ]);

            $touchedAccountIds = [];

            foreach ($validated['lines'] as $l) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id'       => $l['account_id'],
                    'cost_center_id'   => $l['cost_center_id'] ?? null,
                    'debit'            => (float) $l['debit'],
                    'credit'           => (float) $l['credit'],
                    'description'      => $l['description'] ?? $validated['description'],
                ]);

                $touchedAccountIds[] = $l['account_id'];
            }

            // If posted, recalculate balances for affected accounts
            if ($isPosted) {
                foreach (array_unique($touchedAccountIds) as $accId) {
                    $acc = Account::find($accId);
                    if ($acc) {
                        $acc->recalculateBalance();
                    }
                }
            }

            DB::commit();

            return redirect()->route('accounting.journal-entries.show', $entry->id)
                ->with('success', 'تم حفظ واعتِماد القيد اليومي المزدوج بنجاح! رقم القيد: ' . $entryNumber);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ القيد: ' . $e->getMessage());
        }
    }

    /**
     * Show Journal Entry Details
     */
    public function show(JournalEntry $entry)
    {
        $entry->load(['lines.account', 'lines.costCenter', 'creator', 'poster', 'branch']);
        return view('accounting::journal-entries.show', compact('entry'));
    }

    /**
     * Printable Journal Entry Voucher
     */
    public function print(JournalEntry $entry)
    {
        $entry->load(['lines.account', 'lines.costCenter', 'creator', 'poster']);
        return view('accounting::journal-entries.print', compact('entry'));
    }

    /**
     * Delete / Reverse Journal Entry
     */
    public function destroy(JournalEntry $entry)
    {
        DB::beginTransaction();
        try {
            $accountIds = $entry->lines()->pluck('account_id')->toArray();
            $entry->delete();

            // Recalculate balances
            foreach (array_unique($accountIds) as $accId) {
                $acc = Account::find($accId);
                if ($acc) {
                    $acc->recalculateBalance();
                }
            }

            DB::commit();
            return redirect()->route('accounting.journal-entries.index')->with('success', 'تم حذف القيد اليومي وإعادة تحديث أرصدة الحسابات بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'فشل حذف القيد: ' . $e->getMessage());
        }
    }
}
