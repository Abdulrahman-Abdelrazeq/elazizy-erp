@extends('layouts.app')

@section('title', 'إنشاء فاتورة مبيعات جديدة')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1200px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-file-circle-plus" style="color: var(--primary); margin-left: 8px;"></i>
                إنشاء فاتورة مبيعات جديدة (New Sales Invoice)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                رقم الفاتورة المقترح: <strong style="font-family: monospace; color: var(--primary);">{{ $nextNumber }}</strong>
            </span>
        </div>

        <a href="{{ route('invoices.sales.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لفواتير المبيعات
        </a>
    </div>

    @if ($errors->any())
        <div style="background: #FEE2E2; border-right: 4px solid #DC2626; padding: 14px 18px; border-radius: 10px; color: #991B1B; font-size: 13.5px;">
            <strong style="display: block; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> يرجى تصحيح الأخطاء التالية:</strong>
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="saleInvoiceForm" action="{{ route('invoices.sales.store') }}" method="POST">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Header Info Card -->
            <div class="erp-card" style="padding: 22px;">
                <h3 style="font-size: 15.5px; font-weight: 800; color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-info"></i> البيانات الأساسية للفاتورة
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            العميل المستفيد
                        </label>
                        <select name="client_id" id="clientSelect" onchange="onClientChanged()" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                            <option value="" data-group="" data-discount="0">-- عميل نقدي عام (بدون تسجيل حساب) --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" data-group="{{ $c->group ? $c->group->name : '' }}" data-tier="{{ $c->group ? $c->group->price_tier : 'retail' }}" data-discount="{{ $c->group ? $c->group->discount_percentage : 0 }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} (كود: {{ $c->code }}) - الرصيد: {{ formatMoney($c->current_balance) }}
                                </option>
                            @endforeach
                        </select>
                        <div id="clientGroupBadge" style="display: none; margin-top: 6px; font-size: 12px; color: var(--primary); font-weight: 700;"></div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            مستودع الصرف <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="store_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" {{ old('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            تاريخ الفاتورة <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            طريقة السداد <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="payment_type" id="paymentType" onchange="togglePaymentInputs()" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                            <option value="cash" {{ old('payment_type') == 'cash' ? 'selected' : '' }}>نقدي (كاش مسدد بالكامل)</option>
                            <option value="credit" {{ old('payment_type') == 'credit' ? 'selected' : '' }}>آجل (ذمم على الحساب بالكامل)</option>
                            <option value="partial" {{ old('payment_type') == 'partial' ? 'selected' : '' }}>سداد جزئي (مقدم + متبقي)</option>
                            <option value="card" {{ old('payment_type') == 'card' ? 'selected' : '' }}>شبكة / بطاقة بنكية</option>
                            <option value="bank_transfer" {{ old('payment_type') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="erp-card" style="padding: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <h3 style="font-size: 15.5px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-boxes-stacked"></i> بنود وأصناف الفاتورة
                    </h3>
                    <button type="button" onclick="addRow()" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12.5px;">
                        <i class="fa-solid fa-plus"></i> إضافة سطر صنف
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="erp-table" id="itemsTable" style="width: 100%;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th style="width: 35%;">الصنف</th>
                                <th style="width: 15%;">الوحدة</th>
                                <th style="width: 12%;">الكمية</th>
                                <th style="width: 14%;">سعر الوحدة</th>
                                <th style="width: 10%;">الخصم %</th>
                                <th style="width: 12%;">الإجمالي</th>
                                <th style="width: 2%;">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Rows added by JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Financial Calculations & Footer Card -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                
                <!-- Notes & Terms -->
                <div class="erp-card" style="padding: 22px;">
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); margin-bottom: 14px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <i class="fa-solid fa-note-sticky"></i> ملاحظات وشروط الفاتورة
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">ملاحظات الفاتورة</label>
                            <textarea name="notes" rows="2" placeholder="أي ملاحظات خاصة بالفاتورة..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">{{ old('notes') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">تاريخ استحقاق السداد الآجل</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                        </div>
                    </div>
                </div>

                <!-- Totals Summary Card -->
                <div class="erp-card" style="padding: 22px; background: #FAFDFB; border: 1.5px solid var(--primary-light);">
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); margin-bottom: 14px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <i class="fa-solid fa-calculator"></i> ملخص الحسابات المالية
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13.5px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--text-muted); font-weight: 700;">المجموع قبل الخصم:</span>
                            <strong id="displaySubtotal" style="font-size: 15px; color: var(--text-main);">0.00</strong>
                        </div>

                        <!-- Discount on Invoice -->
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <span style="color: var(--text-muted); font-weight: 700;">خصم إضافي:</span>
                            <div style="display: flex; gap: 6px; width: 60%;">
                                <select name="discount_type" id="discountType" onchange="calcGrandTotal()" style="padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px;">
                                    <option value="fixed">مبلغ ثابت</option>
                                    <option value="percentage">نسبة مئوية %</option>
                                </select>
                                <input type="number" step="0.01" min="0" name="discount_value" id="discountValue" value="0.00" oninput="calcGrandTotal()" style="width: 100%; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; text-align: left; font-weight: 700;">
                            </div>
                        </div>

                        <!-- Tax Selection -->
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <span style="color: var(--text-muted); font-weight: 700;">ضريبة القيمة المضافة (VAT):</span>
                            <select name="tax_id" id="taxSelect" onchange="calcGrandTotal()" style="width: 60%; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12.5px; font-weight: 600;">
                                <option value="" data-rate="0">بدون ضريبة (0%)</option>
                                @foreach($taxes as $t)
                                    <option value="{{ $t->id }}" data-rate="{{ $t->rate }}">{{ $t->name }} ({{ $t->rate }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Shipping / Delivery Cost -->
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <span style="color: var(--text-muted); font-weight: 700;">تكلفة الشحن والتوصيل:</span>
                            <input type="number" step="0.01" min="0" name="shipping_cost" id="shippingCost" value="0.00" oninput="calcGrandTotal()" style="width: 60%; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; text-align: left; font-weight: 700;">
                        </div>

                        <hr style="border: 0; border-top: 1.5px dashed var(--border-color); margin: 6px 0;">

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 16px; font-weight: 900; color: var(--primary);">الصافي الإجمالي للفاتورة:</span>
                            <strong id="displayGrandTotal" style="font-size: 20px; font-weight: 900; color: var(--primary);">0.00</strong>
                        </div>

                        <div id="partialPaymentRow" style="display: none; flex-direction: column; gap: 6px; background: #FEF3C7; padding: 10px; border-radius: 10px; margin-top: 6px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 700; color: #92400E; font-size: 12.5px;">المبلغ المسدد الآن (كاش / بنك):</span>
                                <input type="number" step="0.01" min="0" name="paid_amount" id="paidAmount" value="0.00" oninput="calcGrandTotal()" style="width: 110px; padding: 5px 8px; border-radius: 6px; border: 1px solid #D97706; font-size: 13px; font-weight: 700; text-align: left;">
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 700; color: #DC2626; font-size: 12.5px;">المتبقي الآجل على الحساب:</span>
                                <strong id="displayRemaining" style="color: #DC2626; font-size: 14px;">0.00</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('invoices.sales.index') }}" style="background: #E2E8F0; padding: 12px 24px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 14px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; font-size: 15px;">
                    <i class="fa-solid fa-check-double"></i>
                    حفظ واعتماد الفاتورة وصرف المخزون
                </button>
            </div>

        </div>
    </form>
</div>

<!-- Raw Items JSON for Dynamic Client-Side Autocomplete -->
<script>
const allItems = @json($items);
let rowIndex = 0;

function addRow() {
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowIndex;

    let itemsOptions = '<option value="">-- اختر الصنف --</option>';
    allItems.forEach(item => {
        itemsOptions += `<option value="${item.id}" data-price="${item.selling_price}" data-cost="${item.cost_price}" data-wholesale="${item.wholesale_price || item.selling_price}" data-half="${item.half_wholesale_price || item.selling_price}">${item.name} (${item.code}) - ${item.selling_price}</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="items[${rowIndex}][item_id]" onchange="onItemSelect(${rowIndex})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 600;">
                ${itemsOptions}
            </select>
        </td>
        <td>
            <select name="items[${rowIndex}][unit_id]" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12.5px;">
                <option value="">قطعة / افتراضي</option>
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" name="items[${rowIndex}][quantity]" value="1.00" oninput="calcRow(${rowIndex})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: center;">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="items[${rowIndex}][unit_price]" value="0.00" oninput="calcRow(${rowIndex})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: left;">
        </td>
        <td>
            <input type="number" step="0.01" min="0" max="100" name="items[${rowIndex}][discount_percent]" value="0.00" oninput="calcRow(${rowIndex})" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: center;">
        </td>
        <td>
            <span id="row_total_${rowIndex}" style="font-weight: 800; color: var(--primary); font-size: 14px;">0.00</span>
        </td>
        <td style="text-align: center;">
            <button type="button" onclick="removeRow(${rowIndex})" style="background: none; border: none; color: #DC2626; cursor: pointer; font-size: 16px;">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    rowIndex++;
}

function removeRow(idx) {
    const row = document.getElementById('row_' + idx);
    if (row) {
        row.remove();
        calcGrandTotal();
    }
}

function onItemSelect(idx) {
    const row = document.getElementById('row_' + idx);
    const select = row.querySelector(`select[name="items[${idx}][item_id]"]`);
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;

    // Check customer group tier for automatic pricing
    const clientSelect = document.getElementById('clientSelect');
    const clientOpt = clientSelect.options[clientSelect.selectedIndex];
    const tier = clientOpt ? clientOpt.dataset.tier : 'retail';

    let unitPrice = parseFloat(opt.dataset.price) || 0;
    if (tier === 'wholesale') {
        unitPrice = parseFloat(opt.dataset.wholesale) || unitPrice;
    } else if (tier === 'half_wholesale') {
        unitPrice = parseFloat(opt.dataset.half) || unitPrice;
    }

    const priceInput = row.querySelector(`input[name="items[${idx}][unit_price]"]`);
    priceInput.value = unitPrice.toFixed(2);

    calcRow(idx);
}

function calcRow(idx) {
    const row = document.getElementById('row_' + idx);
    if (!row) return;

    const qty = parseFloat(row.querySelector(`input[name="items[${idx}][quantity]"]`).value) || 0;
    const price = parseFloat(row.querySelector(`input[name="items[${idx}][unit_price]"]`).value) || 0;
    const disc = parseFloat(row.querySelector(`input[name="items[${idx}][discount_percent]"]`).value) || 0;

    const lineSubtotal = qty * price;
    const lineDiscount = (lineSubtotal * disc) / 100;
    const net = Math.max(0, lineSubtotal - lineDiscount);

    document.getElementById('row_total_' + idx).innerText = net.toFixed(2);
    calcGrandTotal();
}

function calcGrandTotal() {
    let subtotal = 0;
    const tbody = document.getElementById('itemsBody');
    const rows = tbody.querySelectorAll('tr');

    rows.forEach(tr => {
        const idMatch = tr.id.split('_')[1];
        if (idMatch !== undefined) {
            const rowTotal = parseFloat(document.getElementById('row_total_' + idMatch).innerText) || 0;
            subtotal += rowTotal;
        }
    });

    document.getElementById('displaySubtotal').innerText = subtotal.toFixed(2);

    // Discount
    const discType = document.getElementById('discountType').value;
    const discVal = parseFloat(document.getElementById('discountValue').value) || 0;
    let discAmount = 0;
    if (discType === 'percentage') {
        discAmount = (subtotal * discVal) / 100;
    } else {
        discAmount = Math.min(discVal, subtotal);
    }
    const afterDiscount = Math.max(0, subtotal - discAmount);

    // Tax
    const taxSelect = document.getElementById('taxSelect');
    const taxRate = parseFloat(taxSelect.options[taxSelect.selectedIndex].dataset.rate) || 0;
    const taxAmount = (afterDiscount * taxRate) / 100;

    // Shipping
    const shipping = parseFloat(document.getElementById('shippingCost').value) || 0;
    const grandTotal = afterDiscount + taxAmount + shipping;

    document.getElementById('displayGrandTotal').innerText = grandTotal.toFixed(2);

    // Partial / remaining
    const paymentType = document.getElementById('paymentType').value;
    if (paymentType === 'partial') {
        const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
        const rem = Math.max(0, grandTotal - paid);
        document.getElementById('displayRemaining').innerText = rem.toFixed(2);
    }
}

function onClientChanged() {
    const clientSelect = document.getElementById('clientSelect');
    const opt = clientSelect.options[clientSelect.selectedIndex];
    const badge = document.getElementById('clientGroupBadge');

    if (opt && opt.dataset.group) {
        badge.style.display = 'block';
        badge.innerHTML = `<i class="fa-solid fa-tag"></i> الشريحة: ${opt.dataset.group} (خصم إضافي: ${opt.dataset.discount}%)`;
        document.getElementById('discountValue').value = opt.dataset.discount;
        document.getElementById('discountType').value = 'percentage';
    } else {
        badge.style.display = 'none';
    }

    // Recalculate item prices if rows exist
    const tbody = document.getElementById('itemsBody');
    tbody.querySelectorAll('tr').forEach(tr => {
        const idx = tr.id.split('_')[1];
        onItemSelect(idx);
    });

    calcGrandTotal();
}

function togglePaymentInputs() {
    const pt = document.getElementById('paymentType').value;
    const partialRow = document.getElementById('partialPaymentRow');
    if (pt === 'partial') {
        partialRow.style.display = 'flex';
    } else {
        partialRow.style.display = 'none';
    }
    calcGrandTotal();
}

// Add first row on load
document.addEventListener('DOMContentLoaded', function() {
    addRow();
});
</script>
@endsection

