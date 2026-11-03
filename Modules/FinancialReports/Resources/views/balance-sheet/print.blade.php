<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة المركز المالي والميزانية العمومية - كما في {{ $asOfDate }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: 'Cairo', sans-serif; }
        body { margin: 0; padding: 24px; background: #fff; color: #111; font-size: 13px; }
        .sheet-box { border: 2px solid #0F5132; border-radius: 12px; padding: 28px; max-width: 960px; margin: 0 auto; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #0F5132; padding-bottom: 16px; margin-bottom: 20px; }
        .company-title { font-size: 20px; font-weight: 900; color: #0F5132; margin: 0 0 4px 0; }
        .sheet-badge { font-size: 17px; font-weight: 900; color: #4338CA; border: 2px solid #4338CA; background: rgba(67, 56, 202, 0.05); padding: 6px 20px; border-radius: 8px; text-align: center; }
        .meta-box { background: #F8FAFC; border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px 16px; display: flex; justify-content: space-between; font-weight: 700; margin-bottom: 20px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #CBD5E1; padding: 8px 10px; font-size: 12.5px; }
        .table-title { background: #F1F5F9; font-weight: 900; color: #0F172A; }
        .signatures { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px dashed #CBD5E1; }
        .sig-box { display: flex; flex-direction: column; gap: 30px; }
        .sig-title { font-weight: 800; color: #475569; }
        .sig-line { border-bottom: 1px dotted #94A3B8; margin: 0 10px; }
        .no-print { max-width: 960px; margin: 0 auto 16px auto; display: flex; justify-content: space-between; align-items: center; }
        @media print {
            body { padding: 0; }
            .sheet-box { border: 2px solid #000; max-width: 100%; border-radius: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="{{ route('reports.balance_sheet', ['as_of_date' => $asOfDate]) }}" style="text-decoration: none; color: #0F5132; font-weight: 800; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i> العودة لشاشة الميزانية
        </a>
        <button onclick="window.print()" style="background: #4338CA; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-print"></i> طباعة الآن (Print)
        </button>
    </div>

    <div class="sheet-box">
        <div class="header">
            <div>
                <h1 class="company-title">{{ currentTenant() ? currentTenant()->company_name : 'شركة العزيزي للمقاولات والتوريدات' }}</h1>
                <div style="font-size: 12px; color: #64748B; font-weight: 700;">
                    قائمة المركز المالي والميزانية الختامية (IAS 1 Financial Position)
                </div>
            </div>
            <div>
                <div class="sheet-badge">
                    الميزانية العمومية
                </div>
            </div>
        </div>

        <div class="meta-box">
            <span>كما في تاريخ: {{ $asOfDate }}</span>
            <span>حالة التوازن: {{ $data['is_balanced'] ? 'متوازنة نظامياً' : 'غير متوازنة' }}</span>
            <span>العملة: {{ currentTenant() ? currentTenant()->currency_code : 'SAR' }}</span>
        </div>

        <div class="grid-2">
            <!-- ASSETS -->
            <div>
                <h3 style="margin: 0 0 8px 0; color: #0F5132; font-size: 15px; font-weight: 900;">جانب الأصول (Assets)</h3>

                <!-- Current Assets -->
                <table>
                    <thead>
                        <tr class="table-title">
                            <th colspan="2" style="text-align: right;">1. الأصول المتداولة</th>
                            <th style="width: 100px; text-align: right;">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['current_assets'] as $item)
                        <tr>
                            <td style="width: 70px; font-family: monospace;">{{ $item['account']->code }}</td>
                            <td>{{ $item['account']->name }}</td>
                            <td style="text-align: right; color: #0F5132; font-weight: 700;">{{ formatMoney($item['balance']) }}</td>
                        </tr>
                        @endforeach
                        <tr style="background: #F1F5F9; font-weight: 800;">
                            <td colspan="2">إجمالي الأصول المتداولة:</td>
                            <td style="text-align: right; color: #0F5132;">{{ formatMoney($data['total_current_assets']) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Fixed Assets -->
                <table>
                    <thead>
                        <tr class="table-title">
                            <th colspan="2" style="text-align: right;">2. الأصول الثابتة</th>
                            <th style="width: 100px; text-align: right;">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['fixed_assets'] as $item)
                        <tr>
                            <td style="width: 70px; font-family: monospace;">{{ $item['account']->code }}</td>
                            <td>{{ $item['account']->name }}</td>
                            <td style="text-align: right; color: #0F5132; font-weight: 700;">{{ formatMoney($item['balance']) }}</td>
                        </tr>
                        @endforeach
                        <tr style="background: #F1F5F9; font-weight: 800;">
                            <td colspan="2">إجمالي الأصول الثابتة:</td>
                            <td style="text-align: right; color: #0F5132;">{{ formatMoney($data['total_fixed_assets']) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div style="background: #0F5132; color: #fff; padding: 10px 14px; border-radius: 6px; display: flex; justify-content: space-between; font-weight: 900; font-size: 14px;">
                    <span>إجمالي الأصول:</span>
                    <span>{{ formatMoney($data['total_assets']) }}</span>
                </div>
            </div>

            <!-- LIABILITIES & EQUITY -->
            <div>
                <h3 style="margin: 0 0 8px 0; color: #4338CA; font-size: 15px; font-weight: 900;">جانب الالتزامات وحقوق الملكية</h3>

                <!-- Current Liabilities -->
                <table>
                    <thead>
                        <tr class="table-title">
                            <th colspan="2" style="text-align: right;">1. الالتزامات المتداولة</th>
                            <th style="width: 100px; text-align: right;">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['current_liabilities'] as $item)
                        <tr>
                            <td style="width: 70px; font-family: monospace;">{{ $item['account']->code }}</td>
                            <td>{{ $item['account']->name }}</td>
                            <td style="text-align: right; color: #DC2626; font-weight: 700;">{{ formatMoney($item['balance']) }}</td>
                        </tr>
                        @endforeach
                        <tr style="background: #F1F5F9; font-weight: 800;">
                            <td colspan="2">إجمالي الالتزامات المتداولة:</td>
                            <td style="text-align: right; color: #DC2626;">{{ formatMoney($data['total_current_liabilities']) }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Equity -->
                <table>
                    <thead>
                        <tr class="table-title">
                            <th colspan="2" style="text-align: right;">2. حقوق الملكية ورأس المال</th>
                            <th style="width: 100px; text-align: right;">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['equity_accounts'] as $item)
                        <tr>
                            <td style="width: 70px; font-family: monospace;">{{ $item['account']->code }}</td>
                            <td>{{ $item['account']->name }}</td>
                            <td style="text-align: right; color: #4338CA; font-weight: 700;">{{ formatMoney($item['balance']) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td style="font-family: monospace;">-</td>
                            <td>أرباح الفترة الحالية المنقولة</td>
                            <td style="text-align: right; color: #0F5132; font-weight: 700;">{{ formatMoney($data['period_net_profit']) }}</td>
                        </tr>
                        <tr style="background: #F1F5F9; font-weight: 800;">
                            <td colspan="2">إجمالي حقوق الملكية:</td>
                            <td style="text-align: right; color: #4338CA;">{{ formatMoney($data['total_equity']) }}</td>
                        </tr>
                    </tbody>
                </table>

                <div style="background: #4338CA; color: #fff; padding: 10px 14px; border-radius: 6px; display: flex; justify-content: space-between; font-weight: 900; font-size: 14px;">
                    <span>إجمالي الالتزامات وحقوق الملكية:</span>
                    <span>{{ formatMoney($data['total_liabilities_and_equity']) }}</span>
                </div>
            </div>
        </div>

        <div class="signatures">
            <div class="sig-box">
                <span class="sig-title">رئيس الحسابات</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">المدير المالي</span>
                <span class="sig-line"></span>
            </div>
            <div class="sig-box">
                <span class="sig-title">المحاسب القانوني المعتمد</span>
                <span class="sig-line"></span>
            </div>
        </div>
    </div>

</body>
</html>

