@extends('layouts.app')

@section('title', 'الملف الوظيفي - ' . $employee->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Profile Header -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0; background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 18px;">
            <div style="display: flex; align-items: center; gap: 18px;">
                <div style="width: 68px; height: 68px; border-radius: 18px; background: rgba(15, 81, 50, 0.12); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 900; border: 2px solid rgba(15, 81, 50, 0.2);">
                    {{ mb_substr($employee->name, 0, 1) }}
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                        <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0;">{{ $employee->name }}</h2>
                        <span style="font-family: monospace; font-size: 13px; font-weight: 800; background: #F1F5F9; color: var(--primary); padding: 3px 8px; border-radius: 6px;">
                            {{ $employee->emp_code }}
                        </span>
                        @if($employee->status === 'active')
                            <span style="background: #D1E7DD; color: #0F5132; padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800;">نشط</span>
                        @elseif($employee->status === 'on_leave')
                            <span style="background: #FEF3C7; color: #D97706; padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800;">في إجازة</span>
                        @else
                            <span style="background: #FEE2E2; color: #DC2626; padding: 4px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800;">{{ $employee->status }}</span>
                        @endif
                    </div>
                    <span style="font-size: 13px; color: var(--text-muted); font-weight: 700;">
                        {{ $employee->department?->name ?? 'بدون قسم' }} | {{ $employee->designation?->title ?? 'بدون مسمى' }} | {{ $employee->branch?->name ?? 'المركز الرئيسي' }}
                    </span>
                </div>
            </div>

            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('hr.employees.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-right"></i> القائمة
                </a>
                <a href="{{ route('hr.employees.edit', $employee->id) }}" style="background: #FEF3C7; color: #D97706; padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-pen-to-square"></i> تعديل الملف
                </a>
                <a href="{{ route('hr.loans.index', ['employee_id' => $employee->id]) }}" style="background: rgba(15, 81, 50, 0.1); color: var(--primary); padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-hand-holding-dollar"></i> سجل السلف
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">الراتب الشامل الشهري</span>
            <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ formatMoney($employee->total_salary) }}</strong>
            <span style="font-size: 11px; color: var(--text-muted); display: block;">أساسي: {{ formatMoney($employee->basic_salary) }}</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">المتبقي من سلف الموظف</span>
            <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ formatMoney($employee->active_loan_balance) }}</strong>
            <span style="font-size: 11px; color: var(--text-muted); display: block;">سلف نشطة قيد السداد</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تاريخ التعيين والخدمة</span>
            <strong style="font-size: 16px; font-weight: 800; color: var(--text-main);">{{ $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '-' }}</strong>
            <span style="font-size: 11px; color: #10B981; display: block;">{{ $employee->hire_date ? $employee->hire_date->diffForHumans() : '' }}</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">نوع التعاقد</span>
            <strong style="font-size: 16px; font-weight: 800; color: #4338CA;">
                {{ $employee->contract_type === 'full_time' ? 'دوام كامل' : ($employee->contract_type === 'part_time' ? 'دوام جزئي' : $employee->contract_type) }}
            </strong>
            <span style="font-size: 11px; color: var(--text-muted); display: block;">هاتف: {{ $employee->phone ?: '-' }}</span>
        </div>
    </div>

    <!-- Detailed 2-Column Info Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">

        <!-- Salary & Financial Profile -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 900; color: var(--primary); margin-bottom: 16px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px;">
                <i class="fa-solid fa-coins"></i> هيكل الراتب والبيانات المصرفية
            </h3>

            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted); width: 140px;">الراتب الأساسي:</td>
                        <td style="padding: 10px; font-weight: 800; color: var(--text-main);">{{ formatMoney($employee->basic_salary) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">بدل السكن:</td>
                        <td style="padding: 10px; font-weight: 700; color: var(--text-main);">{{ formatMoney($employee->housing_allowance) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">بدل الانتقال:</td>
                        <td style="padding: 10px; font-weight: 700; color: var(--text-main);">{{ formatMoney($employee->transport_allowance) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">بدلات أخرى:</td>
                        <td style="padding: 10px; font-weight: 700; color: var(--text-main);">{{ formatMoney($employee->other_allowances) }}</td>
                    </tr>
                    <tr style="background: #F8FAFC; font-weight: 900;">
                        <td style="padding: 12px; color: #0F5132;">إجمالي الراتب الشامل:</td>
                        <td style="padding: 12px; color: #0F5132; font-size: 15px;">{{ formatMoney($employee->total_salary) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">اسم البنك:</td>
                        <td style="padding: 10px; font-weight: 700;">{{ $employee->bank_name ?: '-' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">رقم الحساب:</td>
                        <td style="padding: 10px; font-family: monospace; font-weight: 700;">{{ $employee->bank_account ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; color: var(--text-muted);">الآيبان (IBAN):</td>
                        <td style="padding: 10px; font-family: monospace; font-size: 12px;">{{ $employee->iban ?: '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Personal & Contact Info -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 900; color: #4338CA; margin-bottom: 16px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px;">
                <i class="fa-solid fa-id-card-clip"></i> المعلومات الإدارية والشخصية
            </h3>

            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted); width: 140px;">الرقم القومي / الهوية:</td>
                        <td style="padding: 10px; font-family: monospace; font-weight: 700;">{{ $employee->national_id ?: '-' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">البريد الإلكتروني:</td>
                        <td style="padding: 10px; font-weight: 700; color: var(--text-main);">{{ $employee->email ?: '-' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">تاريخ الميلاد:</td>
                        <td style="padding: 10px;">{{ $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '-' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">الفرع التابع له:</td>
                        <td style="padding: 10px; font-weight: 700;">{{ $employee->branch?->name ?? 'المركز الرئيسي' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; color: var(--text-muted); vertical-align: top;">ملاحظات خاصة:</td>
                        <td style="padding: 10px; color: var(--text-muted); line-height: 1.5;">{{ $employee->notes ?: 'لا توجد ملاحظات مسجلة' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Active Loans Table -->
    @if($employee->activeLoans->isNotEmpty())
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: #D97706; margin-bottom: 14px;">
            <i class="fa-solid fa-hand-holding-dollar"></i> السلف النشطة قيد السداد
        </h3>
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #FEF3C7; color: #92400E; font-weight: 800;">
                    <th style="padding: 10px; text-align: right;">رقم السلفة</th>
                    <th style="padding: 10px; text-align: right;">التاريخ</th>
                    <th style="padding: 10px; text-align: right;">إجمالي السلفة</th>
                    <th style="padding: 10px; text-align: right;">القسط الشهري</th>
                    <th style="padding: 10px; text-align: right;">المسدد</th>
                    <th style="padding: 10px; text-align: right;">المتبقي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employee->activeLoans as $loan)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 10px; font-family: monospace; font-weight: 800;">{{ $loan->loan_number }}</td>
                    <td style="padding: 10px;">{{ $loan->date->format('Y-m-d') }}</td>
                    <td style="padding: 10px; font-weight: 700;">{{ formatMoney($loan->amount) }}</td>
                    <td style="padding: 10px; font-weight: 700; color: #4338CA;">{{ formatMoney($loan->monthly_installment) }}</td>
                    <td style="padding: 10px; color: #0F5132;">{{ formatMoney($loan->paid_amount) }}</td>
                    <td style="padding: 10px; font-weight: 800; color: #DC2626;">{{ formatMoney($loan->remaining_amount) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
@endsection

