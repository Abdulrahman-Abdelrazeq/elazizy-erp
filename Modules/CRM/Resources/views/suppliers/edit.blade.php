@extends('layouts.app')

@section('title', 'تعديل بيانات المورد: ' . $supplier->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1000px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-user-pen" style="color: var(--primary); margin-left: 8px;"></i>
                تعديل بيانات المورد: {{ $supplier->name }}
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديث بيانات التواصل، العنوان، والبيانات الضريبية
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('crm.suppliers.statement', $supplier->id) }}" style="background: var(--primary-light); color: var(--primary); border: 1px solid var(--primary); padding: 9px 18px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice"></i>
                كشف الحساب
            </a>
            <a href="{{ route('crm.suppliers.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة لقائمة الموردين
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

    <form action="{{ route('crm.suppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')

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
                        <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            اسم الشركة / المنشأة
                        </label>
                        <input type="text" name="company_name" value="{{ old('company_name', $supplier->company_name) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            كود المورد
                        </label>
                        <input type="text" name="code" value="{{ old('code', $supplier->code) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            رقم الهاتف الأرضي
                        </label>
                        <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            رقم الموبايل / واتساب
                        </label>
                        <input type="text" name="mobile" value="{{ old('mobile', $supplier->mobile) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            البريد الإلكتروني
                        </label>
                        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            المدينة / المنطقة
                        </label>
                        <select name="location_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                            <option value="">-- اختر المنطقة أو المدينة --</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('location_id', $supplier->location_id) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            الرقم الضريبي للمورد
                        </label>
                        <input type="text" name="tax_number" value="{{ old('tax_number', $supplier->tax_number) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        العنوان ومقر الشركة
                    </label>
                    <input type="text" name="address" value="{{ old('address', $supplier->address) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
            </div>

            <!-- Financial Card -->
            <div class="erp-card" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-coins"></i> الرصيد الحالي وحالة الحساب
                </h3>

                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
                    <div>
                        <span style="font-size: 12px; color: var(--text-muted); font-weight: 700;">الرصيد المالي الحالي للمورد:</span>
                        <div style="font-size: 20px; font-weight: 900; margin-top: 4px; color: {{ $supplier->current_balance < 0 ? '#DC2626' : ($supplier->current_balance > 0 ? '#0F5132' : '#64748B') }};">
                            {{ formatMoney(abs($supplier->current_balance)) }}
                            <small style="font-size: 12px; font-weight: 700;">
                                @if($supplier->current_balance < 0)
                                    (دائن - مستحق له علينا)
                                @elseif($supplier->current_balance > 0)
                                    (مدين - دفعات مقدمة له)
                                @else
                                    (خالص)
                                @endif
                            </small>
                        </div>
                    </div>

                    <a href="{{ route('crm.adjustments.index') }}?client_id={{ $supplier->id }}" class="btn-primary-custom" style="padding: 8px 16px; font-size: 12.5px;">
                        <i class="fa-solid fa-scale-balanced"></i> تسوية رصيد
                    </a>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            رقم السجل التجاري
                        </label>
                        <input type="text" name="commercial_register" value="{{ old('commercial_register', $supplier->commercial_register) }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                            حالة الحساب
                        </label>
                        <select name="is_active" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                            <option value="1" {{ old('is_active', $supplier->is_active) ? 'selected' : '' }}>نشط (مسموح بالتعامل والتوريد)</option>
                            <option value="0" {{ !old('is_active', $supplier->is_active) ? 'selected' : '' }}>موقف (تجميد التعامل مؤقتاً)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-top: 16px;">
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">
                        ملاحظات أو شروط السداد والتعاقد
                    </label>
                    <textarea name="notes" rows="3" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">{{ old('notes', $supplier->notes) }}</textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px;">
                <a href="{{ route('crm.suppliers.index') }}" style="background: #E2E8F0; padding: 12px 24px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 14px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 36px; font-size: 15px;">
                    <i class="fa-solid fa-save"></i>
                    تحديث بيانات المورد
                </button>
            </div>

        </div>
    </form>
</div>
@endsection
