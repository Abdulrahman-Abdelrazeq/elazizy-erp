@extends('layouts.app')

@section('title', 'مركز التقارير والقوائم المالية (Financial Reports Hub)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
                مركز التقارير والقوائم المالية والتحليلية (Financial Reports Hub)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                استعراض القوائم المالية الختامية، مؤشرات السيولة والربحية، التدفقات النقدية، والإقرارات الضريبية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('accounting.accounts.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-sitemap" style="color: var(--accent-gold);"></i>
                شجرة الحسابات
            </a>
            <a href="{{ route('accounting.journal-entries.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-book" style="color: var(--primary);"></i>
                دفتر القيود
            </a>
            <a href="{{ route('accounting.trial_balance.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-scale-balanced" style="color: #4338CA;"></i>
                ميزان المراجعة
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('reports.index') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الفترة من تاريخ</label>
                <input type="date" name="from_date" class="erp-input" value="{{ $fromDate }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" class="erp-input" value="{{ $toDate }}">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 20px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تحديث المؤشرات
                </button>
                <a href="{{ route('reports.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Executive KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الإيرادات للفترة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ formatMoney($income['total_revenues']) }}</strong>
                <span style="font-size: 11px; color: #10B981; display: block;">مبيعات وتوريدات معتمدة</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مجمل الربح (Gross Profit)</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ formatMoney($income['gross_profit']) }}</strong>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">هامش ربح: {{ $income['gross_margin_percent'] }}%</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">صافي أرباح الفترة (Net Income)</span>
                <strong style="font-size: 20px; font-weight: 900; color: {{ $income['is_profitable'] ? '#0F5132' : '#DC2626' }};">
                    {{ formatMoney($income['net_profit']) }}
                </strong>
                <span style="font-size: 11px; color: #D97706; display: block;">هامش صافي الربح: {{ $income['net_margin_percent'] }}%</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الأصول (Total Assets)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ formatMoney($balance['total_assets']) }}</strong>
                <span style="font-size: 11px; color: #4338CA; display: block;">متداولة: {{ formatMoney($balance['total_current_assets']) }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Hub to Detailed Statements -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 18px;">
        <!-- 1. Income Statement -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s; border: 1.5px solid var(--border-color);" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="this.style.borderColor='var(--border-color)'">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <span style="width: 44px; height: 44px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </span>
                    <span style="background: #D1E7DD; color: #0F5132; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px;">قائمة أساسية</span>
                </div>
                <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin-bottom: 8px;">قائمة الدخل والأرباح والخسائر</h3>
                <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">
                    استعراض إيرادات المبيعات وتكلفة البضاعة المباعة (COGS) والمصروفات التشغيلية لحساب صافي الربح للفترة.
                </p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('reports.income_statement', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" class="btn-primary-custom" style="flex: 1; justify-content: center; font-size: 13px;">
                    <i class="fa-solid fa-arrow-left"></i> فتح القائمة
                </a>
                <a href="{{ route('reports.income_statement.print', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" target="_blank" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 14px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-size: 13px;">
                    <i class="fa-solid fa-print"></i>
                </a>
            </div>
        </div>

        <!-- 2. Balance Sheet -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s; border: 1.5px solid var(--border-color);" onmouseover="this.style.borderColor='#4338CA'" onmouseout="this.style.borderColor='var(--border-color)'">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <span style="width: 44px; height: 44px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </span>
                    <span style="background: #E0E7FF; color: #4338CA; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px;">الميزانية العمومية</span>
                </div>
                <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin-bottom: 8px;">قائمة المركز المالي والميزانية</h3>
                <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">
                    هيكل الأصول المتداولة والثابتة، والالتزامات قصيرة وطويلة الأجل، وحقوق الملكية مع معادلة التوازن اللحظية.
                </p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('reports.balance_sheet', ['as_of_date' => $toDate]) }}" style="flex: 1; background: #4338CA; color: #fff; padding: 9px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-left"></i> فتح الميزانية
                </a>
                <a href="{{ route('reports.balance_sheet.print', ['as_of_date' => $toDate]) }}" target="_blank" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 14px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-size: 13px;">
                    <i class="fa-solid fa-print"></i>
                </a>
            </div>
        </div>

        <!-- 3. Cash Flow Statement -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s; border: 1.5px solid var(--border-color);" onmouseover="this.style.borderColor='#D97706'" onmouseout="this.style.borderColor='var(--border-color)'">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <span style="width: 44px; height: 44px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </span>
                    <span style="background: #FEF3C7; color: #D97706; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px;">إدارة السيولة</span>
                </div>
                <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin-bottom: 8px;">قائمة التدفقات النقدية (Cash Flow)</h3>
                <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">
                    تحليل التدفقات النقدية من الأنشطة التشغيلية والاستثمارية والتمويلية ومطابقة رصيد النقدية الختامي.
                </p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('reports.cash_flow', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" style="flex: 1; background: #D97706; color: #fff; padding: 9px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-left"></i> فتح القائمة
                </a>
                <a href="{{ route('reports.cash_flow.print', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" target="_blank" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 14px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-size: 13px;">
                    <i class="fa-solid fa-print"></i>
                </a>
            </div>
        </div>

        <!-- 4. Financial Ratios -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between; border: 1.5px solid var(--border-color);">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <span style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-gauge-high"></i>
                    </span>
                    <span style="background: #D1E7DD; color: #0F5132; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px;">تحليل مالي</span>
                </div>
                <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin-bottom: 8px;">النسب والمؤشرات المالية التحليلية</h3>
                <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">
                    نسب السيولة (نسبة التداول، السيولة السريعة)، مؤشرات الربحية (ROA, ROE)، ونسب المديونية والهيكل المالي.
                </p>
            </div>
            <div>
                <a href="{{ route('reports.ratios', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" style="display: block; text-align: center; background: #0F5132; color: #fff; padding: 9px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-arrow-left"></i> استعراض النسب المالية
                </a>
            </div>
        </div>

        <!-- 5. Tax & VAT Declaration -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between; border: 1.5px solid var(--border-color);">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <span style="width: 44px; height: 44px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-receipt"></i>
                    </span>
                    <span style="background: #FEE2E2; color: #DC2626; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px;">إقرار ضريبي</span>
                </div>
                <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin-bottom: 8px;">تقرير ضريبة القيمة المضافة (VAT)</h3>
                <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">
                    ضريبة مخرجات المبيعات، ضريبة مدخلات المشتريات، وصافي الضريبة المستحقة للسداد أو الاسترداد.
                </p>
            </div>
            <div style="display: flex; gap: 8px;">
                <a href="{{ route('reports.tax', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" style="flex: 1; background: #DC2626; color: #fff; padding: 9px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-left"></i> الإقرار الضريبي
                </a>
                <a href="{{ route('reports.tax.print', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" target="_blank" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 14px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-size: 13px;">
                    <i class="fa-solid fa-print"></i>
                </a>
            </div>
        </div>

        <!-- 6. Sales & Profitability Analysis -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between; border: 1.5px solid var(--border-color);">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <span style="width: 44px; height: 44px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: #B45309; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-chart-column"></i>
                    </span>
                    <span style="background: #FEF3C7; color: #B45309; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px;">ربحية تجارية</span>
                </div>
                <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin-bottom: 8px;">تحليل المبيعات وربحية الأصناف والعملاء</h3>
                <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">
                    مقارنة صافي المبيعات بتكلفة البضاعة، وقائمة الأصناف الخمسة الأكثر ربحية والعملاء الأكثر تحقيقاً للعوائد.
                </p>
            </div>
            <div>
                <a href="{{ route('reports.sales_profitability', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" class="btn-primary-custom" style="display: block; text-align: center; font-size: 13px;">
                    <i class="fa-solid fa-arrow-left"></i> استعراض ربحية المبيعات
                </a>
            </div>
        </div>

        <!-- 7. Budgets & Variance Planning -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between; border: 1.5px solid var(--border-color);">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <span style="width: 44px; height: 44px; border-radius: 12px; background: #F1F5F9; color: var(--text-main); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-bullseye"></i>
                    </span>
                    <span style="background: #E2E8F0; color: var(--text-main); font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px;">تخطيط مالي</span>
                </div>
                <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin-bottom: 8px;">الموازنات التقديرية (Budgets & Targets)</h3>
                <p style="font-size: 12.5px; color: var(--text-muted); line-height: 1.6; margin-bottom: 20px;">
                    تحديد الموازنات المستهدفة لبنود الإيرادات والمصروفات ومقارنتها بالأداء الفعلي المحقق وتحليل الانحراف.
                </p>
            </div>
            <div>
                <a href="{{ route('reports.budgets.index') }}" style="display: block; text-align: center; background: #1E293B; color: #fff; padding: 9px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-arrow-left"></i> جدول الموازنات التقديرية
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

