<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوليصة وسند تحويل بضاعة بين الفروع - {{ $transfer->transfer_number }}</title>
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
        .manifest-box {
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
        .manifest-badge {
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
        .meta-item label {
            display: block;
            font-size: 11px;
            color: #64748B;
            font-weight: 700;
            margin-bottom: 3px;
        }
        .meta-item span {
            font-size: 13.5px;
            font-weight: 800;
            color: #0F172A;
        }
        .route-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        .route-card {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 14px 18px;
            background: #FAFAFA;
        }
        .route-title {
            font-size: 13px;
            font-weight: 900;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th {
            background: #0F5132;
            color: #fff;
            padding: 10px 12px;
            text-align: right;
            font-size: 12px;
            font-weight: 800;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #E2E8F0;
            font-size: 12.5px;
        }
        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 36px;
            padding-top: 20px;
            border-top: 1px dashed #CBD5E1;
            text-align: center;
        }
        .signature-box {
            padding: 12px;
            border: 1px dashed #CBD5E1;
            border-radius: 8px;
            min-height: 90px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .actions-bar {
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
            .manifest-box {
                border: none;
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="actions-bar no-print">
        <button onclick="window.print()" style="background: #0F5132; color: #fff; border: none; padding: 10px 22px; border-radius: 8px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة البوليصة والسند
        </button>
        <button onclick="window.close()" style="background: #E2E8F0; border: none; padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
            إغلاق النافذة
        </button>
    </div>

    <div class="manifest-box">
        <!-- Header -->
        <div class="header">
            <div>
                <h1 class="company-title">مجموعة العزبزي الصناعية والتجارية</h1>
                <div style="font-size: 11.5px; color: #64748B;">
                    <span>سجل تجاري: 1010892341</span> | 
                    <span>الرقم الضريبي: 310293847500003</span>
                </div>
            </div>
            <div class="manifest-badge">
                <div>سند تحويل بضاعة بين الفروع ومراكز التوزيع</div>
                <div style="font-size: 13px; font-weight: 700; direction: ltr; margin-top: 4px; color: #D97706;">
                    {{ $transfer->transfer_number }}
                </div>
            </div>
        </div>

        <!-- Meta Details -->
        <div class="meta-grid">
            <div class="meta-item">
                <label>تاريخ السند:</label>
                <span>{{ $transfer->transfer_date ? \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') : '—' }}</span>
            </div>
            <div class="meta-item">
                <label>حالة الشحنة:</label>
                <span style="color: #0F5132;">
                    @if($transfer->status === 'draft') مسودة
                    @elseif($transfer->status === 'dispatched') في الطريق (In-Transit)
                    @elseif($transfer->status === 'received') تم الاستلام والتوريد
                    @elseif($transfer->status === 'cancelled') ملغي
                    @endif
                </span>
            </div>
            <div class="meta-item">
                <label>الناقل / شركة الشحن:</label>
                <span>{{ $transfer->shipping_company ?: 'النقل الداخلي للشركة' }}</span>
            </div>
            <div class="meta-item">
                <label>رقم البوليصة / المركبة:</label>
                <span>{{ $transfer->tracking_number ?: ($transfer->vehicle_plate ?: '—') }}</span>
            </div>
        </div>

        <!-- Route Details -->
        <div class="route-grid">
            <div class="route-card" style="border-right: 3px solid #0F5132;">
                <div class="route-title" style="color: #0F5132;">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i>
                    الجهة المصدر (الشاحنة):
                </div>
                <div><strong>الفرع:</strong> {{ $transfer->fromBranch?->name ?? '—' }}</div>
                <div><strong>المستودع:</strong> {{ $transfer->fromStore?->name ?? '—' }}</div>
                <div><strong>مسؤول الشحن:</strong> {{ $transfer->dispatcher?->name ?? ($transfer->creator?->name ?? '—') }}</div>
            </div>

            <div class="route-card" style="border-right: 3px solid #D97706;">
                <div class="route-title" style="color: #D97706;">
                    <i class="fa-solid fa-arrow-down-to-bracket"></i>
                    الجهة الوجهة (المستلمة):
                </div>
                <div><strong>الفرع:</strong> {{ $transfer->toBranch?->name ?? '—' }}</div>
                <div><strong>المستودع:</strong> {{ $transfer->toStore?->name ?? '—' }}</div>
                <div><strong>مسؤول الاستلام:</strong> {{ $transfer->receiver?->name ?? 'قيد الشحن والتسليم' }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 15%;">كود الصنف</th>
                    <th style="width: 40%;">بيان الصنف والمواصفات</th>
                    <th style="width: 10%;">الوحدة</th>
                    <th style="width: 15%; text-align: center;">الكمية المشحونة</th>
                    <th style="width: 15%; text-align: center;">الكمية المستلمة فعلياً</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfer->items as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-family: monospace; font-weight: 700;">{{ $row->item?->code ?? '—' }}</td>
                    <td style="font-weight: 800;">{{ $row->item?->name ?? 'صنف محذوف' }}</td>
                    <td>{{ $row->item?->unit?->name ?? 'قطعة' }}</td>
                    <td style="text-align: center; font-weight: 800; font-size: 13px;">{{ number_format($row->sent_quantity, 2) }}</td>
                    <td style="text-align: center; font-weight: 800; font-size: 13px;">
                        @if($transfer->status === 'received')
                            {{ number_format($row->received_quantity, 2) }}
                        @else
                            [ ............ ]
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background: #F8FAFC; font-weight: 900;">
                    <td colspan="4" style="text-align: left; padding: 10px 12px;">إجمالي عدد الأصناف والكميات:</td>
                    <td style="text-align: center; color: #0F5132; font-size: 14px;">{{ number_format($transfer->items->sum('sent_quantity'), 2) }}</td>
                    <td style="text-align: center; font-size: 14px;">
                        {{ $transfer->status === 'received' ? number_format($transfer->items->sum('received_quantity'), 2) : '—' }}
                    </td>
                </tr>
            </tfoot>
        </table>

        @if($transfer->notes)
        <div style="background: #F8FAFC; padding: 10px 14px; border-radius: 6px; font-size: 12px; margin-bottom: 20px; border: 1px solid #E2E8F0;">
            <strong>ملاحظات وتعليمات النقل:</strong> {{ $transfer->notes }}
        </div>
        @endif

        <!-- Signatures Handover & Acceptance -->
        <div class="signatures-grid">
            <div class="signature-box">
                <span style="font-weight: 800; font-size: 12px; color: #0F5132;">أمين مستودع المصدر (الشاحن)</span>
                <span style="font-size: 11px; color: #64748B;">الاسم والتوقيع: ..........................</span>
            </div>

            <div class="signature-box">
                <span style="font-weight: 800; font-size: 12px; color: #D97706;">السائق / مندوب الشحن والتسليم</span>
                <span style="font-size: 11px; color: #64748B;">الاسم والتوقيع: {{ $transfer->driver_name ?: '..........................' }}</span>
            </div>

            <div class="signature-box">
                <span style="font-weight: 800; font-size: 12px; color: #0F5132;">أمين مستودع الوجهة (المستلم)</span>
                <span style="font-size: 11px; color: #64748B;">الاسم والتوقيع: ..........................</span>
            </div>
        </div>

        <div style="margin-top: 24px; text-align: center; font-size: 10.5px; color: #94A3B8;">
            طُبع من نظام العزيزي المتكامل لإدارة الموارد المؤسسية (El-Azizy ERP) - بتاريخ {{ now()->format('Y-m-d H:i') }}
        </div>
    </div>

</body>
</html>
