@extends('layouts.app')

@section('title', 'تقرير التدفق النقدي والسيولة المالية')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
                تقرير التدفق النقدي والسيولة المالية (Cash Flow & Liquidity Report)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحليل المقبوضات والمدفوعات والمصروفات حسب البنود خلال الفترة المحددة
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button onclick="window.print()" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; font-weight: 700; font-size: 13px; color: var(--text-main); display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                <i class="fa-solid fa-print" style="color: var(--accent-gold);"></i>
                طباعة التقرير
            </button>
            <a href="{{ route('treasury.treasuries.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-vault"></i>
                الخزائن والحسابات
            </a>
        </div>
    </div>

    <!-- Date Filter Card -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('treasury.reports.cash_flow') }}" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">من تاريخ</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="erp-input" style="width: 170px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="erp-input" style="width: 170px;">
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-size: 13px; font-weight: 700; height: 42px;">
                <i class="fa-solid fa-filter"></i>
                تحديث التقرير
            </button>
        </form>
    </div>

    <!-- Flow Summary Metric Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المقبوضات والتحصيلات (وارد +)</span>
            <strong style="font-size: 22px; font-weight: 900; color: #0F5132;">+ {{ formatMoney($totalIn) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المدفوعات والمصروفات (صادر -)</span>
            <strong style="font-size: 22px; font-weight: 900; color: #DC2626;">- {{ formatMoney($totalOut) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); background: rgba(15, 81, 50, 0.03);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">صافي التدفق المالي للفترة</span>
            <strong style="font-size: 24px; font-weight: 900; color: {{ $netFlow < 0 ? '#DC2626' : 'var(--primary)' }};">
                {{ formatMoney($netFlow) }}
            </strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي رصيد السيولة بالخزائن حالياً</span>
            <strong style="font-size: 22px; font-weight: 900; color: #4338CA;">{{ formatMoney($totalLiquidity) }}</strong>
        </div>
    </div>

    <!-- Expenses by Category Breakdown -->
    @if($categoryExpenses->isNotEmpty())
    <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
        <h3 style="font-size: 16px; font-weight: 900; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-chart-bar" style="color: var(--accent-gold);"></i>
            توزيع المصروفات حسب البنود خلال الفترة
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
            @foreach($categoryExpenses as $cat)
                @php
                    $catAmt = (float) $cat->vouchers_sum_amount;
                    $percent = $totalOut > 0 ? round(($catAmt / $totalOut) * 100, 1) : 0;
                @endphp
                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <strong style="font-size: 14px; color: var(--text-main);">{{ $cat->name }}</strong>
                        <strong style="font-size: 15px; color: #DC2626;">{{ formatMoney($catAmt) }}</strong>
                    </div>
                    <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 4px; overflow: hidden; margin-bottom: 6px;">
                        <div style="width: {{ $percent }}%; height: 100%; background: #DC2626; border-radius: 4px;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted);">
                        <span>النسبة من إجمالي الصرف:</span>
                        <strong>{{ $percent }}%</strong>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Treasuries Status Grid -->
    <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
        <h3 style="font-size: 16px; font-weight: 900; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-vault" style="color: var(--primary);"></i>
            أرصدة الخزائن والحسابات البنكية اللحظية
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
            @foreach($treasuries as $tr)
                <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                    <div>
                        <strong style="font-size: 14.5px; color: var(--text-main); display: block;">{{ $tr->name }}</strong>
                        <span style="font-size: 12px; color: var(--text-muted);">{{ $tr->type_label }}</span>
                    </div>
                    <div style="text-align: left;">
                        <strong style="font-size: 17px; font-weight: 900; color: {{ $tr->current_balance < 0 ? '#DC2626' : 'var(--primary)' }};">
                            {{ formatMoney($tr->current_balance, $tr->currency) }}
                        </strong>
                        <a href="{{ route('treasury.treasuries.statement', $tr->id) }}" style="display: block; font-size: 11.5px; color: var(--accent-gold); text-decoration: none; font-weight: 700; margin-top: 2px;">
                            عرض الكشف &larr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

