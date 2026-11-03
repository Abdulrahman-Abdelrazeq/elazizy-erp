@extends('layouts.app')

@section('title', 'صلاحيات وتعيينات مستخدمي الفروع (Branch Users)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-user-shield"></i>
                </span>
                صلاحيات وتعيينات مستخدمي الفروع (Branch Access Control)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد الفروع المصرح للموظفين بالعمل عليها، الأدوار اللامركزية، وإمكانية التبديل بين الفروع
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('branches.branches.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-building"></i>
                دليل الفروع
            </a>
            <button type="button" onclick="document.getElementById('assignModal').style.display='flex'" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                <i class="fa-solid fa-user-plus"></i>
                تعيين مستخدم لفرع جديد
            </button>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('branches.users.index') }}" style="display: grid; grid-template-columns: 2fr 1fr 100px 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الفرع</label>
                <select name="branch_id" class="erp-input">
                    <option value="">جميع الفروع</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }} ({{ $b->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الدور الوظيفي بالفرع</label>
                <select name="role" class="erp-input">
                    <option value="">جميع الأدوار</option>
                    <option value="manager" {{ request('role') === 'manager' ? 'selected' : '' }}>مدير الفرع (Manager)</option>
                    <option value="accountant" {{ request('role') === 'accountant' ? 'selected' : '' }}>محاسب الفرع (Accountant)</option>
                    <option value="cashier" {{ request('role') === 'cashier' ? 'selected' : '' }}>كاشير / نقطة بيع (Cashier)</option>
                    <option value="storekeeper" {{ request('role') === 'storekeeper' ? 'selected' : '' }}>أمين مستودع (Storekeeper)</option>
                    <option value="sales_rep" {{ request('role') === 'sales_rep' ? 'selected' : '' }}>مسؤول مبيعات (Sales Rep)</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>موظف عام (Staff)</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>

            <div>
                <a href="{{ route('branches.users.index') }}" style="display: block; width: 100%; text-align: center; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Assignments Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">المستخدم</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 180px;">البريد الإلكتروني</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">الفرع المصرح بالعمل عليه</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الدور بالفرع</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 120px;">الفرع الافتراضي</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 120px;">صلاحية التبديل</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 80px;">إلغاء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $asg)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            <span style="display: flex; align-items: center; gap: 8px;">
                                <span style="width: 30px; height: 30px; border-radius: 50%; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">
                                    {{ mb_substr($asg->user?->name ?? 'م', 0, 1) }}
                                </span>
                                {{ $asg->user?->name ?? 'مستخدم محذوف' }}
                            </span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-muted);">
                            {{ $asg->user?->email }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                            <strong style="color: var(--primary);">{{ $asg->branch?->name }}</strong>
                            <span style="font-family: monospace; font-size: 11px; color: var(--text-muted); margin-right: 4px;">({{ $asg->branch?->code }})</span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                            <span style="background: #E0E7FF; color: #4338CA; padding: 3px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                {{ $asg->role_label }}
                            </span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($asg->is_default)
                                <span style="background: #FEF3C7; color: #D97706; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 800;">
                                    <i class="fa-solid fa-star"></i> افتراضي
                                </span>
                            @else
                                <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($asg->can_switch)
                                <span style="color: #0F5132; font-weight: 800; font-size: 12px;"><i class="fa-solid fa-check"></i> مسموح</span>
                            @else
                                <span style="color: #DC2626; font-size: 12px;"><i class="fa-solid fa-lock"></i> مقيد بالفرع</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            <form method="POST" action="{{ route('branches.users.destroy', $asg->id) }}" onsubmit="return confirm('هل أنت متأكد من إلغاء تعيين المستخدم لهذا الفرع؟')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="إلغاء التعيين">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            لا توجد تعيينات مسجلة للمستخدمين بالفروع.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assignments->hasPages())
            <div style="margin-top: 20px;">
                {{ $assignments->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Assign User to Branch -->
<div id="assignModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: modalFadeIn 0.2s ease;">
        <div style="background: linear-gradient(135deg, var(--primary) 0%, #062E20 100%); padding: 18px 24px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-user-plus"></i>
                </span>
                <h3 style="margin: 0; font-size: 16px; font-weight: 800;">تعيين مستخدم لفرع</h3>
            </div>
            <button type="button" onclick="document.getElementById('assignModal').style.display='none'" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('branches.users.store') }}" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اختر المستخدم *</label>
                <select name="user_id" class="erp-input" required>
                    <option value="">-- اختر المستخدم --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اختر الفرع *</label>
                <select name="branch_id" class="erp-input" required>
                    <option value="">-- اختر الفرع --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الدور الوظيفي داخل هذا الفرع *</label>
                <select name="role" class="erp-input" required>
                    <option value="manager">مدير الفرع (Branch Manager)</option>
                    <option value="accountant">محاسب الفرع (Accountant)</option>
                    <option value="cashier">كاشير نقطة بيع (Cashier)</option>
                    <option value="storekeeper">أمين مستودع الفرع (Storekeeper)</option>
                    <option value="sales_rep">مسؤول مبيعات (Sales Rep)</option>
                    <option value="staff" selected>موظف تشغيل عام (Staff)</option>
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 6px;">
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="is_default" value="1">
                    تعيين كفرع افتراضي للمستخدم عند تسجيل الدخول
                </label>
                <label style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    <input type="checkbox" name="can_switch" value="1" checked>
                    السماح للمستخدم بالتبديل بين الفروع المصرح له بها
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('assignModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--text-muted);">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                    <i class="fa-solid fa-check"></i> حفظ التعيين
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
