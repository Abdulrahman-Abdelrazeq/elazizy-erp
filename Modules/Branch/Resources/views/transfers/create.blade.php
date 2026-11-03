@extends('layouts.app')

@section('title', 'إنشاء إذن تحويل بضاعة بين الفروع')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-truck-ramp-box"></i>
                </span>
                إنشاء طلب وإذن تحويل بيني (New Inter-Branch Transfer)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد الفرع والمخزن المصدر والوجهة، بيانات الناقل والسائق، والأصناف المحولة
            </span>
        </div>

        <a href="{{ route('branches.transfers.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لقائمة التحويلات
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

    <form action="{{ route('branches.transfers.store') }}" method="POST" id="transferForm">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 20px;">
            
            <!-- Route & Locations Card -->
            <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
                <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    <i class="fa-solid fa-route" style="color: var(--primary);"></i>
                    مسار الشحن والتحويل (Source & Destination)
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px;">
                    <!-- Source Branch -->
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            الفرع المصدر (الشاحن) <span style="color: red;">*</span>
                        </label>
                        <select name="from_branch_id" id="from_branch_id" class="erp-input" required>
                            <option value="">-- اختر الفرع المصدر --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('from_branch_id', activeBranch()?->id) == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Source Store -->
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            المستودع المصدر (خصم الكميات) <span style="color: red;">*</span>
                        </label>
                        <select name="from_store_id" id="from_store_id" class="erp-input" required>
                            <option value="">-- اختر مستودع المصدر --</option>
                            @foreach($stores as $s)
                                <option value="{{ $s->id }}" {{ old('from_store_id') == $s->id ? 'selected' : '' }} data-branch="{{ $s->branch_id }}">
                                    {{ $s->name }} @if($s->branch) [فرع: {{ $s->branch->name }}] @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Destination Branch -->
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            الفرع الوجهة (المستلم) <span style="color: red;">*</span>
                        </label>
                        <select name="to_branch_id" id="to_branch_id" class="erp-input" required>
                            <option value="">-- اختر فرع الوجهة --</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('to_branch_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->name }} ({{ $b->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Destination Store -->
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            المستودع المستلم (إضافة الكميات) <span style="color: red;">*</span>
                        </label>
                        <select name="to_store_id" id="to_store_id" class="erp-input" required>
                            <option value="">-- اختر مستودع الاستلام --</option>
                            @foreach($stores as $s)
                                <option value="{{ $s->id }}" {{ old('to_store_id') == $s->id ? 'selected' : '' }} data-branch="{{ $s->branch_id }}">
                                    {{ $s->name }} @if($s->branch) [فرع: {{ $s->branch->name }}] @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Transfer Date -->
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            تاريخ التحويل <span style="color: red;">*</span>
                        </label>
                        <input type="date" name="transfer_date" class="erp-input" value="{{ old('transfer_date', date('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <!-- Logistics & Carrier Info -->
            <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
                <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    <i class="fa-solid fa-truck" style="color: var(--accent-gold);"></i>
                    بيانات النقل واللوجستيات (Carrier & Logistics)
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">شركة الشحن / الناقل</label>
                        <input type="text" name="shipping_company" class="erp-input" value="{{ old('shipping_company') }}" placeholder="مثال: أرامكس، ناقل، أسطول الشركة الداخلي...">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اسم السائق المفوّض</label>
                        <input type="text" name="driver_name" class="erp-input" value="{{ old('driver_name') }}" placeholder="اسم السائق...">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">رقم لوحة المركبة</label>
                        <input type="text" name="vehicle_plate" class="erp-input" value="{{ old('vehicle_plate') }}" placeholder="مثال: أ ب ج 1234">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">رقم البوليصة / التتبع</label>
                        <input type="text" name="tracking_number" class="erp-input" value="{{ old('tracking_number') }}" placeholder="Waybill / Tracking #">
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">ملاحظات وتعليمات الشحنة</label>
                    <textarea name="notes" class="erp-input" rows="2" placeholder="أي شروط نقل خاصة، تعليمات تفريغ، أو تفاصيل أخرى...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Items Table Card -->
            <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i>
                        الأصناف المحولة (Transfer Items)
                    </h4>
                    <button type="button" id="addItemBtn" class="btn-primary-custom" style="padding: 7px 14px; font-size: 12px; font-weight: 700; border-radius: 8px;">
                        <i class="fa-solid fa-plus"></i> إضافة صنف للتحويل
                    </button>
                </div>

                <div style="overflow-x: auto;">
                    <table class="erp-table" id="itemsTable" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                        <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                            <tr>
                                <th style="padding: 10px 14px; width: 45%;">الصنف</th>
                                <th style="padding: 10px 14px; width: 20%;">الكمية المحولة</th>
                                <th style="padding: 10px 14px; width: 15%;">متوسط التكلفة التقريبية</th>
                                <th style="padding: 10px 14px; width: 15%;">الإجمالي</th>
                                <th style="padding: 10px 14px; width: 5%; text-align: center;">إلغاء</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            <tr class="item-row" data-index="0" style="border-bottom: 1px solid #EDF2F7;">
                                <td style="padding: 10px 14px;">
                                    <select name="items[0][item_id]" class="erp-input item-select" required onchange="updateItemCost(this)">
                                        <option value="">-- اختر الصنف --</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-cost="{{ $item->avg_cost ?: $item->buy_price ?: 0 }}">
                                                {{ $item->name }} (كود: {{ $item->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="padding: 10px 14px;">
                                    <input type="number" step="0.001" min="0.001" name="items[0][quantity]" class="erp-input item-qty" value="1" required oninput="calculateRowTotal(this)">
                                </td>
                                <td style="padding: 10px 14px;">
                                    <span class="item-cost-display" style="font-weight: 700; color: var(--text-main);">0.00 ر.س</span>
                                </td>
                                <td style="padding: 10px 14px;">
                                    <span class="item-total-display" style="font-weight: 800; color: var(--primary);">0.00 ر.س</span>
                                </td>
                                <td style="padding: 10px 14px; text-align: center;">
                                    <button type="button" class="btn-remove-row" onclick="removeRow(this)" style="background: none; border: none; color: #DC2626; cursor: pointer; font-size: 16px;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot style="background: #F8FAFC; border-top: 2px solid var(--border-color); font-weight: 800;">
                            <tr>
                                <td colspan="3" style="padding: 12px 14px; text-align: left;">إجمالي القيمة التقديرية للشحنة:</td>
                                <td colspan="2" style="padding: 12px 14px; color: var(--primary); font-size: 15px;">
                                    <span id="grandTotalCost">0.00</span> ر.س
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('branches.transfers.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px;">
                    إلغاء وتراجع
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; font-size: 14px; font-weight: 800; border-radius: 10px;">
                    <i class="fa-solid fa-save"></i> حفظ إذن التحويل (مسودة)
                </button>
            </div>

        </div>
    </form>

</div>

<script>
let rowIndex = 1;

function updateItemCost(selectEl) {
    const row = selectEl.closest('.item-row');
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    const cost = parseFloat(selectedOption.getAttribute('data-cost') || 0);
    
    row.querySelector('.item-cost-display').innerText = cost.toFixed(2) + ' ر.س';
    calculateRowTotal(row.querySelector('.item-qty'));
}

function calculateRowTotal(qtyInput) {
    const row = qtyInput.closest('.item-row');
    const selectEl = row.querySelector('.item-select');
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    const cost = parseFloat(selectedOption?.getAttribute('data-cost') || 0);
    const qty = parseFloat(qtyInput.value || 0);
    const total = qty * cost;

    row.querySelector('.item-total-display').innerText = total.toFixed(2) + ' ر.س';
    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grand = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const selectEl = row.querySelector('.item-select');
        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const cost = parseFloat(selectedOption?.getAttribute('data-cost') || 0);
        const qty = parseFloat(row.querySelector('.item-qty').value || 0);
        grand += (qty * cost);
    });
    document.getElementById('grandTotalCost').innerText = grand.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

document.getElementById('addItemBtn').addEventListener('click', function() {
    const tbody = document.getElementById('itemsTableBody');
    const firstRow = tbody.querySelector('.item-row');
    const newRow = firstRow.cloneNode(true);

    newRow.setAttribute('data-index', rowIndex);
    const select = newRow.querySelector('.item-select');
    select.name = `items[${rowIndex}][item_id]`;
    select.selectedIndex = 0;

    const qty = newRow.querySelector('.item-qty');
    qty.name = `items[${rowIndex}][quantity]`;
    qty.value = 1;

    newRow.querySelector('.item-cost-display').innerText = '0.00 ر.س';
    newRow.querySelector('.item-total-display').innerText = '0.00 ر.س';

    tbody.appendChild(newRow);
    rowIndex++;
});

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        btn.closest('.item-row').remove();
        calculateGrandTotal();
    } else {
        alert('يجب أن يحتوي إذن التحويل على صنف واحد على الأقل.');
    }
}
</script>
@endsection
