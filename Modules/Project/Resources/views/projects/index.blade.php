@extends('layouts.app')

@section('title', 'إدارة المشاريع وعقود المقاولات (Project Management)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-helmet-safety"></i>
                </span>
                إدارة المشاريع وعقود المقاولات (Projects & Contracting)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                متابعة عقود المقاولات، بنود المقايسات BOQ، أوامر التغيير، ونسب الإنجاز والمستخلصات الجارية والختامية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('projects.extracts.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice-dollar" style="color: var(--primary);"></i>
                المستخلصات الجارية
            </a>
            <a href="{{ route('projects.analytics.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-line" style="color: var(--accent-gold);"></i>
                مؤشرات الأداء EVM
            </a>
            <a href="{{ route('projects.projects.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-plus-circle"></i>
                تسجيل مشروع وعقد جديد
            </a>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-trowel-bricks"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المشاريع</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--text-main);">{{ number_format($stats['total_projects']) }}</span>
                <span style="font-size: 11px; color: var(--primary); display: block; font-weight: 600;">{{ $stats['in_progress_count'] }} قيد التنفيذ حالياً</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--accent-gold); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: var(--accent-gold); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-file-contract"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">قيمة العقود المعدلة</span>
                <span style="font-size: 18px; font-weight: 900; color: var(--text-main);">{{ number_format($stats['total_contract_val'], 2) }} <small style="font-size: 11px;">ر.س</small></span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">تشمل أوامر التغيير</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-money-check-dollar"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المفوتر بالمستخلصات</span>
                <span style="font-size: 18px; font-weight: 900; color: #2563EB;">{{ number_format($stats['total_billed_val'], 2) }} <small style="font-size: 11px;">ر.س</small></span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">مستخلصات معتمدة ومسددة</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-chart-pie"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">متوسط نسبة الإنجاز</span>
                <span style="font-size: 20px; font-weight: 900; color: #10B981;">{{ $stats['avg_completion'] }}%</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">محتسبة طبقاً لبنود المقايسة</span>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="erp-card" style="padding: 16px;">
        <form method="GET" action="{{ route('projects.projects.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 12px; align-items: end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">بحث بالاسم أو الكود</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المشروع أو العقد..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الفرع المنفذ</label>
                <select name="branch_id" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الفروع</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">العميل / المالك</label>
                <select name="client_id" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع العملاء</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ request('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">حالة المشروع</label>
                <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الحالات</option>
                    <option value="planning" {{ request('status') == 'planning' ? 'selected' : '' }}>تخطيط وتجهيز</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="delivered_primary" {{ request('status') == 'delivered_primary' ? 'selected' : '' }}>تسليم ابتدائي</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>منتهي ومسلّم</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق مؤقتاً</option>
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; flex: 1;">
                    <i class="fa-solid fa-filter"></i>
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'branch_id', 'client_id', 'status']))
                    <a href="{{ route('projects.projects.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); border-radius: 8px; padding: 9px 12px; color: var(--text-muted); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Projects Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: var(--primary);"></i>
                دليل المشاريع وسجل العقود
            </h3>
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                عرض {{ $projects->firstItem() ?? 0 }} إلى {{ $projects->lastItem() ?? 0 }} من أصل {{ $projects->total() }} مشروع
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">كود المشروع</th>
                        <th style="padding: 12px 16px;">اسم المشروع والعميل</th>
                        <th style="padding: 12px 16px;">الفرع ومدير المشروع</th>
                        <th style="padding: 12px 16px;">قيمة العقد المعدلة</th>
                        <th style="padding: 12px 16px; min-width: 140px;">نسبة الإنجاز</th>
                        <th style="padding: 12px 16px;">المفوتر والمحصل</th>
                        <th style="padding: 12px 16px;">الحالة</th>
                        <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 800; color: var(--primary);">
                                <a href="{{ route('projects.projects.show', $project->id) }}" style="text-decoration: none; color: var(--primary); font-family: monospace; font-size: 13px;">
                                    {{ $project->code }}
                                </a>
                                @if($project->contract_number)
                                    <div style="font-size: 11px; color: var(--text-muted); font-weight: 500;">عقد: {{ $project->contract_number }}</div>
                                @endif
                            </td>
                            <td style="padding: 14px 16px;">
                                <a href="{{ route('projects.projects.show', $project->id) }}" style="font-weight: 800; color: var(--text-main); text-decoration: none; display: block;">
                                    {{ $project->name }}
                                </a>
                                <div style="font-size: 12px; color: var(--text-muted); display: flex; align-items: center; gap: 6px; margin-top: 3px;">
                                    <i class="fa-solid fa-user-tie" style="font-size: 10px; color: var(--accent-gold);"></i>
                                    {{ $project->client?->name ?? 'غير محدد' }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 600; color: var(--text-main);">{{ $project->branch?->name ?? '-' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                    {{ $project->projectManager?->name ?? 'لم يعين مدير' }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: var(--text-main);">
                                {{ number_format($project->revised_value, 2) }} <small style="font-size: 10px; color: var(--text-muted);">ر.س</small>
                                @if($project->contract_value != $project->revised_value)
                                    <div style="font-size: 11px; color: #D97706; font-weight: 600;">
                                        أصل: {{ number_format($project->contract_value, 2) }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; margin-bottom: 4px;">
                                    <span>{{ $project->completion_percentage }}%</span>
                                </div>
                                <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                    <div style="width: {{ min(100, $project->completion_percentage) }}%; height: 100%; background: {{ $project->completion_percentage >= 100 ? '#10B981' : ($project->completion_percentage >= 50 ? 'var(--primary)' : 'var(--accent-gold)') }}; border-radius: 999px;"></div>
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-size: 12px; font-weight: 700; color: #2563EB;">
                                    مستخلص: {{ number_format($project->total_billed_amount, 2) }}
                                </div>
                                <div style="font-size: 11px; font-weight: 600; color: #10B981; margin-top: 2px;">
                                    محصل: {{ number_format($project->total_paid_amount, 2) }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                @php
                                    $badgeStyle = match($project->status) {
                                        'in_progress'       => 'background: rgba(15, 81, 50, 0.1); color: var(--primary); border: 1px solid rgba(15, 81, 50, 0.2);',
                                        'completed'         => 'background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);',
                                        'delivered_primary' => 'background: rgba(37, 99, 235, 0.1); color: #2563EB; border: 1px solid rgba(37, 99, 235, 0.2);',
                                        'planning'          => 'background: rgba(217, 119, 6, 0.1); color: #D97706; border: 1px solid rgba(217, 119, 6, 0.2);',
                                        'suspended'         => 'background: rgba(220, 38, 38, 0.1); color: #DC2626; border: 1px solid rgba(220, 38, 38, 0.2);',
                                        default             => 'background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0;',
                                    };
                                @endphp
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; {!! $badgeStyle !!}">
                                    {{ $project->status_label }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <a href="{{ route('projects.projects.show', $project->id) }}" title="عرض بطاقة المشروع 360" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.extracts.create', $project->id) }}" title="إنشاء مستخلص جاري" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(37, 99, 235, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-receipt"></i>
                                    </a>
                                    <a href="{{ route('projects.projects.edit', $project->id) }}" title="تعديل العقد" style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; color: var(--text-main); display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                <i class="fa-solid fa-helmet-safety" style="font-size: 40px; margin-bottom: 12px; color: #CBD5E1; display: block;"></i>
                                لا توجد مشاريع مسجلة مطابقة لخيارات البحث.
                                <div style="margin-top: 12px;">
                                    <a href="{{ route('projects.projects.create') }}" class="btn-primary-custom" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; text-decoration: none; display: inline-block;">
                                        تسجيل أول مشروع الآن
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $projects->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
