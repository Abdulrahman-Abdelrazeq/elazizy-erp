@extends('layouts.app')

@section('title', 'أوامر الشراء (Purchase Orders)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-file-contract" style="color: var(--primary); margin-left: 8px;"></i>
                أوامر الشراء والتوريد (Purchase Orders - PO)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إصدار أوامر الشراء الرسمية للموردين، ومتابعة شحنات التوريد وتحويلها لمستودعات المؤسسة
            </span>
        </div>

        <a href="{{ route('invoices.orders.create') }}" class="btn-primary-custom">
            <i class="fa-solid fa-plus-circle"></i>
            إصدار أمر شراء جديد
        </a>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-file-contract"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي أوامر الشراء</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--primary);">{{ $stats['total_orders'] }} أمر</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تم استلامها وتوريدها للمخزن</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ $stats['received'] }} أمر</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">قيد انتظار التوريد والشحن</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ $stats['pending'] }} أمر</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي قيمة الأوامر</span>
                <strong style="font-size: 18px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['total_value']) }}</strong>
            </div>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form action="{{ route('invoices.orders.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم أمر الشراء أو اسم المورد..." style="width: 100%; padding: 9px 36px 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                <i class="fa-solid fa-search" style="position: absolute; right: 12px; top: 12px; color: #94A3B8;"></i>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 9px 20px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>
        </form>
    </div>

    <!-- Orders Table Card -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>رقم أمر الشراء</th>
                        <th>المورد المعتمد</th>
                        <th>تاريخ الإصدار</th>
                        <th>تاريخ التوريد المتوقع</th>
                        <th>إجمالي القيمة</th>
                        <th>الحالة</th>
                        <th>المسؤول</th>
                        <th style="text-align: center;">الاستلام والتوريد للمخزن</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $ord)
                        <tr>
                            <td>
                                <span style="font-weight: 800; color: var(--primary); font-family: monospace; font-size: 13.5px;">
                                    {{ $ord->invoice_number }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 14px; color: var(--text-main);">{{ $ord->client->name ?? 'غير محدد' }}</strong>
                            </td>
                            <td>
                                <span style="font-size: 13px; font-weight: 700; color: var(--text-main);">
                                    {{ $ord->date->format('Y-m-d') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-muted);">
                                    {{ $ord->due_date ? $ord->due_date->format('Y-m-d') : '-' }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 14.5px; font-weight: 900; color: var(--primary);">
                                    {{ formatMoney($ord->grand_total) }}
                                </strong>
                            </td>
                            <td>
                                @if($ord->status === 'closed')
                                    <span class="badge badge-success">تم الاستلام بالمخزن</span>
                                @else
                                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700;">قيد انتظار التوريد</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--text-muted);">{{ $ord->creator->name ?? 'النظام' }}</span>
                            </td>
                            <td style="text-align: center;">
                                @if($ord->status !== 'closed')
                                    <button onclick="openReceiveOrderModal({{ $ord->id }}, '{{ $ord->invoice_number }}', '{{ addslashes($ord->client->name ?? 'المورد') }}', '{{ formatMoney($ord->grand_total) }}')" class="btn-primary-custom" style="padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-boxes-packing"></i> استلام وتوريد للمخزن
                                    </button>
                                @else
                                    <span style="color: #16A34A; font-weight: 700; font-size: 12px;">
                                        <i class="fa-solid fa-check-double"></i> مستلم ومورد
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-file-contract" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                                لا توجد أوامر شراء مسجلة حالياً
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Receive PO Modal -->
<div id="receiveOrderModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-boxes-packing" style="color: var(--primary);"></i> استلام البضاعة وتوليد فاتورة المشتريات
            </h3>
            <button onclick="document.getElementById('receiveOrderModal').style.display='none'" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer;">&times;</button>
        </div>

        <form id="receiveOrderForm" method="POST">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 12px; font-size: 13px;">
                    <div>أمر الشراء: <strong id="modalOrderNumber" style="font-family: monospace; color: var(--primary);"></strong></div>
                    <div>المورد: <strong id="modalOrderSupplier"></strong></div>
                    <div>قيمة الشحنة: <strong id="modalOrderTotal" style="color: var(--primary);"></strong></div>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">مستودع إيداع واستلام البضاعة <span style="color: #DC2626;">*</span></label>
                    <select name="store_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                        @foreach(\Modules\Inventory\Entities\Store::where('is_active', true)->get() as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">طريقة سداد المورد <span style="color: #DC2626;">*</span></label>
                    <select name="payment_type" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                        <option value="credit">آجل (مستحق للمورد على الحساب)</option>
                        <option value="cash">نقدي (تم سداد كامل المبلغ كاش)</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('receiveOrderModal').style.display='none'" style="background: #E2E8F0; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; color: #475569; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 24px;">تأكيد الاستلام وإضافة المخزون</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openReceiveOrderModal(id, number, supplier, total) {
    document.getElementById('receiveOrderForm').action = "{{ url('invoices/orders') }}/" + id + "/convert";
    document.getElementById('modalOrderNumber').innerText = number;
    document.getElementById('modalOrderSupplier').innerText = supplier;
    document.getElementById('modalOrderTotal').innerText = total;
    document.getElementById('receiveOrderModal').style.display = 'flex';
}
</script>
@endsection
