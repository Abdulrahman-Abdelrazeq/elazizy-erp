@extends('layouts.app')

@section('title', 'دليل الفروع ومراكز التوزيع (Branches Directory)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-network-wired"></i>
                </span>
                دليل الفروع ومراكز التوزيع (Branches Directory)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدارة شبكة الفروع الجغرافية، مراكز التوزيع الإقليمية، نقاط البيع، وصلاحيات التبديل اللامركزية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('branches.users.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-user-shield" style="color: var(--accent-gold);"></i>
                صلاحيات وتعيين الموظفين
            </a>
            <a href="{{ route('branches.transfers.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-truck-fast" style="color: var(--primary);"></i>
                التحويلات البينية
            </a>
            <a href="{{ route('branches.analytics.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-line" style="color: #4338CA;"></i>
                مقارنات أداء الفروع
            </a>
            <a href="{{ route('branches.branches.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة فرع / مركز جديد
            </a>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-building"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي شبكة الفروع</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_branches'] }} فرع / مركز</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">فروع نشطة تشغيلياً</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['active_count'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">المستودعات المرتبطة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $stats['total_stores'] }} مستودع</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">الخزائن ونقاط البيع</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['total_treasury'] }} خزينة</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('branches.branches.index') }}" style="display: grid; grid-template-columns: 2fr 1fr 1fr 100px 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">بحث سريع (اسم الفرع، الكود، المدينة، المدير)</label>
                <input type="text" name="search" class="erp-input" value="{{ request('search') }}" placeholder="اكتب للبحث...">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">نوع الفرع</label>
                <select name="branch_type" class="erp-input">
                    <option value="">جميع الأنواع</option>
                    <option value="hq" {{ request('branch_type') === 'hq' ? 'selected' : '' }}>المقر الرئيسي (HQ)</option>
                    <option value="retail" {{ request('branch_type') === 'retail' ? 'selected' : '' }}>فرع تجزئة ومعرض</option>
                    <option value="wholesale" {{ request('branch_type') === 'wholesale' ? 'selected' : '' }}>فرع جملة وتوزيع</option>
                    <option value="distribution_center" {{ request('branch_type') === 'distribution_center' ? 'selected' : '' }}>مركز توزيع إقليمي</option>
                    <option value="warehouse_only" {{ request('branch_type') === 'warehouse_only' ? 'selected' : '' }}>مستودع لوجستي</option>
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الحالة</label>
                <select name="is_active" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>نشط</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>معطل</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; border-radius: 8px; font-weight: 800; font-size: 13px;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>

            <div>
                <a href="{{ route('branches.branches.index') }}" style="display: block; width: 100%; text-align: center; background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px; border-radius: 8px; text-decoration: none; color: var(--text-muted); font-size: 13px; font-weight: 700;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Branches Grid / Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 90px;">الكود</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم الفرع / المركز</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 150px;">نوع الفرع</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">المدينة / المنطقة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">المدير المسؤول</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 120px;">الهاتف</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 90px;">المستودعات</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 80px;">الخزائن</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 90px;">الحالة</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branches as $b)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            <a href="{{ route('branches.branches.show', $b->id) }}" style="color: inherit; text-decoration: none;">
                                {{ $b->code }}
                            </a>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                            <a href="{{ route('branches.branches.show', $b->id) }}" style="font-weight: 800; color: var(--text-main); text-decoration: none; display: flex; align-items: center; gap: 8px;">
                                <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 14px;">
                                    <i class="fa-solid fa-building"></i>
                                </span>
                                {{ $b->name }}
                                @if($b->is_main)
                                    <span style="background: #FEF3C7; color: #D97706; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 800;">رئيسي</span>
                                @endif
                            </a>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-size: 12px; color: var(--text-muted); font-weight: 700;">
                            {{ $b->type_label }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-main);">
                            {{ $b->city ?: 'غير محدد' }}
                            @if($b->district)
                                <span style="display: block; font-size: 11px; color: var(--text-muted);">{{ $b->district }}</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ $b->manager_name ?: '-' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                            {{ $b->phone ?: '-' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center; font-weight: 800; color: #4338CA;">
                            {{ $b->stores->count() }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center; font-weight: 800; color: #0F5132;">
                            {{ $b->treasuries->count() }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($b->is_active)
                                <span style="background: #D1E7DD; color: #0F5132; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">نشط</span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">معطل</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <a href="{{ route('branches.branches.show', $b->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="الملف الشامل">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('branches.branches.edit', $b->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: #FEF3C7; color: #D97706; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;" title="تعديل">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if(!$b->is_main)
                                <form method="POST" action="{{ route('branches.branches.destroy', $b->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفرع؟')" style="display: inline;">
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
                        <td colspan="10" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-network-wired" style="font-size: 36px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                            لا توجد فروع مسجلة. اضغط "إضافة فرع / مركز جديد" للبدء.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($branches->hasPages())
            <div style="margin-top: 20px;">
                {{ $branches->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
