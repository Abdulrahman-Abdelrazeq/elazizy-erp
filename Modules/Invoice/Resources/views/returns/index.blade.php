@extends('layouts.app')

@section('title', 'مرتجعات المبيعات والمشتريات')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-rotate-left" style="color: #DC2626; margin-left: 8px;"></i>
                إدارة المرتجعات (Returns Management)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                توثيق ومعالجة مرتجعات المبيعات من العملاء، ومرتجعات المشتريات للموردين وإعادة ضبط المخزون والحسابات
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('invoices.returns.create') }}?type=sale_return" class="btn-primary-custom">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة مرتجع مبيعات (عميل)
            </a>
            <a href="{{ route('invoices.returns.create') }}?type=purchase_return" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 9px 18px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                إضافة مرتجع مشتريات (مورد)
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي مرتجعات المبيعات (من العملاء)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ formatMoney($stats['total_sales_returns']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي مرتجعات المشتريات (للموردين)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ formatMoney($stats['total_purchase_returns']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي حركات الإرجاع</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--primary);">{{ $stats['count_sales_returns'] + $stats['count_purchase_returns'] }} حركة</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form action="{{ route('invoices.returns.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 240px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم إشعار المرتجع أو اسم الطرف..." style="width: 100%; padding: 9px 36px 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                <i class="fa-solid fa-search" style="position: absolute; right: 12px; top: 12px; color: #94A3B8;"></i>
            </div>

            <div style="flex: 1; min-width: 180px;">
                <select name="type" onchange="this.form.submit()" style="width: 100%; padding: 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13px; font-weight: 600;">
                    <option value="">-- كل أنواع المرتجعات --</option>
                    <option value="sale_return" {{ request('type') == 'sale_return' ? 'selected' : '' }}>مرتجع مبيعات (عميل)</option>
                    <option value="purchase_return" {{ request('type') == 'purchase_return' ? 'selected' : '' }}>مرتجع مشتريات (مورد)</option>
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 9px 20px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>
        </form>
    </div>

    <!-- Returns Table Card -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>رقم المرتجع</th>
                        <th>نوع المرتجع</th>
                        <th>الطرف (عميل / مورد)</th>
                        <th>التاريخ</th>
                        <th>المستودع المتأثر</th>
                        <th>الفاتورة الأصلية</th>
                        <th>إجمالي قيمة المرتجع</th>
                        <th>الحالة والأثر</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $ret)
                        <tr>
                            <td>
                                <span style="font-weight: 800; color: #DC2626; font-family: monospace; font-size: 13.5px;">
                                    {{ $ret->invoice_number }}
                                </span>
                            </td>
                            <td>
                                @if($ret->type === 'sale_return')
                                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700;">
                                        <i class="fa-solid fa-rotate-left"></i> مرتجع مبيعات
                                    </span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">
                                        <i class="fa-solid fa-arrow-right-from-bracket"></i> مرتجع مشتريات
                                    </span>
                                @endif
                            </td>
                            <td>
                                <strong style="font-size: 14px; color: var(--text-main);">{{ $ret->client->name ?? 'غير محدد' }}</strong>
                            </td>
                            <td>
                                <span style="font-size: 13px; font-weight: 700; color: var(--text-main);">
                                    {{ $ret->date->format('Y-m-d') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: #475569;">
                                    <i class="fa-solid fa-warehouse" style="color: var(--primary); margin-left: 4px;"></i>
                                    {{ $ret->store->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if($ret->parent)
                                    <span style="font-family: monospace; font-size: 12.5px; font-weight: 700; color: var(--primary);">
                                        #{{ $ret->parent->invoice_number }}
                                    </span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">مرتجع حر مباشر</span>
                                @endif
                            </td>
                            <td>
                                <strong style="font-size: 14.5px; font-weight: 900; color: #DC2626;">
                                    {{ formatMoney($ret->grand_total) }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge badge-success">معتمد ومؤثر في المخزون</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-rotate-left" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                                لا توجد حركات مرتجعات مسجلة في النظام
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returns->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $returns->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

