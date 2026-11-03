<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الدخل والأرباح والخسائر - {{ $fromDate }} إلى {{ $toDate }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Cairo', sans-serif; }
        body { margin: 0; padding: 24px; background: #fff; color: #111; font-size: 13px; }
        .statement-box { border: 2px solid #0F5132; border-radius: 12px; padding: 28px; max-width: 900px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0F5132; padding-bottom: 16px; margin-bottom: 20px; }
        .company-title { font-size: 20px; font-weight: 900; color: #0F5132; margin: 0 0 4px 0; }
        .statement-title { font-size: 17px; font-weight: 900; color: #0F5132; border: 2px solid #0F5132; background: rgba(15, 81, 50, 0.05); padding: 6px 20px; border-radius: 8px; text-align: center; }
        .meta-box { background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px 16px; display: flex; justify-content: space-between; font-weight: 700; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #CBD5E1; padding: 9px 14px; }
        .signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px dashed #CBD5E1; }
        .sig-box { display: flex; flex-direction: column; gap: 30px; }
        .sig-title { font-weight: 800; color: #475569; }
        .sig-line { border-bottom: 1px dotted #94A3B8; margin: 0 10px; }
        .no-print { max-width: 900px; margin: 0 auto 16px auto; display: flex; justify-content: space-between; align-items: center; }
        @media print {
            body { padding: 0; }
            .statement-box { border: 2px solid #000; max-width: 100%; border-radius: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('reports.income_statement', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" style="text-decoration: none; color: #0F5132; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i> العودة للقائمة
        </a>
        <button onclick="window.print()" style="background: #0F5132; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة الآن (Print)
        </button>
    </div>

    <div class="statement-box">
        <div class="header">
            <div>
                <h1 class="company-title">{{ currentTenant() ? currentTenant()->company_name : 'شركة العزيزي للمقاولات والتوريدات' }}</h1>
                <div style="font-size: 12px; color: #64748B; font-weight: 700;">
                    القوائم المالية الختامية المعتمدة (معيار IAS 1)
                </div>
            </div>
            <div>
                <div class="statement-title">
                    قائمة الدخل والأرباح والخسائر
                </div>
            </div>
        </div>

        <div class="meta-box">
            <span>عن الفترة المالية من: {{ $fromDate }}</span>
            <span>إلى تاريخ: {{ $toDate }}</span>
            <span>العملة: {{ currentTenant() ? currentTenant()->currency_code : 'SAR' }}</span>
        </div>

        <table>
            <thead>
                <tr style="background: #F1F5F9; font-weight: 800;">
                    <th style="width: 120px; text-align: right;">كود الحساب</th>
                    <th style="text-align: right;">البيان / الحساب المالي</th>
                    <th style="width: 160px; text-align: right;">المبلغ الجزئي</th>
                    <th style="width: 160px; text-align: right;">المبلغ الكلي</th>
                </tr>
            </thead>
            <tbody>
                <!-- Revenues -->
                <tr style="background: #F8FAFC; font-weight: 800;">
                    <td colspan="4" style="color: #0F5132;">أولاً: الإيرادات التشغيلية</td>
                </tr>
                @foreach($data['revenue_lines'] as $rev)
                <tr>
                    <td style="font-family: monospace;">{{ $rev['account']->code }}</td>
                    <td>{{ $rev['account']->name }}</td>
                    <td style="text-align: right;">{{ formatMoney($rev['amount']) }}</td>
                    <td></td>
                </tr>
                @endforeach
                <tr style="font-weight: 800; background: #F1F5F9;">
                    <td colspan="3">إجمالي الإيرادات:</td>
                    <td style="text-align: right; color: #0F5132;">{{ formatMoney($data['total_revenues']) }}</td>
                </tr>

                <!-- COGS -->
                <tr style="background: #F8FAFC; font-weight: 800;">
                    <td colspan="4" style="color: #DC2626;">يخصم: تكلفة البضاعة المباعة (COGS)</td>
                </tr>
                @foreach($data['cogs_lines'] as $cogs)
                <tr>
                    <td style="font-family: monospace;">{{ $cogs['account']->code }}</td>
                    <td>{{ $cogs['account']->name }}</td>
                    <td style="text-align: right; color: #DC2626;">({{ formatMoney($cogs['amount']) }})</td>
                    <td></td>
                </tr>
                @endforeach
                <tr style="font-weight: 800; background: #FEE2E2;">
                    <td colspan="3" style="color: #991B1B;">إجمالي تكلفة المبيعات:</td>
                    <td style="text-align: right; color: #DC2626;">({{ formatMoney($data['total_cogs']) }})</td>
                </tr>

                <!-- GROSS PROFIT -->
                <tr style="background: #D1E7DD; font-weight: 900; font-size: 14px;">
                    <td colspan="3" style="color: #0F5132;">مجمل الربح التجاري (Gross Profit):</td>
                    <td style="text-align: right; color: #0F5132;">{{ formatMoney($data['gross_profit']) }}</td>
                </tr>

                <!-- Operating Expenses -->
                <tr style="background: #F8FAFC; font-weight: 800;">
                    <td colspan="4" style="color: #D97706;">يخصم: المصروفات التشغيلية والإدارية</td>
                </tr>
                @foreach($data['operating_expense_lines'] as $exp)
                <tr>
                    <td style="font-family: monospace;">{{ $exp['account']->code }}</td>
                    <td>{{ $exp['account']->name }}</td>
                    <td style="text-align: right; color: #D97706;">({{ formatMoney($exp['amount']) }})</td>
                    <td></td>
                </tr>
                @endforeach
                <tr style="font-weight: 800; background: #FEF3C7;">
                    <td colspan="3" style="color: #92400E;">إجمالي المصروفات التشغيلية:</td>
                    <td style="text-align: right; color: #D97706;">({{ formatMoney($data['total_operating_expenses']) }})</td>
                </tr>

                <!-- Net Profit -->
                <tr style="background: #0F5132; color: #fff; font-weight: 900; font-size: 15px;">
                    <td colspan="3">صافي الربح / (الخسارة) للفترة:</td>
                    <td style="text-align: right;">{{ formatMoney($data['net_profit']) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig-box">
                <span class="sig-title">رئيس الحسابات</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">المدير المالي</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">المدير التنفيذي / الاعتماد</span>
                <span class="sig-line"></span>
            </div>
        </div>
    </div>

</body>
</html>

