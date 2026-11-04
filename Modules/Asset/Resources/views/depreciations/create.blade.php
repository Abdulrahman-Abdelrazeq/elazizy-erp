@extends('layouts.app')

@section('title', 'تشغيل دورة إهلاك للأصول الثابتة')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 900px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-play"></i>
                </span>
                تشغيل دورة إهلاك الأصول الثابتة (Run Asset Depreciation)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تشغيل آلي مجمع أو فردي لحساب قسط الإهلاك وتوليد القيود المحاسبية لليومية العامة
            </span>
        </div>

        <a href="{{ route('assets.depreciations.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            سجل الإهلاكات
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

    <!-- Mode Selector Tabs -->
    <div style="display: flex; gap: 10px; border-bottom: 2px solid var(--border-color); padding-bottom: 12px;">
        <button type="button" id="tabBatchBtn" onclick="switchDepTab('batch')" class="btn-primary-custom" style="padding: 10px 20px; font-size: 13px; font-weight: 800; border-radius: 8px;">
            <i class="fa-solid fa-bolt"></i> تشغيل إهلاك مجمع (Batch Engine)
        </button>
        <button type="button" id="tabSingleBtn" onclick="switchDepTab('single')" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 10px 20px; font-size: 13px; font-weight: 700; border-radius: 8px; color: var(--text-main); cursor: pointer;">
            <i class="fa-solid fa-cube"></i> إهلاك فردي لأصل محدد
        </button>
    </div>

    <!-- Tab 1: Batch Run -->
    <div id="batchTabContent">
        <form action="{{ route('assets.depreciations.run-batch') }}" method="POST">
            @csrf

            <div class="erp-card" style="padding: 24px;">
                <div style="background: #D1E7DD; border-right: 4px solid #0F5132; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; color: #0F5132; font-weight: 700;">
                    <i class="fa-solid fa-circle-info" style="margin-left: 6px;"></i>
                    سيقوم النظام بحساب أقساط الإهلاك المستحقة آلياً لجميع الأصول النشطة وفقاً لطريقة الإهلاك ونسب كل تصنيف مع تحديث صافي القيمة الدفترية.
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            السنة المالية <span style="color: red;">*</span>
                        </label>
                        <input type="number" name="fiscal_year" class="erp-input" value="{{ old('fiscal_year', date('Y')) }}" required>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            الفترة المالية <span style="color: red;">*</span>
                        </label>
                        <input type="text" name="fiscal_period" class="erp-input" value="{{ old('fiscal_period', date('Y-m')) }}" placeholder="مثال: 2026-11 أو 2026-Q4 أو 2026-Annual" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-bottom: 18px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            نوع دورة الإهلاك <span style="color: red;">*</span>
                        </label>
                        <select name="period_type" class="erp-input" required>
                            <option value="monthly" {{ old('period_type') === 'monthly' ? 'selected' : '' }}>إهلاك شهري (Monthly - القسط السنوي ÷ 12)</option>
                            <option value="annual" {{ old('period_type') === 'annual' ? 'selected' : '' }}>إهلاك سنوي كامل (Annual Full Year)</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">تخصيص لفرع محدد</label>
                        <select name="branch_id" class="erp-input">
                            <option value="">جميع الفروع (شبكة المؤسسة بالكامل)</option>
                            @foreach($branches as $b)
                                <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 8px; padding: 14px 18px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="post_to_gl" id="post_to_gl_batch" value="1" {{ old('post_to_gl', true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                        <div>
                            <label for="post_to_gl_batch" style="font-size: 13.5px; font-weight: 800; color: var(--text-main); cursor: pointer; display: block;">
                                ترحيل القيود تلقائياً لدفتر اليومية العامة (Post to General Ledger)
                            </label>
                            <span style="font-size: 11.5px; color: var(--text-muted);">
                                يتم إنشاء قيود يومية متوازنة آلياً: من ح/ مصروف إهلاك الأصول إلى ح/ مجمع إهلاك الأصول الثابتة.
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; font-size: 14px; font-weight: 800; border-radius: 10px;">
                        <i class="fa-solid fa-play"></i> بدء تشغيل وحساب الإهلاك المجمع
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tab 2: Single Run -->
    <div id="singleTabContent" style="display: none;">
        <form action="{{ route('assets.depreciations.store') }}" method="POST">
            @csrf

            <div class="erp-card" style="padding: 24px;">
                <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                    <i class="fa-solid fa-cube" style="color: var(--accent-gold);"></i>
                    تسجيل إهلاك يدوي لأصل محدد
                </h4>

                <div style="margin-bottom: 18px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                        اختر الأصل الثابت <span style="color: red;">*</span>
                    </label>
                    <select name="asset_id" id="single_asset_id" class="erp-input" required onchange="updateSingleAssetCost(this)">
                        <option value="">-- اختر الأصل --</option>
                        @foreach($assets as $ast)
                            <option value="{{ $ast->id }}"
                                data-monthly="{{ $ast->calculateMonthlyDepreciation() }}"
                                data-annual="{{ $ast->calculateAnnualDepreciation() }}"
                                data-book="{{ $ast->current_book_value }}">
                                {{ $ast->name }} ({{ $ast->asset_number }}) - القيمة الدفترية: {{ number_format($ast->current_book_value, 2) }} ر.س
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 18px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            تاريخ الإهلاك <span style="color: red;">*</span>
                        </label>
                        <input type="date" name="depreciation_date" class="erp-input" value="{{ old('depreciation_date', date('Y-m-d')) }}" required>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            السنة المالية <span style="color: red;">*</span>
                        </label>
                        <input type="number" name="fiscal_year" class="erp-input" value="{{ old('fiscal_year', date('Y')) }}" required>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            الفترة المالية <span style="color: red;">*</span>
                        </label>
                        <input type="text" name="fiscal_period" class="erp-input" value="{{ old('fiscal_period', date('Y-m')) }}" required>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                            مبلغ قسط الإهلاك (ر.س) <span style="color: red;">*</span>
                        </label>
                        <input type="number" step="0.01" min="0.01" name="depreciation_amount" id="single_dep_amount" class="erp-input" value="{{ old('depreciation_amount') }}" required>
                    </div>
                </div>

                <div style="margin-bottom: 18px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">ملاحظات القيد</label>
                    <textarea name="notes" class="erp-input" rows="2" placeholder="أي تفاصيل خاصة بالقيد المحاسبي...">{{ old('notes') }}</textarea>
                </div>

                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 8px; padding: 14px 18px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="post_to_gl" id="post_to_gl_single" value="1" {{ old('post_to_gl', true) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
                        <div>
                            <label for="post_to_gl_single" style="font-size: 13.5px; font-weight: 800; color: var(--text-main); cursor: pointer; display: block;">
                                ترحيل فوري لدفتر اليومية العامة (General Ledger)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; font-size: 14px; font-weight: 800; border-radius: 10px;">
                        <i class="fa-solid fa-save"></i> حفظ وترحيل قيد الإهلاك
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>

<script>
function switchDepTab(tab) {
    if (tab === 'batch') {
        document.getElementById('batchTabContent').style.display = 'block';
        document.getElementById('singleTabContent').style.display = 'none';

        document.getElementById('tabBatchBtn').className = 'btn-primary-custom';
        document.getElementById('tabBatchBtn').style.background = '';
        document.getElementById('tabBatchBtn').style.color = '';

        document.getElementById('tabSingleBtn').className = '';
        document.getElementById('tabSingleBtn').style.background = '#F8FAFC';
        document.getElementById('tabSingleBtn').style.color = 'var(--text-main)';
    } else {
        document.getElementById('batchTabContent').style.display = 'none';
        document.getElementById('singleTabContent').style.display = 'block';

        document.getElementById('tabSingleBtn').className = 'btn-primary-custom';
        document.getElementById('tabSingleBtn').style.background = '';
        document.getElementById('tabSingleBtn').style.color = '';

        document.getElementById('tabBatchBtn').className = '';
        document.getElementById('tabBatchBtn').style.background = '#F8FAFC';
        document.getElementById('tabBatchBtn').style.color = 'var(--text-main)';
    }
}

function updateSingleAssetCost(selectEl) {
    const selected = selectEl.options[selectEl.selectedIndex];
    if (!selected) return;

    const monthly = selected.getAttribute('data-monthly');
    if (monthly && parseFloat(monthly) > 0) {
        document.getElementById('single_dep_amount').value = parseFloat(monthly).toFixed(2);
    }
}
</script>
@endsection
