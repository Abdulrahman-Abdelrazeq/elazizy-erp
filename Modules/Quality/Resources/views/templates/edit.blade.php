@extends('layouts.app')

@section('title', 'تعديل نموذج فحص: ' . $template->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px; max-width: 900px; margin: 0 auto;">

    <!-- Top Breadcrumbs / Title -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <a href="{{ route('quality.templates.index') }}" style="color: var(--primary); text-decoration: none;">نماذج الفحص</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i>
                <a href="{{ route('quality.templates.show', $template->id) }}" style="color: var(--primary); text-decoration: none;">{{ $template->name }}</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i>
                <span>تعديل</span>
            </div>
            <h2 style="font-size: 20px; font-weight: 900; color: var(--text-main); margin: 0;">
                تعديل نموذج الفحص القياسي ({{ $template->code }})
            </h2>
        </div>

        <a href="{{ route('quality.templates.show', $template->id) }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i>
            عرض بطاقة النموذج
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div style="background: #FEF2F2; border: 1px solid #F87171; border-radius: 10px; padding: 14px 18px; color: #991B1B; font-size: 13px;">
            <div style="font-weight: 800; margin-bottom: 6px;">يرجى مراجعة الأخطاء التالية:</div>
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('quality.templates.update', $template->id) }}">
        @csrf
        @method('PUT')

        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-sliders"></i>
                بيانات نموذج الفحص القياسي
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">كود النموذج</label>
                    <input type="text" value="{{ $template->code }}" disabled style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-family: monospace; background: #F1F5F9; color: var(--text-muted);">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        اسم النموذج / قائمة التحقق <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        نوع ومرحلة الفحص <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="type" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="incoming" {{ old('type', $template->type) == 'incoming' ? 'selected' : '' }}>فحص استلام مواد خام (Incoming QC)</option>
                        <option value="in_process" {{ old('type', $template->type) == 'in_process' ? 'selected' : '' }}>فحص أثناء التشغيل والتصنيع (In-Process QC)</option>
                        <option value="final_product" {{ old('type', $template->type) == 'final_product' ? 'selected' : '' }}>فحص المنتج النهائي التام (Final Product QC)</option>
                        <option value="project_site" {{ old('type', $template->type) == 'project_site' ? 'selected' : '' }}>فحص واختبار موقع المشروع (Site Quality Test)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        ربط بصنف محدد
                    </label>
                    <select name="item_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- فحص عام لعدة أصناف --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id', $template->item_id) == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->sku }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        ربط بتصنيف أصناف
                    </label>
                    <select name="category_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- بدون تصنيف محدد --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $template->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; align-items: center; gap: 10px; margin-top: 24px;">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <label for="is_active" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                        النموذج نشط ومعتمد للاستخدام
                    </label>
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                    وصف وملاحظات ومواصفة الفحص
                </label>
                <textarea name="description" rows="3" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">{{ old('description', $template->description) }}</textarea>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('quality.templates.show', $template->id) }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; color: var(--text-main); font-weight: 700; font-size: 14px; text-decoration: none;">
                إلغاء
            </a>
            <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 800; border: none; cursor: pointer;">
                <i class="fa-solid fa-save"></i>
                حفظ التعديلات
            </button>
        </div>
    </form>

</div>
@endsection
