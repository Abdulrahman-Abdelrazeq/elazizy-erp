@extends('layouts.app')

@section('title', 'كشف مسير الرواتب - ' . $payroll->payroll_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0;">
                    مسير الرواتب: <span style="font-family: monospace; color: var(--primary);">{{ $payroll->payroll_number }}</span>
                </h2>
                @if($payroll->status === 'paid')
                    <span style="background: #D1E7DD; color: #0F5132; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-check-double"></i> تم الصرف
                    </span>
                @elseif($payroll->status === 'approved')
                    <span style="background: #E0E7FF; color: #4338CA; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-stamp"></i> معتمد وجاهز للصرف
                    </span>
                @else
                    <span style="background: #FEF3C7; color: #D97706; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-solid fa-clock"></i> مسودة قيد المراجعة
                    </span>
                @endif
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">
                شهر {{ $payroll->month }} / {{ $payroll->year }} &bull; الفرع: {{ $payroll->branch?->name ?? 'المركز الرئيسي' }} &bull; تاريخ الإنشاء: {{ $payroll->created_at->format('Y-m-d H:i') }}
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('hr.payrolls.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع للمسيرات
            </a>

            <a href="{{ route('hr.payrolls.print', $payroll->id) }}" target="_blank" style="background: #0F5132; color: #fff; padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة كشف المسير المعتمد
            </a>

            @if($payroll->status === 'draft')
                <form method="POST" action="{{ route('hr.payrolls.approve', $payroll->id) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من اعتماد كشف مسير الرواتب؟')">
                    @csrf
                    <button type="submit" style="background: #2563EB; color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-stamp"></i>
                        اعتماد المسير
                    </button>
                </form>
            @endif

            @if($payroll->status === 'approved')
                <button type="button" onclick="document.getElementById('payoutModal').style.display='flex'" style="background: #059669; color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-money-bill-wave"></i>
                    صرف الرواتب للموظفين
                </button>
            @endif
        </div>
    </div>

    <!-- Payout Information Banner if Paid -->
    @if($payroll->status === 'paid')
        <div style="background: #D1E7DD; border: 1px solid #A3CFBB; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #0F5132; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <strong style="color: #0F5132; font-size: 14px; display: block;">تم صرف كشف المسير بالكامل وتحديث سلف الموظفين</strong>
                    <span style="color: #146C43; font-size: 12.5px;">
                        تم الصرف من خزينة: <strong>{{ $payroll->treasury?->name ?? 'الخزينة الرئيسية' }}</strong> &bull; بتاريخ: <strong>{{ $payroll->payment_date ? $payroll->payment_date->format('Y-m-d') : '-' }}</strong> &bull; تم الاعتماد بواسطة: <strong>{{ $payroll->approver?->name ?? 'المسؤول' }}</strong>
                    </span>
                </div>
            </div>
            <div style="font-size: 16px; font-weight: 900; color: #0F5132;">
                المبلغ المصروف: {{ formatMoney($payroll->total_net) }}
            </div>
        </div>
    @endif

    <!-- Financial Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #64748B;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">إجمالي الرواتب الأساسية</span>
            <strong style="font-size: 20px; font-weight: 900; color: var(--text-main);">{{ formatMoney($payroll->total_basic) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">إجمالي البدلات والمكافآت (+)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #10B981;">+{{ formatMoney($payroll->total_allowances) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">إجمالي استقطاع السلف والخصم (-)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #DC2626;">-{{ formatMoney($payroll->total_deductions + $payroll->total_loans_deducted) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); background: rgba(15, 81, 50, 0.02);">
            <span style="font-size: 12px; font-weight: 700; color: var(--primary); display: block; margin-bottom: 4px;">صافي الرواتب المستحقة للصرف</span>
            <strong style="font-size: 22px; font-weight: 900; color: var(--primary);">{{ formatMoney($payroll->total_net) }}</strong>
        </div>
    </div>

    <!-- Payroll Items List Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: var(--primary);"></i>
                قائمة مستحقات الموظفين ({{ $payroll->items->count() }} موظف)
            </h3>
            <span style="font-size: 12.5px; color: var(--text-muted);">
                تتضمن تفاصيل الاستحقاقات والاستقطاعات لكل موظف
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 12.5px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 40px;">#</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 90px;">الكود</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم الموظف</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">القسم / الوظيفة</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 105px;">الأساسي</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 105px;">البدلات</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 105px;">إجمالي الراتب</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 105px; color: #DC2626;">قسط السلفة</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 105px; color: #DC2626;">خصومات أخرى</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 115px;">الصافي للصرف</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 90px;">طريقة الصرف</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 70px;">قسيمة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payroll->items as $idx => $item)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center; color: var(--text-muted); font-size: 11px;">
                            {{ $idx + 1 }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            {{ $item->employee?->emp_code ?? '-' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            <a href="{{ route('hr.employees.show', $item->employee_id) }}" style="color: inherit; text-decoration: none;">
                                {{ $item->employee?->name ?? 'موظف محذوف' }}
                            </a>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 11.5px;">
                            <span style="font-weight: 700; color: var(--text-main); display: block;">{{ $item->employee?->department?->name ?? '-' }}</span>
                            <span>{{ $item->employee?->designation?->title ?? '-' }}</span>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ formatMoney($item->basic_salary) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: #10B981;">
                            +{{ formatMoney($item->allowances_amount + $item->overtime_amount) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            {{ formatMoney($item->gross_salary) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: #DC2626;">
                            @if($item->loan_deduction > 0)
                                -{{ formatMoney($item->loan_deduction) }}
                            @else
                                <span style="color: var(--text-muted); font-weight: 400;">0.00</span>
                            @endif
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: #DC2626;">
                            @if($item->deductions_amount > 0)
                                -{{ formatMoney($item->deductions_amount) }}
                            @else
                                <span style="color: var(--text-muted); font-weight: 400;">0.00</span>
                            @endif
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: #0F5132; font-size: 13.5px;">
                            {{ formatMoney($item->net_salary) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($item->payment_method === 'bank')
                                <span style="background: #E0E7FF; color: #4338CA; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                    <i class="fa-solid fa-building-columns"></i> بنكي
                                </span>
                            @else
                                <span style="background: #F1F5F9; color: var(--text-muted); padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                    <i class="fa-solid fa-money-bill"></i> نقدي
                                </span>
                            @endif
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            <a href="{{ route('hr.payrolls.slip', $item->id) }}" target="_blank" style="width: 30px; height: 30px; border-radius: 6px; background: #F1F5F9; color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="طباعة قسيمة الراتب">
                                <i class="fa-solid fa-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #F8FAFC; font-weight: 900;">
                        <td colspan="4" style="padding: 12px 10px; text-align: left; font-size: 13px; color: var(--text-main);">الإجماليات الكلية:</td>
                        <td style="padding: 12px 10px; color: var(--text-main);">{{ formatMoney($payroll->total_basic) }}</td>
                        <td style="padding: 12px 10px; color: #10B981;">+{{ formatMoney($payroll->total_allowances) }}</td>
                        <td style="padding: 12px 10px; color: var(--text-main);">{{ formatMoney($payroll->total_basic + $payroll->total_allowances) }}</td>
                        <td style="padding: 12px 10px; color: #DC2626;">-{{ formatMoney($payroll->total_loans_deducted) }}</td>
                        <td style="padding: 12px 10px; color: #DC2626;">-{{ formatMoney($payroll->total_deductions) }}</td>
                        <td style="padding: 12px 10px; color: #0F5132; font-size: 14px;">{{ formatMoney($payroll->total_net) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Payout Payroll Batch -->
<div id="payoutModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: modalFadeIn 0.2s ease;">
        <div style="background: linear-gradient(135deg, #059669 0%, #064E3B 100%); padding: 18px 24px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </span>
                <h3 style="margin: 0; font-size: 16px; font-weight: 800;">صرف مسير الرواتب وتسوية السلف</h3>
            </div>
            <button type="button" onclick="document.getElementById('payoutModal').style.display='none'" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('hr.payrolls.pay', $payroll->id) }}" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 14px; font-size: 13px; color: #065F46;">
                <strong>المبلغ الإجمالي المطلوب صرفه:</strong>
                <div style="font-size: 20px; font-weight: 900; margin-top: 4px; color: #047857;">
                    {{ formatMoney($payroll->total_net) }}
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الخزينة أو الحساب البنكي للصرف منه *</label>
                <select name="treasury_id" class="erp-input" required>
                    <option value="">-- اختر الخزينة أو الحساب --</option>
                    @foreach($treasuries as $t)
                        <option value="{{ $t->id }}">
                            {{ $t->name }} (الرصيد الحالي: {{ formatMoney($t->balance) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">تاريخ الصرف *</label>
                <input type="date" name="payment_date" class="erp-input" value="{{ date('Y-m-d') }}" required>
            </div>

            <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 12px; font-size: 12px; color: #92400E;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-left: 4px;"></i>
                عند تأكيد الصرف، سيتم تسوية وتخفيض مبالغ السلف النشطة على الموظفين بمقدار الأقساط المستقطعة آلياً.
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('payoutModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--text-muted);">
                    إلغاء
                </button>
                <button type="submit" style="background: #059669; color: #fff; padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                    <i class="fa-solid fa-check"></i> تأكيد وصرف المسير
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
