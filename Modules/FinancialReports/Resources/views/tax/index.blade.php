@extends('layouts.app')

@section('title', 'تقرير ضريبة القيمة المضافة والإقرار الضريبي (VAT Declaration)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #DC2626; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-receipt"></i>
                </span>
                تقرير ضريبة القيمة المضافة والإقرار الضريبي (VAT Declaration)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                احتساب ضريبة مخرجات المبيعات وضريبة مدخلات المشتريات وتحديد صافي الضريبة المستحقة للسداد أو الاسترداد
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('reports.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                مركز التقارير
            </a>
            <a href="{{ route('reports.tax.print', ['from_date' => $fromDate, 'to_date' => $toDate]) }}" target="_blank" style="background: #DC2626; color: #fff; padding: 9px 18px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة نموذج الإقرار الضريبي
            </a>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('reports.tax') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الفترة الضريبية من</label>
                <input type="date" name="from_date" class="erp-input" value="{{ $fromDate }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" class="erp-input" value="{{ $toDate }}">
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 20px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تحديث الإقرار
                </button>
                <a href="{{ route('reports.tax') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إعادة ضبط
                </a>
            </div>
        </form>
    </div>

    <!-- Net Tax Status Banner -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0; background: {{ $data['is_payable'] ? 'rgba(220, 38, 38, 0.05)' : 'rgba(15, 81, 50, 0.05)' }}; border: 2px solid {{ $data['is_payable'] ? '#FCA5A5' : '#A3CFBB' }};">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 52px; height: 52px; border-radius: 14px; background: {{ $data['is_payable'] ? '#FEE2E2' : '#D1E7DD' }}; color: {{ $data['is_payable'] ? '#DC2626' : '#0F5132' }}; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                    <i class="fa-solid {{ $data['is_payable'] ? 'fa-file-invoice-dollar' : 'fa-circle-dollar-to-slot' }}"></i>
                </div>
                <div>
                    <span style="font-size: 12.5px; font-weight: 700; color: var(--text-muted); display: block;">
                        {{ $data['is_payable'] ? 'صافي ضريبة القيمة المضافة المستحقة للسداد (VAT Payable)' : 'رصيد ضريبة دائن مستحق للاسترداد / الترحيل (VAT Refundable)' }}
                    </span>
                    <strong style="font-size: 26px; font-weight: 900; color: {{ $data['is_payable'] ? '#DC2626' : '#0F5132' }};">
                        {{ formatMoney(abs($data['net_vat_payable'])) }}
                    </strong>
                </div>
            </div>

            <div style="text-align: left;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">الضريبة المحصلة - الضريبة المدفوعة:</span>
                <span style="font-size: 14px; font-weight: 800; color: var(--text-main);">
                    {{ formatMoney($data['net_output_vat']) }} - {{ formatMoney($data['net_input_vat']) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Dual VAT Breakdown Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px;">

        <!-- 1. OUTPUT VAT (المبيعات) -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; border-top: 4px solid var(--primary);">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 900; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-up-right-dots"></i>
                    ضريبة مخرجات المبيعات (Output VAT)
                </h3>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">محصلة من العملاء</span>
            </div>

            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-main); font-weight: 700;">إجمالي المبيعات الخاضعة للضريبة:</td>
                        <td style="padding: 10px; text-align: right; font-weight: 800;">{{ formatMoney($data['sales_taxable']) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-main); font-weight: 700;">ضريبة المبيعات المحصلة (+):</td>
                        <td style="padding: 10px; text-align: right; font-weight: 800; color: #0F5132;">{{ formatMoney($data['output_vat']) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">يخصم: ضريبة مرتجعات المبيعات (-):</td>
                        <td style="padding: 10px; text-align: right; color: #DC2626; font-weight: 700;">({{ formatMoney($data['sales_return_vat']) }})</td>
                    </tr>
                    <tr style="background: #D1E7DD; font-weight: 900;">
                        <td style="padding: 12px; color: #0F5132;">صافي ضريبة المخرجات:</td>
                        <td style="padding: 12px; text-align: right; color: #0F5132; font-size: 15px;">{{ formatMoney($data['net_output_vat']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 2. INPUT VAT (المشتريات) -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0; border-top: 4px solid #4338CA;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 12px; margin-bottom: 16px;">
                <h3 style="font-size: 16px; font-weight: 900; color: #4338CA; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-arrow-down-left-dots"></i>
                    ضريبة مدخلات المشتريات (Input VAT)
                </h3>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">مدفوعة للموردين</span>
            </div>

            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-main); font-weight: 700;">إجمالي المشتريات الخاضعة للضريبة:</td>
                        <td style="padding: 10px; text-align: right; font-weight: 800;">{{ formatMoney($data['purchases_taxable']) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-main); font-weight: 700;">ضريبة المشتريات المدفوعة (+):</td>
                        <td style="padding: 10px; text-align: right; font-weight: 800; color: #4338CA;">{{ formatMoney($data['input_vat']) }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 10px; color: var(--text-muted);">يخصم: ضريبة مرتجعات المشتريات (-):</td>
                        <td style="padding: 10px; text-align: right; color: #DC2626; font-weight: 700;">({{ formatMoney($data['purchase_return_vat']) }})</td>
                    </tr>
                    <tr style="background: #E0E7FF; font-weight: 900;">
                        <td style="padding: 12px; color: #3730A3;">صافي ضريبة المدخلات القابلة للخصم:</td>
                        <td style="padding: 12px; text-align: right; color: #3730A3; font-size: 15px;">{{ formatMoney($data['net_input_vat']) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
