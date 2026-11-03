<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سند قيد يومية - {{ $entry->entry_number }}</title>
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
        .voucher-box {
            border: 2px solid #0F5132;
            border-radius: 12px;
            padding: 28px;
            max-width: 900px;
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
        .voucher-badge {
            font-size: 17px;
            font-weight: 900;
            color: #0F5132;
            border: 2px solid #0F5132;
            background: rgba(15, 81, 50, 0.05);
            padding: 6px 20px;
            border-radius: 8px;
            text-align: center;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 20px;
            background: #F8FAFC;
            padding: 14px 18px;
            border-radius: 8px;
            border: 1px solid #E2E8F0;
        }
        .meta-row {
            display: flex;
            gap: 8px;
            line-height: 1.6;
        }
        .meta-label {
            font-weight: 700;
            color: #64748B;
            min-width: 90px;
        }
        .meta-val {
            font-weight: 800;
            color: #0F172A;
        }
        .statement-box {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            line-height: 1.6;
            font-weight: 700;
            background: #F8FAFC;
        }
        .lines-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .lines-table th, .lines-table td {
            border: 1px solid #CBD5E1;
            padding: 10px 12px;
        }
        .lines-table th {
            background: #F1F5F9;
            color: #0F172A;
            font-weight: 800;
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
            margin-top: 40px;
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
            max-width: 900px;
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
            .voucher-box {
                border: 2px solid #000;
                box-shadow: none;
                max-width: 100%;
                border-radius: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('accounting.journal-entries.show', $entry->id) }}" style="text-decoration: none; color: #0F5132; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i> العودة لشاشة القيد
        </a>
        <button onclick="window.print()" style="background: #0F5132; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة الآن (Print)
        </button>
    </div>

    <div class="voucher-box">
        <div class="header">
            <div>
                <h1 class="company-title">{{ activeTenant() ? activeTenant()->company_name : 'شركة العزيزي للمقاولات والتوريدات' }}</h1>
                <div style="font-size: 12px; color: #64748B; font-weight: 700;">
                    النظام المحاسبي العام الموحد | {{ $entry->branch?->name ?? 'المركز الرئيسي' }}
                </div>
            </div>
            <div>
                <div class="voucher-badge">
                    سند قيد يومية (Journal Voucher)
                </div>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-row">
                <span class="meta-label">رقم القيد:</span>
                <span class="meta-val" style="font-family: monospace; font-size: 14px; color: #0F5132;">{{ $entry->entry_number }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">التاريخ:</span>
                <span class="meta-val">{{ \Carbon\Carbon::parse($entry->date)->format('Y-m-d') }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">المستند المرجعي:</span>
                <span class="meta-val">{{ $entry->reference ?: '-' }}</span>
            </div>
            <div class="meta-row">
                <span class="meta-label">حالة القيد:</span>
                <span class="meta-val" style="color: {{ $entry->status === 'posted' ? '#0F5132' : '#D97706' }};">
                    {{ $entry->status === 'posted' ? 'معتمد ومرحل' : 'مسودة' }}
                </span>
            </div>
            <div class="meta-row">
                <span class="meta-label">مصدر القيد:</span>
                <span class="meta-val">
                    {{ $entry->source_type === 'manual' ? 'قيد يدوي عام' : ($entry->source_type === 'opening' ? 'قيد افتتاحي' : $entry->source_type) }}
                </span>
            </div>
            <div class="meta-row">
                <span class="meta-label">المستخدم:</span>
                <span class="meta-val">{{ $entry->creator?->name ?? 'النظام' }}</span>
            </div>
        </div>

        <div class="statement-box">
            <span style="color: #64748B; font-size: 11px; display: block; margin-bottom: 2px;">البيان العام للقيد:</span>
            {{ $entry->description }}
        </div>

        <table class="lines-table">
            <thead>
                <tr>
                    <th style="width: 40px; text-align: center;">#</th>
                    <th style="width: 120px; text-align: right;">كود الحساب</th>
                    <th style="text-align: right;">اسم الحساب المالي</th>
                    <th style="width: 140px; text-align: right;">مركز التكلفة</th>
                    <th style="text-align: right;">البيان التفصيلي للسطر</th>
                    <th style="width: 120px; text-align: right;">مدين (Debit)</th>
                    <th style="width: 120px; text-align: right;">دائن (Credit)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entry->lines as $idx => $line)
                <tr>
                    <td style="text-align: center; font-weight: 700; color: #64748B;">{{ $idx + 1 }}</td>
                    <td style="font-family: monospace; font-weight: 800; color: #0F5132;">{{ $line->account?->code ?? '-' }}</td>
                    <td style="font-weight: 800;">{{ $line->account?->name ?? 'حساب غير محدد' }}</td>
                    <td style="color: #475569; font-size: 12px;">{{ $line->costCenter?->name ?? '-' }}</td>
                    <td style="font-size: 12px; color: #334155;">{{ $line->description ?: $entry->description }}</td>
                    <td style="text-align: right; font-weight: 800; color: {{ $line->debit > 0 ? '#0F5132' : '#94A3B8' }};">
                        {{ $line->debit > 0 ? formatMoney($line->debit) : '-' }}
                    </td>
                    <td style="text-align: right; font-weight: 800; color: {{ $line->credit > 0 ? '#4338CA' : '#94A3B8' }};">
                        {{ $line->credit > 0 ? formatMoney($line->credit) : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: left; padding: 12px; font-size: 14px;">
                        الإجمالي المتوازن (المجموع):
                    </td>
                    <td style="text-align: right; color: #0F5132; font-size: 14px;">
                        {{ formatMoney($entry->total_debit) }}
                    </td>
                    <td style="text-align: right; color: #4338CA; font-size: 14px;">
                        {{ formatMoney($entry->total_credit) }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <div class="signatures">
            <div class="sig-box">
                <span class="sig-title">المحاسب (المنشئ)</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">المراجع الداخلي</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">المدير المالي</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">الاعتماد العام</span>
                <span class="sig-line"></span>
            </div>
        </div>
    </div>

</body>
</html>
