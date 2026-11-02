@extends('layouts.app')

@section('title', 'تسجيل مرتجع جديد')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1100px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-rotate-left" style="color: #DC2626; margin-left: 8px;"></i>
                تسجيل حركة مرتجع ({{ $type === 'sale_return' ? 'مرتجع مبيعات من عميل' : 'مرتجع مشتريات إلى مورد' }})
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                رقم السند: <strong style="font-family: monospace; color: #DC2626;">{{ $nextNumber }}</strong>
                @if($originalInvoice)
                    | الفاتورة الأصلية المرتبطة: <strong style="font-family: monospace; color: var(--primary);">#{{ $originalInvoice->invoice_number }}</strong>
                @endif
            </span>
        </div>

        <a href="{{ route('invoices.returns.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة للمرتجعات
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

    <form id="returnForm" action="{{ route('invoices.returns.store') }}" method="POST">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        @if($originalInvoice)
            <input type="hidden" name="parent_id" value="{{ $originalInvoice->id }}">
        @endif

        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Header Info Card -->
            <div class="erp-card" style="padding: 22px;">
                <h3 style="font-size: 15.5px; font-weight: 800; color: #DC2626; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-info"></i> البيانات الأساسية للمرتجع
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            {{ $type === 'sale_return' ? 'العميل' : 'المورد' }} <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="client_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                            <option value="">-- اختر الطرف --</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ ($originalInvoice && $originalInvoice->client_id == $c->id) || old('client_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} (كود: {{ $c->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            المستودع المعني بالحركة <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="store_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" {{ ($originalInvoice && $originalInvoice->store_id == $st->id) || old('store_id') == $st->id ? 'selected' : '' }}>
                                    {{ $st->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            تاريخ المرتجع <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                    </div>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="erp-card" style="padding: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                    <h3 style="font-size: 15.5px; font-weight: 800; color: #DC2626; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-boxes-stacked"></i> الأصناف والكميات المرتجعة
                    </h3>
                    <button type="button" onclick="addRetRow()" class="btn-primary-custom" style="padding: 6px 14px; font-size: 12.5px; background: #DC2626;">
                        <i class="fa-solid fa-plus"></i> إضافة سطر مرتجع
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="erp-table" style="width: 100%;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th style="width: 45%;">الصنف</th>
                                <th style="width: 15%;">الكمية المرتجعة</th>
                                <th style="width: 18%;">سعر الوحدة</th>
                                <th style="width: 18%;">الإجمالي المسترد</th>
                                <th style="width: 4%;">حذف</th>
                            </tr>
                        </thead>
                        <tbody id="retItemsBody">
                            <!-- Rows added dynamically -->
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 14px; margin-top: 16px;">
                    <span style="font-size: 15px; font-weight: 900; color: var(--text-main);">صافي المبلغ المرتجع:</span>
                    <strong id="displayRetTotal" style="font-size: 20px; font-weight: 900; color: #DC2626;">0.00</strong>
                </div>
            </div>

            <!-- Notes -->
            <div class="erp-card" style="padding: 22px;">
                <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                    سبب الإرجاع وملاحظات الجودة / الفحص
                </label>
                <textarea name="notes" rows="2" placeholder="أذكر سبب الإرجاع: تالف، صنف غير مطابق، رغبة العميل..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit Button Bar -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('invoices.returns.index') }}" style="background: #E2E8F0; padding: 12px 24px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 14px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; font-size: 15px; background: #DC2626;">
                    <i class="fa-solid fa-check"></i>
                    اعتماد المرتجع وضبط المخزن والحسابات
                </button>
            </div>

        </div>
    </form>
</div>

<script>
const retItems = @json($items);
let retRowIdx = 0;

function addRetRow() {
    const tbody = document.getElementById('retItemsBody');
    const tr = document.createElement('tr');
    tr.id = 'ret_row_' + retRowIdx;

    let itemsOptions = '<option value="">-- اختر الصنف --</option>';
    retItems.forEach(item => {
        itemsOptions += `<option value="${item.id}" data-price="${item.selling_price}">${item.name} (${item.code})</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="items[${retRowIdx}][item_id]" onchange="onRetItemSelect(${retRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 600;">
                ${itemsOptions}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01" name="items[${retRowIdx}][quantity]" value="1.00" oninput="calcRetRow(${retRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: center;">
        </td>
        <td>
            <input type="number" step="0.01" min="0" name="items[${retRowIdx}][unit_price]" value="0.00" oninput="calcRetRow(${retRowIdx})" required style="width: 100%; padding: 8px 10px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700; text-align: left;">
        </td>
        <td>
            <span id="ret_row_total_${retRowIdx}" style="font-weight: 800; color: #DC2626; font-size: 14px;">0.00</span>
        </td>
        <td style="text-align: center;">
            <button type="button" onclick="removeRetRow(${retRowIdx})" style="background: none; border: none; color: #DC2626; cursor: pointer; font-size: 16px;">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    `;

    tbody.appendChild(tr);
    retRowIdx++;
}

function removeRetRow(idx) {
    const row = document.getElementById('ret_row_' + idx);
    if (row) {
        row.remove();
        calcRetGrandTotal();
    }
}

function onRetItemSelect(idx) {
    const row = document.getElementById('ret_row_' + idx);
    const select = row.querySelector(`select[name="items[${idx}][item_id]"]`);
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;

    const price = parseFloat(opt.dataset.price) || 0;
    row.querySelector(`input[name="items[${idx}][unit_price]"]`).value = price.toFixed(2);
    calcRetRow(idx);
}

function calcRetRow(idx) {
    const row = document.getElementById('ret_row_' + idx);
    if (!row) return;

    const qty = parseFloat(row.querySelector(`input[name="items[${idx}][quantity]"]`).value) || 0;
    const price = parseFloat(row.querySelector(`input[name="items[${idx}][unit_price]"]`).value) || 0;
    const lineTotal = qty * price;

    document.getElementById('ret_row_total_' + idx).innerText = lineTotal.toFixed(2);
    calcRetGrandTotal();
}

function calcRetGrandTotal() {
    let total = 0;
    const tbody = document.getElementById('retItemsBody');
    tbody.querySelectorAll('tr').forEach(tr => {
        const idMatch = tr.id.split('_')[2];
        if (idMatch !== undefined) {
            const rowTotal = parseFloat(document.getElementById('ret_row_total_' + idMatch).innerText) || 0;
            total += rowTotal;
        }
    });

    document.getElementById('displayRetTotal').innerText = total.toFixed(2);
}

document.addEventListener('DOMContentLoaded', function() {
    addRetRow();
});
</script>
@endsection

