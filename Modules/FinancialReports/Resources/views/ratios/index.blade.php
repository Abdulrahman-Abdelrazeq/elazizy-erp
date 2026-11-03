@extends('layouts.app')

@section('title', 'النسب والمؤشرات المالية التحليلية (Financial Ratios)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-gauge-high"></i>
                </span>
                لوحة النسب والمؤشرات المالية والتحليلية (Financial Ratios & KPIs)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                قياس الملاءة المالية، نسب السيولة السريعة، مؤشرات الربحية والعائد على الاستثمار، وهيكل المديونية
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
            <button onclick="window.print()" style="background: var(--primary); color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة لوحة المؤشرات
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('reports.ratios') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
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
                    <i class="fa-solid fa-filter"></i> تحديث النسب
                </button>
                <a href="{{ route('reports.ratios') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- 1. LIQUIDITY RATIOS (نسب السيولة) -->
    <div>
        <h3 style="font-size: 17px; font-weight: 900; color: var(--primary); margin-bottom: 14px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-water"></i>
            أولاً: مؤشرات السيولة والملاءة المالية (Liquidity Ratios)
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            <!-- Current Ratio -->
            <div class="erp-card" style="padding: 20px; margin-bottom: 0; border-top: 4px solid var(--primary);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 13px; font-weight: 800; color: var(--text-main);">نسبة التداول (Current Ratio)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: {{ $ratios['current_ratio'] >= 1.5 ? '#D1E7DD' : '#FEF3C7' }}; color: {{ $ratios['current_ratio'] >= 1.5 ? '#0F5132' : '#92400E' }};">
                        {{ $ratios['current_ratio'] >= 1.5 ? 'ملاءة ممتازة' : 'مقبول' }}
                    </span>
                </div>
                <div style="font-size: 28px; font-weight: 900; color: var(--primary); margin-bottom: 6px;">
                    {{ $ratios['current_ratio'] }}x
                </div>
                <span style="font-size: 11.5px; color: var(--text-muted); display: block; line-height: 1.5;">
                    الأصول المتداولة ÷ الالتزامات المتداولة (المعيار النموذجي: 1.5 - 2.0)
                </span>
            </div>

            <!-- Quick Ratio -->
            <div class="erp-card" style="padding: 20px; margin-bottom: 0; border-top: 4px solid #10B981;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 13px; font-weight: 800; color: var(--text-main);">نسبة السيولة السريعة (Quick Ratio)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: {{ $ratios['quick_ratio'] >= 1.0 ? '#D1E7DD' : '#FEF3C7' }}; color: {{ $ratios['quick_ratio'] >= 1.0 ? '#0F5132' : '#92400E' }};">
                        {{ $ratios['quick_ratio'] >= 1.0 ? 'سيولة فورية آمنة' : 'متوسطة' }}
                    </span>
                </div>
                <div style="font-size: 28px; font-weight: 900; color: #10B981; margin-bottom: 6px;">
                    {{ $ratios['quick_ratio'] }}x
                </div>
                <span style="font-size: 11.5px; color: var(--text-muted); display: block; line-height: 1.5;">
                    (الأصول المتداولة - المخزون) ÷ الالتزامات (المعيار: 1.0+)
                </span>
            </div>

            <!-- Working Capital -->
            <div class="erp-card" style="padding: 20px; margin-bottom: 0; border-top: 4px solid #4338CA;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 13px; font-weight: 800; color: var(--text-main);">صافي رأس المال العامل (Working Capital)</span>
                    <span style="padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: #E0E7FF; color: #4338CA;">
                        فائض تشغيلي
                    </span>
                </div>
                <div style="font-size: 24px; font-weight: 900; color: #4338CA; margin-bottom: 6px;">
                    {{ formatMoney($ratios['working_capital']) }}
                </div>
                <span style="font-size: 11.5px; color: var(--text-muted); display: block; line-height: 1.5;">
                    الأصول المتداولة - الالتزامات المتداولة (السيولة المتاحة لتمويل العمليات)
                </span>
            </div>
        </div>
    </div>

    <!-- 2. PROFITABILITY RATIOS (نسب الربحية) -->
    <div>
        <h3 style="font-size: 17px; font-weight: 900; color: #D97706; margin-bottom: 14px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-chart-line"></i>
            ثانياً: مؤشرات الربحية والعائد على الاستثمار (Profitability Ratios)
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
            <!-- Gross Margin -->
            <div class="erp-card" style="padding: 20px; margin-bottom: 0; border-top: 4px solid #D97706;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">هامش مجمل الربح (Gross Margin)</span>
                <div style="font-size: 26px; font-weight: 900; color: #D97706; margin-bottom: 4px;">
                    {{ $ratios['gross_margin'] }}%
                </div>
                <span style="font-size: 11px; color: var(--text-muted);">مجمل الربح ÷ إجمالي المبيعات</span>
            </div>

            <!-- Net Margin -->
            <div class="erp-card" style="padding: 20px; margin-bottom: 0; border-top: 4px solid #10B981;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">هامش صافي الربح (Net Margin)</span>
                <div style="font-size: 26px; font-weight: 900; color: {{ $ratios['net_margin'] >= 0 ? '#0F5132' : '#DC2626' }}; margin-bottom: 4px;">
                    {{ $ratios['net_margin'] }}%
                </div>
                <span style="font-size: 11px; color: var(--text-muted);">صافي الربح ÷ إجمالي الإيرادات</span>
            </div>

            <!-- ROA -->
            <div class="erp-card" style="padding: 20px; margin-bottom: 0; border-top: 4px solid var(--primary);">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">العائد على الأصول (ROA)</span>
                <div style="font-size: 26px; font-weight: 900; color: var(--primary); margin-bottom: 4px;">
                    {{ $ratios['roa'] }}%
                </div>
                <span style="font-size: 11px; color: var(--text-muted);">صافي الربح ÷ إجمالي الأصول</span>
            </div>

            <!-- ROE -->
            <div class="erp-card" style="padding: 20px; margin-bottom: 0; border-top: 4px solid #4338CA;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">العائد على حقوق الملكية (ROE)</span>
                <div style="font-size: 26px; font-weight: 900; color: #4338CA; margin-bottom: 4px;">
                    {{ $ratios['roe'] }}%
                </div>
                <span style="font-size: 11px; color: var(--text-muted);">صافي الربح ÷ حقوق الملكية</span>
            </div>
        </div>
    </div>

    <!-- 3. LEVERAGE RATIOS (نسب المديونية والهيكل المالي) -->
    <div>
        <h3 style="font-size: 17px; font-weight: 900; color: #475569; margin-bottom: 14px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-shield-halved"></i>
            ثالثاً: مؤشرات المديونية والهيكل التمويلي (Leverage Ratios)
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px;">
            <div class="erp-card" style="padding: 20px; margin-bottom: 0; border-top: 4px solid #64748B;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 13px; font-weight: 800; color: var(--text-main);">نسبة الديون إلى حقوق الملكية (Debt to Equity)</span>
                    <span style="font-size: 11px; font-weight: 700; color: var(--text-muted);">D/E Ratio</span>
                </div>
                <div style="font-size: 26px; font-weight: 900; color: var(--text-main); margin-bottom: 4px;">
                    {{ $ratios['debt_to_equity'] }}x
                </div>
                <span style="font-size: 11.5px; color: var(--text-muted);">
                    إجمالي الالتزامات ({{ formatMoney($ratios['total_liabilities']) }}) ÷ حقوق الملكية ({{ formatMoney($ratios['total_equity']) }})
                </span>
            </div>

            <div class="erp-card" style="padding: 20px; margin-bottom: 0; border-top: 4px solid #64748B;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <span style="font-size: 13px; font-weight: 800; color: var(--text-main);">نسبة الديون إلى إجمالي الأصول (Debt to Assets)</span>
                    <span style="font-size: 11px; font-weight: 700; color: var(--text-muted);">D/A Ratio</span>
                </div>
                <div style="font-size: 26px; font-weight: 900; color: var(--text-main); margin-bottom: 4px;">
                    {{ $ratios['debt_to_assets'] }}%
                </div>
                <span style="font-size: 11.5px; color: var(--text-muted);">
                    نسبة ما تم تمويله من أصول المنشأة عبر الالتزامات الخارجية مقابل رأس المال
                </span>
            </div>
        </div>
    </div>

</div>
@endsection
