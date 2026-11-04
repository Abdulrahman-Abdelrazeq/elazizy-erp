@extends('layouts.app')

@section('title', 'تصنيفات وفئات الأصول الثابتة (Asset Categories)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-shapes"></i>
                </span>
                تصنيفات وفئات الأصول الثابتة (Asset Categories)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد طرق الإهلاك المحاسبي، نسب الإهلاك السنوية، وربط الفئات بشجرة الحسابات العامة
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('assets.assets.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-building-columns" style="color: var(--primary);"></i>
                سجل الأصول
            </a>
            <a href="{{ route('assets.depreciations.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calculator" style="color: var(--accent-gold);"></i>
                قيود وجداول الإهلاك
            </a>
            <a href="{{ route('assets.categories.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة تصنيف أصول جديد
            </a>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي التصنيفات</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_categories'] }} تصنيف</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تصنيفات نشطة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['active_categories'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-cubes"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الأصول المسجلة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $stats['total_assets'] }} أصل</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">التكلفة التاريخية الإجمالية</span>
                <strong style="font-size: 19px; font-weight: 900; color: #D97706;">{{ number_format($stats['total_gross_val'], 2) }} ر.س</strong>
            </div>
        </div>
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

    <!-- Categories Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-list-check" style="color: var(--primary); margin-left: 6px;"></i>
                قائمة تصنيفات الأصول الثابتة وطرق الإهلاك
            </h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">اسم التصنيف</th>
                        <th style="padding: 12px 16px;">الكود</th>
                        <th style="padding: 12px 16px;">طريقة الإهلاك</th>
                        <th style="padding: 12px 16px; text-align: center;">النسبة السنوية</th>
                        <th style="padding: 12px 16px; text-align: center;">العمر الافتراضي</th>
                        <th style="padding: 12px 16px;">حساب مجمع الإهلاك</th>
                        <th style="padding: 12px 16px; text-align: center;">الأصول التابعة</th>
                        <th style="padding: 12px 16px;">صافي القيمة الدفترية</th>
                        <th style="padding: 12px 16px; text-align: center;">الحالة</th>
                        <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr style="border-bottom: 1px solid #EDF2F7; transition: background 0.2s ease;">
                        <td style="padding: 14px 16px; font-weight: 800; color: var(--text-main);">
                            {{ $cat->name }}
                        </td>
                        <td style="padding: 14px 16px; font-family: monospace; font-weight: 700; color: var(--text-muted);">
                            {{ $cat->code ?: '—' }}
                        </td>
                        <td style="padding: 14px 16px; font-weight: 600; color: var(--text-main);">
                            {{ $cat->depreciation_method_label }}
                        </td>
                        <td style="padding: 14px 16px; text-align: center; font-weight: 800; color: var(--primary);">
                            {{ number_format($cat->depreciation_rate, 1) }}%
                        </td>
                        <td style="padding: 14px 16px; text-align: center; font-weight: 700; color: var(--text-muted);">
                            {{ $cat->useful_life_years }} سنوات
                        </td>
                        <td style="padding: 14px 16px; font-size: 12px; color: var(--text-muted);">
                            {{ $cat->depreciationAccount?->name ? ($cat->depreciationAccount->code . ' - ' . $cat->depreciationAccount->name) : 'تلقائي (شجرة الحسابات)' }}
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            <span style="background: #F1F5F9; color: var(--text-main); font-weight: 800; padding: 4px 10px; border-radius: 8px; font-size: 12px;">
                                {{ $cat->assets_count }} أصل
                            </span>
                        </td>
                        <td style="padding: 14px 16px; font-weight: 800; color: #0F5132;">
                            {{ number_format($cat->total_book_value, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            @if($cat->is_active)
                                <span style="background: #D1E7DD; color: #0F5132; font-weight: 700; padding: 3px 8px; border-radius: 6px; font-size: 11.5px;">نشط</span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; font-weight: 700; padding: 3px 8px; border-radius: 6px; font-size: 11.5px;">معطل</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('assets.categories.edit', $cat->id) }}" title="تعديل" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if($cat->assets_count == 0)
                                    <form action="{{ route('assets.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('تأكيد حذف تصنيف الأصول؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="حذف" style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-shapes" style="font-size: 40px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                            لا توجد تصنيفات أصول مسجلة حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
