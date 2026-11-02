@extends('layouts.app')

@section('title', 'تعديل بيانات العميل: ' . $customer->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1000px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-user-pen" style="color: var(--primary); margin-left: 8px;"></i>
                تعديل بيانات العميل: {{ $customer->name }}
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديث بيانات التواصل، الحدود الائتمانية، أو تغيير شريحة التسعير
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('crm.customers.statement', $customer->id) }}" style="background: var(--primary-light); color: var(--primary); border: 1px solid var(--primary); padding: 9px 18px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice"></i>
                كشف الحساب
            </a>
            <a href="{{ route('crm.customers.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة لقائمة العملاء
            </a>
        </div>
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

    <form action="{{ route('crm.customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: flex; flex-direction: column; gap: 20px;">

            <!-- Basic Info Card -->
            <div class="erp-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-id-card"></i> البيانات الأساسية والتواصل
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            اسم العميل الكامل <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            اسم المنشأة / الاسم التجاري
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name', $customer->company_name) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            كود العميل
                        </label>
                        <input type="text" name="code" value="{{ old('code', $customer->code) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            شريحة تسعير العميل
                        </label>
                        <select name="group_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                            <option value="">-- عام (بدون شريحة محددة) --</option>
                            @foreach($groups as $grp)
                                <option value="{{ $grp->id }}" {{ old('group_id', $customer->group_id) == $grp->id ? 'selected' : '' }}>
                                    {{ $grp->name }} ({{ $grp->price_tier == 'wholesale' ? 'جملة' : ($grp->price_tier == 'half_wholesale' ? 'نصف جملة' : 'تجزئة') }} - خصم {{ $grp->discount_percentage }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            رقم الهاتف الأرضي
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            رقم الموبايل / واتساب
                        </label>
                        <input type="text" name="mobile" value="{{ old('mobile', $customer->mobile) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            البريد الإلكتروني
                        </label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            المدينة / المنطقة
                        </label>
                        <select name="location_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                            <option value="">-- اختر المنطقة أو المدينة --</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('location_id', $customer->location_id) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        العنوان التفصيلي
                    </label>
                    <input type="text" name="address" value="{{ old('address', $customer->address) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
            </div>

            <!-- Tax & Legal Info Card -->
            <div class="erp-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-stamp"></i> البيانات الضريبية والقانونية
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            الرقم الضريبي (Tax ID)
                        </label>
                        <input type="text" name="tax_number" value="{{ old('tax_number', $customer->tax_number) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            السجل التجاري
                        </label>
                        <input type="text" name="commercial_register" value="{{ old('commercial_register', $customer->commercial_register) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            الرقم القومي (للأفراد)
                        </label>
                        <input type="text" name="national_id" value="{{ old('national_id', $customer->national_id) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>
            </div>

            <!-- Financial & Credit Limits Card -->
            <div class="erp-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-wallet"></i> الحدود الائتمانية والرصيد الحالي
                </h3>

                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                    <div>
                        <span style="font-size: 12px; color: var(--text-muted); font-weight: 700;">الرصيد المالي الحالي للعميل:</span>
                        <div style="font-size: 20px; font-weight: 900; margin-top: 4px; color: {{ $customer->current_balance > 0 ? '#DC2626' : ($customer->current_balance < 0 ? '#0F5132' : '#64748B') }};">
                            {{ formatMoney(abs($customer->current_balance)) }}
                            <small style="font-size: 12px; font-weight: 700;">
                                @if($customer->current_balance > 0)
                                    (مدين - مستحق عليه)
                                @elseif($customer->current_balance < 0)
                                    (دائن - رصيد له)
                                @else
                                    (خالص)
                                @endif
                            </small>
                        </div>
                    </div>

                    <a href="{{ route('crm.adjustments.index') }}?client_id={{ $customer->id }}" class="btn-primary-custom" style="padding: 8px 16px; font-size: 12.5px;">
                        <i class="fa-solid fa-scale-balanced"></i> تسوية أو شطب رصيد
                    </a>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            الحد الائتماني المسموح به
                        </label>
                        <input type="number" step="0.01" min="0" name="credit_limit" value="{{ old('credit_limit', $customer->credit_limit) }}" placeholder="0.00 = بلا حد" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            أيام فترة السماح للسداد (يوم)
                        </label>
                        <input type="number" min="0" name="credit_days" value="{{ old('credit_days', $customer->credit_days) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            حالة الحساب
                        </label>
                        <select name="is_active" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                            <option value="1" {{ old('is_active', $customer->is_active) ? 'selected' : '' }}>نشط (مسموح بالتعامل)</option>
                            <option value="0" {{ !old('is_active', $customer->is_active) ? 'selected' : '' }}>موقف (تجميد التعامل مؤقتاً)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        ملاحظات إضافية عن العميل
                    </label>
                    <textarea name="notes" rows="3" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">{{ old('notes', $customer->notes) }}</textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('crm.customers.index') }}" style="background: #E2E8F0; padding: 12px 24px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 14px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; font-size: 15px;">
                    <i class="fa-solid fa-save"></i>
                    تحديث بيانات العميل
                </button>
            </div>

        </div>
    </form>
</div>
@endsection
