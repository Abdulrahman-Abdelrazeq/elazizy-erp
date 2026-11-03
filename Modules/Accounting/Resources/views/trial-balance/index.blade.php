@extends('layouts.app')

@section('title', 'ميزان المراجعة بالأرصدة والمجاميع (Trial Balance)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-scale-balanced"></i>
                </span>
                ميزان المراجعة بالأرصدة والمجاميع (Trial Balance)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                التحقق الشامل من توازن القيود المحاسبية، وتدقيق أرصدة أول المدة، حركات الفترة، والأرصدة الختامية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('accounting.accounts.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-sitemap" style="color: var(--accent-gold);"></i>
                شجرة الحسابات
            </a>
            <a href="{{ route('accounting.ledger.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-book-open" style="color: #0F5132;"></i>
                دفتر الأستاذ
            </a>
            <button onclick="window.print()" style="background: var(--primary); color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة ميزان المراجعة
            </button>
        </div>
    </div>

    <!-- Balance Status Banner -->
    @if ($isBalanced)
        <div style="background: #D1E7DD; border: 1.5px solid #A3CFBB; color: #0F5132; padding: 16px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-circle-check" style="font-size: 24px; color: #0F5132;"></i>
                <div>
                    <strong style="font-size: 15px; display: block;">ميزان المراجعة متوازن تماماً ونظامي 100%</strong>
                    <span style="font-size: 12.5px; opacity: 0.9;">إجمالي الأرصدة المدينة يطابق تماماً إجمالي الأرصدة الدائنة في جميع المراحل (أول المدة، حركات الفترة، وآخر المدة).</span>
                </div>
            </div>
            <span style="background: #0F5132; color: #fff; padding: 6px 14px; border-radius: 20px; font-weight: 800; font-size: 12px;">
                <i class="fa-solid fa-shield-check"></i> مدقق محاسبياً
            </span>
        </div>
    @else
        <div style="background: #FEE2E2; border: 1.5px solid #F87171; color: #991B1B; padding: 16px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 24px; color: #DC2626;"></i>
                <div>
                    <strong style="font-size: 15px; display: block;">تنبيه محاسبي: ميزان المراجعة غير متوازن!</strong>
                    <span style="font-size: 12.5px; opacity: 0.9;">يوجد فارق بين إجمالي المدين والدائن بمقدار: {{ formatMoney(abs($totalEndingDebit - $totalEndingCredit)) }}، يرجى مراجعة القيود اليومية غير المعتمدة أو الأرصدة الافتتاحية.</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters Bar -->
    <div class="erp-card" style="padding: 20px; margin-bottom: 0;">
        <form method="GET" action="{{ route('accounting.trial_balance.index') }}" style="display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="from_date" class="erp-input" value="{{ $fromDate }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" class="erp-input" value="{{ $toDate }}">
            </div>

            <div style="padding-bottom: 8px;">
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="only_leafs" value="1" {{ $onlyLeafs ? 'checked' : '' }} style="width: 17px; height: 17px; accent-color: var(--primary);">
                    عرض الحسابات الفرعية التحليلية فقط (استثناء الحسابات الرئيسية)
                </label>
            </div>

            <div style="display: flex; gap: 10px; margin-right: auto;">
                <button type="submit" class="btn-primary-custom" style="padding: 10px 20px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تحديث الميزان
                </button>
                <a href="{{ route('accounting.trial_balance.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Trial Balance 6-Column Report Table -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 12.5px;" id="trialBalanceTable">
                <thead>
                    <tr style="background: #0F5132; color: #fff;">
                        <th rowspan="2" style="padding: 12px; text-align: right; border: 1px solid rgba(255,255,255,0.2); width: 110px;">كود الحساب</th>
                        <th rowspan="2" style="padding: 12px; text-align: right; border: 1px solid rgba(255,255,255,0.2); min-width: 240px;">اسم الحساب المالي</th>
                        <th rowspan="2" style="padding: 12px; text-align: center; border: 1px solid rgba(255,255,255,0.2); width: 80px;">الطبيعة</th>
                        <th colspan="2" style="padding: 8px; text-align: center; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.15);">رصيد أول المدة</th>
                        <th colspan="2" style="padding: 8px; text-align: center; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.25);">حركات الفترة</th>
                        <th colspan="2" style="padding: 8px; text-align: center; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.15);">رصيد آخر المدة (الختامي)</th>
                    </tr>
                    <tr style="background: #0D452B; color: #fff; font-size: 11.5px;">
                        <th style="padding: 8px; text-align: right; border: 1px solid rgba(255,255,255,0.2); width: 110px;">مدين</th>
                        <th style="padding: 8px; text-align: right; border: 1px solid rgba(255,255,255,0.2); width: 110px;">دائن</th>
                        <th style="padding: 8px; text-align: right; border: 1px solid rgba(255,255,255,0.2); width: 110px;">مدين</th>
                        <th style="padding: 8px; text-align: right; border: 1px solid rgba(255,255,255,0.2); width: 110px;">دائن</th>
                        <th style="padding: 8px; text-align: right; border: 1px solid rgba(255,255,255,0.2); width: 110px;">مدين</th>
                        <th style="padding: 8px; text-align: right; border: 1px solid rgba(255,255,255,0.2); width: 110px;">دائن</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @php
                            $acc = $row['account'];
                            $isParent = $acc->is_parent;
                            $indent = ($acc->level - 1) * 16;
                        @endphp
                        <tr style="border-bottom: 1px solid var(--border-color); background: {{ $isParent ? '#F8FAFC' : 'transparent' }}; font-weight: {{ $isParent ? '800' : '500' }}; transition: background 0.15s ease;" onmouseover="this.style.background='#F1F5F9'" onmouseout="this.style.background='{{ $isParent ? '#F8FAFC' : 'transparent' }}'">
                            <td style="padding: 10px 12px; border: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: {{ $isParent ? 'var(--primary)' : 'var(--text-muted)' }};">
                                {{ $acc->code }}
                            </td>
                            <td style="padding: 10px 12px; border: 1px solid var(--border-color);">
                                <div style="padding-right: {{ $indent }}px; display: flex; align-items: center; gap: 6px;">
                                    @if($isParent)
                                        <i class="fa-solid fa-folder" style="color: var(--accent-gold); font-size: 13px;"></i>
                                        <span style="color: var(--primary);">{{ $acc->name }}</span>
                                    @else
                                        <i class="fa-regular fa-file-lines" style="color: #94A3B8; font-size: 12px;"></i>
                                        <a href="{{ route('accounting.ledger.index', ['account_id' => $acc->id, 'from_date' => $fromDate, 'to_date' => $toDate]) }}" style="color: var(--text-main); text-decoration: none; font-weight: 700;">
                                            {{ $acc->name }}
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 10px 12px; border: 1px solid var(--border-color); text-align: center; font-size: 11px;">
                                <span style="padding: 2px 6px; border-radius: 4px; background: {{ $acc->nature === 'debit' ? '#D1E7DD' : '#E0E7FF' }}; color: {{ $acc->nature === 'debit' ? '#0F5132' : '#4338CA' }}; font-weight: 700;">
                                    {{ $acc->nature === 'debit' ? 'مدين' : 'دائن' }}
                                </span>
                            </td>

                            <!-- Initial -->
                            <td style="padding: 10px 12px; border: 1px solid var(--border-color); text-align: right; color: {{ $row['init_debit'] > 0 ? '#0F5132' : '#94A3B8' }};">
                                {{ $row['init_debit'] > 0 ? formatMoney($row['init_debit']) : '-' }}
                            </td>
                            <td style="padding: 10px 12px; border: 1px solid var(--border-color); text-align: right; color: {{ $row['init_credit'] > 0 ? '#4338CA' : '#94A3B8' }};">
                                {{ $row['init_credit'] > 0 ? formatMoney($row['init_credit']) : '-' }}
                            </td>

                            <!-- Period -->
                            <td style="padding: 10px 12px; border: 1px solid var(--border-color); text-align: right; color: {{ $row['period_debit'] > 0 ? '#0F5132' : '#94A3B8' }};">
                                {{ $row['period_debit'] > 0 ? formatMoney($row['period_debit']) : '-' }}
                            </td>
                            <td style="padding: 10px 12px; border: 1px solid var(--border-color); text-align: right; color: {{ $row['period_credit'] > 0 ? '#4338CA' : '#94A3B8' }};">
                                {{ $row['period_credit'] > 0 ? formatMoney($row['period_credit']) : '-' }}
                            </td>

                            <!-- Ending -->
                            <td style="padding: 10px 12px; border: 1px solid var(--border-color); text-align: right; font-weight: 800; color: {{ $row['end_debit'] > 0 ? '#0F5132' : '#94A3B8' }};">
                                {{ $row['end_debit'] > 0 ? formatMoney($row['end_debit']) : '-' }}
                            </td>
                            <td style="padding: 10px 12px; border: 1px solid var(--border-color); text-align: right; font-weight: 800; color: {{ $row['end_credit'] > 0 ? '#4338CA' : '#94A3B8' }};">
                                {{ $row['end_credit'] > 0 ? formatMoney($row['end_credit']) : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding: 30px; text-align: center; color: var(--text-muted);">
                                لا توجد حسابات مسجلة في شجرة الحسابات.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: #E2E8F0; font-weight: 900; font-size: 13.5px; border-top: 3px solid var(--primary);">
                        <td colspan="3" style="padding: 14px 16px; text-align: left; color: #0F172A; border: 1px solid #CBD5E1;">
                            الإجمالي العام (للحسابات الفرعية التحليلية):
                        </td>
                        <!-- Initial Totals -->
                        <td style="padding: 14px 12px; text-align: right; color: #0F5132; border: 1px solid #CBD5E1;">
                            {{ formatMoney($totalInitialDebit) }}
                        </td>
                        <td style="padding: 14px 12px; text-align: right; color: #4338CA; border: 1px solid #CBD5E1;">
                            {{ formatMoney($totalInitialCredit) }}
                        </td>

                        <!-- Period Totals -->
                        <td style="padding: 14px 12px; text-align: right; color: #0F5132; border: 1px solid #CBD5E1;">
                            {{ formatMoney($totalPeriodDebit) }}
                        </td>
                        <td style="padding: 14px 12px; text-align: right; color: #4338CA; border: 1px solid #CBD5E1;">
                            {{ formatMoney($totalPeriodCredit) }}
                        </td>

                        <!-- Ending Totals -->
                        <td style="padding: 14px 12px; text-align: right; color: #0F5132; border: 1px solid #CBD5E1;">
                            {{ formatMoney($totalEndingDebit) }}
                        </td>
                        <td style="padding: 14px 12px; text-align: right; color: #4338CA; border: 1px solid #CBD5E1;">
                            {{ formatMoney($totalEndingCredit) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
