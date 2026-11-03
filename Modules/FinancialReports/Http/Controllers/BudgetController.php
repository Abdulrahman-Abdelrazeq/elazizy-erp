<?php

namespace Modules\FinancialReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Accounting\Entities\Account;
use Modules\FinancialReports\Entities\FinancialBudget;

class BudgetController extends Controller
{
    /**
     * Financial Budgets & Variance Analysis (الموازنات التقديرية وتحليل الانحراف)
     */
    public function index(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $budgets = FinancialBudget::with(['account', 'branch', 'creator'])
            ->forYear($year)
            ->get();

        $accounts = Account::active()->orderBy('code')->get();

        $totalBudget = (float) $budgets->sum('target_amount');
        $totalActual = (float) $budgets->sum(fn($b) => $b->actual_amount);
        $totalVariance = $totalActual - $totalBudget;

        return view('reports::budgets.index', compact(
            'budgets',
            'accounts',
            'year',
            'totalBudget',
            'totalActual',
            'totalVariance'
        ));
    }

    /**
     * Store or Update Budget Target
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fiscal_year'   => 'required|integer|min:2020|max:2050',
            'account_id'    => 'required|exists:chart_of_accounts,id',
            'period_type'   => 'required|in:annual,q1,q2,q3,q4,monthly',
            'target_amount' => 'required|numeric|min:0',
            'notes'         => 'nullable|string|max:255',
        ]);

        $tenantId = Auth::user()->tenant_id ?: (session('tenant_id') ?: 1);

        FinancialBudget::updateOrCreate(
            [
                'tenant_id'   => $tenantId,
                'fiscal_year' => $validated['fiscal_year'],
                'account_id'  => $validated['account_id'],
                'period_type' => $validated['period_type'],
            ],
            [
                'target_amount' => $validated['target_amount'],
                'notes'         => $validated['notes'] ?? null,
                'branch_id'     => activeBranch() ? activeBranch()->id : null,
                'created_by'    => Auth::id(),
            ]
        );

        return back()->with('success', 'تم حفظ وتحديث الموازنة التقديرية للحساب بنجاح.');
    }

    /**
     * Delete Budget Target
     */
    public function destroy(FinancialBudget $budget)
    {
        $budget->delete();
        return back()->with('success', 'تم حذف بند الموازنة التقديرية بنجاح.');
    }
}

