@extends('layouts.app')

@section('title', 'التحويلات النقدية والبنكية بين الخزائن')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-right-left"></i>
                </span>
                التحويلات المالية الداخلية (Treasury & Bank Transfers)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                نقل وتغذية السيولة النقدية بين الصناديق والحسابات البنكية والمحافظ مع احتساب العمولات
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('treasury.treasuries.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-vault" style="color: var(--accent-gold);"></i>
                الخزائن والحسابات
            </a>
            <a href="{{ route('treasury.transfers.create') }}" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-plus-circle"></i>
                إجراء تحويل مالي جديد
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid var(--primary);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المبالغ المحولة</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ formatMoney($stats['total_transfers']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid #D97706;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الرسوم والمصاريف البنكية</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ formatMoney($stats['total_fees']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid #4338CA;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">عدد عمليات التحويل المنفذة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $stats['transfers_count'] }} عملية</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('treasury.transfers.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 120px 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من خزينة</label>
                <select name="from_treasury_id" class="erp-input">
                    <option value="">جميع الخزائن المصدرة</option>
                    @foreach($treasuries as $t)
                        <option value="{{ $t->id }}" {{ request('from_treasury_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى خزينة</label>
                <select name="to_treasury_id" class="erp-input">
                    <option value="">جميع الخزائن المستلمة</option>
                    @foreach($treasuries as $t)
                        <option value="{{ $t->id }}" {{ request('to_treasury_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="erp-input">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="erp-input">
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>

            <a href="{{ route('treasury.transfers.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 8px; font-size: 12.5px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                إعادة ضبط
            </a>
        </form>
    </div>

    <!-- Transfers Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 130px;">رقم التحويل</th>
                        <th>التاريخ</th>
                        <th>من خزينة / بنك (المصدر)</th>
                        <th>إلى خزينة / بنك (الوجهة)</th>
                        <th>المبلغ المحول</th>
                        <th>رسوم التحويل</th>
                        <th>إجمالي المسحوب</th>
                        <th>البيان والملاحظات</th>
                        <th>الحالة</th>
                        <th>المسؤول</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $trf)
                        <tr>
                            <td>
                                <strong style="font-family: monospace; font-size: 14px; color: var(--primary);">
                                    {{ $trf->transfer_number }}
                                </strong>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; font-weight: 600;">{{ $trf->transfer_date->format('Y-m-d') }}</span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-arrow-up" style="color: #DC2626; font-size: 12px;"></i>
                                    <strong style="color: var(--text-main);">{{ $trf->fromTreasury->name ?? '-' }}</strong>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-arrow-down" style="color: #0F5132; font-size: 12px;"></i>
                                    <strong style="color: var(--text-main);">{{ $trf->toTreasury->name ?? '-' }}</strong>
                                </div>
                            </td>
                            <td>
                                <strong style="font-size: 15px; font-weight: 900; color: var(--primary);">
                                    {{ formatMoney($trf->amount) }}
                                </strong>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: {{ $trf->transfer_fee > 0 ? '#D97706' : 'var(--text-muted)' }};">
                                    {{ formatMoney($trf->transfer_fee) }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: #DC2626;">
                                    -{{ formatMoney($trf->amount + $trf->transfer_fee) }}
                                </strong>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-muted);">{{ $trf->notes ?: 'تحويل مالي داخلي' }}</span>
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 700;">
                                    مكتمل
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--text-muted);">{{ $trf->creator->name ?? 'مستخدم' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                                <i class="fa-solid fa-right-left" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                <span style="font-size: 15px; font-weight: 700; display: block;">لا توجد تحويلات مالية مسجلة</span>
                                <span style="font-size: 13px;">قم بإنشاء تحويل لنقل الأرصدة بين الخزائن والبنوك</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div style="padding: 16px; border-top: 1px solid var(--border-color);">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
