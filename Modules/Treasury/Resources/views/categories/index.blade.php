@extends('layouts.app')

@section('title', 'بنود وتصنيفات المصروفات والإيرادات')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-tags"></i>
                </span>
                بنود وتصنيفات المصروفات والإيرادات (Expense & Revenue Categories)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                شجرة تصنيف أوجه الصرف العمومية والإدارية والتشغيلية والإيرادات المتنوعة
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('treasury.vouchers.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice-dollar" style="color: var(--accent-gold);"></i>
                سجل السندات المالية
            </a>
            <button type="button" onclick="openCreateCategoryModal()" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة بند تصنيف جديد
            </button>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">بنود المصروفات المسجلة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #DC2626;">{{ $stats['total_expenses'] }} بند</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">بنود الإيرادات المتنوعة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['total_revenues'] }} بند</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-sitemap"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">التصنيفات الرئيسية (الأب)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $parents->count() }} تصنيف</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('treasury.categories.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px 100px; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    بحث باسم البند أو الكود
                </label>
                <input type="text" name="search" value="{{ request('search') }}" class="erp-input" placeholder="اسم البند...">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    نوع البند
                </label>
                <select name="type" class="erp-input">
                    <option value="">جميع الأنواع</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>مصروفات عمومية</option>
                    <option value="revenue" {{ request('type') === 'revenue' ? 'selected' : '' }}>إيرادات متنوعة</option>
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i>
                تصفية
            </button>

            <a href="{{ route('treasury.categories.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 8px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                إعادة ضبط
            </a>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 90px;">الكود</th>
                        <th>اسم البند / التصنيف</th>
                        <th>النوع</th>
                        <th>التصنيف الأب</th>
                        <th>الوصف والبيان</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 120px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td>
                                <span class="badge" style="background: #F1F5F9; color: var(--text-main); font-weight: 700; font-family: monospace;">
                                    {{ $cat->code ?: 'CAT-' . $cat->id }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 14px; color: var(--text-main);">{{ $cat->name }}</strong>
                            </td>
                            <td>
                                @if($cat->type === 'expense')
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">
                                        <i class="fa-solid fa-arrow-trend-down"></i> مصروفات
                                    </span>
                                @else
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">
                                        <i class="fa-solid fa-arrow-trend-up"></i> إيرادات
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--text-muted);">
                                    {{ $cat->parent->name ?? 'تصنيف رئيسي' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-muted);">{{ $cat->description ?: '—' }}</span>
                            </td>
                            <td>
                                @if($cat->is_active)
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 700;">نشط</span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">معطل</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button type="button" 
                                            onclick="openEditCategoryModal({{ json_encode($cat) }})"
                                            title="تعديل"
                                            style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('treasury.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا البند؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="حذف"
                                                style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                                <i class="fa-solid fa-tags" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                <span style="font-size: 15px; font-weight: 700; display: block;">لا توجد بنود مصروفات أو إيرادات</span>
                                <span style="font-size: 13px;">قم بإضافة البنود لتصنيف المدفوعات والمقبوضات بدقة</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div style="padding: 16px; border-top: 1px solid var(--border-color);">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Create Category -->
<div id="createCategoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-plus-circle" style="color: var(--accent-gold);"></i>
                إضافة بند تصنيف جديد
            </h4>
            <button type="button" onclick="closeCreateCategoryModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('treasury.categories.store') }}" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم البند / التصنيف <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" class="erp-input" placeholder="مثال: إيجار المقر، فواتير كهرباء" required>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        كود البند
                    </label>
                    <input type="text" name="code" class="erp-input" placeholder="تلقائي أو كود دفتري">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        النوع <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="type" class="erp-input" required>
                        <option value="expense">مصروفات عمومية وتشغيلية</option>
                        <option value="revenue">إيرادات ومقبوضات متنوعة</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        التصنيف الأب (اختياري)
                    </label>
                    <select name="parent_id" class="erp-input">
                        <option value="">تصنيف رئيسي (بدون أب)</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    الوصف والتفاصيل
                </label>
                <textarea name="description" rows="2" class="erp-input" placeholder="شرح تفاصيل هذا البند..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeCreateCategoryModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-weight: 800;">
                    <i class="fa-solid fa-check-circle"></i>
                    حفظ البند
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Category -->
<div id="editCategoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--accent-gold);"></i>
                تعديل بند التصنيف
            </h4>
            <button type="button" onclick="closeEditCategoryModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form id="editCategoryForm" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم البند / التصنيف <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" id="edit_cat_name" class="erp-input" required>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        كود البند
                    </label>
                    <input type="text" name="code" id="edit_cat_code" class="erp-input">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        النوع <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="type" id="edit_cat_type" class="erp-input" required>
                        <option value="expense">مصروفات عمومية وتشغيلية</option>
                        <option value="revenue">إيرادات ومقبوضات متنوعة</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        التصنيف الأب
                    </label>
                    <select name="parent_id" id="edit_cat_parent_id" class="erp-input">
                        <option value="">تصنيف رئيسي (بدون أب)</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" id="edit_cat_is_active" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                <label for="edit_cat_is_active" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    البند مفعل وجاهز للاستخدام
                </label>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    الوصف
                </label>
                <textarea name="description" id="edit_cat_description" rows="2" class="erp-input"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeEditCategoryModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-weight: 800;">
                    <i class="fa-solid fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateCategoryModal() {
        document.getElementById('createCategoryModal').style.display = 'flex';
    }
    function closeCreateCategoryModal() {
        document.getElementById('createCategoryModal').style.display = 'none';
    }

    function openEditCategoryModal(c) {
        const form = document.getElementById('editCategoryForm');
        form.action = "{{ url('treasury/categories') }}/" + c.id;
        document.getElementById('edit_cat_name').value = c.name;
        document.getElementById('edit_cat_code').value = c.code || '';
        document.getElementById('edit_cat_type').value = c.type;
        document.getElementById('edit_cat_parent_id').value = c.parent_id || '';
        document.getElementById('edit_cat_description').value = c.description || '';
        document.getElementById('edit_cat_is_active').checked = c.is_active ? true : false;
        document.getElementById('editCategoryModal').style.display = 'flex';
    }
    function closeEditCategoryModal() {
        document.getElementById('editCategoryModal').style.display = 'none';
    }
</script>
@endsection
