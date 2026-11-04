@extends('layouts.app')

@section('title', 'تسجيل استبعاد أو بيع أصل ثابت')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 900px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #DC2626; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-recycle"></i>
                </span>
                تسجيل استبعاد أو بيع أصل ثابت (Asset Disposal / Sale)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                معالجة بيع أو تكهين الأصل وحساب الأرباح والخسائر الرأسمالية وإقفال مجمع الإهلاك
            </span>
        </div>

        <a href="{{ route('assets.disposals.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            سجل الاستبعادات
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

    <form action="{{ route('assets.disposals.store') }}" method="POST">
        @csrf

        <div class="erp-card" style="padding: 24px;">
            <div style="margin-bottom: 20px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    اختر الأصل المراد استبعاده أو بيعه <span style="color: red;">*</span>
                </label>
                <select name="asset_id" id="disposal_asset_id" class="erp-input" required onchange="updateAssetBookValue(this)">
                    <option value="">-- اختر الأصل --</option>
                    @foreach($assets as $ast)
                        <option value="{{ $ast->id }}" {{ (old('asset_id') == $ast->id || $selectedAssetId == $ast->id) ? 'selected' : '' }}
                            data-book="{{ $ast->current_book_value }}"
                            data-cost="{{ $ast->purchase_cost }}"
                            data-accum="{{ $ast->total_depreciated_amount }}">
                            {{ $ast->name }} ({{ $ast->asset_number }}) - القيمة الدفترية الحالية: {{ number_format($ast->current_book_value, 2) }} ر.س
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Current Asset Book Status Box -->
            <div id="assetBookBox" style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 16px 20px; margin-bottom: 22px; display: flex; justify-content: space-around; text-align: center;">
                <div>
                    <span style="font-size: 11.5px; color: var(--text-muted); display: block;">تكلفة الشراء التاريخية</span>
                    <strong id="displayCost" style="font-size: 16px; color: var(--text-main);">0.00 ر.س</strong>
                </div>
                <div style="border-right: 1px solid var(--border-color); padding-right: 20px;">
                    <span style="font-size: 11.5px; color: var(--text-muted); display: block;">مجمع الإهلاك الحالي</span>
                    <strong id="displayAccum" style="font-size: 16px; color: #DC2626;">0.00 ر.س</strong>
                </div>
                <div style="border-right: 1px solid var(--border-color); padding-right: 20px;">
                    <span style="font-size: 11.5px; color: var(--text-muted); display: block;">صافي القيمة الدفترية وقت الاستبعاد</span>
                    <strong id="displayBook" style="font-size: 17px; color: #0F5132;">0.00 ر.س</strong>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        نوع عملية الاستبعاد <span style="color: red;">*</span>
                    </label>
                    <select name="disposal_type" id="disposal_type" class="erp-input" required onchange="toggleSaleFields(this.value)">
                        <option value="scrap" {{ old('disposal_type') === 'scrap' ? 'selected' : '' }}>تكهين وتحويل لخردة (Scrapped / Salvage)</option>
                        <option value="sale" {{ old('disposal_type') === 'sale' ? 'selected' : '' }}>بيع الأصل نقداً (Asset Sale)</option>
                        <option value="loss" {{ old('disposal_type') === 'loss' ? 'selected' : '' }}>إتلاف أو فقدان (Loss / Write-off)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        تاريخ الاستبعاد / البيع <span style="color: red;">*</span>
                    </label>
                    <input type="date" name="disposal_date" class="erp-input" value="{{ old('disposal_date', date('Y-m-d')) }}" required>
                </div>

                <div id="salePriceDiv" style="display: none;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        سعر البيع المحقق (ر.س)
                    </label>
                    <input type="number" step="0.01" min="0" name="sale_price" id="sale_price" class="erp-input" value="{{ old('sale_price', 0.00) }}" oninput="calculateGainLoss()">
                </div>

                <div id="treasuryDiv" style="display: none;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        الخزينة المودع بها الثمن
                    </label>
                    <select name="treasury_id" class="erp-input">
                        <option value="">-- اختر الخزينة --</option>
                        @foreach($treasuries as $tr)
                            <option value="{{ $tr->id }}" {{ old('treasury_id') == $tr->id ? 'selected' : '' }}>
                                {{ $tr->name }} (الرصيد: {{ number_format($tr->current_balance, 2) }} ر.س)
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اسم المشتري / الجهة المستلمة</label>
                    <input type="text" name="buyer_name" class="erp-input" value="{{ old('buyer_name') }}" placeholder="الاسم أو شركة الخردة أو المشتري...">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">ملاحظات وأسباب الاستبعاد</label>
                    <input type="text" name="notes" class="erp-input" value="{{ old('notes') }}" placeholder="تقرير الفحص الفني، قرار لجنة التكهين...">
                </div>
            </div>

            <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 8px; padding: 14px 18px; margin-bottom: 24px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="post_to_gl" id="post_to_gl_disp" value="1" {{ old('post_to_gl', true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <div>
                        <label for="post_to_gl_disp" style="font-size: 13.5px; font-weight: 800; color: var(--text-main); cursor: pointer; display: block;">
                            توليد قيد إقفال واستبعاد الأصل في دفتر اليومية العامة (GL)
                        </label>
                        <span style="font-size: 11.5px; color: var(--text-muted);">
                            يتم إقفال حساب الأصل التكلفة التاريخية وحساب مجمع الإهلاك وإثبات الأرباح أو الخسائر الرأسمالية.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('assets.disposals.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; font-size: 14px; font-weight: 800; border-radius: 10px; background: #DC2626;">
                    <i class="fa-solid fa-check"></i> تأكيد استبعاد / بيع الأصل
                </button>
            </div>
        </div>
    </form>

</div>

<script>
function updateAssetBookValue(selectEl) {
    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt) return;

    const cost = parseFloat(opt.getAttribute('data-cost') || 0);
    const accum = parseFloat(opt.getAttribute('data-accum') || 0);
    const book = parseFloat(opt.getAttribute('data-book') || 0);

    document.getElementById('displayCost').innerText = cost.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' ر.س';
    document.getElementById('displayAccum').innerText = accum.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' ر.س';
    document.getElementById('displayBook').innerText = book.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' ر.س';
}

function toggleSaleFields(type) {
    const saleDiv = document.getElementById('salePriceDiv');
    const treasuryDiv = document.getElementById('treasuryDiv');

    if (type === 'sale') {
        saleDiv.style.display = 'block';
        treasuryDiv.style.display = 'block';
    } else {
        saleDiv.style.display = 'none';
        treasuryDiv.style.display = 'none';
    }
}

// Initial trigger if preselected
document.addEventListener('DOMContentLoaded', function() {
    const assetSelect = document.getElementById('disposal_asset_id');
    if (assetSelect.selectedIndex > 0) {
        updateAssetBookValue(assetSelect);
    }
    toggleSaleFields(document.getElementById('disposal_type').value);
});
</script>
@endsection
