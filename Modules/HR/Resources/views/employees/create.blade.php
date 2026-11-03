@extends('layouts.app')

@section('title', 'إضافة موظف جديد (New Employee)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-user-plus"></i>
                </span>
                إضافة موظف جديد (New Employee)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تسجيل البيانات الشخصية والوظيفية وهيكل الراتب والبدلات للموظف الجديد
            </span>
        </div>

        <div>
            <a href="{{ route('hr.employees.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة لدليل الموظفين
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div style="background: #FEE2E2; border: 1px solid #F87171; color: #991B1B; padding: 14px 20px; border-radius: 12px; font-size: 13px;">
            <strong style="display: block; margin-bottom: 6px;"><i class="fa-solid fa-triangle-exclamation"></i> يرجى تصحيح الأخطاء التالية:</strong>
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hr.employees.store') }}">
        @csrf

        <!-- 1. Personal & Identity Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-address-card"></i>
                البيانات الشخصية والتعريفية
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الرقم الوظيفي <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="emp_code" class="erp-input" value="{{ old('emp_code', $nextCode) }}" required style="font-family: monospace; font-weight: 800; color: var(--primary);">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اسم الموظف الكامل <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="name" class="erp-input" value="{{ old('name') }}" required placeholder="الاسم الرباعي للموظف">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الرقم القومي / الهوية الوطنية</label>
                    <input type="text" name="national_id" class="erp-input" value="{{ old('national_id') }}" placeholder="14 رقم">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">رقم الهاتف / الجوال</label>
                    <input type="text" name="phone" class="erp-input" value="{{ old('phone') }}" placeholder="01xxxxxxxxx">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">البريد الإلكتروني</label>
                    <input type="email" name="email" class="erp-input" value="{{ old('email') }}" placeholder="employee@company.com">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">تاريخ الميلاد</label>
                    <input type="date" name="birth_date" class="erp-input" value="{{ old('birth_date') }}">
                </div>
            </div>
        </div>

        <!-- 2. Job & Contract Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #4338CA; margin-bottom: 18px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-briefcase"></i>
                بيانات التعيين والتعاقد
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الفرع</label>
                    <select name="branch_id" class="erp-input">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">القسم الإداري</label>
                    <select name="department_id" class="erp-input">
                        <option value="">-- بدون قسم --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">المسمى الوظيفي</label>
                    <select name="designation_id" class="erp-input">
                        <option value="">-- بدون مسمى --</option>
                        @foreach($designations as $desig)
                            <option value="{{ $desig->id }}" {{ old('designation_id') == $desig->id ? 'selected' : '' }}>{{ $desig->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">تاريخ التعيين والالتحاق <span style="color: #EF4444;">*</span></label>
                    <input type="date" name="hire_date" class="erp-input" value="{{ old('hire_date', date('Y-m-d')) }}" required>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">نوع العقد <span style="color: #EF4444;">*</span></label>
                    <select name="contract_type" class="erp-input" required>
                        <option value="full_time" {{ old('contract_type') === 'full_time' ? 'selected' : '' }}>دوام كامل (Full-time)</option>
                        <option value="part_time" {{ old('contract_type') === 'part_time' ? 'selected' : '' }}>دوام جزئي (Part-time)</option>
                        <option value="probation" {{ old('contract_type') === 'probation' ? 'selected' : '' }}>فترة تجربة (Probation)</option>
                        <option value="contract" {{ old('contract_type') === 'contract' ? 'selected' : '' }}>عقد مؤقت / مشروع</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">حالة الموظف <span style="color: #EF4444;">*</span></label>
                    <select name="status" class="erp-input" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>نشط (على رأس العمل)</option>
                        <option value="on_leave" {{ old('status') === 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>موقوف عن العمل</option>
                        <option value="terminated" {{ old('status') === 'terminated' ? 'selected' : '' }}>منتهي الخدمة</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Salary & Compensation Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #D97706; margin-bottom: 18px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-money-bill-wave"></i>
                هيكل الراتب الشهري والبدلات
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الراتب الأساسي (Basic Salary) <span style="color: #EF4444;">*</span></label>
                    <input type="number" step="0.01" min="0" name="basic_salary" class="erp-input salary-calc" value="{{ old('basic_salary', '0.00') }}" required style="font-weight: 800; font-size: 14px; color: var(--primary);">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">بدل السكن</label>
                    <input type="number" step="0.01" min="0" name="housing_allowance" class="erp-input salary-calc" value="{{ old('housing_allowance', '0.00') }}" style="font-weight: 700;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">بدل الانتقال / المواصلات</label>
                    <input type="number" step="0.01" min="0" name="transport_allowance" class="erp-input salary-calc" value="{{ old('transport_allowance', '0.00') }}" style="font-weight: 700;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">بدلات وحوافز أخرى</label>
                    <input type="number" step="0.01" min="0" name="other_allowances" class="erp-input salary-calc" value="{{ old('other_allowances', '0.00') }}" style="font-weight: 700;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">إجمالي الراتب الشامل التقديري</label>
                    <input type="text" id="totalSalaryDisplay" class="erp-input" readonly value="0.00" style="background: #F8FAFC; font-weight: 900; color: #0F5132; font-size: 15px;">
                </div>
            </div>
        </div>

        <!-- 4. Banking & Notes Card -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin-bottom: 18px; border-bottom: 1px dashed var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-building-columns"></i>
                البيانات المصرفية وملاحظات أخرى
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اسم البنك</label>
                    <input type="text" name="bank_name" class="erp-input" value="{{ old('bank_name') }}" placeholder="مثال: البنك الأهلي / الراجحي">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">رقم الحساب البنكي</label>
                    <input type="text" name="bank_account" class="erp-input" value="{{ old('bank_account') }}" placeholder="رقم الحساب">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">رقم الآيبان (IBAN)</label>
                    <input type="text" name="iban" class="erp-input" value="{{ old('iban') }}" placeholder="EG... / SA...">
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">ملاحظات إضافية على ملف الموظف</label>
                <textarea name="notes" rows="2" class="erp-input" placeholder="ملاحظات أو شروط خاصة بالموظف...">{{ old('notes') }}</textarea>
            </div>

            <div style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('hr.employees.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 32px; border-radius: 10px; font-size: 13.5px; font-weight: 800;">
                    <i class="fa-solid fa-check-circle"></i>
                    حفظ وإضافة الموظف
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function calcTotal() {
    let basic = parseFloat(document.querySelector('input[name="basic_salary"]').value) || 0;
    let housing = parseFloat(document.querySelector('input[name="housing_allowance"]').value) || 0;
    let transport = parseFloat(document.querySelector('input[name="transport_allowance"]').value) || 0;
    let other = parseFloat(document.querySelector('input[name="other_allowances"]').value) || 0;
    let total = basic + housing + transport + other;
    document.getElementById('totalSalaryDisplay').value = total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

document.querySelectorAll('.salary-calc').forEach(input => {
    input.addEventListener('input', calcTotal);
});
document.addEventListener('DOMContentLoaded', calcTotal);
</script>
@endsection

