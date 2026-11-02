@extends('layouts.app')

@section('title', 'فواتير المشتريات')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-cart-shopping" style="color: var(--primary); margin-left: 8px;"></i>
                إدارة فواتير المشتريات (Purchase Invoices)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                توثيق فواتير المشتريات وتوريد البضائع للمستودعات ومتابعة التزامات الموردين
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('invoices.orders.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-contract" style="color: var(--accent-gold);"></i>
                أوامر الشراء (PO)
            </a>
            <a href="{{ route('invoices.returns.index') }}?type=purchase_return" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-rotate-left" style="color: #DC2626;"></i>
                مرتجعات المشتريات
            </a>
            <a href="{{ route('invoices.purchases.create') }}" class="btn-primary-custom">
                <i class="fa-solid fa-plus-circle"></i>
                تسجيل فاتورة مشتريات جديدة
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-cart-shopping"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي قيمة المشتريات</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--primary);">{{ formatMoney($stats['total_purchases']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">المشتريات المسددة نقدياً</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['cash_purchases']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مشتريات آجلة (مستحقة للموردين)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ formatMoney($stats['credit_purchases']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">عدد فواتير الشراء</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--text-main);">{{ $stats['total_invoices'] }} فاتورة</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form action="{{ route('invoices.purchases.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 220px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم الفاتورة أو اسم المورد..." style="width: 100%; padding: 9px 36px 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                <i class="fa-solid fa-search" style="position: absolute; right: 12px; top: 12px; color: #94A3B8;"></i>
            </div>

            <div style="flex: 1; min-width: 160px;">
                <select name="client_id" onchange="this.form.submit()" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13px; font-weight: 600;">
                    <option value="">-- كل الموردين --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}" {{ request('client_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 160px;">
                <select name="store_id" onchange="this.form.submit()" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13px; font-weight: 600;">
                    <option value="">-- كل المستودعات --</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 9px 20px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>
        </form>
    </div>

    <!-- Purchases Table Card -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>المورد</th>
                        <th>التاريخ</th>
                        <th>مستودع الاستلام</th>
                        <th>طريقة الدفع</th>
                        <th>قبل الخصم</th>
                        <th>الخصم</th>
                        <th>الضريبة</th>
                        <th>الصافي الإجمالي</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
                        <th style="text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        <tr>
                            <td>
                                <a href="{{ route('invoices.purchases.show', $inv->id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none; font-family: monospace; font-size: 13.5px;">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td>
                                <div>
                                    <strong style="font-size: 14px; color: var(--text-main); display: block;">{{ $inv->client->name ?? 'مورد غير محدد' }}</strong>
                                    @if($inv->client && $inv->client->company_name)
                                        <small style="color: var(--text-muted); font-size: 11.5px;">{{ $inv->client->company_name }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 13px; font-weight: 700; color: var(--text-main);">
                                    {{ $inv->date->format('Y-m-d') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: #475569;">
                                    <i class="fa-solid fa-warehouse" style="color: var(--primary); margin-left: 4px;"></i>
                                    {{ $inv->store->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($inv->payment_type === 'cash')
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">نقدي</span>
                                @elseif($inv->payment_type === 'credit')
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">آجل (مستحق)</span>
                                @else
                                    <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700;">{{ $inv->payment_type_label }}</span>
                                @endif
                            </td>
                            <td>{{ formatMoney($inv->subtotal) }}</td>
                            <td style="color: #DC2626; font-weight: 700;">{{ $inv->discount_amount > 0 ? '-' . formatMoney($inv->discount_amount) : '0.00' }}</td>
                            <td style="color: #2563EB;">{{ $inv->tax_amount > 0 ? '+' . formatMoney($inv->tax_amount) : '0.00' }}</td>
                            <td>
                                <strong style="font-size: 14.5px; font-weight: 900; color: var(--primary);">
                                    {{ formatMoney($inv->grand_total) }}
                                </strong>
                            </td>
                            <td>
                                @if($inv->remaining_amount > 0)
                                    <span style="font-size: 13px; font-weight: 800; color: #DC2626;">
                                        {{ formatMoney($inv->remaining_amount) }}
                                    </span>
                                @else
                                    <span style="font-size: 12px; color: #16A34A; font-weight: 700;">
                                        <i class="fa-solid fa-circle-check"></i> مسدد
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success">معتمدة</span>
                            </td>
                            <td>
                                <div style="display: flex; justify-content: center; gap: 6px;">
                                    <a href="{{ route('invoices.purchases.show', $inv->id) }}" title="عرض التفاصيل" style="background: var(--primary-light); color: var(--primary); width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 13px;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-cart-shopping" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                                لا توجد فواتير مشتريات مسجلة بعد
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

