@extends('layouts.app')

@section('title', 'دليل العملات وأسعار الصرف')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Breadcrumb & Actions Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-coins" style="color: var(--primary); margin-left: 8px;"></i>
                إدارة العملات وأسعار الصرف
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد العملة الأساسية للنظام والعملات الأجنبية مع أسعار تحويلها
            </span>
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('settings.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة للإعدادات
            </a>
            <button type="button" onclick="document.getElementById('currencyModal').style.display='flex'" class="btn-primary-custom">
                <i class="fa-solid fa-plus"></i>
                إضافة عملة جديدة
            </button>
        </div>
    </div>

    <!-- Currencies Table Card -->
    <div class="erp-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 14px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                        <th style="padding: 14px 18px;">اسم العملة</th>
                        <th style="padding: 14px 18px;">الكود الدولي (ISO)</th>
                        <th style="padding: 14px 18px;">الرمز</th>
                        <th style="padding: 14px 18px;">سعر الصرف مقابل الأساس</th>
                        <th style="padding: 14px 18px;">اسم الجزء (الكسور)</th>
                        <th style="padding: 14px 18px;">الحالة</th>
                        <th style="padding: 14px 18px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($currencies as $curr)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 18px; font-weight: 700; color: var(--text-main);">
                                {{ $curr->name }}
                                @if($curr->is_default)
                                    <span style="background: var(--primary-light); color: var(--primary); font-size: 11px; padding: 2px 8px; border-radius: 12px; margin-right: 6px;">
                                        العملة الأساسية للنظام
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 14px 18px; font-weight: 700; color: #4338CA; font-family: monospace;">
                                {{ $curr->code }}
                            </td>
                            <td style="padding: 14px 18px; font-weight: 800; font-size: 15px;">
                                {{ $curr->symbol }}
                            </td>
                            <td style="padding: 14px 18px; font-weight: 700; color: var(--primary);">
                                {{ number_format($curr->exchange_rate, 4) }}
                            </td>
                            <td style="padding: 14px 18px; color: var(--text-muted);">
                                {{ $curr->subunit ?? '-' }}
                            </td>
                            <td style="padding: 14px 18px;">
                                <span style="background: {{ $curr->is_active ? '#D1E7DD' : '#FEE2E2' }}; color: {{ $curr->is_active ? '#0F5132' : '#991B1B' }}; padding: 3px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                    {{ $curr->is_active ? 'مفعل' : 'معطل' }}
                                </span>
                            </td>
                            <td style="padding: 14px 18px;">
                                @if(!$curr->is_default)
                                    <form action="{{ route('settings.currencies.destroy', $curr->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه العملة؟')" style="margin: 0; display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: #FEE2E2; border: none; color: #DC2626; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">أساسية</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 32px; text-align: center; color: var(--text-muted);">
                                لا توجد عملات مسجلة. أضف العملة الأولى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Create Currency Modal -->
<div id="currencyModal" class="theme-modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 800; font-size: 18px; color: var(--text-main);">
                <i class="fa-solid fa-coins" style="color: var(--primary); margin-left: 8px;"></i>
                إضافة عملة جديدة
            </h3>
            <button type="button" onclick="document.getElementById('currencyModal').style.display='none'" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ route('settings.currencies.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اسم العملة *</label>
                <input type="text" name="name" required placeholder="مثال: ريال سعودي" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">الكود الدولي (ISO) *</label>
                    <input type="text" name="code" required placeholder="SAR" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; text-transform: uppercase;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">الرمز المختصر *</label>
                    <input type="text" name="symbol" required placeholder="ر.س" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">سعر الصرف *</label>
                    <input type="number" step="0.0001" name="exchange_rate" required value="1.0000" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اسم الجزء (الكسور)</label>
                    <input type="text" name="subunit" placeholder="هللة / قرش" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                <input type="checkbox" name="is_default" id="curr_default" value="1" style="accent-color: var(--primary); width: 16px; height: 16px;">
                <label for="curr_default" style="font-weight: 700; font-size: 13.5px; cursor: pointer;">تعيين كعملة رئيسية للنظام (Base Currency)</label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('currencyModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">إلغاء</button>
                <button type="submit" class="btn-primary-custom">إضافة العملة</button>
            </div>
        </form>
    </div>
</div>
@endsection

