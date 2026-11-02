@extends('layouts.app')

@section('title', 'تسويات الجرد والعجز والهالك')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-clipboard-check" style="color: var(--primary); margin-left: 8px;"></i>
                تسويات الجرد المخزني ومحاضر الهالك والتالف
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تسجيل الفروقات الناتجة عن الجرد الفعلي للمستودعات وتعديل الأرصدة تلقائياً
            </span>
        </div>

        <a href="{{ route('inventory.adjustments.create') }}" class="btn-primary-custom">
            <i class="fa-solid fa-plus"></i>
            تسجيل تسوية جرد جديدة
        </a>
    </div>

    <!-- Adjustments Table Card -->
    <div class="erp-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 14px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                        <th style="padding: 14px 18px;">رقم المحضر</th>
                        <th style="padding: 14px 18px;">التاريخ</th>
                        <th style="padding: 14px 18px;">المستودع</th>
                        <th style="padding: 14px 18px;">نوع التسوية</th>
                        <th style="padding: 14px 18px;">عدد الأصناف</th>
                        <th style="padding: 14px 18px;">المسؤول</th>
                        <th style="padding: 14px 18px;">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 18px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                {{ $adj->adjustment_number }}
                            </td>
                            <td style="padding: 14px 18px; color: var(--text-muted);">
                                {{ $adj->date ? $adj->date->format('Y-m-d') : '-' }}
                            </td>
                            <td style="padding: 14px 18px; font-weight: 700;">
                                {{ $adj->store->name ?? '-' }}
                            </td>
                            <td style="padding: 14px 18px;">
                                @if($adj->type === 'addition')
                                    <span style="background: #D1E7DD; color: #0F5132; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                        <i class="fa-solid fa-plus-circle"></i> زيادة جردية (إضافة)
                                    </span>
                                @elseif($adj->type === 'deficit')
                                    <span style="background: #FEF3C7; color: #92400E; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                        <i class="fa-solid fa-minus-circle"></i> عجز جردي (خصم)
                                    </span>
                                @else
                                    <span style="background: #FEE2E2; color: #991B1B; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                        <i class="fa-solid fa-trash-can"></i> هالك وتالف (شطب)
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px; font-weight: 700;">
                                {{ $adj->items->count() }} صنف
                            </td>
                            <td style="padding: 14px 18px; color: var(--text-muted);">
                                {{ $adj->creator->name ?? 'النظام' }}
                            </td>
                            <td style="padding: 14px 18px; color: var(--text-muted); font-size: 13px;">
                                {{ $adj->notes ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 36px; text-align: center; color: var(--text-muted);">
                                لا توجد محاضر تسويات جرد سابقة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($adjustments->hasPages())
            <div style="margin-top: 20px;">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

