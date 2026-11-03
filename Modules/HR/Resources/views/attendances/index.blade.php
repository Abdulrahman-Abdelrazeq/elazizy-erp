@extends('layouts.app')

@section('title', 'كشف الحضور والانصراف والدوام (Attendance Sheet)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-clock"></i>
                </span>
                كشف الحضور والانصراف والدوام اليومي (Daily Attendance Sheet)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تسجيل ومتابعة حضور الموظفين، ساعات الدوام، التأخيرات، والعمل الإضافي (Overtime)
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('hr.employees.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-users"></i>
                دليل الموظفين
            </a>
            <button type="button" onclick="markAllPresent()" style="background: #D1E7DD; color: #0F5132; border: 1px solid #A3CFBB; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-check-double"></i>
                تعيين الكل حاضر
            </button>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">حاضر اليوم (Present)</span>
            <strong style="font-size: 22px; font-weight: 900; color: #0F5132;">{{ $stats['present_count'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">غائب اليوم (Absent)</span>
            <strong style="font-size: 22px; font-weight: 900; color: #DC2626;">{{ $stats['absent_count'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">متأخر عن الموعد (Late)</span>
            <strong style="font-size: 22px; font-weight: 900; color: #D97706;">{{ $stats['late_count'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجازات رسمية (On Leave)</span>
            <strong style="font-size: 22px; font-weight: 900; color: #4338CA;">{{ $stats['on_leave'] }}</strong>
        </div>
    </div>

    <!-- Date Picker Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('hr.attendances.index') }}" style="display: flex; flex-wrap: wrap; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">تاريخ الكشف اليومي</label>
                <input type="date" name="date" class="erp-input" value="{{ $date }}" onchange="this.form.submit()" style="font-weight: 800;">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">القسم</label>
                <select name="department_id" class="erp-input" onchange="this.form.submit()">
                    <option value="">جميع الأقسام</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <a href="{{ route('hr.attendances.index') }}" style="display: inline-block; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    اليوم
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Sheet Form -->
    <form method="POST" action="{{ route('hr.attendances.store') }}">
        @csrf
        <input type="hidden" name="date" value="{{ $date }}">

        <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 12px; margin-bottom: 18px; flex-wrap: wrap; gap: 10px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 900; color: var(--primary); margin: 0;">
                        كشف الحضور ليوم {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                    </h3>
                    <span style="font-size: 12px; color: var(--text-muted);">عدّل الحالات وساعات التأخير والإضافي ثم اضغط حفظ الكشف</span>
                </div>

                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-save"></i>
                    حفظ وتثبيت الكشف
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 45px;">#</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 100px;">الكود</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم الموظف</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">القسم</th>
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 150px;">حالة الدوام اليوم</th>
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 130px;">وقت الحضور</th>
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 130px;">وقت الانصراف</th>
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">تأخير (دقيقة)</th>
                            <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">إضافي (ساعة)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $idx => $emp)
                            @php
                                $att = $attendances->get($emp->id);
                                $currentStatus = $att ? $att->status : 'present';
                            @endphp
                            <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 10px; text-align: center; font-weight: 700; color: var(--text-muted);">
                                    {{ $idx + 1 }}
                                </td>
                                <td style="padding: 10px; font-family: monospace; font-weight: 800; color: var(--primary);">
                                    {{ $emp->emp_code }}
                                </td>
                                <td style="padding: 10px; font-weight: 800; color: var(--text-main);">
                                    {{ $emp->name }}
                                </td>
                                <td style="padding: 10px; color: var(--text-muted);">
                                    {{ $emp->department?->name ?? '-' }}
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <select name="attendances[{{ $emp->id }}][status]" class="erp-input status-select" style="font-size: 12.5px; font-weight: 700; padding: 6px;">
                                        <option value="present" {{ $currentStatus === 'present' ? 'selected' : '' }}>حاضر</option>
                                        <option value="late" {{ $currentStatus === 'late' ? 'selected' : '' }}>متأخر</option>
                                        <option value="absent" {{ $currentStatus === 'absent' ? 'selected' : '' }}>غائب</option>
                                        <option value="on_leave" {{ $currentStatus === 'on_leave' ? 'selected' : '' }}>إجازة رسمية</option>
                                    </select>
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <input type="time" name="attendances[{{ $emp->id }}][check_in]" class="erp-input" value="{{ $att && $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('H:i') : '09:00' }}" style="padding: 6px; font-size: 12.5px;">
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <input type="time" name="attendances[{{ $emp->id }}][check_out]" class="erp-input" value="{{ $att && $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('H:i') : '17:00' }}" style="padding: 6px; font-size: 12.5px;">
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <input type="number" min="0" name="attendances[{{ $emp->id }}][late_minutes]" class="erp-input" value="{{ $att ? $att->late_minutes : 0 }}" style="padding: 6px; font-size: 12.5px; width: 80px; text-align: center;">
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <input type="number" step="0.5" min="0" name="attendances[{{ $emp->id }}][overtime_hours]" class="erp-input" value="{{ $att ? $att->overtime_hours : 0 }}" style="padding: 6px; font-size: 12.5px; width: 80px; text-align: center; font-weight: 700; color: #4338CA;">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="padding: 30px; text-align: center; color: var(--text-muted);">
                                    لا يوجد موظفون نشطون مسجلون في النظام.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($employees->isNotEmpty())
            <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-primary-custom" style="padding: 11px 32px; border-radius: 8px; font-weight: 800; font-size: 14px;">
                    <i class="fa-solid fa-save"></i> حفظ وتثبيت الكشف
                </button>
            </div>
            @endif
        </div>
    </form>

</div>

<script>
function markAllPresent() {
    document.querySelectorAll('.status-select').forEach(select => {
        select.value = 'present';
    });
}
</script>
@endsection

