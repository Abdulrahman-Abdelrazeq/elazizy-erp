@extends('layouts.app')

@section('title', 'إضافة فرع / مركز توزيع جديد (Add Branch)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-plus-circle"></i>
                </span>
                إضافة فرع / مركز توزيع جديد
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد الموقع الجغرافي، تصنيف الفرع، المدير المسؤول، والموازنة التقديرية
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('branches.branches.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع لدليل الفروع
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('branches.branches.store') }}" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <!-- General Info Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-building"></i> البيانات الأساسية للفرع
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اسم الفرع / المركز *</label>
                    <input type="text" name="name" class="erp-input" placeholder="مثال: فرع الرياض - العليا" value="{{ old('name') }}" required>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">كود الفرع</label>
                    <input type="text" name="code" class="erp-input" placeholder="تلقائي: BR-002" value="{{ old('code') }}">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">نوع وتصنيف الفرع *</label>
                    <select name="branch_type" class="erp-input" required>
                        <option value="retail" {{ old('branch_type') === 'retail' ? 'selected' : '' }}>فرع مبيعات وتجزئة (معرض)</option>
                        <option value="wholesale" {{ old('branch_type') === 'wholesale' ? 'selected' : '' }}>فرع مبيعات جملة وتوزيع</option>
                        <option value="distribution_center" {{ old('branch_type') === 'distribution_center' ? 'selected' : '' }}>مركز توزيع إقليمي (Hub)</option>
                        <option value="warehouse_only" {{ old('branch_type') === 'warehouse_only' ? 'selected' : '' }}>مستودع تخزين مركزي فقط</option>
                        <option value="hq" {{ old('branch_type') === 'hq' ? 'selected' : '' }}>المقر الرئيسي (HQ)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الفرع الأب / التابع له</label>
                    <select name="parent_id" class="erp-input">
                        <option value="">بدون فرع أب (مستقل / رئيسي)</option>
                        @foreach($parentBranches as $pb)
                            <option value="{{ $pb->id }}" {{ old('parent_id') == $pb->id ? 'selected' : '' }}>{{ $pb->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">المدير المسؤول عن الفرع</label>
                    <input type="text" name="manager_name" class="erp-input" placeholder="اسم مدير الفرع" value="{{ old('manager_name') }}">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">رقم هاتف الفرع</label>
                    <input type="text" name="phone" class="erp-input" placeholder="05xxxxxxxx" value="{{ old('phone') }}">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">البريد الإلكتروني للفرع</label>
                    <input type="email" name="email" class="erp-input" placeholder="branch@elazizy.com" value="{{ old('email') }}">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الموازنة التقديرية الشهرية (SAR)</label>
                    <input type="number" step="0.01" min="0" name="monthly_budget" class="erp-input" value="{{ old('monthly_budget', '0.00') }}">
                </div>
            </div>
        </div>

        <!-- Location & Legal Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 800; color: #D97706; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-map-location-dot"></i> الموقع الجغرافي والبيانات النظامية
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">المدينة</label>
                    <input type="text" name="city" class="erp-input" placeholder="الرياض / جدة / الدمام..." value="{{ old('city') }}">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الحي / المنطقة</label>
                    <input type="text" name="district" class="erp-input" placeholder="حي العليا / الملز..." value="{{ old('district') }}">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">السجل التجاري الفرعي (إن وجد)</label>
                    <input type="text" name="commercial_registration" class="erp-input" placeholder="رقم السجل التجاري" value="{{ old('commercial_registration') }}">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الرقم الضريبي الفرعي</label>
                    <input type="text" name="tax_number" class="erp-input" placeholder="الرقم الضريبي 15 رقم" value="{{ old('tax_number') }}">
                </div>
            </div>

            <div style="margin-top: 16px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">العنوان التفصيلي</label>
                <textarea name="address" class="erp-input" rows="2" placeholder="الشارع، رقم المبنى، الرمز البريدي...">{{ old('address') }}</textarea>
            </div>

            <div style="display: flex; gap: 24px; margin-top: 18px; flex-wrap: wrap;">
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="is_main" value="1" {{ old('is_main') ? 'checked' : '' }}>
                    تعيين كفرع رئيسي للشركة (HQ)
                </label>
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    تفعيل الفرع للعمليات التشغيلية
                </label>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('branches.branches.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; text-decoration: none; color: var(--text-muted); font-size: 14px; font-weight: 700;">
                إلغاء
            </a>
            <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; border-radius: 10px; font-size: 14px; font-weight: 800; cursor: pointer; border: none;">
                <i class="fa-solid fa-check"></i> حفظ بيانات الفرع
            </button>
        </div>

    </form>

</div>
@endsection
