<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قسيمة راتب - {{ $item->employee?->name }} ({{ $item->payroll?->payroll_number }})</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        body {
            margin: 0;
            padding: 24px;
            background: #f1f5f9;
            color: #111;
            font-size: 13px;
        }
        .slip-box {
            border: 2px solid #0F5132;
            border-radius: 12px;
            padding: 28px;
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #0F5132;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .company-title {
            font-size: 19px;
            font-weight: 900;
            color: #0F5132;
            margin: 0 0 4px 0;
        }
        .slip-badge {
            font-size: 15px;
            font-weight: 900;
            color: #0F5132;
            border: 2px solid #0F5132;
            background: rgba(15, 81, 50, 0.05);
            padding: 6px 18px;
            border-radius: 8px;
            text-align: center;
        }
        .employee-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 16px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        .emp-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .emp-label {
            font-size: 11.5px;
            font-weight: 700;
            color: #64748B;
        }
        .emp-val {
            font-weight: 800;
            color: #0F172A;
        }
        .breakdown-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .column-box {
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            overflow: hidden;
        }
        .column-header {
            padding: 10px 14px;
            font-weight: 800;
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .column-header.earnings {
            background: #D1E7DD;
            color: #0F5132;
        }
        .column-header.deductions {
            background: #FEE2E2;
            color: #DC2626;
        }
        .column-table {
            width: 100%;
            border-collapse: collapse;
        }
        .column-table td {
            padding: 9px 14px;
            border-bottom: 1px solid #F1F5F9;
            font-size: 12.5px;
        }
        .column-table tr:last-child td {
            border-bottom: none;
        }
        .column-table td.val {
            text-align: left;
            font-weight: 800;
        }
        .total-row {
            background: #F8FAFC;
            font-weight: 900 !important;
            border-top: 2px solid #E2E8F0;
        }
        .net-salary-card {
            background: linear-gradient(135deg, rgba(15, 81, 50, 0.08) 0%, rgba(6, 46, 32, 0.03) 100%);
            border: 2px solid #0F5132;
            border-radius: 10px;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .net-title {
            font-size: 14px;
            font-weight: 800;
            color: #0F5132;
        }
        .net-value {
            font-size: 24px;
            font-weight: 900;
            color: #0F5132;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            text-align: center;
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px dashed #CBD5E1;
        }
        .sig-box {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        .sig-title {
            font-weight: 800;
            color: #475569;
        }
        .sig-line {
            border-bottom: 1px dotted #94A3B8;
            margin: 0 40px;
        }
        .no-print {
            max-width: 800px;
            margin: 0 auto 16px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .slip-box {
                border: 2px solid #000;
                box-shadow: none;
                max-width: 100%;
                border-radius: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('hr.payrolls.show', $item->payroll_id) }}" style="text-decoration: none; color: #0F5132; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i> العودة لكشف المسير
        </a>
        <button onclick="window.print()" style="background: #0F5132; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة القسيمة (Print Payslip)
        </button>
    </div>

    <div class="slip-box">
        <div class="header">
            <div>
                <h1 class="company-title">{{ activeTenant() ? activeTenant()->company_name : 'شركة العزيزي للأنظمة والحلول المتكاملة' }}</h1>
                <div style="font-size: 12px; color: #64748B; font-weight: 700;">
                    إدارة الموارد البشرية والرواتب | {{ $item->payroll?->branch?->name ?? 'المركز الرئيسي' }}
                </div>
            </div>
            <div>
                <div class="slip-badge">
                    قسيمة راتب شهرية (Payslip)
                </div>
            </div>
        </div>

        <div class="employee-card">
            <div class="emp-item">
                <span class="emp-label">اسم الموظف:</span>
                <span class="emp-val">{{ $item->employee?->name ?? '-' }}</span>
            </div>
            <div class="emp-item">
                <span class="emp-label">الرقم الوظيفي:</span>
                <span class="emp-val" style="font-family: monospace; color: #0F5132;">{{ $item->employee?->emp_code ?? '-' }}</span>
            </div>
            <div class="emp-item">
                <span class="emp-label">الشهر والسنة:</span>
                <span class="emp-val">شهر {{ $item->payroll?->month }} / {{ $item->payroll?->year }}</span>
            </div>
            <div class="emp-item">
                <span class="emp-label">القسم:</span>
                <span class="emp-val">{{ $item->employee?->department?->name ?? '-' }}</span>
            </div>
            <div class="emp-item">
                <span class="emp-label">المسمى الوظيفي:</span>
                <span class="emp-val">{{ $item->employee?->designation?->title ?? '-' }}</span>
            </div>
            <div class="emp-item">
                <span class="emp-label">رقم المسير:</span>
                <span class="emp-val" style="font-family: monospace;">{{ $item->payroll?->payroll_number ?? '-' }}</span>
            </div>
        </div>

        <div class="breakdown-grid">
            <!-- Earnings Box -->
            <div class="column-box">
                <div class="column-header earnings">
                    <span><i class="fa-solid fa-plus-circle"></i> الاستحقاقات والبدلات (Earnings)</span>
                    <span>المبلغ</span>
                </div>
                <table class="column-table">
                    <tr>
                        <td>الراتب الأساسي</td>
                        <td class="val" style="color: #0F5132;">{{ formatMoney($item->basic_salary) }}</td>
                    </tr>
                    <tr>
                        <td>بدل السكن المقترح</td>
                        <td class="val">{{ formatMoney($item->employee?->housing_allowance ?? 0) }}</td>
                    </tr>
                    <tr>
                        <td>بدل الانتقال المقترح</td>
                        <td class="val">{{ formatMoney($item->employee?->transport_allowance ?? 0) }}</td>
                    </tr>
                    @if(($item->employee?->other_allowances ?? 0) > 0)
                    <tr>
                        <td>بدلات ومزايا أخرى</td>
                        <td class="val">{{ formatMoney($item->employee?->other_allowances ?? 0) }}</td>
                    </tr>
                    @endif
                    @if($item->overtime_amount > 0)
                    <tr>
                        <td>ساعات إضافية ومكافآت</td>
                        <td class="val" style="color: #10B981;">+{{ formatMoney($item->overtime_amount) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td style="font-weight: 800; color: #0F5132;">إجمالي الاستحقاقات (Gross):</td>
                        <td class="val" style="color: #0F5132; font-size: 14px;">{{ formatMoney($item->gross_salary) }}</td>
                    </tr>
                </table>
            </div>

            <!-- Deductions Box -->
            <div class="column-box">
                <div class="column-header deductions">
                    <span><i class="fa-solid fa-minus-circle"></i> الاستقطاعات والخصومات (Deductions)</span>
                    <span>المبلغ</span>
                </div>
                <table class="column-table">
                    <tr>
                        <td>قسط سلفة الموظف</td>
                        <td class="val" style="color: #DC2626;">
                            {{ $item->loan_deduction > 0 ? formatMoney($item->loan_deduction) : '0.00' }}
                        </td>
                    </tr>
                    <tr>
                        <td>خصومات غياب وتأخير وجزاءات</td>
                        <td class="val" style="color: #DC2626;">
                            {{ $item->deductions_amount > 0 ? formatMoney($item->deductions_amount) : '0.00' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="color: #94A3B8;">تأمينات اجتماعية / أخرى</td>
                        <td class="val" style="color: #94A3B8;">0.00</td>
                    </tr>
                    <tr class="total-row">
                        <td style="font-weight: 800; color: #DC2626;">إجمالي الاستقطاعات:</td>
                        <td class="val" style="color: #DC2626; font-size: 14px;">{{ formatMoney($item->total_deductions) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="net-salary-card">
            <div>
                <span class="net-title">صافي الراتب المستحق للصرف (Net Payable):</span>
                <div style="font-size: 11.5px; color: #64748B; margin-top: 2px;">
                    طريقة الصرف: <strong>{{ $item->payment_method === 'bank' ? 'تحويل لحساب بنكي' : 'صرف نقدي من الخزينة' }}</strong>
                    @if($item->employee?->bank_account)
                        (رقم الحساب: {{ $item->employee->bank_account }})
                    @endif
                </div>
            </div>
            <div class="net-value">
                {{ formatMoney($item->net_salary) }}
            </div>
        </div>

        <div class="signatures">
            <div class="sig-box">
                <span class="sig-title">مسؤول الموارد البشرية والرواتب</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">توقيع الموظف المستلم</span>
                <span class="sig-line"></span>
            </div>
        </div>
    </div>

</body>
</html>

