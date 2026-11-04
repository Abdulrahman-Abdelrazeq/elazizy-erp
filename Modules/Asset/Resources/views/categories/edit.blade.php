@extends('layouts.app')

@section('title', 'تعديل تصنيف الأصول: ' . $category->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 900px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-shapes"></i>
                </span>
                تعديل تصنيف الأصول: {{ $category->name }}
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديث طريقة الإهلاك، العمر الافتراضي، أو حسابات دفتر الأستاذ العام
            </span>
        </div>

        <a href="{{ route('assets.categories.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة للتصنيفات
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

    <form action="{{ route('assets.categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="erp-card" style="padding: 24px;">
            <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                البيانات الأساسية ومعايير الإهلاك
            </h4>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        اسم تصنيف الأصول <span style="color: red;">*</span>
                    </label>
                    <input type="text" name="name" class="erp-input" value="{{ old('name', $category->name) }}" required>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">كود التصنيف</label>
                    <input type="text" name="code" class="erp-input" value="{{ old('code', $category->code) }}">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        طريقة الإهلاك المحاسبي <span style="color: red;">*</span>
                    </label>
                    <select name="depreciation_method" class="erp-input" required>
                        <option value="straight_line" {{ old('depreciation_method', $category->depreciation_method) === 'straight_line' ? 'selected' : '' }}>القسط الثابت (Straight-Line)</option>
                        <option value="declining_balance" {{ old('depreciation_method', $category->depreciation_method) === 'declining_balance' ? 'selected' : '' }}>القسط المتناقص (Declining Balance)</option>
                        <option value="none" {{ old('depreciation_method', $category->depreciation_method) === 'none' ? 'selected' : '' }}>أصل غير خاضع للإهلاك (مثل الأراضي)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        العمر الافتراضي (سنوات) <span style="color: red;">*</span>
                    </label>
                    <input type="number" step="0.5" min="0.1" max="100" name="useful_life_years" class="erp-input" value="{{ old('useful_life_years', $category->useful_life_years) }}" required>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        النسبة السنوية للإهلاك (%) <span style="color: red;">*</span>
                    </label>
                    <input type="number" step="0.01" min="0" max="100" name="depreciation_rate" class="erp-input" value="{{ old('depreciation_rate', $category->depreciation_rate) }}" required>
                </div>
            </div>

            <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin: 24px 0 18px 0; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                <i class="fa-solid fa-link" style="color: var(--accent-gold);"></i>
                الربط بالحسابات في دفتر الأستاذ العام (General Ledger Integration)
            </h4>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">حساب الأصل (الأصول الثابتة 12XX)</label>
                    <select name="asset_account_id" class="erp-input">
                        <option value="">-- اختياري (تلقائي) --</option>
                        @foreach($assetAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('asset_account_id', $category->asset_account_id) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->code }} - {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">حساب مجمع الإهلاك (Contra Asset)</label>
                    <select name="depreciation_account_id" class="erp-input">
                        <option value="">-- اختياري (تلقائي) --</option>
                        @foreach($assetAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('depreciation_account_id', $category->depreciation_account_id) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->code }} - {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">حساب مصروف الإهلاك (قائمة الدخل 51XX)</label>
                    <select name="depreciation_expense_account_id" class="erp-input">
                        <option value="">-- اختياري (تلقائي) --</option>
                        @foreach($expenseAccounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('depreciation_expense_account_id', $category->depreciation_expense_account_id) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->code }} - {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 18px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الوصف والملاحظات</label>
                <textarea name="description" class="erp-input" rows="2">{{ old('description', $category->description) }}</textarea>
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                <label for="is_active" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">التصنيف نشط ومتاح لإضافة الأصول</label>
            </div>

            <!-- Submit -->
            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border-color);">
                <a href="{{ route('assets.categories.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 10px 20px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-size: 13.5px; font-weight: 800; border-radius: 10px;">
                    <i class="fa-solid fa-save"></i> حفظ التعديلات
                </button>
            </div>
        </div>
    </form>

</div>
@endsection
