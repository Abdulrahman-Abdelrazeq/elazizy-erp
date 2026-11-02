@extends('layouts.app')

@section('title', 'تسويات وشطب الديون والأرصدة')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-scale-balanced" style="color: var(--primary); margin-left: 8px;"></i>
                تسويات وشطب الديون (Balance Settlements & Write-Offs)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                معالجة فروق الأرصدة، شطب الديون المعدومة، وخصومات التسوية للعملاء والموردين
            </span>
        </div>

        <button onclick="document.getElementById('addAdjustmentModal').style.display='flex'" class="btn-primary-custom">
            <i class="fa-solid fa-plus-circle"></i>
            تسجيل تسوية / شطب رصيد
        </button>
    </div>

    <!-- Info Banner -->
    <div style="background: #FEF2F2; border-right: 4px solid #DC2626; padding: 14px 20px; border-radius: 12px; display: flex; align-items: center; gap: 14px;">
        <i class="fa-solid fa-triangle-exclamation" style="font-size: 24px; color: #DC2626;"></i>
        <div style="font-size: 13px; color: #991B1B; line-height: 1.6;">
            <strong>تنبيه مالي محاسبي:</strong> شطب الديون المعدومة أو خصومات التسوية تقوم بتخفيض الرصيد المستحق على العميل في كشف حسابه مباشرة. يرجى التأكد من المرفقات وموافقة الإدارة المالية قبل اعتماد حركة الشطب.
        </div>
    </div>

    <!-- Adjustments Table Card -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>رقم الحركة</th>
                        <th>العميل / المورد</th>
                        <th>تاريخ الحركة</th>
                        <th>نوع التسوية</th>
                        <th>قيمة المبلغ المسوى</th>
                        <th>المستخدم المسؤول</th>
                        <th>البيان ومبرر التسوية</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adjustments as $adj)
                        <tr>
                            <td>
                                <span style="font-weight: 800; color: var(--primary); font-family: monospace; font-size: 13px;">
                                    #ADJ-{{ $adj->id }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <strong style="font-size: 14px; color: var(--text-main); display: block;">{{ $adj->client->name ?? 'غير معروف' }}</strong>
                                    <small style="color: var(--text-muted); font-size: 11.5px;">
                                        {{ $adj->client && $adj->client->type == 'customer' ? 'عميل' : 'مورد' }}
                                        - كود: {{ $adj->client->code ?? '-' }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 700; font-size: 13.5px; color: var(--text-main);">
                                    {{ $adj->date->format('Y-m-d') }}
                                </span>
                            </td>
                            <td>
                                @if($adj->type === 'write_off')
                                    <span style="background: #FEE2E2; color: #DC2626; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-ban"></i> شطب دين معدوم
                                    </span>
                                @elseif($adj->type === 'discount')
                                    <span style="background: #FEF3C7; color: #D97706; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-tags"></i> خصم تسوية مالي
                                    </span>
                                @else
                                    <span style="background: #E0E7FF; color: #4338CA; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-sliders"></i> فرق رصيد افتتاحي
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong style="font-size: 15px; font-weight: 900; color: #0F5132;">
                                    {{ formatMoney($adj->amount) }}
                                </strong>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-main); font-weight: 600;">
                                    <i class="fa-solid fa-user-shield" style="color: var(--primary); margin-left: 4px;"></i>
                                    {{ $adj->creator->name ?? 'النظام' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-muted);">
                                    {{ $adj->notes }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-scale-balanced" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                                لا توجد حركات شطب أو تسويات ديون مسجلة في النظام
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($adjustments->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $adjustments->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Add Adjustment -->
<div id="addAdjustmentModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #fff; width: 100%; max-width: 540px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-scale-balanced" style="color: var(--primary);"></i> تسجيل حركة تسوية / شطب رصيد
            </h3>
            <button onclick="document.getElementById('addAdjustmentModal').style.display='none'" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('crm.adjustments.store') }}" method="POST">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">اختيار العميل أو المورد <span style="color: #DC2626;">*</span></label>
                    <select name="client_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 600;">
                        <option value="">-- اختر الطرف المراد تسوية حسابه --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->type == 'customer' ? 'عميل' : 'مورد' }}) - الرصيد الحالي: {{ formatMoney($c->current_balance) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">نوع التسوية <span style="color: #DC2626;">*</span></label>
                        <select name="type" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                            <option value="write_off">شطب دين معدوم</option>
                            <option value="discount">خصم تسوية مالي</option>
                            <option value="opening_diff">فرق رصيد افتتاحي</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">تاريخ الحركة <span style="color: #DC2626;">*</span></label>
                        <input type="date" name="date" value="{{ now()->toDateString() }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">المبلغ المسوى / المشطوب <span style="color: #DC2626;">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">سبب ومبرر التسوية <span style="color: #DC2626;">*</span></label>
                    <textarea name="notes" rows="2" required placeholder="أذكر سبب التسوية أو قرار الإدارة المالية..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('addAdjustmentModal').style.display='none'" style="background: #E2E8F0; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; color: #475569; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 24px;">تنفيذ واعتماد التسوية</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
