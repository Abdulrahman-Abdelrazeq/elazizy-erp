@extends('layouts.app')

@section('title', 'سجل أوامر وفحوصات الجودة (Quality Inspections)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-clipboard-check"></i>
                </span>
                سجل أوامر وفحوصات الجودة (Quality Inspections Registry)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تتبع نتائج فحص المواد الخام الواردة، مراحل التشغيل والتصنيع، المنتجات التامة، واختبارات مواقع المشاريع
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('quality.templates.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: var(--primary);"></i>
                نماذج ومعايير الفحص
            </a>
            <a href="{{ route('quality.ncrs.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #DC2626;"></i>
                عدم المطابقة (NCR)
            </a>
            <a href="{{ route('quality.capas.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-shield-halved" style="color: #2563EB;"></i>
                الإجراءات التصحيحية CAPA
            </a>
            <a href="{{ route('quality.inspections.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-plus-circle"></i>
                إصدار أمر فحص جديد
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي أوامر الفحص</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--text-main);">{{ number_format($stats['total']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">مسجلة بالنظام</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">مطابق ومعتمد (Passed)</span>
                <span style="font-size: 20px; font-weight: 900; color: #10B981;">{{ number_format($stats['passed']) }}</span>
                <span style="font-size: 11px; color: #10B981; display: block; font-weight: 700;">إفراج للاستخدام والتسليم</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">مرفوض وغير مطابق (Rejected)</span>
                <span style="font-size: 20px; font-weight: 900; color: #DC2626;">{{ number_format($stats['rejected']) }}</span>
                <span style="font-size: 11px; color: #DC2626; display: block; font-weight: 700;">تم إنشاء تقرير NCR</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--accent-gold); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: var(--accent-gold); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">مقبول بتجاوز / استثناء</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--accent-gold);">{{ number_format($stats['conditional']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">سماحية معتمدة فندقياً</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #64748B; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #F1F5F9; color: #64748B; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">قيد الفحص والمراجعة</span>
                <span style="font-size: 20px; font-weight: 900; color: #64748B;">{{ number_format($stats['pending']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">بانتظار رصد المفتش</span>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="erp-card" style="padding: 16px;">
        <form method="GET" action="{{ route('quality.inspections.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)) 100px; gap: 12px; align-items: end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">بحث بالرقم أو الدفعة</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الفحص أو التشغيلة..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">نوع الفحص</label>
                <select name="type" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الأنواع</option>
                    <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>استلام مواد خام (Incoming)</option>
                    <option value="in_process" {{ request('type') == 'in_process' ? 'selected' : '' }}>أثناء التشغيل (In-Process)</option>
                    <option value="final_product" {{ request('type') == 'final_product' ? 'selected' : '' }}>منتج نهائي تام (Final Product)</option>
                    <option value="project_site" {{ request('type') == 'project_site' ? 'selected' : '' }}>موقع المشروع (Site Test)</option>
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">حالة النتيجة</label>
                <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع النتائج</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الفحص</option>
                    <option value="passed" {{ request('status') == 'passed' ? 'selected' : '' }}>مطابق ومعتمد</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    <option value="conditionally_passed" {{ request('status') == 'conditionally_passed' ? 'selected' : '' }}>مقبول بتجاوز</option>
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الفرع</label>
                <select name="branch_id" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الفروع</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; flex: 1;">
                    <i class="fa-solid fa-filter"></i>
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'type', 'status', 'branch_id']))
                    <a href="{{ route('quality.inspections.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); border-radius: 8px; padding: 9px 12px; color: var(--text-muted); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Inspections Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: var(--primary);"></i>
                أوامر وفحوصات الجودة المسجلة
            </h3>
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                عرض {{ $inspections->firstItem() ?? 0 }} إلى {{ $inspections->lastItem() ?? 0 }} من أصل {{ $inspections->total() }} أمر فحص
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">رقم أمر الفحص</th>
                        <th style="padding: 12px 16px;">النوع والمرحلة</th>
                        <th style="padding: 12px 16px;">الصنف أو المشروع / المورد</th>
                        <th style="padding: 12px 16px;">رقم الدفعة / التشغيلة</th>
                        <th style="padding: 12px 16px;">المفتش والفرع</th>
                        <th style="padding: 12px 16px;">الكمية المفحوصة والمقبولة</th>
                        <th style="padding: 12px 16px;">الحالة والنتيجة</th>
                        <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inspections as $ins)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                <a href="{{ route('quality.inspections.show', $ins->id) }}" style="text-decoration: none; color: var(--primary);">
                                    {{ $ins->inspection_number }}
                                </a>
                                <div style="font-size: 11px; color: var(--text-muted); font-weight: normal; margin-top: 2px;">
                                    تاريخ: {{ $ins->inspection_date?->format('Y-m-d') }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-weight: 700; color: var(--text-main);">{{ $ins->type_label }}</span>
                                @if($ins->template)
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                        نموذج: {{ $ins->template->name }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($ins->item)
                                    <div style="font-weight: 700; color: var(--text-main);">{{ $ins->item->name }}</div>
                                @endif
                                @if($ins->project)
                                    <div style="font-size: 12px; color: var(--primary); font-weight: 600;">
                                        <i class="fa-solid fa-helmet-safety"></i> {{ $ins->project->name }}
                                    </div>
                                @endif
                                @if($ins->supplier)
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        المورد: {{ $ins->supplier->name }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; font-family: monospace; font-weight: 600;">
                                {{ $ins->batch_number ?? '-' }}
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 600; color: var(--text-main);">{{ $ins->inspector?->name ?? '-' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $ins->branch?->name ?? '-' }}</div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 700; color: var(--text-main);">
                                    إجمالي: {{ number_format($ins->total_quantity, 2) }}
                                </div>
                                <div style="font-size: 11px; color: #10B981; font-weight: 700;">
                                    مقبول: {{ number_format($ins->passed_quantity, 2) }}
                                    @if($ins->failed_quantity > 0)
                                        <span style="color: #DC2626; margin-right: 4px;">| مرفوض: {{ number_format($ins->failed_quantity, 2) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                @php
                                    $statusStyle = match($ins->status) {
                                        'passed'               => 'background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);',
                                        'rejected'             => 'background: rgba(220, 38, 38, 0.1); color: #DC2626; border: 1px solid rgba(220, 38, 38, 0.2);',
                                        'conditionally_passed' => 'background: rgba(217, 119, 6, 0.1); color: #D97706; border: 1px solid rgba(217, 119, 6, 0.2);',
                                        default                => 'background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0;',
                                    };
                                @endphp
                                <span style="display: inline-block; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $statusStyle !!}">
                                    {{ $ins->status_label }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <a href="{{ route('quality.inspections.show', $ins->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; text-decoration: none;" title="عرض وتقييم الفحص">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('quality.inspections.print', $ins->id) }}" target="_blank" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--text-main); display: flex; align-items: center; justify-content: center; text-decoration: none;" title="طباعة شهادة الفحص المعتمدة">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                <i class="fa-solid fa-clipboard-check" style="font-size: 40px; margin-bottom: 12px; color: #CBD5E1; display: block;"></i>
                                لا توجد أوامر فحص مسجلة. قم بإصدار أول أمر فحص جودة الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($inspections->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $inspections->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
