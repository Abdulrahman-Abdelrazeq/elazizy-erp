@extends('layouts.app')

@section('title', 'سجل وبطاقات الأصول الثابتة (Fixed Assets Registry)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-building-columns"></i>
                </span>
                سجل وبطاقات الأصول الثابتة (Fixed Assets Registry)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدارة محفظة الأصول والمعدات، تتبع العهد والمواقع، حساب الإهلاك، وصافي القيمة الدفترية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('assets.categories.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-shapes" style="color: var(--primary);"></i>
                تصنيفات الأصول
            </a>
            <a href="{{ route('assets.depreciations.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-calculator" style="color: var(--accent-gold);"></i>
                قيود الإهلاك
            </a>
            <a href="{{ route('assets.disposals.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-recycle" style="color: #DC2626;"></i>
                الاستبعاد والتكهين
            </a>
            <a href="{{ route('assets.reports.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-pie" style="color: #4338CA;"></i>
                تقارير الأصول
            </a>
            <a href="{{ route('assets.assets.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-plus-circle"></i>
                تسجيل أصل جديد
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الأصول</span>
                <strong style="font-size: 19px; font-weight: 900; color: var(--primary);">{{ $stats['total_count'] }} أصل</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أصول نشطة بالخدمة</span>
                <strong style="font-size: 19px; font-weight: 900; color: #0F5132;">{{ $stats['active_count'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">التكلفة التاريخية الإجمالية</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ number_format($stats['total_purchase_cost'], 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #EF4444; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #EF4444; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-arrow-trend-down"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مجمع الإهلاك المتراكم</span>
                <strong style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ number_format($stats['total_depreciated'], 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">صافي القيمة الدفترية</span>
                <strong style="font-size: 18px; font-weight: 900; color: #4338CA;">{{ number_format($stats['total_book_value'], 2) }} ر.س</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('assets.assets.index') }}" style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">بحث سريع (اسم الأصل، الرقم التسلسلي، الكود، الباركود)</label>
                <input type="text" name="search" class="erp-input" value="{{ request('search') }}" placeholder="اكتب للبحث...">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الفرع</label>
                <select name="branch_id" class="erp-input">
                    <option value="">جميع الفروع</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">تصنيف الأصل</label>
                <select name="category_id" class="erp-input">
                    <option value="">جميع التصنيفات</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الحالة التشغيلية</label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط بالخدمة</option>
                    <option value="in_maintenance" {{ request('status') === 'in_maintenance' ? 'selected' : '' }}>تحت الصيانة</option>
                    <option value="fully_depreciated" {{ request('status') === 'fully_depreciated' ? 'selected' : '' }}>مكتمل الإهلاك دفترياً</option>
                    <option value="disposed" {{ request('status') === 'disposed' ? 'selected' : '' }}>مستبعد / مكهّن</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>مباع</option>
                </select>
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; font-size: 13px; font-weight: 800; justify-content: center;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>
        </form>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div style="background: #D1E7DD; border: 1px solid #A3CFBB; color: #0F5132; padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
        <i class="fa-solid fa-circle-check" style="margin-left: 8px;"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-left: 8px;"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Assets Registry Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-table-list" style="color: var(--primary); margin-left: 6px;"></i>
                سجل الأصول والمعدات ({{ $assets->total() }})
            </h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">رقم الأصل</th>
                        <th style="padding: 12px 16px;">اسم ومواصفات الأصل</th>
                        <th style="padding: 12px 16px;">التصنيف والفرع</th>
                        <th style="padding: 12px 16px;">الموظف العهدة / الموقع</th>
                        <th style="padding: 12px 16px;">تاريخ الشراء</th>
                        <th style="padding: 12px 16px;">التكلفة التاريخية</th>
                        <th style="padding: 12px 16px;">مجمع الإهلاك</th>
                        <th style="padding: 12px 16px;">صافي القيمة الدفترية</th>
                        <th style="padding: 12px 16px; text-align: center;">الحالة</th>
                        <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $ast)
                    <tr style="border-bottom: 1px solid #EDF2F7; transition: background 0.2s ease;">
                        <td style="padding: 14px 16px;">
                            <a href="{{ route('assets.assets.show', $ast->id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-cube"></i>
                                {{ $ast->asset_number }}
                            </a>
                        </td>
                        <td style="padding: 14px 16px;">
                            <strong style="color: var(--text-main); display: block; font-size: 13.5px;">{{ $ast->name }}</strong>
                            <small style="color: var(--text-muted); font-family: monospace;">
                                @if($ast->serial_number) S/N: {{ $ast->serial_number }} @endif
                                @if($ast->model_number) | الموديل: {{ $ast->model_number }} @endif
                            </small>
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="font-weight: 700; color: var(--text-main); display: block;">{{ $ast->category?->name ?? '—' }}</span>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-network-wired"></i> {{ $ast->branch?->name ?? '—' }}</small>
                        </td>
                        <td style="padding: 14px 16px; font-size: 12.5px;">
                            @if($ast->custodian)
                                <strong style="color: var(--text-main); display: block;"><i class="fa-solid fa-user-shield" style="color: var(--accent-gold);"></i> {{ $ast->custodian->name }}</strong>
                            @else
                                <span style="color: var(--text-muted);">عهدة عامة</span>
                            @endif
                            @if($ast->location)
                                <small style="color: var(--text-muted);"><i class="fa-solid fa-location-dot"></i> {{ $ast->location->name }}</small>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; color: var(--text-main); font-weight: 600;">
                            {{ $ast->purchase_date ? $ast->purchase_date->format('Y-m-d') : '—' }}
                        </td>
                        <td style="padding: 14px 16px; font-weight: 800; color: var(--text-main);">
                            {{ number_format($ast->purchase_cost, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; font-weight: 700; color: #DC2626;">
                            {{ number_format($ast->total_depreciated_amount, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; font-weight: 900; color: #0F5132;">
                            {{ number_format($ast->current_book_value, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            @if($ast->status === 'active')
                                <span style="background: #D1E7DD; color: #0F5132; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-check"></i> نشط
                                </span>
                            @elseif($ast->status === 'in_maintenance')
                                <span style="background: #FEF3C7; color: #D97706; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-wrench"></i> صيانة
                                </span>
                            @elseif($ast->status === 'fully_depreciated')
                                <span style="background: #E0E7FF; color: #4338CA; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-flag-checkered"></i> مكتمل الإهلاك
                                </span>
                            @elseif($ast->status === 'disposed')
                                <span style="background: #FEE2E2; color: #DC2626; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-trash"></i> مستبعد
                                </span>
                            @elseif($ast->status === 'sold')
                                <span style="background: #E2E8F0; color: #475569; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-handshake"></i> مباع
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('assets.assets.show', $ast->id) }}" title="عرض بطاقة الأصل" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('assets.assets.barcode', $ast->id) }}" target="_blank" title="طباعة ملصق الباركود" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: #4338CA; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                    <i class="fa-solid fa-barcode"></i>
                                </a>
                                <a href="{{ route('assets.assets.edit', $ast->id) }}" title="تعديل" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--accent-gold); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                @if(in_array($ast->status, ['active', 'in_maintenance', 'fully_depreciated']))
                                    <a href="{{ route('assets.disposals.create', ['asset_id' => $ast->id]) }}" title="استبعاد أو بيع" style="width: 32px; height: 32px; border-radius: 8px; background: #FEF3C7; border: 1px solid #FDE68A; color: #B45309; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-recycle"></i>
                                    </a>
                                @endif
                                @if($ast->depreciations->count() == 0 && !$ast->disposal)
                                    <form action="{{ route('assets.assets.destroy', $ast->id) }}" method="POST" onsubmit="return confirm('تأكيد حذف بطاقة الأصل؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="حذف" style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-building-columns" style="font-size: 40px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                            لا توجد أصول ثابتة مسجلة حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $assets->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
