@extends('layouts.app')

@section('title', 'فواتير المبيعات')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary); margin-left: 8px;"></i>
                إدارة فواتير المبيعات (Sales Invoices)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إصدار ومتابعة فواتير البيع النقدية والآجلة، الخصومات والضرائب، وطباعة الإيصالات
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('invoices.quotations.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-handshake" style="color: var(--accent-gold);"></i>
                عروض الأسعار
            </a>
            <a href="{{ route('invoices.returns.index') }}?type=sale_return" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-rotate-left" style="color: #DC2626;"></i>
                مرتجعات المبيعات
            </a>
            <a href="{{ route('invoices.sales.create') }}" class="btn-primary-custom">
                <i class="fa-solid fa-plus-circle"></i>
                إنشاء فاتورة مبيعات جديدة
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي قيمة المبيعات</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--primary);">{{ formatMoney($stats['total_sales']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">المبيعات النقدية (كاش)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['cash_sales']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">المبيعات الآجلة (ذمم)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ formatMoney($stats['credit_sales']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">عدد الفواتير الصادرة</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--text-main);">{{ $stats['total_invoices'] }} فاتورة</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form action="{{ route('invoices.sales.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 220px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم الفاتورة أو اسم العميل..." style="width: 100%; padding: 9px 36px 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                <i class="fa-solid fa-search" style="position: absolute; right: 12px; top: 12px; color: #94A3B8;"></i>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <select name="payment_type" onchange="this.form.submit()" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13px; font-weight: 600;">
                    <option value="">-- كل طرق الدفع --</option>
                    <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>نقدي (كاش)</option>
                    <option value="credit" {{ request('payment_type') == 'credit' ? 'selected' : '' }}>آجل (ذمم)</option>
                    <option value="partial" {{ request('payment_type') == 'partial' ? 'selected' : '' }}>سداد جزئي</option>
                    <option value="card" {{ request('payment_type') == 'card' ? 'selected' : '' }}>بطاقة بنكية / مدى</option>
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <select name="store_id" onchange="this.form.submit()" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13px; font-weight: 600;">
                    <option value="">-- كل المستودعات --</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 150px;">
                <select name="client_id" onchange="this.form.submit()" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13px; font-weight: 600;">
                    <option value="">-- كل العملاء --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 9px 20px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>

            @if(request()->anyFilled(['search', 'payment_type', 'store_id', 'client_id']))
                <a href="{{ route('invoices.sales.index') }}" style="background: #E2E8F0; padding: 9px 16px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-times"></i> إلغاء
                </a>
            @endif
        </form>
    </div>

    <!-- Sales Table Card -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>التاريخ</th>
                        <th>المستودع</th>
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
                                <a href="{{ route('invoices.sales.show', $inv->id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none; font-family: monospace; font-size: 13.5px;">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td>
                                <div>
                                    <strong style="font-size: 14px; color: var(--text-main); display: block;">{{ $inv->client ? $inv->client->name : 'عميل نقدي عام' }}</strong>
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
                                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700;">آجل (ذمم)</span>
                                @elseif($inv->payment_type === 'card')
                                    <span class="badge" style="background: #E0E7FF; color: #4338CA; font-weight: 700;">بطاقة / مدى</span>
                                @else
                                    <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700;">{{ $inv->payment_type_label }}</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 13px; color: var(--text-muted);">{{ formatMoney($inv->subtotal) }}</span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: #DC2626; font-weight: 700;">
                                    {{ $inv->discount_amount > 0 ? '-' . formatMoney($inv->discount_amount) : '0.00' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: #2563EB;">
                                    {{ $inv->tax_amount > 0 ? '+' . formatMoney($inv->tax_amount) : '0.00' }}
                                </span>
                            </td>
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
                                        <i class="fa-solid fa-circle-check"></i> خالص
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($inv->status === 'approved')
                                    <span class="badge badge-success">معتمدة</span>
                                @elseif($inv->status === 'cancelled')
                                    <span class="badge badge-danger">ملغاة</span>
                                @else
                                    <span class="badge badge-warning">{{ $inv->status_label }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; justify-content: center; gap: 6px;">
                                    <a href="{{ route('invoices.sales.show', $inv->id) }}" title="عرض التفاصيل" style="background: var(--primary-light); color: var(--primary); width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 13px;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('invoices.sales.print', $inv->id) }}" target="_blank" title="طباعة الفاتورة" style="background: #F1F5F9; color: #334155; width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 13px;">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    @if($inv->status !== 'cancelled')
                                        <form action="{{ route('invoices.sales.destroy', $inv->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إلغاء هذه الفاتورة وإرجاع الأصناف للمخزن وعكس الحساب المالي؟');" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="إلغاء الفاتورة" style="background: #FEE2E2; color: #DC2626; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                                لا توجد فواتير مبيعات مسجلة بعد في النظام
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
