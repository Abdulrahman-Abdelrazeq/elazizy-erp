@extends('layouts.app')

@section('title', 'الأقسام والتصنيفات والماركات')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-folder-tree" style="color: var(--primary); margin-left: 8px;"></i>
                إدارة الأقسام والتصنيفات والماركات التجارية
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تنظيم شجرة تصنيف المنتجات والعلامات التجارية
            </span>
        </div>

        <div style="display: flex; gap: 12px;">
            <button type="button" onclick="document.getElementById('brandModal').style.display='flex'" style="background: #FFF; border: 1.5px solid var(--border-color); padding: 9px 18px; border-radius: 10px; font-weight: 700; cursor: pointer; color: var(--text-main); font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-copyright" style="color: var(--accent-gold);"></i>
                إضافة ماركة تجارية
            </button>
            <button type="button" onclick="document.getElementById('categoryModal').style.display='flex'" class="btn-primary-custom">
                <i class="fa-solid fa-plus"></i>
                إضافة قسم جديد
            </button>
        </div>
    </div>

    <!-- Grid: Categories Table & Brands List -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Categories -->
        <div class="erp-card">
            <div class="card-header-custom">
                <h3 class="card-title-custom">
                    <i class="fa-solid fa-tags"></i>
                    دليل الأقسام والتصنيفات الرئيسية والفرعية
                </h3>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 14px;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                            <th style="padding: 12px 16px;">اسم القسم</th>
                            <th style="padding: 12px 16px;">الكود</th>
                            <th style="padding: 12px 16px;">القسم الرئيسي</th>
                            <th style="padding: 12px 16px;">عدد الأصناف</th>
                            <th style="padding: 12px 16px; text-align: center;">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 12px 16px; font-weight: 800; color: var(--text-main);">
                                    <i class="fa-solid fa-folder" style="color: var(--accent-gold); margin-left: 8px;"></i>
                                    {{ $cat->name }}
                                </td>
                                <td style="padding: 12px 16px; font-family: monospace; color: var(--text-muted);">
                                    {{ $cat->code ?? '-' }}
                                </td>
                                <td style="padding: 12px 16px; color: #475569;">
                                    {{ $cat->parent->name ?? 'قسم رئيسي' }}
                                </td>
                                <td style="padding: 12px 16px; font-weight: 700; color: var(--primary);">
                                    {{ $cat->items_count }} صنف
                                </td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    <form action="{{ route('inventory.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('حذف هذا القسم؟')" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #FEE2E2; border: none; color: #DC2626; width: 30px; height: 30px; border-radius: 8px; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 32px; text-align: center; color: var(--text-muted);">
                                    لا توجد أقسام مسجلة. انقر على "إضافة قسم جديد".
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Brands -->
        <div class="erp-card">
            <div class="card-header-custom">
                <h3 class="card-title-custom">
                    <i class="fa-solid fa-copyright"></i>
                    الماركات والعلامات التجارية
                </h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                @forelse($brands as $brand)
                    <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 14px; color: var(--text-main); display: block;">{{ $brand->name }}</strong>
                            <span style="font-size: 11.5px; color: var(--primary); font-weight: 700;">{{ $brand->items_count }} صنف مرتبط</span>
                        </div>
                        <form action="{{ route('inventory.categories.brands.destroy', $brand->id) }}" method="POST" onsubmit="return confirm('حذف الماركة؟')" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: none; border: none; color: #EF4444; cursor: pointer;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                @empty
                    <div style="text-align: center; padding: 24px; color: var(--text-muted); font-size: 13px;">
                        لا توجد ماركات مسجلة.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Modal Create Category -->
<div id="categoryModal" class="theme-modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 800; font-size: 18px; color: var(--text-main);">إضافة قسم جديد</h3>
            <button type="button" onclick="document.getElementById('categoryModal').style.display='none'" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ route('inventory.categories.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اسم القسم *</label>
                <input type="text" name="name" required placeholder="مثال: المواد الغذائية / الإلكترونيات" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">كود القسم</label>
                <input type="text" name="code" placeholder="CAT-01" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">يتبع لقسم رئيسي (اختياري)</label>
                <select name="parent_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                    <option value="">-- قسم رئيسي مستقل --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('categoryModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">إلغاء</button>
                <button type="submit" class="btn-primary-custom">حفظ القسم</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Create Brand -->
<div id="brandModal" class="theme-modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 800; font-size: 18px; color: var(--text-main);">إضافة ماركة تجارية جديدة</h3>
            <button type="button" onclick="document.getElementById('brandModal').style.display='none'" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ route('inventory.categories.brands.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اسم الماركة / الشركة المصنعة *</label>
                <input type="text" name="name" required placeholder="مثال: المراعي / سامسونج / أبل" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('brandModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">إلغاء</button>
                <button type="submit" class="btn-primary-custom">إضافة الماركة</button>
            </div>
        </form>
    </div>
</div>
@endsection

