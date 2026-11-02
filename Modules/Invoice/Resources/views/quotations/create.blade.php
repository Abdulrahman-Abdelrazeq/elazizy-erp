@extends('layouts.app')

@section('title', 'إنشاء عرض سعر جديد للعميل')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1200px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-handshake" style="color: var(--primary); margin-left: 8px;"></i>
                إنشاء عرض سعر جديد (New Quotation)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                رقم عرض السعر: <strong style="font-family: monospace; color: var(--primary);">{{ $nextNumber }}</strong>
            </span>
        </div>

        <a href="{{ route('invoices.quotations.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لعروض الأسعار
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

    <form id="quoteForm" action="{{ route('invoices.quotations.store') }}" method="POST">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Header Info Card -->
            <div class="erp-card" style="padding: 22px;">
                <h3 style="font-size: 15.5px; font-weight: 800; color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-user"></i> بيانات العميل وصلاحية العرض
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            العميل المستهدف <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="client_id" id="quoteClientSelect" onchange="onQuoteClientChanged()" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                            <option value="">-- اختر العميل --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" data-tier="{{ $c->group ? $c->group->price_tier : 'retail' }}" data-discount="{{ $c->group ? $c->group->discount_percentage : 0 }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} (كود: {{ $c->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            تاريخ إصدار العرض <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            تاريخ انتهاء صلاحية العرض
                        </label>
                        <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(15)->toDateString()) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                    </div>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="erp-card" style="padding: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <h3 style="font-size: 15.5px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-boxes-stacked"></i> الأصناف والأسعار المقترحة
                    </h3>
                    <button type="button" onclick="addQuoteRow()" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12.5px;">
                        <i class="fa-solid fa-plus"></i> إضافة صنف للعرض
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="erp-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th style="width: 40%;">الصنف</th>
                                <th style="width: 15%;">الوحدة</th>
                                <th style="width: 12%;">الكمية</th>
                                <th style="width: 15%;">سعر الوحدة</th>
                                <th style="width: 15%;">الإجمالي</th>
                                <th style="width: 3%;">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="quoteItemsBody">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals & Notes -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
                
                <div class="erp-card" style="padding: 22px;">
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); margin-bottom: 14px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <i class="fa-solid fa-note-sticky"></i> الشروط والأحكام وملاحظات العرض
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">شروط العرض والتسليم والضمان</label>
                            <textarea name="terms_conditions" rows="3" placeholder="الأسعار شاملة التوصيل، صلاحية العرض 15 يوماً من تاريخه..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">{{ old('terms_conditions', 'الأسعار سارية لمدة 15 يوماً من تاريخه. مدة التوريد: خلال 3 أيام عمل من تاريخ التعميد.') }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">ملاحظات إضافية</label>
                            <textarea name="notes" rows="2" placeholder="ملاحظات سرية أو إدارية..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="erp-card" style="padding: 22px; background: #FAFDFB; border: 1.5px solid var(--primary-light);">
                    <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); margin-bottom: 14px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                        <i class="fa-solid fa-calculator"></i> ملخص قيمة العرض
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13.5px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: var(--text-muted); font-weight: 700;">المجموع قبل الخصم:</span>
                            <strong id="displayQuoteSubtotal" style="font-size: 15px; color: var(--text-main);">0.00</strong>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <span style="color: var(--text-muted); font-weight: 700;">خصم العرض:</span>
                            <div style="display: flex; gap: 6px; width: 60%;">
                                <select name="discount_type" id="quoteDiscountType" onchange="calcQuoteGrandTotal()" style="padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12px;">
                                    <option value="fixed">مبلغ ثابت</option>
                                    <option value="percentage">نسبة %</option>
                                </select>
                                <input type="number" step="0.01" min="0" name="discount_value" id="quoteDiscountValue" value="0.00" oninput="calcQuoteGrandTotal()" style="width: 100%; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; text-align: left; font-weight: 700;">
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <span style="color: var(--text-muted); font-weight: 700;">ضريبة القيمة المضافة (VAT):</span>
                            <select name="tax_id" id="quoteTaxSelect" onchange="calcQuoteGrandTotal()" style="width: 60%; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12.5px; font-weight: 600;">
                                <option value="" data-rate="0">بدون ضريبة (0%)</option>
                                @foreach($taxes as $t)
                                    <option value="{{ $t->id }}" data-rate="{{ $t->rate }}">{{ $t->name }} ({{ $t->rate }}%)</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                            <span style="color: var(--text-muted); font-weight: 700;">مصاريف الشحن / التوصيل:</span>
                            <input type="number" step="0.01" min="0" name="shipping_cost" id="quoteShippingCost" value="0.00" oninput="calcQuoteGrandTotal()" style="width: 60%; padding: 6px 8px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; text-align: left; font-weight: 700;">
                        </div>

                        <hr style="border: 0; border-top: 1.5px dashed var(--border-color); margin: 6px 0;">

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 16px; font-weight: 900; color: var(--primary);">صافي قيمة عرض السعر:</span>
                            <strong id="displayQuoteGrandTotal" style="font-size: 20px; font-weight: 900; color: var(--primary);">0.00</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('invoices.quotations.index') }}" style="background: #E2E8F0; padding: 12px 24px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 14px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; font-size: 15px;">
                    <i class="fa-solid fa-save"></i>
                    حفظ وإصدار عرض السعر
                </button>
            </div>

        </div>
    </form>
</div>

<script>
const quoteItems = @json($items);
let quoteRowIdx = 0;

function addQuoteRow() {
    const tbody = document.getElementById('quoteItemsBody');
    const tr = document.createElement('tr');
    tr.id = 'quote_row_' + quoteRowIdx;

    let itemsOptions = '<option value="">-- اختر الصنف --</option>';
    quoteItems.forEach(item => {
        itemsOptions += `<option value="${item.id}" data-price="${item.selling_price}" data-wholesale="${item.wholesale_price || item.selling_price}" data-half="${item.half_wholesale_price || item.selling_price}">${item.name} (${item.code}) - ${item.selling_price}</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="items[${quoteRowIdx}][item_id]" onchange="onQuoteItemSelect(${quoteRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 600;">
                ${itemsOptions}
            </select>
        </td>
        <td>
            <select name="items[${quoteRowIdx}][unit_id]" style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12.5px;">
                <option value="">قطعة / افتراضي</option>
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" name="items[${quoteRowIdx}][quantity]" value="1.00" oninput="calcQuoteRow(${quoteRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: center;">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="items[${quoteRowIdx}][unit_price]" value="0.00" oninput="calcQuoteRow(${quoteRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: left;">
        </td>
        <td>
            <span id="quote_row_total_${quoteRowIdx}" style="font-weight: 800; color: var(--primary); font-size: 14px;">0.00</span>
        </td>
        <td style="text-align: center;">
            <button type="button" onclick="removeQuoteRow(${quoteRowIdx})" style="background: none; border: none; color: #DC2626; cursor: pointer; font-size: 16px;">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    quoteRowIdx++;
}

function removeQuoteRow(idx) {
    const row = document.getElementById('quote_row_' + idx);
    if (row) {
        row.remove();
        calcQuoteGrandTotal();
    }
}

function onQuoteItemSelect(idx) {
    const row = document.getElementById('quote_row_' + idx);
    const select = row.querySelector(`select[name="items[${idx}][item_id]"]`);
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;

    const clientSelect = document.getElementById('quoteClientSelect');
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

    calcQuoteRow(idx);
}

function calcQuoteRow(idx) {
    const row = document.getElementById('quote_row_' + idx);
    if (!row) return;

    const qty = parseFloat(row.querySelector(`input[name="items[${idx}][quantity]"]`).value) || 0;
    const price = parseFloat(row.querySelector(`input[name="items[${idx}][unit_price]"]`).value) || 0;
    const lineTotal = qty * price;

    document.getElementById('quote_row_total_' + idx).innerText = lineTotal.toFixed(2);
    calcQuoteGrandTotal();
}

function calcQuoteGrandTotal() {
    let subtotal = 0;
    const tbody = document.getElementById('quoteItemsBody');
    tbody.querySelectorAll('tr').forEach(tr => {
        const idMatch = tr.id.split('_')[2];
        if (idMatch !== undefined) {
            const rowTotal = parseFloat(document.getElementById('quote_row_total_' + idMatch).innerText) || 0;
            subtotal += rowTotal;
        }
    });

    document.getElementById('displayQuoteSubtotal').innerText = subtotal.toFixed(2);

    const discType = document.getElementById('quoteDiscountType').value;
    const discVal = parseFloat(document.getElementById('quoteDiscountValue').value) || 0;
    let discAmount = discType === 'percentage' ? (subtotal * discVal) / 100 : Math.min(discVal, subtotal);
    const afterDiscount = Math.max(0, subtotal - discAmount);

    const taxSelect = document.getElementById('quoteTaxSelect');
    const taxRate = parseFloat(taxSelect.options[taxSelect.selectedIndex].dataset.rate) || 0;
    const taxAmount = (afterDiscount * taxRate) / 100;

    const shipping = parseFloat(document.getElementById('quoteShippingCost').value) || 0;
    const grandTotal = afterDiscount + taxAmount + shipping;

    document.getElementById('displayQuoteGrandTotal').innerText = grandTotal.toFixed(2);
}

function onQuoteClientChanged() {
    const clientSelect = document.getElementById('quoteClientSelect');
    const clientOpt = clientSelect.options[clientSelect.selectedIndex];
    if (clientOpt && clientOpt.dataset.discount) {
        document.getElementById('quoteDiscountValue').value = clientOpt.dataset.discount;
        document.getElementById('quoteDiscountType').value = 'percentage';
    }

    const tbody = document.getElementById('quoteItemsBody');
    tbody.querySelectorAll('tr').forEach(tr => {
        const idx = tr.id.split('_')[2];
        onQuoteItemSelect(idx);
    });

    calcQuoteGrandTotal();
}

document.addEventListener('DOMContentLoaded', function() {
    addQuoteRow();
});
</script>
@endsection

