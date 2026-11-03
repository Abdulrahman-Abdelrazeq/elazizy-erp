@extends('layouts.app')

@section('title', 'سلف وقروض الموظفين (Employee Loans & Advances)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: #D97706; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </span>
                سلف وقروض الموظفين (Employee Loans & Advances)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدارة طلبات السلف، جدولة الأقساط الشهرية، والاستقطاع الآلي المباشر من مسيرات الرواتب
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('hr.employees.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-users"></i>
                دليل الموظفين
            </a>
            <button onclick="openLoanModal()" class="btn-gold-custom" style="padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px;">
                <i class="fa-solid fa-plus-circle"></i>
                تسجيل سلفة موظف جديدة
            </button>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي السلف الممنوحة</span>
            <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ formatMoney($stats['total_loaned']) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي ما تم سداده / استقطاعه</span>
            <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['total_paid']) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">الرصيد المتبقي قيد التحصيل</span>
            <strong style="font-size: 20px; font-weight: 900; color: #DC2626;">{{ formatMoney($stats['total_remaining']) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">عدد السلف النشطة</span>
            <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['active_loans'] }} سلفة</strong>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('hr.loans.index') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الموظف</label>
                <select name="employee_id" class="erp-input">
                    <option value="">جميع الموظفين</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->emp_code }} - {{ $emp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الحالة</label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشطة قيد السداد</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>مسددة بالكامل</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 20px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
                <a href="{{ route('hr.loans.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Loans Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">رقم السلفة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">الموظف المستفيد</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">تاريخ المنح</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">مبلغ السلفة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">القسط الشهري</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">المسدد</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">المتبقي</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 100px;">الحالة</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 130px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; font-family: monospace; font-weight: 800; color: #D97706;">
                            {{ $loan->loan_number }}
                        </td>
                        <td style="padding: 12px; font-weight: 800; color: var(--text-main);">
                            {{ $loan->employee?->name ?? 'موظف محذوف' }}
                            <span style="font-size: 11px; color: var(--text-muted); display: block;">{{ $loan->employee?->emp_code }}</span>
                        </td>
                        <td style="padding: 12px; color: var(--text-muted);">
                            {{ $loan->date->format('Y-m-d') }}
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 800; color: var(--text-main);">
                            {{ formatMoney($loan->amount) }}
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 700; color: #4338CA;">
                            {{ formatMoney($loan->monthly_installment) }}
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 700; color: #0F5132;">
                            {{ formatMoney($loan->paid_amount) }}
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 900; color: {{ $loan->remaining_amount > 0 ? '#DC2626' : '#0F5132' }};">
                            {{ formatMoney($loan->remaining_amount) }}
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            @if($loan->status === 'active')
                                <span style="background: #FEF3C7; color: #D97706; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">سارية</span>
                            @elseif($loan->status === 'paid')
                                <span style="background: #D1E7DD; color: #0F5132; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">مسددة بالكامل</span>
                            @else
                                <span style="background: #F1F5F9; color: var(--text-muted); padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">{{ $loan->status }}</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                @if($loan->status === 'active')
                                    <button onclick="openRepayModal({{ $loan->id }}, '{{ $loan->loan_number }}', '{{ $loan->employee?->name }}', {{ $loan->remaining_amount }}, {{ $loan->monthly_installment }})" style="background: #D1E7DD; color: #0F5132; border: none; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; cursor: pointer;" title="سداد قسط يدوي">
                                        سداد قسط
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('hr.loans.destroy', $loan->id) }}" onsubmit="return confirm('هل تريد حذف هذه السلفة؟')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #EF4444; cursor: pointer; font-size: 15px;" title="حذف">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="padding: 30px; text-align: center; color: var(--text-muted);">
                            لا توجد سلف مسجلة حالياً.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
            <div style="margin-top: 20px;">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Create Loan Modal -->
<div class="theme-modal" id="loanModal">
    <div class="modal-box" style="max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #D97706; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-hand-holding-dollar"></i> تسجيل سلفة موظف جديدة
            </h3>
            <button onclick="closeLoanModal()" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('hr.loans.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الموظف المستفيد <span style="color: #EF4444;">*</span></label>
                    <select name="employee_id" class="erp-input" required>
                        <option value="">-- اختر الموظف --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->emp_code }} - {{ $emp->name }} (راتب: {{ formatMoney($emp->basic_salary) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">تاريخ منح السلفة <span style="color: #EF4444;">*</span></label>
                    <input type="date" name="date" class="erp-input" value="{{ date('Y-m-d') }}" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">مبلغ السلفة <span style="color: #EF4444;">*</span></label>
                        <input type="number" step="0.01" min="1" name="amount" id="loanAmount" class="erp-input" required placeholder="0.00" oninput="calcInstallment()">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">عدد الأقساط الشهرية <span style="color: #EF4444;">*</span></label>
                        <input type="number" min="1" name="total_installments" id="loanInstallments" class="erp-input" value="1" required oninput="calcInstallment()">
                    </div>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">قيمة القسط الشهري المستقطع</label>
                    <input type="number" step="0.01" min="1" name="monthly_installment" id="loanMonthly" class="erp-input" required style="font-weight: 800; color: #4338CA;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الخزينة المنصرف منها (اختياري)</label>
                    <select name="treasury_id" class="erp-input">
                        <option value="">-- اختياري --</option>
                        @foreach($treasuries as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} (رصيد: {{ formatMoney($t->current_balance) }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">البيان / ملاحظات</label>
                    <input type="text" name="notes" class="erp-input" placeholder="سبب طلب السلفة...">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="closeLoanModal()" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-gold-custom" style="padding: 8px 24px; border-radius: 8px; font-size: 13px; font-weight: 800;">اعتماد ومنح السلفة</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Repay Installment Modal -->
<div class="theme-modal" id="repayModal">
    <div class="modal-box" style="max-width: 420px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #0F5132; margin: 0;">سداد قسط سلفة يدوي</h3>
            <button onclick="closeRepayModal()" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form id="repayForm" method="POST" action="">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="background: #F8FAFC; padding: 12px; border-radius: 8px; font-size: 12.5px;">
                    <span style="display: block; font-weight: 700; color: var(--text-main);" id="repayEmpName">-</span>
                    <span style="color: var(--text-muted);" id="repayLoanNumber">-</span>
                    <strong style="display: block; margin-top: 4px; color: #DC2626;" id="repayRemaining">-</strong>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">مبلغ القسط المراد سداده <span style="color: #EF4444;">*</span></label>
                    <input type="number" step="0.01" min="1" name="repay_amount" id="repayAmountInput" class="erp-input" required style="font-weight: 800; font-size: 15px; color: #0F5132;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="closeRepayModal()" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 8px 24px; border-radius: 8px; font-size: 13px; font-weight: 800;">تأكيد السداد</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openLoanModal() { document.getElementById('loanModal').classList.add('active'); }
function closeLoanModal() { document.getElementById('loanModal').classList.remove('active'); }

function calcInstallment() {
    let amt = parseFloat(document.getElementById('loanAmount').value) || 0;
    let inst = parseInt(document.getElementById('loanInstallments').value) || 1;
    if (amt > 0 && inst > 0) {
        document.getElementById('loanMonthly').value = (amt / inst).toFixed(2);
    }
}

function openRepayModal(id, number, empName, remaining, monthly) {
    document.getElementById('repayForm').action = "/hr/loans/" + id + "/repay";
    document.getElementById('repayEmpName').textContent = 'الموظف: ' + empName;
    document.getElementById('repayLoanNumber').textContent = 'رقم السلفة: ' + number;
    document.getElementById('repayRemaining').textContent = 'المتبقي الإجمالي: ' + remaining.toFixed(2) + ' ج.م/ر.س';
    document.getElementById('repayAmountInput').value = Math.min(remaining, monthly);
    document.getElementById('repayAmountInput').max = remaining;
    document.getElementById('repayModal').classList.add('active');
}
function closeRepayModal() { document.getElementById('repayModal').classList.remove('active'); }
</script>
@endsection
