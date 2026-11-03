@extends('layouts.app')

@section('title', 'الملف الشامل للفرع - ' . $branch->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0;">
                    {{ $branch->name }}
                    <span style="font-family: monospace; color: var(--primary); font-size: 16px;">({{ $branch->code }})</span>
                </h2>
                <span style="background: rgba(15, 81, 50, 0.1); color: var(--primary); padding: 3px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800;">
                    {{ $branch->type_label }}
                </span>
                @if($branch->is_main)
                    <span style="background: #FEF3C7; color: #D97706; padding: 3px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800;">
                        <i class="fa-solid fa-star"></i> الفرع الرئيسي للشركة
                    </span>
                @endif
                @if(!$branch->is_active)
                    <span style="background: #FEE2E2; color: #DC2626; padding: 3px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800;">
                        معطل
                    </span>
                @endif
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">
                المدينة: {{ $branch->city ?: 'غير محدد' }} &bull; المدير: {{ $branch->manager_name ?: '-' }} &bull; الهاتف: {{ $branch->phone ?: '-' }}
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('branches.branches.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع للفروع
            </a>

            <!-- Quick Switch to this Branch in Session -->
            <form action="{{ route('switch.branch') }}" method="POST" style="display: inline; margin: 0;">
                @csrf
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                    <i class="fa-solid fa-arrow-right-arrow-left"></i>
                    التبديل لبيئة هذا الفرع
                </button>
            </form>

            <a href="{{ route('branches.transfers.create') }}?from_branch_id={{ $branch->id }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-truck-ramp-box" style="color: var(--primary);"></i>
                طلب تحويل بضاعة
            </a>

            <a href="{{ route('branches.branches.edit', $branch->id) }}" style="background: #FEF3C7; color: #D97706; padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square"></i>
                تعديل البيانات
            </a>
        </div>
    </div>

    <!-- 4 Key Operational Metrics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">قيمة المخزون المتاح بالفرع</span>
                <strong style="font-size: 19px; font-weight: 900; color: var(--primary);">{{ formatMoney($inventoryValue ?? $branch->getInventoryValue()) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مبيعات الفرع هذا الشهر</span>
                <strong style="font-size: 19px; font-weight: 900; color: #0F5132;">{{ formatMoney($monthlySales ?? $branch->getMonthlySales()) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أرصدة الخزائن النقدية بالفرع</span>
                <strong style="font-size: 19px; font-weight: 900; color: #D97706;">{{ formatMoney($branch->treasuries->sum('current_balance')) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">الكادر الوظيفي بالفرع</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $branch->employees->count() }} موظف</strong>
            </div>
        </div>
    </div>

    <!-- Section 1: Warehouses & Stores -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-warehouse" style="color: var(--primary);"></i>
            المستودعات والمخازن التابعة للفرع ({{ $branch->stores->count() }})
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">كود المخزن</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم المستودع</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 150px;">أمين المستودع</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">الهاتف</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 120px;">عدد الأصناف المتاحة</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 90px;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branch->stores as $st)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            {{ $st->code ?: '-' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800;">
                            {{ $st->name }}
                            @if($st->is_main)
                                <span style="background: #FEF3C7; color: #D97706; padding: 2px 6px; border-radius: 10px; font-size: 10px; font-weight: 700; margin-right: 4px;">رئيسي</span>
                            @endif
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); color: var(--text-main);">
                            {{ $st->keeper_name ?: '-' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            {{ $st->phone ?: '-' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center; font-weight: 800; color: #4338CA;">
                            {{ $st->stocks()->where('quantity', '>', 0)->count() }} صنف
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($st->is_active)
                                <span style="background: #D1E7DD; color: #0F5132; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">نشط</span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">معطل</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 24px; text-align: center; color: var(--text-muted);">
                            لا توجد مستودعات مرتبطة بهذا الفرع حالياً.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Treasuries & Cash Drawers -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-cash-register" style="color: #D97706;"></i>
            الخزائن النقدية والحسابات البنكية بالفرع ({{ $branch->treasuries->count() }})
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم الخزينة / الحساب</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 150px;">النوع</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 170px;">الرصيد الحالي</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 90px;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branch->treasuries as $tr)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800;">
                            {{ $tr->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            {{ $tr->type_label }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: #0F5132; font-size: 14px;">
                            {{ formatMoney($tr->current_balance) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($tr->is_active)
                                <span style="background: #D1E7DD; color: #0F5132; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">نشطة</span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">معطلة</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding: 24px; text-align: center; color: var(--text-muted);">
                            لا توجد خزائن نقدية مسجلة لهذا الفرع.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 3: Assigned Users & Local Permissions -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-user-shield" style="color: #4338CA;"></i>
            المستخدمون المصرح لهم بالعمل في هذا الفرع ({{ $branch->assignedUsers->count() }})
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم المستخدم</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 170px;">البريد الإلكتروني</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 150px;">الدور بالفرع</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">الفرع الافتراضي</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">صلاحية التبديل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branch->assignedUsers as $usr)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800;">
                            {{ $usr->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            {{ $usr->email }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: #4338CA;">
                            {{ $usr->pivot->role }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($usr->pivot->is_default)
                                <span style="background: #FEF3C7; color: #D97706; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">نعم</span>
                            @else
                                <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($usr->pivot->can_switch)
                                <span style="color: #0F5132; font-weight: 800;"><i class="fa-solid fa-check"></i> مسموح</span>
                            @else
                                <span style="color: #DC2626;"><i class="fa-solid fa-ban"></i> مقيد</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="padding: 24px; text-align: center; color: var(--text-muted);">
                            لم يتم تعيين مستخدمين محددين لهذا الفرع حتى الآن.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
