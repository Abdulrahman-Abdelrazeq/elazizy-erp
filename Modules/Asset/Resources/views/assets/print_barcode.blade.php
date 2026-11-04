<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملصق باركود الأصل - {{ $asset->asset_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&family=Libre+Barcode+128&display=swap" rel="stylesheet">
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
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .actions-bar {
            width: 380px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .asset-sticker {
            width: 380px;
            background: #fff;
            border: 2px solid #0F5132;
            border-radius: 10px;
            padding: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .company-header {
            font-size: 13px;
            font-weight: 900;
            color: #0F5132;
            border-bottom: 1.5px solid #0F5132;
            padding-bottom: 6px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .asset-title {
            font-size: 15px;
            font-weight: 900;
            color: #111;
            margin: 6px 0;
            line-height: 1.3;
        }
        .asset-meta {
            font-size: 11px;
            color: #475569;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            text-align: right;
            margin-bottom: 10px;
            background: #F8FAFC;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #E2E8F0;
        }
        .barcode-visual {
            font-family: 'Libre Barcode 128', cursive, monospace;
            font-size: 48px;
            line-height: 1;
            margin: 4px 0 2px 0;
            letter-spacing: 2px;
            color: #000;
        }
        .barcode-text {
            font-family: monospace;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 2px;
            color: #0F5132;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .actions-bar {
                display: none !important;
            }
            .asset-sticker {
                box-shadow: none;
                border: 1.5px solid #000;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

    <div class="actions-bar">
        <button onclick="window.print()" style="background: #0F5132; color: #fff; border: none; padding: 8px 18px; border-radius: 8px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-print"></i> طباعة الملصق
        </button>
        <button onclick="window.close()" style="background: #E2E8F0; border: none; padding: 8px 14px; border-radius: 8px; font-weight: 700; font-size: 12px; cursor: pointer;">
            إغلاق
        </button>
    </div>

    <div class="asset-sticker">
        <div class="company-header">
            <span>مجموعة العزيزي - بطاقة عهدة وأصل</span>
            <span style="font-size: 10.5px; color: #D97706; font-weight: 800;">PROPERTY OF ELAZIZY</span>
        </div>

        <div class="asset-title">
            {{ $asset->name }}
        </div>

        <div class="asset-meta">
            <div><strong>الفرع:</strong> {{ $asset->branch?->name }}</div>
            <div><strong>التصنيف:</strong> {{ $asset->category?->name }}</div>
            <div><strong>الموقع:</strong> {{ $asset->location?->name ?: 'الرئيسي' }}</div>
            <div><strong>العهدة:</strong> {{ $asset->custodian?->name ?: 'عهدة عامة' }}</div>
            @if($asset->serial_number)
                <div style="grid-column: span 2;"><strong>S/N:</strong> {{ $asset->serial_number }}</div>
            @endif
        </div>

        <!-- Visual Barcode -->
        <div class="barcode-visual">
            *{{ $asset->barcode ?: $asset->asset_number }}*
        </div>
        <div class="barcode-text">
            {{ $asset->barcode ?: $asset->asset_number }}
        </div>

        <div style="font-size: 9.5px; color: #94A3B8; margin-top: 6px; border-top: 1px dashed #CBD5E1; padding-top: 4px;">
            لا يجوز نقل أو استبعاد هذا الأصل دون إذن محاسبي مسبق من الإدارة المالية
        </div>
    </div>

</body>
</html>
