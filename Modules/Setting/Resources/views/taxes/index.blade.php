@extends('layouts.app')

@section('title', 'دليل الضرائب وضريبة القيمة المضافة')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Breadcrumb & Actions Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-percent" style="color: var(--primary); margin-left: 8px;"></i>
                إدارة الضرائب وضريبة القيمة المضافة (VAT)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد نسب الضرائب المطبقة على فواتير المبيعات والمشتريات
            </span>
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('settings.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة للإعدادات العامة
            </a>
            <button type="button" onclick="document.getElementById('taxModal').style.display='flex'" class="btn-primary-custom">
                <i class="fa-solid fa-plus"></i>
                إضافة ضريبة جديدة
            </button>
        </div>
    </div>

    <!-- Taxes Table Card -->
    <div class="erp-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 14px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                        <th style="padding: 14px 18px;">اسم الضريبة</th>
                        <th style="padding: 14px 18px;">النسبة / القيمة</th>
                        <th style="padding: 14px 18px;">النوع</th>
                        <th style="padding: 14px 18px;">نطاق التطبيق</th>
                        <th style="padding: 14px 18px;">الرمز التعريفي</th>
                        <th style="padding: 14px 18px;">الحالة</th>
                        <th style="padding: 14px 18px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($taxes as $tax)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 18px; font-weight: 700; color: var(--text-main);">
                                {{ $tax->name }}
                                @if($tax->is_default)
                                    <span style="background: var(--primary-light); color: var(--primary); font-size: 11px; padding: 2px 8px; border-radius: 12px; margin-right: 6px;">
                                        الافتراضية
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px; font-weight: 800; color: var(--primary); font-size: 15px;">
                                {{ $tax->rate }}%
                            </td>
                            <td style="padding: 14px 18px;">
                                {{ $tax->type === 'percentage' ? 'نسبة مئوية (%)' : 'مبلغ ثابت' }}
                            </td>
                            <td style="padding: 14px 18px;">
                                @if($tax->applies_to === 'all')
                                    <span style="background: #E0E7FF; color: #4338CA; padding: 3px 8px; border-radius: 8px; font-size: 11.5px; font-weight: 700;">المبيعات والمشتريات</span>
                                @elseif($tax->applies_to === 'sales')
                                    <span style="background: #D1E7DD; color: #0F5132; padding: 3px 8px; border-radius: 8px; font-size: 11.5px; font-weight: 700;">المبيعات فقط</span>
                                @else
                                    <span style="background: #FEF3C7; color: #92400E; padding: 3px 8px; border-radius: 8px; font-size: 11.5px; font-weight: 700;">المشتريات فقط</span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px; color: var(--text-muted); font-family: monospace;">
                                {{ $tax->code ?? '-' }}
                            </td>
                            <td style="padding: 14px 18px;">
                                <span style="background: {{ $tax->is_active ? '#D1E7DD' : '#FEE2E2' }}; color: {{ $tax->is_active ? '#0F5132' : '#991B1B' }}; padding: 3px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                    {{ $tax->is_active ? 'مفعل' : 'معطل' }}
                                </span>
                            </td>
                            <td style="padding: 14px 18px;">
                                <form action="{{ route('settings.taxes.destroy', $tax->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الضريبة؟')" style="margin: 0; display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #FEE2E2; border: none; color: #DC2626; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 32px; text-align: center; color: var(--text-muted);">
                                لا توجد ضرائب مسجلة حتى الآن. انقر على "إضافة ضريبة جديدة" للبدء.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Create Tax Modal -->
<div id="taxModal" class="theme-modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 800; font-size: 18px; color: var(--text-main);">
                <i class="fa-solid fa-percent" style="color: var(--primary); margin-left: 8px;"></i>
                إضافة ضريبة جديدة
            </h3>
            <button type="button" onclick="document.getElementById('taxModal').style.display='none'" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ route('settings.taxes.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اسم الضريبة *</label>
                <input type="text" name="name" required placeholder="مثال: ضريبة القيمة المضافة القياسية 15%" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">نسبة الضريبة (%) *</label>
                    <input type="number" step="0.01" name="rate" required value="15.00" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">كود الضريبة (ZATCA Code)</label>
                    <input type="text" name="code" placeholder="VAT-15" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">نوع الضريبة</label>
                    <select name="type" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                        <option value="percentage">نسبة مئوية (%)</option>
                        <option value="fixed">مبلغ ثابت</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">تطبق على</label>
                    <select name="applies_to" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                        <option value="all">المبيعات والمشتريات معاً</option>
                        <option value="sales">المبيعات فقط</option>
                        <option value="purchases">المشتريات فقط</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                <input type="checkbox" name="is_default" id="is_default" value="1" style="accent-color: var(--primary); width: 16px; height: 16px;">
                <label for="is_default" style="font-weight: 700; font-size: 13.5px; cursor: pointer;">تعيين كضريبة افتراضية لجميع الفواتير الجديدة</label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('taxModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">إلغاء</button>
                <button type="submit" class="btn-primary-custom">إضافة الضريبة</button>
            </div>
        </form>
    </div>
</div>
@endsection

