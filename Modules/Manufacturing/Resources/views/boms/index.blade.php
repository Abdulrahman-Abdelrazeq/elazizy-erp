@extends('layouts.app')

@section('title', 'قوائم وفواتير المواد والتراكيب (Bills of Materials - BOM)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-layer-group"></i>
                </span>
                قوائم وفواتير المواد والتراكيب (BOM)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تعريف نسب ومكونات المواد الخام للمنتجات، مراحل ومسارات التشغيل، واحتساب التكاليف المعيارية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('manufacturing.workstations.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-gears" style="color: var(--accent-gold);"></i>
                محطات العمل
            </a>
            <a href="{{ route('manufacturing.orders.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-clipboard-check" style="color: var(--primary);"></i>
                أوامر التصنيع
            </a>
            <a href="{{ route('manufacturing.boms.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-plus-circle"></i>
                إنشاء قائمة مواد (BOM) جديدة
            </a>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي قوائم المواد</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_boms'] }} قائمة</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تراكيب نشطة ومعتمدة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['active_boms'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي قيمة الدفعات المصنعة</span>
                <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['total_cost']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أوامر التشغيل المرتبطة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['orders_count'] }} أمر تشغيل</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('manufacturing.boms.index') }}" style="display: grid; grid-template-columns: 2fr 1fr 100px 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">بحث سريع (اسم التركيبة، رقم الـ BOM، أو اسم المنتج)</label>
                <input type="text" name="search" class="erp-input" value="{{ request('search') }}" placeholder="ابحث بالاسم أو الكود...">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الحالة</label>
                <select name="is_active" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط ومعتمد</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>معطل</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>

            <div>
                <a href="{{ route('manufacturing.boms.index') }}" style="display: block; width: 100%; text-align: center; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- BOMs Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">رقم الـ BOM</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم التركيبة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">المنتج التام المصنع</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">كمية الناتج</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">المواد الخام</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">تكلفة العمالة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">تكلفة الوحدة</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 90px;">الحالة</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 140px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($boms as $bom)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            <a href="{{ route('manufacturing.boms.show', $bom->id) }}" style="color: inherit; text-decoration: none;">
                                {{ $bom->bom_number }}
                            </a>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            <a href="{{ route('manufacturing.boms.show', $bom->id) }}" style="color: inherit; text-decoration: none;">
                                {{ $bom->name }}
                            </a>
                            <span style="display: block; font-size: 11px; font-weight: 600; color: var(--text-muted);">
                                {{ $bom->items->count() }} مكونات خام &bull; {{ $bom->operations->count() }} مراحل تشغيل
                            </span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                            <span style="font-weight: 800; color: var(--text-main); display: block;">{{ $bom->product?->name ?? 'صنف غير محدد' }}</span>
                            <span style="font-family: monospace; font-size: 11px; color: var(--text-muted);">SKU: {{ $bom->product?->sku ?? '-' }}</span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ $bom->output_quantity }} {{ $bom->unit?->name ?? ($bom->product?->unit?->name ?? 'قطعة') }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ formatMoney($bom->raw_material_cost) }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: #D97706;">
                            {{ formatMoney($bom->labor_cost + $bom->overhead_cost) }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: #0F5132; font-size: 14px;">
                            {{ formatMoney($bom->cost_per_unit) }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($bom->is_active)
                                <span style="background: #D1E7DD; color: #0F5132; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">نشط</span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">معطل</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('manufacturing.boms.show', $bom->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="عرض التفاصيل">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('manufacturing.boms.edit', $bom->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="تعديل">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="{{ route('manufacturing.boms.duplicate', $bom->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; background: #E0E7FF; color: #4338CA; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="تكرار كنسخة جديدة">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('manufacturing.boms.destroy', $bom->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف قائمة المواد هذه؟')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="حذف">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-layer-group" style="font-size: 36px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                            لا توجد قوائم مواد مسجلة. اضغط "إنشاء قائمة مواد (BOM) جديدة" للبدء.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($boms->hasPages())
            <div style="margin-top: 20px;">
                {{ $boms->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
