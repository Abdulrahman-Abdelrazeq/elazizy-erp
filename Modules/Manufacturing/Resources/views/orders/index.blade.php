@extends('layouts.app')

@section('title', 'أوامر التصنيع والتشغيل (Manufacturing Orders - MO)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-clipboard-check"></i>
                </span>
                أوامر التصنيع والتشغيل (Work Orders)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إصدار أوامر الإنتاج، حجز وصرف المواد الخام، تتبع خطوط التصنيع، وإيداع المنتجات التامة في المخازن
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('manufacturing.boms.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-layer-group" style="color: var(--primary);"></i>
                قوائم المواد (BOM)
            </a>
            <a href="{{ route('manufacturing.reports.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-pie" style="color: var(--accent-gold);"></i>
                تقارير الإنتاج
            </a>
            <a href="{{ route('manufacturing.orders.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-plus-circle"></i>
                إصدار أمر تصنيع جديد
            </a>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي أوامر التصنيع</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_orders'] }} أمر</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أوامر مكتملة وموردة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['completed'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-spinner"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">قيد التشغيل بالخط</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['in_progress'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #DBEAFE; color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-cubes"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الوحدات المنتجة</span>
                <strong style="font-size: 19px; font-weight: 900; color: #2563EB;">{{ number_format($stats['total_produced'], 0) }} وحدة</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">التكلفة الفعلية المنفذة</span>
                <strong style="font-size: 18px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['total_cost']) }}</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('manufacturing.orders.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) 100px 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الحالة</label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة (Draft)</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>مؤكد (Confirmed)</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>قيد التشغيل (In Progress)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل ومورد (Completed)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي (Cancelled)</option>
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">المنتج المصنع</label>
                <select name="product_id" class="erp-input">
                    <option value="">جميع المنتجات</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">المستودع / المخزن</label>
                <select name="store_id" class="erp-input">
                    <option value="">جميع المخازن</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="from_date" class="erp-input" value="{{ request('from_date') }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" class="erp-input" value="{{ request('to_date') }}">
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>

            <div>
                <a href="{{ route('manufacturing.orders.index') }}" style="display: block; width: 100%; text-align: center; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">رقم أمر التشغيل</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 105px;">تاريخ الأمر</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">المنتج وقائمة الـ BOM</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">الكمية المستهدفة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">الكمية المنتجة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">مخزن الصرف / الإنتاج</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">التكلفة الإجمالية</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">الحالة</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 120px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $ord)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            <a href="{{ route('manufacturing.orders.show', $ord->id) }}" style="color: inherit; text-decoration: none;">
                                {{ $ord->order_number }}
                            </a>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                            {{ $ord->order_date ? $ord->order_date->format('Y-m-d') : '-' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                            <span style="font-weight: 800; color: var(--text-main); display: block;">{{ $ord->product?->name }}</span>
                            <span style="font-size: 11px; color: var(--text-muted); display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-layer-group" style="color: var(--primary);"></i>
                                {{ $ord->bom?->name }} ({{ $ord->bom?->bom_number }})
                            </span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ $ord->planned_quantity }} {{ $ord->product?->unit?->name ?? 'وحدة' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: {{ $ord->status === 'completed' ? '#0F5132' : 'var(--text-muted)' }};">
                            {{ $ord->produced_quantity }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-size: 11.5px; color: var(--text-muted);">
                            <div>من: <strong>{{ $ord->fromStore?->name ?? '-' }}</strong></div>
                            <div>إلى: <strong style="color: #0F5132;">{{ $ord->toStore?->name ?? '-' }}</strong></div>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: #0F5132;">
                            {{ $ord->status === 'completed' ? formatMoney($ord->actual_total_cost) : formatMoney($ord->estimated_total_cost) }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($ord->status === 'completed')
                                <span style="background: #D1E7DD; color: #0F5132; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-circle-check"></i> مكتمل
                                </span>
                            @elseif($ord->status === 'in_progress')
                                <span style="background: #FEF3C7; color: #D97706; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-spinner fa-spin"></i> قيد التشغيل
                                </span>
                            @elseif($ord->status === 'confirmed')
                                <span style="background: #E0E7FF; color: #4338CA; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-stamp"></i> مؤكد
                                </span>
                            @elseif($ord->status === 'cancelled')
                                <span style="background: #FEE2E2; color: #DC2626; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">
                                    ملغي
                                </span>
                            @else
                                <span style="background: #F1F5F9; color: var(--text-muted); padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">
                                    مسودة
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('manufacturing.orders.show', $ord->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="عرض أمر التشغيل">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('manufacturing.orders.print', $ord->id) }}" target="_blank" style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; color: var(--text-main); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="طباعة أمر التشغيل">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @if($ord->status !== 'completed')
                                <form method="POST" action="{{ route('manufacturing.orders.destroy', $ord->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف أمر التصنيع؟')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="حذف">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-clipboard-check" style="font-size: 36px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                            لا توجد أوامر تصنيع مسجلة. اضغط "إصدار أمر تصنيع جديد" للبدء.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div style="margin-top: 20px;">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
