<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أمر تشغيل وتصنيع معتمد - {{ $order->order_number }}</title>
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
        .order-box {
            border: 2px solid #0F5132;
            border-radius: 12px;
            padding: 28px;
            max-width: 950px;
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
        .order-badge {
            font-size: 16px;
            font-weight: 900;
            color: #0F5132;
            border: 2px solid #0F5132;
            background: rgba(15, 81, 50, 0.05);
            padding: 6px 18px;
            border-radius: 8px;
            text-align: center;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
            background: #F8FAFC;
            padding: 14px 18px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
        }
        .meta-row {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .meta-label {
            font-weight: 700;
            color: #64748B;
            font-size: 11.5px;
        }
        .meta-val {
            font-weight: 800;
            color: #0F172A;
        }
        .product-banner {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 8px;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .lines-table th, .lines-table td {
            border: 1px solid #CBD5E1;
            padding: 8px 10px;
        }
        .lines-table th {
            background: #F1F5F9;
            color: #0F172A;
            font-weight: 800;
            font-size: 12px;
        }
        .lines-table tfoot td {
            background: #E2E8F0;
            font-weight: 900;
        }
        .signatures {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            text-align: center;
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px dashed #CBD5E1;
        }
        .sig-box {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        .sig-title {
            font-weight: 800;
            color: #475569;
        }
        .sig-line {
            border-bottom: 1px dotted #94A3B8;
            margin: 0 10px;
        }
        .no-print {
            max-width: 950px;
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
            .order-box {
                border: 2px solid #000;
                box-shadow: none;
                max-width: 100%;
                border-radius: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('manufacturing.orders.show', $order->id) }}" style="text-decoration: none; color: #0F5132; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i> العودة لشاشة الأمر
        </a>
        <button onclick="window.print()" style="background: #0F5132; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة أمر التشغيل (Print)
        </button>
    </div>

    <div class="order-box">
        <div class="header">
            <div>
                <h1 class="company-title">{{ activeTenant() ? activeTenant()->company_name : 'شركة العزيزي للأنظمة والحلول المتكاملة' }}</h1>
                <div style="font-size: 12px; color: #64748B; font-weight: 700;">
                    إدارة الإنتاج والتشغيل الصناعي | {{ $order->branch?->name ?? 'المركز الرئيسي' }}
                </div>
            </div>
            <div>
                <div class="order-badge">
                    أمر تشغيل وتصنيع معتمد (Work Order)
                </div>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-row">
                <span class="meta-label">رقم أمر التشغيل:</span>
                <span class="meta-val" style="font-family: monospace; color: #0F5132;">{{ $order->order_number }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">قائمة المواد (BOM):</span>
                <span class="meta-val">{{ $order->bom?->bom_number }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">تاريخ الإصدار:</span>
                <span class="meta-val">{{ $order->order_date ? $order->order_date->format('Y-m-d') : '-' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">موعد الإنجاز المتوقع:</span>
                <span class="meta-val">{{ $order->expected_completion_date ? $order->expected_completion_date->format('Y-m-d') : '-' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">مخزن صرف الخامات:</span>
                <span class="meta-val">{{ $order->fromStore?->name }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">مخزن توريد المنتج التام:</span>
                <span class="meta-val" style="color: #0F5132;">{{ $order->toStore?->name }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">حالة الأمر:</span>
                <span class="meta-val">{{ $order->status_label }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">مسؤول التخطيط:</span>
                <span class="meta-val">{{ $order->creator?->name ?? 'النظام' }}</span>
            </div>
        </div>

        <div class="product-banner">
            <div>
                <span style="font-size: 11.5px; color: #166534; font-weight: 700; display: block;">المنتج المطلوب تصنيعه:</span>
                <strong style="font-size: 16px; color: #0F5132;">{{ $order->product?->name }}</strong>
                <span style="font-size: 12px; font-family: monospace; color: #475569; margin-right: 8px;">(SKU: {{ $order->product?->sku ?: '-' }})</span>
            </div>
            <div style="font-size: 16px; font-weight: 900; color: #0F5132;">
                الكمية المطلوبة: {{ $order->planned_quantity }} {{ $order->product?->unit?->name ?? 'وحدة' }}
            </div>
        </div>

        <h4 style="font-size: 14px; font-weight: 800; margin: 0 0 10px 0; color: #0F5132;">1. المواد الخام المقررة والمطلوب صرفها:</h4>
        <table class="lines-table">
            <thead>
                <tr>
                    <th style="width: 35px; text-align: center;">#</th>
                    <th style="width: 100px; text-align: right;">كود الصنف</th>
                    <th style="text-align: right;">اسم المادة الخام</th>
                    <th style="width: 130px; text-align: right;">الكمية المقررة</th>
                    <th style="width: 130px; text-align: right;">الكمية المصروفة فعلياً</th>
                    <th style="width: 150px; text-align: center;">توقيع أمين المخزن</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $idx => $it)
                <tr>
                    <td style="text-align: center; color: #64748B;">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace; font-weight: 800; color: #0F5132;">{{ $it->item?->sku ?: '-' }}</td>
                    <td style="font-weight: 800;">{{ $it->item?->name }}</td>
                    <td style="font-weight: 800;">{{ $it->planned_quantity }} {{ $it->item?->unit?->name }}</td>
                    <td style="font-weight: 800; color: #0F5132;">{{ $order->status === 'completed' ? $it->actual_quantity . ' ' . $it->item?->unit?->name : '..........' }}</td>
                    <td style="text-align: center;"></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($order->bom && $order->bom->operations->count() > 0)
        <h4 style="font-size: 14px; font-weight: 800; margin: 18px 0 10px 0; color: #D97706;">2. مراحل وخطوط التشغيل الصناعي:</h4>
        <table class="lines-table">
            <thead>
                <tr>
                    <th style="width: 45px; text-align: center;">م</th>
                    <th style="text-align: right;">اسم المرحلة / العملية</th>
                    <th style="width: 180px; text-align: right;">محطة العمل</th>
                    <th style="width: 110px; text-align: right;">الوقت المعياري</th>
                    <th style="width: 140px; text-align: center;">توقيع الفني المنفذ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->bom->operations as $op)
                <tr>
                    <td style="text-align: center; font-weight: 800;">{{ $op->sequence_order }}</td>
                    <td style="font-weight: 800;">{{ $op->operation_name }}</td>
                    <td>{{ $op->workstation?->name ?? 'تشغيل يدوي' }}</td>
                    <td>{{ $op->standard_time_minutes }} دقيقة</td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="signatures">
            <div class="sig-box">
                <span class="sig-title">إعداد: مسؤول التخطيط</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">صرف: أمين مستودع الخام</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">تنفيذ: مشرف خط الإنتاج</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">استلام: مراقب الجودة والتوريد</span>
                <span class="sig-line"></span>
            </div>
        </div>
    </div>

</body>
</html>
