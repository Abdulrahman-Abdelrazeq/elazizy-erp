@extends('layouts.app')

@section('title', 'إصدار أمر تصنيع وتشغيل جديد (New Work Order)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-clipboard-plus"></i>
                </span>
                إصدار أمر تصنيع وتشغيل جديد (Work Order)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد قائمة المواد، كمية الإنتاج المستهدفة، مخازن الصرف والاستلام، والجدول الزمني للتشغيل
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('manufacturing.orders.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع لأوامر التصنيع
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('manufacturing.orders.store') }}" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <!-- Order Information Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-sliders"></i> بيانات وخيارات أمر التشغيل
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">قائمة المواد المعتمدة (BOM) *</label>
                    <select name="bom_id" id="bom_id" class="erp-input" required onchange="onBomSelect()">
                        <option value="">-- اختر قائمة المواد (BOM) --</option>
                        @foreach($boms as $b)
                            <option value="{{ $b->id }}" data-output="{{ $b->output_quantity }}" data-cost="{{ $b->cost_per_unit }}" data-unit="{{ $b->unit?->name ?? ($b->product?->unit?->name ?? 'وحدة') }}" data-prod="{{ $b->product?->name }}" {{ request('bom_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->bom_number }} &bull; {{ $b->name }} ({{ $b->product?->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الكمية المستهدف إنتاجها *</label>
                    <input type="number" step="0.001" min="0.001" name="planned_quantity" id="planned_quantity" class="erp-input" value="1.000" required oninput="recalcOrderPreview()">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">مخزن سحب المواد الخام (From Store) *</label>
                    <select name="from_store_id" class="erp-input" required>
                        <option value="">-- اختر مخزن الصرف --</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ $st->is_main ? 'selected' : '' }}>{{ $st->name }} ({{ $st->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">مخزن استلام المنتج التام (To Store) *</label>
                    <select name="to_store_id" class="erp-input" required>
                        <option value="">-- اختر مخزن الإنتاج التام --</option>
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}" {{ $st->is_main ? 'selected' : '' }}>{{ $st->name }} ({{ $st->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الفرع</label>
                    <select name="branch_id" class="erp-input">
                        <option value="">المركز الرئيسي</option>
                        @foreach($branches as $br)
                            <option value="{{ $br->id }}">{{ $br->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">تاريخ بدء أمر التشغيل *</label>
                    <input type="date" name="order_date" class="erp-input" value="{{ date('Y-m-d') }}" required>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">تاريخ الإنجاز المتوقع</label>
                    <input type="date" name="expected_completion_date" class="erp-input" value="{{ date('Y-m-d', strtotime('+3 days')) }}">
                </div>
            </div>

            <div style="margin-top: 16px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">ملاحظات وتعليمات التشغيل للمصنع</label>
                <textarea name="notes" class="erp-input" rows="2" placeholder="أية تعليمات خاصة بالفنيين أو مواصفات إضافية لأمر الإنتاج..."></textarea>
            </div>
        </div>

        <!-- Live Estimate & Component Preview Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; background: #F8FAFC; border: 1.5px dashed var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px;">
                <h4 style="font-size: 15px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-calculator"></i> التقدير المالي للإنتاج والمواد الخام
                </h4>
                <div style="font-size: 13px; font-weight: 700; color: var(--text-main);">
                    المنتج: <span id="previewProdName" style="color: var(--primary);">-</span>
                    &bull; الوحدة: <span id="previewUnitName">-</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; padding: 16px; background: #fff; border-radius: 10px; border: 1px solid var(--border-color);">
                <div>
                    <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تكلفة الوحدة المعيارية:</span>
                    <strong id="previewUnitCost" style="font-size: 17px; font-weight: 900; color: #0F5132;">0.00 SAR</strong>
                </div>
                <div>
                    <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">الكمية المستهدفة:</span>
                    <strong id="previewTargetQty" style="font-size: 17px; font-weight: 900; color: var(--text-main);">1.00</strong>
                </div>
                <div>
                    <span style="font-size: 11.5px; font-weight: 700; color: var(--primary); display: block;">إجمالي التكلفة المتوقعة:</span>
                    <strong id="previewTotalCost" style="font-size: 20px; font-weight: 900; color: var(--primary);">0.00 SAR</strong>
                </div>
            </div>

            <div style="margin-top: 14px; font-size: 12.5px; color: var(--text-muted);">
                <i class="fa-solid fa-circle-info" style="color: var(--accent-gold); margin-left: 4px;"></i>
                يقوم النظام تلقائياً بنسخ المواد الخام من تركيبة الـ BOM واحتساب الكميات المطلوبة بدقة بنسبة وتناسب مع الكمية المستهدفة.
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('manufacturing.orders.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; text-decoration: none; color: var(--text-muted); font-size: 14px; font-weight: 700;">
                إلغاء
            </a>
            <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; border-radius: 10px; font-size: 14px; font-weight: 800; cursor: pointer; border: none;">
                <i class="fa-solid fa-check"></i> حفظ وإصدار أمر التصنيع
            </button>
        </div>

    </form>

</div>

<script>
function onBomSelect() {
    const sel = document.getElementById('bom_id');
    const opt = sel.options[sel.selectedIndex];

    if (opt && opt.value) {
        document.getElementById('previewProdName').innerText = opt.dataset.prod;
        document.getElementById('previewUnitName').innerText = opt.dataset.unit;
        document.getElementById('previewUnitCost').innerText = parseFloat(opt.dataset.cost).toFixed(2) + ' SAR';
        recalcOrderPreview();
    } else {
        document.getElementById('previewProdName').innerText = '-';
        document.getElementById('previewUnitName').innerText = '-';
        document.getElementById('previewUnitCost').innerText = '0.00 SAR';
        document.getElementById('previewTotalCost').innerText = '0.00 SAR';
    }
}

function recalcOrderPreview() {
    const sel = document.getElementById('bom_id');
    const opt = sel.options[sel.selectedIndex];
    const qty = parseFloat(document.getElementById('planned_quantity').value) || 0;
    document.getElementById('previewTargetQty').innerText = qty.toFixed(2);

    if (opt && opt.value) {
        const unitCost = parseFloat(opt.dataset.cost) || 0;
        const total = qty * unitCost;
        document.getElementById('previewTotalCost').innerText = total.toFixed(2) + ' SAR';
    }
}

window.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('bom_id').value) {
        onBomSelect();
    }
});
</script>
@endsection
