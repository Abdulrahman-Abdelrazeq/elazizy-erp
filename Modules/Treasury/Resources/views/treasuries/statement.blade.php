@extends('layouts.app')

@section('title', 'كشف حساب الخزينة - ' . $treasury->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <a href="{{ route('treasury.treasuries.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 700;">
                    <i class="fa-solid fa-arrow-right"></i> الخزائن والحسابات
                </a>
                <span style="color: var(--border-color);">/</span>
                <span style="font-size: 14px; font-weight: 800; color: var(--primary);">كشف حساب: {{ $treasury->name }}</span>
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                كشف حركة الخزينة والتسوية النقدية (Account Statement)
                @if($treasury->type === 'cash')
                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-size: 12px; font-weight: 700;">خزينة نقدية</span>
                @elseif($treasury->type === 'bank')
                    <span class="badge" style="background: #E0E7FF; color: #4338CA; font-size: 12px; font-weight: 700;">حساب بنكي</span>
                @else
                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-size: 12px; font-weight: 700;">محفظة إلكترونية</span>
                @endif
            </h2>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button onclick="window.print()" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; font-weight: 700; font-size: 13px; color: var(--text-main); display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                <i class="fa-solid fa-print" style="color: var(--accent-gold);"></i>
                طباعة كشف الحساب
            </button>
            <a href="{{ route('treasury.vouchers.create') }}?type=receipt" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-circle-arrow-down"></i>
                إيداع / سند قبض
            </a>
            <a href="{{ route('treasury.vouchers.create') }}?type=payment" style="background: #DC2626; color: #fff; padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-arrow-up"></i>
                صرف / سند دفع
            </a>
        </div>
    </div>

    <!-- Date Range Filter Card -->
    <div class="erp-card" style="padding: 16px 20px; margin-bottom: 0;">
        <form method="GET" action="{{ route('treasury.treasuries.statement', $treasury->id) }}" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">من تاريخ</label>
                <input type="date" name="from_date" value="{{ $fromDate }}" class="erp-input" style="width: 170px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ $toDate }}" class="erp-input" style="width: 170px;">
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px 20px; font-size: 13px; font-weight: 700; height: 42px;">
                <i class="fa-solid fa-filter"></i>
                عرض الحركات
            </button>
        </form>
    </div>

    <!-- Period Balance Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #64748B;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">رصيد بداية الفترة (قبل {{ $fromDate }})</span>
            <strong style="font-size: 20px; font-weight: 900; color: #334155;">{{ formatMoney($periodOpeningBalance, $treasury->currency) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الإيداعات / المقبوضات (وارد +)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">+ {{ formatMoney($totalIn, $treasury->currency) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المسحوبات / المدفوعات (صادر -)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #DC2626;">- {{ formatMoney($totalOut, $treasury->currency) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); background: rgba(15, 81, 50, 0.03);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">رصيد نهاية الفترة الحسابية</span>
            <strong style="font-size: 22px; font-weight: 900; color: var(--primary);">{{ formatMoney($periodClosingBalance, $treasury->currency) }}</strong>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">التاريخ والوقت</th>
                        <th style="width: 110px;">نوع الحركة</th>
                        <th>مصدر الحركة</th>
                        <th>البيان والتفاصيل</th>
                        <th style="color: #0F5132; width: 130px;">وارد (إيداع +)</th>
                        <th style="color: #DC2626; width: 130px;">صادر (صرف -)</th>
                        <th style="width: 140px;">الرصيد بعد الحركة</th>
                        <th>المستخدم</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="background: #F8FAFC; font-weight: 700;">
                        <td>{{ $fromDate }}</td>
                        <td colspan="5" style="color: var(--text-muted);">--- رصيد بداية الفترة المنقول ---</td>
                        <td style="font-size: 15px; font-weight: 900; color: var(--text-main);">{{ formatMoney($periodOpeningBalance, $treasury->currency) }}</td>
                        <td>النظام</td>
                    </tr>

                    @php $running = $periodOpeningBalance; @endphp
                    @forelse($transactions as $tx)
                        @php
                            if ($tx->type === 'in') {
                                $running += $tx->amount;
                            } else {
                                $running -= $tx->amount;
                            }
                        @endphp
                        <tr>
                            <td style="font-size: 12px; font-weight: 600;">
                                {{ $tx->transaction_date->format('Y-m-d h:i A') }}
                            </td>
                            <td>
                                @if($tx->type === 'in')
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">
                                        <i class="fa-solid fa-arrow-down"></i> وارد (+)
                                    </span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">
                                        <i class="fa-solid fa-arrow-up"></i> صادر (-)
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background: #F1F5F9; color: var(--text-main); font-weight: 600;">
                                    {{ $tx->source_label }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--text-main); font-size: 13px;">{{ $tx->description }}</strong>
                            </td>
                            <td style="font-weight: 800; color: #0F5132;">
                                {{ $tx->type === 'in' ? '+' . formatMoney($tx->amount, $treasury->currency) : '—' }}
                            </td>
                            <td style="font-weight: 800; color: #DC2626;">
                                {{ $tx->type === 'out' ? '-' . formatMoney($tx->amount, $treasury->currency) : '—' }}
                            </td>
                            <td style="font-weight: 900; font-size: 14px; color: {{ $tx->balance_after < 0 ? '#DC2626' : 'var(--text-main)' }};">
                                {{ formatMoney($tx->balance_after, $treasury->currency) }}
                            </td>
                            <td style="font-size: 12px; color: var(--text-muted);">
                                {{ $tx->creator->name ?? 'مستخدم النظام' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 36px 16px; color: var(--text-muted);">
                                لا توجد حركات مالية مسجلة خلال هذه الفترة الزمنية المحددة.
                            </td>
                        </tr>
                    @endforelse

                    <tr style="background: rgba(15, 81, 50, 0.05); font-weight: 800; border-top: 2px solid var(--primary);">
                        <td colspan="4" style="font-size: 14px; color: var(--primary);">
                            <i class="fa-solid fa-calculator"></i> إجمالي حركة الفترة والرصيد الختامي:
                        </td>
                        <td style="color: #0F5132; font-size: 15px;">+ {{ formatMoney($totalIn, $treasury->currency) }}</td>
                        <td style="color: #DC2626; font-size: 15px;">- {{ formatMoney($totalOut, $treasury->currency) }}</td>
                        <td style="font-size: 16px; color: var(--primary);">{{ formatMoney($periodClosingBalance, $treasury->currency) }}</td>
                        <td>—</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
