@extends('layouts.app')

@section('title', 'تفاصيل قائمة المواد - ' . $bom->bom_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0;">
                    قائمة المواد: <span style="font-family: monospace; color: var(--primary);">{{ $bom->bom_number }}</span>
                </h2>
                @if($bom->is_active)
                    <span style="background: #D1E7DD; color: #0F5132; padding: 3px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800;">
                        <i class="fa-solid fa-circle-check"></i> نشط ومعتمد
                    </span>
                @else
                    <span style="background: #FEE2E2; color: #DC2626; padding: 3px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800;">
                        <i class="fa-solid fa-ban"></i> معطل
                    </span>
                @endif
                @if($bom->is_default)
                    <span style="background: #FEF3C7; color: #D97706; padding: 3px 12px; border-radius: 20px; font-size: 11.5px; font-weight: 800;">
                        <i class="fa-solid fa-star"></i> تركيبة افتراضية
                    </span>
                @endif
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">
                {{ $bom->name }} &bull; تم الإنشاء بواسطة: {{ $bom->creator?->name ?? 'النظام' }} &bull; التاريخ: {{ $bom->created_at->format('Y-m-d H:i') }}
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('manufacturing.boms.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع للقوائم
            </a>
            <a href="{{ route('manufacturing.orders.create') }}?bom_id={{ $bom->id }}" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-bolt"></i>
                إصدار أمر تصنيع فوري
            </a>
            <a href="{{ route('manufacturing.boms.edit', $bom->id) }}" style="background: #FEF3C7; color: #D97706; padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square"></i>
                تعديل التركيبة
            </a>
        </div>
    </div>

    <!-- Product Summary Header Banner -->
    <div class="erp-card" style="padding: 20px; margin-bottom: 0; background: linear-gradient(135deg, rgba(15, 81, 50, 0.05) 0%, rgba(6, 46, 32, 0.02) 100%); border-right: 4px solid var(--primary);">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 22px;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <div>
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">المنتج تام الصنع الناتج:</span>
                    <strong style="font-size: 17px; color: var(--text-main);">{{ $bom->product?->name }}</strong>
                    <span style="font-size: 12px; font-family: monospace; color: var(--text-muted); margin-right: 8px;">(SKU: {{ $bom->product?->sku ?? '-' }})</span>
                </div>
            </div>
            <div style="display: flex; gap: 20px; align-items: center;">
                <div>
                    <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">ناتج الدفعة الواحدة:</span>
                    <strong style="font-size: 16px; font-weight: 900; color: var(--primary);">
                        {{ $bom->output_quantity }} {{ $bom->unit?->name ?? ($bom->product?->unit?->name ?? 'وحدة') }}
                    </strong>
                </div>
                <div>
                    <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">سعر البيع الحالي:</span>
                    <strong style="font-size: 16px; font-weight: 900; color: #059669;">{{ formatMoney($bom->product?->sell_price ?? 0) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Cards (Cost Breakdown) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #64748B;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">تكلفة المواد الخام</span>
            <strong style="font-size: 19px; font-weight: 900; color: var(--text-main);">{{ formatMoney($bom->raw_material_cost) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">تكلفة العمالة والتشغيل</span>
            <strong style="font-size: 19px; font-weight: 900; color: #D97706;">{{ formatMoney($bom->labor_cost) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">تكاليف إضافية (Overheads)</span>
            <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ formatMoney($bom->overhead_cost) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">إجمالي تكلفة الدفعة الكلية</span>
            <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ formatMoney($bom->total_cost) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #059669; background: #ECFDF5;">
            <span style="font-size: 12px; font-weight: 800; color: #047857; display: block; margin-bottom: 4px;">تكلفة تصنيع الوحدة الواحدة</span>
            <strong style="font-size: 22px; font-weight: 900; color: #059669;">{{ formatMoney($bom->cost_per_unit) }}</strong>
        </div>
    </div>

    <!-- Components & Materials Table -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i>
            مكونات المواد الخام ({{ $bom->items->count() }} مواد)
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 40px;">#</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 100px;">كود الصنف</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم المادة الخام</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">الكمية المطلوبة</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">سعر تكلفة الوحدة</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">نسبة الهدر</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">إجمالي التكلفة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bom->items as $idx => $it)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center; color: var(--text-muted); font-size: 11px;">
                            {{ $idx + 1 }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            {{ $it->item?->sku ?: '-' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            {{ $it->item?->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ $it->quantity }} {{ $it->item?->unit?->name ?? 'وحدة' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ formatMoney($it->unit_cost) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: #DC2626;">
                            {{ $it->scrap_percentage > 0 ? $it->scrap_percentage . '%' : '0%' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: #0F5132;">
                            {{ formatMoney($it->total_cost) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #F8FAFC; font-weight: 900;">
                        <td colspan="6" style="padding: 12px 10px; text-align: left; color: var(--text-main);">إجمالي المواد الخام للدفعة:</td>
                        <td style="padding: 12px 10px; color: #0F5132; font-size: 14px;">{{ formatMoney($bom->raw_material_cost) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Routing Operations Table -->
    @if($bom->operations->count() > 0)
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: #D97706; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-route"></i> مراحل ومسارات التشغيل الصناعي (Routing)
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 60px;">الترتيب</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم المرحلة / العملية</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 220px;">محطة العمل</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الوقت القياسي</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">تكلفة المرحلة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bom->operations as $op)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center; font-weight: 800; color: #D97706;">
                            {{ $op->sequence_order }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            {{ $op->operation_name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            {{ $op->workstation?->name ?? 'تشغيل يدوي عام' }}
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
                <tfoot>
                    <tr style="background: #F8FAFC; font-weight: 900;">
                        <td colspan="4" style="padding: 12px 10px; text-align: left; color: var(--text-main);">إجمالي تكلفة العمليات:</td>
                        <td style="padding: 12px 10px; color: #D97706; font-size: 14px;">{{ formatMoney($bom->labor_cost) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
