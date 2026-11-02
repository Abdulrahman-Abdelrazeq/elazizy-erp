<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير Z - وردية {{ $shift->shift_number }}</title>
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
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .company-name {
            font-size: 15px;
            font-weight: 900;
            margin: 0 0 2px 0;
        }
        .report-title {
            font-size: 13px;
            font-weight: 900;
            background: #000;
            color: #fff;
            padding: 3px 0;
            margin: 6px 0;
            letter-spacing: 0.5px;
        }
        .meta {
            font-size: 10.5px;
            line-height: 1.5;
            margin-bottom: 8px;
        }
        .meta-row {
            display: flex;
            justify-content: space-between;
        }
        .section-divider {
            border-bottom: 1px dashed #000;
            margin: 6px 0;
        }
        .double-divider {
            border-bottom: 2px solid #000;
            margin: 6px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            line-height: 1.6;
            font-size: 11px;
        }
        .row.bold {
            font-weight: 800;
        }
        .row.highlight {
            font-weight: 900;
            font-size: 12.5px;
            background: #eee;
            padding: 3px 2px;
        }
        .signatures {
            margin-top: 18px;
            font-size: 10px;
            display: flex;
            justify-content: space-between;
            padding-top: 6px;
        }
        .sig-box {
            text-align: center;
            width: 48%;
        }
        .sig-line {
            border-bottom: 1px dotted #000;
            margin-top: 25px;
        }
        .no-print-bar {
            background: #0F5132;
            color: #fff;
            padding: 8px;
            text-align: center;
            margin-bottom: 10px;
            border-radius: 6px;
            font-weight: 700;
        }
        .no-print-bar button {
            background: #fff;
            color: #0F5132;
            border: none;
            padding: 5px 14px;
            font-weight: 800;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 8px;
        }
        @media print {
            .no-print-bar {
                display: none;
            }
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

    <div class="no-print-bar">
        <span>تقرير Z لإغلاق الوردية والدرج</span>
        <button onclick="window.print()">طباعة الإيصال</button>
    </div>

    <div class="header">
        <h1 class="company-name">{{ getSetting('company_name', 'مؤسسة العزيزي للحلول المتكاملة') }}</h1>
        <div style="font-size: 10px;">{{ getSetting('company_address', 'الفرع الرئيسي') }}</div>
        <div class="report-title">تقرير إغلاق الوردية (Z-REPORT)</div>
        <div style="font-size: 10px; font-weight: 700;">نظام نقاط البيع والكاشير</div>
    </div>

    <div class="meta">
        <div class="meta-row">
            <span>رقم الوردية:</span>
            <strong>{{ $shift->shift_number }}</strong>
        </div>
        <div class="meta-row">
            <span>نقطة البيع:</span>
            <strong>{{ $shift->terminal->name ?? '-' }} ({{ $shift->terminal->code ?? '' }})</strong>
        </div>
        <div class="meta-row">
            <span>الكاشير:</span>
            <strong>{{ $shift->user->name ?? '-' }}</strong>
        </div>
        <div class="meta-row">
            <span>وقت الفتح:</span>
            <span>{{ $shift->started_at ? $shift->started_at->format('Y-m-d H:i') : '-' }}</span>
        </div>
        <div class="meta-row">
            <span>وقت الإغلاق:</span>
            <span>{{ $shift->ended_at ? $shift->ended_at->format('Y-m-d H:i') : 'مفتوحة حالياً' }}</span>
        </div>
        @if($shift->closedBy)
        <div class="meta-row">
            <span>أغلق بواسطة:</span>
            <span>{{ $shift->closedBy->name }}</span>
        </div>
        @endif
        <div class="meta-row">
            <span>تاريخ الطباعة:</span>
            <span>{{ now()->format('Y-m-d H:i:s') }}</span>
        </div>
    </div>

    <div class="section-divider"></div>

    <div style="font-weight: 800; font-size: 11px; margin-bottom: 4px;">تفاصيل المبيعات والمتحصلات:</div>

    <div class="row">
        <span>عهدة الافتتاح (رصيد البداية):</span>
        <strong>{{ formatMoney($shift->opening_balance) }}</strong>
    </div>
    <div class="row">
        <span>المبيعات النقدية (كاش):</span>
        <strong>{{ formatMoney($shift->cash_sales) }}</strong>
    </div>
    <div class="row">
        <span>مبيعات الشبكة / الفيزا:</span>
        <strong>{{ formatMoney($shift->card_sales) }}</strong>
    </div>
    <div class="row">
        <span>مبيعات الآجل (ذمم):</span>
        <strong>{{ formatMoney($shift->credit_sales) }}</strong>
    </div>
    <div class="row">
        <span>إجمالي المرتجعات:</span>
        <strong style="color: red;">- {{ formatMoney($shift->total_returns) }}</strong>
    </div>

    <div class="section-divider"></div>

    <div class="row highlight">
        <span>إجمالي مبيعات الوردية:</span>
        <span>{{ formatMoney($shift->cash_sales + $shift->card_sales + $shift->credit_sales) }}</span>
    </div>

    <div class="row bold" style="margin-top: 4px;">
        <span>عدد الفواتير الصادرة:</span>
        <span>{{ $shift->invoices()->count() }} فاتورة</span>
    </div>

    <div class="double-divider"></div>

    <div style="font-weight: 800; font-size: 11px; margin-bottom: 4px;">جرد الدرج ومطابقة النقدية:</div>

    <div class="row">
        <span>المتوقع بالدرج (عهدة + كاش - مرتجع):</span>
        <strong>{{ formatMoney($shift->expected_cash) }}</strong>
    </div>

    @if($shift->isClosed())
        <div class="row">
            <span>الفعلي المحصى بالدرج:</span>
            <strong>{{ formatMoney($shift->actual_cash) }}</strong>
        </div>

        @php $diff = (float) $shift->cash_difference; @endphp
        <div class="row highlight" style="margin-top: 4px;">
            <span>فارق الدرج (Variance):</span>
            @if(abs($diff) < 0.01)
                <span>متطابق (0.00 ج.م)</span>
            @elseif($diff < 0)
                <span style="color: red;">عجز: {{ formatMoney(abs($diff)) }}</span>
            @else
                <span>زيادة: +{{ formatMoney($diff) }}</span>
            @endif
        </div>
    @else
        <div class="row" style="font-style: italic; color: #555;">
            <span>حالة الدرج:</span>
            <span>الوردية قيد التداول (لم تغلق بعد)</span>
        </div>
    @endif

    @if($shift->start_notes || $shift->end_notes)
        <div class="section-divider"></div>
        <div style="font-size: 9.5px; line-height: 1.4;">
            @if($shift->start_notes)
                <div><strong>ملاحظات الاستلام:</strong> {{ $shift->start_notes }}</div>
            @endif
            @if($shift->end_notes)
                <div><strong>ملاحظات التسليم:</strong> {{ $shift->end_notes }}</div>
            @endif
        </div>
    @endif

    <div class="signatures">
        <div class="sig-box">
            <div>توقيع الكاشير</div>
            <div class="sig-line"></div>
        </div>
        <div class="sig-box">
            <div>توقيع المشرف</div>
            <div class="sig-line"></div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 14px; font-size: 9px; color: #555;">
        --- نهاية تقرير الوردية Z ---
    </div>

</body>
</html>
