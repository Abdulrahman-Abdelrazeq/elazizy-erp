@extends('layouts.app')

@section('title', 'عروض الأسعار للعملاء')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-handshake" style="color: var(--primary); margin-left: 8px;"></i>
                عروض الأسعار للعملاء (Price Quotations)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إنشاء ومتابعة عروض الأسعار، وتفعيلها وتحويلها مباشرة لفواتير مبيعات معتمدة
            </span>
        </div>

        <a href="{{ route('invoices.quotations.create') }}" class="btn-primary-custom">
            <i class="fa-solid fa-plus-circle"></i>
            إنشاء عرض سعر جديد
        </a>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي عروض الأسعار</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--primary);">{{ $stats['total_quotes'] }} عرض</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تم تحويلها لمبيعات فعلية</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ $stats['converted'] }} عرض</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">عروض قيد الانتظار والمتابعة</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ $stats['pending'] }} عرض</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">القيمة الإجمالية للعروض</span>
                <strong style="font-size: 18px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['total_value']) }}</strong>
            </div>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form action="{{ route('invoices.quotations.index') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 240px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث برقم عرض السعر أو اسم العميل..." style="width: 100%; padding: 9px 36px 9px 12px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                <i class="fa-solid fa-search" style="position: absolute; right: 12px; top: 12px; color: #94A3B8;"></i>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 9px 20px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>
        </form>
    </div>

    <!-- Quotations Table Card -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>رقم العرض</th>
                        <th>العميل المستهدف</th>
                        <th>تاريخ العرض</th>
                        <th>تاريخ الصلاحية</th>
                        <th>المجموع قبل الخصم</th>
                        <th>الخصم والضريبة</th>
                        <th>الصافي الإجمالي</th>
                        <th>الحالة</th>
                        <th>المسؤول</th>
                        <th style="text-align: center;">الإجراءات والتحويل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotations as $q)
                        <tr>
                            <td>
                                <span style="font-weight: 800; color: var(--primary); font-family: monospace; font-size: 13.5px;">
                                    {{ $q->invoice_number }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <strong style="font-size: 14px; color: var(--text-main); display: block;">{{ $q->client->name ?? 'عميل غير مسجل' }}</strong>
                                    @if($q->client && $q->client->company_name)
                                        <small style="color: var(--text-muted); font-size: 11.5px;">{{ $q->client->company_name }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 13px; font-weight: 700; color: var(--text-main);">
                                    {{ $q->date->format('Y-m-d') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-muted);">
                                    {{ $q->due_date ? $q->due_date->format('Y-m-d') : 'ساري حتى الإلغاء' }}
                                </span>
                            </td>
                            <td>{{ formatMoney($q->subtotal) }}</td>
                            <td>
                                <div style="font-size: 12px;">
                                    @if($q->discount_amount > 0)
                                        <span style="color: #DC2626; display: block;">خصم: -{{ formatMoney($q->discount_amount) }}</span>
                                    @endif
                                    @if($q->tax_amount > 0)
                                        <span style="color: #2563EB; display: block;">ضريبة: +{{ formatMoney($q->tax_amount) }}</span>
                                    @endif
                                    @if(!$q->discount_amount && !$q->tax_amount)
                                        <span>-</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <strong style="font-size: 14.5px; font-weight: 900; color: var(--primary);">
                                    {{ formatMoney($q->grand_total) }}
                                </strong>
                            </td>
                            <td>
                                @if($q->status === 'closed')
                                    <span class="badge badge-success">تم التحويل لفاتورة بيع</span>
                                @else
                                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700;">ساري قيد المتابعة</span>
                                @endif
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--text-muted);">{{ $q->creator->name ?? 'النظام' }}</span>
                            </td>
                            <td style="text-align: center;">
                                @if($q->status !== 'closed')
                                    <button onclick="openConvertModal({{ $q->id }}, '{{ $q->invoice_number }}', '{{ addslashes($q->client->name ?? 'العميل') }}', '{{ formatMoney($q->grand_total) }}')" class="btn-primary-custom" style="padding: 6px 12px; font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-arrow-right-arrow-left"></i> تحويل لفاتورة بيع
                                    </button>
                                @else
                                    <span style="color: #16A34A; font-weight: 700; font-size: 12px;">
                                        <i class="fa-solid fa-check-double"></i> مكتمل
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-handshake" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                                لا توجد عروض أسعار مسجلة حالياً
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($quotations->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $quotations->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Convert Quotation Modal -->
<div id="convertQuoteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-arrow-right-arrow-left" style="color: var(--primary);"></i> تحويل عرض السعر إلى فاتورة مبيعات
            </h3>
            <button onclick="document.getElementById('convertQuoteModal').style.display='none'" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer;">&times;</button>
        </div>

        <form id="convertQuoteForm" method="POST">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 12px; font-size: 13px;">
                    <div>عرض السعر: <strong id="modalQuoteNumber" style="font-family: monospace; color: var(--primary);"></strong></div>
                    <div>العميل: <strong id="modalClientName"></strong></div>
                    <div>القيمة: <strong id="modalGrandTotal" style="color: var(--primary);"></strong></div>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">مستودع صرف البضاعة <span style="color: #DC2626;">*</span></label>
                    <select name="store_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                        @foreach(\Modules\Inventory\Entities\Store::where('is_active', true)->get() as $st)
                            <option value="{{ $st->id }}">{{ $st->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">طريقة السداد المعتمدة <span style="color: #DC2626;">*</span></label>
                    <select name="payment_type" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                        <option value="cash">نقدي (كاش مسدد بالكامل)</option>
                        <option value="credit">آجل (ذمم على حساب العميل)</option>
                        <option value="card">بطاقة بنكية / مدى</option>
                    </select>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('convertQuoteModal').style.display='none'" style="background: #E2E8F0; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; color: #475569; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 24px;">تأكيد وصرف الفاتورة</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openConvertModal(id, number, client, total) {
    document.getElementById('convertQuoteForm').action = "{{ url('invoices/quotations') }}/" + id + "/convert";
    document.getElementById('modalQuoteNumber').innerText = number;
    document.getElementById('modalClientName').innerText = client;
    document.getElementById('modalGrandTotal').innerText = total;
    document.getElementById('convertQuoteModal').style.display = 'flex';
}
</script>
@endsection

