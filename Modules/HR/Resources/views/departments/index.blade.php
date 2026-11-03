@extends('layouts.app')

@section('title', 'الهيكل الإداري والأقسام والوظائف (Departments & Designations)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-sitemap"></i>
                </span>
                الهيكل التنظيمي: الأقسام والمسميات الوظيفية (Departments & Designations)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد الهيكل الإداري، أقسام الشركة، والمسميات الوظيفية المتاحة وتوزيع الموظفين عليها
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('hr.employees.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-users"></i>
                دليل الموظفين
            </a>
            <button onclick="openDeptModal()" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة قسم جديد
            </button>
            <button onclick="openDesigModal()" style="background: #4338CA; color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-briefcase"></i>
                إضافة وظيفة جديدة
            </button>
        </div>
    </div>

    <!-- Dual Columns: Departments & Designations -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 20px;">

        <!-- 1. DEPARTMENTS TABLE -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 900; color: var(--primary); margin-bottom: 16px; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-building-user"></i>
                الأقسام الإدارية (Departments)
            </h3>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; font-weight: 800; color: var(--text-muted);">
                            <th style="padding: 10px; text-align: right;">القسم</th>
                            <th style="padding: 10px; text-align: right;">المدير المسؤول</th>
                            <th style="padding: 10px; text-align: center; width: 80px;">الموظفون</th>
                            <th style="padding: 10px; text-align: center; width: 60px;">حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $dept)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 10px; font-weight: 700; color: var(--text-main);">
                                {{ $dept->name }}
                                @if($dept->code)
                                    <span style="font-size: 11px; color: var(--text-muted); font-family: monospace;">({{ $dept->code }})</span>
                                @endif
                            </td>
                            <td style="padding: 10px; color: var(--text-muted);">{{ $dept->manager_name ?: '-' }}</td>
                            <td style="padding: 10px; text-align: center;">
                                <span style="background: rgba(15, 81, 50, 0.1); color: var(--primary); padding: 2px 8px; border-radius: 12px; font-weight: 800; font-size: 12px;">
                                    {{ $dept->employees_count }}
                                </span>
                            </td>
                            <td style="padding: 10px; text-align: center;">
                                <form method="POST" action="{{ route('hr.departments.destroy', $dept->id) }}" onsubmit="return confirm('هل تريد حذف هذا القسم؟')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #EF4444; cursor: pointer;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: var(--text-muted);">لا توجد أقسام مسجلة</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 2. DESIGNATIONS TABLE -->
        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <h3 style="font-size: 16px; font-weight: 900; color: #4338CA; margin-bottom: 16px; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-user-tie"></i>
                المسميات والوظائف (Designations)
            </h3>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; font-weight: 800; color: var(--text-muted);">
                            <th style="padding: 10px; text-align: right;">المسمى الوظيفي</th>
                            <th style="padding: 10px; text-align: right;">القسم التابع له</th>
                            <th style="padding: 10px; text-align: center; width: 80px;">شاغلو الوظيفة</th>
                            <th style="padding: 10px; text-align: center; width: 60px;">حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($designations as $desig)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 10px; font-weight: 700; color: var(--text-main);">{{ $desig->title }}</td>
                            <td style="padding: 10px; color: var(--text-muted);">{{ $desig->department?->name ?? 'عام' }}</td>
                            <td style="padding: 10px; text-align: center;">
                                <span style="background: #E0E7FF; color: #4338CA; padding: 2px 8px; border-radius: 12px; font-weight: 800; font-size: 12px;">
                                    {{ $desig->employees_count }}
                                </span>
                            </td>
                            <td style="padding: 10px; text-align: center;">
                                <form method="POST" action="{{ route('hr.designations.destroy', $desig->id) }}" onsubmit="return confirm('هل تريد حذف هذه الوظيفة؟')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #EF4444; cursor: pointer;">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: var(--text-muted);">لا توجد وظائف مسجلة</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- Create Department Modal -->
<div class="theme-modal" id="deptModal">
    <div class="modal-box" style="max-width: 450px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0;">إضافة قسم إداري جديد</h3>
            <button onclick="closeDeptModal()" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="{{ route('hr.departments.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">اسم القسم <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="name" class="erp-input" required placeholder="مثال: قطاع المبيعات أو الإدارة المالية">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">كود القسم (اختياري)</label>
                    <input type="text" name="code" class="erp-input" placeholder="مثال: SALES">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">اسم مدير القسم</label>
                    <input type="text" name="manager_name" class="erp-input" placeholder="اسم المسؤول">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="closeDeptModal()" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800;">حفظ القسم</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Create Designation Modal -->
<div class="theme-modal" id="desigModal">
    <div class="modal-box" style="max-width: 450px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #4338CA; margin: 0;">إضافة مسمى وظيفي جديد</h3>
            <button onclick="closeDesigModal()" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="{{ route('hr.designations.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">المسمى الوظيفي <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="title" class="erp-input" required placeholder="مثال: محاسب أول / مندوب مبيعات">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">القسم التابع له</label>
                    <select name="department_id" class="erp-input">
                        <option value="">-- بدون قسم محدد --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">وصف الوظيفة (اختياري)</label>
                    <input type="text" name="description" class="erp-input" placeholder="الوصف والمهام">
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="closeDesigModal()" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">إلغاء</button>
                    <button type="submit" style="background: #4338CA; color: #fff; border: none; padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer;">حفظ الوظيفة</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openDeptModal() { document.getElementById('deptModal').classList.add('active'); }
function closeDeptModal() { document.getElementById('deptModal').classList.remove('active'); }
function openDesigModal() { document.getElementById('desigModal').classList.add('active'); }
function closeDesigModal() { document.getElementById('desigModal').classList.remove('active'); }
</script>
@endsection

