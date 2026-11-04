<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة مستخلص - {{ $extract->extract_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #0F5132;
            --accent-gold: #D97706;
            --text-main: #0F172A;
            --border-color: #E2E8F0;
        }
        * {
            box-sizing: border-box;
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
        }
        body {
            background: #F8FAFC;
            color: var(--text-main);
            padding: 24px;
        }
        .certificate-sheet {
            max-width: 960px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 36px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }
        .header-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px double var(--primary);
            padding-bottom: 20px;
            margin-bottom: 24px;
        }
        .company-logo-text {
            font-size: 20px;
            font-weight: 900;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cert-title {
            text-align: center;
            background: #F1F5F9;
            border: 1px solid var(--border-color);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        .cert-title h1 {
            font-size: 18px;
            font-weight: 900;
            color: var(--primary);
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px 24px;
            background: #F8FAFC;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 13px;
        }
        .meta-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed var(--border-color);
            padding-bottom: 6px;
        }
        .meta-item span:first-child {
            color: #64748B;
            font-weight: 700;
        }
        .meta-item span:last-child {
            font-weight: 800;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 24px;
        }
        table th, table td {
            border: 1px solid var(--border-color);
            padding: 8px 10px;
            text-align: right;
        }
        table th {
            background: #F1F5F9;
            font-weight: 800;
        }
        .summary-box {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 28px;
        }
        .summary-table {
            width: 380px;
            border-collapse: collapse;
            font-size: 13px;
        }
        .summary-table td {
            padding: 6px 12px;
            border: 1px solid var(--border-color);
        }
        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 36px;
            border-top: 2px solid var(--border-color);
            padding-top: 20px;
            text-align: center;
        }
        .sig-block h5 {
            font-size: 12px;
            font-weight: 800;
            color: #475569;
            margin-bottom: 40px;
        }
        .sig-line {
            border-top: 1px dashed #94A3B8;
            padding-top: 4px;
            font-size: 11px;
            color: #64748B;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background: var(--primary);
            color: #FFFFFF;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
            }
            .certificate-sheet {
                box-shadow: none;
                border: none;
                padding: 15px;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print();">
            <i class="fa-solid fa-print"></i>
            طباعة شهادة المستخلص الرسمية
        </button>
    </div>

    <div class="certificate-sheet">
        <!-- Header -->
        <div class="header-box">
            <div class="company-logo-text">
                <i class="fa-solid fa-helmet-safety"></i>
                <div>
                    <div>شركة العزيزي للمقاولات العامة</div>
                    <div style="font-size: 11px; color: #64748B; font-weight: normal;">El-Azizy General Contracting & Engineering</div>
                </div>
            </div>
            <div style="text-align: left; font-size: 12px; color: #475569;">
                <div><strong>كود المستند:</strong> {{ $extract->extract_number }}</div>
                <div><strong>تاريخ الإصدار:</strong> {{ $extract->issue_date?->format('Y-m-d') }}</div>
                <div><strong>الحالة:</strong> {{ $extract->status_label }}</div>
            </div>
        </div>

        <!-- Certificate Title -->
        <div class="cert-title">
            <h1>شهادة صرف مستخلص أعمال مقاولة ({{ $extract->extract_type_label }})</h1>
            <div style="font-size: 12px; color: #64748B; margin-top: 4px;">
                عن الفترة من: {{ $extract->period_from?->format('Y-m-d') }} إلى: {{ $extract->period_to?->format('Y-m-d') }}
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div class="meta-item">
                <span>اسم المشروع:</span>
                <span>{{ $extract->project?->name }}</span>
            </div>
            <div class="meta-item">
                <span>كود المشروع:</span>
                <span style="font-family: monospace;">{{ $extract->project?->code }}</span>
            </div>
            <div class="meta-item">
                <span>الجهة المالكة (العميل):</span>
                <span>{{ $extract->project?->client?->name ?? '-' }}</span>
            </div>
            <div class="meta-item">
                <span>رقم وتاريخ العقد:</span>
                <span>{{ $extract->project?->contract_number ?? '-' }} ({{ $extract->project?->contract_date?->format('Y-m-d') ?? '-' }})</span>
            </div>
            <div class="meta-item">
                <span>المهندس الاستشاري:</span>
                <span>{{ $extract->consultant_engineer_name ?? 'الاستشاري المشرف' }}</span>
            </div>
            <div class="meta-item">
                <span>قيمة العقد المعدلة:</span>
                <span>{{ number_format($extract->project?->revised_value, 2) }} ر.س</span>
            </div>
        </div>

        <!-- Breakdown Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">م</th>
                    <th>كود البند</th>
                    <th>بيان الأعمال المنفذة</th>
                    <th style="width: 60px;">الوحدة</th>
                    <th style="width: 70px;">السعر</th>
                    <th style="width: 70px;">السابق</th>
                    <th style="width: 75px; background: #E0E7FF;">الحالي</th>
                    <th style="width: 75px;">التراكمي</th>
                    <th style="width: 85px;">قيمة الحالي</th>
                    <th style="width: 90px;">القيمة التراكمية</th>
                </tr>
            </thead>
            <tbody>
                @foreach($extract->items as $idx => $item)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="font-family: monospace; font-weight: 700;">{{ $item->boqItem?->item_code }}</td>
                        <td>{{ $item->item_description }}</td>
                        <td style="text-align: center;">{{ $item->unit }}</td>
                        <td>{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ number_format($item->previous_quantity, 2) }}</td>
                        <td style="background: #EEF2FF; font-weight: 800;">{{ number_format($item->current_quantity, 2) }}</td>
                        <td style="font-weight: 700;">{{ number_format($item->total_quantity, 2) }}</td>
                        <td style="font-weight: 800;">{{ number_format($item->current_amount, 2) }}</td>
                        <td>{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Deductions & Calculation Box -->
        <div class="summary-box">
            <table class="summary-table">
                <tr>
                    <td><strong>إجمالي أعمال الفترة الحالية:</strong></td>
                    <td style="font-weight: 800; text-align: left;">{{ number_format($extract->current_gross_amount, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td style="color: #DC2626;">خصم الدفعة المقدمة ({{ $extract->project?->advance_payment_percentage }}%):</td>
                    <td style="color: #DC2626; text-align: left;">- {{ number_format($extract->advance_payment_deduction, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td style="color: #DC2626;">خصم محتجز ضمان الأعمال ({{ $extract->project?->retention_percentage }}%):</td>
                    <td style="color: #DC2626; text-align: left;">- {{ number_format($extract->retention_deduction, 2) }} ر.س</td>
                </tr>
                @if($extract->other_deductions > 0)
                    <tr>
                        <td style="color: #DC2626;">استقطاعات وجزاءات أخرى:</td>
                        <td style="color: #DC2626; text-align: left;">- {{ number_format($extract->other_deductions, 2) }} ر.س</td>
                    </tr>
                @endif
                <tr style="background: #F8FAFC;">
                    <td><strong>الصافي قبل الضريبة:</strong></td>
                    <td style="font-weight: 800; text-align: left;">{{ number_format($extract->net_subtotal, 2) }} ر.س</td>
                </tr>
                <tr>
                    <td>ضريبة القيمة المضافة ({{ $extract->tax_rate }}%):</td>
                    <td style="text-align: left;">{{ number_format($extract->tax_amount, 2) }} ر.س</td>
                </tr>
                <tr style="background: #F1F5F9; border-top: 2px solid var(--primary);">
                    <td style="font-size: 14px; font-weight: 900; color: var(--primary);">صافي المبلغ المستحق للصرف:</td>
                    <td style="font-size: 15px; font-weight: 900; color: var(--primary); text-align: left;">{{ number_format($extract->net_amount, 2) }} ر.س</td>
                </tr>
            </table>
        </div>

        @if($extract->notes)
            <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 8px; padding: 12px; font-size: 12px; margin-bottom: 24px;">
                <strong>ملاحظات واشتراطات:</strong> {{ $extract->notes }}
            </div>
        @endif

        <!-- Signatures Grid -->
        <div class="signatures-grid">
            <div class="sig-block">
                <h5>مهندس الموقع (المقاول)</h5>
                <div class="sig-line">التوقيع والختم</div>
            </div>
            <div class="sig-block">
                <h5>المهندس الاستشاري المشرف</h5>
                <div class="sig-line">الاعتماد والتوقيع</div>
            </div>
            <div class="sig-block">
                <h5>ممثل الجهة المالكة</h5>
                <div class="sig-line">الموافقة والاعتماد</div>
            </div>
            <div class="sig-block">
                <h5>الإدارة المالية والصرف</h5>
                <div class="sig-line">إذن الصرف والتدقيق</div>
            </div>
        </div>
    </div>

</body>
</html>
