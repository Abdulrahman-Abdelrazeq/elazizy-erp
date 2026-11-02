@extends('layouts.app')

@section('title', 'باقات واشتراكات SaaS')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Current Subscription Header -->
    <div class="erp-card" style="background: linear-gradient(135deg, #FFFFFF 0%, #F8FAFC 100%);">
        <div class="card-header-custom">
            <div>
                <h3 class="card-title-custom">
                    <i class="fa-solid fa-gem" style="color: var(--accent-gold);"></i>
                    حالة اشتراك شركة: {{ $tenant->company_name }}
                </h3>
                <span style="font-size: 13px; color: var(--text-muted);">
                    تاريخ بداية الاشتراك: {{ $tenant->subscription_starts_at ? $tenant->subscription_starts_at->format('Y-m-d') : 'غير محدد' }} |
                    تاريخ الانتهاء: <strong style="color: var(--primary);">{{ $tenant->subscription_ends_at ? $tenant->subscription_ends_at->format('Y-m-d') : 'غير محدد' }}</strong>
                </span>
            </div>

            <div>
                @if($tenant->isSubscriptionActive())
                    <span class="subscription-pill" style="font-size: 13.5px; padding: 8px 16px;">
                        <i class="fa-solid fa-circle-check"></i>
                        ساري المفعول (متبقي {{ $tenant->remainingDays() }} يوم)
                    </span>
                @else
                    <span class="subscription-pill expired" style="font-size: 13.5px; padding: 8px 16px;">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        الاشتراك منتهي، يلزم التجديد
                    </span>
                @endif
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <span style="font-size: 12.5px; color: var(--text-muted); font-weight: 700;">الباقة الحالية:</span>
                <h4 style="font-size: 20px; font-weight: 900; color: var(--primary);">
                    {{ $tenant->plan->name ?? 'باقة المؤسسات الشاملة' }}
                </h4>
            </div>

            <button type="button" onclick="document.getElementById('renewModal').style.display='flex'" class="btn-gold-custom">
                <i class="fa-solid fa-arrow-rotate-right"></i>
                تجديد أو ترقية الاشتراك الآن
            </button>
        </div>
    </div>

    <!-- Available Plans Grid -->
    <div>
        <h3 style="font-size: 18px; font-weight: 800; margin-bottom: 16px; color: var(--text-main);">
            <i class="fa-solid fa-layer-group" style="color: var(--primary); margin-left: 8px;"></i>
            باقات وخطط اشتراك نظام العزيزي ERP
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
            @foreach($plans as $plan)
                <div class="erp-card" style="margin-bottom: 0; position: relative; border: 2px solid {{ $tenant->plan_id == $plan->id ? 'var(--primary)' : 'var(--border-color)' }};">
                    @if($tenant->plan_id == $plan->id)
                        <div style="position: absolute; top: -12px; left: 20px; background: var(--primary); color: #fff; padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: 800;">
                            باقتك الحالية
                        </div>
                    @endif

                    <h4 style="font-size: 18px; font-weight: 800; color: var(--text-main); margin-bottom: 6px;">{{ $plan->name }}</h4>
                    <p style="font-size: 12.5px; color: var(--text-muted); min-height: 38px;">{{ $plan->description }}</p>

                    <div style="margin: 18px 0; padding: 12px; background: #F8FAFC; border-radius: 10px;">
                        <span style="font-size: 24px; font-weight: 900; color: var(--primary);">
                            {{ formatMoney($plan->price_yearly) }}
                        </span>
                        <span style="font-size: 12px; color: var(--text-muted);">/ سنوياً</span>
                        <div style="font-size: 11.5px; color: #64748B; margin-top: 4px;">
                            أو {{ formatMoney($plan->price_monthly) }} / شهرياً
                        </div>
                    </div>

                    <ul style="list-style: none; padding: 0; margin-bottom: 20px; font-size: 13px; color: #334155;">
                        <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-check" style="color: var(--primary);"></i>
                            عدد المستخدمين: <strong>{{ $plan->max_users }} مستخدم</strong>
                        </li>
                        <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-check" style="color: var(--primary);"></i>
                            عدد الفروع: <strong>{{ $plan->max_branches }} فروع</strong>
                        </li>
                        <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-check" style="color: var(--primary);"></i>
                            عدد المستودعات: <strong>{{ $plan->max_stores }} مخازن</strong>
                        </li>
                        <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                            <i class="fa-solid fa-check" style="color: var(--primary);"></i>
                            دعم فني وتحديثات مستمرة
                        </li>
                    </ul>

                    <button type="button" onclick="selectPlanForRenew({{ $plan->id }}, '{{ $plan->name }}')" class="btn-primary-custom" style="width: 100%; justify-content: center;">
                        اختيار هذه الباقة والتجديد
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Renewal History Table -->
    <div class="erp-card">
        <div class="card-header-custom">
            <h3 class="card-title-custom">
                <i class="fa-solid fa-clock-rotate-left"></i>
                سجل الاشتراكات والتجديدات السابقة
            </h3>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13.5px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                        <th style="padding: 12px 16px;">رقم العملية</th>
                        <th style="padding: 12px 16px;">الباقة</th>
                        <th style="padding: 12px 16px;">الدورة</th>
                        <th style="padding: 12px 16px;">المبلغ</th>
                        <th style="padding: 12px 16px;">تاريخ البدء</th>
                        <th style="padding: 12px 16px;">تاريخ الانتهاء</th>
                        <th style="padding: 12px 16px;">طريقة الدفع</th>
                        <th style="padding: 12px 16px;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 16px; font-weight: 700; color: var(--primary);">
                                {{ $item->payment_reference ?? '#' . $item->id }}
                            </td>
                            <td style="padding: 12px 16px; font-weight: 600;">
                                {{ $item->plan->name ?? '-' }}
                            </td>
                            <td style="padding: 12px 16px;">
                                {{ $item->billing_cycle === 'yearly' ? 'سنوي' : 'شهري' }}
                            </td>
                            <td style="padding: 12px 16px; font-weight: 700;">
                                {{ formatMoney($item->price) }}
                            </td>
                            <td style="padding: 12px 16px;">
                                {{ $item->starts_at ? $item->starts_at->format('Y-m-d') : '-' }}
                            </td>
                            <td style="padding: 12px 16px; color: var(--primary); font-weight: 600;">
                                {{ $item->ends_at ? $item->ends_at->format('Y-m-d') : '-' }}
                            </td>
                            <td style="padding: 12px 16px;">
                                {{ $item->payment_method === 'bank_transfer' ? 'تحويل بنكي' : ($item->payment_method === 'free_trial' ? 'فترة مجانية' : $item->payment_method) }}
                            </td>
                            <td style="padding: 12px 16px;">
                                <span style="background: var(--primary-light); color: var(--primary); padding: 3px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                    نشط ومؤكد
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 24px; text-align: center; color: var(--text-muted);">
                                لا توجد سجلات اشتراك سابقة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Renew Modal -->
<div id="renewModal" class="theme-modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 800; font-size: 18px; color: var(--text-main);">
                <i class="fa-solid fa-arrow-rotate-right" style="color: var(--accent-gold); margin-left: 8px;"></i>
                تجديد أو ترقية اشتراك المنظومة
            </h3>
            <button type="button" onclick="document.getElementById('renewModal').style.display='none'" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ route('subscription.renew') }}" method="POST">
            @csrf

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اختر الباقة</label>
                <select name="plan_id" id="modal_plan_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-weight: 600; font-size: 13.5px;">
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ $tenant->plan_id == $plan->id ? 'selected' : '' }}>
                            {{ $plan->name }} ({{ formatMoney($plan->price_yearly) }} / سنة)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">دورة الفوترة والتجديد</label>
                <select name="billing_cycle" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-weight: 600; font-size: 13.5px;">
                    <option value="yearly">تجديد سنوي (خصم خاص لشهرين مجاناً)</option>
                    <option value="monthly">تجديد شهري</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">طريقة الدفع</label>
                <select name="payment_method" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-weight: 600; font-size: 13.5px;">
                    <option value="bank_transfer">تحويل بنكي / إيداع مباشر</option>
                    <option value="credit_card">بطاقة مدى / ائتمانية (Mada / Visa / Master)</option>
                    <option value="cash">نقداً لدى الإدارة المالية</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <button type="button" onclick="document.getElementById('renewModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">إلغاء</button>
                <button type="submit" class="btn-primary-custom">تأكيد التجديد وتحديث الاشتراك</button>
            </div>
        </form>
    </div>
</div>

<script>
    function selectPlanForRenew(planId, planName) {
        document.getElementById('modal_plan_id').value = planId;
        document.getElementById('renewModal').style.display = 'flex';
    }
</script>
@endsection

