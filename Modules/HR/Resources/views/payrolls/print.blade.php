<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف مسير الرواتب المعتمد - {{ $payroll->payroll_number }}</title>
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
            font-size: 12.5px;
        }
        .sheet-box {
            border: 2px solid #0F5132;
            border-radius: 12px;
            padding: 28px;
            max-width: 1100px;
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
            font-size: 20px;
            font-weight: 900;
            color: #0F5132;
            margin: 0 0 4px 0;
        }
        .sheet-badge {
            font-size: 16px;
            font-weight: 900;
            color: #0F5132;
            border: 2px solid #0F5132;
            background: rgba(15, 81, 50, 0.05);
            padding: 6px 20px;
            border-radius: 8px;
            text-align: center;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
            background: #F8FAFC;
            padding: 14px 18px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
        }
        .meta-row {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .meta-label {
            font-weight: 700;
            color: #64748B;
            font-size: 11.5px;
        }
        .meta-val {
            font-weight: 800;
            color: #0F172A;
        }
        .payroll-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .payroll-table th, .payroll-table td {
            border: 1px solid #CBD5E1;
            padding: 8px 10px;
        }
        .payroll-table th {
            background: #F1F5F9;
            color: #0F172A;
            font-weight: 800;
            font-size: 12px;
        }
        .payroll-table tfoot td {
            background: #E2E8F0;
            font-weight: 900;
        }
        .signatures {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
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
            margin: 0 10px;
        }
        .no-print {
            max-width: 1100px;
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
            .sheet-box {
                border: 2px solid #000;
                box-shadow: none;
                max-width: 100%;
                border-radius: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('hr.payrolls.show', $payroll->id) }}" style="text-decoration: none; color: #0F5132; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i> العودة لشاشة المسير
        </a>
        <button onclick="window.print()" style="background: #0F5132; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة كشف المسير (Print)
        </button>
    </div>

    <div class="sheet-box">
        <div class="header">
            <div>
                <h1 class="company-title">{{ activeTenant() ? activeTenant()->company_name : 'شركة العزيزي للأنظمة والحلول المتكاملة' }}</h1>
                <div style="font-size: 12px; color: #64748B; font-weight: 700;">
                    إدارة الموارد البشرية والرواتب | فرع: {{ $payroll->branch?->name ?? 'المركز الرئيسي' }}
                </div>
            </div>
            <div>
                <div class="sheet-badge">
                    كشف مسير رواتب الموظفين المعتمد
                </div>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-row">
                <span class="meta-label">رقم المسير:</span>
                <span class="meta-val" style="font-family: monospace; color: #0F5132;">{{ $payroll->payroll_number }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">الفترة / الشهر:</span>
                <span class="meta-val">شهر {{ $payroll->month }} لسنة {{ $payroll->year }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">حالة المسير:</span>
                <span class="meta-val" style="color: {{ $payroll->status === 'paid' ? '#0F5132' : ($payroll->status === 'approved' ? '#2563EB' : '#D97706') }};">
                    {{ $payroll->status === 'paid' ? 'تم الصرف والتسوية' : ($payroll->status === 'approved' ? 'معتمد للصرف' : 'مسودة') }}
                </span>
            </div>
            <div class="meta-row">
                <span class="meta-label">تاريخ الإصدار / الطباعة:</span>
                <span class="meta-val">{{ date('Y-m-d') }}</span>
            </div>
        </div>

        <table class="payroll-table">
            <thead>
                <tr>
                    <th style="width: 30px; text-align: center;">#</th>
                    <th style="width: 80px; text-align: center;">الكود</th>
                    <th style="text-align: right;">اسم الموظف</th>
                    <th style="width: 120px; text-align: right;">الوظيفة</th>
                    <th style="width: 90px; text-align: right;">الأساسي</th>
                    <th style="width: 90px; text-align: right;">البدلات</th>
                    <th style="width: 95px; text-align: right;">إجمالي الراتب</th>
                    <th style="width: 85px; text-align: right; color: #DC2626;">قسط السلفة</th>
                    <th style="width: 85px; text-align: right; color: #DC2626;">خصومات</th>
                    <th style="width: 100px; text-align: right;">صافي المستحق</th>
                    <th style="width: 75px; text-align: center;">طريقة الصرف</th>
                    <th style="width: 110px; text-align: center;">توقيع الاستلام</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payroll->items as $idx => $item)
                <tr>
                    <td style="text-align: center; color: #64748B; font-size: 11px;">{{ $idx + 1 }}</td>
                    <td style="text-align: center; font-family: monospace; font-weight: 800; color: #0F5132;">{{ $item->employee?->emp_code ?? '-' }}</td>
                    <td style="font-weight: 800;">{{ $item->employee?->name ?? '-' }}</td>
                    <td style="font-size: 11.5px; color: #475569;">{{ $item->employee?->designation?->title ?? '-' }}</td>
                    <td style="text-align: right; font-weight: 700;">{{ formatMoney($item->basic_salary) }}</td>
                    <td style="text-align: right; font-weight: 700; color: #0F5132;">+{{ formatMoney($item->allowances_amount + $item->overtime_amount) }}</td>
                    <td style="text-align: right; font-weight: 800;">{{ formatMoney($item->gross_salary) }}</td>
                    <td style="text-align: right; font-weight: 700; color: #DC2626;">
                        {{ $item->loan_deduction > 0 ? '-' . formatMoney($item->loan_deduction) : '-' }}
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #DC2626;">
                        {{ $item->deductions_amount > 0 ? '-' . formatMoney($item->deductions_amount) : '-' }}
                    </td>
                    <td style="text-align: right; font-weight: 900; color: #0F5132; font-size: 13px;">
                        {{ formatMoney($item->net_salary) }}
                    </td>
                    <td style="text-align: center; font-size: 11px;">
                        {{ $item->payment_method === 'bank' ? 'تحويل بنكي' : 'نقدي' }}
                    </td>
                    <td style="text-align: center;"></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: left; padding: 10px; font-size: 13px;">الإجمالي الكلي:</td>
                    <td style="text-align: right;">{{ formatMoney($payroll->total_basic) }}</td>
                    <td style="text-align: right; color: #0F5132;">+{{ formatMoney($payroll->total_allowances) }}</td>
                    <td style="text-align: right;">{{ formatMoney($payroll->total_basic + $payroll->total_allowances) }}</td>
                    <td style="text-align: right; color: #DC2626;">-{{ formatMoney($payroll->total_loans_deducted) }}</td>
                    <td style="text-align: right; color: #DC2626;">-{{ formatMoney($payroll->total_deductions) }}</td>
                    <td style="text-align: right; color: #0F5132; font-size: 13.5px;">{{ formatMoney($payroll->total_net) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <div class="signatures">
            <div class="sig-box">
                <span class="sig-title">إعداد: مسؤول الرواتب</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">تدقيق: المحاسب المالي</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">مراجعة: المدير المالي</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">اعتماد: المدير العام</span>
                <span class="sig-line"></span>
            </div>
        </div>
    </div>

</body>
</html>
