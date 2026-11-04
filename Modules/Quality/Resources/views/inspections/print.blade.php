<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>شهادة فحص جودة - {{ $inspection->inspection_number }}</title>
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
        .cert-sheet {
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
        .company-logo {
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
        .decision-box {
            background: #F8FAFC;
            border: 2px solid var(--primary);
            border-radius: 8px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
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
            body { background: #FFFFFF; padding: 0; }
            .cert-sheet { box-shadow: none; border: none; padding: 15px; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button class="btn-print" onclick="window.print();">
            <i class="fa-solid fa-print"></i>
            طباعة شهادة الفحص والاعتماد
        </button>
    </div>

    <div class="cert-sheet">
        <!-- Header -->
        <div class="header-box">
            <div class="company-logo">
                <i class="fa-solid fa-clipboard-check"></i>
                <div>
                    <div>شركة العزيزي للتجارة والمقاولات والصناعة</div>
                    <div style="font-size: 11px; color: #64748B; font-weight: normal;">Quality Assurance & Inspection Department</div>
                </div>
            </div>
            <div style="text-align: left; font-size: 12px; color: #475569;">
                <div><strong>رقم الشهادة:</strong> {{ $inspection->inspection_number }}</div>
                <div><strong>تاريخ الفحص:</strong> {{ $inspection->inspection_date?->format('Y-m-d') }}</div>
                <div><strong>الفرع:</strong> {{ $inspection->branch?->name }}</div>
            </div>
        </div>

        <!-- Title -->
        <div class="cert-title">
            <h1>شهادة فحص واعتماد جودة (Quality Inspection & Release Certificate)</h1>
            <div style="font-size: 12px; color: #64748B; margin-top: 4px;">
                مرحلة الفحص: {{ $inspection->type_label }}
            </div>
        </div>

        <!-- Meta Grid -->
        <div class="meta-grid">
            <div class="meta-item">
                <span>الصنف / المادة المفحوصة:</span>
                <span>{{ $inspection->item?->name ?? 'مواد موقع المشروع' }}</span>
            </div>
            <div class="meta-item">
                <span>رقم التشغيلة / الدفعة (Batch):</span>
                <span style="font-family: monospace;">{{ $inspection->batch_number ?? '-' }}</span>
            </div>
            <div class="meta-item">
                <span>المورد / جهة التوريد:</span>
                <span>{{ $inspection->supplier?->name ?? ($inspection->project?->name ?? 'تصنيع داخلي') }}</span>
            </div>
            <div class="meta-item">
                <span>مهندس / مفتش الجودة:</span>
                <span>{{ $inspection->inspector?->name ?? '-' }}</span>
            </div>
            <div class="meta-item">
                <span>إجمالي كمية الدفعة:</span>
                <span>{{ number_format($inspection->total_quantity, 2) }}</span>
            </div>
            <div class="meta-item">
                <span>حجم العينة المسحوبة (AQL):</span>
                <span>{{ number_format($inspection->sample_size, 2) }}</span>
            </div>
        </div>

        <!-- Checkpoints Results Table -->
        <table>
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">م</th>
                    <th>نقطة الفحص والاختبار</th>
                    <th>المواصفة والحدود المقبولة</th>
                    <th style="width: 140px;">القيمة المرصودة / القراءة</th>
                    <th style="width: 100px; text-align: center;">النتيجة</th>
                    <th>ملاحظات العيوب</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspection->results as $idx => $res)
                    <tr>
                        <td style="text-align: center;">{{ $idx + 1 }}</td>
                        <td style="font-weight: 700;">{{ $res->checkpoint_name }}</td>
                        <td style="color: var(--primary);">{{ $res->standard_criteria ?? '-' }}</td>
                        <td style="font-family: monospace; font-weight: 700;">{{ $res->observed_value ?? '-' }}</td>
                        <td style="text-align: center; font-weight: 800; color: {{ $res->is_passed ? '#059669' : '#DC2626' }};">
                            {{ $res->is_passed ? 'مطابق ✓' : 'مخالف ✗' }}
                        </td>
                        <td>{{ $res->defect_notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 15px;">فحص عام للدفعة والمظهر الخارجي.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Final Decision Box -->
        <div class="decision-box">
            <div>
                <div style="font-size: 12px; color: #64748B; font-weight: 700;">قرار الاعتماد النهائي:</div>
                <div style="font-size: 18px; font-weight: 900; color: {{ $inspection->status === 'passed' ? '#059669' : ($inspection->status === 'rejected' ? '#DC2626' : '#D97706') }};">
                    {{ $inspection->status_label }}
                </div>
                @if($inspection->notes)
                    <div style="font-size: 12px; color: #475569; margin-top: 4px;">{{ $inspection->notes }}</div>
                @endif
            </div>

            <div style="text-align: left; font-size: 13px;">
                <div>الكمية المقبولة والمفرج عنها: <strong style="color: #059669; font-size: 15px;">{{ number_format($inspection->passed_quantity, 2) }}</strong></div>
                @if($inspection->failed_quantity > 0)
                    <div style="color: #DC2626;">الكمية المرفوضة والمعيبة: <strong>{{ number_format($inspection->failed_quantity, 2) }}</strong></div>
                @endif
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures-grid">
            <div class="sig-block">
                <h5>مفتش / مهندس الجودة</h5>
                <div class="sig-line">التوقيع والتاريخ</div>
            </div>
            <div class="sig-block">
                <h5>مدير توكيد الجودة QA</h5>
                <div class="sig-line">الاعتماد والختم</div>
            </div>
            <div class="sig-block">
                <h5>أمين المستودع / الموقع</h5>
                <div class="sig-line">استلام الدفعة المعتمدة</div>
            </div>
            <div class="sig-block">
                <h5>الإدارة الفنية والإنتاج</h5>
                <div class="sig-line">التصديق النهائي</div>
            </div>
        </div>
    </div>

</body>
</html>
