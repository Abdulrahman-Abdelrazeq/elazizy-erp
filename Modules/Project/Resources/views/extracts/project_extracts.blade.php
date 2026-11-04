@extends('layouts.app')

@section('title', 'مستخلصات مشروع: ' . $project->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                <a href="{{ route('projects.projects.index') }}" style="color: var(--primary); text-decoration: none;">المشاريع</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i>
                <a href="{{ route('projects.projects.show', $project->id) }}" style="color: var(--primary); text-decoration: none;">{{ $project->name }}</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px;"></i>
                <span>المستخلصات</span>
            </div>
            <h2 style="font-size: 20px; font-weight: 900; color: var(--text-main); margin: 0;">
                سجل المستخلصات لمشروع: {{ $project->name }} ({{ $project->code }})
            </h2>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('projects.projects.show', $project->id) }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-arrow-right"></i>
                بطاقة المشروع
            </a>
            <a href="{{ route('projects.extracts.create', $project->id) }}" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-plus-circle"></i>
                إنشاء مستخلص جاري
            </a>
        </div>
    </div>

    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">رقم المستخلص</th>
                        <th style="padding: 12px 16px;">النوع والفترة</th>
                        <th style="padding: 12px 16px;">أعمال الفترة الحالية</th>
                        <th style="padding: 12px 16px;">صافي المستحق</th>
                        <th style="padding: 12px 16px;">المسدد</th>
                        <th style="padding: 12px 16px;">الحالة</th>
                        <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($project->extracts as $extract)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                <a href="{{ route('projects.extracts.show', $extract->id) }}" style="text-decoration: none; color: var(--primary);">
                                    {{ $extract->extract_number }}
                                </a>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 700;">{{ $extract->extract_type_label }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">
                                    {{ $extract->period_from?->format('Y-m-d') }} إلى {{ $extract->period_to?->format('Y-m-d') }}
                                </div>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800;">{{ number_format($extract->current_gross_amount, 2) }} ر.س</td>
                            <td style="padding: 14px 16px; font-weight: 900; color: #2563EB;">{{ number_format($extract->net_amount, 2) }} ر.س</td>
                            <td style="padding: 14px 16px; font-weight: 800; color: #10B981;">{{ number_format($extract->paid_amount, 2) }} ر.س</td>
                            <td style="padding: 14px 16px;"><span style="font-weight: 800;">{{ $extract->status_label }}</span></td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <a href="{{ route('projects.extracts.show', $extract->id) }}" style="padding: 5px 10px; border-radius: 6px; background: rgba(37, 99, 235, 0.1); color: #2563EB; text-decoration: none; font-weight: 700; font-size: 12px;">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 30px; text-align: center; color: var(--text-muted);">لا توجد مستخلصات مسجلة لهذا المشروع.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
