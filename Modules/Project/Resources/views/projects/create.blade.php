@extends('layouts.app')

@section('title', 'تسجيل مشروع وعقد مقاولة جديد')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px; max-width: 1100px; margin: 0 auto;">

    <!-- Top Breadcrumbs / Title -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <a href="{{ route('projects.projects.index') }}" style="color: var(--primary); text-decoration: none;">المشاريع والمقاولات</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i>
                <span>تسجيل مشروع وعقد جديد</span>
            </div>
            <h2 style="font-size: 20px; font-weight: 900; color: var(--text-main); margin: 0;">
                تسجيل بيانات مشروع وعقد مقاولة جديد
            </h2>
        </div>

        <a href="{{ route('projects.projects.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة للمشاريع
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div style="background: #FEF2F2; border: 1px solid #F87171; border-radius: 10px; padding: 14px 18px; color: #991B1B; font-size: 13px;">
            <div style="font-weight: 800; margin-bottom: 6px;">يرجى مراجعة الأخطاء التالية:</div>
            <ul style="margin: 0; padding-right: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.projects.store') }}">
        @csrf

        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-contract"></i>
                1. البيانات الأساسية للمشروع والعقد
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        كود المشروع <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="code" value="{{ old('code', $suggestedCode) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-family: monospace; background: #F8FAFC;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        اسم المشروع <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: إنشاء برج أعمال تجاري - العليا" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        العميل / المالك <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="client_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- اختر العميل --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        الفرع المنفذ <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="branch_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- اختر الفرع --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        مدير المشروع (مهندس الموقع)
                    </label>
                    <select name="project_manager_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- اختياري: اختر المهندس المسؤول --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('project_manager_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }} ({{ $emp->job_title ?? 'موظف' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        مركز التكلفة المحاسبي
                    </label>
                    <select name="cost_center_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- بدون مركز تكلفة مخصص --</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}" {{ old('cost_center_id') == $cc->id ? 'selected' : '' }}>{{ $cc->code }} - {{ $cc->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-coins"></i>
                2. الشروط المالية والتعاقدية للمقاولة
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        قيمة العقد الإجمالية (ر.س) <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="number" step="0.01" name="contract_value" value="{{ old('contract_value', '0.00') }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; font-weight: 800; color: var(--primary);">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        نسبة الدفعة المقدمة (%)
                    </label>
                    <input type="number" step="0.1" name="advance_payment_percentage" value="{{ old('advance_payment_percentage', '10') }}" placeholder="10%" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <span style="font-size: 11px; color: var(--text-muted);">تُخصم تدريجياً من كل مستخلص جاري</span>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        نسبة محتجز ضمان الأعمال (%)
                    </label>
                    <input type="number" step="0.1" name="retention_percentage" value="{{ old('retention_percentage', '5') }}" placeholder="5%" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <span style="font-size: 11px; color: var(--text-muted);">يُحتجز لحين التسليم النهائي للأعمال</span>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        رقم العقد بالجهة المالكة
                    </label>
                    <input type="text" name="contract_number" value="{{ old('contract_number') }}" placeholder="مثال: CNT-2026-88" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        تاريخ توقيع العقد
                    </label>
                    <input type="date" name="contract_date" value="{{ old('contract_date', date('Y-m-d')) }}" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        حالة المشروع الابتدائية <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="status" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>تخطيط وتجهيز (Planning)</option>
                        <option value="in_progress" {{ old('status', 'in_progress') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ (In-Progress)</option>
                        <option value="delivered_primary" {{ old('status') == 'delivered_primary' ? 'selected' : '' }}>تسليم ابتدائي (Primary Delivery)</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>منتهي ومسلّم (Completed)</option>
                        <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>معلق وموقف مؤقتاً (Suspended)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="erp-card" style="padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calendar-days"></i>
                3. الجدول الزمني والموقع ونطاق الأعمال
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        تاريخ بدء المشروع والتسليم للموقع <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        تاريخ انتهاء المشروع التعاقدي المتوقع <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date" name="expected_end_date" value="{{ old('expected_end_date', date('Y-m-d', strtotime('+6 months'))) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        موقع المشروع والمدينة
                    </label>
                    <input type="text" name="location" value="{{ old('location') }}" placeholder="مثال: الرياض - حي الصحافة - طريق الملك فهد" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                    وصف المشروع ونطاق العمل التفصيلي (Scope of Work)
                </label>
                <textarea name="description" rows="3" placeholder="أدخل تفاصيل ومواصفات المشروع، الاشتراطات الهندسية، وأي شروط خاصة بالعقد..." style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">{{ old('description') }}</textarea>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('projects.projects.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; color: var(--text-main); font-weight: 700; font-size: 14px; text-decoration: none;">
                إلغاء
            </a>
            <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 800; border: none; cursor: pointer;">
                <i class="fa-solid fa-save"></i>
                حفظ وتسجيل المشروع
            </button>
        </div>
    </form>

</div>
@endsection
