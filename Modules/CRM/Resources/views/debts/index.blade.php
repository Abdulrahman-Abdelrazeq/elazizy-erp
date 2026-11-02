@extends('layouts.app')

@section('title', 'جدولة الديون والأقساط ومواعيد السداد')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-calendar-check" style="color: var(--primary); margin-left: 8px;"></i>
                جدولة الديون والأقساط (Debt Schedules & Installments)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تتبع مواعيد استحقاق أقساط العملاء، الديون المتأخرة، وتوثيق حركات التحصيل
            </span>
        </div>

        <button onclick="document.getElementById('addScheduleModal').style.display='flex'" class="btn-primary-custom">
            <i class="fa-solid fa-plus-circle"></i>
            جدولة قسط / دين جديد
        </button>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أقساط قيد الانتظار (مستقبلية)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ formatMoney($stats['total_pending']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أقساط متأخرة واجبة التحصيل</span>
                <strong style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ formatMoney($stats['total_overdue']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الأقساط المحصلة</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['total_paid']) }}</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form action="{{ route('crm.debts.index') }}" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 240px;">
                <select name="client_id" onchange="this.form.submit()" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                    <option value="">-- كل العملاء والموردين --</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->type == 'customer' ? 'عميل' : 'مورد' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 180px;">
                <select name="status" onchange="this.form.submit()" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                    <option value="">-- كل حالات السداد --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار (مستقبلي)</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>متأخر عن تاريخ الاستحقاق</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>تم التحصيل والسداد</option>
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px 20px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>

            @if(request()->filled('client_id') || request()->filled('status'))
                <a href="{{ route('crm.debts.index') }}" style="background: #E2E8F0; padding: 10px 18px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-times"></i> إلغاء الفلتر
                </a>
            @endif
        </form>
    </div>

    <!-- Schedules Table Card -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>العميل / الطرف</th>
                        <th>تاريخ الاستحقاق</th>
                        <th>قيمة القسط / الدين</th>
                        <th>المبلغ المسدد</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
                        <th>تاريخ السداد الفعلي</th>
                        <th>ملاحظات</th>
                        <th style="text-align: center;">إجراء التحصيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>
                                <div>
                                    <strong style="font-size: 14px; color: var(--text-main); display: block;">{{ $schedule->client->name ?? 'غير معروف' }}</strong>
                                    <small style="color: var(--text-muted); font-size: 11.5px;">
                                        {{ $schedule->client && $schedule->client->type == 'customer' ? 'عميل' : 'مورد' }}
                                        - كود: {{ $schedule->client->code ?? '-' }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; font-size: 13.5px; color: {{ $schedule->due_date->isPast() && $schedule->status !== 'paid' ? '#DC2626' : 'var(--text-main)' }};">
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ $schedule->due_date->format('Y-m-d') }}
                                </div>
                            </td>
                            <td>
                                <strong style="font-size: 14.5px; font-weight: 900; color: var(--text-main);">
                                    {{ formatMoney($schedule->amount) }}
                                </strong>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: #16A34A; font-size: 13.5px;">
                                    {{ formatMoney($schedule->paid_amount) }}
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: #DC2626; font-size: 13.5px;">
                                    {{ formatMoney($schedule->amount - $schedule->paid_amount) }}
                                </span>
                            </td>
                            <td>
                                @if($schedule->status === 'paid')
                                    <span style="background: #DCFCE7; color: #15803D; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-circle-check"></i> تم السداد
                                    </span>
                                @elseif($schedule->status === 'overdue')
                                    <span style="background: #FEE2E2; color: #B91C1C; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> متأخر
                                    </span>
                                @else
                                    <span style="background: #FEF3C7; color: #B45309; padding: 4px 10px; border-radius: 6px; font-size: 11.5px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-clock"></i> قيد الانتظار
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-muted);">
                                    {{ $schedule->paid_at ? $schedule->paid_at->format('Y-m-d') : '-' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-muted);">
                                    {{ $schedule->notes ?: '-' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($schedule->status !== 'paid')
                                    <form action="{{ route('crm.debts.pay', $schedule->id) }}" method="POST" onsubmit="return confirm('هل تؤكد استلام وسداد هذا القسط بمبلغ {{ formatMoney($schedule->amount) }} وتخفيض رصيد العميل؟');">
                                        @csrf
                                        <button type="submit" class="btn-primary-custom" style="padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-hand-holding-dollar"></i> تسجيل السداد
                                        </button>
                                    </form>
                                @else
                                    <span style="color: #16A34A; font-weight: 700; font-size: 12px;">
                                        <i class="fa-solid fa-check-double"></i> خالص
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-calendar-check" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                                لا توجد أقساط مجدولة مسجلة وفقاً لمعايير البحث الحالية
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schedules->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Add Schedule -->
<div id="addScheduleModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #fff; width: 100%; max-width: 520px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> جدولة قسط / استحقاق دين جديد
            </h3>
            <button onclick="document.getElementById('addScheduleModal').style.display='none'" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('crm.debts.store') }}" method="POST">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">اختيار العميل أو المورد <span style="color: #DC2626;">*</span></label>
                    <select name="client_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 600;">
                        <option value="">-- اختر الطرف --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->name }} ({{ $c->type == 'customer' ? 'عميل' : 'مورد' }}) - الرصيد: {{ formatMoney($c->current_balance) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">مبلغ القسط <span style="color: #DC2626;">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">تاريخ الاستحقاق <span style="color: #DC2626;">*</span></label>
                        <input type="date" name="due_date" value="{{ now()->addDays(30)->toDateString() }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">ملاحظات أو رقم السند / الشيك</label>
                    <textarea name="notes" rows="2" placeholder="رقم الشيك أو تفاصيل الاتفاق..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('addScheduleModal').style.display='none'" style="background: #E2E8F0; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; color: #475569; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 24px;">حفظ الجدولة</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
