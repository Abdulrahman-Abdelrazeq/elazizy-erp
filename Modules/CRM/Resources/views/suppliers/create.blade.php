@extends('layouts.app')

@section('title', 'إضافة مورد جديد')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1000px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-truck-field" style="color: var(--primary); margin-left: 8px;"></i>
                إضافة مورد جديد
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تسجيل بيانات المورد الجديد، الرصيد الافتتاحي، والبيانات الضريبية
            </span>
        </div>

        <a href="{{ route('crm.suppliers.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لقائمة الموردين
        </a>
    </div>

    @if ($errors->any())
        <div style="background: #FEE2E2; border-right: 4px solid #DC2626; padding: 14px 18px; border-radius: 10px; color: #991B1B; font-size: 13.5px;">
            <strong style="display: block; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> يرجى تصحيح الأخطاء التالية:</strong>
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('crm.suppliers.store') }}" method="POST">
        @csrf

        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Basic Info Card -->
            <div class="erp-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-building"></i> البيانات الأساسية والتواصل
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            اسم المورد / المسؤول <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: شركة الأمل للتوريدات أو م. أحمد رشاد" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            اسم الشركة / المنشأة
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="الاسم التجاري للمورد" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            كود المورد (تلقائي إن ترك فارغاً)
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="SUP-1001" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            رقم الهاتف الأرضي
                        </label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="0224567890" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            رقم الموبايل / واتساب
                        </label>
                        <input type="text" name="mobile" value="{{ old('mobile') }}" placeholder="01098765432" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            البريد الإلكتروني
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="supplier@domain.com" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            المدينة / المنطقة
                        </label>
                        <select name="location_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                            <option value="">-- اختر المنطقة أو المدينة --</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('location_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            الرقم الضريبي للمورد
                        </label>
                        <input type="text" name="tax_number" value="{{ old('tax_number') }}" placeholder="الرقم الضريبي" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        العنوان ومقر الشركة
                    </label>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="الشارع، المنطقة، رقم المبنى..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
            </div>

            <!-- Financial & Opening Balance Card -->
            <div class="erp-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-coins"></i> الرصيد الافتتاحي والبيانات المالية
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            الرصيد الافتتاحي
                        </label>
                        <input type="number" step="0.01" min="0" name="opening_balance" value="{{ old('opening_balance', '0.00') }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            طبيعة الرصيد الافتتاحي <span style="color: #DC2626;">*</span>
                        </label>
                        <select name="opening_balance_type" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                            <option value="credit" {{ old('opening_balance_type') == 'credit' ? 'selected' : '' }}>دائن (مستحق للمورد علينا)</option>
                            <option value="debit" {{ old('opening_balance_type') == 'debit' ? 'selected' : '' }}>مدين (دفعات مقدمة للمورد لنا)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            رقم السجل التجاري
                        </label>
                        <input type="text" name="commercial_register" value="{{ old('commercial_register') }}" placeholder="السجل التجاري" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        ملاحظات أو شروط السداد والتعاقد
                    </label>
                    <textarea name="notes" rows="3" placeholder="طريقة الدفع المتفق عليها، مواعيد التوريد..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('crm.suppliers.index') }}" style="background: #E2E8F0; padding: 12px 24px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 14px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; font-size: 15px;">
                    <i class="fa-solid fa-save"></i>
                    حفظ المورد الجديد
                </button>
            </div>

        </div>
    </form>
</div>
@endsection
