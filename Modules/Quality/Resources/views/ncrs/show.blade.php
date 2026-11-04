@extends('layouts.app')

@section('title', 'تقرير عدم مطابقة: ' . $ncr->ncr_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px; max-width: 1200px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <a href="{{ route('quality.ncrs.index') }}" style="color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 700;">تقارير عدم المطابقة</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px; color: var(--text-muted);"></i>
                <span style="font-family: monospace; font-size: 13px; font-weight: 800; background: #FEE2E2; color: #991B1B; padding: 2px 8px; border-radius: 6px;">{{ $ncr->ncr_number }}</span>
                @php
                    $sevBadge = match($ncr->severity) {
                        'critical' => 'background: rgba(220, 38, 38, 0.1); color: #991B1B; border: 1px solid rgba(220, 38, 38, 0.3);',
                        'major'    => 'background: rgba(217, 119, 6, 0.1); color: #B45309; border: 1px solid rgba(217, 119, 6, 0.3);',
                        default    => 'background: #F1F5F9; color: #475569; border: 1px solid #E2E8F0;',
                    };
                @endphp
                <span style="display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $sevBadge !!}">
                    درجة الخطورة: {{ $ncr->severity_label }}
                </span>
                <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: #F1F5F9; color: var(--text-main);">
                    الحالة: {{ $ncr->status_label }}
                </span>
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 38px; height: 38px; border-radius: 10px; background: rgba(220, 38, 38, 0.1); color: #DC2626; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
                {{ $ncr->title }}
            </h2>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 6px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <span><i class="fa-solid fa-calendar-day"></i> <strong>تاريخ التسجيل:</strong> {{ $ncr->created_at?->format('Y-m-d') }}</span>
                <span><i class="fa-solid fa-code-branch"></i> <strong>الفرع:</strong> {{ $ncr->branch?->name }}</span>
                @if($ncr->reportedBy)
                    <span><i class="fa-solid fa-user-pen"></i> <strong>المسجل:</strong> {{ $ncr->reportedBy->name }}</span>
                @endif
                @if($ncr->inspection)
                    <span><i class="fa-solid fa-clipboard-check" style="color: var(--primary);"></i> أمر الفحص: <a href="{{ route('quality.inspections.show', $ncr->inspection_id) }}" style="color: var(--primary); font-family: monospace;">{{ $ncr->inspection->inspection_number }}</a></span>
                @endif
            </div>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <button type="button" onclick="document.getElementById('disposition-modal').style.display='flex'" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">
                <i class="fa-solid fa-gavel"></i>
                تحديد / تحديث قرار التصرف
            </button>
            <button type="button" onclick="document.getElementById('add-capa-modal').style.display='flex'" style="background: #2563EB; color: #FFFFFF; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">
                <i class="fa-solid fa-shield-halved"></i>
                إضافة إجراء تصحيحي / وقائي (CAPA)
            </button>
            @if($ncr->status !== 'closed')
                <form method="POST" action="{{ route('quality.ncrs.close', $ncr->id) }}" onsubmit="return confirm('هل أنت متأكد من إغلاق تقرير عدم المطابقة رسمياً بعد استيفاء الإجراءات؟');" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #10B981; color: #FFFFFF; padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">
                        <i class="fa-solid fa-lock"></i>
                        إغلاق التقرير رسمياً
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">الكمية المعيبة</div>
            <div style="font-size: 20px; font-weight: 900; color: #DC2626;">
                {{ number_format($ncr->defect_quantity, 2) }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                {{ $ncr->item?->name ?? 'مواد عامة' }}
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #6366F1;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">الخسارة التقديرية (COPQ)</div>
            <div style="font-size: 20px; font-weight: 900; color: #6366F1;">
                {{ number_format($ncr->estimated_loss_amount, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                تكلفة الهدر أو إعادة التشغيل
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--accent-gold);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">قرار التصرف المعتمد</div>
            <div style="font-size: 18px; font-weight: 900; color: var(--accent-gold);">
                {{ $ncr->disposition_label }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                السبب: {{ $ncr->root_cause_label }}
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">الإجراءات التصحيحية المرتبطة</div>
            <div style="font-size: 20px; font-weight: 900; color: #2563EB;">
                {{ $ncr->capas->count() }} <small style="font-size: 11px;">إجراء CAPA</small>
            </div>
            <div style="font-size: 11px; color: #10B981; font-weight: 700; margin-top: 4px;">
                المحقق: {{ $ncr->capas->where('status', 'verified_closed')->count() }}
            </div>
        </div>
    </div>

    <!-- Description & Root Cause Card -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        <div class="erp-card" style="padding: 20px; margin-bottom: 0;">
            <h4 style="font-size: 15px; font-weight: 800; color: #DC2626; border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-file-lines"></i>
                توصيف حالة عدم المطابقة
            </h4>
            <p style="font-size: 13px; color: var(--text-main); line-height: 1.8; margin-bottom: 16px;">
                {{ $ncr->description }}
            </p>

            <div style="background: #F8FAFC; border-radius: 8px; padding: 12px; font-size: 12px; display: flex; flex-direction: column; gap: 6px;">
                <div><span style="color: var(--text-muted);">المورد:</span> <strong>{{ $ncr->supplier?->name ?? 'غير محدد' }}</strong></div>
                <div><span style="color: var(--text-muted);">المشروع:</span> <strong>{{ $ncr->project?->name ?? 'غير محدد' }}</strong></div>
                <div><span style="color: var(--text-muted);">أمر التصنيع:</span> <strong>{{ $ncr->manufacturingOrder?->order_number ?? 'غير محدد' }}</strong></div>
            </div>
        </div>

        <div class="erp-card" style="padding: 20px; margin-bottom: 0;">
            <h4 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-magnifying-glass-chart"></i>
                تحليل الأسباب الجذرية (Root Cause Analysis)
            </h4>
            <div style="font-size: 13px; font-weight: 700; color: var(--accent-gold); margin-bottom: 8px;">
                تصنيف السبب: {{ $ncr->root_cause_label }}
            </div>
            <p style="font-size: 13px; color: var(--text-main); line-height: 1.8; margin-bottom: 16px;">
                {{ $ncr->root_cause_analysis ?? 'لم يتم استكمال تحليل الأسباب الجذرية (5 Whys) بعد.' }}
            </p>

            @if($ncr->disposition_notes)
                <div style="background: #FEF3C7; border: 1px solid #FCD34D; border-radius: 8px; padding: 12px; font-size: 12px; color: #92400E;">
                    <strong>ملاحظات قرار التصرف:</strong> {{ $ncr->disposition_notes }}
                </div>
            @endif
        </div>
    </div>

    <!-- Linked CAPAs Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #F8FAFC;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-shield-halved" style="color: #2563EB;"></i>
                    خطة الإجراءات التصحيحية والوقائية (CAPA Actions)
                </h3>
                <span style="font-size: 12px; color: var(--text-muted);">
                    الإجراءات المعتمدة لمنع تكرار هذا العيب في عمليات التشغيل والتوريد القادمة
                </span>
            </div>

            <button type="button" onclick="document.getElementById('add-capa-modal').style.display='flex'" class="btn-primary-custom" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 800; border: none; cursor: pointer; background: #2563EB;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة إجراء CAPA جديد
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F1F5F9; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">كود الإجراء</th>
                        <th style="padding: 12px 16px;">النوع والعنوان</th>
                        <th style="padding: 12px 16px;">خطة العمل المقترحة</th>
                        <th style="padding: 12px 16px;">المسؤول والموعد</th>
                        <th style="padding: 12px 16px;">حالة الإجراء</th>
                        <th style="padding: 12px 16px;">فاعلية الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ncr->capas as $capa)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: #2563EB;">
                                {{ $capa->capa_number }}
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 800; color: var(--text-main);">{{ $capa->title }}</div>
                                <div style="font-size: 11px; color: var(--accent-gold); font-weight: 600;">{{ $capa->type_label }}</div>
                            </td>
                            <td style="padding: 14px 16px; font-size: 12px; color: var(--text-main);">
                                {{ $capa->action_plan }}
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 600;">{{ $capa->assignee?->name ?? 'لم يعين' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">الموعد: {{ $capa->due_date?->format('Y-m-d') }}</div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: #F1F5F9;">
                                    {{ $capa->status_label }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px;">
                                @php
                                    $effBadge = match($capa->effectiveness_status) {
                                        'effective'   => 'background: rgba(16, 185, 129, 0.1); color: #059669;',
                                        'ineffective' => 'background: rgba(220, 38, 38, 0.1); color: #DC2626;',
                                        default       => 'background: #F1F5F9; color: #64748B;',
                                    };
                                @endphp
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $effBadge !!}">
                                    {{ $capa->effectiveness_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 24px; text-align: center; color: var(--text-muted);">
                                لم يتم تسجيل أي إجراء تصحيحي أو وقائي (CAPA) لهذا التقرير حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Update Disposition -->
<div id="disposition-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 12px; max-width: 500px; width: 100%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0;">اعتماد قرار التصرف في المعيب (Disposition)</h3>
            <button type="button" onclick="document.getElementById('disposition-modal').style.display='none'" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('quality.ncrs.disposition', $ncr->id) }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">قرار التصرف <span style="color: #DC2626;">*</span></label>
                    <select name="disposition" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-weight: 700;">
                        <option value="return_to_vendor" {{ $ncr->disposition == 'return_to_vendor' ? 'selected' : '' }}>إرجاع للمورد مع طلب استبدال أو خصم (Return to Vendor)</option>
                        <option value="rework" {{ $ncr->disposition == 'rework' ? 'selected' : '' }}>إعادة تشغيل وتصليح داخلي (Rework)</option>
                        <option value="scrap" {{ $ncr->disposition == 'scrap' ? 'selected' : '' }}>إتلاف وتكهين فوري (Scrap)</option>
                        <option value="use_as_is" {{ $ncr->disposition == 'use_as_is' ? 'selected' : '' }}>قبول باستثناء فني (Use As-Is)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الخسارة التقديرية المحتسبة (ر.س)</label>
                    <input type="number" step="0.01" name="estimated_loss_amount" value="{{ $ncr->estimated_loss_amount }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">مبررات وتفاصيل قرار التصرف <span style="color: #DC2626;">*</span></label>
                    <textarea name="disposition_notes" rows="3" required placeholder="أدخل تعليمات التصرف للمستودع والإنتاج..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">{{ $ncr->disposition_notes }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('disposition-modal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">حفظ واعتماد القرار</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add CAPA -->
<div id="add-capa-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 12px; max-width: 550px; width: 100%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #2563EB; margin: 0;">تسجيل إجراء تصحيحي / وقائي (CAPA)</h3>
            <button type="button" onclick="document.getElementById('add-capa-modal').style.display='none'" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('quality.capas.store') }}">
            @csrf
            <input type="hidden" name="ncr_id" value="{{ $ncr->id }}">

            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">نوع الإجراء <span style="color: #DC2626;">*</span></label>
                        <select name="type" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                            <option value="corrective">إجراء تصحيحي (Corrective)</option>
                            <option value="preventive">إجراء وقائي (Preventive)</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">تاريخ الاستحقاق المستهدف <span style="color: #DC2626;">*</span></label>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">عنوان الإجراء <span style="color: #DC2626;">*</span></label>
                    <input type="text" name="title" required placeholder="مثال: تعديل معايير معايرة قالب التشكيل وتدريب المشغلين" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">خطة العمل التنفيذية (Action Plan) <span style="color: #DC2626;">*</span></label>
                    <textarea name="action_plan" rows="3" required placeholder="الخطوات العملية الواجب تنفيذها لمنع تكرار الخلل..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;"></textarea>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">المسؤول عن التنفيذ</label>
                    <select name="assigned_to" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="">-- اختر الموظف المسؤول --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">معيار التحقق من فاعلية الإجراء</label>
                    <input type="text" name="effectiveness_criteria" placeholder="مثال: عدم تكرار العيب خلال 3 تشغيلات متتالية" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('add-capa-modal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">إلغاء</button>
                    <button type="submit" style="background: #2563EB; color: #FFFFFF; padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">حفظ وتكليف الإجراء</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
