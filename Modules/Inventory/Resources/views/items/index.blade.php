@extends('layouts.app')

@section('title', 'دليل الأصناف والمنتجات')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Summary & Actions Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-boxes-stacked" style="color: var(--primary); margin-left: 8px;"></i>
                دليل الأصناف والمخزون العام
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدارة المنتجات، الأسعار المتعددة، أرصدة المستودعات، وطباعة ملصقات الباركود
            </span>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('inventory.transfers.create') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-right-left" style="color: var(--accent-gold);"></i>
                تحويل مخزني
            </a>
            <a href="{{ route('inventory.items.create') }}" class="btn-primary-custom">
                <i class="fa-solid fa-plus"></i>
                إضافة صنف جديد
            </a>
        </div>
    </div>

    <!-- Inventory Quick Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الأصناف المسجلة</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--text-main);">{{ $stats['total_items'] }} صنف</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أصناف بلغت حد الطلب</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ $stats['low_stock'] }} صنف</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أصناف نفذ رصيدها</span>
                <strong style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ $stats['out_of_stock'] }} صنف</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #CCFBF1; color: #0F766E; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي قيمة المخزون الحالية</span>
                <strong style="font-size: 17px; font-weight: 900; color: var(--primary);">{{ formatMoney($stats['total_valuation']) }}</strong>
            </div>
        </div>
    </div>

    <!-- Search & Filters Bar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form action="{{ route('inventory.items.index') }}" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 240px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الصنف، الباركود، أو كود SKU..." style="width: 100%; padding: 10px 38px 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                <i class="fa-solid fa-search" style="position: absolute; right: 14px; top: 13px; color: #94A3B8;"></i>
            </div>

            <div style="flex: 1; min-width: 180px;">
                <select name="category_id" onchange="this.form.submit()" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                    <option value="">-- كل الأقسام والتصنيفات --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px 20px;">
                <i class="fa-solid fa-filter"></i>
                تصفية
            </button>

            @if(request()->anyFilled(['search', 'category_id']))
                <a href="{{ route('inventory.items.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 10px 16px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 600; font-size: 13.5px; display: flex; align-items: center;">
                    إعادة ضبط
                </a>
            @endif
        </form>
    </div>

    <!-- Items Datatable Card -->
    <div class="erp-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13.5px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                        <th style="padding: 12px 16px;">الباركود / SKU</th>
                        <th style="padding: 12px 16px;">اسم الصنف</th>
                        <th style="padding: 12px 16px;">القسم</th>
                        <th style="padding: 12px 16px;">الوحدة</th>
                        <th style="padding: 12px 16px;">سعر التكلفة</th>
                        <th style="padding: 12px 16px;">سعر البيع</th>
                        <th style="padding: 12px 16px;">الرصيد المتاح</th>
                        <th style="padding: 12px 16px;">حالة المخزون</th>
                        <th style="padding: 12px 16px; text-align: center;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        @php
                            $status = $item->stockStatus();
                            $qty = $item->totalQuantity();
                        @endphp
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 16px; font-family: monospace; font-size: 12.5px; color: var(--text-muted);">
                                <strong style="color: var(--text-main); font-size: 13px;">{{ $item->barcode }}</strong>
                                <span style="display: block; font-size: 10.5px;">{{ $item->sku }}</span>
                            </td>
                            <td style="padding: 12px 16px; font-weight: 700; color: var(--text-main); font-size: 14px;">
                                {{ $item->name }}
                                @if($item->is_service)
                                    <span style="background: #E0E7FF; color: #4338CA; font-size: 10.5px; padding: 2px 6px; border-radius: 6px; margin-right: 4px;">خدمي</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px; color: var(--text-muted);">
                                {{ $item->category->name ?? 'عام' }}
                            </td>
                            <td style="padding: 12px 16px; font-weight: 600;">
                                {{ $item->unit->name ?? 'قطعة' }}
                            </td>
                            <td style="padding: 12px 16px; color: var(--text-muted);">
                                {{ formatMoney($item->buy_price) }}
                            </td>
                            <td style="padding: 12px 16px; font-weight: 800; color: var(--primary); font-size: 14.5px;">
                                {{ formatMoney($item->sell_price) }}
                            </td>
                            <td style="padding: 12px 16px; font-weight: 800; font-size: 14px;">
                                @if($item->is_service)
                                    <span style="color: #64748B;">∞</span>
                                @else
                                    {{ number_format($qty, 0) }} {{ $item->unit->name ?? '' }}
                                @endif
                            </td>
                            <td style="padding: 12px 16px;">
                                @if($status['status'] === 'in_stock')
                                    <span style="background: #D1E7DD; color: #0F5132; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                        <i class="fa-solid fa-check"></i> متوفر
                                    </span>
                                @elseif($status['status'] === 'low_stock')
                                    <span style="background: #FEF3C7; color: #92400E; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> منخفض
                                    </span>
                                @elseif($status['status'] === 'out_of_stock')
                                    <span style="background: #FEE2E2; color: #991B1B; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                        <i class="fa-solid fa-circle-xmark"></i> نفذ
                                    </span>
                                @else
                                    <span style="background: #E0E7FF; color: #4338CA; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                        خدمة
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 8px;">
                                    <a href="{{ route('inventory.items.barcode', $item->id) }}" target="_blank" style="background: #FEF3C7; color: #D97706; width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="طباعة ملصق الباركود">
                                        <i class="fa-solid fa-barcode"></i>
                                    </a>
                                    <a href="{{ route('inventory.items.edit', $item->id) }}" style="background: var(--primary-light); color: var(--primary); width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('inventory.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟')" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #FEE2E2; border: none; color: #DC2626; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding: 36px; text-align: center; color: var(--text-muted);">
                                لا توجد أصناف مطابقة للبحث. انقر على "إضافة صنف جديد" لإدخال أول صنف.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div style="margin-top: 20px;">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

