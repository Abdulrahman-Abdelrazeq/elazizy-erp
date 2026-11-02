@extends('layouts.app')

@section('title', 'تسجيل فاتورة مشتريات جديدة')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1200px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-cart-plus" style="color: var(--primary); margin-left: 8px;"></i>
                تسجيل فاتورة مشتريات وتوريد بضاعة (New Purchase Invoice)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                رقم الفاتورة: <strong style="font-family: monospace; color: var(--primary);">{{ $nextNumber }}</strong>
            </span>
        </div>

        <a href="{{ route('invoices.purchases.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لفواتير المشتريات
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

    <form id="purchaseInvoiceForm" action="{{ route('invoices.purchases.store') }}" method="POST">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Header Info Card -->
            <div class="erp-card" style="padding: 22px;">
                <h3 style="font-size: 15.5px; font-weight: 800; color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-truck"></i> بيانات المورد والمستودع
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            المورد <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="client_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                            <option value="">-- اختر المورد --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('client_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} (كود: {{ $s->code }}) - المستحق له: {{ formatMoney(abs($s->current_balance)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            مستودع الاستلام والإيداع <span style="color: #DC2626;">*</span>
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
                            طريقة السداد للمورد <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="payment_type" id="purPaymentType" onchange="togglePurPayment()" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                            <option value="cash">نقدي (تم السداد بالكامل كاش)</option>
                            <option value="credit">آجل (مستحق للمورد على الحساب)</option>
                            <option value="partial">سداد جزئي (دفعة نقدية + متبقي)</option>
                            <option value="bank_transfer">تحويل بنكي</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="erp-card" style="padding: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <h3 style="font-size: 15.5px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-boxes-stacked"></i> الأصناف الموردة وأسعار الشراء
                    </h3>
                    <button type="button" onclick="addPurRow()" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12.5px;">
                        <i class="fa-solid fa-plus"></i> إضافة صنف للشراء
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="erp-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th style="width: 40%;">الصنف</th>
                                <th style="width: 15%;">الوحدة</th>
                                <th style="width: 12%;">الكمية المشتراة</th>
                                <th style="width: 15%;">سعر الشراء للوحدة</th>
                                <th style="width: 15%;">الإجمالي</th>
                                <th style="width: 3%;">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="purItemsBody">
                            <!-- Rows added dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals & Notes -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                
                <div class="erp-card" style="padding: 22px;">
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); margin-bottom: 14px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <i class="fa-solid fa-note-sticky"></i> الملاحظات وشروط التوريد
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">ملاحظات الفاتورة أو رقم فاتورة المورد الورقية</label>
                            <textarea name="notes" rows="2" placeholder="رقم سند التوريد أو فاتورة المورد..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">{{ old('notes') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">تاريخ استحقاق السداد الآجل</label>
                            <input type="date" name="due_date" value="{{ old('due_date') }}" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                        </div>
                    </div>
                </div>

                <div class="erp-card" style="padding: 22px; background: #FAFDFB; border: 1.5px solid var(--primary-light);">
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); margin-bottom: 14px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <i class="fa-solid fa-calculator"></i> ملخص إجمالي الشراء
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13.5px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--text-muted); font-weight: 700;">المجموع قبل الخصم:</span>
                            <strong id="displayPurSubtotal" style="font-size: 15px; color: var(--text-main);">0.00</strong>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <span style="color: var(--text-muted); font-weight: 700;">خصم الشراء:</span>
                            <div style="display: flex; gap: 6px; width: 60%;">
                                <select name="discount_type" id="purDiscountType" onchange="calcPurGrandTotal()" style="padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px;">
                                    <option value="fixed">مبلغ ثابت</option>
                                    <option value="percentage">نسبة %</option>
                                </select>
                                <input type="number" step="0.01" min="0" name="discount_value" id="purDiscountValue" value="0.00" oninput="calcPurGrandTotal()" style="width: 100%; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; text-align: left; font-weight: 700;">
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <span style="color: var(--text-muted); font-weight: 700;">ضريبة المشتريات (VAT):</span>
                            <select name="tax_id" id="purTaxSelect" onchange="calcPurGrandTotal()" style="width: 60%; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12.5px; font-weight: 600;">
                                <option value="" data-rate="0">بدون ضريبة (0%)</option>
                                @foreach($taxes as $t)
                                    <option value="{{ $t->id }}" data-rate="{{ $t->rate }}">{{ $t->name }} ({{ $t->rate }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <span style="color: var(--text-muted); font-weight: 700;">مصاريف الشحن / التوصيل:</span>
                            <input type="number" step="0.01" min="0" name="shipping_cost" id="purShippingCost" value="0.00" oninput="calcPurGrandTotal()" style="width: 60%; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; text-align: left; font-weight: 700;">
                        </div>

                        <hr style="border: 0; border-top: 1.5px dashed var(--border-color); margin: 6px 0;">

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 16px; font-weight: 900; color: var(--primary);">إجمالي قيمة الفاتورة:</span>
                            <strong id="displayPurGrandTotal" style="font-size: 20px; font-weight: 900; color: var(--primary);">0.00</strong>
                        </div>

                        <div id="purPartialRow" style="display: none; flex-direction: column; gap: 6px; background: #FEF3C7; padding: 10px; border-radius: 10px; margin-top: 6px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 700; color: #92400E; font-size: 12.5px;">المسدد للمورد الآن:</span>
                                <input type="number" step="0.01" min="0" name="paid_amount" id="purPaidAmount" value="0.00" oninput="calcPurGrandTotal()" style="width: 110px; padding: 5px 8px; border-radius: 6px; border: 1px solid #D97706; font-size: 13px; font-weight: 700; text-align: left;">
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: 700; color: #DC2626; font-size: 12.5px;">المتبقي للمورد على الحساب:</span>
                                <strong id="displayPurRemaining" style="color: #DC2626; font-size: 14px;">0.00</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('invoices.purchases.index') }}" style="background: #E2E8F0; padding: 12px 24px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 14px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; font-size: 15px;">
                    <i class="fa-solid fa-check-double"></i>
                    حفظ الفاتورة وتوريد الأصناف للمستودع
                </button>
            </div>

        </div>
    </form>
</div>

<script>
const purItems = @json($items);
let purRowIdx = 0;

function addPurRow() {
    const tbody = document.getElementById('purItemsBody');
    const tr = document.createElement('tr');
    tr.id = 'pur_row_' + purRowIdx;

    let itemsOptions = '<option value="">-- اختر الصنف --</option>';
    purItems.forEach(item => {
        itemsOptions += `<option value="${item.id}" data-cost="${item.cost_price}">${item.name} (${item.code}) - تكلفة: ${item.cost_price}</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="items[${purRowIdx}][item_id]" onchange="onPurItemSelect(${purRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 600;">
                ${itemsOptions}
            </select>
        </td>
        <td>
            <select name="items[${purRowIdx}][unit_id]" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12.5px;">
                <option value="">قطعة / افتراضي</option>
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" name="items[${purRowIdx}][quantity]" value="1.00" oninput="calcPurRow(${purRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: center;">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="items[${purRowIdx}][unit_price]" value="0.00" oninput="calcPurRow(${purRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: left;">
        </td>
        <td>
            <span id="pur_row_total_${purRowIdx}" style="font-weight: 800; color: var(--primary); font-size: 14px;">0.00</span>
        </td>
        <td style="text-align: center;">
            <button type="button" onclick="removePurRow(${purRowIdx})" style="background: none; border: none; color: #DC2626; cursor: pointer; font-size: 16px;">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    purRowIdx++;
}

function removePurRow(idx) {
    const row = document.getElementById('pur_row_' + idx);
    if (row) {
        row.remove();
        calcPurGrandTotal();
    }
}

function onPurItemSelect(idx) {
    const row = document.getElementById('pur_row_' + idx);
    const select = row.querySelector(`select[name="items[${idx}][item_id]"]`);
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;

    const costPrice = parseFloat(opt.dataset.cost) || 0;
    const priceInput = row.querySelector(`input[name="items[${idx}][unit_price]"]`);
    priceInput.value = costPrice.toFixed(2);

    calcPurRow(idx);
}

function calcPurRow(idx) {
    const row = document.getElementById('pur_row_' + idx);
    if (!row) return;

    const qty = parseFloat(row.querySelector(`input[name="items[${idx}][quantity]"]`).value) || 0;
    const price = parseFloat(row.querySelector(`input[name="items[${idx}][unit_price]"]`).value) || 0;
    const lineTotal = qty * price;

    document.getElementById('pur_row_total_' + idx).innerText = lineTotal.toFixed(2);
    calcPurGrandTotal();
}

function calcPurGrandTotal() {
    let subtotal = 0;
    const tbody = document.getElementById('purItemsBody');
    tbody.querySelectorAll('tr').forEach(tr => {
        const idMatch = tr.id.split('_')[2];
        if (idMatch !== undefined) {
            const rowTotal = parseFloat(document.getElementById('pur_row_total_' + idMatch).innerText) || 0;
            subtotal += rowTotal;
        }
    });

    document.getElementById('displayPurSubtotal').innerText = subtotal.toFixed(2);

    const discType = document.getElementById('purDiscountType').value;
    const discVal = parseFloat(document.getElementById('purDiscountValue').value) || 0;
    let discAmount = discType === 'percentage' ? (subtotal * discVal) / 100 : Math.min(discVal, subtotal);
    const afterDiscount = Math.max(0, subtotal - discAmount);

    const taxSelect = document.getElementById('purTaxSelect');
    const taxRate = parseFloat(taxSelect.options[taxSelect.selectedIndex].dataset.rate) || 0;
    const taxAmount = (afterDiscount * taxRate) / 100;

    const shipping = parseFloat(document.getElementById('purShippingCost').value) || 0;
    const grandTotal = afterDiscount + taxAmount + shipping;

    document.getElementById('displayPurGrandTotal').innerText = grandTotal.toFixed(2);

    const pt = document.getElementById('purPaymentType').value;
    if (pt === 'partial') {
        const paid = parseFloat(document.getElementById('purPaidAmount').value) || 0;
        const rem = Math.max(0, grandTotal - paid);
        document.getElementById('displayPurRemaining').innerText = rem.toFixed(2);
    }
}

function togglePurPayment() {
    const pt = document.getElementById('purPaymentType').value;
    const row = document.getElementById('purPartialRow');
    if (pt === 'partial') {
        row.style.display = 'flex';
    } else {
        row.style.display = 'none';
    }
    calcPurGrandTotal();
}

document.addEventListener('DOMContentLoaded', function() {
    addPurRow();
});
</script>
@endsection
