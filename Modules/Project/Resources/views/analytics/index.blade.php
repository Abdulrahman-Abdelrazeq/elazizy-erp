@extends('layouts.app')

@section('title', 'تحليلات ومؤشرات أداء المشاريع (Project Analytics & EVM)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: var(--accent-gold); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
                لوحة تحليلات ومؤشرات أداء المشاريع (Contracting & EVM Analytics)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                متابعة مؤشرات القيمة المكتسبة، الموازنات التقديرية، أوامر التغيير، والتدفقات النقدية لمشاريع المقاولات
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('projects.projects.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-helmet-safety" style="color: var(--primary);"></i>
                دليل المشاريع
            </a>
            <a href="{{ route('projects.extracts.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-invoice-dollar" style="color: #2563EB;"></i>
                المستخلصات الجارية
            </a>
        </div>
    </div>

    <!-- Primary KPI Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">إجمالي العقود المعدلة</div>
            <div style="font-size: 20px; font-weight: 900; color: var(--text-main);">
                {{ number_format($totalRevisedContract, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--accent-gold); font-weight: 600; margin-top: 4px;">
                أصل العقود: {{ number_format($totalOriginalContract, 2) }} ر.س
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #9333EA;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">أوامر التغيير المعتمدة</div>
            <div style="font-size: 20px; font-weight: 900; color: #9333EA;">
                {{ $totalApprovedCO >= 0 ? '+' : '' }}{{ number_format($totalApprovedCO, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                صافي التعديلات على قيمة المشاريع
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">إجمالي المفوتر بالمستخلصات</div>
            <div style="font-size: 20px; font-weight: 900; color: #2563EB;">
                {{ number_format($totalBilled, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                مستخلصات معتمدة ومسددة
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">إجمالي التحصيل النقدي</div>
            <div style="font-size: 20px; font-weight: 900; color: #10B981;">
                {{ number_format($totalPaid, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: #DC2626; font-weight: 700; margin-top: 4px;">
                متبقي للتحصيل: {{ number_format($outstandingCollection, 2) }} ر.س
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 16px; background: #F8FAFC;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">محتجزات ضمان الأعمال (Retention)</span>
            <span style="font-size: 18px; font-weight: 900; color: var(--accent-gold);">{{ number_format($totalRetentionDeducted, 2) }} ر.س</span>
            <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">تُصرف عند التسليم النهائي للمشروع</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 16px; background: #F8FAFC;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">الاستقطاع من الدفعة المقدمة</span>
            <span style="font-size: 18px; font-weight: 900; color: #4338CA;">{{ number_format($totalAdvanceDeducted, 2) }} ر.س</span>
            <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">مسترد من المستخلصات الجارية</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 16px; background: #F8FAFC;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">متوسط نسبة إنجاز المحفظة</span>
            <span style="font-size: 18px; font-weight: 900; color: #059669;">{{ $avgCompletion }}%</span>
            <span style="font-size: 11px; color: var(--text-muted); display: block; margin-top: 2px;">محتسبة طبقاً لبنود المقايسة المسعرة</span>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 16px; background: #F8FAFC;">
            <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">توزيع حالات المشاريع</span>
            <div style="font-size: 12px; color: var(--text-main); font-weight: 700; display: flex; gap: 8px; margin-top: 4px; flex-wrap: wrap;">
                <span>تنفيذ: {{ $statusCounts['in_progress'] }}</span> |
                <span>تخطيط: {{ $statusCounts['planning'] }}</span> |
                <span>منتهي: {{ $statusCounts['completed'] }}</span>
            </div>
        </div>
    </div>

    <!-- Active Projects Performance Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); background: #F8FAFC;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-trophy" style="color: var(--accent-gold);"></i>
                تقرير الأداء المالي والفني للمشاريع النشطة
            </h3>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F1F5F9; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">كود واسم المشروع</th>
                        <th style="padding: 12px 16px;">العميل والفرع</th>
                        <th style="padding: 12px 16px;">القيمة المعدلة</th>
                        <th style="padding: 12px 16px; min-width: 140px;">نسبة الإنجاز</th>
                        <th style="padding: 12px 16px;">المفوتر</th>
                        <th style="padding: 12px 16px;">المحصل</th>
                        <th style="padding: 12px 16px;">المتبقي</th>
                        <th style="padding: 12px 16px;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px;">
                                <a href="{{ route('projects.projects.show', $p->id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none; font-size: 13px;">
                                    {{ $p->code }} - {{ $p->name }}
                                </a>
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                    {{ $p->boq_items_count }} بند مقايسة | {{ $p->milestones_count }} مرحلة | {{ $p->extracts_count }} مستخلص
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 700; color: var(--text-main);">{{ $p->client?->name ?? '-' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $p->branch?->name ?? '-' }}</div>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: var(--text-main);">
                                {{ number_format($p->revised_value, 2) }} <small style="font-size: 10px;">ر.س</small>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; margin-bottom: 3px;">
                                    <span>{{ $p->completion_percentage }}%</span>
                                </div>
                                <div style="width: 100%; height: 7px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                    <div style="width: {{ min(100, $p->completion_percentage) }}%; height: 100%; background: {{ $p->completion_percentage >= 100 ? '#10B981' : ($p->completion_percentage >= 50 ? 'var(--primary)' : 'var(--accent-gold)') }}; border-radius: 999px;"></div>
                                </div>
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: #2563EB;">
                                {{ number_format($p->total_billed_amount, 2) }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: #10B981;">
                                {{ number_format($p->total_paid_amount, 2) }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: {{ $p->remaining_due > 0 ? '#DC2626' : '#10B981' }};">
                                {{ number_format($p->remaining_due, 2) }}
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-size: 11px; font-weight: 800; padding: 4px 8px; border-radius: 6px; background: #F1F5F9; color: var(--text-main);">
                                    {{ $p->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding: 30px; text-align: center; color: var(--text-muted);">
                                لا توجد مشاريع مسجلة في النظام بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
