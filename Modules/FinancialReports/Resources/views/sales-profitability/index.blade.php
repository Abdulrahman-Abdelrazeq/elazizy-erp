@extends('layouts.app')

@section('title', 'تحليل المبيعات وربحية الأصناف والعملاء (Sales & Profitability)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-chart-column"></i>
                </span>
                تحليل المبيعات وربحية الأصناف والعملاء (Sales Profitability Analysis)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تتبع كفاءة التسعير، هوامش الربح المحققة على مستوى المنتجات، والعملاء الأكثر مساهمة في نمو الأرباح
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('reports.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                مركز التقارير
            </a>
            <a href="{{ route('invoices.sales.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary);"></i>
                فواتير المبيعات
            </a>
            <button onclick="window.print()" style="background: var(--primary); color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة تقرير الربحية
            </button>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('reports.sales_profitability') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="from_date" class="erp-input" value="{{ $fromDate }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" class="erp-input" value="{{ $toDate }}">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 20px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تحديث التحليل
                </button>
                <a href="{{ route('reports.sales_profitability') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المبيعات المحققة</span>
            <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ formatMoney($data['total_sales']) }}</strong>
            <span style="font-size: 11px; color: var(--text-muted); display: block;">فواتير مدفوعة ومعتمدة</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تكلفة البضاعة المقدرة (COGS)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #DC2626;">{{ formatMoney($data['total_cost']) }}</strong>
            <span style="font-size: 11px; color: #DC2626; display: block;">سعر شراء وتكلفة المخزون</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مجمل الربح التجاري (Gross Profit)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ formatMoney($data['gross_profit']) }}</strong>
            <span style="font-size: 11px; color: #10B981; display: block;">المبيعات - التكلفة</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">متوسط هامش الربح التجاري</span>
            <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $data['profit_margin'] }}%</strong>
            <span style="font-size: 11px; color: #D97706; display: block;">نسبة الربح من المبيعات</span>
        </div>
    </div>

    <!-- Dual Top Tables Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 20px;">

        <!-- Top Products -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 900; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-trophy" style="color: var(--accent-gold);"></i>
                    الأصناف الأكثر ربحية (Top Products)
                </h3>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700;">أعلى مساهمة في الأرباح</span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; font-weight: 800; color: var(--text-muted);">
                            <th style="padding: 10px; text-align: right;">الصنف</th>
                            <th style="padding: 10px; text-align: center; width: 60px;">الكمية</th>
                            <th style="padding: 10px; text-align: right; width: 110px;">المبيعات</th>
                            <th style="padding: 10px; text-align: right; width: 110px;">الربح المحقق</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['top_products'] as $prod)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 10px; font-weight: 700; color: var(--text-main);">{{ $prod['name'] }}</td>
                            <td style="padding: 10px; text-align: center; font-weight: 800; color: var(--text-muted);">{{ $prod['qty'] }}</td>
                            <td style="padding: 10px; text-align: right; font-weight: 700;">{{ formatMoney($prod['sales']) }}</td>
                            <td style="padding: 10px; text-align: right; font-weight: 900; color: #0F5132;">{{ formatMoney($prod['profit']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: var(--text-muted);">لا توجد حركات بيع للأصناف خلال الفترة</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Clients -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 900; color: #4338CA; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-crown" style="color: var(--accent-gold);"></i>
                    العملاء الأكثر تحقيقاً للعوائد (Top Clients)
                </h3>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700;">أعلى ربحية صافية</span>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; font-weight: 800; color: var(--text-muted);">
                            <th style="padding: 10px; text-align: right;">العميل</th>
                            <th style="padding: 10px; text-align: center; width: 60px;">الفواتير</th>
                            <th style="padding: 10px; text-align: right; width: 110px;">المشتريات</th>
                            <th style="padding: 10px; text-align: right; width: 110px;">الربح الصافي</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['top_clients'] as $client)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 10px; font-weight: 700; color: var(--text-main);">{{ $client['name'] }}</td>
                            <td style="padding: 10px; text-align: center; font-weight: 800; color: var(--text-muted);">{{ $client['orders_count'] }}</td>
                            <td style="padding: 10px; text-align: right; font-weight: 700;">{{ formatMoney($client['sales']) }}</td>
                            <td style="padding: 10px; text-align: right; font-weight: 900; color: #0F5132;">{{ formatMoney($client['profit']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: var(--text-muted);">لا توجد مبيعات مسجلة لعملاء خلال الفترة</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

