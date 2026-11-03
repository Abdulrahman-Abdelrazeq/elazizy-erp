@extends('layouts.app')

@section('title', 'قائمة الدخل والأرباح والخسائر (Income Statement)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </span>
                قائمة الدخل والأرباح والخسائر (Income Statement)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                التقرير المالي المعياري لتحديد نتائج أعمال الشركة وصافي الأرباح أو الخسائر المحققة خلال الفترة
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('reports.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                مركز التقارير
            </a>
            <a href="{{ route('reports.balance_sheet') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-scale-balanced" style="color: #4338CA;"></i>
                الميزانية العمومية
            </a>
            <a href="{{ route('reports.income_statement.print', ['from_date' => $fromDate, 'to_date' => $toDate, 'cost_center_id' => $costCenterId]) }}" target="_blank" style="background: var(--primary); color: #fff; padding: 9px 18px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة قائمة الدخل
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('reports.income_statement') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 120px 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="from_date" class="erp-input" value="{{ $fromDate }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" class="erp-input" value="{{ $toDate }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">مركز التكلفة</label>
                <select name="cost_center_id" class="erp-input">
                    <option value="">جميع مراكز التكلفة</option>
                    @foreach($costCenters as $cc)
                        <option value="{{ $cc->id }}" {{ $costCenterId == $cc->id ? 'selected' : '' }}>
                            {{ $cc->code }} - {{ $cc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> عرض
                </button>
            </div>

            <div>
                <a href="{{ route('reports.income_statement') }}" style="display: block; width: 100%; text-align: center; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الإيرادات (Revenues)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ formatMoney($data['total_revenues']) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تكلفة البضاعة المباعة (COGS)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #DC2626;">{{ formatMoney($data['total_cogs']) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مجمل الربح (Gross Profit)</span>
            <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ formatMoney($data['gross_profit']) }}</strong>
            <span style="font-size: 11px; color: var(--text-muted); display: block;">هامش مجمل الربح: {{ $data['gross_margin_percent'] }}%</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">المصروفات التشغيلية والإدارية</span>
            <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ formatMoney($data['total_operating_expenses']) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid {{ $data['is_profitable'] ? '#0F5132' : '#DC2626' }}; background: {{ $data['is_profitable'] ? 'rgba(15, 81, 50, 0.04)' : 'rgba(220, 38, 38, 0.04)' }};">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">صافي ربح / (خسارة) الفترة</span>
            <strong style="font-size: 22px; font-weight: 900; color: {{ $data['is_profitable'] ? '#0F5132' : '#DC2626' }};">
                {{ formatMoney($data['net_profit']) }}
            </strong>
            <span style="font-size: 11px; font-weight: 700; color: {{ $data['is_profitable'] ? '#0F5132' : '#DC2626' }}; display: block;">
                هامش صافي الربح: {{ $data['net_margin_percent'] }}%
            </span>
        </div>
    </div>

    <!-- Formal Income Statement Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 14px; margin-bottom: 20px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 900; color: var(--primary); margin: 0;">
                    قائمة الدخل عن الفترة من {{ $fromDate }} إلى {{ $toDate }}
                </h3>
                <span style="font-size: 12px; color: var(--text-muted);">جميع المبالغ مسجلة بالعملة الرسمية المعتمدة للنظام</span>
            </div>
            <span style="background: #F1F5F9; color: var(--text-main); padding: 4px 12px; border-radius: 8px; font-size: 12px; font-weight: 700;">
                معيار المحاسبة الدولي (IAS 1)
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                <tbody>
                    <!-- 1. REVENUES -->
                    <tr style="background: #F8FAFC;">
                        <td colspan="2" style="padding: 12px 16px; font-weight: 900; color: var(--primary); font-size: 15px;">
                            <i class="fa-solid fa-circle-chevron-down" style="margin-left: 6px;"></i> أولاً: الإيرادات التشغيلية (Operating Revenues)
                        </td>
                        <td style="padding: 12px 16px; text-align: right; width: 180px;"></td>
                    </tr>
                    @forelse($data['revenue_lines'] as $rev)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px 32px; color: var(--text-muted); font-family: monospace; width: 130px;">
                            {{ $rev['account']->code }}
                        </td>
                        <td style="padding: 10px 12px; font-weight: 700; color: var(--text-main);">
                            {{ $rev['account']->name }}
                        </td>
                        <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: #0F5132;">
                            {{ formatMoney($rev['amount']) }}
                        </td>
                    </tr>
                    @empty
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td colspan="2" style="padding: 10px 32px; color: var(--text-muted); font-size: 12.5px;">لا توجد حركات إيرادات مسجلة خلال الفترة</td>
                        <td style="padding: 10px 16px; text-align: right; color: var(--text-muted);">0.00</td>
                    </tr>
                    @endforelse
                    <tr style="background: #F1F5F9; font-weight: 900;">
                        <td colspan="2" style="padding: 12px 24px; color: var(--text-main);">إجمالي الإيرادات التشغيلية:</td>
                        <td style="padding: 12px 16px; text-align: right; color: #0F5132; font-size: 15px;">
                            {{ formatMoney($data['total_revenues']) }}
                        </td>
                    </tr>

                    <!-- 2. COGS -->
                    <tr style="background: #F8FAFC;">
                        <td colspan="2" style="padding: 12px 16px; font-weight: 900; color: #DC2626; font-size: 15px; border-top: 2px solid var(--border-color);">
                            <i class="fa-solid fa-circle-chevron-down" style="margin-left: 6px;"></i> يخصم: تكلفة المبيعات / البضاعة المباعة (Cost of Goods Sold)
                        </td>
                        <td style="padding: 12px 16px; text-align: right;"></td>
                    </tr>
                    @forelse($data['cogs_lines'] as $cogs)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px 32px; color: var(--text-muted); font-family: monospace;">
                            {{ $cogs['account']->code }}
                        </td>
                        <td style="padding: 10px 12px; font-weight: 700; color: var(--text-main);">
                            {{ $cogs['account']->name }}
                        </td>
                        <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: #DC2626;">
                            ({{ formatMoney($cogs['amount']) }})
                        </td>
                    </tr>
                    @empty
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td colspan="2" style="padding: 10px 32px; color: var(--text-muted); font-size: 12.5px;">لا توجد تكاليف بضاعة مباشرة مقيدة</td>
                        <td style="padding: 10px 16px; text-align: right; color: var(--text-muted);">0.00</td>
                    </tr>
                    @endforelse
                    <tr style="background: #FEE2E2; font-weight: 900;">
                        <td colspan="2" style="padding: 12px 24px; color: #991B1B;">إجمالي تكلفة البضاعة المباعة:</td>
                        <td style="padding: 12px 16px; text-align: right; color: #DC2626; font-size: 15px;">
                            ({{ formatMoney($data['total_cogs']) }})
                        </td>
                    </tr>

                    <!-- GROSS PROFIT BAR -->
                    <tr style="background: #D1E7DD; font-weight: 900; font-size: 15px; border-top: 2px solid var(--primary); border-bottom: 2px solid var(--primary);">
                        <td colspan="2" style="padding: 14px 20px; color: #0F5132;">
                            مجمل الربح التجاري (Gross Profit):
                        </td>
                        <td style="padding: 14px 16px; text-align: right; color: #0F5132; font-size: 17px;">
                            {{ formatMoney($data['gross_profit']) }}
                        </td>
                    </tr>

                    <!-- 3. OPERATING EXPENSES -->
                    <tr style="background: #F8FAFC;">
                        <td colspan="2" style="padding: 12px 16px; font-weight: 900; color: #D97706; font-size: 15px; border-top: 2px solid var(--border-color);">
                            <i class="fa-solid fa-circle-chevron-down" style="margin-left: 6px;"></i> يخصم: المصروفات التشغيلية والإدارية والعمومية (Operating Expenses)
                        </td>
                        <td style="padding: 12px 16px; text-align: right;"></td>
                    </tr>
                    @forelse($data['operating_expense_lines'] as $exp)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px 32px; color: var(--text-muted); font-family: monospace;">
                            {{ $exp['account']->code }}
                        </td>
                        <td style="padding: 10px 12px; font-weight: 700; color: var(--text-main);">
                            {{ $exp['account']->name }}
                        </td>
                        <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: #D97706;">
                            ({{ formatMoney($exp['amount']) }})
                        </td>
                    </tr>
                    @empty
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td colspan="2" style="padding: 10px 32px; color: var(--text-muted); font-size: 12.5px;">لا توجد مصروفات إدارية أو تشغيلية مقيدة</td>
                        <td style="padding: 10px 16px; text-align: right; color: var(--text-muted);">0.00</td>
                    </tr>
                    @endforelse
                    <tr style="background: #FEF3C7; font-weight: 900;">
                        <td colspan="2" style="padding: 12px 24px; color: #92400E;">إجمالي المصروفات التشغيلية والإدارية:</td>
                        <td style="padding: 12px 16px; text-align: right; color: #D97706; font-size: 15px;">
                            ({{ formatMoney($data['total_operating_expenses']) }})
                        </td>
                    </tr>

                    <!-- NET PROFIT FINAL BAR -->
                    <tr style="background: {{ $data['is_profitable'] ? '#0F5132' : '#DC2626' }}; color: #fff; font-weight: 900; font-size: 16px;">
                        <td colspan="2" style="padding: 16px 20px;">
                            صافي الربح / (الخسارة) للفترة المالية (Net Profit / Loss):
                        </td>
                        <td style="padding: 16px; text-align: right; font-size: 18px;">
                            {{ formatMoney($data['net_profit']) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
