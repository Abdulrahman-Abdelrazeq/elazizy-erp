@extends('layouts.app')

@section('title', 'قائمة المركز المالي والميزانية العمومية (Balance Sheet)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-scale-balanced"></i>
                </span>
                قائمة المركز المالي والميزانية العمومية (Balance Sheet)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                استعراض أصول المنشأة والتزاماتها وحقوق الملكية وفق المعادلة المحاسبية الذهبية (الأصول = الالتزامات + حقوق الملكية)
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('reports.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                مركز التقارير
            </a>
            <a href="{{ route('reports.income_statement') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary);"></i>
                قائمة الدخل
            </a>
            <a href="{{ route('reports.balance_sheet.print', ['as_of_date' => $asOfDate]) }}" target="_blank" style="background: #4338CA; color: #fff; padding: 9px 18px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة الميزانية
            </a>
        </div>
    </div>

    <!-- Golden Equation & Balance Banner -->
    @if ($data['is_balanced'])
        <div style="background: #D1E7DD; border: 1.5px solid #A3CFBB; color: #0F5132; padding: 16px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-circle-check" style="font-size: 26px; color: #0F5132;"></i>
                <div>
                    <strong style="font-size: 15px; display: block;">الميزانية العمومية متوازنة تماماً بنسبة 100%</strong>
                    <span style="font-size: 12.5px; opacity: 0.95;">إجمالي الأصول ({{ formatMoney($data['total_assets']) }}) = إجمالي الالتزامات وحقوق الملكية ({{ formatMoney($data['total_liabilities_and_equity']) }})</span>
                </div>
            </div>
            <span style="background: #0F5132; color: #fff; padding: 6px 14px; border-radius: 20px; font-weight: 800; font-size: 12px;">
                <i class="fa-solid fa-shield-check"></i> مدققة ومعتمدة
            </span>
        </div>
    @else
        <div style="background: #FEE2E2; border: 1.5px solid #F87171; color: #991B1B; padding: 16px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 26px; color: #DC2626;"></i>
                <div>
                    <strong style="font-size: 15px; display: block;">تنبيه محاسبي: الميزانية غير متوازنة!</strong>
                    <span style="font-size: 12.5px; opacity: 0.95;">يوجد فارق بمقدار: {{ formatMoney($data['diff']) }}. يرجى مراجعة الأرصدة الافتتاحية أو القيود غير المعتمدة.</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('reports.balance_sheet') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">كما في تاريخ (As of Date)</label>
                <input type="date" name="as_of_date" class="erp-input" value="{{ $asOfDate }}">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 20px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تحديث الميزانية
                </button>
                <a href="{{ route('reports.balance_sheet') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    اليوم
                </a>
            </div>
        </form>
    </div>

    <!-- Dual Balance Sheet Columns Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 20px;">

        <!-- RIGHT COLUMN: ASSETS (الأصول) -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; border-top: 4px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 12px; margin-bottom: 18px;">
                <h3 style="font-size: 17px; font-weight: 900; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-building-columns"></i>
                    جانب الأصول (Assets)
                </h3>
                <span style="font-size: 12px; font-weight: 800; color: var(--text-muted);">مدين (Debit)</span>
            </div>

            <!-- Current Assets -->
            <div style="margin-bottom: 20px;">
                <h4 style="font-size: 14.5px; font-weight: 800; color: var(--text-main); margin-bottom: 10px; background: #F8FAFC; padding: 8px 12px; border-radius: 8px; border-right: 3px solid var(--primary);">
                    1. الأصول المتداولة (Current Assets)
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tbody>
                        @forelse($data['current_assets'] as $item)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 8px 12px; color: var(--text-muted); font-family: monospace; width: 100px;">{{ $item['account']->code }}</td>
                            <td style="padding: 8px 12px; font-weight: 700; color: var(--text-main);">{{ $item['account']->name }}</td>
                            <td style="padding: 8px 12px; text-align: right; font-weight: 700; color: #0F5132;">{{ formatMoney($item['balance']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding: 10px; text-align: center; color: var(--text-muted); font-size: 12px;">لا توجد أصول متداولة</td>
                        </tr>
                        @endforelse
                        <tr style="background: #F1F5F9; font-weight: 800;">
                            <td colspan="2" style="padding: 10px 12px; color: var(--text-main);">مجموع الأصول المتداولة:</td>
                            <td style="padding: 10px 12px; text-align: right; color: #0F5132;">{{ formatMoney($data['total_current_assets']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Fixed / Non-Current Assets -->
            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 14.5px; font-weight: 800; color: var(--text-main); margin-bottom: 10px; background: #F8FAFC; padding: 8px 12px; border-radius: 8px; border-right: 3px solid #10B981;">
                    2. الأصول الثابتة وغير المتداولة (Fixed & Non-Current Assets)
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tbody>
                        @forelse($data['fixed_assets'] as $item)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 8px 12px; color: var(--text-muted); font-family: monospace; width: 100px;">{{ $item['account']->code }}</td>
                            <td style="padding: 8px 12px; font-weight: 700; color: var(--text-main);">{{ $item['account']->name }}</td>
                            <td style="padding: 8px 12px; text-align: right; font-weight: 700; color: #0F5132;">{{ formatMoney($item['balance']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding: 10px; text-align: center; color: var(--text-muted); font-size: 12px;">لا توجد أصول ثابتة مقيدة</td>
                        </tr>
                        @endforelse
                        <tr style="background: #F1F5F9; font-weight: 800;">
                            <td colspan="2" style="padding: 10px 12px; color: var(--text-main);">مجموع الأصول الثابتة:</td>
                            <td style="padding: 10px 12px; text-align: right; color: #0F5132;">{{ formatMoney($data['total_fixed_assets']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total Assets Footer -->
            <div style="background: #0F5132; color: #fff; padding: 14px 18px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 900; font-size: 16px;">
                <span>إجمالي الأصول (Total Assets):</span>
                <span>{{ formatMoney($data['total_assets']) }}</span>
            </div>
        </div>

        <!-- LEFT COLUMN: LIABILITIES & EQUITY (الالتزامات وحقوق الملكية) -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; border-top: 4px solid #4338CA;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 12px; margin-bottom: 18px;">
                <h3 style="font-size: 17px; font-weight: 900; color: #4338CA; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-hand-holding-hand"></i>
                    الالتزامات وحقوق الملكية (Liabilities & Equity)
                </h3>
                <span style="font-size: 12px; font-weight: 800; color: var(--text-muted);">دائن (Credit)</span>
            </div>

            <!-- Current Liabilities -->
            <div style="margin-bottom: 20px;">
                <h4 style="font-size: 14.5px; font-weight: 800; color: var(--text-main); margin-bottom: 10px; background: #F8FAFC; padding: 8px 12px; border-radius: 8px; border-right: 3px solid #DC2626;">
                    1. الالتزامات المتداولة قصيرة الأجل (Current Liabilities)
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tbody>
                        @forelse($data['current_liabilities'] as $item)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 8px 12px; color: var(--text-muted); font-family: monospace; width: 100px;">{{ $item['account']->code }}</td>
                            <td style="padding: 8px 12px; font-weight: 700; color: var(--text-main);">{{ $item['account']->name }}</td>
                            <td style="padding: 8px 12px; text-align: right; font-weight: 700; color: #DC2626;">{{ formatMoney($item['balance']) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding: 10px; text-align: center; color: var(--text-muted); font-size: 12px;">لا توجد التزامات متداولة</td>
                        </tr>
                        @endforelse
                        <tr style="background: #F1F5F9; font-weight: 800;">
                            <td colspan="2" style="padding: 10px 12px; color: var(--text-main);">مجموع الالتزامات المتداولة:</td>
                            <td style="padding: 10px 12px; text-align: right; color: #DC2626;">{{ formatMoney($data['total_current_liabilities']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Equity -->
            <div style="margin-bottom: 24px;">
                <h4 style="font-size: 14.5px; font-weight: 800; color: var(--text-main); margin-bottom: 10px; background: #F8FAFC; padding: 8px 12px; border-radius: 8px; border-right: 3px solid #D97706;">
                    2. حقوق الملكية ورأس المال (Owner's Equity)
                </h4>
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <tbody>
                        @foreach($data['equity_accounts'] as $item)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 8px 12px; color: var(--text-muted); font-family: monospace; width: 100px;">{{ $item['account']->code }}</td>
                            <td style="padding: 8px 12px; font-weight: 700; color: var(--text-main);">{{ $item['account']->name }}</td>
                            <td style="padding: 8px 12px; text-align: right; font-weight: 700; color: #4338CA;">{{ formatMoney($item['balance']) }}</td>
                        </tr>
                        @endforeach
                        <!-- Net Period Profit row -->
                        <tr style="border-bottom: 1px solid var(--border-color); background: rgba(15, 81, 50, 0.05);">
                            <td style="padding: 8px 12px; color: var(--text-muted); font-family: monospace;">-</td>
                            <td style="padding: 8px 12px; font-weight: 800; color: #0F5132;">صافي أرباح الفترة الحالية (من قائمة الدخل)</td>
                            <td style="padding: 8px 12px; text-align: right; font-weight: 800; color: #0F5132;">{{ formatMoney($data['period_net_profit']) }}</td>
                        </tr>
                        <tr style="background: #E0E7FF; font-weight: 800;">
                            <td colspan="2" style="padding: 10px 12px; color: #3730A3;">مجموع حقوق الملكية:</td>
                            <td style="padding: 10px 12px; text-align: right; color: #3730A3;">{{ formatMoney($data['total_equity']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total Liabilities & Equity Footer -->
            <div style="background: #4338CA; color: #fff; padding: 14px 18px; border-radius: 10px; display: flex; justify-content: space-between; align-items: center; font-weight: 900; font-size: 16px;">
                <span>إجمالي الالتزامات وحقوق الملكية:</span>
                <span>{{ formatMoney($data['total_liabilities_and_equity']) }}</span>
            </div>
        </div>

    </div>

</div>
@endsection

