@extends('layouts.app')

@section('title', 'مصفوفة وتقارير الأصول الثابتة والإهلاك (Fixed Assets Analytics)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
                مصفوفة وتحليلات الأصول الثابتة والإهلاك (Fixed Assets & Depreciation Matrix)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحليل محفظة الأصول، مجمع الإهلاك، صافي القيمة الدفترية، والتوزيع الجغرافي والنوعي
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('assets.assets.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-building-columns" style="color: var(--primary);"></i>
                سجل الأصول
            </a>
            <a href="{{ route('assets.depreciations.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calculator" style="color: var(--accent-gold);"></i>
                قيود الإهلاك
            </a>
            <a href="{{ route('assets.reports.print', request()->query()) }}" target="_blank" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-print"></i>
                طباعة كشف وسجل الأصول
            </a>
        </div>
    </div>

    <!-- Consolidated KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الأصول المفحوصة</span>
                <strong style="font-size: 19px; font-weight: 900; color: var(--primary);">{{ $consolidated['total_assets_count'] }} أصل</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">التكلفة التاريخية الإجمالية</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ number_format($consolidated['total_gross_cost'], 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #EF4444; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #EF4444; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-arrow-trend-down"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مجمع الإهلاك المتراكم</span>
                <strong style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ number_format($consolidated['total_accum_dep'], 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">صافي القيمة الدفترية الحالية</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ number_format($consolidated['total_net_book_val'], 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إهلاك السنة المالية (YTD)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #4338CA;">{{ number_format($consolidated['ytd_depreciation'], 2) }} ر.س</strong>
            </div>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('assets.reports.index') }}" style="display: grid; grid-template-columns: 1fr 1fr 1fr 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الفرع</label>
                <select name="branch_id" class="erp-input">
                    <option value="">جميع الفروع</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">التصنيف</label>
                <select name="category_id" class="erp-input">
                    <option value="">جميع التصنيفات</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الحالة</label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط بالخدمة</option>
                    <option value="in_maintenance" {{ request('status') === 'in_maintenance' ? 'selected' : '' }}>تحت الصيانة</option>
                    <option value="fully_depreciated" {{ request('status') === 'fully_depreciated' ? 'selected' : '' }}>مكتمل الإهلاك دفترياً</option>
                    <option value="disposed" {{ request('status') === 'disposed' ? 'selected' : '' }}>مستبعد / مكهّن</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>مباع</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; font-size: 13px; font-weight: 800; justify-content: center;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>
        </form>
    </div>

    <!-- Category & Branch Matrices Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        
        <!-- Category Breakdown -->
        <div class="erp-card" style="margin-bottom: 0; padding: 0; overflow: hidden;">
            <div style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); background: #F8FAFC;">
                <h4 style="font-size: 13.5px; font-weight: 800; color: var(--text-main); margin: 0;">
                    <i class="fa-solid fa-shapes" style="color: var(--primary); margin-left: 6px;"></i>
                    توزيع الأصول والقيمة حسب التصنيف
                </h4>
            </div>
            <div style="overflow-x: auto;">
                <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 12.5px;">
                    <thead style="background: #F8FAFC; color: var(--text-muted);">
                        <tr>
                            <th style="padding: 10px 14px;">التصنيف</th>
                            <th style="padding: 10px 14px; text-align: center;">العدد</th>
                            <th style="padding: 10px 14px;">التكلفة التاريخية</th>
                            <th style="padding: 10px 14px;">صافي القيمة الدفترية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryBreakdown as $row)
                        <tr style="border-bottom: 1px solid #EDF2F7;">
                            <td style="padding: 10px 14px; font-weight: 700;">{{ $row['category']->name }}</td>
                            <td style="padding: 10px 14px; text-align: center; font-weight: 800;">{{ $row['count'] }}</td>
                            <td style="padding: 10px 14px; color: var(--text-main);">{{ number_format($row['gross_cost'], 2) }} ر.س</td>
                            <td style="padding: 10px 14px; font-weight: 800; color: #0F5132;">{{ number_format($row['book_value'], 2) }} ر.س</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align: center; padding: 20px; color: var(--text-muted);">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Branch Breakdown -->
        <div class="erp-card" style="margin-bottom: 0; padding: 0; overflow: hidden;">
            <div style="padding: 14px 18px; border-bottom: 1px solid var(--border-color); background: #F8FAFC;">
                <h4 style="font-size: 13.5px; font-weight: 800; color: var(--text-main); margin: 0;">
                    <i class="fa-solid fa-network-wired" style="color: #4338CA; margin-left: 6px;"></i>
                    توزيع الأصول والقيمة حسب الفرع
                </h4>
            </div>
            <div style="overflow-x: auto;">
                <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 12.5px;">
                    <thead style="background: #F8FAFC; color: var(--text-muted);">
                        <tr>
                            <th style="padding: 10px 14px;">الفرع</th>
                            <th style="padding: 10px 14px; text-align: center;">العدد</th>
                            <th style="padding: 10px 14px;">التكلفة التاريخية</th>
                            <th style="padding: 10px 14px;">صافي القيمة الدفترية</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branchBreakdown as $row)
                        <tr style="border-bottom: 1px solid #EDF2F7;">
                            <td style="padding: 10px 14px; font-weight: 700;">{{ $row['branch']->name }}</td>
                            <td style="padding: 10px 14px; text-align: center; font-weight: 800;">{{ $row['count'] }}</td>
                            <td style="padding: 10px 14px; color: var(--text-main);">{{ number_format($row['gross_cost'], 2) }} ر.س</td>
                            <td style="padding: 10px 14px; font-weight: 800; color: #0F5132;">{{ number_format($row['book_value'], 2) }} ر.س</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" style="text-align: center; padding: 20px; color: var(--text-muted);">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Detailed Asset Register Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-list-check" style="color: var(--primary); margin-left: 6px;"></i>
                كشف وسجل الأصول التفصيلي ومجمع الإهلاك ({{ $assets->count() }} أصل)
            </h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">رقم الأصل</th>
                        <th style="padding: 12px 16px;">بيان الأصل والمواصفات</th>
                        <th style="padding: 12px 16px;">التصنيف والفرع</th>
                        <th style="padding: 12px 16px;">العهدة / الموقع</th>
                        <th style="padding: 12px 16px;">تاريخ الشراء</th>
                        <th style="padding: 12px 16px;">التكلفة التاريخية</th>
                        <th style="padding: 12px 16px;">قيمة الخردة</th>
                        <th style="padding: 12px 16px;">مجمع الإهلاك</th>
                        <th style="padding: 12px 16px;">صافي القيمة الدفترية</th>
                        <th style="padding: 12px 16px; text-align: center;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $ast)
                    <tr style="border-bottom: 1px solid #EDF2F7;">
                        <td style="padding: 12px 16px; font-family: monospace; font-weight: 800; color: var(--primary);">
                            <a href="{{ route('assets.assets.show', $ast->id) }}" style="color: inherit; text-decoration: none;">
                                {{ $ast->asset_number }}
                            </a>
                        </td>
                        <td style="padding: 12px 16px; font-weight: 800; color: var(--text-main);">
                            {{ $ast->name }}
                        </td>
                        <td style="padding: 12px 16px;">
                            <strong style="display: block;">{{ $ast->category?->name ?? '—' }}</strong>
                            <small style="color: var(--text-muted);">{{ $ast->branch?->name ?? '—' }}</small>
                        </td>
                        <td style="padding: 12px 16px; font-size: 12px;">
                            {{ $ast->custodian?->name ?: 'عهدة عامة' }}
                            @if($ast->location) <small style="color: var(--text-muted); display: block;">{{ $ast->location->name }}</small> @endif
                        </td>
                        <td style="padding: 12px 16px; color: var(--text-main);">
                            {{ $ast->purchase_date ? $ast->purchase_date->format('Y-m-d') : '—' }}
                        </td>
                        <td style="padding: 12px 16px; font-weight: 700; color: var(--text-main);">
                            {{ number_format($ast->purchase_cost, 2) }} ر.س
                        </td>
                        <td style="padding: 12px 16px; color: var(--text-muted);">
                            {{ number_format($ast->salvage_value, 2) }} ر.س
                        </td>
                        <td style="padding: 12px 16px; font-weight: 700; color: #DC2626;">
                            {{ number_format($ast->total_depreciated_amount, 2) }} ر.س
                        </td>
                        <td style="padding: 12px 16px; font-weight: 900; color: #0F5132;">
                            {{ number_format($ast->current_book_value, 2) }} ر.س
                        </td>
                        <td style="padding: 12px 16px; text-align: center;">
                            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-main);">
                                {{ $ast->status_label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background: #F8FAFC; border-top: 2px solid var(--border-color); font-weight: 900;">
                    <tr>
                        <td colspan="5" style="padding: 14px 16px; font-size: 14px;">الإجمالي المجمّع:</td>
                        <td style="padding: 14px 16px; color: var(--text-main); font-size: 14px;">{{ number_format($consolidated['total_gross_cost'], 2) }} ر.س</td>
                        <td style="padding: 14px 16px; color: var(--text-muted); font-size: 14px;">{{ number_format($consolidated['total_salvage'], 2) }} ر.س</td>
                        <td style="padding: 14px 16px; color: #DC2626; font-size: 14px;">{{ number_format($consolidated['total_accum_dep'], 2) }} ر.س</td>
                        <td style="padding: 14px 16px; color: #0F5132; font-size: 14.5px;">{{ number_format($consolidated['total_net_book_val'], 2) }} ر.س</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
