@extends('layouts.app')

@section('title', 'إنشاء قيد يومية جديد (New Journal Entry)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-plus-circle"></i>
                </span>
                إنشاء قيد يومية مزدوج جديد (Journal Entry)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تسجيل حركة مالية مزدوجة مع التحقق التلقائي من توازن الطرفين (المدين والدائن) وربط مراكز التكلفة
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('accounting.journal-entries.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة لدفتر القيود
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div style="background: #FEE2E2; border: 1px solid #F87171; color: #991B1B; padding: 14px 20px; border-radius: 12px; font-size: 13px;">
            <strong style="display: block; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> يرجى تصحيح الأخطاء التالية:</strong>
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('accounting.journal-entries.store') }}" id="journalEntryForm">
        @csrf

        <!-- General Info Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; border-bottom: 1px dashed var(--border-color); padding-bottom: 12px;">
                <i class="fa-solid fa-circle-info"></i>
                البيانات الأساسية للقيد المحاسبي
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">رقم القيد الآلي</label>
                    <input type="text" class="erp-input" value="{{ $entryNumber }}" readonly style="background: #F1F5F9; font-weight: 800; color: var(--primary); font-family: monospace; font-size: 14px;">
                </div>

                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        تاريخ القيد <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="date" name="date" class="erp-input" value="{{ old('date', date('Y-m-d')) }}" required>
                </div>

                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        رقم المرجع / المستند المرفق
                    </label>
                    <input type="text" name="reference" class="erp-input" value="{{ old('reference') }}" placeholder="مثال: فاتورة رقم 1024 أو شيك 5543">
                </div>

                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        حالة القيد <span style="color: #EF4444;">*</span>
                    </label>
                    <select name="status" class="erp-input" required>
                        <option value="posted" {{ old('status', 'posted') === 'posted' ? 'selected' : '' }}>معتمد ومؤثر على الأرصدة (Posted)</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>مسودة غير مرحلة (Draft)</option>
                    </select>
                </div>
            </div>

            <div style="margin-top: 18px;">
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    البيان العام للقيد (شرح العملية) <span style="color: #EF4444;">*</span>
                </label>
                <textarea name="description" rows="2" class="erp-input" required placeholder="اكتب بياناً واضحاً وشاملاً لموضوع القيد المحاسبي...">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- Journal Lines Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; flex-wrap: wrap; gap: 12px; border-bottom: 1px dashed var(--border-color); padding-bottom: 12px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; margin: 0;">
                        <i class="fa-solid fa-list-ol"></i>
                        أطراف القيد المحاسبي (Journal Lines)
                    </h3>
                    <span style="font-size: 12px; color: var(--text-muted);">أضف سطرين على الأقل مع تحقيق التساوي التام بين إجمالي المدين وإجمالي الدائن</span>
                </div>

                <button type="button" onclick="addNewLine()" style="background: rgba(15, 81, 50, 0.1); border: 1px solid var(--primary); color: var(--primary); padding: 7px 16px; border-radius: 8px; font-weight: 800; font-size: 12.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-plus"></i>
                    إضافة سطر جديد
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;" id="linesTable">
                    <thead>
                        <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 45px;">#</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); min-width: 250px;">الحساب (Account) <span style="color: #EF4444;">*</span></th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); min-width: 170px;">مركز التكلفة</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); min-width: 140px;">مدين (Debit)</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); min-width: 140px;">دائن (Credit)</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); min-width: 220px;">البيان التفصيلي للسطر</th>
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 60px;">حذف</th>
                        </tr>
                    </thead>
                    <tbody id="linesTbody">
                        <!-- Default 2 Rows -->
                        @for ($i = 0; $i < 2; $i++)
                        <tr class="line-row" data-index="{{ $i }}">
                            <td style="padding: 10px; text-align: center; font-weight: 800; color: var(--text-muted); border-bottom: 1px solid var(--border-color);" class="row-num">
                                {{ $i + 1 }}
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                <select name="lines[{{ $i }}][account_id]" class="erp-input account-select" required style="font-size: 12.5px;">
                                    <option value="">-- اختر الحساب المالي --</option>
                                    @foreach($leafAccounts as $acc)
                                        <option value="{{ $acc->id }}" {{ old("lines.{$i}.account_id") == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }} ({{ $acc->nature === 'debit' ? 'مدين بطبيعته' : 'دائن بطبيعته' }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                <select name="lines[{{ $i }}][cost_center_id]" class="erp-input" style="font-size: 12.5px;">
                                    <option value="">-- اختياري --</option>
                                    @foreach($costCenters as $cc)
                                        <option value="{{ $cc->id }}" {{ old("lines.{$i}.cost_center_id") == $cc->id ? 'selected' : '' }}>
                                            {{ $cc->code }} - {{ $cc->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                <input type="number" step="0.01" min="0" name="lines[{{ $i }}][debit]" class="erp-input line-debit" value="{{ old("lines.{$i}.debit", '0.00') }}" oninput="handleDebitInput(this)" style="font-weight: 800; color: #0F5132; text-align: right;">
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                <input type="number" step="0.01" min="0" name="lines[{{ $i }}][credit]" class="erp-input line-credit" value="{{ old("lines.{$i}.credit", '0.00') }}" oninput="handleCreditInput(this)" style="font-weight: 800; color: #4338CA; text-align: right;">
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
                                <input type="text" name="lines[{{ $i }}][description]" class="erp-input" value="{{ old("lines.{$i}.description") }}" placeholder="شرح اختياري لهذا الطرف">
                            </td>
                            <td style="padding: 10px; text-align: center; border-bottom: 1px solid var(--border-color);">
                                <button type="button" onclick="removeLine(this)" style="background: none; border: none; color: #EF4444; font-size: 16px; cursor: pointer; opacity: 0.8;" title="حذف السطر">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Double-Entry Balancing Verification Bar -->
            <div style="margin-top: 24px; padding: 20px; border-radius: 14px; background: #F8FAFC; border: 2px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;" id="balanceBar">
                <div style="display: flex; gap: 30px; flex-wrap: wrap; align-items: center;">
                    <div>
                        <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المدين (Debit)</span>
                        <strong id="displayTotalDebit" style="font-size: 22px; font-weight: 900; color: #0F5132;">0.00 ج.م</strong>
                    </div>

                    <div style="font-size: 22px; color: var(--text-muted); font-weight: 300;">=</div>

                    <div>
                        <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الدائن (Credit)</span>
                        <strong id="displayTotalCredit" style="font-size: 22px; font-weight: 900; color: #4338CA;">0.00 ج.م</strong>
                    </div>

                    <div style="height: 36px; width: 1px; background: var(--border-color);"></div>

                    <div>
                        <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">الفرق المحاسبي</span>
                        <strong id="displayDifference" style="font-size: 18px; font-weight: 900; color: #EF4444;">0.00 ج.م</strong>
                    </div>
                </div>

                <div id="balanceBadge" style="padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 800; display: inline-flex; align-items: center; gap: 10px; background: #FEE2E2; color: #991B1B; border: 1px solid #F87171;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>القيد غير متوازن</span>
                </div>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('accounting.journal-entries.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 14px;">
                    إلغاء الأمر
                </a>
                <button type="submit" id="submitBtn" class="btn-primary-custom" style="padding: 12px 32px; border-radius: 10px; font-size: 14px; font-weight: 800;">
                    <i class="fa-solid fa-check-circle"></i>
                    حفظ واعتِماد القيد المحاسبي
                </button>
            </div>
        </div>
    </form>
</div>

<template id="rowTemplate">
    <tr class="line-row" data-index="__INDEX__">
        <td style="padding: 10px; text-align: center; font-weight: 800; color: var(--text-muted); border-bottom: 1px solid var(--border-color);" class="row-num">
            __ROW_NUM__
        </td>
        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
            <select name="lines[__INDEX__][account_id]" class="erp-input account-select" required style="font-size: 12.5px;">
                <option value="">-- اختر الحساب المالي --</option>
                @foreach($leafAccounts as $acc)
                    <option value="{{ $acc->id }}">
                        {{ $acc->code }} - {{ $acc->name }} ({{ $acc->nature === 'debit' ? 'مدين بطبيعته' : 'دائن بطبيعته' }})
                    </option>
                @endforeach
            </select>
        </td>
        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
            <select name="lines[__INDEX__][cost_center_id]" class="erp-input" style="font-size: 12.5px;">
                <option value="">-- اختياري --</option>
                @foreach($costCenters as $cc)
                    <option value="{{ $cc->id }}">
                        {{ $cc->code }} - {{ $cc->name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
            <input type="number" step="0.01" min="0" name="lines[__INDEX__][debit]" class="erp-input line-debit" value="0.00" oninput="handleDebitInput(this)" style="font-weight: 800; color: #0F5132; text-align: right;">
        </td>
        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
            <input type="number" step="0.01" min="0" name="lines[__INDEX__][credit]" class="erp-input line-credit" value="0.00" oninput="handleCreditInput(this)" style="font-weight: 800; color: #4338CA; text-align: right;">
        </td>
        <td style="padding: 10px; border-bottom: 1px solid var(--border-color);">
            <input type="text" name="lines[__INDEX__][description]" class="erp-input" placeholder="شرح اختياري لهذا الطرف">
        </td>
        <td style="padding: 10px; text-align: center; border-bottom: 1px solid var(--border-color);">
            <button type="button" onclick="removeLine(this)" style="background: none; border: none; color: #EF4444; font-size: 16px; cursor: pointer; opacity: 0.8;" title="حذف السطر">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    </tr>
</template>

<script>
let rowIndex = {{ max(2, count(old('lines', []))) }};

function handleDebitInput(input) {
    let val = parseFloat(input.value) || 0;
    if (val > 0) {
        let row = input.closest('tr');
        let creditInput = row.querySelector('.line-credit');
        if (creditInput) creditInput.value = '0.00';
    }
    recalculateTotals();
}

function handleCreditInput(input) {
    let val = parseFloat(input.value) || 0;
    if (val > 0) {
        let row = input.closest('tr');
        let debitInput = row.querySelector('.line-debit');
        if (debitInput) debitInput.value = '0.00';
    }
    recalculateTotals();
}

function addNewLine() {
    let template = document.getElementById('rowTemplate').innerHTML;
    let nextIndex = rowIndex++;
    let rowCount = document.querySelectorAll('#linesTbody tr').length + 1;

    let html = template
        .replace(/__INDEX__/g, nextIndex)
        .replace(/__ROW_NUM__/g, rowCount);

    let tbody = document.getElementById('linesTbody');
    let tr = document.createElement('tr');
    tr.className = 'line-row';
    tr.setAttribute('data-index', nextIndex);
    tr.innerHTML = html.replace(/^<tr[^>]*>|<\/tr>$/g, '');
    tbody.appendChild(tr);

    reindexRows();
    recalculateTotals();
}

function removeLine(btn) {
    let rows = document.querySelectorAll('#linesTbody tr');
    if (rows.length <= 2) {
        alert('يجب أن يحتوي القيد المحاسبي على طرفين على الأقل (مدين ودائن)!');
        return;
    }
    btn.closest('tr').remove();
    reindexRows();
    recalculateTotals();
}

function reindexRows() {
    let rows = document.querySelectorAll('#linesTbody tr');
    rows.forEach((row, idx) => {
        let numEl = row.querySelector('.row-num');
        if (numEl) numEl.textContent = idx + 1;
    });
}

function recalculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;

    document.querySelectorAll('.line-debit').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.line-credit').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });

    let diff = Math.abs(totalDebit - totalCredit);

    document.getElementById('displayTotalDebit').textContent = totalDebit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ج.م';
    document.getElementById('displayTotalCredit').textContent = totalCredit.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ج.م';
    document.getElementById('displayDifference').textContent = diff.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ج.م';

    let badge = document.getElementById('balanceBadge');
    let bar = document.getElementById('balanceBar');
    let submitBtn = document.getElementById('submitBtn');

    if (diff <= 0.01 && totalDebit > 0) {
        badge.style.background = '#D1E7DD';
        badge.style.color = '#0F5132';
        badge.style.borderColor = '#A3CFBB';
        badge.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>القيد متوازن محاسبياً ومكتمل</span>';
        bar.style.borderColor = '#10B981';
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
    } else {
        badge.style.background = '#FEE2E2';
        badge.style.color = '#991B1B';
        badge.style.borderColor = '#F87171';
        let msg = totalDebit <= 0 ? 'القيد بقيمة صفرية' : 'القيد غير متوازن (فارق: ' + diff.toFixed(2) + ' ج.م)';
        badge.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> <span>' + msg + '</span>';
        bar.style.borderColor = '#EF4444';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.6';
    }
}

document.getElementById('journalEntryForm').addEventListener('submit', function(e) {
    let totalDebit = 0;
    let totalCredit = 0;

    document.querySelectorAll('.line-debit').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.line-credit').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });

    let diff = Math.abs(totalDebit - totalCredit);

    if (diff > 0.01 || totalDebit <= 0) {
        e.preventDefault();
        alert('لا يمكن حفظ القيد! تأكد من أن إجمالي المدين يساوي إجمالي الدائن تماماً وأن القيمة أكبر من الصفر.');
        return false;
    }
});

// Run once on initial load
document.addEventListener('DOMContentLoaded', function() {
    recalculateTotals();
});
</script>
@endsection

