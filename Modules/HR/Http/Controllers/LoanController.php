<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\EmployeeLoan;
use Modules\Treasury\Entities\Treasury;

class LoanController extends Controller
{
    /**
     * Employee Loans Listing
     */
    public function index(Request $request)
    {
        $query = EmployeeLoan::with(['employee', 'treasury', 'creator'])->latest();

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->paginate(15)->withQueryString();

        $employees = Employee::active()->orderBy('name')->get();
        $treasuries = Treasury::active()->get();

        $stats = [
            'total_loaned'    => (float) EmployeeLoan::whereIn('status', ['active', 'paid'])->sum('amount'),
            'total_paid'      => (float) EmployeeLoan::sum('paid_amount'),
            'total_remaining' => (float) EmployeeLoan::where('status', 'active')->get()->sum('remaining_amount'),
            'active_loans'    => EmployeeLoan::where('status', 'active')->count(),
        ];

        return view('hr::loans.index', compact('loans', 'employees', 'treasuries', 'stats'));
    }

    /**
     * Store New Loan Request / Advance
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'         => 'required|exists:hr_employees,id',
            'treasury_id'         => 'nullable|exists:treasuries,id',
            'date'                => 'required|date',
            'amount'              => 'required|numeric|min:1',
            'monthly_installment' => 'required|numeric|min:1',
            'total_installments'  => 'required|integer|min:1',
            'notes'               => 'nullable|string',
        ]);

        $loanNumber = EmployeeLoan::generateNumber();

        EmployeeLoan::create([
            'employee_id'         => $validated['employee_id'],
            'treasury_id'         => $validated['treasury_id'] ?? null,
            'loan_number'         => $loanNumber,
            'date'                => $validated['date'],
            'amount'              => $validated['amount'],
            'monthly_installment' => $validated['monthly_installment'],
            'total_installments'  => $validated['total_installments'],
            'paid_amount'         => 0.00,
            'status'              => 'active',
            'notes'               => $validated['notes'] ?? null,
            'created_by'          => Auth::id(),
        ]);

        return back()->with('success', 'تم تسجيل واعتماد سلفة الموظف بنجاح! رقم السلفة: ' . $loanNumber);
    }

    /**
     * Repay installment manually
     */
    public function repay(Request $request, EmployeeLoan $loan)
    {
        $validated = $request->validate([
            'repay_amount' => 'required|numeric|min:1',
        ]);

        $newPaid = $loan->paid_amount + $validated['repay_amount'];
        $loan->paid_amount = min($loan->amount, $newPaid);

        if ($loan->paid_amount >= $loan->amount) {
            $loan->status = 'paid';
        }

        $loan->save();

        return back()->with('success', 'تم تسجيل سداد قسط السلفة بنجاح! المتبقي: ' . formatMoney($loan->remaining_amount));
    }

    /**
     * Delete Loan
     */
    public function destroy(EmployeeLoan $loan)
    {
        $loan->delete();
        return back()->with('success', 'تم حذف سجل السلفة بنجاح.');
    }
}
