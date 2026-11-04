@extends('layouts.app')

@section('title', 'تقارير حالات عدم المطابقة (Non-Conformance Reports - NCR)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #DC2626; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
                تقارير حالات عدم المطابقة والعيوب (NCRs)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                حصر العيوب الفنية والانحرافات عن المواصفات، تحليل الأسباب الجذرية، وتحديد قرارات التصرف وإعادة التشغيل أو الإرجاع
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('quality.inspections.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-clipboard-check" style="color: var(--primary);"></i>
                سجل الفحوصات
            </a>
            <a href="{{ route('quality.capas.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-shield-halved" style="color: #2563EB;"></i>
                الإجراءات التصحيحية CAPA
            </a>
            <a href="{{ route('quality.ncrs.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none; background: #DC2626;">
                <i class="fa-solid fa-plus-circle"></i>
                تسجيل تقرير عدم مطابقة (NCR)
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي تقارير NCR</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--text-main);">{{ number_format($stats['total']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">حالات عدم مطابقة</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--accent-gold); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: var(--accent-gold); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hourglass-start"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">حالات مفتوحة قيد المعالجة</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--accent-gold);">{{ number_format($stats['open'] + $stats['under_invest']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">تتطلب اتخاذ قرار التصرف</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #991B1B; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(153, 27, 27, 0.1); color: #991B1B; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-skull-crossbones"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">عيوب حرجة (Critical)</span>
                <span style="font-size: 20px; font-weight: 900; color: #991B1B;">{{ number_format($stats['critical']) }}</span>
                <span style="font-size: 11px; color: #991B1B; display: block; font-weight: 700;">أعلى درجات الخطورة</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">حالات مغلقة ومحققة</span>
                <span style="font-size: 20px; font-weight: 900; color: #10B981;">{{ number_format($stats['closed']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">تم تنفيذ قرار التصرف</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #6366F1; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(99, 102, 241, 0.1); color: #6366F1; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">تكلفة الهدر والعيوب (COPQ)</span>
                <span style="font-size: 18px; font-weight: 900; color: #6366F1;">{{ number_format($stats['total_losses'], 2) }} <small style="font-size: 11px;">ر.س</small></span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">خسائر تقديرية لعدم المطابقة</span>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="erp-card" style="padding: 16px;">
        <form method="GET" action="{{ route('quality.ncrs.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 12px; align-items: end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">بحث بالرقم أو العنوان</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم NCR أو بيان العيب..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">درجة الخطورة</label>
                <select name="severity" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع درجات الخطورة</option>
                    <option value="critical" {{ request('severity') == 'critical' ? 'selected' : '' }}>حرج (Critical)</option>
                    <option value="major" {{ request('severity') == 'major' ? 'selected' : '' }}>رئيسي (Major)</option>
                    <option value="minor" {{ request('severity') == 'minor' ? 'selected' : '' }}>ثانوي (Minor)</option>
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">السبب الجذري</label>
                <select name="root_cause_category" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الأسباب</option>
                    <option value="raw_material" {{ request('root_cause_category') == 'raw_material' ? 'selected' : '' }}>مواد خام وموردين</option>
                    <option value="machinery" {{ request('root_cause_category') == 'machinery' ? 'selected' : '' }}>آلات ومعدات</option>
                    <option value="workmanship" {{ request('root_cause_category') == 'workmanship' ? 'selected' : '' }}>عمالة وتشغيل بشري</option>
                    <option value="environment" {{ request('root_cause_category') == 'environment' ? 'selected' : '' }}>بيئة وتخزين</option>
                    <option value="design" {{ request('root_cause_category') == 'design' ? 'selected' : '' }}>مخططات وتصميم</option>
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الحالة</label>
                <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الحالات</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوح</option>
                    <option value="under_investigation" {{ request('status') == 'under_investigation' ? 'selected' : '' }}>قيد التحقيق</option>
                    <option value="action_taken" {{ request('status') == 'action_taken' ? 'selected' : '' }}>تم اتخاذ الإجراء</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; flex: 1;">
                    <i class="fa-solid fa-filter"></i>
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'severity', 'root_cause_category', 'status']))
                    <a href="{{ route('quality.ncrs.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); border-radius: 8px; padding: 9px 12px; color: var(--text-muted); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- NCRs Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #DC2626;"></i>
                سجل حالات عدم المطابقة والعيوب الفنية
            </h3>
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                عرض {{ $ncrs->firstItem() ?? 0 }} إلى {{ $ncrs->lastItem() ?? 0 }} من أصل {{ $ncrs->total() }} تقرير
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">رقم التقرير NCR</th>
                        <th style="padding: 12px 16px;">عنوان وموضوع الخلل</th>
                        <th style="padding: 12px 16px;">الصنف / الجهة المعنية</th>
                        <th style="padding: 12px 16px;">درجة الخطورة</th>
                        <th style="padding: 12px 16px;">السبب الجذري</th>
                        <th style="padding: 12px 16px;">الكمية المعيبة</th>
                        <th style="padding: 12px 16px;">قرار التصرف</th>
                        <th style="padding: 12px 16px;">الحالة</th>
                        <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ncrs as $ncr)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: #DC2626;">
                                <a href="{{ route('quality.ncrs.show', $ncr->id) }}" style="text-decoration: none; color: #DC2626;">
                                    {{ $ncr->ncr_number }}
                                </a>
                                <div style="font-size: 11px; color: var(--text-muted); font-weight: normal; margin-top: 2px;">
                                    بتاريخ: {{ $ncr->created_at?->format('Y-m-d') }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <a href="{{ route('quality.ncrs.show', $ncr->id) }}" style="font-weight: 800; color: var(--text-main); text-decoration: none; display: block;">
                                    {{ $ncr->title }}
                                </a>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                    {{ Str::limit($ncr->description, 50) }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($ncr->item)
                                    <div style="font-weight: 700;">{{ $ncr->item->name }}</div>
                                @endif
                                @if($ncr->supplier)
                                    <div style="font-size: 11px; color: var(--text-muted);">المورد: {{ $ncr->supplier->name }}</div>
                                @elseif($ncr->project)
                                    <div style="font-size: 11px; color: var(--primary);">مشروع: {{ $ncr->project->name }}</div>
                                @endif
                            </td>
                            <td style="padding: 14px 16px;">
                                @php
                                    $sevBadge = match($ncr->severity) {
                                        'critical' => 'background: rgba(220, 38, 38, 0.1); color: #991B1B; border: 1px solid rgba(220, 38, 38, 0.3);',
                                        'major'    => 'background: rgba(217, 119, 6, 0.1); color: #B45309; border: 1px solid rgba(217, 119, 6, 0.3);',
                                        default    => 'background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;',
                                    };
                                @endphp
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $sevBadge !!}">
                                    {{ $ncr->severity_label }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; font-size: 12px; color: var(--text-muted);">
                                {{ $ncr->root_cause_label }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: #DC2626;">
                                {{ number_format($ncr->defect_quantity, 2) }}
                            </td>
                            <td style="padding: 14px 16px;">
                                @php
                                    $dispBadge = match($ncr->disposition) {
                                        'scrap'            => 'background: #FEE2E2; color: #991B1B;',
                                        'rework'           => 'background: #FEF3C7; color: #92400E;',
                                        'return_to_vendor' => 'background: #E0E7FF; color: #3730A3;',
                                        'use_as_is'        => 'background: #ECFDF5; color: #065F46;',
                                        default            => 'background: #F1F5F9; color: #64748B;',
                                    };
                                @endphp
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; {!! $dispBadge !!}">
                                    {{ $ncr->disposition_label }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: #F1F5F9; color: var(--text-main);">
                                    {{ $ncr->status_label }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <a href="{{ route('quality.ncrs.show', $ncr->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(220, 38, 38, 0.1); color: #DC2626; display: flex; align-items: center; justify-content: center; text-decoration: none;" title="عرض وتحديد قرار التصرف">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                <i class="fa-solid fa-circle-check" style="font-size: 40px; margin-bottom: 12px; color: #10B981; display: block;"></i>
                                لا توجد حالات عدم مطابقة مسجلة. خطوط الإنتاج والتوريدات مطابقة للمواصفات!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ncrs->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $ncrs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
