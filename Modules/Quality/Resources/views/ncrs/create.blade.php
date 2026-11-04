@extends('layouts.app')

@section('title', 'تسجيل تقرير عدم مطابقة (NCR)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px; max-width: 1000px; margin: 0 auto;">

    <!-- Top Breadcrumbs / Title -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <a href="{{ route('quality.ncrs.index') }}" style="color: var(--primary); text-decoration: none;">تقارير عدم المطابقة</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i>
                <span>تقرير جديد</span>
            </div>
            <h2 style="font-size: 20px; font-weight: 900; color: #DC2626; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                تسجيل تقرير حالة عدم مطابقة / عيب فني (Non-Conformance Report - NCR)
            </h2>
        </div>

        <a href="{{ route('quality.ncrs.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لسجل NCR
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

    <form method="POST" action="{{ route('quality.ncrs.store') }}">
        @csrf

        <div class="erp-card" style="padding: 24px; margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 800; color: #DC2626; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle-exclamation"></i>
                1. توصيف العيب الفني ومصدر المخالفة
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        رقم تقرير عدم المطابقة <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="ncr_number" value="{{ old('ncr_number', $suggestedNumber) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-family: monospace; background: #F8FAFC;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        الفرع المعني <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="branch_id" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        درجة الخطورة والتأثير <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="severity" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-weight: 700;">
                        <option value="minor" {{ old('severity') == 'minor' ? 'selected' : '' }}>ثانوي وبسيط (Minor - لا يعطل الاستخدام)</option>
                        <option value="major" {{ old('severity') == 'major' ? 'selected' : '' }}>رئيسي وجوهري (Major - يؤثر على الكفاءة)</option>
                        <option value="critical" {{ old('severity') == 'critical' ? 'selected' : '' }}>حرج وخطير (Critical - خطر سلامة أو فشل وظيفي)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        تصنيف السبب الجذري <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="root_cause_category" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="raw_material" {{ old('root_cause_category') == 'raw_material' ? 'selected' : '' }}>مواد خام وموردين (Raw Material)</option>
                        <option value="machinery" {{ old('root_cause_category') == 'machinery' ? 'selected' : '' }}>الآلات وخطوط الإنتاج (Machinery)</option>
                        <option value="workmanship" {{ old('root_cause_category') == 'workmanship' ? 'selected' : '' }}>العمالة والتشغيل البشري (Workmanship)</option>
                        <option value="environment" {{ old('root_cause_category') == 'environment' ? 'selected' : '' }}>بيئة العمل والتخزين (Environment)</option>
                        <option value="design" {{ old('root_cause_category') == 'design' ? 'selected' : '' }}>المخططات والمواصفات (Design)</option>
                        <option value="other" {{ old('root_cause_category') == 'other' ? 'selected' : '' }}>أسباب أخرى (Other)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        الصنف المعيب
                    </label>
                    <select name="item_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- بدون صنف محدد --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>{{ $item->name }} ({{ $item->sku }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        المورد المعني (إن وجد)
                    </label>
                    <select name="supplier_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- بدون مورد --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        مشروع المقاولة (إن وجد)
                    </label>
                    <select name="project_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- بدون مشروع --</option>
                        @foreach($projects as $prj)
                            <option value="{{ $prj->id }}" {{ old('project_id') == $prj->id ? 'selected' : '' }}>{{ $prj->code }} - {{ $prj->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        مكتشف / مسجل التقرير
                    </label>
                    <select name="reported_by" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- اختر الموظف/المفتش --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('reported_by') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                    عنوان وموضوع الخلل <span style="color: #DC2626;">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="مثال: ظهور شروخ وشعرية بخرسانة القواعد بعد الصب أو انحراف مقاسات الألواح" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                    الوصف التفصيلي لحالة عدم المطابقة <span style="color: #DC2626;">*</span>
                </label>
                <textarea name="description" rows="3" required placeholder="أدخل تفاصيل ومكان رصد العيب، الكميات المتأثرة، والأدلة المادية..." style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="erp-card" style="padding: 24px; margin-bottom: 24px;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-gavel"></i>
                2. الآثار المالية وتحليل الأسباب وقرار التصرف (Disposition)
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 18px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        الكمية المعيبة والمتأثرة <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="number" step="0.001" name="defect_quantity" value="{{ old('defect_quantity', '1.000') }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        التكلفة التقديرية للخسارة / الهدر (ر.س)
                    </label>
                    <input type="number" step="0.01" name="estimated_loss_amount" value="{{ old('estimated_loss_amount', '0.00') }}" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                        قرار التصرف والمعالجة (Disposition) <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="disposition" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-weight: 700;">
                        <option value="pending" {{ old('disposition') == 'pending' ? 'selected' : '' }}>قيد الدراسة وتحديد القرار</option>
                        <option value="return_to_vendor" {{ old('disposition') == 'return_to_vendor' ? 'selected' : '' }}>إرجاع للمورد مع طلب استبدال أو خصم</option>
                        <option value="rework" {{ old('disposition') == 'rework' ? 'selected' : '' }}>إعادة تشغيل وتصليح داخلي (Rework)</option>
                        <option value="scrap" {{ old('disposition') == 'scrap' ? 'selected' : '' }}>إتلاف وتكهين فوري (Scrap)</option>
                        <option value="use_as_is" {{ old('disposition') == 'use_as_is' ? 'selected' : '' }}>قبول باستثناء فني مع تخفيض سعر (Use As-Is)</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                    تحليل السبب الجذري المبدئي (Root Cause Analysis / 5 Whys)
                </label>
                <textarea name="root_cause_analysis" rows="2" placeholder="ما هو السبب الأساسي الذي أدى لظهور هذا الخلل؟" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">{{ old('root_cause_analysis') }}</textarea>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 6px; display: block;">
                    ملاحظات وتوجيهات قرار التصرف
                </label>
                <textarea name="disposition_notes" rows="2" placeholder="أي تعليمات تنفيذية لفرق المستودعات أو الصيانة أو الإنتاج..." style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">{{ old('disposition_notes') }}</textarea>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 12px;">
            <a href="{{ route('quality.ncrs.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; color: var(--text-main); font-weight: 700; font-size: 14px; text-decoration: none;">
                إلغاء
            </a>
            <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 800; border: none; cursor: pointer; background: #DC2626;">
                <i class="fa-solid fa-save"></i>
                حفظ وتسجيل تقرير NCR
            </button>
        </div>
    </form>

</div>
@endsection
