<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة مبيعات ضريبية - {{ $sale->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        body {
            background-color: #f1f5f9;
            color: #0f172a;
            margin: 0;
            padding: 20px;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #0F5132;
            padding-bottom: 16px;
            margin-bottom: 20px;
        }
        .company-title {
            font-size: 22px;
            font-weight: 900;
            color: #0F5132;
            margin: 0 0 4px 0;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 20px;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }
        th {
            background: #0F5132;
            color: #ffffff;
            padding: 10px 12px;
            text-align: right;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .totals-table {
            width: 320px;
            margin-right: auto;
            border-collapse: collapse;
            font-size: 13px;
        }
        .totals-table td {
            padding: 6px 10px;
            border-bottom: 1px dashed #cbd5e1;
        }
        .totals-table tr.grand td {
            font-size: 16px;
            font-weight: 900;
            color: #0F5132;
            border-top: 2px solid #0F5132;
            border-bottom: 2px solid #0F5132;
        }
        .signatures {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            font-size: 12.5px;
            text-align: center;
        }
        .print-btn-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #0F5132;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-size: 14px;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .invoice-box {
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .print-btn-bar {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <button onclick="window.print()" class="btn-print">🖨️ طباعة الفاتورة الآن</button>
    </div>

    <div class="invoice-box">
        
        <!-- Header -->
        <div class="header">
            <div>
                <h1 class="company-title">{{ getSetting('company_name', 'مؤسسة العزيزي للحلول المتكاملة') }}</h1>
                <div style="font-size: 12.5px; color: #64748b; line-height: 1.5;">
                    <div>{{ getSetting('company_address', 'القاهرة - جمهورية مصر العربية') }}</div>
                    <div>هاتف: {{ getSetting('company_phone', '01000000000') }}</div>
                    <div>الرقم الضريبي: <strong>{{ getSetting('company_tax_number', '123-456-789') }}</strong></div>
                </div>
            </div>

            <div style="text-align: left;">
                <div style="background: #D1E7DD; color: #0F5132; padding: 6px 14px; border-radius: 6px; font-weight: 900; font-size: 14px; display: inline-block; margin-bottom: 8px;">
                    فاتورة ضريبية معتمدة
                </div>
                <div style="font-size: 12px; color: #64748b;">رقم: <strong>{{ $sale->invoice_number }}</strong></div>
                <div style="font-size: 12px; color: #64748b;">التاريخ: <strong>{{ $sale->date->format('Y-m-d') }}</strong></div>
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div>
                <strong style="color: #0F5132; display: block; margin-bottom: 4px;">بيانات العميل:</strong>
                <div>الاسم: <strong>{{ $sale->client ? $sale->client->name : 'عميل نقدي عام' }}</strong></div>
                @if($sale->client && $sale->client->mobile)
                    <div>الهاتف: {{ $sale->client->mobile }}</div>
                @endif
                @if($sale->client && $sale->client->tax_number)
                    <div>الرقم الضريبي: {{ $sale->client->tax_number }}</div>
                @endif
            </div>

            <div>
                <strong style="color: #0F5132; display: block; margin-bottom: 4px;">بيانات العملية:</strong>
                <div>طريقة الدفع: <strong>{{ $sale->payment_type_label }}</strong></div>
                <div>المستودع: {{ $sale->store->name ?? '-' }}</div>
                <div>المسؤول: {{ $sale->creator->name ?? 'النظام' }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">م</th>
                    <th style="width: 45%;">الصنف / البيان</th>
                    <th style="width: 10%;">الوحدة</th>
                    <th style="width: 10%; text-align: center;">الكمية</th>
                    <th style="width: 15%; text-align: left;">سعر الوحدة</th>
                    <th style="width: 15%; text-align: left;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->item->name ?? '-' }}</strong>
                            @if($item->item && $item->item->code)
                                <small style="color: #64748b; display: block;">كود: {{ $item->item->code }}</small>
                            @endif
                        </td>
                        <td>{{ $item->unit ? $item->unit->name : 'قطعة' }}</td>
                        <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                        <td style="text-align: left;">{{ formatMoney($item->unit_price) }}</td>
                        <td style="text-align: left; font-weight: 800;">{{ formatMoney($item->total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div style="display: flex; justify-content: flex-end;">
            <table class="totals-table">
                <tr>
                    <td>المجموع قبل الخصم:</td>
                    <td style="text-align: left; font-weight: 700;">{{ formatMoney($sale->subtotal) }}</td>
                </tr>
                @if($sale->discount_amount > 0)
                    <tr style="color: #dc2626;">
                        <td>الخصم الإضافي:</td>
                        <td style="text-align: left; font-weight: 700;">-{{ formatMoney($sale->discount_amount) }}</td>
                    </tr>
                @endif
                @if($sale->tax_amount > 0)
                    <tr style="color: #2563eb;">
                        <td>ضريبة القيمة المضافة ({{ $sale->tax_rate }}%):</td>
                        <td style="text-align: left; font-weight: 700;">+{{ formatMoney($sale->tax_amount) }}</td>
                    </tr>
                @endif
                @if($sale->shipping_cost > 0)
                    <tr>
                        <td>تكلفة الشحن والتوصيل:</td>
                        <td style="text-align: left; font-weight: 700;">+{{ formatMoney($sale->shipping_cost) }}</td>
                    </tr>
                @endif
                <tr class="grand">
                    <td>الصافي الإجمالي:</td>
                    <td style="text-align: left;">{{ formatMoney($sale->grand_total) }}</td>
                </tr>
                <tr>
                    <td style="color: #16a34a;">المبلغ المسدد:</td>
                    <td style="text-align: left; font-weight: 700; color: #16a34a;">{{ formatMoney($sale->paid_amount) }}</td>
                </tr>
                @if($sale->remaining_amount > 0)
                    <tr style="color: #dc2626;">
                        <td>المتبقي الآجل:</td>
                        <td style="text-align: left; font-weight: 800;">{{ formatMoney($sale->remaining_amount) }}</td>
                    </tr>
                @endif
            </table>
        </div>

        @if($sale->notes)
            <div style="margin-top: 16px; font-size: 12px; color: #64748b; background: #f8fafc; padding: 10px; border-radius: 6px;">
                <strong>ملاحظات:</strong> {{ $sale->notes }}
            </div>
        @endif

        <!-- Signatures -->
        <div class="signatures">
            <div style="width: 200px;">
                <p style="margin-bottom: 40px; font-weight: 700;">توقيع البائع / المحاسب</p>
                <div style="border-top: 1px solid #94a3b8;"></div>
            </div>
            <div style="width: 200px;">
                <p style="margin-bottom: 40px; font-weight: 700;">ختم المؤسسة</p>
                <div style="border-top: 1px solid #94a3b8;"></div>
            </div>
            <div style="width: 200px;">
                <p style="margin-bottom: 40px; font-weight: 700;">استلام العميل</p>
                <div style="border-top: 1px solid #94a3b8;"></div>
            </div>
        </div>

    </div>

</body>
</html>
