@extends('layouts.app')

@section('title', 'أرصدة وجرد: ' . $store->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-boxes-packing" style="color: var(--primary); margin-left: 8px;"></i>
                جرد وأرصدة: {{ $store->name }}
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                الفرع: {{ $store->branch->name ?? '-' }} | كود المستودع: {{ $store->code }} | أمين المستودع: {{ $store->keeper_name ?? 'غير محدد' }}
            </span>
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('inventory.stores.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة لقائمة المستودعات
            </a>
            <a href="{{ route('inventory.transfers.create') }}" class="btn-primary-custom">
                <i class="fa-solid fa-right-left"></i>
                تحويل أصناف من هذا المستودع
            </a>
        </div>
    </div>

    <!-- Stock Table Card -->
    <div class="erp-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 14px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                        <th style="padding: 14px 18px;">الصنف</th>
                        <th style="padding: 14px 18px;">الباركود / SKU</th>
                        <th style="padding: 14px 18px;">القسم</th>
                        <th style="padding: 14px 18px;">الوحدة</th>
                        <th style="padding: 14px 18px;">الكمية المتوفرة بالمستودع</th>
                        <th style="padding: 14px 18px;">متوسط التكلفة</th>
                        <th style="padding: 14px 18px;">إجمالي قيمة الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stocks as $stock)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 18px; font-weight: 800; color: var(--text-main);">
                                {{ $stock->item->name ?? '-' }}
                            </td>
                            <td style="padding: 14px 18px; font-family: monospace; font-size: 12.5px; color: var(--text-muted);">
                                {{ $stock->item->barcode ?? '-' }}
                            </td>
                            <td style="padding: 14px 18px; color: var(--text-muted);">
                                {{ $stock->item->category->name ?? 'عام' }}
                            </td>
                            <td style="padding: 14px 18px;">
                                {{ $stock->item->unit->name ?? 'قطعة' }}
                            </td>
                            <td style="padding: 14px 18px; font-weight: 900; font-size: 15px; color: var(--primary);">
                                {{ number_format($stock->quantity, 0) }}
                            </td>
                            <td style="padding: 14px 18px; color: var(--text-muted);">
                                {{ formatMoney($stock->avg_cost) }}
                            </td>
                            <td style="padding: 14px 18px; font-weight: 800; font-size: 14.5px; color: var(--text-main);">
                                {{ formatMoney($stock->quantity * $stock->avg_cost) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 36px; text-align: center; color: var(--text-muted);">
                                لا توجد أرصدة أصناف مسجلة داخل هذا المستودع حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stocks->hasPages())
            <div style="margin-top: 20px;">
                {{ $stocks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

