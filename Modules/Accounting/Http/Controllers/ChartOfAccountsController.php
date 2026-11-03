<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\Account;

class ChartOfAccountsController extends Controller
{
    /**
     * Chart of Accounts Tree & Table View
     */
    public function index(Request $request)
    {
        // Root accounts with nested children for the tree view
        $rootAccounts = Account::whereNull('parent_id')
            ->with(['allChildren' => fn($q) => $q->orderBy('code')])
            ->orderBy('code')
            ->get();

        // Flat query for searching or table view
        $query = Account::with('parent')->orderBy('code');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('name_en', 'like', "%{$s}%");
            });
        }

        $accounts = $query->paginate(20)->withQueryString();

        $stats = [
            'total'       => Account::count(),
            'assets'      => Account::where('type', 'asset')->count(),
            'liabilities' => Account::where('type', 'liability')->count(),
            'equity'      => Account::where('type', 'equity')->count(),
            'revenues'    => Account::where('type', 'revenue')->count(),
            'expenses'    => Account::where('type', 'expense')->count(),
        ];

        $parents = Account::parents()->active()->orderBy('code')->get();

        return view('accounting::accounts.index', compact('rootAccounts', 'accounts', 'stats', 'parents'));
    }

    /**
     * Store new Account in Chart of Accounts
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id'       => 'nullable|exists:chart_of_accounts,id',
            'code'            => 'required|string|max:30',
            'name'            => 'required|string|max:200',
            'name_en'         => 'nullable|string|max:200',
            'type'            => 'nullable|in:asset,liability,equity,revenue,expense',
            'nature'          => 'nullable|in:debit,credit',
            'is_parent'       => 'nullable|boolean',
            'opening_balance' => 'nullable|numeric',
            'notes'           => 'nullable|string',
        ]);

        $parent = null;
        if (!empty($validated['parent_id'])) {
            $parent = Account::find($validated['parent_id']);
        }

        $type = $validated['type'] ?? ($parent ? $parent->type : 'asset');
        $nature = $validated['nature'] ?? ($parent ? $parent->nature : ($type === 'asset' || $type === 'expense' ? 'debit' : 'credit'));
        $level = $parent ? ($parent->level + 1) : 1;
        $opening = (float) ($validated['opening_balance'] ?? 0);

        $account = Account::create([
            'parent_id'       => $parent ? $parent->id : null,
            'code'            => $validated['code'],
            'name'            => $validated['name'],
            'name_en'         => $validated['name_en'] ?? null,
            'type'            => $type,
            'nature'          => $nature,
            'level'           => $level,
            'is_parent'       => $request->boolean('is_parent'),
            'opening_balance' => $opening,
            'current_balance' => $opening,
            'is_active'       => true,
            'notes'           => $validated['notes'] ?? null,
        ]);

        if ($parent && !$parent->is_parent) {
            $parent->update(['is_parent' => true]);
        }

        return back()->with('success', 'تمت إضافة الحساب المالي (' . $account->name . ') بنجاح!');
    }

    /**
     * Update Account
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:200',
            'name_en'   => 'nullable|string|max:200',
            'is_active' => 'nullable|boolean',
            'notes'     => 'nullable|string',
        ]);

        $account->update([
            'name'      => $validated['name'],
            'name_en'   => $validated['name_en'] ?? null,
            'is_active' => $request->boolean('is_active', true),
            'notes'     => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم تحديث بيانات الحساب بنجاح!');
    }

    /**
     * Delete Account (only if no children and no journal entries)
     */
    public function destroy(Account $account)
    {
        if ($account->children()->exists()) {
            return back()->with('error', 'لا يمكن حذف الحساب لاحتوائه على حسابات فرعية متفرعة منه!');
        }

        if ($account->journalLines()->exists()) {
            return back()->with('error', 'لا يمكن حذف الحساب لوجود قيود يومية مسجلة عليه في دفتر اليومية!');
        }

        $parent = $account->parent;
        $account->delete();

        if ($parent && !$parent->children()->exists()) {
            $parent->update(['is_parent' => false]);
        }

        return back()->with('success', 'تم حذف الحساب بنجاح.');
    }

    /**
     * Helper endpoint to suggest next account code under parent
     */
    public function nextCode(Request $request)
    {
        $parentId = $request->input('parent_id');
        if (!$parentId) {
            return response()->json(['code' => '']);
        }

        $parent = Account::find($parentId);
        if (!$parent) {
            return response()->json(['code' => '']);
        }

        $lastChild = Account::where('parent_id', $parent->id)->latest('id')->first();
        if ($lastChild) {
            $next = ((int) $lastChild->code) + 1;
            $code = (string) $next;
        } else {
            $code = $parent->code . '01';
        }

        return response()->json([
            'code'   => $code,
            'type'   => $parent->type,
            'nature' => $parent->nature,
        ]);
    }
}
