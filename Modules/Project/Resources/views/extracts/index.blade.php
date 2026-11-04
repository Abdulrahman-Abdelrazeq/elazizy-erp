@extends('layouts.app')

@section('title', 'سجل المستخلصات الجارية والختامية (Contractor Extracts)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: #2563EB; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </span>
                سجل المستخلصات الجارية والختامية (Progressive Billing & Extracts)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إصدار ومتابعة مستخلصات عقود المقاولات، تتبع استقطاعات الدفعة المقدمة والضمان، والتحصيل المالي
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('projects.projects.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-helmet-safety" style="color: var(--primary);"></i>
                دليل المشاريع
            </a>
            <a href="{{ route('projects.analytics.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-line" style="color: var(--accent-gold);"></i>
                تحليلات الأداء
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المستخلصات</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--text-main);">{{ number_format($stats['total_extracts']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">مسودة / معتمد / مسدد</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي قيمة الأعمال المنجزة</span>
                <span style="font-size: 18px; font-weight: 900; color: var(--primary);">{{ number_format($stats['total_gross'], 2) }} <small style="font-size: 11px;">ر.س</small></span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">قبل الاستقطاعات والضريبة</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">المحصل الفعلي</span>
                <span style="font-size: 18px; font-weight: 900; color: #10B981;">{{ number_format($stats['total_paid'], 2) }} <small style="font-size: 11px;">ر.س</small></span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">مدفوعات واردة للخزينة</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">مستحقات معتمدة قيد التحصيل</span>
                <span style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ number_format($stats['pending_payment'], 2) }} <small style="font-size: 11px;">ر.س</small></span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">مستخلصات معتمدة لم تُسدد بعد</span>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="erp-card" style="padding: 16px;">
        <form method="GET" action="{{ route('projects.extracts.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 12px; align-items: end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">بحث بالرقم أو الاستشاري</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم المستخلص، اسم المشروع..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">المشروع</label>
                <select name="project_id" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع المشاريع</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">نوع المستخلص</label>
                <select name="extract_type" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الأنواع</option>
                    <option value="advance" {{ request('extract_type') == 'advance' ? 'selected' : '' }}>دفعة مقدمة</option>
                    <option value="progress" {{ request('extract_type') == 'progress' ? 'selected' : '' }}>مستخلص جاري</option>
                    <option value="final" {{ request('extract_type') == 'final' ? 'selected' : '' }}>مستخلص ختامي</option>
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الحالة</label>
                <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>مرفوع للاستشاري</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد للصرف</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مسدد بالكامل</option>
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; flex: 1;">
                    <i class="fa-solid fa-filter"></i>
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'project_id', 'extract_type', 'status']))
                    <a href="{{ route('projects.extracts.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); border-radius: 8px; padding: 9px 12px; color: var(--text-muted); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Extracts Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: #2563EB;"></i>
                بيان المستخلصات التعاقدية
            </h3>
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                عرض {{ $extracts->firstItem() ?? 0 }} إلى {{ $extracts->lastItem() ?? 0 }} من أصل {{ $extracts->total() }} مستخلص
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">رقم المستخلص</th>
                        <th style="padding: 12px 16px;">المشروع والعميل</th>
                        <th style="padding: 12px 16px;">النوع والفترة</th>
                        <th style="padding: 12px 16px;">أعمال الفترة الحالية</th>
                        <th style="padding: 12px 16px;">صافي المستحق (شامل الضريبة)</th>
                        <th style="padding: 12px 16px;">المسدد</th>
                        <th style="padding: 12px 16px;">الحالة</th>
                        <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($extracts as $extract)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                <a href="{{ route('projects.extracts.show', $extract->id) }}" style="text-decoration: none; color: var(--primary);">
                                    {{ $extract->extract_number }}
                                </a>
                                <div style="font-size: 11px; color: var(--text-muted); font-weight: normal; margin-top: 2px;">
                                    بتاريخ {{ $extract->issue_date?->format('Y-m-d') }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <a href="{{ route('projects.projects.show', $extract->project_id) }}" style="font-weight: 800; color: var(--text-main); text-decoration: none; display: block;">
                                    {{ $extract->project?->name ?? '-' }}
                                </a>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                    {{ $extract->project?->client?->name ?? 'غير محدد' }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-weight: 700; color: var(--text-main);">{{ $extract->extract_type_label }}</span>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                    من {{ $extract->period_from?->format('Y-m-d') }} إلى {{ $extract->period_to?->format('Y-m-d') }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: var(--text-main);">
                                {{ number_format($extract->current_gross_amount, 2) }} <small style="font-size: 10px; color: var(--text-muted);">ر.س</small>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 900; color: #2563EB;">
                                {{ number_format($extract->net_amount, 2) }} <small style="font-size: 10px; color: var(--text-muted);">ر.س</small>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: #10B981;">
                                {{ number_format($extract->paid_amount, 2) }} <small style="font-size: 10px; color: var(--text-muted);">ر.س</small>
                                @if($extract->paid_amount < $extract->net_amount && in_array($extract->status, ['approved', 'paid']))
                                    <div style="font-size: 11px; color: #DC2626; font-weight: 600;">
                                        متبقي: {{ number_format(max(0, $extract->net_amount - $extract->paid_amount), 2) }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px 16px;">
                                @php
                                    $extStyle = match($extract->status) {
                                        'paid'      => 'background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);',
                                        'approved'  => 'background: rgba(37, 99, 235, 0.1); color: #2563EB; border: 1px solid rgba(37, 99, 235, 0.2);',
                                        'submitted' => 'background: rgba(217, 119, 6, 0.1); color: #D97706; border: 1px solid rgba(217, 119, 6, 0.2);',
                                        default     => 'background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0;',
                                    };
                                @endphp
                                <span style="display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $extStyle !!}">
                                    {{ $extract->status_label }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <a href="{{ route('projects.extracts.show', $extract->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(37, 99, 235, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center; text-decoration: none;" title="عرض المستخلص">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('projects.extracts.print', $extract->id) }}" target="_blank" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--text-main); display: flex; align-items: center; justify-content: center; text-decoration: none;" title="طباعة شهادة المستخلص">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                <i class="fa-solid fa-file-invoice-dollar" style="font-size: 40px; margin-bottom: 12px; color: #CBD5E1; display: block;"></i>
                                لا توجد مستخلصات مطابقة لخيارات البحث.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($extracts->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $extracts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
