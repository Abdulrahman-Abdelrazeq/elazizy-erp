@extends('layouts.app')

@section('title', 'دليل وسجلات الموظفين (Employees Directory)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-users"></i>
                </span>
                دليل وسجلات الموظفين (Employees Directory)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدارة ملفات العاملين، الأقسام والمسميات الوظيفية، تفاصيل الرواتب والبدلات، وحالة العمل
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('hr.departments.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-sitemap" style="color: var(--accent-gold);"></i>
                الأقسام والوظائف
            </a>
            <a href="{{ route('hr.payrolls.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-money-check-dollar" style="color: var(--primary);"></i>
                مسيرات الرواتب
            </a>
            <a href="{{ route('hr.employees.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-user-plus"></i>
                إضافة موظف جديد
            </a>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-id-card"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الموظفين المسجلين</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_employees'] }} موظف</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-user-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">موظفون على رأس العمل (نشط)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['active_count'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-person-walking-luggage"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">في إجازة رسمية</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['on_leave_count'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">فاتورة الرواتب الشهرية الإجمالية</span>
                <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['total_salaries']) }}</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('hr.employees.index') }}" style="display: grid; grid-template-columns: 2fr repeat(auto-fit, minmax(160px, 1fr)) 100px 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">بحث سريع (بالاسم، الكود، الهاتف، الهوية)</label>
                <input type="text" name="search" class="erp-input" value="{{ request('search') }}" placeholder="اكتب للبحث...">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">القسم</label>
                <select name="department_id" class="erp-input">
                    <option value="">جميع الأقسام</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الحالة</label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط (على رأس العمل)</option>
                    <option value="on_leave" {{ request('status') === 'on_leave' ? 'selected' : '' }}>في إجازة</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>موقوف</option>
                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>منتهي الخدمة</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>

            <div>
                <a href="{{ route('hr.employees.index') }}" style="display: block; width: 100%; text-align: center; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Employees Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">الرقم الوظيفي</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم الموظف</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 150px;">القسم والوظيفة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">الهاتف</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">تاريخ التعيين</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">الراتب الشامل</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">الحالة</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 130px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            {{ $emp->emp_code }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                            <a href="{{ route('hr.employees.show', $emp->id) }}" style="font-weight: 800; color: var(--text-main); text-decoration: none; display: flex; align-items: center; gap: 8px;">
                                <span style="width: 32px; height: 32px; border-radius: 50%; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800;">
                                    {{ mb_substr($emp->name, 0, 1) }}
                                </span>
                                {{ $emp->name }}
                            </a>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                            <span style="font-weight: 700; color: var(--text-main); display: block;">{{ $emp->department?->name ?? 'غير محدد' }}</span>
                            <span style="font-size: 11px; color: var(--text-muted);">{{ $emp->designation?->title ?? '-' }}</span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-main);">
                            {{ $emp->phone ?: '-' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                            {{ $emp->hire_date ? $emp->hire_date->format('Y-m-d') : '-' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: right; font-weight: 800; color: #0F5132; font-size: 14px;">
                            {{ formatMoney($emp->total_salary) }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($emp->status === 'active')
                                <span style="background: #D1E7DD; color: #0F5132; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">نشط</span>
                            @elseif($emp->status === 'on_leave')
                                <span style="background: #FEF3C7; color: #D97706; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">إجازة</span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">{{ $emp->status }}</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('hr.employees.show', $emp->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="عرض الملف">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('hr.employees.edit', $emp->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="تعديل">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form method="POST" action="{{ route('hr.employees.destroy', $emp->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموظف؟')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="حذف">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 30px; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-users" style="font-size: 32px; display: block; margin-bottom: 8px; opacity: 0.3;"></i>
                            لا يوجد موظفون مسجلون. اضغط "إضافة موظف جديد" للبدء.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div style="margin-top: 20px;">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

