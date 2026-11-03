@extends('layouts.app')

@section('title', 'تحليلات ومقارنات أداء الفروع (Branches Performance & Consolidated Analytics)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-chart-line"></i>
                </span>
                مقارنات أداء الفروع والتحليلات المجمّعة (Consolidated Analytics)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                رؤية استراتيجية مجمّعة ومقارنة للمبيعات، تقييم المخزون، السيولة النقدية، والشحنات اللوجستية عبر كل الفروع
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('branches.branches.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-network-wired" style="color: var(--primary);"></i>
                دليل الفروع
            </a>
            <a href="{{ route('branches.transfers.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-truck-fast" style="color: var(--accent-gold);"></i>
                التحويلات البينية
            </a>
            <button onclick="window.print()" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer;">
                <i class="fa-solid fa-print"></i> طباعة التقرير المقارن
            </button>
        </div>
    </div>

    <!-- Consolidated KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <!-- Total Network Valuation -->
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي تقييم المخزون المجمّع</span>
                <strong style="font-size: 19px; font-weight: 900; color: var(--primary);">{{ number_format($consolidated['total_inventory_val'], 2) }} ر.س</strong>
            </div>
        </div>

        <!-- Monthly Network Sales -->
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مبيعات الشهر لكل الفروع</span>
                <strong style="font-size: 19px; font-weight: 900; color: #0F5132;">{{ number_format($consolidated['total_sales_month'], 2) }} ر.س</strong>
            </div>
        </div>

        <!-- Treasury Liquidity -->
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">رصيد الخزائن المجمّع</span>
                <strong style="font-size: 19px; font-weight: 900; color: #D97706;">{{ number_format($consolidated['total_treasury_bal'], 2) }} ر.س</strong>
            </div>
        </div>

        <!-- In-Transit Value -->
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-truck-moving"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">بضاعة في الطريق (In-Transit)</span>
                <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">
                    {{ number_format($consolidated['in_transit_value'], 2) }} ر.س
                    <small style="font-size: 12px; font-weight: 700; color: var(--text-muted);">({{ $consolidated['in_transit_transfers'] }} شحنة)</small>
                </strong>
            </div>
        </div>
    </div>

    <!-- Comparative Performance Matrix -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-table-columns" style="color: var(--primary);"></i>
                مصفوفة المقارنة الشاملة لشبكة الفروع (Branch Performance Matrix)
            </h4>
            <span style="font-size: 12.5px; font-weight: 700; color: var(--text-muted);">
                إجمالي شبكة العمل: {{ $consolidated['total_branches'] }} فرع | {{ $consolidated['total_stores'] }} مستودع | {{ $consolidated['total_employees'] }} موظف
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">الفرع / المركز</th>
                        <th style="padding: 12px 16px;">المدينة / التصنيف</th>
                        <th style="padding: 12px 16px;">المستودعات</th>
                        <th style="padding: 12px 16px;">فريق العمل</th>
                        <th style="padding: 12px 16px;">مبيعات الشهر الحالية</th>
                        <th style="padding: 12px 16px;">تقييم المخزون المتاح</th>
                        <th style="padding: 12px 16px;">سيولة الخزينة</th>
                        <th style="padding: 12px 16px; text-align: center;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branchStats as $st)
                    @php
                        $b = $st['branch'];
                        $salesShare = $consolidated['total_sales_month'] > 0 ? ($st['monthly_sales'] / $consolidated['total_sales_month']) * 100 : 0;
                        $invShare = $consolidated['total_inventory_val'] > 0 ? ($st['inventory_value'] / $consolidated['total_inventory_val']) * 100 : 0;
                    @endphp
                    <tr style="border-bottom: 1px solid #EDF2F7; transition: background 0.2s ease;">
                        <td style="padding: 14px 16px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(15, 81, 50, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 15px;">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <div>
                                    <a href="{{ route('branches.branches.show', $b->id) }}" style="font-weight: 800; color: var(--text-main); text-decoration: none; font-size: 14px;">
                                        {{ $b->name }}
                                    </a>
                                    @if($b->is_main)
                                        <span style="background: #FEF3C7; color: #D97706; font-size: 10.5px; font-weight: 800; padding: 2px 6px; border-radius: 6px; margin-right: 4px;">رئيسي HQ</span>
                                    @endif
                                    <small style="color: var(--text-muted); display: block; font-family: monospace;">كود: {{ $b->code }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <strong style="color: var(--text-main); display: block;">{{ $b->city ?: 'المملكة' }}</strong>
                            <small style="color: var(--text-muted);">
                                @if($b->branch_type === 'hq') المقر الرئيسي
                                @elseif($b->branch_type === 'retail') معرض تجزئة
                                @elseif($b->branch_type === 'wholesale') مركز جملة
                                @elseif($b->branch_type === 'distribution_center') مركز توزيع إقليمي
                                @endif
                            </small>
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="font-weight: 800; color: var(--text-main);">{{ $st['stores_count'] }}</span>
                            <span style="color: var(--text-muted); font-size: 12px;">مستودع</span>
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="font-weight: 800; color: var(--text-main);">{{ $st['employees_count'] }}</span>
                            <span style="color: var(--text-muted); font-size: 12px;">موظف</span>
                        </td>
                        <td style="padding: 14px 16px;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <strong style="color: #0F5132; font-size: 14px;">{{ number_format($st['monthly_sales'], 2) }} ر.س</strong>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="background: #E2E8F0; border-radius: 4px; height: 6px; width: 60px; overflow: hidden;">
                                        <div style="background: #10B981; height: 100%; width: {{ min(100, $salesShare) }}%;"></div>
                                    </div>
                                    <span style="font-size: 11px; color: var(--text-muted); font-weight: 700;">{{ number_format($salesShare, 1) }}%</span>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <strong style="color: var(--primary); font-size: 14px;">{{ number_format($st['inventory_value'], 2) }} ر.س</strong>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="background: #E2E8F0; border-radius: 4px; height: 6px; width: 60px; overflow: hidden;">
                                        <div style="background: var(--primary); height: 100%; width: {{ min(100, $invShare) }}%;"></div>
                                    </div>
                                    <span style="font-size: 11px; color: var(--text-muted); font-weight: 700;">{{ number_format($invShare, 1) }}%</span>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 14px 16px;">
                            <strong style="color: #D97706; font-size: 14px;">{{ number_format($st['treasury_bal'], 2) }} ر.س</strong>
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            <a href="{{ route('branches.branches.show', $b->id) }}" title="عرض الملف المتكامل" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 6px 12px; border-radius: 8px; text-decoration: none; color: var(--primary); font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                تفاصيل الفرع
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background: #F8FAFC; border-top: 2px solid var(--border-color); font-weight: 900;">
                    <tr>
                        <td colspan="2" style="padding: 14px 16px; font-size: 14px; color: var(--text-main);">الإجمالي المجمّع للمؤسسة:</td>
                        <td style="padding: 14px 16px; color: var(--text-main);">{{ $consolidated['total_stores'] }} مستودع</td>
                        <td style="padding: 14px 16px; color: var(--text-main);">{{ $consolidated['total_employees'] }} موظف</td>
                        <td style="padding: 14px 16px; color: #0F5132; font-size: 14.5px;">{{ number_format($consolidated['total_sales_month'], 2) }} ر.س</td>
                        <td style="padding: 14px 16px; color: var(--primary); font-size: 14.5px;">{{ number_format($consolidated['total_inventory_val'], 2) }} ر.س</td>
                        <td style="padding: 14px 16px; color: #D97706; font-size: 14.5px;">{{ number_format($consolidated['total_treasury_bal'], 2) }} ر.س</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
