<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إيصال كاشير - {{ $invoice->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Cairo', monospace, sans-serif;
        }
        body {
            margin: 0;
            padding: 10px;
            background: #fff;
            color: #000;
            width: 80mm;
            font-size: 11px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 15px;
            font-weight: 900;
            margin: 0 0 2px 0;
        }
        .meta {
            font-size: 10.5px;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10.5px;
            margin-bottom: 8px;
        }
        th {
            border-bottom: 1px solid #000;
            padding: 4px 0;
            text-align: right;
        }
        td {
            padding: 4px 0;
            border-bottom: 0.5px dashed #ccc;
        }
        .totals {
            border-top: 1px solid #000;
            padding-top: 6px;
            font-size: 11.5px;
            line-height: 1.6;
        }
        .totals .row {
            display: flex;
            justify-content: space-between;
        }
        .grand-total {
            font-size: 14px;
            font-weight: 900;
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            padding: 4px 0;
            margin: 4px 0;
        }
        .footer {
            text-align: center;
            margin-top: 14px;
            border-top: 1px dashed #000;
            padding-top: 8px;
            font-size: 10px;
        }
        @media print {
            body {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1 class="company-name">{{ getSetting('company_name', 'مؤسسة العزيزي للحلول المتكاملة') }}</h1>
        <div class="meta">
            <div>{{ getSetting('company_address', 'الفرع الرئيسي') }}</div>
            <div>الرقم الضريبي: <strong>{{ getSetting('company_tax_number', '123-456-789') }}</strong></div>
            <div style="font-weight: 800; margin-top: 4px;">فاتورة ضريبية مبسطة (POS)</div>
        </div>
    </div>

    <div class="meta" style="margin-bottom: 8px;">
        <div>رقم الفاتورة: <strong>{{ $invoice->invoice_number }}</strong></div>
        <div>التاريخ والوقت: <strong>{{ $invoice->created_at->format('Y-m-d H:i') }}</strong></div>
        <div>الكاشير: <strong>{{ $invoice->creator->name ?? 'الكاشير' }}</strong></div>
        <div>العميل: <strong>{{ $invoice->client ? $invoice->client->name : 'عميل نقدي' }}</strong></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50%;">الصنف</th>
                <th style="width: 15%; text-align: center;">الكمية</th>
                <th style="width: 15%; text-align: left;">السعر</th>
                <th style="width: 20%; text-align: left;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->item->name ?? '-' }}</td>
                    <td style="text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                    <td style="text-align: left;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: left; font-weight: 800;">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>المجموع قبل الخصم:</span>
            <strong>{{ number_format($invoice->subtotal, 2) }}</strong>
        </div>

        @if($invoice->discount_amount > 0)
            <div class="row" style="color: #000;">
                <span>الخصم:</span>
                <strong>-{{ number_format($invoice->discount_amount, 2) }}</strong>
            </div>
        @endif

        @if($invoice->tax_amount > 0)
            <div class="row">
                <span>ضريبة القيمة المضافة ({{ $invoice->tax_rate }}%):</span>
                <strong>+{{ number_format($invoice->tax_amount, 2) }}</strong>
            </div>
        @endif

        <div class="row grand-total">
            <span>الصافي الإجمالي:</span>
            <span>{{ number_format($invoice->grand_total, 2) }} {{ currentTenant()->currency_code ?? 'SAR' }}</span>
        </div>

        <div class="row">
            <span>طريقة الدفع:</span>
            <span>{{ $invoice->payment_type_label }}</span>
        </div>

        <div class="row">
            <span>المبلغ المدفوع:</span>
            <strong>{{ number_format($invoice->paid_amount, 2) }}</strong>
        </div>

        @if($invoice->remaining_amount > 0)
            <div class="row">
                <span>المتبقي على الحساب:</span>
                <strong>{{ number_format($invoice->remaining_amount, 2) }}</strong>
            </div>
        @endif
    </div>

    <div class="footer">
        <div style="font-weight: 800; margin-bottom: 4px;">شكراً لتسوقكم معنا!</div>
        <div>الأسعار شاملة ضريبة القيمة المضافة</div>
        <div>يرجى الاحتفاظ بالإيصال للاستبدال والاسترجاع خلال 14 يوماً</div>
    </div>

</body>
</html>
