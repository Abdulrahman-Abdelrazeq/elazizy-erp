@extends('layouts.app')

@section('title', 'الموازنات التقديرية والتخطيط المالي (Financial Budgets)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-bullseye"></i>
                </span>
                الموازنات التقديرية وتحليل الانحراف (Budgets & Variance Planning)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد الموازنات المالية المستهدفة للإيرادات والمصروفات ومقارنتها دورياً بالأداء الفعلي المحقق
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('reports.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                مركز التقارير
            </a>
            <button onclick="openBudgetModal()" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة مستهدف موازنة جديد
            </button>
        </div>
    </div>

    <!-- Filter by Year & Summary -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('reports.budgets.index') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">السنة المالية</label>
                <select name="year" class="erp-input" onchange="this.form.submit()" style="font-weight: 800; width: 140px;">
                    @for($y = date('Y') + 1; $y >= date('Y') - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div style="margin-right: auto; display: flex; gap: 24px; align-items: center; flex-wrap: wrap;">
                <div>
                    <span style="font-size: 11.5px; color: var(--text-muted); font-weight: 700; display: block;">إجمالي الموازنة المستهدفة:</span>
                    <strong style="font-size: 17px; font-weight: 900; color: var(--primary);">{{ formatMoney($totalBudget) }}</strong>
                </div>
                <div>
                    <span style="font-size: 11.5px; color: var(--text-muted); font-weight: 700; display: block;">الفعلي المنفذ من القيود:</span>
                    <strong style="font-size: 17px; font-weight: 900; color: #4338CA;">{{ formatMoney($totalActual) }}</strong>
                </div>
                <div>
                    <span style="font-size: 11.5px; color: var(--text-muted); font-weight: 700; display: block;">صافي الانحراف المالي:</span>
                    <strong style="font-size: 17px; font-weight: 900; color: {{ $totalVariance >= 0 ? '#10B981' : '#DC2626' }};">
                        {{ formatMoney($totalVariance) }}
                    </strong>
                </div>
            </div>
        </form>
    </div>

    <!-- Budgets Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 45px;">#</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">كود الحساب</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم الحساب المالي</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 100px;">الفترة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">المستهدف التقديري</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الفعلي المحقق</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الانحراف (Variance)</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 130px;">نسبة الإنجاز</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 70px;">حذف</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $idx => $b)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; text-align: center; font-weight: 700; color: var(--text-muted);">
                            {{ $idx + 1 }}
                        </td>
                        <td style="padding: 12px; font-family: monospace; font-weight: 800; color: var(--primary);">
                            {{ $b->account?->code ?? '-' }}
                        </td>
                        <td style="padding: 12px;">
                            <span style="font-weight: 800; color: var(--text-main); display: block;">{{ $b->account?->name ?? 'حساب محذوف' }}</span>
                            @if($b->notes)
                                <span style="font-size: 11px; color: var(--text-muted);">{{ $b->notes }}</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <span style="background: #F1F5F9; color: var(--text-main); padding: 3px 8px; border-radius: 6px; font-size: 11.5px; font-weight: 700;">
                                {{ $b->period_type === 'annual' ? 'موازنة سنوية' : $b->period_type }}
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 800; color: var(--primary);">
                            {{ formatMoney($b->target_amount) }}
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 800; color: #4338CA;">
                            {{ formatMoney($b->actual_amount) }}
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 800; color: {{ $b->variance >= 0 ? '#10B981' : '#DC2626' }};">
                            {{ formatMoney($b->variance) }}
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                                <span style="font-weight: 800; font-size: 12px; color: {{ $b->achievement_percent > 100 ? '#DC2626' : 'var(--primary)' }};">
                                    {{ $b->achievement_percent }}%
                                </span>
                                <div style="width: 80px; height: 6px; background: #E2E8F0; border-radius: 4px; overflow: hidden;">
                                    <div style="width: {{ min(100, $b->achievement_percent) }}%; height: 100%; background: {{ $b->achievement_percent > 100 ? '#DC2626' : 'var(--primary)' }};"></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <form method="POST" action="{{ route('reports.budgets.destroy', $b->id) }}" onsubmit="return confirm('هل تريد حذف هذا المستهدف التقديري؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #EF4444; font-size: 15px; cursor: pointer; opacity: 0.8;">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="padding: 30px; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-bullseye" style="font-size: 28px; display: block; margin-bottom: 8px; opacity: 0.4;"></i>
                            لم يتم تسجيل موازنات تقديرية للسنة المالية {{ $year }}. اضغط زر "إضافة مستهدف موازنة جديد" للبدء.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Create Budget Target Modal -->
<div class="theme-modal" id="budgetModal">
    <div class="modal-box" style="max-width: 500px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h3 style="font-size: 17px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-bullseye"></i>
                إضافة / تعديل موازنة تقديرية لحساب
            </h3>
            <button onclick="closeBudgetModal()" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('reports.budgets.store') }}">
            @csrf

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">السنة المالية <span style="color: #EF4444;">*</span></label>
                    <input type="number" name="fiscal_year" class="erp-input" value="{{ $year }}" required min="2020" max="2050">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الحساب المالي <span style="color: #EF4444;">*</span></label>
                    <select name="account_id" class="erp-input" required>
                        <option value="">-- اختر الحساب المالي --</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}">
                                {{ $acc->code }} - {{ $acc->name }} ({{ $acc->type_label }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">نوع الفترة</label>
                    <select name="period_type" class="erp-input" required>
                        <option value="annual">موازنة سنوية كاملة (Annual)</option>
                        <option value="q1">الربع الأول (Q1)</option>
                        <option value="q2">الربع الثاني (Q2)</option>
                        <option value="q3">الربع الثالث (Q3)</option>
                        <option value="q4">الربع الرابع (Q4)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">المبلغ المستهدف التقديري (Target Amount) <span style="color: #EF4444;">*</span></label>
                    <input type="number" step="0.01" min="0" name="target_amount" class="erp-input" required placeholder="0.00" style="font-weight: 800; font-size: 15px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">ملاحظات تخطيطية</label>
                    <input type="text" name="notes" class="erp-input" placeholder="مثال: الخطة التسويقية للفرع الرئيسي">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="closeBudgetModal()" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">
                        إلغاء
                    </button>
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                        <i class="fa-solid fa-check"></i> حفظ المستهدف
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openBudgetModal() {
    document.getElementById('budgetModal').classList.add('active');
}
function closeBudgetModal() {
    document.getElementById('budgetModal').classList.remove('active');
}
</script>
@endsection
