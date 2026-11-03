@extends('layouts.app')

@section('title', 'أمر تشغيل وتصنيع - ' . $order->order_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0;">
                    أمر التشغيل: <span style="font-family: monospace; color: var(--primary);">{{ $order->order_number }}</span>
                </h2>
                @if($order->status === 'completed')
                    <span style="background: #D1E7DD; color: #0F5132; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-circle-check"></i> مكتمل ومُورّد للمخزن
                    </span>
                @elseif($order->status === 'in_progress')
                    <span style="background: #FEF3C7; color: #D97706; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-spinner fa-spin"></i> قيد التشغيل الصناعي
                    </span>
                @elseif($order->status === 'confirmed')
                    <span style="background: #E0E7FF; color: #4338CA; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-stamp"></i> مؤكد وجاهز للتشغيل
                    </span>
                @elseif($order->status === 'cancelled')
                    <span style="background: #FEE2E2; color: #DC2626; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800;">
                        ملغي
                    </span>
                @else
                    <span style="background: #F1F5F9; color: var(--text-muted); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800;">
                        مسودة تخطيطية
                    </span>
                @endif
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">
                تاريخ الأمر: {{ $order->order_date ? $order->order_date->format('Y-m-d') : '-' }} &bull; الفرع: {{ $order->branch?->name ?? 'المركز الرئيسي' }} &bull; بواسطة: {{ $order->creator?->name ?? 'النظام' }}
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('manufacturing.orders.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع للأوامر
            </a>

            <a href="{{ route('manufacturing.orders.print', $order->id) }}" target="_blank" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة أمر التشغيل
            </a>

            @if($order->status === 'draft')
                <form method="POST" action="{{ route('manufacturing.orders.confirm', $order->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #2563EB; color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-stamp"></i>
                        تأكيد أمر التشغيل
                    </button>
                </form>
            @endif

            @if(in_array($order->status, ['draft', 'confirmed']))
                <form method="POST" action="{{ route('manufacturing.orders.start', $order->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #D97706; color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-play"></i>
                        بدء التشغيل والإنتاج
                    </button>
                </form>
            @endif

            @if($order->status === 'in_progress')
                <button type="button" onclick="document.getElementById('completeModal').style.display='flex'" style="background: #0F5132; color: #fff; border: none; padding: 9px 20px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-check-to-slot"></i>
                    استكمال الإنتاج وتوريد المخزن
                </button>
            @endif

            @if(!in_array($order->status, ['completed', 'cancelled']))
                <form method="POST" action="{{ route('manufacturing.orders.cancel', $order->id) }}" onsubmit="return confirm('هل أنت متأكد من إلغاء أمر التصنيع؟')" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #FEE2E2; color: #DC2626; border: none; padding: 9px 16px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer;">
                        <i class="fa-solid fa-ban"></i> إلغاء
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Completion Success Banner if Completed -->
    @if($order->status === 'completed')
        <div style="background: #D1E7DD; border: 1px solid #A3CFBB; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #0F5132; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <strong style="color: #0F5132; font-size: 14.5px; display: block;">تم تنفيذ أمر التصنيع بالكامل وخصم الخامات وتوريد المنتج النهائي</strong>
                    <span style="color: #146C43; font-size: 12.5px;">
                        تاريخ الإنجاز: <strong>{{ $order->actual_completion_date ? $order->actual_completion_date->format('Y-m-d H:i') : '-' }}</strong> &bull; تم التوريد إلى: <strong>{{ $order->toStore?->name }}</strong> &bull; بواسطة: <strong>{{ $order->completedBy?->name ?? 'المشرف' }}</strong>
                    </span>
                </div>
            </div>
            <div style="font-size: 16px; font-weight: 900; color: #0F5132;">
                الكمية المنتجة: {{ $order->produced_quantity }} {{ $order->product?->unit?->name ?? 'وحدة' }}
            </div>
        </div>
    @endif

    <!-- Order Overview Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">المنتج المصنع تام الصنع</span>
            <strong style="font-size: 16px; font-weight: 900; color: var(--text-main); display: block;">{{ $order->product?->name }}</strong>
            <span style="font-size: 11px; color: var(--text-muted);">SKU: {{ $order->product?->sku ?: '-' }}</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">الكمية (المخططة / المنتجة)</span>
            <strong style="font-size: 20px; font-weight: 900; color: var(--text-main);">
                {{ $order->produced_quantity ?: $order->planned_quantity }} <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">/ {{ $order->planned_quantity }} {{ $order->product?->unit?->name }}</span>
            </strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">مسار المخازن</span>
            <div style="font-size: 13px; font-weight: 800; color: var(--text-main);">من: <span style="color: #DC2626;">{{ $order->fromStore?->name }}</span></div>
            <div style="font-size: 13px; font-weight: 800; color: var(--text-main);">إلى: <span style="color: #0F5132;">{{ $order->toStore?->name }}</span></div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #059669;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">إجمالي التكلفة</span>
            <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">
                {{ $order->status === 'completed' ? formatMoney($order->actual_total_cost) : formatMoney($order->estimated_total_cost) }}
            </strong>
            <span style="font-size: 11px; color: var(--text-muted); display: block;">تكلفة الوحدة: {{ formatMoney($order->unit_cost) }}</span>
        </div>
    </div>

    <!-- Raw Materials & Stock Availability Table -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i>
                المواد الخام المطلوبة وتوافرها في المخزن ({{ $order->fromStore?->name }})
            </h3>
            <span style="font-size: 12.5px; color: var(--text-muted);">
                تتبع الكميات المطلوبة والرصيد المتوفر قبل التنفيذ
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 40px;">#</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">المادة الخام</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الكمية المخططة</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">المستهلك فعلياً</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الرصيد المتاح بالمخزن</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">تكلفة الوحدة</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">الإجمالي</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">كفاية الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $idx => $it)
                    @php
                        $check = $stockCheck[$it->item_id] ?? ['available' => 0, 'sufficient' => false];
                    @endphp
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center; color: var(--text-muted); font-size: 11px;">
                            {{ $idx + 1 }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            {{ $it->item?->name }}
                            <span style="font-family: monospace; font-size: 11px; color: var(--text-muted); display: block;">SKU: {{ $it->item?->sku ?: '-' }}</span>
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ $it->planned_quantity }} {{ $it->item?->unit?->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: {{ $order->status === 'completed' ? '#0F5132' : 'var(--text-muted)' }};">
                            {{ $it->actual_quantity }} {{ $it->item?->unit?->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: {{ $check['sufficient'] ? '#0F5132' : '#DC2626' }};">
                            {{ $check['available'] }} {{ $it->item?->unit?->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ formatMoney($it->unit_cost) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: #0F5132;">
                            {{ formatMoney($it->total_cost) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($check['sufficient'])
                                <span style="background: #D1E7DD; color: #0F5132; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                    <i class="fa-solid fa-check"></i> متوفر
                                </span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 700;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> عجز
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Operations / Routing Table -->
    @if($order->bom && $order->bom->operations->count() > 0)
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: #D97706; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-route"></i> مراحل التشغيل ومحطات الإنتاج المعتمدة بأمر التشغيل
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 60px;">الترتيب</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">المرحلة / العملية</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 220px;">محطة العمل</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الوقت القياسي</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">تكلفة المرحلة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->bom->operations as $op)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center; font-weight: 800; color: #D97706;">
                            {{ $op->sequence_order }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            {{ $op->operation_name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            {{ $op->workstation?->name ?? 'يدوي عام' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ $op->standard_time_minutes }} دقيقة
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: #D97706;">
                            {{ formatMoney($op->operation_cost) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<!-- Modal: Complete Manufacturing Order -->
<div id="completeModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 600px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: modalFadeIn 0.2s ease;">
        <div style="background: linear-gradient(135deg, var(--primary) 0%, #062E20 100%); padding: 18px 24px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-check-to-slot"></i>
                </span>
                <h3 style="margin: 0; font-size: 16px; font-weight: 800;">استكمال وتوريد إنتاج أمر التصنيع</h3>
            </div>
            <button type="button" onclick="document.getElementById('completeModal').style.display='none'" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('manufacturing.orders.complete', $order->id) }}" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 14px; font-size: 12.5px; color: #065F46;">
                <strong>تحديث المخزون الفوري:</strong>
                سيتم خصم كميات المواد الخام المستهلكة من مخزن ({{ $order->fromStore?->name }}) وإيداع الناتج المصنع في مخزن ({{ $order->toStore?->name }}).
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الكمية المنتجة الصالحة للتوريد *</label>
                    <input type="number" step="0.001" min="0.001" name="produced_quantity" class="erp-input" value="{{ $order->planned_quantity }}" required>
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">كمية المعيب / التالف (إن وجد)</label>
                    <input type="number" step="0.001" min="0" name="rejected_quantity" class="erp-input" value="0.000">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">تكلفة العمالة الفعلية (SAR)</label>
                    <input type="number" step="0.01" min="0" name="actual_labor_cost" class="erp-input" value="{{ $order->estimated_labor_cost }}">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">تكاليف إضافية فعلية (SAR)</label>
                    <input type="number" step="0.01" min="0" name="actual_overhead_cost" class="erp-input" value="{{ $order->estimated_overhead_cost }}">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('completeModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--text-muted);">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                    <i class="fa-solid fa-check"></i> تأكيد التوريد وإنهاء الأمر
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
