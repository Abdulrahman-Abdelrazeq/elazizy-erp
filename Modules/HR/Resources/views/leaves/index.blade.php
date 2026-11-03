@extends('layouts.app')

@section('title', 'إدارة وسجلات إجازات الموظفين (Leave Management)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-person-walking-luggage"></i>
                </span>
                إدارة وسجلات إجازات الموظفين (Leaves Management)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                متابعة طلبات الإجازات السنوية والمرضية والعارضة والاعتمادات وتأثيرها على دوام الموظفين
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('hr.employees.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-users"></i>
                دليل الموظفين
            </a>
            <button onclick="openLeaveModal()" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px;">
                <i class="fa-solid fa-plus-circle"></i>
                تسجيل طلب إجازة جديد
            </button>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي طلبات الإجازات</span>
            <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_leaves'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجازات معتمدة ومؤكدة</span>
            <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['approved_leaves'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">طلبات قيد المراجعة</span>
            <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['pending_leaves'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">موظفون في إجازة حالياً</span>
            <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $stats['current_on_leave'] }}</strong>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('hr.leaves.index') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الموظف</label>
                <select name="employee_id" class="erp-input">
                    <option value="">جميع الموظفين</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">نوع الإجازة</label>
                <select name="leave_type" class="erp-input">
                    <option value="">جميع الأنواع</option>
                    <option value="annual" {{ request('leave_type') === 'annual' ? 'selected' : '' }}>إجازة سنوية اعتيادية</option>
                    <option value="sick" {{ request('leave_type') === 'sick' ? 'selected' : '' }}>إجازة مرضية</option>
                    <option value="emergency" {{ request('leave_type') === 'emergency' ? 'selected' : '' }}>إجازة عارضة</option>
                    <option value="unpaid" {{ request('leave_type') === 'unpaid' ? 'selected' : '' }}>بدون راتب</option>
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الحالة</label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمدة</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 20px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
                <a href="{{ route('hr.leaves.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Leaves Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">الموظف</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">نوع الإجازة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">من تاريخ</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">إلى تاريخ</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 80px;">الأيام</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">السبب / الملاحظات</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 100px;">الحالة</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 120px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaves as $leave)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; font-weight: 800; color: var(--text-main);">
                            {{ $leave->employee?->name ?? 'موظف محذوف' }}
                            <span style="font-size: 11px; color: var(--text-muted); display: block;">{{ $leave->employee?->emp_code }}</span>
                        </td>
                        <td style="padding: 12px; font-weight: 700; color: #4338CA;">
                            {{ $leave->leave_type_label }}
                        </td>
                        <td style="padding: 12px; color: var(--text-muted);">
                            {{ $leave->start_date->format('Y-m-d') }}
                        </td>
                        <td style="padding: 12px; color: var(--text-muted);">
                            {{ $leave->end_date->format('Y-m-d') }}
                        </td>
                        <td style="padding: 12px; text-align: center; font-weight: 800;">
                            {{ $leave->days_count }}
                        </td>
                        <td style="padding: 12px; color: var(--text-muted);">
                            {{ $leave->reason ?: '-' }}
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            @if($leave->status === 'approved')
                                <span style="background: #D1E7DD; color: #0F5132; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">معتمدة</span>
                            @elseif($leave->status === 'pending')
                                <span style="background: #FEF3C7; color: #D97706; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">قيد الانتظار</span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">مرفوضة</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                @if($leave->status === 'pending')
                                    <form method="POST" action="{{ route('hr.leaves.status', $leave->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" style="background: #D1E7DD; color: #0F5132; border: none; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; cursor: pointer;" title="اعتماد">
                                            اعتماد
                                        </button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('hr.leaves.destroy', $leave->id) }}" onsubmit="return confirm('هل تريد حذف سجل الإجازة؟')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #EF4444; cursor: pointer; font-size: 15px;" title="حذف">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 30px; text-align: center; color: var(--text-muted);">
                            لا توجد سجلات إجازات مسجلة.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leaves->hasPages())
            <div style="margin-top: 20px;">
                {{ $leaves->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Create Leave Modal -->
<div class="theme-modal" id="leaveModal">
    <div class="modal-box" style="max-width: 480px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-person-walking-luggage"></i> تسجيل طلب إجازة
            </h3>
            <button onclick="closeLeaveModal()" style="background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('hr.leaves.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الموظف <span style="color: #EF4444;">*</span></label>
                    <select name="employee_id" class="erp-input" required>
                        <option value="">-- اختر الموظف --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->emp_code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">نوع الإجازة <span style="color: #EF4444;">*</span></label>
                    <select name="leave_type" class="erp-input" required>
                        <option value="annual">إجازة سنوية اعتيادية</option>
                        <option value="sick">إجازة مرضية</option>
                        <option value="emergency">إجازة عارضة</option>
                        <option value="unpaid">إجازة بدون راتب</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ <span style="color: #EF4444;">*</span></label>
                        <input type="date" name="start_date" class="erp-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ <span style="color: #EF4444;">*</span></label>
                        <input type="date" name="end_date" class="erp-input" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">حالة الاعتماد</label>
                    <select name="status" class="erp-input" required>
                        <option value="approved">معتمدة ومباشرة فوراً</option>
                        <option value="pending">معلقة قيد موافقة الإدارة</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">سبب الإجازة (اختياري)</label>
                    <input type="text" name="reason" class="erp-input" placeholder="اكتب سبباً مختصراً...">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="closeLeaveModal()" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 8px 24px; border-radius: 8px; font-size: 13px; font-weight: 800;">حفظ الإجازة</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openLeaveModal() { document.getElementById('leaveModal').classList.add('active'); }
function closeLeaveModal() { document.getElementById('leaveModal').classList.remove('active'); }
</script>
@endsection

