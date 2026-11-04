<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف وسجل الأصول الثابتة والإهلاك المعتمد</title>
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
        .report-box {
            border: 2px solid #0F5132;
            border-radius: 12px;
            padding: 28px;
            max-width: 1050px;
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
        .report-badge {
            font-size: 15px;
            font-weight: 900;
            color: #0F5132;
            border: 2px solid #0F5132;
            background: rgba(15, 81, 50, 0.05);
            padding: 6px 18px;
            border-radius: 8px;
            text-align: center;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
            background: #F8FAFC;
            padding: 14px 18px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
        }
        .summary-item label {
            display: block;
            font-size: 11px;
            color: #64748B;
            font-weight: 700;
            margin-bottom: 3px;
        }
        .summary-item span {
            font-size: 15px;
            font-weight: 900;
            color: #0F172A;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 12px;
        }
        th {
            background: #0F5132;
            color: #fff;
            padding: 10px 12px;
            text-align: right;
            font-size: 11.5px;
            font-weight: 800;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #E2E8F0;
        }
        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px dashed #CBD5E1;
            text-align: center;
        }
        .signature-box {
            padding: 12px;
            border: 1px dashed #CBD5E1;
            border-radius: 8px;
            min-height: 90px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .actions-bar {
            max-width: 1050px;
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
            .report-box {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="actions-bar no-print">
        <button onclick="window.print()" style="background: #0F5132; color: #fff; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة كشف وسجل الأصول
        </button>
        <button onclick="window.close()" style="background: #E2E8F0; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
            إغلاق النافذة
        </button>
    </div>

    <div class="report-box">
        <!-- Header -->
        <div class="header">
            <div>
                <h1 class="company-title">مجموعة العزيزي الصناعية والتجارية</h1>
                <div style="font-size: 11.5px; color: #64748B;">
                    <span>سجل تجاري: 1010892341</span> | 
                    <span>الرقم الضريبي: 310293847500003</span> |
                    <span>تاريخ الإصدار: {{ now()->format('Y-m-d') }}</span>
                </div>
            </div>
            <div class="report-badge">
                <div>كشف وسجل الأصول الثابتة والإهلاك المعتمد</div>
                <div style="font-size: 11.5px; font-weight: 700; color: #D97706; margin-top: 4px;">
                    Fixed Assets Registry & Book Values
                </div>
            </div>
        </div>

        <!-- Summary KPI -->
        <div class="summary-grid">
            <div class="summary-item">
                <label>إجمالي عدد الأصول:</label>
                <span>{{ $consolidated['total_assets_count'] }} أصل</span>
            </div>
            <div class="summary-item">
                <label>التكلفة التاريخية الإجمالية:</label>
                <span style="color: #D97706;">{{ number_format($consolidated['total_gross_cost'], 2) }} ر.س</span>
            </div>
            <div class="summary-item">
                <label>مجمع الإهلاك المتراكم:</label>
                <span style="color: #DC2626;">{{ number_format($consolidated['total_accum_dep'], 2) }} ر.س</span>
            </div>
            <div class="summary-item">
                <label>صافي القيمة الدفترية الحالية:</label>
                <span style="color: #0F5132;">{{ number_format($consolidated['total_net_book_val'], 2) }} ر.س</span>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 14%;">رقم الأصل</th>
                    <th style="width: 25%;">اسم وبيان الأصل والمواصفات</th>
                    <th style="width: 16%;">التصنيف والفرع</th>
                    <th style="width: 10%;">تاريخ الشراء</th>
                    <th style="width: 10%;">التكلفة التاريخية</th>
                    <th style="width: 10%;">مجمع الإهلاك</th>
                    <th style="width: 10%;">صافي القيمة الدفترية</th>
                </tr>
            </thead>
            <tbody>
                @foreach($assets as $idx => $ast)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td style="font-family: monospace; font-weight: 800; color: #0F5132;">{{ $ast->asset_number }}</td>
                    <td style="font-weight: 800;">
                        {{ $ast->name }}
                        @if($ast->serial_number) <small style="display: block; color: #64748B; font-weight: 400; font-family: monospace;">S/N: {{ $ast->serial_number }}</small> @endif
                    </td>
                    <td>
                        <strong>{{ $ast->category?->name ?? '—' }}</strong>
                        <small style="display: block; color: #64748B;">{{ $ast->branch?->name ?? '—' }}</small>
                    </td>
                    <td>{{ $ast->purchase_date ? $ast->purchase_date->format('Y-m-d') : '—' }}</td>
                    <td style="font-weight: 700;">{{ number_format($ast->purchase_cost, 2) }}</td>
                    <td style="color: #DC2626;">{{ number_format($ast->total_depreciated_amount, 2) }}</td>
                    <td style="font-weight: 900; color: #0F5132;">{{ number_format($ast->current_book_value, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #F8FAFC; font-weight: 900;">
                    <td colspan="5" style="text-align: left; padding: 10px 12px;">الإجمالي العام المعتمد:</td>
                    <td style="padding: 10px 12px; color: #D97706;">{{ number_format($consolidated['total_gross_cost'], 2) }}</td>
                    <td style="padding: 10px 12px; color: #DC2626;">{{ number_format($consolidated['total_accum_dep'], 2) }}</td>
                    <td style="padding: 10px 12px; color: #0F5132; font-size: 13.5px;">{{ number_format($consolidated['total_net_book_val'], 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Signatures Box -->
        <div class="signatures-grid">
            <div class="signature-box">
                <span style="font-weight: 800; font-size: 12px; color: #0F5132;">مسؤول حسابات الأصول الثابتة</span>
                <span style="font-size: 11px; color: #64748B;">الاسم والتوقيع: ..........................</span>
            </div>

            <div class="signature-box">
                <span style="font-weight: 800; font-size: 12px; color: #D97706;">المراجع الداخلي المعتمد</span>
                <span style="font-size: 11px; color: #64748B;">الاسم والتوقيع: ..........................</span>
            </div>

            <div class="signature-box">
                <span style="font-weight: 800; font-size: 12px; color: #0F5132;">المدير المالي للمجموعة (CFO)</span>
                <span style="font-size: 11px; color: #64748B;">الاسم والتوقيع: ..........................</span>
            </div>
        </div>

        <div style="margin-top: 24px; text-align: center; font-size: 10.5px; color: #94A3B8;">
            طُبع من نظام العزيزي المتكامل لإدارة الموارد المؤسسية (El-Azizy ERP) - بتاريخ {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>

</body>
</html>
