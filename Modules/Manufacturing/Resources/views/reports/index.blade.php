@extends('layouts.app')

@section('title', 'تحليلات وتقارير عمليات وتكاليف التصنيع')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
                تحليلات وتقارير عمليات وتكاليف التصنيع (Manufacturing Analytics)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                مراقبة تكلفة البضاعة المصنعة (COGM)، كفاءة استهلاك المواد الخام، ومؤشرات الأداء الإنتاجي
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('manufacturing.orders.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-clipboard-check" style="color: var(--primary);"></i>
                أوامر التصنيع
            </a>
            <button onclick="window.print()" style="background: #0F5132; color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i> طباعة التقرير
            </button>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('manufacturing.reports.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px 90px; gap: 12px; align-items: flex-end;">
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
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">مخزن الإنتاج التام</label>
                <select name="store_id" class="erp-input">
                    <option value="">جميع المخازن</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}" {{ request('store_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ الإنجاز</label>
                <input type="date" name="from_date" class="erp-input" value="{{ request('from_date') }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ الإنجاز</label>
                <input type="date" name="to_date" class="erp-input" value="{{ request('to_date') }}">
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>

            <div>
                <a href="{{ route('manufacturing.reports.index') }}" style="display: block; width: 100%; text-align: center; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Aggregate Production & Cost Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">عمليات التصنيع المكتملة</span>
            <strong style="font-size: 22px; font-weight: 900; color: var(--primary);">{{ $stats['operations_count'] }} عملية</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">إجمالي الوحدات المنتجة</span>
            <strong style="font-size: 22px; font-weight: 900; color: #0F5132;">{{ number_format($stats['total_produced'], 0) }} وحدة</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">تكلفة الإنتاج الكلية (COGM)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['total_cost']) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">متوسط تكلفة الوحدة المصنعة</span>
            <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ formatMoney($stats['avg_unit_cost']) }}</strong>
        </div>
    </div>

    <!-- Cost Breakdown Analytics Bar -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-chart-column" style="color: var(--primary);"></i>
            توزيع عناصر تكاليف التصنيع
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
            <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 16px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">تكاليف المواد الخام المباشرة</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['material_cost']) }}</strong>
                <span style="display: block; font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                    تشكل {{ $stats['total_cost'] > 0 ? round(($stats['material_cost'] / $stats['total_cost']) * 100, 1) : 0 }}% من التكلفة
                </span>
            </div>

            <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 16px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">تكاليف الأيدي العاملة والتشغيل</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ formatMoney($stats['labor_cost']) }}</strong>
                <span style="display: block; font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                    تشكل {{ $stats['total_cost'] > 0 ? round(($stats['labor_cost'] / $stats['total_cost']) * 100, 1) : 0 }}% من التكلفة
                </span>
            </div>

            <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 16px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">التكاليف الإضافية غير المباشرة</span>
                <strong style="font-size: 18px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['overhead_cost']) }}</strong>
                <span style="display: block; font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                    تشكل {{ $stats['total_cost'] > 0 ? round(($stats['overhead_cost'] / $stats['total_cost']) * 100, 1) : 0 }}% من التكلفة
                </span>
            </div>
        </div>
    </div>

    <!-- Top Produced Products -->
    @if($topProducts->count() > 0)
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-trophy" style="color: var(--accent-gold);"></i>
            أعلى الأصناف إنتاجاً (Top Manufactured Products)
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 40px;">#</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">المنتج المصنع</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">مرات التشغيل</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 160px;">إجمالي الكمية المنتجة</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 160px;">إجمالي التكلفة المنفقة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $idx => $tp)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center; font-weight: 800; color: var(--text-muted);">
                            {{ $idx + 1 }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            {{ $tp->product?->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ $tp->runs_count }} أوامر تشغيل
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: #0F5132;">
                            {{ number_format($tp->total_qty, 0) }} {{ $tp->product?->unit?->name ?? 'وحدة' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: var(--primary);">
                            {{ formatMoney($tp->total_spent) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Production Log Table -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-list-check" style="color: var(--primary);"></i>
            سجل عمليات التصنيع المكتملة
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">رقم الأمر</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">تاريخ الإنجاز</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color);">المنتج المصنع</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">مخزن التوريد</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">الكمية المنتجة</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">تكلفة الوحدة</th>
                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">التكلفة الكلية</th>
                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid var(--border-color); width: 80px;">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($completedOrders as $ord)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            {{ $ord->order_number }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                            {{ $ord->actual_completion_date ? $ord->actual_completion_date->format('Y-m-d') : '-' }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            {{ $ord->product?->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            {{ $ord->toStore?->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: #0F5132;">
                            {{ $ord->produced_quantity }} {{ $ord->product?->unit?->name }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ formatMoney($ord->unit_cost) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: var(--primary);">
                            {{ formatMoney($ord->actual_total_cost) }}
                        </td>
                        <td style="padding: 10px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            <a href="{{ route('manufacturing.orders.show', $ord->id) }}" style="width: 30px; height: 30px; border-radius: 6px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 30px; text-align: center; color: var(--text-muted);">
                            لا توجد عمليات تصنيع مكتملة مسجلة في هذه الفترة.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($completedOrders->hasPages())
            <div style="margin-top: 20px;">
                {{ $completedOrders->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
