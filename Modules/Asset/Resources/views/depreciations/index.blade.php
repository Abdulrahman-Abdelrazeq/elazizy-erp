@extends('layouts.app')

@section('title', 'قيود وجداول الإهلاك الدوري للأصول (Depreciation Schedules)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-calculator"></i>
                </span>
                قيود وجداول الإهلاك الدوري للأصول (Depreciation Schedules)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تشغيل الإهلاك الشهري والسنوي التلقائي، حساب مجمع الإهلاك، وترحيل القيود لدفتر اليومية العامة (GL)
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('assets.assets.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-building-columns" style="color: var(--primary);"></i>
                سجل الأصول
            </a>
            <a href="{{ route('assets.reports.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-pie" style="color: #4338CA;"></i>
                مصفوفة الإهلاك والتقارير
            </a>
            <a href="{{ route('assets.depreciations.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-play"></i>
                تشغيل دورة إهلاك جديدة
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي حركات الإهلاك</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_runs'] }} حركة</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #EF4444; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #EF4444; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي مبالغ الإهلاك</span>
                <strong style="font-size: 19px; font-weight: 900; color: #DC2626;">{{ number_format($stats['total_amount'], 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">قيود مرحلة للأستاذ العام</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['posted_count'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">قيود بانتظار الترحيل</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['unposted_count'] }}</strong>
            </div>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('assets.depreciations.index') }}" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الأصل</label>
                <select name="asset_id" class="erp-input">
                    <option value="">جميع الأصول</option>
                    @foreach($assets as $ast)
                        <option value="{{ $ast->id }}" {{ request('asset_id') == $ast->id ? 'selected' : '' }}>
                            {{ $ast->name }} ({{ $ast->asset_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">السنة المالية</label>
                <input type="number" name="fiscal_year" class="erp-input" value="{{ request('fiscal_year') }}" placeholder="مثال: 2026">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الفترة المالية</label>
                <input type="text" name="fiscal_period" class="erp-input" value="{{ request('fiscal_period') }}" placeholder="مثال: 2026-11 أو 2026-Q4">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">حالة الترحيل</label>
                <select name="is_posted" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="1" {{ request('is_posted') === '1' ? 'selected' : '' }}>مرحل لليومية العامة</option>
                    <option value="0" {{ request('is_posted') === '0' ? 'selected' : '' }}>غير مرحل</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; font-size: 13px; font-weight: 800; justify-content: center;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>
        </form>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div style="background: #D1E7DD; border: 1px solid #A3CFBB; color: #0F5132; padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
        <i class="fa-solid fa-circle-check" style="margin-left: 8px;"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-left: 8px;"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Depreciations Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-clock-rotate-left" style="color: var(--primary); margin-left: 6px;"></i>
                سجل قيود وإدخالات الإهلاك الدوري ({{ $depreciations->total() }})
            </h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">الأصل الثابت</th>
                        <th style="padding: 12px 16px;">الفرع والتصنيف</th>
                        <th style="padding: 12px 16px;">تاريخ الإهلاك</th>
                        <th style="padding: 12px 16px;">الفترة المالية</th>
                        <th style="padding: 12px 16px;">مبلغ قسط الإهلاك</th>
                        <th style="padding: 12px 16px;">مجمع الإهلاك بعد القيد</th>
                        <th style="padding: 12px 16px;">القيمة الدفترية المتبقية</th>
                        <th style="padding: 12px 16px;">قيد اليومية العامة (GL)</th>
                        <th style="padding: 12px 16px; text-align: center;">الحالة والترحيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($depreciations as $d)
                    <tr style="border-bottom: 1px solid #EDF2F7; transition: background 0.2s ease;">
                        <td style="padding: 14px 16px;">
                            <a href="{{ route('assets.assets.show', $d->asset_id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none; display: block;">
                                {{ $d->asset?->name ?? 'أصل محذوف' }}
                            </a>
                            <small style="color: var(--text-muted); font-family: monospace;">{{ $d->asset?->asset_number }}</small>
                        </td>
                        <td style="padding: 14px 16px;">
                            <strong style="color: var(--text-main); display: block;">{{ $d->asset?->category?->name ?? '—' }}</strong>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-network-wired"></i> {{ $d->asset?->branch?->name ?? '—' }}</small>
                        </td>
                        <td style="padding: 14px 16px; font-weight: 600; color: var(--text-main);">
                            {{ $d->depreciation_date ? $d->depreciation_date->format('Y-m-d') : '—' }}
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="font-weight: 800; color: var(--text-main); display: block;">{{ $d->fiscal_period }}</span>
                            <small style="color: var(--text-muted);">سنة: {{ $d->fiscal_year }}</small>
                        </td>
                        <td style="padding: 14px 16px; font-weight: 800; color: #DC2626;">
                            {{ number_format($d->depreciation_amount, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; color: var(--text-main);">
                            {{ number_format($d->accumulated_depreciation_after, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; font-weight: 900; color: #0F5132;">
                            {{ number_format($d->book_value_after, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px;">
                            @if($d->journalEntry)
                                <a href="{{ route('accounting.journal-entries.show', $d->journal_entry_id) }}" style="font-family: monospace; font-weight: 800; color: #4338CA; text-decoration: none;">
                                    <i class="fa-solid fa-file-invoice"></i> {{ $d->journalEntry->entry_number }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">مسودة غير مرحلة</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            @if($d->is_posted)
                                <span style="background: #D1E7DD; color: #0F5132; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-check-circle"></i> معتمد ومرحل
                                </span>
                            @else
                                <form action="{{ route('assets.depreciations.post-gl', $d->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" style="background: #FEF3C7; border: 1px solid #FDE68A; color: #B45309; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 11.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-cloud-arrow-up"></i> ترحيل الآن
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-calculator" style="font-size: 40px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                            لا توجد قيود إهلاك مسجلة حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($depreciations->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $depreciations->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
