@extends('layouts.app')

@section('title', 'دليل وسجل العملاء')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-users" style="color: var(--primary); margin-left: 8px;"></i>
                إدارة علاقات العملاء (CRM - Customers)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                دليل العملاء، متابعة المديونيات، الحدود الائتمانية، وكشوفات الحسابات التفصيلية
            </span>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('crm.debts.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calendar-check" style="color: var(--accent-gold);"></i>
                جدولة الأقساط والديون
            </a>
            <a href="{{ route('crm.customers.create') }}" class="btn-primary-custom">
                <i class="fa-solid fa-user-plus"></i>
                إضافة عميل جديد
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي العملاء المسجلين</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--text-main);">{{ $stats['total_customers'] }} عميل</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي مديونيات العملاء (لنا)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ formatMoney($stats['total_debit']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أرصدة مقدمة للعملاء (لهم)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['total_credit']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تجاوزوا الحد الائتماني</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ $stats['over_limit'] }} عميل</strong>
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form action="{{ route('crm.customers.index') }}" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 240px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم العميل، الهاتف، كود الحساب، أو الرقم الضريبي..." style="width: 100%; padding: 10px 38px 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                <i class="fa-solid fa-search" style="position: absolute; right: 14px; top: 13px; color: #94A3B8;"></i>
            </div>

            <div style="flex: 1; min-width: 160px;">
                <select name="group_id" onchange="this.form.submit()" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                    <option value="">-- كل شرائح العملاء --</option>
                    @foreach($groups as $grp)
                        <option value="{{ $grp->id }}" {{ request('group_id') == $grp->id ? 'selected' : '' }}>{{ $grp->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 160px;">
                <select name="debt_status" onchange="this.form.submit()" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                    <option value="">-- كل الحالات المالية --</option>
                    <option value="debtors" {{ request('debt_status') == 'debtors' ? 'selected' : '' }}>المدينين فقط (عليهم مستحقات)</option>
                    <option value="creditors" {{ request('debt_status') == 'creditors' ? 'selected' : '' }}>الدائنين فقط (لهم أرصدة)</option>
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px 20px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>

            @if(request()->anyFilled(['search', 'group_id', 'debt_status']))
                <a href="{{ route('crm.customers.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 10px 16px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 600; font-size: 13.5px; display: flex; align-items: center;">
                    إعادة ضبط
                </a>
            @endif
        </form>
    </div>

    <!-- Customers Table Card -->
    <div class="erp-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13.5px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                        <th style="padding: 12px 16px;">كود الحساب</th>
                        <th style="padding: 12px 16px;">اسم العميل / المؤسسة</th>
                        <th style="padding: 12px 16px;">رقم الاتصال</th>
                        <th style="padding: 12px 16px;">الشريحة والتصنيف</th>
                        <th style="padding: 12px 16px;">الحد الائتماني</th>
                        <th style="padding: 12px 16px;">الرصيد المالي الحالي</th>
                        <th style="padding: 12px 16px;">الحالة</th>
                        <th style="padding: 12px 16px; text-align: center;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        @php
                            $bStatus = $c->balanceStatus();
                            $overLimit = $c->isCreditLimitExceeded();
                        @endphp
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 16px; font-family: monospace; font-weight: 700; color: var(--primary);">
                                {{ $c->code ?? '#' . $c->id }}
                            </td>
                            <td style="padding: 12px 16px;">
                                <strong style="font-size: 14px; color: var(--text-main); display: block;">{{ $c->name }}</strong>
                                @if($c->company_name)
                                    <span style="font-size: 11.5px; color: var(--text-muted);">{{ $c->company_name }}</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px;">
                                @if($c->phone || $c->mobile)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $c->phone ?: $c->mobile) }}" target="_blank" style="color: #059669; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-brands fa-whatsapp"></i>
                                        <span>{{ $c->phone ?: $c->mobile }}</span>
                                    </a>
                                @else
                                    <span style="color: var(--text-muted);">-</span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px;">
                                <span style="background: #E0E7FF; color: #4338CA; padding: 3px 8px; border-radius: 8px; font-size: 11px; font-weight: 700;">
                                    {{ $c->group->name ?? 'عملاء التجزئة' }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; color: var(--text-muted);">
                                {{ $c->credit_limit > 0 ? formatMoney($c->credit_limit) : 'غير مقيد' }}
                            </td>
                            <td style="padding: 12px 16px;">
                                <span style="background: {{ $bStatus['bg'] }}; color: {{ $bStatus['color'] }}; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                    {{ formatMoney(abs($c->current_balance)) }}
                                    <small style="font-size: 10px;">({{ $bStatus['label'] }})</small>
                                </span>
                                @if($overLimit)
                                    <span style="display: block; font-size: 10.5px; color: #DC2626; font-weight: 700; margin-top: 2px;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> تجاوز الحد الائتماني
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 12px 16px;">
                                <span style="background: {{ $c->is_active ? '#D1E7DD' : '#FEE2E2' }}; color: {{ $c->is_active ? '#0F5132' : '#991B1B' }}; padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: 700;">
                                    {{ $c->is_active ? 'نشط' : 'معطل' }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="{{ route('crm.customers.statement', $c->id) }}" style="background: #CCFBF1; color: #0F766E; width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="كشف حساب تفصيلي">
                                        <i class="fa-solid fa-receipt"></i>
                                    </a>
                                    <a href="{{ route('crm.customers.edit', $c->id) }}" style="background: var(--primary-light); color: var(--primary); width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="تعديل">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('crm.customers.destroy', $c->id) }}" method="POST" onsubmit="return confirm('حذف هذا العميل؟')" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #FEE2E2; border: none; color: #DC2626; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 36px; text-align: center; color: var(--text-muted);">
                                لا يوجد عملاء مطابقين للبحث.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div style="margin-top: 20px;">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
