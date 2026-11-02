@extends('layouts.app')

@section('title', 'سندات القبض والصرف المالي')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </span>
                سندات القبض والصرف المالي (Cash & Bank Vouchers)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إصدار وتوثيق حركات التحصيل النقدي والبنكي وسداد المستحقات والمصروفات العمومية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('treasury.vouchers.create') }}?type=receipt" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-circle-arrow-down"></i>
                إنشاء سند قبض جديد
            </a>
            <a href="{{ route('treasury.vouchers.create') }}?type=payment" style="background: #DC2626; color: #fff; padding: 10px 18px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">
                <i class="fa-solid fa-circle-arrow-up"></i>
                إنشاء سند صرف جديد
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid #10B981;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-arrow-down"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المقبوضات (تحصيلات)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['total_receipts']) }}</strong>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">({{ $stats['receipts_count'] }} سند قبض)</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid #DC2626;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-arrow-up"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المدفوعات (مصروفات)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #DC2626;">{{ formatMoney($stats['total_payments']) }}</strong>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">({{ $stats['payments_count'] }} سند صرف)</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid var(--primary); background: rgba(15, 81, 50, 0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">صافي التدفق النقدي للفترة</span>
                <strong style="font-size: 20px; font-weight: 900; color: {{ $stats['net_flow'] < 0 ? '#DC2626' : 'var(--primary)' }};">
                    {{ formatMoney($stats['net_flow']) }}
                </strong>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">(مقبوضات - مدفوعات)</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid #4338CA;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي عدد السندات</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $stats['receipts_count'] + $stats['payments_count'] }} سند</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('treasury.vouchers.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)) 110px 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">نوع السند</label>
                <select name="type" class="erp-input">
                    <option value="">جميع السندات</option>
                    <option value="receipt" {{ request('type') === 'receipt' ? 'selected' : '' }}>سندات قبض (تحصيل)</option>
                    <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>سندات صرف (دفع)</option>
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الخزينة / البنك</label>
                <select name="treasury_id" class="erp-input">
                    <option value="">جميع الخزائن</option>
                    @foreach($treasuries as $tr)
                        <option value="{{ $tr->id }}" {{ request('treasury_id') == $tr->id ? 'selected' : '' }}>{{ $tr->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">البند / التصنيف</label>
                <select name="category_id" class="erp-input">
                    <option value="">جميع البنود</option>
                    @foreach($categories as $ct)
                        <option value="{{ $ct->id }}" {{ request('category_id') == $ct->id ? 'selected' : '' }}>{{ $ct->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="erp-input">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="erp-input">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" class="erp-input" placeholder="رقم السند، الطرف...">
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>

            <a href="{{ route('treasury.vouchers.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 8px; font-size: 12.5px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                إعادة ضبط
            </a>
        </form>
    </div>

    <!-- Vouchers Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">رقم السند</th>
                        <th>النوع</th>
                        <th>التاريخ</th>
                        <th>الخزينة / الحساب</th>
                        <th>المبلغ</th>
                        <th>طريقة الدفع</th>
                        <th>المقبوض منه / المصروف له</th>
                        <th>البند والتصنيف</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 130px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $v)
                        <tr>
                            <td>
                                <a href="{{ route('treasury.vouchers.show', $v->id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none; font-family: monospace; font-size: 13.5px;">
                                    {{ $v->voucher_number }}
                                </a>
                            </td>
                            <td>
                                @if($v->type === 'receipt')
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 800;">
                                        <i class="fa-solid fa-circle-arrow-down"></i> سند قبض
                                    </span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 800;">
                                        <i class="fa-solid fa-circle-arrow-up"></i> سند صرف
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 12px; font-weight: 600;">{{ $v->date->format('Y-m-d') }}</span>
                            </td>
                            <td>
                                <strong style="color: var(--text-main);">{{ $v->treasury->name ?? '-' }}</strong>
                            </td>
                            <td>
                                <strong style="font-size: 15px; font-weight: 900; color: {{ $v->type === 'receipt' ? '#0F5132' : '#DC2626' }};">
                                    {{ $v->type === 'receipt' ? '+' : '-' }}{{ formatMoney($v->amount) }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge" style="background: #F1F5F9; color: var(--text-main); font-weight: 600;">
                                    {{ $v->payment_method_label }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--text-main);">
                                    {{ $v->client->name ?? ($v->received_from_or_paid_to ?: '—') }}
                                </strong>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-muted);">
                                    {{ $v->category->name ?? '—' }}
                                </span>
                            </td>
                            <td>
                                @if($v->status === 'approved')
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 700;">معتمد</span>
                                @elseif($v->status === 'cancelled')
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">ملغي</span>
                                @else
                                    <span class="badge" style="background: #F1F5F9; color: #64748B; font-weight: 700;">مسودة</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="{{ route('treasury.vouchers.show', $v->id) }}" title="عرض تفاصيل السند" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('treasury.vouchers.print', $v->id) }}" target="_blank" title="طباعة السند" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--accent-gold); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    @if($v->status !== 'cancelled')
                                    <form action="{{ route('treasury.vouchers.destroy', $v->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا السند وعكس أثره المالي؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="إلغاء السند" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                <span style="font-size: 15px; font-weight: 700; display: block;">لا توجد سندات مالية مطابقة للبحث</span>
                                <span style="font-size: 13px;">قم بإنشاء أول سند قبض أو صرف لإثبات الحركات النقدية</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vouchers->hasPages())
            <div style="padding: 16px; border-top: 1px solid var(--border-color);">
                {{ $vouchers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
