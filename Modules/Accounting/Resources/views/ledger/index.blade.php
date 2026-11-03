@extends('layouts.app')

@section('title', 'دفتر الأستاذ العام (General Ledger)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-book-open"></i>
                </span>
                دفتر الأستاذ العام وكشف الحركات (General Ledger)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                استعراض تفصيلي لحركات الحسابات المالية، أرصدة أول المدة، الحركات المدينة والدائنة، والرصيد التراكمي
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('accounting.accounts.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-sitemap" style="color: var(--accent-gold);"></i>
                شجرة الحسابات
            </a>
            <a href="{{ route('accounting.trial_balance.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-scale-balanced" style="color: #4338CA;"></i>
                ميزان المراجعة
            </a>
            <button onclick="window.print()" style="background: var(--primary); color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة كشف الأستاذ
            </button>
        </div>
    </div>

    <!-- Filters & Account Selector -->
    <div class="erp-card" style="padding: 20px; margin-bottom: 0;">
        <form method="GET" action="{{ route('accounting.ledger.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px 100px; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">
                    الحساب المالي المراد فحصه <span style="color: #EF4444;">*</span>
                </label>
                <select name="account_id" class="erp-input" required style="font-weight: 800; color: var(--primary);">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ ($selectedAccount && $selectedAccount->id == $acc->id) ? 'selected' : '' }}>
                            {{ $acc->code }} - {{ $acc->name }} ({{ $acc->nature === 'debit' ? 'مدين' : 'دائن' }})
                        </option>
                    @endforeach
                </select>
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
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="from_date" class="erp-input" value="{{ $fromDate }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" class="erp-input" value="{{ $toDate }}">
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> عرض
                </button>
            </div>

            <div>
                <a href="{{ route('accounting.ledger.index') }}" style="display: block; width: 100%; text-align: center; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    @if($selectedAccount)
    <!-- Account Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">رصيد أول المدة (افتتاحي)</span>
                <strong style="font-size: 19px; font-weight: 900; color: var(--primary);">{{ formatMoney($openingBalance) }}</strong>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">قبل تاريخ {{ $fromDate }}</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-arrow-down-left"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي حركات المدين (الفترة)</span>
                <strong style="font-size: 19px; font-weight: 900; color: #0F5132;">{{ formatMoney($totalDebit) }}</strong>
                <span style="font-size: 11px; color: #10B981; display: block;">حركات واردة / زيادة أصول</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-arrow-up-right"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي حركات الدائن (الفترة)</span>
                <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ formatMoney($totalCredit) }}</strong>
                <span style="font-size: 11px; color: #4338CA; display: block;">حركات منصرفة / التزامات</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">الرصيد الختامي بعد الحركات</span>
                <strong style="font-size: 19px; font-weight: 900; color: #D97706;">{{ formatMoney($closingBalance) }}</strong>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">حتى تاريخ {{ $toDate }}</span>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-numeric"></i>
                حركات الحساب:
                <span style="font-family: monospace; color: var(--text-main);">[{{ $selectedAccount->code }}]</span>
                <span>{{ $selectedAccount->name }}</span>
                <span style="font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 6px; background: {{ $selectedAccount->nature === 'debit' ? '#D1E7DD' : '#E0E7FF' }}; color: {{ $selectedAccount->nature === 'debit' ? '#0F5132' : '#4338CA' }};">
                    {{ $selectedAccount->nature === 'debit' ? 'طبيعة الحساب: مدين (Debit)' : 'طبيعة الحساب: دائن (Credit)' }}
                </span>
            </h3>

            <span style="font-size: 12.5px; color: var(--text-muted); font-weight: 700;">
                الفترة من: {{ $fromDate }} إلى {{ $toDate }}
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 45px;">#</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">التاريخ</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">رقم القيد</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">المرجع</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">مركز التكلفة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">البيان والشرح التفصيلي</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">مدين (+)</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">دائن (-)</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الرصيد التراكمي</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Row 0: Opening Balance -->
                    <tr style="background: #FEF3C7; font-weight: 800; border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 12px; text-align: center; color: #D97706;">-</td>
                        <td style="padding: 12px; color: #92400E;">{{ $fromDate }}</td>
                        <td style="padding: 12px; color: #92400E;">-</td>
                        <td style="padding: 12px; color: #92400E;">-</td>
                        <td style="padding: 12px; color: #92400E;">-</td>
                        <td style="padding: 12px; color: #92400E;">رصيد ما قبل الفترة (رصيد أول المدة المنقول)</td>
                        <td style="padding: 12px; text-align: right; color: #92400E;">-</td>
                        <td style="padding: 12px; text-align: right; color: #92400E;">-</td>
                        <td style="padding: 12px; text-align: right; color: #92400E; font-size: 14px;">
                            {{ formatMoney($openingBalance) }}
                        </td>
                    </tr>

                    @php
                        $running = $openingBalance;
                    @endphp

                    @forelse($lines as $idx => $line)
                        @php
                            if ($selectedAccount->nature === 'debit') {
                                $running = $running + (float)$line->debit - (float)$line->credit;
                            } else {
                                $running = $running + (float)$line->credit - (float)$line->debit;
                            }
                        @endphp
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 12px; text-align: center; font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border-color);">
                                {{ $idx + 1 }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-main);">
                                {{ \Carbon\Carbon::parse($line->journalEntry->date)->format('Y-m-d') }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800;">
                                <a href="{{ route('accounting.journal-entries.show', $line->journalEntry->id) }}" style="color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                    {{ $line->journalEntry->entry_number }}
                                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px;"></i>
                                </a>
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                                {{ $line->journalEntry->reference ?: '-' }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-muted);">
                                @if($line->costCenter)
                                    <span style="background: rgba(217, 119, 6, 0.1); color: #B45309; padding: 2px 6px; border-radius: 4px; font-weight: 700;">
                                        {{ $line->costCenter->name }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-main);">
                                {{ $line->description ?: $line->journalEntry->description }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: right; font-weight: 800; font-size: 13.5px; color: {{ $line->debit > 0 ? '#0F5132' : '#94A3B8' }};">
                                {{ $line->debit > 0 ? formatMoney($line->debit) : '-' }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: right; font-weight: 800; font-size: 13.5px; color: {{ $line->credit > 0 ? '#4338CA' : '#94A3B8' }};">
                                {{ $line->credit > 0 ? formatMoney($line->credit) : '-' }}
                            </td>
                            <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: right; font-weight: 900; font-size: 14px; color: {{ $running >= 0 ? 'var(--primary)' : '#DC2626' }};">
                                {{ formatMoney($running) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding: 30px; text-align: center; color: var(--text-muted); font-size: 13px;">
                                <i class="fa-solid fa-inbox" style="font-size: 28px; display: block; margin-bottom: 8px; opacity: 0.5;"></i>
                                لا توجد حركات مقيدة لهذا الحساب خلال الفترة المحددة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background: #F1F5F9; font-weight: 900; font-size: 14px;">
                        <td colspan="6" style="padding: 14px; text-align: left; color: var(--text-main);">
                            مجموع حركات الفترة والرصيد الختامي:
                        </td>
                        <td style="padding: 14px; text-align: right; color: #0F5132; font-size: 15px;">
                            {{ formatMoney($totalDebit) }}
                        </td>
                        <td style="padding: 14px; text-align: right; color: #4338CA; font-size: 15px;">
                            {{ formatMoney($totalCredit) }}
                        </td>
                        <td style="padding: 14px; text-align: right; color: #D97706; font-size: 16px;">
                            {{ formatMoney($closingBalance) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

