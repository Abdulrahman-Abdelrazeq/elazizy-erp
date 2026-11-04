@extends('layouts.app')

@section('title', 'سجل الإجراءات التصحيحية والوقائية (CAPA Registry)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: #2563EB; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-shield-halved"></i>
                </span>
                سجل الإجراءات التصحيحية والوقائية (CAPA Registry)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تتبع خطط المعالجة الجذرية وتقييم فاعلية الإجراءات لمنع تكرار العيوب والانحرافات الفنية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('quality.ncrs.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #DC2626;"></i>
                تقارير عدم المطابقة (NCR)
            </a>
            <a href="{{ route('quality.inspections.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-clipboard-check" style="color: var(--primary);"></i>
                سجل الفحوصات
            </a>
            <a href="{{ route('quality.analytics.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-pie" style="color: var(--accent-gold);"></i>
                تحليلات الجودة
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي إجراءات CAPA</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--text-main);">{{ number_format($stats['total']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">خطط عمل معتمدة</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--accent-gold); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: var(--accent-gold); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-wrench"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجراءات تصحيحية (Corrective)</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--accent-gold);">{{ number_format($stats['corrective']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">معالجة الخلل المباشر</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-shield-virus"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجراءات وقائية (Preventive)</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ number_format($stats['preventive']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">منع تكرار الخطأ مستقبلاً</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-badge-check"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">محقق ومغلق بفاعلية</span>
                <span style="font-size: 20px; font-weight: 900; color: #10B981;">{{ number_format($stats['verified']) }}</span>
                <span style="font-size: 11px; color: #10B981; display: block; font-weight: 700;">ثبتت فاعليته بعد المراجعة</span>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="erp-card" style="padding: 16px;">
        <form method="GET" action="{{ route('quality.capas.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 100px; gap: 12px; align-items: end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">بحث بالرقم أو العنوان</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم CAPA أو موضوع الإجراء..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">نوع الإجراء</label>
                <select name="type" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الأنواع</option>
                    <option value="corrective" {{ request('type') == 'corrective' ? 'selected' : '' }}>إجراء تصحيحي (Corrective)</option>
                    <option value="preventive" {{ request('type') == 'preventive' ? 'selected' : '' }}>إجراء وقائي (Preventive)</option>
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">حالة التنفيذ</label>
                <select name="status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد التكليف</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>جاري التنفيذ</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>تم التنفيذ</option>
                    <option value="verified_closed" {{ request('status') == 'verified_closed' ? 'selected' : '' }}>محقق ومغلق</option>
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">تقييم الفاعلية</label>
                <select name="effectiveness_status" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع التقييمات</option>
                    <option value="effective" {{ request('effectiveness_status') == 'effective' ? 'selected' : '' }}>إجراء فعال ومثبت</option>
                    <option value="ineffective" {{ request('effectiveness_status') == 'ineffective' ? 'selected' : '' }}>غير فعال</option>
                    <option value="pending_evaluation" {{ request('effectiveness_status') == 'pending_evaluation' ? 'selected' : '' }}>قيد تقييم الفاعلية</option>
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; flex: 1;">
                    <i class="fa-solid fa-filter"></i>
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'type', 'status', 'effectiveness_status']))
                    <a href="{{ route('quality.capas.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); border-radius: 8px; padding: 9px 12px; color: var(--text-muted); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- CAPAs Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: #2563EB;"></i>
                بيان الإجراءات التصحيحية والوقائية المعتمدة
            </h3>
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                عرض {{ $capas->firstItem() ?? 0 }} إلى {{ $capas->lastItem() ?? 0 }} من أصل {{ $capas->total() }} إجراء
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">كود الإجراء</th>
                        <th style="padding: 12px 16px;">النوع والعنوان</th>
                        <th style="padding: 12px 16px;">تقرير NCR المرتبط</th>
                        <th style="padding: 12px 16px;">المسؤول والموعد</th>
                        <th style="padding: 12px 16px;">حالة التنفيذ</th>
                        <th style="padding: 12px 16px;">تقييم الفاعلية</th>
                        <th style="padding: 12px 16px; text-align: center;">إجراءات وتحديث</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($capas as $capa)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: #2563EB;">
                                {{ $capa->capa_number }}
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 800; color: var(--text-main);">{{ $capa->title }}</div>
                                <div style="font-size: 11px; color: var(--accent-gold); font-weight: 600;">{{ $capa->type_label }}</div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <a href="{{ route('quality.ncrs.show', $capa->ncr_id) }}" style="font-weight: 700; color: #DC2626; text-decoration: none; font-family: monospace;">
                                    {{ $capa->ncr?->ncr_number }}
                                </a>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ Str::limit($capa->ncr?->title, 35) }}</div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <div style="font-weight: 600;">{{ $capa->assignee?->name ?? 'لم يعين' }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">تاريخ الاستحقاق: {{ $capa->due_date?->format('Y-m-d') }}</div>
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: #F1F5F9;">
                                    {{ $capa->status_label }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px;">
                                @php
                                    $effStyle = match($capa->effectiveness_status) {
                                        'effective'   => 'background: rgba(16, 185, 129, 0.1); color: #059669;',
                                        'ineffective' => 'background: rgba(220, 38, 38, 0.1); color: #DC2626;',
                                        default       => 'background: #F1F5F9; color: #64748B;',
                                    };
                                @endphp
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $effStyle !!}">
                                    {{ $capa->effectiveness_label }}
                                </span>
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <button type="button" onclick="openVerifyModal('{{ $capa->id }}', '{{ $capa->capa_number }}')" style="padding: 5px 10px; border-radius: 6px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #059669; font-size: 11px; font-weight: 800; cursor: pointer;">
                                        <i class="fa-solid fa-check"></i> تقييم الفاعلية
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                لا توجد إجراءات تصحيحية أو وقائية مسجلة.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($capas->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $capas->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Verify Effectiveness -->
<div id="verify-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 12px; max-width: 480px; width: 100%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0;">التحقق من فاعلية الإجراء: <span id="verify-capa-code" style="font-family: monospace;"></span></h3>
            <button type="button" onclick="document.getElementById('verify-modal').style.display='none'" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" id="verify-form" action="">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">نتيجة تقييم الفاعلية <span style="color: #DC2626;">*</span></label>
                    <select name="effectiveness_status" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-weight: 800;">
                        <option value="effective">إجراء فعال ومثبت بالدليل (Effective - إغلاق رسمي)</option>
                        <option value="ineffective">غير فعال وتكرر الخلل (Ineffective - يتطلب خطة بديلة)</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">ملاحظات المراجعة والتدقيق</label>
                    <textarea name="notes" rows="3" placeholder="أدخل تفاصيل عينات المتابعة ونتائج التدقيق اللاحق..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('verify-modal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">حفظ التقييم</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openVerifyModal(capaId, capaCode) {
    document.getElementById('verify-capa-code').innerText = capaCode;
    document.getElementById('verify-form').action = '/quality/capas/' + capaId + '/verify';
    document.getElementById('verify-modal').style.display = 'flex';
}
</script>
@endsection
