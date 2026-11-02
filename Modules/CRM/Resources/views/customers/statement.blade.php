@extends('layouts.app')

@section('title', 'كشف حساب تفصيلي: ' . $customer->name)

@section('content')
<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: #fff !important;
        font-family: 'Cairo', sans-serif !important;
    }
    .erp-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        break-inside: avoid;
    }
    .app-sidebar, .top-header, .theme-settings-btn {
        display: none !important;
    }
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
}
</style>

<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1100px; margin: 0 auto;">

    <!-- Action Toolbar (No Print) -->
    <div class="no-print" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-file-invoice" style="color: var(--primary); margin-left: 8px;"></i>
                كشف حساب عميل (Account Statement)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                الحركات المالية، الأقساط المجدولة، والتسويات للعميل: <strong>{{ $customer->name }}</strong>
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-primary-custom" style="display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة كشف الحساب
            </button>
            <a href="{{ route('crm.customers.edit', $customer->id) }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-user-pen"></i>
                تعديل العميل
            </a>
            <a href="{{ route('crm.customers.index') }}" style="background: #E2E8F0; padding: 9px 16px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <!-- Official Statement Document Card -->
    <div class="erp-card" style="padding: 32px; background: #fff; border-radius: 16px;">
        
        <!-- Document Header (Visible in print) -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid var(--border-color); padding-bottom: 20px; margin-bottom: 24px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 900; color: var(--primary); margin-bottom: 4px;">
                    {{ getSetting('company_name', 'مؤسسة العزيزي للحلول المتكاملة') }}
                </h1>
                <p style="font-size: 12.5px; color: var(--text-muted); margin: 0;">
                    {{ getSetting('company_address', 'القاهرة - جمهورية مصر العربية') }} | هاتف: {{ getSetting('company_phone', '01000000000') }}
                </p>
                <p style="font-size: 12px; color: var(--text-muted); margin: 2px 0 0 0;">
                    السجل الضريبي: {{ getSetting('company_tax_number', '123-456-789') }}
                </p>
            </div>

            <div style="text-align: left;">
                <div style="background: var(--primary-light); color: var(--primary); padding: 6px 16px; border-radius: 8px; font-weight: 800; font-size: 14px; display: inline-block; margin-bottom: 8px;">
                    كشف حساب معتمد
                </div>
                <div style="font-size: 12px; color: var(--text-muted);">
                    تاريخ التقرير: <strong>{{ now()->format('Y-m-d H:i') }}</strong>
                </div>
                <div style="font-size: 12px; color: var(--text-muted);">
                    كود العميل: <strong>{{ $customer->code }}</strong>
                </div>
            </div>
        </div>

        <!-- Customer Summary Info Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 18px; margin-bottom: 24px;">
            <div>
                <span style="font-size: 11.5px; color: var(--text-muted); font-weight: 700; display: block;">اسم العميل:</span>
                <strong style="font-size: 15px; color: var(--text-main);">{{ $customer->name }}</strong>
                @if($customer->company_name)
                    <div style="font-size: 12px; color: var(--text-muted);">({{ $customer->company_name }})</div>
                @endif
            </div>

            <div>
                <span style="font-size: 11.5px; color: var(--text-muted); font-weight: 700; display: block;">الهاتف / الموبايل:</span>
                <strong style="font-size: 14px; color: var(--text-main);">{{ $customer->mobile ?: ($customer->phone ?: 'غير مسجل') }}</strong>
            </div>

            <div>
                <span style="font-size: 11.5px; color: var(--text-muted); font-weight: 700; display: block;">الشريحة التسعيرية:</span>
                <span class="badge" style="background: #E0E7FF; color: #4338CA; font-weight: 700; font-size: 12px;">
                    {{ $customer->group ? $customer->group->name : 'فئة عامة' }}
                </span>
            </div>

            <div>
                <span style="font-size: 11.5px; color: var(--text-muted); font-weight: 700; display: block;">الحد الائتماني:</span>
                <strong style="font-size: 14px; color: var(--accent-gold);">
                    {{ $customer->credit_limit > 0 ? formatMoney($customer->credit_limit) : 'بلا سقف ائتماني' }}
                </strong>
            </div>
        </div>

        <!-- Financial KPI Ribbon -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 28px;">
            <div style="border: 1px solid var(--border-color); border-radius: 10px; padding: 14px; text-align: center; background: #FAFAFA;">
                <span style="font-size: 11.5px; color: var(--text-muted); font-weight: 700;">الرصيد الافتتاحي</span>
                <h4 style="font-size: 16px; font-weight: 900; margin: 4px 0 0 0; color: #475569;">
                    {{ formatMoney($customer->opening_balance) }}
                    <small style="font-size: 10.5px;">({{ $customer->opening_balance_type == 'debit' ? 'مدين' : 'دائن' }})</small>
                </h4>
            </div>

            <div style="border: 1px solid #FECACA; border-radius: 10px; padding: 14px; text-align: center; background: #FEF2F2;">
                <span style="font-size: 11.5px; color: #991B1B; font-weight: 700;">الرصيد الحالي المستحق</span>
                <h4 style="font-size: 18px; font-weight: 900; margin: 4px 0 0 0; color: {{ $customer->current_balance > 0 ? '#DC2626' : ($customer->current_balance < 0 ? '#0F5132' : '#475569') }};">
                    {{ formatMoney(abs($customer->current_balance)) }}
                    <small style="font-size: 11px;">
                        @if($customer->current_balance > 0)
                            (مدين - لنا)
                        @elseif($customer->current_balance < 0)
                            (دائن - له)
                        @else
                            (خالص)
                        @endif
                    </small>
                </h4>
            </div>

            <div style="border: 1px solid #FED7AA; border-radius: 10px; padding: 14px; text-align: center; background: #FFF7ED;">
                <span style="font-size: 11.5px; color: #9A3412; font-weight: 700;">إجمالي الأقساط المجدولة</span>
                <h4 style="font-size: 16px; font-weight: 900; margin: 4px 0 0 0; color: #EA580C;">
                    {{ formatMoney($customer->debtSchedules->sum('amount')) }}
                </h4>
            </div>

            <div style="border: 1px solid #BBF7D0; border-radius: 10px; padding: 14px; text-align: center; background: #F0FDF4;">
                <span style="font-size: 11.5px; color: #166534; font-weight: 700;">المسدد من الأقساط</span>
                <h4 style="font-size: 16px; font-weight: 900; margin: 4px 0 0 0; color: #16A34A;">
                    {{ formatMoney($customer->debtSchedules->sum('paid_amount')) }}
                </h4>
            </div>
        </div>

        <!-- Section 1: Debt Schedules & Installments -->
        <div style="margin-bottom: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 8px; margin: 0;">
                    <i class="fa-solid fa-calendar-days"></i> سجل الأقساط والمواعيد المجدولة للسداد
                </h3>
                <span class="no-print" style="font-size: 12px; color: var(--text-muted);">
                    {{ $customer->debtSchedules->count() }} قسط مجدول
                </span>
            </div>

            @if($customer->debtSchedules->count() > 0)
                <div style="overflow-x: auto; border: 1px solid var(--border-color); border-radius: 10px;">
                    <table class="erp-table" style="width: 100%; margin-bottom: 0;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th style="padding: 10px 14px; font-size: 12px;">م</th>
                                <th style="padding: 10px 14px; font-size: 12px;">تاريخ الاستحقاق</th>
                                <th style="padding: 10px 14px; font-size: 12px;">قيمة القسط</th>
                                <th style="padding: 10px 14px; font-size: 12px;">المدفوع</th>
                                <th style="padding: 10px 14px; font-size: 12px;">المتبقي</th>
                                <th style="padding: 10px 14px; font-size: 12px;">الحالة</th>
                                <th style="padding: 10px 14px; font-size: 12px;">تاريخ السداد</th>
                                <th style="padding: 10px 14px; font-size: 12px;">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->debtSchedules as $index => $sch)
                                <tr>
                                    <td style="padding: 10px 14px; font-weight: 700;">{{ $index + 1 }}</td>
                                    <td style="padding: 10px 14px; font-weight: 700;">{{ $sch->due_date->format('Y-m-d') }}</td>
                                    <td style="padding: 10px 14px; font-weight: 800; color: var(--text-main);">{{ formatMoney($sch->amount) }}</td>
                                    <td style="padding: 10px 14px; font-weight: 800; color: #16A34A;">{{ formatMoney($sch->paid_amount) }}</td>
                                    <td style="padding: 10px 14px; font-weight: 800; color: #DC2626;">{{ formatMoney($sch->amount - $sch->paid_amount) }}</td>
                                    <td style="padding: 10px 14px;">
                                        @if($sch->status === 'paid')
                                            <span style="background: #DCFCE7; color: #15803D; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">
                                                <i class="fa-solid fa-circle-check"></i> تم السداد
                                            </span>
                                        @elseif($sch->status === 'overdue')
                                            <span style="background: #FEE2E2; color: #B91C1C; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">
                                                <i class="fa-solid fa-clock"></i> متأخر عن الموعد
                                            </span>
                                        @else
                                            <span style="background: #FEF3C7; color: #B45309; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">
                                                <i class="fa-solid fa-hourglass-half"></i> قيد الانتظار
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 10px 14px; font-size: 12px; color: var(--text-muted);">{{ $sch->paid_at ? $sch->paid_at->format('Y-m-d') : '-' }}</td>
                                    <td style="padding: 10px 14px; font-size: 12px; color: var(--text-muted);">{{ $sch->notes ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="background: #F8FAFC; border: 1px dashed var(--border-color); border-radius: 10px; padding: 18px; text-align: center; color: var(--text-muted); font-size: 13px;">
                    لا توجد أقساط مجدولة مسجلة لهذا العميل حالياً.
                </div>
            @endif
        </div>

        <!-- Section 2: Balance Adjustments & Write-offs -->
        <div style="margin-bottom: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 8px; margin: 0;">
                    <i class="fa-solid fa-scale-balanced"></i> حركات التسوية المالية والشطب (Balance Adjustments)
                </h3>
            </div>

            @if($customer->balanceAdjustments->count() > 0)
                <div style="overflow-x: auto; border: 1px solid var(--border-color); border-radius: 10px;">
                    <table class="erp-table" style="width: 100%; margin-bottom: 0;">
                        <thead>
                            <tr style="background: #F8FAFC;">
                                <th style="padding: 10px 14px; font-size: 12px;">التاريخ</th>
                                <th style="padding: 10px 14px; font-size: 12px;">نوع الحركة</th>
                                <th style="padding: 10px 14px; font-size: 12px;">المبلغ المسوى</th>
                                <th style="padding: 10px 14px; font-size: 12px;">المسؤول</th>
                                <th style="padding: 10px 14px; font-size: 12px;">البيان والسبب</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->balanceAdjustments as $adj)
                                <tr>
                                    <td style="padding: 10px 14px; font-weight: 700;">{{ $adj->date->format('Y-m-d') }}</td>
                                    <td style="padding: 10px 14px;">
                                        @if($adj->type === 'write_off')
                                            <span style="background: #FEE2E2; color: #DC2626; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">شطب دين معدوم</span>
                                        @elseif($adj->type === 'discount')
                                            <span style="background: #FEF3C7; color: #D97706; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">خصم تسوية مالي</span>
                                        @else
                                            <span style="background: #E0E7FF; color: #4338CA; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">فرق رصيد افتتاحي</span>
                                        @endif
                                    </td>
                                    <td style="padding: 10px 14px; font-weight: 800; color: #0F5132;">{{ formatMoney($adj->amount) }}</td>
                                    <td style="padding: 10px 14px; font-size: 12px;">{{ $adj->creator->name ?? 'النظام' }}</td>
                                    <td style="padding: 10px 14px; font-size: 12px; color: var(--text-muted);">{{ $adj->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="background: #F8FAFC; border: 1px dashed var(--border-color); border-radius: 10px; padding: 18px; text-align: center; color: var(--text-muted); font-size: 13px;">
                    لا توجد حركات تسوية أو شطب مسجلة لهذا العميل.
                </div>
            @endif
        </div>

        <!-- Document Signatures Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px dashed var(--border-color); display: flex; justify-content: space-between; align-items: flex-end; font-size: 13px;">
            <div style="text-align: center; min-width: 180px;">
                <p style="font-weight: 700; margin-bottom: 50px;">المحاسب المسؤول</p>
                <div style="border-top: 1px solid #94A3B8; padding-top: 6px; color: var(--text-muted); font-size: 11.5px;">التوقيع / الختم</div>
            </div>

            <div style="text-align: center; min-width: 180px;">
                <p style="font-weight: 700; margin-bottom: 50px;">المدير المالي</p>
                <div style="border-top: 1px solid #94A3B8; padding-top: 6px; color: var(--text-muted); font-size: 11.5px;">التوقيع / الاعتماد</div>
            </div>

            <div style="text-align: center; min-width: 180px;">
                <p style="font-weight: 700; margin-bottom: 50px;">توقيع العميل بالمصادقة</p>
                <div style="border-top: 1px solid #94A3B8; padding-top: 6px; color: var(--text-muted); font-size: 11.5px;">الاسم / التوقيع</div>
            </div>
        </div>

    </div>
</div>
@endsection
