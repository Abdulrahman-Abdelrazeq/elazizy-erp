<?php

namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Treasury\Entities\Treasury;
use Modules\Treasury\Entities\TreasuryTransaction;

class TreasuryController extends Controller
{
    /**
     * Treasuries & Bank Accounts Listing
     */
    public function index(Request $request)
    {
        $query = Treasury::with(['branch', 'creator'])->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('account_number', 'like', "%{$s}%")
                  ->orWhere('bank_name', 'like', "%{$s}%");
            });
        }

        $treasuries = $query->paginate(15)->withQueryString();

        $stats = [
            'total_balance' => (float) Treasury::where('is_active', true)->sum('current_balance'),
            'cash_balance'  => (float) Treasury::where('is_active', true)->where('type', 'cash')->sum('current_balance'),
            'bank_balance'  => (float) Treasury::where('is_active', true)->where('type', 'bank')->sum('current_balance'),
            'wallet_balance'=> (float) Treasury::where('is_active', true)->where('type', 'wallet')->sum('current_balance'),
            'total_count'   => Treasury::count(),
        ];

        return view('treasury::treasuries.index', compact('treasuries', 'stats'));
    }

    /**
     * Store a new Treasury or Bank Account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:150',
            'type'            => 'required|in:cash,bank,wallet',
            'account_number'  => 'nullable|string|max:100',
            'bank_name'       => 'nullable|string|max:150',
            'bank_branch'     => 'nullable|string|max:150',
            'iban'            => 'nullable|string|max:100',
            'swift'           => 'nullable|string|max:50',
            'opening_balance' => 'required|numeric|min:0',
            'currency'        => 'required|string|max:10',
            'is_default'      => 'nullable|boolean',
            'notes'           => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            if ($request->boolean('is_default')) {
                Treasury::where('is_default', true)->update(['is_default' => false]);
            }

            $opening = (float) $validated['opening_balance'];

            $treasury = Treasury::create([
                'branch_id'       => activeBranch() ? activeBranch()->id : null,
                'name'            => $validated['name'],
                'type'            => $validated['type'],
                'account_number'  => $validated['account_number'] ?? null,
                'bank_name'       => $validated['bank_name'] ?? null,
                'bank_branch'     => $validated['bank_branch'] ?? null,
                'iban'            => $validated['iban'] ?? null,
                'swift'           => $validated['swift'] ?? null,
                'opening_balance' => $opening,
                'current_balance' => $opening,
                'currency'        => $validated['currency'] ?? 'EGP',
                'is_active'       => true,
                'is_default'      => $request->boolean('is_default'),
                'notes'           => $validated['notes'] ?? null,
                'created_by'      => Auth::id(),
            ]);

            // If initial opening balance > 0, register opening transaction
            if ($opening > 0) {
                TreasuryTransaction::create([
                    'tenant_id'        => $treasury->tenant_id,
                    'treasury_id'      => $treasury->id,
                    'type'             => 'in',
                    'amount'           => $opening,
                    'balance_before'   => 0.00,
                    'balance_after'    => $opening,
                    'source_type'      => 'opening',
                    'source_id'        => $treasury->id,
                    'transaction_date' => now(),
                    'description'      => 'رصيد افتتاحي عند التأسيس',
                    'created_by'       => Auth::id(),
                ]);
            }

            DB::commit();
            return back()->with('success', 'تم تعريف الخزينة / الحساب البنكي بنجاح!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الحفظ: ' . $e->getMessage());
        }
    }

    /**
     * Update Treasury / Bank Account
     */
    public function update(Request $request, Treasury $treasury)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'type'           => 'required|in:cash,bank,wallet',
            'account_number' => 'nullable|string|max:100',
            'bank_name'      => 'nullable|string|max:150',
            'bank_branch'    => 'nullable|string|max:150',
            'iban'           => 'nullable|string|max:100',
            'swift'          => 'nullable|string|max:50',
            'currency'       => 'required|string|max:10',
            'is_active'      => 'nullable|boolean',
            'is_default'     => 'nullable|boolean',
            'notes'          => 'nullable|string',
        ]);

        if ($request->boolean('is_default')) {
            Treasury::where('id', '!=', $treasury->id)->where('is_default', true)->update(['is_default' => false]);
        }

        $treasury->update([
            'name'           => $validated['name'],
            'type'           => $validated['type'],
            'account_number' => $validated['account_number'] ?? null,
            'bank_name'      => $validated['bank_name'] ?? null,
            'bank_branch'    => $validated['bank_branch'] ?? null,
            'iban'           => $validated['iban'] ?? null,
            'swift'          => $validated['swift'] ?? null,
            'currency'       => $validated['currency'] ?? 'EGP',
            'is_active'      => $request->boolean('is_active', true),
            'is_default'     => $request->boolean('is_default'),
            'notes'          => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم تحديث بيانات الخزينة بنجاح!');
    }

    /**
     * Delete Treasury
     */
    public function destroy(Treasury $treasury)
    {
        if ($treasury->transactions()->exists()) {
            return back()->with('error', 'لا يمكن حذف الخزينة لوجود حركات مالية مسجلة عليها!');
        }

        $treasury->delete();
        return back()->with('success', 'تم حذف الخزينة بنجاح.');
    }

    /**
     * Statement of Account (كشف حساب حركة الخزينة / البنك)
     */
    public function statement(Request $request, Treasury $treasury)
    {
        $fromDate = $request->input('from_date', now()->startOfMonth()->toDateString());
        $toDate = $request->input('to_date', now()->endOfMonth()->toDateString());

        // Opening balance before fromDate
        $priorIn = (float) $treasury->transactions()
            ->whereDate('transaction_date', '<', $fromDate)
            ->where('type', 'in')
            ->sum('amount');

        $priorOut = (float) $treasury->transactions()
            ->whereDate('transaction_date', '<', $fromDate)
            ->where('type', 'out')
            ->sum('amount');

        $periodOpeningBalance = $treasury->opening_balance + $priorIn - $priorOut;

        // Current period transactions
        $transactions = $treasury->transactions()
            ->whereDate('transaction_date', '>=', $fromDate)
            ->whereDate('transaction_date', '<=', $toDate)
            ->oldest('transaction_date')
            ->get();

        $totalIn = $transactions->where('type', 'in')->sum('amount');
        $totalOut = $transactions->where('type', 'out')->sum('amount');
        $periodClosingBalance = $periodOpeningBalance + $totalIn - $totalOut;

        return view('treasury::treasuries.statement', compact(
            'treasury',
            'transactions',
            'fromDate',
            'toDate',
            'periodOpeningBalance',
            'totalIn',
            'totalOut',
            'periodClosingBalance'
        ));
    }
}
