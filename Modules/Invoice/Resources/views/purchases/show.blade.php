@extends('layouts.app')

@section('title', 'فاتورة مشتريات: ' . $purchase->invoice_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1100px; margin: 0 auto;">

    <!-- Action Toolbar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0;">
                    <i class="fa-solid fa-cart-shopping" style="color: var(--primary);"></i>
                    فاتورة مشتريات وتوريد: {{ $purchase->invoice_number }}
                </h2>
                <span class="badge badge-success">معتمدة وموردة</span>
            </div>
            <span style="font-size: 13px; color: var(--text-muted); margin-top: 4px; display: block;">
                تاريخ التوريد: <strong>{{ $purchase->date->format('Y-m-d') }}</strong> | تم الإدخال بواسطة: <strong>{{ $purchase->creator->name ?? 'المشرف' }}</strong>
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('invoices.returns.create') }}?type=purchase_return&invoice_id={{ $purchase->id }}" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-rotate-left"></i> مردود مشتريات
            </a>
            <a href="{{ route('invoices.purchases.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-right"></i> القائمة
            </a>
        </div>
    </div>

    <!-- Official Document Box -->
    <div class="erp-card" style="padding: 32px; background: #fff; border-radius: 16px;">
        
        <!-- Top Info Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; border-bottom: 2px solid var(--border-color); padding-bottom: 24px; margin-bottom: 24px;">
            <div>
                <span style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">المستلم (المنشأة):</span>
                <h3 style="font-size: 18px; font-weight: 900; color: var(--primary); margin: 4px 0 6px 0;">
                    {{ getSetting('company_name', 'مؤسسة العزيزي للحلول المتكاملة') }}
                </h3>
                <div style="font-size: 12.5px; color: var(--text-muted); line-height: 1.5;">
                    <div>المستودع المستقبل: <strong>{{ $purchase->store->name ?? '-' }}</strong></div>
                </div>
            </div>

            <div>
                <span style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">المورد:</span>
                <h4 style="font-size: 16px; font-weight: 900; color: var(--text-main); margin: 4px 0 6px 0;">
                    {{ $purchase->client ? $purchase->client->name : 'مورد غير محدد' }}
                </h4>
                @if($purchase->client)
                    <div style="font-size: 12.5px; color: var(--text-muted); line-height: 1.5;">
                        @if($purchase->client->company_name)
                            <div>{{ $purchase->client->company_name }}</div>
                        @endif
                        @if($purchase->client->mobile)
                            <div>هاتف: {{ $purchase->client->mobile }}</div>
                        @endif
                        @if($purchase->client->tax_number)
                            <div>الرقم الضريبي للمورد: <strong>{{ $purchase->client->tax_number }}</strong></div>
                        @endif
                    </div>
                @endif
            </div>

            <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 14px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12.5px;">
                    <span style="color: var(--text-muted);">رقم السند / الفاتورة:</span>
                    <strong style="font-family: monospace; color: var(--primary);">{{ $purchase->invoice_number }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12.5px;">
                    <span style="color: var(--text-muted);">طريقة السداد:</span>
                    <strong style="color: var(--text-main);">{{ $purchase->payment_type_label }}</strong>
                </div>
                @if($purchase->due_date)
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                        <span style="color: var(--text-muted);">تاريخ استحقاق السداد:</span>
                        <strong style="color: #DC2626;">{{ $purchase->due_date->format('Y-m-d') }}</strong>
                    </div>
                @endif
            </div>
        </div>

        <!-- Line Items Table -->
        <div style="overflow-x: auto; margin-bottom: 24px;">
            <table class="erp-table" style="width: 100%;">
                <thead>
                    <tr style="background: #F8FAFC;">
                        <th style="padding: 10px 14px; font-size: 12px;">م</th>
                        <th style="padding: 10px 14px; font-size: 12px;">الصنف والوصف</th>
                        <th style="padding: 10px 14px; font-size: 12px;">الوحدة</th>
                        <th style="padding: 10px 14px; font-size: 12px;">الكمية المستلمة</th>
                        <th style="padding: 10px 14px; font-size: 12px;">سعر الشراء</th>
                        <th style="padding: 10px 14px; font-size: 12px;">الخصم</th>
                        <th style="padding: 10px 14px; font-size: 12px;">الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchase->items as $index => $item)
                        <tr>
                            <td style="padding: 10px 14px; font-weight: 700;">{{ $index + 1 }}</td>
                            <td style="padding: 10px 14px;">
                                <div>
                                    <strong style="font-size: 14px; color: var(--text-main);">{{ $item->item->name ?? '-' }}</strong>
                                    <small style="color: var(--text-muted); font-size: 11.5px; display: block;">كود: {{ $item->item->code ?? '-' }}</small>
                                </div>
                            </td>
                            <td style="padding: 10px 14px; font-size: 12.5px;">{{ $item->unit ? $item->unit->name : 'قطعة' }}</td>
                            <td style="padding: 10px 14px; font-weight: 800; font-size: 13.5px;">{{ $item->quantity }}</td>
                            <td style="padding: 10px 14px; font-weight: 700;">{{ formatMoney($item->unit_price) }}</td>
                            <td style="padding: 10px 14px; font-size: 12.5px; color: #DC2626;">
                                {{ $item->discount_amount > 0 ? formatMoney($item->discount_amount) : '-' }}
                            </td>
                            <td style="padding: 10px 14px; font-weight: 900; color: var(--primary); font-size: 14px;">
                                {{ formatMoney($item->total) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals & Payment Breakdown -->
        <div style="display: flex; justify-content: flex-end; margin-bottom: 24px;">
            <div style="width: 100%; max-width: 380px; background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between; font-size: 13px;">
                    <span style="color: var(--text-muted); font-weight: 700;">المجموع قبل الخصم:</span>
                    <strong style="color: var(--text-main);">{{ formatMoney($purchase->subtotal) }}</strong>
                </div>

                @if($purchase->discount_amount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #DC2626;">
                        <span style="font-weight: 700;">الخصم:</span>
                        <strong>-{{ formatMoney($purchase->discount_amount) }}</strong>
                    </div>
                @endif

                @if($purchase->tax_amount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #2563EB;">
                        <span style="font-weight: 700;">ضريبة القيمة المضافة:</span>
                        <strong>+{{ formatMoney($purchase->tax_amount) }}</strong>
                    </div>
                @endif

                <hr style="border: 0; border-top: 1.5px dashed var(--border-color); margin: 4px 0;">

                <div style="display: flex; justify-content: space-between; font-size: 16px;">
                    <span style="font-weight: 900; color: var(--primary);">الإجمالي المستحق للمورد:</span>
                    <strong style="font-weight: 900; color: var(--primary); font-size: 18px;">{{ formatMoney($purchase->grand_total) }}</strong>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #16A34A;">
                    <span style="font-weight: 700;">المسدد للمورد:</span>
                    <strong style="font-weight: 800;">{{ formatMoney($purchase->paid_amount) }}</strong>
                </div>

                @if($purchase->remaining_amount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #DC2626; background: #FEE2E2; padding: 6px 10px; border-radius: 6px;">
                        <span style="font-weight: 700;">المتبقي للمورد (آجل):</span>
                        <strong style="font-weight: 900;">{{ formatMoney($purchase->remaining_amount) }}</strong>
                    </div>
                @endif
            </div>
        </div>

        @if($purchase->notes)
            <div style="background: #FFFBEB; border-right: 4px solid var(--accent-gold); padding: 12px 16px; border-radius: 8px; font-size: 12.5px; color: #78350F;">
                <strong>ملاحظات:</strong> {{ $purchase->notes }}
            </div>
        @endif

    </div>
</div>
@endsection
