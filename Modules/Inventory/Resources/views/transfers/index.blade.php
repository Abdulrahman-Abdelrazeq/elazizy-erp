@extends('layouts.app')

@section('title', 'أذونات التحويل بين المستودعات')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-right-left" style="color: var(--primary); margin-left: 8px;"></i>
                أذونات التحويل المخزني بين المستودعات
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تتبع حركة نقل البضائع والأصناف بين مستودعات الفروع والمخازن المركزية
            </span>
        </div>

        <a href="{{ route('inventory.transfers.create') }}" class="btn-primary-custom">
            <i class="fa-solid fa-plus"></i>
            إنشاء إذن تحويل جديد
        </a>
    </div>

    <!-- Transfers Table Card -->
    <div class="erp-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 14px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                        <th style="padding: 14px 18px;">رقم الإذن</th>
                        <th style="padding: 14px 18px;">التاريخ</th>
                        <th style="padding: 14px 18px;">من مستودع</th>
                        <th style="padding: 14px 18px;">إلى مستودع</th>
                        <th style="padding: 14px 18px;">عدد الأصناف</th>
                        <th style="padding: 14px 18px;">المسؤول</th>
                        <th style="padding: 14px 18px;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $tr)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 18px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                {{ $tr->transfer_number }}
                            </td>
                            <td style="padding: 14px 18px; color: var(--text-muted);">
                                {{ $tr->date ? $tr->date->format('Y-m-d') : '-' }}
                            </td>
                            <td style="padding: 14px 18px; font-weight: 700; color: #DC2626;">
                                <i class="fa-solid fa-arrow-up-from-bracket fa-xs" style="margin-left: 4px;"></i>
                                {{ $tr->fromStore->name ?? '-' }}
                            </td>
                            <td style="padding: 14px 18px; font-weight: 700; color: #0F5132;">
                                <i class="fa-solid fa-arrow-down-to-bracket fa-xs" style="margin-left: 4px;"></i>
                                {{ $tr->toStore->name ?? '-' }}
                            </td>
                            <td style="padding: 14px 18px; font-weight: 700;">
                                {{ $tr->items->count() }} صنف
                            </td>
                            <td style="padding: 14px 18px; color: var(--text-muted);">
                                {{ $tr->creator->name ?? 'النظام' }}
                            </td>
                            <td style="padding: 14px 18px;">
                                <span style="background: #D1E7DD; color: #0F5132; padding: 4px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                    <i class="fa-solid fa-check"></i> مكتمل ومرحل
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 36px; text-align: center; color: var(--text-muted);">
                                لا توجد تحويلات مخزنية سابقة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
            <div style="margin-top: 20px;">
                {{ $transfers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

