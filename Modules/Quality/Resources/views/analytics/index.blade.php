@extends('layouts.app')

@section('title', 'تحليلات ومؤشرات جودة العمليات (Quality Analytics & COPQ)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: var(--accent-gold); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-chart-pie"></i>
                </span>
                لوحة تحليلات الجودة ومؤشرات FPY & COPQ
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                متابعة معدلات الجودة من المرة الأولى، تكاليف رداءة الجودة، تصنيف العيوب (Pareto)، وبطاقة أداء جودة الموردين
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('quality.inspections.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-clipboard-check" style="color: var(--primary);"></i>
                سجل الفحوصات
            </a>
            <a href="{{ route('quality.ncrs.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #DC2626;"></i>
                عدم المطابقة (NCR)
            </a>
        </div>
    </div>

    <!-- Executive Metrics Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">معدل الجودة الأولي (FPY)</span>
                <span style="font-size: 22px; font-weight: 900; color: #10B981;">{{ $firstPassYield }}%</span>
            </div>
            <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 999px; overflow: hidden; margin-top: 8px;">
                <div style="width: {{ min(100, $firstPassYield) }}%; height: 100%; background: #10B981; border-radius: 999px;"></div>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">
                First-Pass Yield لجميع خطوط العمليات
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">تكلفة رداءة الجودة (COPQ)</div>
            <div style="font-size: 22px; font-weight: 900; color: #DC2626;">
                {{ number_format($totalCopq, 2) }} <small style="font-size: 12px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                خسائر الهدر وإعادة التشغيل (Scrap & Rework)
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">إجمالي الفحوصات المنفذة</div>
            <div style="font-size: 22px; font-weight: 900; color: var(--primary);">
                {{ number_format($totalInspections) }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                مطابق: {{ $passedCount }} | مرفوض: {{ $rejectedCount }} | بتجاوز: {{ $conditionalCount }}
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">فاعلية إجراءات CAPA</span>
                <span style="font-size: 22px; font-weight: 900; color: #2563EB;">{{ $capaEffectivenessRate }}%</span>
            </div>
            <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 999px; overflow: hidden; margin-top: 8px;">
                <div style="width: {{ min(100, $capaEffectivenessRate) }}%; height: 100%; background: #2563EB; border-radius: 999px;"></div>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">
                إجراءات أغلقت وثبتت فاعليتها
            </div>
        </div>
    </div>

    <!-- Charts & Pareto Breakdown Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <!-- Root Cause Pareto Distribution -->
        <div class="erp-card" style="padding: 20px; margin-bottom: 0;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-column"></i>
                تحليل باريتو للأسباب الجذرية للعيوب (Pareto Analysis)
            </h3>

            @php
                $totalDefects = array_sum($rootCauses);
            @endphp

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach([
                    'raw_material' => ['name' => 'المواد الخام والموردين (Raw Material)', 'color' => '#DC2626'],
                    'machinery'    => ['name' => 'الآلات وخطوط الإنتاج (Machinery)', 'color' => '#D97706'],
                    'workmanship'  => ['name' => 'العمالة والتشغيل البشري (Workmanship)', 'color' => '#2563EB'],
                    'environment'  => ['name' => 'بيئة العمل والتخزين (Environment)', 'color' => '#10B981'],
                    'design'       => ['name' => 'المخططات والمواصفات (Design)', 'color' => '#8B5CF6'],
                    'other'        => ['name' => 'أسباب متفرقة أخرى (Other)', 'color' => '#64748B'],
                ] as $key => $info)
                    @php
                        $cnt = $rootCauses[$key] ?? 0;
                        $pct = $totalDefects > 0 ? round(($cnt / $totalDefects) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; margin-bottom: 4px;">
                            <span>{{ $info['name'] }}</span>
                            <span><strong>{{ $cnt }}</strong> ({{ $pct }}%)</span>
                        </div>
                        <div style="width: 100%; height: 7px; background: #F1F5F9; border-radius: 999px; overflow: hidden;">
                            <div style="width: {{ $pct }}%; height: 100%; background: {{ $info['color'] }}; border-radius: 999px;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Defect Severity Breakdown -->
        <div class="erp-card" style="padding: 20px; margin-bottom: 0;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                توزيع خطورة العيوب وحالات عدم المطابقة
            </h3>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                <div style="background: #FEF2F2; border-right: 4px solid #DC2626; padding: 14px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 800; color: #991B1B; font-size: 13px;">عيوب حرجة وخطيرة (Critical Severity)</span>
                        <span style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ $severities['critical'] }}</span>
                    </div>
                    <div style="font-size: 11px; color: #991B1B; margin-top: 4px;">تتطلب إيقاف التشغيل فوراً وفتح إجراء CAPA عاجل</div>
                </div>

                <div style="background: #FFFBEB; border-right: 4px solid #D97706; padding: 14px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 800; color: #92400E; font-size: 13px;">عيوب رئيسية وجوهرية (Major Severity)</span>
                        <span style="font-size: 18px; font-weight: 900; color: #D97706;">{{ $severities['major'] }}</span>
                    </div>
                    <div style="font-size: 11px; color: #92400E; margin-top: 4px;">تؤثر على كفاءة المنتج وتستوجب إعادة التشغيل أو الإرجاع للمورد</div>
                </div>

                <div style="background: #F8FAFC; border-right: 4px solid #64748B; padding: 14px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 800; color: #334155; font-size: 13px;">عيوب ثانوية وبسيطة (Minor Severity)</span>
                        <span style="font-size: 18px; font-weight: 900; color: #64748B;">{{ $severities['minor'] }}</span>
                    </div>
                    <div style="font-size: 11px; color: #64748B; margin-top: 4px;">عيوب مظهرية طفيفة يتم قبولها باستثناء فني أو تصحيح بسيط</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier QC Scorecard -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); background: #F8FAFC;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-truck-ramp-box" style="color: var(--accent-gold);"></i>
                بطاقة تقييم جودة الموردين (Supplier Quality Scorecard)
            </h3>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F1F5F9; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">اسم المورد</th>
                        <th style="padding: 12px 16px;">إجمالي التوريدات المفحوصة</th>
                        <th style="padding: 12px 16px;">المقبول</th>
                        <th style="padding: 12px 16px;">المرفوض</th>
                        <th style="padding: 12px 16px; min-width: 140px;">معدل المطابقة والجودة</th>
                        <th style="padding: 12px 16px;">تصنيف المورد</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $sup)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 16px; font-weight: 800; color: var(--text-main);">
                                {{ $sup->supplier?->name ?? 'مورد غير مسجل' }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 700;">
                                {{ $sup->total_inspections }} فحص
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: #10B981;">
                                {{ $sup->passed_inspections }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: {{ $sup->rejected_inspections > 0 ? '#DC2626' : 'var(--text-muted)' }};">
                                {{ $sup->rejected_inspections }}
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; margin-bottom: 3px;">
                                    <span>{{ $sup->pass_rate }}%</span>
                                </div>
                                <div style="width: 100%; height: 6px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                    <div style="width: {{ $sup->pass_rate }}%; height: 100%; background: {{ $sup->pass_rate >= 90 ? '#10B981' : ($sup->pass_rate >= 75 ? 'var(--accent-gold)' : '#DC2626') }}; border-radius: 999px;"></div>
                                </div>
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($sup->pass_rate >= 90)
                                    <span style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                                        مورد معتمد فئة (A)
                                    </span>
                                @elseif($sup->pass_rate >= 75)
                                    <span style="background: rgba(217, 119, 6, 0.1); color: #D97706; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                                        مورد خاضع للرقابة (B)
                                    </span>
                                @else
                                    <span style="background: rgba(220, 38, 38, 0.1); color: #DC2626; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800;">
                                        مورد عالي الخطورة (C)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 30px; text-align: center; color: var(--text-muted);">
                                لا توجد فحوصات استلام لموردين مسجلة حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
