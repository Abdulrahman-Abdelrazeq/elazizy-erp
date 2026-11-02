<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>طباعة ملصق الباركود - {{ $item->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
        }
        body {
            background: #F1F5F9;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .no-print {
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
        }
        .btn-print {
            background: #0F5132;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 800;
            cursor: pointer;
            font-size: 14px;
        }
        .labels-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .barcode-sticker {
            width: 38mm;
            height: 25mm;
            background: #fff;
            border: 1px dashed #94A3B8;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .sticker-company {
            font-size: 8px;
            font-weight: 800;
            color: #0F172A;
            line-height: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sticker-title {
            font-size: 8.5px;
            font-weight: 800;
            color: #0F5132;
            line-height: 1.1;
            max-height: 18px;
            overflow: hidden;
        }
        .sticker-barcode-lines {
            height: 22px;
            background: repeating-linear-gradient(90deg, #000 0px, #000 1.5px, transparent 1.5px, transparent 3px, #000 3px, #000 5px, transparent 5px, transparent 7px, #000 7px, #000 8px, transparent 8px, transparent 10px);
            width: 90%;
            margin: 1px auto;
        }
        .sticker-barcode-num {
            font-family: monospace;
            font-size: 8px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .sticker-price {
            font-size: 9.5px;
            font-weight: 900;
            color: #DC2626;
            line-height: 1;
        }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .barcode-sticker {
                border: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">
            🖨️ طباعة الملصقات الحرارية الآن
        </button>
        <button onclick="window.close()" style="background: #E2E8F0; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; cursor: pointer;">
            إغلاق
        </button>
    </div>

    <!-- Repeating sample stickers (e.g. 10 labels) -->
    <div class="labels-grid">
        @for($i = 0; $i < 12; $i++)
            <div class="barcode-sticker">
                <div class="sticker-company">{{ currentTenant()->company_name ?? 'شركة العزيزي' }}</div>
                <div class="sticker-title">{{ $item->name }}</div>
                <div>
                    <div class="sticker-barcode-lines"></div>
                    <div class="sticker-barcode-num">{{ $item->barcode }}</div>
                </div>
                <div class="sticker-price">السعر: {{ formatMoney($item->sell_price) }}</div>
            </div>
        @endfor
    </div>

</body>
</html>

