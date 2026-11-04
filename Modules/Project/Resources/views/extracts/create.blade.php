@extends('layouts.app')

@section('title', 'إنشاء مستخلص جاري: ' . $project->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px; max-width: 1200px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <a href="{{ route('projects.projects.index') }}" style="color: var(--primary); text-decoration: none;">المشاريع</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i>
                <a href="{{ route('projects.projects.show', $project->id) }}" style="color: var(--primary); text-decoration: none;">{{ $project->name }}</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i>
                <span>إنشاء مستخلص جاري</span>
            </div>
            <h2 style="font-size: 20px; font-weight: 900; color: var(--text-main); margin: 0;">
                إنشاء مستخلص جاري / ختامي لأعمال المقاولة
            </h2>
        </div>

        <a href="{{ route('projects.projects.show', $project->id) }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لبطاقة المشروع
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div style="background: #FEF2F2; border: 1px solid #F87171; border-radius: 10px; padding: 14px 18px; color: #991B1B; font-size: 13px;">
            <div style="font-weight: 800; margin-bottom: 6px;">يرجى مراجعة الأخطاء التالية:</div>
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.extracts.store', $project->id) }}" id="extract-form">
        @csrf

        <!-- Project & Contract Info Card -->
        <div class="erp-card" style="padding: 20px; margin-bottom: 20px; background: #F8FAFC;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; font-size: 13px;">
                <div>
                    <span style="color: var(--text-muted); display: block;">كود واسم المشروع:</span>
                    <strong style="color: var(--primary);">{{ $project->code }} - {{ $project->name }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">العميل / المالك:</span>
                    <strong>{{ $project->client?->name ?? '-' }}</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">قيمة العقد المعدلة:</span>
                    <strong style="color: var(--text-main);">{{ number_format($project->revised_value, 2) }} ر.س</strong>
                </div>
                <div>
                    <span style="color: var(--text-muted); display: block;">نسبة الاستقطاعات:</span>
                    <strong>مقدمة: {{ $project->advance_payment_percentage }}% | ضمان: {{ $project->retention_percentage }}%</strong>
                </div>
            </div>
        </div>

        <!-- Extract Header Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice"></i>
                1. بيانات المستخلص والفترة
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        رقم المستخلص <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="extract_number" value="{{ old('extract_number', $suggestedNumber) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-family: monospace; background: #F8FAFC;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        نوع المستخلص <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="extract_type" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="progress" {{ old('extract_type', 'progress') == 'progress' ? 'selected' : '' }}>مستخلص جاري (Progress Extract)</option>
                        <option value="advance" {{ old('extract_type') == 'advance' ? 'selected' : '' }}>دفعة مقدمة (Advance Payment)</option>
                        <option value="final" {{ old('extract_type') == 'final' ? 'selected' : '' }}>مستخلص ختامي نهائي (Final Extract)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        تاريخ الإصدار <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date" name="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        فترة الأعمال (من) <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date" name="period_from" value="{{ old('period_from', date('Y-m-01')) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        فترة الأعمال (إلى) <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date" name="period_to" value="{{ old('period_to', date('Y-m-d')) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        اسم المهندس الاستشاري المشرف
                    </label>
                    <input type="text" name="consultant_engineer_name" value="{{ old('consultant_engineer_name') }}" placeholder="المهندس / المكتب الاستشاري" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>
            </div>
        </div>

        <!-- BOQ Items Quantities Table -->
        <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 20px;">
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); background: #F8FAFC;">
                <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-list-check"></i>
                    2. حصر الأعمال وكميات الفترة الحالية من المقايسة (BOQ)
                </h3>
                <span style="font-size: 12px; color: var(--text-muted);">
                    أدخل الكمية المنجزة خلال فترة هذا المستخلص لكل بند (يتم احتساب الكمية التراكمية والمبالغ آلياً)
                </span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                    <thead>
                        <tr style="background: #F1F5F9; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                            <th style="padding: 12px 14px;">كود البند</th>
                            <th style="padding: 12px 14px;">بيان ووصف البند</th>
                            <th style="padding: 12px 14px;">الوحدة</th>
                            <th style="padding: 12px 14px;">الكمية التعاقدية</th>
                            <th style="padding: 12px 14px;">السابق تنفيذه</th>
                            <th style="padding: 12px 14px; min-width: 140px; background: rgba(37, 99, 235, 0.05); color: #2563EB;">كمية الفترة الحالية</th>
                            <th style="padding: 12px 14px;">السعر الإفرادي</th>
                            <th style="padding: 12px 14px;">إجمالي الفترة الحالية</th>
                            <th style="padding: 12px 14px;">الكمية التراكمية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($project->boqItems as $idx => $boq)
                            @php
                                $prevQty = (float) $boq->executed_quantity;
                                $unitPrice = (float) $boq->unit_price;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 12px 14px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                    {{ $boq->item_code }}
                                    <input type="hidden" name="items[{{ $idx }}][boq_item_id]" value="{{ $boq->id }}">
                                </td>
                                <td style="padding: 12px 14px; font-weight: 700; color: var(--text-main);">
                                    {{ $boq->description }}
                                </td>
                                <td style="padding: 12px 14px; color: var(--text-muted);">
                                    {{ $boq->unit }}
                                </td>
                                <td style="padding: 12px 14px; font-weight: 700;">
                                    {{ number_format($boq->revised_quantity, 2) }}
                                </td>
                                <td style="padding: 12px 14px; font-weight: 700; color: var(--text-muted);">
                                    <span id="prev-qty-{{ $idx }}">{{ number_format($prevQty, 2) }}</span>
                                </td>
                                <td style="padding: 10px 14px; background: rgba(37, 99, 235, 0.02);">
                                    <input type="number" step="0.001" min="0" name="items[{{ $idx }}][current_quantity]" id="input-qty-{{ $idx }}" value="{{ old("items.{$idx}.current_quantity", '0') }}" class="current-qty-input" data-idx="{{ $idx }}" data-prev="{{ $prevQty }}" data-price="{{ $unitPrice }}" data-contract="{{ $boq->revised_quantity }}" style="width: 100%; padding: 8px 10px; border: 1px solid #93C5FD; border-radius: 6px; font-size: 13px; font-weight: 800; text-align: center; color: #1E40AF;">
                                </td>
                                <td style="padding: 12px 14px; font-weight: 800;">
                                    {{ number_format($unitPrice, 2) }} <small style="font-size: 10px;">ر.س</small>
                                </td>
                                <td style="padding: 12px 14px; font-weight: 800; color: var(--primary);">
                                    <span id="current-amt-{{ $idx }}">0.00</span> <small style="font-size: 10px;">ر.س</small>
                                </td>
                                <td style="padding: 12px 14px; font-weight: 800; color: #2563EB;">
                                    <span id="total-qty-{{ $idx }}">{{ number_format($prevQty, 2) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Deductions & Financial Summary -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calculator"></i>
                3. ملخص الحسابات والاستقطاعات والضريبة
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                <!-- Deductions Inputs -->
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">استقطاعات وجزاءات أخرى (ر.س)</label>
                        <input type="number" step="0.01" name="other_deductions" id="other-deductions" value="{{ old('other_deductions', '0.00') }}" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">نسبة ضريبة القيمة المضافة (%)</label>
                        <input type="number" step="0.1" name="tax_rate" id="tax-rate" value="{{ old('tax_rate', '15.0') }}" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">ملاحظات المستخلص</label>
                        <textarea name="notes" rows="2" placeholder="أي شروط أو بنود خاصة بالمستخلص..." style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Live Summary Calculated Card -->
                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">إجمالي أعمال الفترة الحالية:</span>
                        <strong id="summary-current-gross" style="color: var(--text-main);">0.00 ر.س</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; color: #DC2626;">
                        <span>خصم الدفعة المقدمة ({{ $project->advance_payment_percentage }}%):</span>
                        <strong id="summary-advance-deduction">- 0.00 ر.س</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; color: #DC2626;">
                        <span>خصم ضمان الأعمال ({{ $project->retention_percentage }}%):</span>
                        <strong id="summary-retention-deduction">- 0.00 ر.س</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; color: #DC2626;">
                        <span>استقطاعات أخرى:</span>
                        <strong id="summary-other-deduction">- 0.00 ر.س</strong>
                    </div>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 8px; display: flex; justify-content: space-between;">
                        <span style="font-weight: 700; color: var(--text-main);">الصافي الخاضع للضريبة:</span>
                        <strong id="summary-subtotal" style="font-size: 14px;">0.00 ر.س</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; color: var(--text-muted);">
                        <span>ضريبة القيمة المضافة (15%):</span>
                        <strong id="summary-tax">0.00 ر.س</strong>
                    </div>

                    <div style="border-top: 2px solid var(--primary); padding-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 15px; font-weight: 900; color: var(--primary);">صافي المستحق للصرف:</span>
                        <strong id="summary-net-due" style="font-size: 20px; font-weight: 900; color: var(--primary);">0.00 ر.س</strong>
                    </div>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('projects.projects.show', $project->id) }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; color: var(--text-main); font-weight: 700; font-size: 14px; text-decoration: none;">
                إلغاء
            </a>
            <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 800; border: none; cursor: pointer;">
                <i class="fa-solid fa-save"></i>
                حفظ وإصدار المستخلص
            </button>
        </div>
    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const advanceRate = {{ (float) ($project->advance_payment_percentage ?? 10) }};
    const retentionRate = {{ (float) ($project->retention_percentage ?? 5) }};
    const qtyInputs = document.querySelectorAll('.current-qty-input');
    const otherDeductionsInput = document.getElementById('other-deductions');
    const taxRateInput = document.getElementById('tax-rate');

    function calculate() {
        let currentGross = 0;

        qtyInputs.forEach(input => {
            const idx = input.getAttribute('data-idx');
            const prev = parseFloat(input.getAttribute('data-prev')) || 0;
            const price = parseFloat(input.getAttribute('data-price')) || 0;
            const currentQty = parseFloat(input.value) || 0;

            const currentAmt = currentQty * price;
            const totalQty = prev + currentQty;

            document.getElementById('current-amt-' + idx).innerText = currentAmt.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            document.getElementById('total-qty-' + idx).innerText = totalQty.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            currentGross += currentAmt;
        });

        const otherDeduction = parseFloat(otherDeductionsInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 15;

        const advanceDeduction = (currentGross * advanceRate) / 100;
        const retentionDeduction = (currentGross * retentionRate) / 100;

        const subtotal = Math.max(0, currentGross - advanceDeduction - retentionDeduction - otherDeduction);
        const taxAmount = (subtotal * taxRate) / 100;
        const netDue = subtotal + taxAmount;

        document.getElementById('summary-current-gross').innerText = currentGross.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
        document.getElementById('summary-advance-deduction').innerText = '- ' + advanceDeduction.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
        document.getElementById('summary-retention-deduction').innerText = '- ' + retentionDeduction.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
        document.getElementById('summary-other-deduction').innerText = '- ' + otherDeduction.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
        document.getElementById('summary-subtotal').innerText = subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
        document.getElementById('summary-tax').innerText = taxAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
        document.getElementById('summary-net-due').innerText = netDue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س';
    }

    qtyInputs.forEach(inp => inp.addEventListener('input', calculate));
    if (otherDeductionsInput) otherDeductionsInput.addEventListener('input', calculate);
    if (taxRateInput) taxRateInput.addEventListener('input', calculate);

    calculate();
});
</script>
@endsection
