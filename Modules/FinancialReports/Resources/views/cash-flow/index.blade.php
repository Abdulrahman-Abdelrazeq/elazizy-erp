@extends('layouts.app')

@section('title', 'قائمة التدفقات النقدية (Cash Flow Statement)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: #D97706; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                </span>
                قائمة التدفقات النقدية (Cash Flow Statement)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تتبع حركة المقبوضات والمدفوعات النقدية والبنكية مصنفة وفق الأنشطة التشغيلية والاستثمارية والتمويلية (معيار IAS 7)
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('reports.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                مركز التقارير
            </a>
            <a href="{{ route('reports.income_statement') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary);"></i>
                قائمة الدخل
            </a>
            <a href="{{ route('reports.cash_flow.print', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" target="_blank" style="background: #D97706; color: #fff; padding: 9px 18px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة قائمة التدفقات
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('reports.cash_flow') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="from_date" class="erp-input" value="{{ $fromDate }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" class="erp-input" value="{{ $toDate }}">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 20px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تحديث التقرير
                </button>
                <a href="{{ route('reports.cash_flow') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">رصيد النقدية في بداية الفترة</span>
            <strong style="font-size: 19px; font-weight: 900; color: var(--primary);">{{ formatMoney($data['opening_cash']) }}</strong>
            <span style="font-size: 11px; color: var(--text-muted); display: block;">قبل تاريخ {{ $fromDate }}</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">صافي التدفق التشغيلي (Operating)</span>
            <strong style="font-size: 19px; font-weight: 900; color: {{ $data['net_operating_cash'] >= 0 ? '#0F5132' : '#DC2626' }};">
                {{ formatMoney($data['net_operating_cash']) }}
            </strong>
            <span style="font-size: 11px; color: #10B981; display: block;">مقبوضات - مدفوعات تشغيلية</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">صافي التدفق الاستثماري (Investing)</span>
            <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">
                {{ formatMoney($data['net_investing_cash']) }}
            </strong>
            <span style="font-size: 11px; color: var(--text-muted); display: block;">شراء أصول ثابتة ومعدات</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">رصيد النقدية في نهاية الفترة</span>
            <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ formatMoney($data['closing_cash']) }}</strong>
            <span style="font-size: 11px; color: #D97706; display: block;">الخزائن والحسابات البنكية</span>
        </div>
    </div>

    <!-- Formal Statement Breakdown Table -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 900; color: var(--primary); margin-bottom: 18px; border-bottom: 2px solid var(--border-color); padding-bottom: 12px;">
            تفاصيل قائمة التدفقات النقدية عن الفترة من {{ $fromDate }} إلى {{ $toDate }}
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                <tbody>
                    <!-- 1. Operating Activities -->
                    <tr style="background: #F8FAFC;">
                        <td style="padding: 12px 16px; font-weight: 900; color: #0F5132; font-size: 15px;">
                            <i class="fa-solid fa-circle-chevron-down" style="margin-left: 6px;"></i> 1. التدفقات النقدية من الأنشطة التشغيلية (Operating Activities)
                        </td>
                        <td style="padding: 12px 16px; text-align: right; width: 180px;"></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px 32px; color: var(--text-main);">
                            متحصلات نقدية من العملاء والمبيعات (+)
                        </td>
                        <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: #0F5132;">
                            {{ formatMoney($data['operating_inflow']) }}
                        </td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px 32px; color: var(--text-main);">
                            مدفوعات نقدية للموردين والمصروفات التشغيلية (-)
                        </td>
                        <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: #DC2626;">
                            ({{ formatMoney($data['operating_outflow']) }})
                        </td>
                    </tr>
                    <tr style="background: #D1E7DD; font-weight: 800;">
                        <td style="padding: 11px 24px; color: #0F5132;">صافي التدفق النقدي من الأنشطة التشغيلية:</td>
                        <td style="padding: 11px 16px; text-align: right; color: #0F5132; font-size: 14.5px;">
                            {{ formatMoney($data['net_operating_cash']) }}
                        </td>
                    </tr>

                    <!-- 2. Investing Activities -->
                    <tr style="background: #F8FAFC;">
                        <td style="padding: 12px 16px; font-weight: 900; color: #4338CA; font-size: 15px; border-top: 2px solid var(--border-color);">
                            <i class="fa-solid fa-circle-chevron-down" style="margin-left: 6px;"></i> 2. التدفقات النقدية من الأنشطة الاستثمارية (Investing Activities)
                        </td>
                        <td style="padding: 12px 16px; text-align: right;"></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px 32px; color: var(--text-main);">
                            مدفوعات نقدية لشراء أصول ثابتة ومعدات وتجهيزات (-)
                        </td>
                        <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: #4338CA;">
                            ({{ formatMoney($data['investing_outflow']) }})
                        </td>
                    </tr>
                    <tr style="background: #E0E7FF; font-weight: 800;">
                        <td style="padding: 11px 24px; color: #3730A3;">صافي التدفق النقدي من الأنشطة الاستثمارية:</td>
                        <td style="padding: 11px 16px; text-align: right; color: #3730A3; font-size: 14.5px;">
                            {{ formatMoney($data['net_investing_cash']) }}
                        </td>
                    </tr>

                    <!-- 3. Financing Activities -->
                    <tr style="background: #F8FAFC;">
                        <td style="padding: 12px 16px; font-weight: 900; color: #D97706; font-size: 15px; border-top: 2px solid var(--border-color);">
                            <i class="fa-solid fa-circle-chevron-down" style="margin-left: 6px;"></i> 3. التدفقات النقدية من الأنشطة التمويلية (Financing Activities)
                        </td>
                        <td style="padding: 12px 16px; text-align: right;"></td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px 32px; color: var(--text-main);">
                            متحصلات نقدية من زيادة رأس المال أو مساهمات الشركاء (+)
                        </td>
                        <td style="padding: 10px 16px; text-align: right; font-weight: 700; color: #D97706;">
                            {{ formatMoney($data['financing_inflow']) }}
                        </td>
                    </tr>
                    <tr style="background: #FEF3C7; font-weight: 800;">
                        <td style="padding: 11px 24px; color: #92400E;">صافي التدفق النقدي من الأنشطة التمويلية:</td>
                        <td style="padding: 11px 16px; text-align: right; color: #92400E; font-size: 14.5px;">
                            {{ formatMoney($data['net_financing_cash']) }}
                        </td>
                    </tr>

                    <!-- Net Change and Final Reconciliation -->
                    <tr style="background: #F1F5F9; font-weight: 900; border-top: 2px solid var(--border-color);">
                        <td style="padding: 13px 20px; color: var(--text-main);">صافي التغير في النقدية وما في حكمها خلال الفترة:</td>
                        <td style="padding: 13px 16px; text-align: right; color: {{ $data['net_change_in_cash'] >= 0 ? '#0F5132' : '#DC2626' }}; font-size: 15px;">
                            {{ formatMoney($data['net_change_in_cash']) }}
                        </td>
                    </tr>
                    <tr style="background: #F8FAFC; font-weight: 800;">
                        <td style="padding: 11px 20px; color: var(--text-muted);">يضاف: رصيد النقدية وما في حكمها في بداية الفترة:</td>
                        <td style="padding: 11px 16px; text-align: right; color: var(--text-muted); font-size: 14px;">
                            {{ formatMoney($data['opening_cash']) }}
                        </td>
                    </tr>
                    <tr style="background: #0F5132; color: #fff; font-weight: 900; font-size: 16px;">
                        <td style="padding: 16px 20px;">رصيد النقدية وما في حكمها في نهاية الفترة (Closing Cash Balance):</td>
                        <td style="padding: 16px; text-align: right; font-size: 18px;">
                            {{ formatMoney($data['closing_cash']) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
