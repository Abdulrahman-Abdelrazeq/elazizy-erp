@extends('layouts.app')

@section('title', 'إصدار أمر فحص جودة جديد')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px; max-width: 1000px; margin: 0 auto;">

    <!-- Top Breadcrumbs / Title -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <a href="{{ route('quality.inspections.index') }}" style="color: var(--primary); text-decoration: none;">أوامر الفحص</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i>
                <span>أمر فحص جديد</span>
            </div>
            <h2 style="font-size: 20px; font-weight: 900; color: var(--text-main); margin: 0;">
                إصدار أمر فحص واختبار جودة جديد (Quality Inspection Order)
            </h2>
        </div>

        <a href="{{ route('quality.inspections.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لسجل الفحوصات
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

    <form method="POST" action="{{ route('quality.inspections.store') }}">
        @csrf

        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-clipboard-check"></i>
                1. البيانات الأساسية لأمر الفحص
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        رقم أمر الفحص <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="inspection_number" value="{{ old('inspection_number', $suggestedNumber) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-family: monospace; background: #F8FAFC;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        نوع ومرحلة الفحص <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="type" id="inspection_type" required onchange="toggleContextFields()" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="incoming" {{ old('type', $preselectedType) == 'incoming' ? 'selected' : '' }}>فحص استلام مواد خام (Incoming QC)</option>
                        <option value="in_process" {{ old('type', $preselectedType) == 'in_process' ? 'selected' : '' }}>فحص أثناء التشغيل والتصنيع (In-Process QC)</option>
                        <option value="final_product" {{ old('type', $preselectedType) == 'final_product' ? 'selected' : '' }}>فحص المنتج النهائي التام (Final Product QC)</option>
                        <option value="project_site" {{ old('type', $preselectedType) == 'project_site' ? 'selected' : '' }}>فحص واختبار موقع المشروع (Site Quality Test)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        الفرع المسؤول <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="branch_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        نموذج وقائمة الفحص المعتمدة
                    </label>
                    <select name="template_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- بدون نموذج محدد مسبقاً --</option>
                        @foreach($templates as $tmpl)
                            <option value="{{ $tmpl->id }}" {{ old('template_id') == $tmpl->id ? 'selected' : '' }}>{{ $tmpl->code }} - {{ $tmpl->name }} ({{ $tmpl->items->count() }} معيار)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        مفتش / مهندس الجودة المسؤول <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="inspector_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- اختر المهندس/المفتش --</option>
                        @foreach($inspectors as $ins)
                            <option value="{{ $ins->id }}" {{ old('inspector_id') == $ins->id ? 'selected' : '' }}>{{ $ins->name }} ({{ $ins->job_title ?? 'موظف' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        تاريخ الفحص <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date" name="inspection_date" value="{{ old('inspection_date', date('Y-m-d')) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>
            </div>
        </div>

        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-boxes-stacked"></i>
                2. تفاصيل الصنف والدفعة ومصدر التوريد
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        الصنف الخاضع للفحص
                    </label>
                    <select name="item_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- اختياري: اختر الصنف --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id', $preselectedItem) == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->sku }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="supplier-wrapper">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        المورد (في فحص الاستلام)
                    </label>
                    <select name="supplier_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- بدون مورد محدد --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="mfg-wrapper">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        أمر التصنيع والتشغيل (إن وجد)
                    </label>
                    <select name="manufacturing_order_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- بدون أمر تصنيع --</option>
                        @foreach($manufacturingOrders as $mo)
                            <option value="{{ $mo->id }}" {{ old('manufacturing_order_id') == $mo->id ? 'selected' : '' }}>{{ $mo->order_number }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="project-wrapper">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        مشروع المقاولة (لفحص الموقع)
                    </label>
                    <select name="project_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- بدون مشروع محدد --</option>
                        @foreach($projects as $prj)
                            <option value="{{ $prj->id }}" {{ old('project_id') == $prj->id ? 'selected' : '' }}>{{ $prj->code }} - {{ $prj->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        رقم التشغيلة / الدفعة (Batch / Lot)
                    </label>
                    <input type="text" name="batch_number" value="{{ old('batch_number') }}" placeholder="مثال: BATCH-2026-X12" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        إجمالي كمية الدفعة <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="number" step="0.001" name="total_quantity" id="total_qty" value="{{ old('total_quantity', '100.000') }}" required oninput="calculateSampleSize()" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        حجم العينة المسحوبة للفحص <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="number" step="0.001" name="sample_size" id="sample_size" value="{{ old('sample_size', '10.000') }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <span style="font-size: 11px; color: var(--text-muted);">عينة عشوائية ممثلة للدفعة (AQL)</span>
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                    ملاحظات وتعليمات الفحص
                </label>
                <textarea name="notes" rows="2" placeholder="أي شروط خاصة بعملية الفحص وسحب العينات..." style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('quality.inspections.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; color: var(--text-main); font-weight: 700; font-size: 14px; text-decoration: none;">
                إلغاء
            </a>
            <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 800; border: none; cursor: pointer;">
                <i class="fa-solid fa-save"></i>
                إصدار أمر الفحص وبدء الفحص الميداني
            </button>
        </div>
    </form>

</div>

<script>
function calculateSampleSize() {
    const total = parseFloat(document.getElementById('total_qty').value) || 0;
    const sampleInput = document.getElementById('sample_size');
    if (total > 0 && (!sampleInput.value || parseFloat(sampleInput.value) === 0)) {
        // Default 10% sampling rule
        sampleInput.value = Math.max(1, Math.round(total * 0.1));
    }
}

function toggleContextFields() {
    const type = document.getElementById('inspection_type').value;
    const supWrap = document.getElementById('supplier-wrapper');
    const mfgWrap = document.getElementById('mfg-wrapper');
    const prjWrap = document.getElementById('project-wrapper');

    if (type === 'incoming') {
        supWrap.style.display = 'block';
        mfgWrap.style.display = 'none';
        prjWrap.style.display = 'none';
    } else if (type === 'in_process' || type === 'final_product') {
        supWrap.style.display = 'none';
        mfgWrap.style.display = 'block';
        prjWrap.style.display = 'none';
    } else if (type === 'project_site') {
        supWrap.style.display = 'none';
        mfgWrap.style.display = 'none';
        prjWrap.style.display = 'block';
    }
}
document.addEventListener('DOMContentLoaded', toggleContextFields);
</script>
@endsection
