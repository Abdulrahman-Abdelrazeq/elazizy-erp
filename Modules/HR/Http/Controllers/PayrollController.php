<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\HR\Entities\Employee;
use Modules\HR\Entities\Payroll;
use Modules\HR\Entities\PayrollItem;
use Modules\Treasury\Entities\Treasury;

class PayrollController extends Controller
{
    /**
     * Payroll Batches Listing
     */
    public function index(Request $request)
    {
        $query = Payroll::with(['branch', 'treasury', 'creator', 'approver'])->latest('year')->latest('month');

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->paginate(15)->withQueryString();
        $branches = Branch::all();
        $treasuries = Treasury::active()->get();

        $stats = [
            'total_payrolls' => Payroll::count(),
            'paid_payrolls'  => Payroll::where('status', 'paid')->count(),
            'total_paid_net' => (float) Payroll::where('status', 'paid')->sum('total_net'),
            'draft_count'    => Payroll::where('status', 'draft')->count(),
        ];

        return view('hr::payrolls.index', compact('payrolls', 'branches', 'treasuries', 'stats'));
    }

    /**
     * Generate Monthly Payroll Batch Automatically
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'year'      => 'required|integer|min:2020|max:2050',
            'month'     => 'required|integer|min:1|max:12',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $tenantId = currentTenant() ? currentTenant()->id : 1;
        $year = (int) $validated['year'];
        $month = (int) $validated['month'];
        $branchId = $validated['branch_id'] ?? (activeBranch() ? activeBranch()->id : null);

        // Check if payroll already exists and is paid
        $existing = Payroll::where('tenant_id', $tenantId)
            ->where('branch_id', $branchId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if ($existing && $existing->status === 'paid') {
            return back()->with('error', 'مسير الرواتب لهذا الشهر تم صرفه واعتماده مسبقاً ولا يمكن إعادة توليده!');
        }

        DB::beginTransaction();
        try {
            $payrollNumber = Payroll::generateNumber($year, $month);

            $payroll = Payroll::updateOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'branch_id' => $branchId,
                    'year'      => $year,
                    'month'     => $month,
                ],
                [
                    'payroll_number' => $payrollNumber,
                    'status'         => 'draft',
                    'created_by'     => Auth::id(),
                ]
            );

            // Fetch active employees for this branch
            $employees = Employee::active()
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->get();

            if ($employees->isEmpty()) {
                DB::rollBack();
                return back()->with('error', 'لا يوجد موظفون نشطون مؤهلون لمسير الرواتب في هذا الفرع!');
            }

            foreach ($employees as $emp) {
                $basic = $emp->basic_salary;
                $allowances = $emp->housing_allowance + $emp->transport_allowance + $emp->other_allowances;

                // Active loan installment deduction
                $loanDeduction = 0.00;
                $activeLoan = $emp->activeLoans()->first();
                if ($activeLoan && $activeLoan->remaining_amount > 0) {
                    $loanDeduction = min($activeLoan->monthly_installment, $activeLoan->remaining_amount);
                }

                $net = max(0.00, $basic + $allowances - $loanDeduction);

                PayrollItem::updateOrCreate(
                    [
                        'tenant_id'   => $tenantId,
                        'payroll_id'  => $payroll->id,
                        'employee_id' => $emp->id,
                    ],
                    [
                        'basic_salary'      => $basic,
                        'allowances_amount' => $allowances,
                        'overtime_amount'   => 0.00,
                        'deductions_amount' => 0.00,
                        'loan_deduction'    => $loanDeduction,
                        'net_salary'        => $net,
                        'status'            => 'pending',
                        'payment_method'    => $emp->bank_account ? 'bank' : 'cash',
                    ]
                );
            }

            $payroll->recalculateTotals();

            DB::commit();

            return redirect()->route('hr.payrolls.show', $payroll->id)
                ->with('success', 'تم توليد مسير الرواتب لشهر ' . $month . '/' . $year . ' بنجاح! عدد الموظفين المشمولين: ' . $employees->count());
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء توليد المسير: ' . $e->getMessage());
        }
    }

    /**
     * Show Payroll Batch Details
     */
    public function show(Payroll $payroll)
    {
        $payroll->load(['branch', 'treasury', 'creator', 'approver', 'items.employee.department', 'items.employee.designation']);
        $treasuries = Treasury::active()->get();

        return view('hr::payrolls.show', compact('payroll', 'treasuries'));
    }

    /**
     * Printable Payroll Sheet (كشف مسير الرواتب المعتمد)
     */
    public function print(Payroll $payroll)
    {
        $payroll->load(['branch', 'treasury', 'creator', 'approver', 'items.employee.department', 'items.employee.designation']);
        return view('hr::payrolls.print', compact('payroll'));
    }

    /**
     * Printable Single Employee Payslip (قسيمة راتب الموظف)
     */
    public function printSlip(PayrollItem $item)
    {
        $item->load(['payroll.branch', 'employee.department', 'employee.designation']);
        return view('hr::payrolls.slip', compact('item'));
    }

    /**
     * Approve Payroll Batch
     */
    public function approve(Payroll $payroll)
    {
        if ($payroll->status === 'paid') {
            return back()->with('error', 'المسير معتمد ومدفوع بالفعل.');
        }

        $payroll->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return back()->with('success', 'تم اعتماد مسير الرواتب بنجاح وجاهز للصرف.');
    }

    /**
     * Pay out Payroll Batch
     */
    public function pay(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'treasury_id'  => 'required|exists:treasuries,id',
            'payment_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $payroll->update([
                'status'       => 'paid',
                'treasury_id'  => $validated['treasury_id'],
                'payment_date' => $validated['payment_date'],
            ]);

            // Update items status and deduct loans
            foreach ($payroll->items as $item) {
                $item->update(['status' => 'paid']);

                if ($item->loan_deduction > 0) {
                    $loan = $item->employee->activeLoans()->first();
                    if ($loan) {
                        $loan->paid_amount += $item->loan_deduction;
                        if ($loan->paid_amount >= $loan->amount) {
                            $loan->status = 'paid';
                        }
                        $loan->save();
                    }
                }
            }

            DB::commit();

            return back()->with('success', 'تم صرف مسير الرواتب وتسجيل استقطاعات السلف بنجاح! المبلغ المنصرف: ' . formatMoney($payroll->total_net));
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'فشل في عملية صرف المسير: ' . $e->getMessage());
        }
    }

    /**
     * Delete Draft Payroll
     */
    public function destroy(Payroll $payroll)
    {
        if ($payroll->status === 'paid') {
            return back()->with('error', 'لا يمكن حذف مسير رواتب تم صرفه!');
        }

        $payroll->delete();
        return redirect()->route('hr.payrolls.index')->with('success', 'تم حذف مسير الرواتب بنجاح.');
    }
}

