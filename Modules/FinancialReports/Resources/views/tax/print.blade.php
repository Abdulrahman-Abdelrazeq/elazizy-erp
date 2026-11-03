<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نموذج الإقرار الضريبي لضريبة القيمة المضافة - {{ $fromDate }} إلى {{ $toDate }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Cairo', sans-serif; }
        body { margin: 0; padding: 24px; background: #fff; color: #111; font-size: 13px; }
        .tax-box { border: 2px solid #0F5132; border-radius: 12px; padding: 28px; max-width: 900px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0F5132; padding-bottom: 16px; margin-bottom: 20px; }
        .company-title { font-size: 20px; font-weight: 900; color: #0F5132; margin: 0 0 4px 0; }
        .tax-badge { font-size: 17px; font-weight: 900; color: #DC2626; border: 2px solid #DC2626; background: rgba(220, 38, 38, 0.05); padding: 6px 20px; border-radius: 8px; text-align: center; }
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
            .tax-box { border: 2px solid #000; max-width: 100%; border-radius: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('reports.tax', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" style="text-decoration: none; color: #0F5132; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i> العودة لشاشة الضريبة
        </a>
        <button onclick="window.print()" style="background: #DC2626; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة الإقرار الضريبي
        </button>
    </div>

    <div class="tax-box">
        <div class="header">
            <div>
                <h1 class="company-title">{{ currentTenant() ? currentTenant()->company_name : 'شركة العزيزي للمقاولات والتوريدات' }}</h1>
                <div style="font-size: 12px; color: #64748B; font-weight: 700;">
                    الرقم الضريبي (VAT ID): {{ currentTenant() ? currentTenant()->tax_number : '300123456789003' }}
                </div>
            </div>
            <div>
                <div class="tax-badge">
                    إقرار ضريبة القيمة المضافة
                </div>
            </div>
        </div>

        <div class="meta-box">
            <span>عن الفترة الضريبية من: {{ $fromDate }}</span>
            <span>إلى تاريخ: {{ $toDate }}</span>
            <span>العملة: {{ currentTenant() ? currentTenant()->currency_code : 'SAR' }}</span>
        </div>

        <table>
            <thead>
                <tr style="background: #F1F5F9; font-weight: 800;">
                    <th style="text-align: right;">البند الضريبي</th>
                    <th style="width: 160px; text-align: right;">المبلغ الأساسي (الخاضع)</th>
                    <th style="width: 160px; text-align: right;">مبلغ الضريبة</th>
                </tr>
            </thead>
            <tbody>
                <!-- Sales -->
                <tr style="background: #F8FAFC; font-weight: 800; color: #0F5132;">
                    <td colspan="3">1. مبيعات الفترة والضريبة المحصلة (المخرجات)</td>
                </tr>
                <tr>
                    <td style="padding-right: 24px;">المبيعات الخاضعة للنسبة الأساسية</td>
                    <td style="text-align: right;">{{ formatMoney($data['sales_taxable']) }}</td>
                    <td style="text-align: right; color: #0F5132; font-weight: 700;">{{ formatMoney($data['output_vat']) }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 24px;">يخصم: مردودات المبيعات</td>
                    <td style="text-align: right;">{{ formatMoney($data['sales_return_taxable']) }}</td>
                    <td style="text-align: right; color: #DC2626;">({{ formatMoney($data['sales_return_vat']) }})</td>
                </tr>
                <tr style="background: #D1E7DD; font-weight: 900;">
                    <td colspan="2">صافي ضريبة المخرجات:</td>
                    <td style="text-align: right; color: #0F5132;">{{ formatMoney($data['net_output_vat']) }}</td>
                </tr>

                <!-- Purchases -->
                <tr style="background: #F8FAFC; font-weight: 800; color: #4338CA;">
                    <td colspan="3">2. مشتريات الفترة والضريبة المدفوعة (المدخلات القابلة للخصم)</td>
                </tr>
                <tr>
                    <td style="padding-right: 24px;">المشتريات الخاضعة للنسبة الأساسية</td>
                    <td style="text-align: right;">{{ formatMoney($data['purchases_taxable']) }}</td>
                    <td style="text-align: right; color: #4338CA; font-weight: 700;">{{ formatMoney($data['input_vat']) }}</td>
                </tr>
                <tr>
                    <td style="padding-right: 24px;">يخصم: مردودات المشتريات</td>
                    <td style="text-align: right;">{{ formatMoney($data['purchase_return_taxable']) }}</td>
                    <td style="text-align: right; color: #DC2626;">({{ formatMoney($data['purchase_return_vat']) }})</td>
                </tr>
                <tr style="background: #E0E7FF; font-weight: 900;">
                    <td colspan="2">صافي ضريبة المدخلات:</td>
                    <td style="text-align: right; color: #3730A3;">{{ formatMoney($data['net_input_vat']) }}</td>
                </tr>

                <!-- Final Net Payable -->
                <tr style="background: {{ $data['is_payable'] ? '#DC2626' : '#0F5132' }}; color: #fff; font-weight: 900; font-size: 15px;">
                    <td colspan="2">
                        {{ $data['is_payable'] ? 'صافي الضريبة المستحقة للسداد للهيئة / المصلحة:' : 'صافي رصيد الضريبة الدائن القابل للاسترداد:' }}
                    </td>
                    <td style="text-align: right; font-size: 17px;">
                        {{ formatMoney(abs($data['net_vat_payable'])) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="signatures">
            <div class="sig-box">
                <span class="sig-title">المحاسب الضريبي المعتمد</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">المدير المالي</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">إقرار المفوض بالتوقيع</span>
                <span class="sig-line"></span>
            </div>
        </div>
    </div>

</body>
</html>

