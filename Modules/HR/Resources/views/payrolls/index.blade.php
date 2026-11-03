@extends('layouts.app')

@section('title', 'مسيرات الرواتب الشهرية (Monthly Payrolls)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-money-check-dollar"></i>
                </span>
                مسيرات الرواتب الشهرية (Monthly Payrolls)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                توليد واعتماد وصرف كشوف الرواتب الشهرية، احتساب البدلات، واستقطاع أقساط السلف والخصومات
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('hr.employees.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-users" style="color: var(--primary);"></i>
                دليل الموظفين
            </a>
            <a href="{{ route('hr.loans.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-hand-holding-dollar" style="color: var(--accent-gold);"></i>
                سلف الموظفين
            </a>
            <button type="button" onclick="document.getElementById('generateModal').style.display='flex'" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                توليد مسير رواتب جديد
            </button>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي كشوف المسيرات</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_payrolls'] }} مسير</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مسيرات تم صرفها</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['paid_payrolls'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الرواتب المصروفة فعلياً</span>
                <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['total_paid_net']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مسودات قيد الإعداد</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['draft_count'] }}</strong>
            </div>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('hr.payrolls.index') }}" style="display: grid; grid-template-columns: 1fr 1fr 100px 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">السنة المالية</label>
                <select name="year" class="erp-input">
                    <option value="">جميع السنوات</option>
                    @for($y = date('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الحالة</label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة (Draft)</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>معتمد (Approved)</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>تم الصرف (Paid)</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>

            <div>
                <a href="{{ route('hr.payrolls.index') }}" style="display: block; width: 100%; text-align: center; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Payrolls Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">رقم المسير</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">الشهر / السنة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الفرع</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">إجمالي الأساسي</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">البدلات والإضافي</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">استقطاع السلف والخصم</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">صافي المسير</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">الحالة</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 140px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $p)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            <a href="{{ route('hr.payrolls.show', $p->id) }}" style="color: inherit; text-decoration: none;">
                                {{ $p->payroll_number }}
                            </a>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            شهر {{ $p->month }} / {{ $p->year }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                            <i class="fa-solid fa-building-user" style="color: var(--accent-gold); margin-left: 4px;"></i>
                            {{ $p->branch?->name ?? 'المركز الرئيسي' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ formatMoney($p->total_basic) }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: #0F5132;">
                            +{{ formatMoney($p->total_allowances) }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: #DC2626;">
                            -{{ formatMoney($p->total_deductions + $p->total_loans_deducted) }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 900; color: var(--primary); font-size: 14px;">
                            {{ formatMoney($p->total_net) }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($p->status === 'paid')
                                <span style="background: #D1E7DD; color: #0F5132; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-check-double"></i> تم الصرف
                                </span>
                            @elseif($p->status === 'approved')
                                <span style="background: #E0E7FF; color: #4338CA; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-stamp"></i> معتمد
                                </span>
                            @else
                                <span style="background: #FEF3C7; color: #D97706; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-clock"></i> مسودة
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('hr.payrolls.show', $p->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="عرض الكشف">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('hr.payrolls.print', $p->id) }}" target="_blank" style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; color: var(--text-main); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="طباعة كشف المسير">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @if($p->status !== 'paid')
                                <form method="POST" action="{{ route('hr.payrolls.destroy', $p->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا المسير؟')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="حذف">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-money-check-dollar" style="font-size: 36px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                            لا توجد مسيرات رواتب مسجلة حتى الآن.
                            <div style="margin-top: 10px;">
                                <button type="button" onclick="document.getElementById('generateModal').style.display='flex'" class="btn-primary-custom" style="padding: 8px 16px; border-radius: 8px; font-size: 12.5px; font-weight: 800; cursor: pointer; border: none;">
                                    <i class="fa-solid fa-wand-magic-sparkles"></i> توليد مسير الآن
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payrolls->hasPages())
            <div style="margin-top: 20px;">
                {{ $payrolls->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Generate Monthly Payroll Batch -->
<div id="generateModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 520px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: modalFadeIn 0.2s ease;">
        <div style="background: linear-gradient(135deg, var(--primary) 0%, #062E20 100%); padding: 18px 24px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i>
                </span>
                <h3 style="margin: 0; font-size: 16px; font-weight: 800;">توليد مسير رواتب شهري آلي</h3>
            </div>
            <button type="button" onclick="document.getElementById('generateModal').style.display='none'" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('hr.payrolls.generate') }}" style="padding: 24px; display: flex; flex-direction: column; gap: 18px;">
            @csrf

            <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px; padding: 14px; font-size: 12.5px; color: #166534; display: flex; gap: 10px; align-items: flex-start;">
                <i class="fa-solid fa-circle-info" style="font-size: 16px; margin-top: 2px;"></i>
                <div>
                    <strong>التوليد التلقائي الذكي:</strong>
                    يقوم النظام بحساب الرواتب الأساسية، البدلات المقررة، واستقطاع أقساط السلف النشطة تلقائياً لكافة موظفي الفرع المحددين.
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">السنة *</label>
                    <select name="year" class="erp-input" required>
                        @for($y = date('Y') + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الشهر *</label>
                    <select name="month" class="erp-input" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                شهر {{ $m }} ({{ date('F', mktime(0, 0, 0, $m, 10)) }})
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الفرع</label>
                <select name="branch_id" class="erp-input">
                    <option value="">جميع الفروع / المركز الرئيسي</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('generateModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--text-muted);">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                    <i class="fa-solid fa-bolt"></i> بدء التوليد والاحتساب
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

