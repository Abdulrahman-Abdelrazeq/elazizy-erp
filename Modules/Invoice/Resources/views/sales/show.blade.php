@extends('layouts.app')

@section('title', 'فاتورة مبيعات: ' . $sale->invoice_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1100px; margin: 0 auto;">

    <!-- Action Toolbar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0;">
                    <i class="fa-solid fa-file-invoice" style="color: var(--primary);"></i>
                    فاتورة مبيعات: {{ $sale->invoice_number }}
                </h2>
                @if($sale->status === 'approved')
                    <span class="badge badge-success">معتمدة</span>
                @elseif($sale->status === 'cancelled')
                    <span class="badge badge-danger">ملغاة</span>
                @else
                    <span class="badge badge-warning">{{ $sale->status_label }}</span>
                @endif
            </div>
            <span style="font-size: 13px; color: var(--text-muted); margin-top: 4px; display: block;">
                تاريخ الإصدار: <strong>{{ $sale->date->format('Y-m-d') }}</strong> | تم الإنشاء بواسطة: <strong>{{ $sale->creator->name ?? 'المشرف' }}</strong>
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('invoices.sales.print', $sale->id) }}" target="_blank" class="btn-primary-custom" style="padding: 9px 18px;">
                <i class="fa-solid fa-print"></i> طباعة الفاتورة
            </a>
            <a href="{{ route('invoices.returns.create') }}?type=sale_return&invoice_id={{ $sale->id }}" style="background: #FEE2E2; color: #DC2626; border: 1px solid #FECACA; padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-rotate-left"></i> طلب مرتجع
            </a>
            <a href="{{ route('invoices.sales.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-right"></i> القائمة
            </a>
        </div>
    </div>

    <!-- Official Invoice Document Box -->
    <div class="erp-card" style="padding: 32px; background: #fff; border-radius: 16px;">
        
        <!-- Document Top Info Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; border-bottom: 2px solid var(--border-color); padding-bottom: 24px; margin-bottom: 24px;">
            <div>
                <span style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">المنشأة المصدرة:</span>
                <h3 style="font-size: 18px; font-weight: 900; color: var(--primary); margin: 4px 0 6px 0;">
                    {{ getSetting('company_name', 'مؤسسة العزيزي للحلول المتكاملة') }}
                </h3>
                <div style="font-size: 12.5px; color: var(--text-muted); line-height: 1.5;">
                    <div>{{ getSetting('company_address', 'القاهرة - جمهورية مصر العربية') }}</div>
                    <div>هاتف: {{ getSetting('company_phone', '01000000000') }}</div>
                    <div>الرقم الضريبي: <strong>{{ getSetting('company_tax_number', '123-456-789') }}</strong></div>
                </div>
            </div>

            <div>
                <span style="font-size: 11px; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">بيانات العميل:</span>
                <h4 style="font-size: 16px; font-weight: 900; color: var(--text-main); margin: 4px 0 6px 0;">
                    {{ $sale->client ? $sale->client->name : 'عميل نقدي عام' }}
                </h4>
                @if($sale->client)
                    <div style="font-size: 12.5px; color: var(--text-muted); line-height: 1.5;">
                        @if($sale->client->company_name)
                            <div>{{ $sale->client->company_name }}</div>
                        @endif
                        @if($sale->client->mobile)
                            <div>هاتف: {{ $sale->client->mobile }}</div>
                        @endif
                        @if($sale->client->tax_number)
                            <div>الرقم الضريبي للعميل: <strong>{{ $sale->client->tax_number }}</strong></div>
                        @endif
                    </div>
                @endif
            </div>

            <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 14px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12.5px;">
                    <span style="color: var(--text-muted);">رقم الفاتورة:</span>
                    <strong style="font-family: monospace; color: var(--primary);">{{ $sale->invoice_number }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12.5px;">
                    <span style="color: var(--text-muted);">طريقة السداد:</span>
                    <strong style="color: var(--text-main);">{{ $sale->payment_type_label }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 12.5px;">
                    <span style="color: var(--text-muted);">المستودع المصروف منه:</span>
                    <strong style="color: var(--text-main);">{{ $sale->store->name ?? '-' }}</strong>
                </div>
                @if($sale->due_date)
                    <div style="display: flex; justify-content: space-between; font-size: 12.5px;">
                        <span style="color: var(--text-muted);">تاريخ الاستحقاق:</span>
                        <strong style="color: #DC2626;">{{ $sale->due_date->format('Y-m-d') }}</strong>
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
                        <th style="padding: 10px 14px; font-size: 12px;">الكمية</th>
                        <th style="padding: 10px 14px; font-size: 12px;">سعر الوحدة</th>
                        <th style="padding: 10px 14px; font-size: 12px;">الخصم</th>
                        <th style="padding: 10px 14px; font-size: 12px;">الإجمالي الصافي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $index => $item)
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
                    <span style="color: var(--text-muted); font-weight: 700;">المجموع قبل الخصم والضريبة:</span>
                    <strong style="color: var(--text-main);">{{ formatMoney($sale->subtotal) }}</strong>
                </div>

                @if($sale->discount_amount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #DC2626;">
                        <span style="font-weight: 700;">الخصم الإضافي ({{ $sale->discount_type === 'percentage' ? $sale->discount_value.'%' : 'مبلغ' }}):</span>
                        <strong>-{{ formatMoney($sale->discount_amount) }}</strong>
                    </div>
                @endif

                @if($sale->tax_amount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #2563EB;">
                        <span style="font-weight: 700;">ضريبة القيمة المضافة ({{ $sale->tax_rate }}%):</span>
                        <strong>+{{ formatMoney($sale->tax_amount) }}</strong>
                    </div>
                @endif

                @if($sale->shipping_cost > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 13px;">
                        <span style="color: var(--text-muted); font-weight: 700;">تكلفة الشحن والتوصيل:</span>
                        <strong>+{{ formatMoney($sale->shipping_cost) }}</strong>
                    </div>
                @endif

                <hr style="border: 0; border-top: 1.5px dashed var(--border-color); margin: 4px 0;">

                <div style="display: flex; justify-content: space-between; font-size: 16px;">
                    <span style="font-weight: 900; color: var(--primary);">الصافي الإجمالي:</span>
                    <strong style="font-weight: 900; color: var(--primary); font-size: 18px;">{{ formatMoney($sale->grand_total) }}</strong>
                </div>

                <div style="display: flex; justify-content: space-between; font-size: 13px; color: #16A34A;">
                    <span style="font-weight: 700;">المبلغ المسدد:</span>
                    <strong style="font-weight: 800;">{{ formatMoney($sale->paid_amount) }}</strong>
                </div>

                @if($sale->remaining_amount > 0)
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #DC2626; background: #FEE2E2; padding: 6px 10px; border-radius: 6px;">
                        <span style="font-weight: 700;">المتبقي الآجل:</span>
                        <strong style="font-weight: 900;">{{ formatMoney($sale->remaining_amount) }}</strong>
                    </div>
                @endif
            </div>
        </div>

        @if($sale->notes)
            <div style="background: #FFFBEB; border-right: 4px solid var(--accent-gold); padding: 12px 16px; border-radius: 8px; font-size: 12.5px; color: #78350F; margin-bottom: 20px;">
                <strong>ملاحظات:</strong> {{ $sale->notes }}
            </div>
        @endif

    </div>
</div>
@endsection

