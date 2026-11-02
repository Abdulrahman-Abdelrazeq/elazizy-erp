@extends('layouts.app')

@section('title', 'إصدار أمر شراء جديد')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1200px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-file-contract" style="color: var(--primary); margin-left: 8px;"></i>
                إصدار أمر شراء وتوريد جديد (New Purchase Order - PO)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                رقم أمر الشراء: <strong style="font-family: monospace; color: var(--primary);">{{ $nextNumber }}</strong>
            </span>
        </div>

        <a href="{{ route('invoices.orders.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لأوامر الشراء
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

    <form id="orderForm" action="{{ route('invoices.orders.store') }}" method="POST">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Header Info Card -->
            <div class="erp-card" style="padding: 22px;">
                <h3 style="font-size: 15.5px; font-weight: 800; color: var(--primary); margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-truck"></i> بيانات المورد ومواعيد التوريد المطلوبة
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            المورد الصادر إليه أمر الشراء <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="client_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                            <option value="">-- اختر المورد --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('client_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} (كود: {{ $s->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            تاريخ إصدار الأمر <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            تاريخ التوريد والاستلام المتوقع
                        </label>
                        <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->toDateString()) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                    </div>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="erp-card" style="padding: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <h3 style="font-size: 15.5px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-boxes-stacked"></i> الأصناف والكميات المطلوبة من المورد
                    </h3>
                    <button type="button" onclick="addOrderRow()" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12.5px;">
                        <i class="fa-solid fa-plus"></i> إضافة بند صنف
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="erp-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th style="width: 45%;">الصنف</th>
                                <th style="width: 18%;">الكمية المطلوبة</th>
                                <th style="width: 18%;">السعر المتفق عليه</th>
                                <th style="width: 15%;">الإجمالي</th>
                                <th style="width: 4%;">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="orderItemsBody">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 14px; margin-top: 16px;">
                    <span style="font-size: 15px; font-weight: 900; color: var(--text-main);">إجمالي قيمة أمر الشراء التقديرية:</span>
                    <strong id="displayOrderTotal" style="font-size: 20px; font-weight: 900; color: var(--primary);">0.00</strong>
                </div>
            </div>

            <!-- Terms & Conditions -->
            <div class="erp-card" style="padding: 22px;">
                <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); margin-bottom: 14px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
                    <i class="fa-solid fa-note-sticky"></i> شروط أمر الشراء والملاحظات
                </h3>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">شروط الفحص والاستلام ومطابقة المواصفات</label>
                        <textarea name="terms_conditions" rows="3" placeholder="البضاعة تخضع للفحص الفني والعد في المستودع قبل اعتماد الاستلام النهائي..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">{{ old('terms_conditions', 'يخضع هذا الطلب للفحص والتدقيق ومطابقة المواصفات عند الاستلام بالمستودع. أي صنف تالف أو غير مطابق يتم رفضه وإعادته فوراً.') }}</textarea>
                    </div>

                    <div>
                        <label style="display: block; font-size: 12.5px; font-weight: 700; color: var(--text-main); margin-bottom: 4px;">ملاحظات إضافية</label>
                        <textarea name="notes" rows="2" placeholder="ملاحظات النقل أو التغليف..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('invoices.orders.index') }}" style="background: #E2E8F0; padding: 12px 24px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 14px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; font-size: 15px;">
                    <i class="fa-solid fa-check"></i>
                    اعتماد وإصدار أمر الشراء
                </button>
            </div>

        </div>
    </form>
</div>

<script>
const ordItems = @json($items);
let ordRowIdx = 0;

function addOrderRow() {
    const tbody = document.getElementById('orderItemsBody');
    const tr = document.createElement('tr');
    tr.id = 'ord_row_' + ordRowIdx;

    let itemsOptions = '<option value="">-- اختر الصنف --</option>';
    ordItems.forEach(item => {
        itemsOptions += `<option value="${item.id}" data-cost="${item.cost_price}">${item.name} (${item.code}) - تكلفة: ${item.cost_price}</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="items[${ordRowIdx}][item_id]" onchange="onOrdItemSelect(${ordRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 600;">
                ${itemsOptions}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" name="items[${ordRowIdx}][quantity]" value="1.00" oninput="calcOrdRow(${ordRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: center;">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="items[${ordRowIdx}][unit_price]" value="0.00" oninput="calcOrdRow(${ordRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: left;">
        </td>
        <td>
            <span id="ord_row_total_${ordRowIdx}" style="font-weight: 800; color: var(--primary); font-size: 14px;">0.00</span>
        </td>
        <td style="text-align: center;">
            <button type="button" onclick="removeOrdRow(${ordRowIdx})" style="background: none; border: none; color: #DC2626; cursor: pointer; font-size: 16px;">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    ordRowIdx++;
}

function removeOrdRow(idx) {
    const row = document.getElementById('ord_row_' + idx);
    if (row) {
        row.remove();
        calcOrdGrandTotal();
    }
}

function onOrdItemSelect(idx) {
    const row = document.getElementById('ord_row_' + idx);
    const select = row.querySelector(`select[name="items[${idx}][item_id]"]`);
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;

    const costPrice = parseFloat(opt.dataset.cost) || 0;
    row.querySelector(`input[name="items[${idx}][unit_price]"]`).value = costPrice.toFixed(2);
    calcOrdRow(idx);
}

function calcOrdRow(idx) {
    const row = document.getElementById('ord_row_' + idx);
    if (!row) return;

    const qty = parseFloat(row.querySelector(`input[name="items[${idx}][quantity]"]`).value) || 0;
    const price = parseFloat(row.querySelector(`input[name="items[${idx}][unit_price]"]`).value) || 0;
    const lineTotal = qty * price;

    document.getElementById('ord_row_total_' + idx).innerText = lineTotal.toFixed(2);
    calcOrdGrandTotal();
}

function calcOrdGrandTotal() {
    let total = 0;
    const tbody = document.getElementById('orderItemsBody');
    tbody.querySelectorAll('tr').forEach(tr => {
        const idMatch = tr.id.split('_')[2];
        if (idMatch !== undefined) {
            const rowTotal = parseFloat(document.getElementById('ord_row_total_' + idMatch).innerText) || 0;
            total += rowTotal;
        }
    });

    document.getElementById('displayOrderTotal').innerText = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    addOrderRow();
});
</script>
@endsection

