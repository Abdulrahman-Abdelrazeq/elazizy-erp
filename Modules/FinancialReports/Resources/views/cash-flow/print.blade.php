<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة التدفقات النقدية - {{ $fromDate }} إلى {{ $toDate }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Cairo', sans-serif; }
        body { margin: 0; padding: 24px; background: #fff; color: #111; font-size: 13px; }
        .statement-box { border: 2px solid #0F5132; border-radius: 12px; padding: 28px; max-width: 900px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0F5132; padding-bottom: 16px; margin-bottom: 20px; }
        .company-title { font-size: 20px; font-weight: 900; color: #0F5132; margin: 0 0 4px 0; }
        .statement-title { font-size: 17px; font-weight: 900; color: #D97706; border: 2px solid #D97706; background: rgba(217, 119, 6, 0.05); padding: 6px 20px; border-radius: 8px; text-align: center; }
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
        <a href="{{ route('reports.cash_flow', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" style="text-decoration: none; color: #0F5132; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i> العودة للقائمة
        </a>
        <button onclick="window.print()" style="background: #D97706; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة الآن (Print)
        </button>
    </div>

    <div class="statement-box">
        <div class="header">
            <div>
                <h1 class="company-title">{{ currentTenant() ? currentTenant()->company_name : 'شركة العزيزي للمقاولات والتوريدات' }}</h1>
                <div style="font-size: 12px; color: #64748B; font-weight: 700;">
                    قائمة التدفقات النقدية المعيارية (IAS 7 Cash Flow Statement)
                </div>
            </div>
            <div>
                <div class="statement-title">
                    قائمة التدفقات النقدية
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
                    <th style="text-align: right;">البيان التفصيلي لحركة النقدية</th>
                    <th style="width: 180px; text-align: right;">المبلغ المحقق</th>
                </tr>
            </thead>
            <tbody>
                <!-- 1. Operating -->
                <tr style="background: #F8FAFC; font-weight: 800; color: #0F5132;">
                    <td colspan="2">1. التدفقات النقدية من الأنشطة التشغيلية (Operating)</td>
                </tr>
                <tr>
                    <td style="padding-right: 28px;">متحصلات نقدية من العملاء والمبيعات (+)</td>
                    <td style="text-align: right; color: #0F5132; font-weight: 700;">{{ formatMoney($data['operating_inflow']) }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 28px;">مدفوعات نقدية للموردين والمصروفات (-)</td>
                    <td style="text-align: right; color: #DC2626; font-weight: 700;">({{ formatMoney($data['operating_outflow']) }})</td>
                </tr>
                <tr style="background: #D1E7DD; font-weight: 800;">
                    <td>صافي التدفق النقدي من الأنشطة التشغيلية:</td>
                    <td style="text-align: right; color: #0F5132;">{{ formatMoney($data['net_operating_cash']) }}</td>
                </tr>

                <!-- 2. Investing -->
                <tr style="background: #F8FAFC; font-weight: 800; color: #4338CA;">
                    <td colspan="2">2. التدفقات النقدية من الأنشطة الاستثمارية (Investing)</td>
                </tr>
                <tr>
                    <td style="padding-right: 28px;">مدفوعات شراء أصول ثابتة وتجهيزات (-)</td>
                    <td style="text-align: right; color: #4338CA; font-weight: 700;">({{ formatMoney($data['investing_outflow']) }})</td>
                </tr>
                <tr style="background: #E0E7FF; font-weight: 800;">
                    <td>صافي التدفق النقدي من الأنشطة الاستثمارية:</td>
                    <td style="text-align: right; color: #3730A3;">{{ formatMoney($data['net_investing_cash']) }}</td>
                </tr>

                <!-- 3. Financing -->
                <tr style="background: #F8FAFC; font-weight: 800; color: #D97706;">
                    <td colspan="2">3. التدفقات النقدية من الأنشطة التمويلية (Financing)</td>
                </tr>
                <tr>
                    <td style="padding-right: 28px;">متحصلات رأس المال والتمويل (+)</td>
                    <td style="text-align: right; color: #D97706; font-weight: 700;">{{ formatMoney($data['financing_inflow']) }}</td>
                </tr>
                <tr style="background: #FEF3C7; font-weight: 800;">
                    <td>صافي التدفق النقدي من الأنشطة التمويلية:</td>
                    <td style="text-align: right; color: #92400E;">{{ formatMoney($data['net_financing_cash']) }}</td>
                </tr>

                <!-- Totals -->
                <tr style="background: #F1F5F9; font-weight: 900;">
                    <td>صافي التغير في النقدية خلال الفترة:</td>
                    <td style="text-align: right;">{{ formatMoney($data['net_change_in_cash']) }}</td>
                </tr>
                <tr>
                    <td style="color: #64748B;">يضاف: رصيد النقدية في بداية الفترة:</td>
                    <td style="text-align: right; color: #64748B;">{{ formatMoney($data['opening_cash']) }}</td>
                </tr>
                <tr style="background: #0F5132; color: #fff; font-weight: 900; font-size: 15px;">
                    <td>رصيد النقدية في نهاية الفترة:</td>
                    <td style="text-align: right;">{{ formatMoney($data['closing_cash']) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig-box">
                <span class="sig-title">أمين الخزينة</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">المدير المالي</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">المحاسب القانوني المعتمد</span>
                <span class="sig-line"></span>
            </div>
        </div>
    </div>

</body>
</html>

