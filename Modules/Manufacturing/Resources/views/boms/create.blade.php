@extends('layouts.app')

@section('title', 'إنشاء قائمة مواد وتراكيب جديدة (New BOM)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-plus-circle"></i>
                </span>
                إنشاء قائمة مواد وتراكيب جديدة (New BOM)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد المكونات الخام، نسب الهدر، مراحل التشغيل الصناعية، وحساب تكلفة التصنيع المعيارية
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('manufacturing.boms.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع لقوائم المواد
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('manufacturing.boms.store') }}" id="bomForm" style="display: flex; flex-direction: column; gap: 20px;">
        @csrf

        <!-- Card 1: Basic Information -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-info-circle"></i> البيانات الأساسية لقائمة المواد
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اسم التركيبة / قائمة المواد *</label>
                    <input type="text" name="name" class="erp-input" placeholder="مثال: تركيبة تصنيع طاولة خشبية فاخرة" value="{{ old('name') }}" required>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">المنتج تام الصنع (الناتج النهائي) *</label>
                    <select name="product_id" id="product_id" class="erp-input" required onchange="updateProductDefaults()">
                        <option value="">-- اختر المنتج المصنع --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" data-cost="{{ $p->avg_cost ?: $p->buy_price }}" data-unit="{{ $p->unit?->name }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} (SKU: {{ $p->sku ?: '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">كمية الناتج للدفعة الواحدة *</label>
                    <input type="number" step="0.001" min="0.001" name="output_quantity" id="output_quantity" class="erp-input" value="{{ old('output_quantity', '1.000') }}" required oninput="recalcAll()">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الوحدة</label>
                    <select name="unit_id" class="erp-input">
                        <option value="">نفس وحدة الصنف الافتراضية</option>
                        @foreach($units as $u)
                            <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">التكاليف الإضافية غير المباشرة (Overheads) SAR</label>
                    <input type="number" step="0.01" min="0" name="overhead_cost" id="overhead_cost" class="erp-input" value="{{ old('overhead_cost', '0.00') }}" oninput="recalcAll()">
                </div>
            </div>

            <div style="display: flex; gap: 24px; margin-top: 18px; flex-wrap: wrap;">
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="is_default" value="1" checked>
                    تعيين كتركيبة افتراضية لهذا الصنف
                </label>
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="update_item_cost" value="1" checked>
                    تحديث سعر تكلفة الصنف في كارت الصنف تلقائياً بسعر الوحدة المحتسب
                </label>
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    تفعيل التركيبة
                </label>
            </div>
        </div>

        <!-- Card 2: Raw Materials Table -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-boxes-stacked"></i> مكونات المواد الخام الداخلة في التركيب
                </h3>
                <button type="button" onclick="addMaterialRow()" class="btn-primary-custom" style="padding: 8px 16px; border-radius: 8px; font-size: 12.5px; font-weight: 800; border: none; cursor: pointer;">
                    <i class="fa-solid fa-plus"></i> إضافة مادة خام
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;" id="materialsTable">
                    <thead>
                        <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                            <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">المادة الخام / الصنف *</th>
                            <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الكمية المطلوبة *</th>
                            <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">تكلفة الوحدة (SAR)</th>
                            <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">نسبة الهدر (%)</th>
                            <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 150px;">إجمالي التكلفة</th>
                            <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 60px;">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="materialsBody">
                        <!-- Dynamic Material Rows -->
                    </tbody>
                    <tfoot>
                        <tr style="background: #F8FAFC; font-weight: 800;">
                            <td colspan="4" style="padding: 12px 10px; text-align: left; color: var(--text-main);">إجمالي تكلفة المواد الخام:</td>
                            <td id="totalMaterialCostText" style="padding: 12px 10px; color: #0F5132; font-size: 15px; font-weight: 900;">0.00 SAR</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Card 3: Production Routing & Operations -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 800; color: #D97706; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-route"></i> مراحل وعمليات التشغيل ومحطات العمل (Routing)
                </h3>
                <button type="button" onclick="addOperationRow()" style="background: #FEF3C7; color: #D97706; border: 1px solid #FDE68A; padding: 8px 16px; border-radius: 8px; font-size: 12.5px; font-weight: 800; cursor: pointer;">
                    <i class="fa-solid fa-plus"></i> إضافة مرحلة تشغيل
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;" id="operationsTable">
                    <thead>
                        <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                            <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم العملية / المرحلة</th>
                            <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 220px;">محطة العمل / خط الإنتاج</th>
                            <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الوقت القياسي (دقيقة)</th>
                            <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 150px;">تكلفة المرحلة (SAR)</th>
                            <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 60px;">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="operationsBody">
                        <!-- Dynamic Operations Rows -->
                    </tbody>
                    <tfoot>
                        <tr style="background: #F8FAFC; font-weight: 800;">
                            <td colspan="3" style="padding: 12px 10px; text-align: left; color: var(--text-main);">إجمالي تكلفة العمالة والتشغيل:</td>
                            <td id="totalLaborCostText" style="padding: 12px 10px; color: #D97706; font-size: 15px; font-weight: 900;">0.00 SAR</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Card 4: Financial Summary & Submit -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; background: linear-gradient(135deg, rgba(15, 81, 50, 0.03) 0%, rgba(6, 46, 32, 0.08) 100%); border: 2px solid var(--primary);">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 20px;">
                <div>
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">تكلفة المواد الخام:</span>
                    <strong id="sumMaterial" style="font-size: 18px; font-weight: 900; color: var(--text-main);">0.00 SAR</strong>
                </div>
                <div>
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">تكلفة العمليات والعمالة:</span>
                    <strong id="sumLabor" style="font-size: 18px; font-weight: 900; color: #D97706;">0.00 SAR</strong>
                </div>
                <div>
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">التكاليف غير المباشرة:</span>
                    <strong id="sumOverhead" style="font-size: 18px; font-weight: 900; color: #4338CA;">0.00 SAR</strong>
                </div>
                <div>
                    <span style="font-size: 12px; font-weight: 700; color: var(--primary); display: block;">إجمالي تكلفة الدفعة:</span>
                    <strong id="sumTotal" style="font-size: 22px; font-weight: 900; color: var(--primary);">0.00 SAR</strong>
                </div>
                <div style="border-right: 2px solid var(--border-color); padding-right: 16px;">
                    <span style="font-size: 12px; font-weight: 800; color: #0F5132; display: block;">تكلفة الوحدة المنتجة الواحدة:</span>
                    <strong id="unitCost" style="font-size: 24px; font-weight: 900; color: #0F5132;">0.00 SAR</strong>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('manufacturing.boms.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; text-decoration: none; color: var(--text-muted); font-size: 14px; font-weight: 700;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; border-radius: 10px; font-size: 14px; font-weight: 800; cursor: pointer; border: none;">
                    <i class="fa-solid fa-check"></i> حفظ قائمة المواد (BOM)
                </button>
            </div>
        </div>

    </form>

</div>

<!-- JSON Data for Materials and Workstations -->
<script>
const rawMaterialsList = @json($rawMaterials);
const workstationsList = @json($workstations);
let materialIndex = 0;
let operationIndex = 0;

function addMaterialRow(itemId = '', qty = 1, unitCost = '', scrap = 0) {
    const tbody = document.getElementById('materialsBody');
    const tr = document.createElement('tr');
    tr.id = 'matRow_' + materialIndex;
    tr.style.borderBottom = '1px solid var(--border-color)';

    let options = '<option value="">-- اختر مادة خام --</option>';
    rawMaterialsList.forEach(m => {
        const cost = m.avg_cost > 0 ? m.avg_cost : m.buy_price;
        const selected = (m.id == itemId) ? 'selected' : '';
        options += `<option value="${m.id}" data-cost="${cost}" ${selected}>${m.name} (التكلفة: ${cost} SAR)</option>`;
    });

    tr.innerHTML = `
        <td style="padding: 8px 10px;">
            <select name="items[${materialIndex}][item_id]" class="erp-input mat-select" required onchange="onMaterialChange(${materialIndex})">
                ${options}
            </select>
        </td>
        <td style="padding: 8px 10px;">
            <input type="number" step="0.001" min="0.001" name="items[${materialIndex}][quantity]" id="matQty_${materialIndex}" class="erp-input" value="${qty}" required oninput="calcMaterialRow(${materialIndex})">
        </td>
        <td style="padding: 8px 10px;">
            <input type="number" step="0.01" min="0" name="items[${materialIndex}][unit_cost]" id="matCost_${materialIndex}" class="erp-input" value="${unitCost}" required oninput="calcMaterialRow(${materialIndex})">
        </td>
        <td style="padding: 8px 10px;">
            <input type="number" step="0.1" min="0" max="100" name="items[${materialIndex}][scrap_percentage]" id="matScrap_${materialIndex}" class="erp-input" value="${scrap}" oninput="calcMaterialRow(${materialIndex})">
        </td>
        <td style="padding: 8px 10px;">
            <input type="text" id="matTotal_${materialIndex}" class="erp-input" readonly style="background: #F8FAFC; font-weight: 800; color: #0F5132;" value="0.00">
        </td>
        <td style="padding: 8px 10px; text-align: center;">
            <button type="button" onclick="removeRow('matRow_${materialIndex}')" style="background: #FEE2E2; color: #DC2626; border: none; width: 30px; height: 30px; border-radius: 6px; cursor: pointer;">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    if (itemId) {
        onMaterialChange(materialIndex, unitCost);
    }
    materialIndex++;
    recalcAll();
}

function onMaterialChange(idx, defaultCost = null) {
    const row = document.getElementById('matRow_' + idx);
    const select = row.querySelector('.mat-select');
    const costInput = document.getElementById('matCost_' + idx);
    const selectedOpt = select.options[select.selectedIndex];

    if (defaultCost !== null && defaultCost > 0) {
        costInput.value = defaultCost;
    } else if (selectedOpt && selectedOpt.dataset.cost) {
        costInput.value = selectedOpt.dataset.cost;
    }
    calcMaterialRow(idx);
}

function calcMaterialRow(idx) {
    const qty = parseFloat(document.getElementById('matQty_' + idx).value) || 0;
    const cost = parseFloat(document.getElementById('matCost_' + idx).value) || 0;
    const scrap = parseFloat(document.getElementById('matScrap_' + idx).value) || 0;

    const total = qty * cost * (1 + (scrap / 100));
    document.getElementById('matTotal_' + idx).value = total.toFixed(2);
    recalcAll();
}

function addOperationRow(opName = '', wsId = '', time = 10, opCost = '') {
    const tbody = document.getElementById('operationsBody');
    const tr = document.createElement('tr');
    tr.id = 'opRow_' + operationIndex;
    tr.style.borderBottom = '1px solid var(--border-color)';

    let wsOptions = '<option value="">-- اختياري: محطة العمل --</option>';
    workstationsList.forEach(ws => {
        const selected = (ws.id == wsId) ? 'selected' : '';
        wsOptions += `<option value="${ws.id}" data-hourly="${ws.hourly_cost}" ${selected}>${ws.name} (${ws.hourly_cost} SAR/س)</option>`;
    });

    tr.innerHTML = `
        <td style="padding: 8px 10px;">
            <input type="text" name="operations[${operationIndex}][operation_name]" class="erp-input" placeholder="مثال: مرحلة التجميع الآلي" value="${opName}">
        </td>
        <td style="padding: 8px 10px;">
            <select name="operations[${operationIndex}][workstation_id]" class="erp-input op-ws-select" onchange="onWsChange(${operationIndex})">
                ${wsOptions}
            </select>
        </td>
        <td style="padding: 8px 10px;">
            <input type="number" step="1" min="0" name="operations[${operationIndex}][standard_time_minutes]" id="opTime_${operationIndex}" class="erp-input" value="${time}" oninput="calcOpRow(${operationIndex})">
        </td>
        <td style="padding: 8px 10px;">
            <input type="number" step="0.01" min="0" name="operations[${operationIndex}][operation_cost]" id="opCost_${operationIndex}" class="erp-input" value="${opCost}" oninput="recalcAll()">
        </td>
        <td style="padding: 8px 10px; text-align: center;">
            <button type="button" onclick="removeRow('opRow_${operationIndex}')" style="background: #FEE2E2; color: #DC2626; border: none; width: 30px; height: 30px; border-radius: 6px; cursor: pointer;">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    operationIndex++;
    recalcAll();
}

function onWsChange(idx) {
    const row = document.getElementById('opRow_' + idx);
    const select = row.querySelector('.op-ws-select');
    const selectedOpt = select.options[select.selectedIndex];
    const time = parseFloat(document.getElementById('opTime_' + idx).value) || 0;
    const costInput = document.getElementById('opCost_' + idx);

    if (selectedOpt && selectedOpt.dataset.hourly) {
        const hourly = parseFloat(selectedOpt.dataset.hourly) || 0;
        costInput.value = ((hourly / 60) * time).toFixed(2);
    }
    recalcAll();
}

function calcOpRow(idx) {
    onWsChange(idx);
}

function removeRow(rowId) {
    const elem = document.getElementById(rowId);
    if (elem) elem.remove();
    recalcAll();
}

function recalcAll() {
    // 1. Sum materials
    let materialTotal = 0;
    document.querySelectorAll('[id^="matTotal_"]').forEach(el => {
        materialTotal += parseFloat(el.value) || 0;
    });
    document.getElementById('totalMaterialCostText').innerText = materialTotal.toFixed(2) + ' SAR';
    document.getElementById('sumMaterial').innerText = materialTotal.toFixed(2) + ' SAR';

    // 2. Sum operations labor
    let laborTotal = 0;
    document.querySelectorAll('[id^="opCost_"]').forEach(el => {
        laborTotal += parseFloat(el.value) || 0;
    });
    document.getElementById('totalLaborCostText').innerText = laborTotal.toFixed(2) + ' SAR';
    document.getElementById('sumLabor').innerText = laborTotal.toFixed(2) + ' SAR';

    // 3. Overheads
    const overhead = parseFloat(document.getElementById('overhead_cost').value) || 0;
    document.getElementById('sumOverhead').innerText = overhead.toFixed(2) + ' SAR';

    // 4. Batch total
    const totalBatch = materialTotal + laborTotal + overhead;
    document.getElementById('sumTotal').innerText = totalBatch.toFixed(2) + ' SAR';

    // 5. Unit Cost
    const outputQty = parseFloat(document.getElementById('output_quantity').value) || 1;
    const unitCost = outputQty > 0 ? (totalBatch / outputQty) : 0;
    document.getElementById('unitCost').innerText = unitCost.toFixed(2) + ' SAR';
}

function updateProductDefaults() {
    const sel = document.getElementById('product_id');
    const opt = sel.options[sel.selectedIndex];
    if (opt && !document.querySelector('input[name="name"]').value) {
        document.querySelector('input[name="name"]').value = 'تركيبة تصنيع ' + opt.text.split('(')[0].trim();
    }
}

// Initialize with one default material row and one operation row
window.addEventListener('DOMContentLoaded', () => {
    addMaterialRow();
    addOperationRow('مرحلة التجهيز والتشغيل');
});
</script>
@endsection
