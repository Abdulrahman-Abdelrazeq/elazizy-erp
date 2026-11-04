@extends('layouts.app')

@section('title', 'تسجيل وبطاقة أصل ثابت جديد')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1000px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-cube"></i>
                </span>
                تسجيل وبطاقة أصل ثابت جديد (New Fixed Asset)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدخال التكلفة التاريخية، قيمة الخردة، ربط الفرع والموظف العهدة، وتحديد معايير الإهلاك
            </span>
        </div>

        <a href="{{ route('assets.assets.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لسجل الأصول
        </a>
    </div>

    @if (isset($errors) && $errors->any())
    <div style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 14px 18px; border-radius: 10px; font-size: 13px; font-weight: 700;">
        <ul style="margin: 0; padding-right: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('assets.assets.store') }}" method="POST">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Basic Asset Info Card -->
            <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
                <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                    البيانات التعريفية والتشغيلية للأصل
                </h4>

                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 18px; margin-bottom: 18px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            اسم الأصل والمعدّة <span style="color: red;">*</span>
                        </label>
                        <input type="text" name="name" class="erp-input" value="{{ old('name') }}" placeholder="مثال: سيارة نقل إيسوزو، ماكينة CNC ليزر، خادم Dell PowerEdge..." required>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">رقم / كود الأصل</label>
                        <input type="text" name="asset_number" class="erp-input" value="{{ old('asset_number', $suggestedNumber) }}" placeholder="AST-2026-0001">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الباركود (Barcode)</label>
                        <input type="text" name="barcode" class="erp-input" value="{{ old('barcode') }}" placeholder="كود الباركود للملصق">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 18px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            تصنيف الأصل <span style="color: red;">*</span>
                        </label>
                        <select name="category_id" id="category_id" class="erp-input" required onchange="updateCategoryDefaults(this)">
                            <option value="">-- اختر تصنيف الأصل --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}
                                    data-method="{{ $cat->depreciation_method }}"
                                    data-rate="{{ $cat->depreciation_rate }}"
                                    data-life="{{ $cat->useful_life_years }}">
                                    {{ $cat->name }} ({{ $cat->depreciation_method_label }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            الفرع التابع له <span style="color: red;">*</span>
                        </label>
                        <select name="branch_id" class="erp-input" required>
                            <option value="">-- اختر الفرع --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id', activeBranch()?->id) == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الموظف المستلم (العهدة)</label>
                        <select name="custodian_employee_id" class="erp-input">
                            <option value="">-- عهدة عامة / بدون موظف مخصص --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('custodian_employee_id') == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->name }} ({{ $emp->emp_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الموقع الجغرافي / المبنى</label>
                        <select name="location_id" class="erp-input">
                            <option value="">-- اختياري --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الرقم التسلسلي (Serial Number)</label>
                        <input type="text" name="serial_number" class="erp-input" value="{{ old('serial_number') }}" placeholder="S/N: 98124912...">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">رقم الموديل / الطراز</label>
                        <input type="text" name="model_number" class="erp-input" value="{{ old('model_number') }}" placeholder="Model / Part #">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">المورد / جهة الشراء</label>
                        <select name="supplier_id" class="erp-input">
                            <option value="">-- اختياري --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
                                    {{ $sup->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">تاريخ انتهاء الضمان</label>
                        <input type="date" name="warranty_expiry" class="erp-input" value="{{ old('warranty_expiry') }}">
                    </div>
                </div>
            </div>

            <!-- Financials & Depreciation Card -->
            <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
                <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    <i class="fa-solid fa-calculator" style="color: var(--accent-gold);"></i>
                    التكاليف المالية وطريقة الإهلاك المحاسبي
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 18px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            تكلفة الشراء التاريخية (ر.س) <span style="color: red;">*</span>
                        </label>
                        <input type="number" step="0.01" min="0.01" name="purchase_cost" id="purchase_cost" class="erp-input" value="{{ old('purchase_cost') }}" required oninput="calculateDepreciableCost()">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">قيمة الخردة / النفاية المتبقية (ر.س)</label>
                        <input type="number" step="0.01" min="0" name="salvage_value" id="salvage_value" class="erp-input" value="{{ old('salvage_value', 0.00) }}" oninput="calculateDepreciableCost()">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            تاريخ الشراء <span style="color: red;">*</span>
                        </label>
                        <input type="date" name="purchase_date" class="erp-input" value="{{ old('purchase_date', date('Y-m-d')) }}" required>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            تاريخ بدء التشغيل والخدمة <span style="color: red;">*</span>
                        </label>
                        <input type="date" name="in_service_date" class="erp-input" value="{{ old('in_service_date', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 18px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">طريقة الإهلاك</label>
                        <select name="depreciation_method" id="depreciation_method" class="erp-input">
                            <option value="straight_line" {{ old('depreciation_method') === 'straight_line' ? 'selected' : '' }}>القسط الثابت (Straight-Line)</option>
                            <option value="declining_balance" {{ old('depreciation_method') === 'declining_balance' ? 'selected' : '' }}>القسط المتناقص (Declining Balance)</option>
                            <option value="none" {{ old('depreciation_method') === 'none' ? 'selected' : '' }}>أصل غير خاضع للإهلاك</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">العمر الافتراضي (سنوات)</label>
                        <input type="number" step="0.5" min="0.1" max="100" name="useful_life_years" id="useful_life_years" class="erp-input" value="{{ old('useful_life_years', 5) }}">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">النسبة السنوية للإهلاك (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="depreciation_rate" id="depreciation_rate" class="erp-input" value="{{ old('depreciation_rate', 20.00) }}">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">مركز التكلفة (Cost Center)</label>
                        <select name="cost_center_id" class="erp-input">
                            <option value="">-- اختياري --</option>
                            @foreach($costCenters as $cc)
                                <option value="{{ $cc->id }}" {{ old('cost_center_id') == $cc->id ? 'selected' : '' }}>
                                    {{ $cc->code }} - {{ $cc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">ملاحظات وشروط الأصل</label>
                    <textarea name="notes" class="erp-input" rows="2" placeholder="أي بيانات أو تعليمات تشغيلية خاصة...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('assets.assets.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; font-size: 14px; font-weight: 800; border-radius: 10px;">
                    <i class="fa-solid fa-save"></i> حفظ وتسجيل بطاقة الأصل
                </button>
            </div>

        </div>
    </form>

</div>

<script>
function updateCategoryDefaults(selectEl) {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    if (!selectedOption) return;

    const method = selectedOption.getAttribute('data-method');
    const rate = selectedOption.getAttribute('data-rate');
    const life = selectedOption.getAttribute('data-life');

    if (method) document.getElementById('depreciation_method').value = method;
    if (rate) document.getElementById('depreciation_rate').value = rate;
    if (life) document.getElementById('useful_life_years').value = life;
}

function calculateDepreciableCost() {
    // optional live dynamic calculation
}
</script>
@endsection
