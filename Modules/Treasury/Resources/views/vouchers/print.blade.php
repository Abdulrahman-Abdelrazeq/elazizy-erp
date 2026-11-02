<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $voucher->type === 'receipt' ? 'سند قبض' : 'سند صرف' }} - {{ $voucher->voucher_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
        }
        body {
            margin: 0;
            padding: 24px;
            background: #fff;
            color: #111;
            font-size: 13.5px;
        }
        .voucher-box {
            border: 2px solid #0F5132;
            border-radius: 12px;
            padding: 24px;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
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
            font-size: 18px;
            font-weight: 900;
            color: #0F5132;
            margin: 0 0 4px 0;
        }
        .voucher-badge {
            font-size: 18px;
            font-weight: 900;
            color: {{ $voucher->type === 'receipt' ? '#0F5132' : '#DC2626' }};
            border: 2px solid {{ $voucher->type === 'receipt' ? '#0F5132' : '#DC2626' }};
            padding: 6px 18px;
            border-radius: 8px;
            text-align: center;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
            font-size: 13.5px;
        }
        .meta-row {
            display: flex;
            gap: 8px;
            line-height: 1.6;
        }
        .meta-label {
            font-weight: 700;
            color: #555;
            min-width: 110px;
        }
        .meta-val {
            font-weight: 800;
            color: #111;
        }
        .amount-box {
            background: #F8FAFC;
            border: 1.5px solid #CBD5E1;
            border-radius: 8px;
            padding: 12px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .amount-num {
            font-size: 22px;
            font-weight: 900;
            color: {{ $voucher->type === 'receipt' ? '#0F5132' : '#DC2626' }};
        }
        .statement-box {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 30px;
            line-height: 1.8;
            font-weight: 700;
            min-height: 70px;
        }
        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            text-align: center;
            margin-top: 40px;
        }
        .sig-col span {
            font-size: 12px;
            font-weight: 700;
            color: #555;
            display: block;
            margin-bottom: 35px;
        }
        .sig-col div {
            border-bottom: 1px dotted #555;
            width: 75%;
            margin: 0 auto;
        }
        .no-print-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .no-print-bar button {
            background: #0F5132;
            color: #fff;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            font-weight: 800;
            border-radius: 8px;
            cursor: pointer;
        }
        @media print {
            .no-print-bar {
                display: none;
            }
            body {
                padding: 0;
            }
            .voucher-box {
                border: 1px solid #000;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print-bar">
        <button onclick="window.print()">طباعة السند الرسمي</button>
    </div>

    <div class="voucher-box">
        <div class="header">
            <div>
                <h1 class="company-title">{{ getSetting('company_name', 'مؤسسة العزيزي للحلول المتكاملة') }}</h1>
                <div style="font-size: 11.5px; color: #555;">{{ getSetting('company_address', 'الفرع الرئيسي') }}</div>
                <div style="font-size: 11.5px; color: #555;">الرقم الضريبي: <strong>{{ getSetting('company_tax_number', '123-456-789') }}</strong></div>
            </div>

            <div class="voucher-badge">
                {{ $voucher->type === 'receipt' ? 'سند قبض / تحصيل' : 'سند صرف / دفع' }}
            </div>

            <div style="text-align: left; font-size: 12px; line-height: 1.5;">
                <div>رقم السند: <strong style="font-family: monospace; font-size: 14px; color: #0F5132;">{{ $voucher->voucher_number }}</strong></div>
                <div>التاريخ: <strong>{{ $voucher->date->format('Y-m-d') }}</strong></div>
            </div>
        </div>

        <div class="amount-box">
            <div>
                <span style="font-size: 12px; color: #555; font-weight: 700; display: block;">المبلغ المسجل:</span>
                <span class="amount-num">{{ formatMoney($voucher->amount) }}</span>
            </div>
            <div>
                <span style="font-size: 12px; color: #555; font-weight: 700; display: block;">طريقة الدفع:</span>
                <strong style="font-size: 14px;">{{ $voucher->payment_method_label }}</strong>
                @if($voucher->reference_number)
                    <span style="display: block; font-size: 11px; color: #555;">(مرجع/شيك: {{ $voucher->reference_number }})</span>
                @endif
            </div>
            <div>
                <span style="font-size: 12px; color: #555; font-weight: 700; display: block;">الخزينة / الحساب:</span>
                <strong style="font-size: 14px;">{{ $voucher->treasury->name ?? '-' }}</strong>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-row">
                <span class="meta-label">{{ $voucher->type === 'receipt' ? 'استلمنا من السيد/ة:' : 'صرفنا إلى السيد/ة:' }}</span>
                <span class="meta-val">{{ $voucher->client->name ?? ($voucher->received_from_or_paid_to ?: '—') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">البند والتصنيف:</span>
                <span class="meta-val">{{ $voucher->category->name ?? 'مصروفات/إيرادات عامة' }}</span>
            </div>
        </div>

        <div style="font-weight: 800; margin-bottom: 6px; font-size: 13px;">وذلك عن (البيان):</div>
        <div class="statement-box">
            {{ $voucher->notes }}
        </div>

        <div class="signatures">
            <div class="sig-col">
                <span>توقيع أمين الخزينة / الصندوق</span>
                <div></div>
            </div>
            <div class="sig-col">
                <span>توقيع المحاسب المالي</span>
                <div></div>
            </div>
            <div class="sig-col">
                <span>{{ $voucher->type === 'receipt' ? 'توقيع المسلّم' : 'توقيع المستلم' }}</span>
                <div></div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px; font-size: 11px; color: #777;">
            حرر بواسطة: {{ $voucher->creator->name ?? 'النظام' }} | تاريخ الطباعة: {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>

</body>
</html>

