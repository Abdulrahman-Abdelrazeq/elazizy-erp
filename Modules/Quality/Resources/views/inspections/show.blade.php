@extends('layouts.app')

@section('title', 'أمر فحص: ' . $inspection->inspection_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px; max-width: 1200px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <a href="{{ route('quality.inspections.index') }}" style="color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 700;">سجل الفحوصات</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px; color: var(--text-muted);"></i>
                <span style="font-family: monospace; font-size: 13px; font-weight: 800; background: #E2E8F0; padding: 2px 8px; border-radius: 6px;">{{ $inspection->inspection_number }}</span>
                <span style="display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; background: rgba(15, 81, 50, 0.1); color: var(--primary);">
                    {{ $inspection->type_label }}
                </span>
                @php
                    $statusStyle = match($inspection->status) {
                        'passed'               => 'background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);',
                        'rejected'             => 'background: rgba(220, 38, 38, 0.1); color: #DC2626; border: 1px solid rgba(220, 38, 38, 0.2);',
                        'conditionally_passed' => 'background: rgba(217, 119, 6, 0.1); color: #D97706; border: 1px solid rgba(217, 119, 6, 0.2);',
                        default                => 'background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0;',
                    };
                @endphp
                <span style="display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $statusStyle !!}">
                    {{ $inspection->status_label }}
                </span>
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 38px; height: 38px; border-radius: 10px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-clipboard-check"></i>
                </span>
                أمر فحص واختبار جودة: {{ $inspection->inspection_number }}
            </h2>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 6px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <span><i class="fa-solid fa-calendar-day"></i> <strong>تاريخ الفحص:</strong> {{ $inspection->inspection_date?->format('Y-m-d') }}</span>
                <span><i class="fa-solid fa-user-check" style="color: var(--primary);"></i> <strong>المفتش المسؤول:</strong> {{ $inspection->inspector?->name ?? 'لم يعين' }}</span>
                <span><i class="fa-solid fa-code-branch"></i> <strong>الفرع:</strong> {{ $inspection->branch?->name }}</span>
                @if($inspection->batch_number)
                    <span><i class="fa-solid fa-barcode"></i> <strong>التشغيلة:</strong> {{ $inspection->batch_number }}</span>
                @endif
            </div>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('quality.inspections.print', $inspection->id) }}" target="_blank" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-print"></i>
                طباعة شهادة الفحص المعتمدة
            </a>
            @if($inspection->status === 'rejected' || $inspection->status === 'conditionally_passed')
                <a href="{{ route('quality.ncrs.create', ['inspection_id' => $inspection->id, 'item_id' => $inspection->item_id, 'branch_id' => $inspection->branch_id]) }}" style="background: #FEF2F2; border: 1px solid #FCA5A5; color: #DC2626; padding: 9px 16px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    إصدار تقرير عدم مطابقة (NCR)
                </a>
            @endif
        </div>
    </div>

    <!-- Overview Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">إجمالي كمية الدفعة</div>
            <div style="font-size: 20px; font-weight: 900; color: var(--text-main);">
                {{ number_format($inspection->total_quantity, 2) }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                حجم العينة: {{ number_format($inspection->sample_size, 2) }}
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">الكمية المقبولة والمطابقة</div>
            <div style="font-size: 20px; font-weight: 900; color: #10B981;">
                {{ number_format($inspection->passed_quantity, 2) }}
            </div>
            <div style="font-size: 11px; color: #10B981; font-weight: 700; margin-top: 4px;">
                نسبة القبول: {{ $inspection->pass_rate }}%
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">الكمية المرفوضة والتالفة</div>
            <div style="font-size: 20px; font-weight: 900; color: #DC2626;">
                {{ number_format($inspection->failed_quantity, 2) }}
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                نسبة العيوب: {{ $inspection->total_quantity > 0 ? round(($inspection->failed_quantity / $inspection->total_quantity) * 100, 1) : 0 }}%
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--accent-gold);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">نقاط التحقق والمعايير</div>
            <div style="font-size: 20px; font-weight: 900; color: var(--accent-gold);">
                {{ $inspection->results->count() }} <small style="font-size: 12px;">نقطة فحص</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                المطابق: {{ $inspection->results->where('is_passed', true)->count() }} | المخالف: {{ $inspection->results->where('is_passed', false)->count() }}
            </div>
        </div>
    </div>

    <!-- Evaluation & Checkpoints Recording Form -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); background: #F8FAFC;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-microscope"></i>
                رصد نتائج فحص المعايير واعتماد القرار النهائي (Evaluation & Release)
            </h3>
            <span style="font-size: 12px; color: var(--text-muted);">
                أدخل القيم المرصودة لكل معيار قياسي وحدد حالة المطابقة والكميات المفرج عنها
            </span>
        </div>

        <form method="POST" action="{{ route('quality.inspections.evaluate', $inspection->id) }}">
            @csrf

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                    <thead>
                        <tr style="background: #F1F5F9; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                            <th style="padding: 12px 16px; width: 50px;">#</th>
                            <th style="padding: 12px 16px;">نقطة الفحص والاختبار</th>
                            <th style="padding: 12px 16px;">المواصفة والحدود المقبولة</th>
                            <th style="padding: 12px 16px; min-width: 150px;">القيمة المرصودة / القراءة</th>
                            <th style="padding: 12px 16px; min-width: 140px;">نتيجة النقطة</th>
                            <th style="padding: 12px 16px; min-width: 180px;">ملاحظات العيوب إن وجدت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inspection->results as $idx => $res)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 12px 16px; font-weight: 800; color: var(--text-muted);">
                                    {{ $idx + 1 }}
                                    <input type="hidden" name="results[{{ $idx }}][id]" value="{{ $res->id }}">
                                </td>
                                <td style="padding: 12px 16px; font-weight: 800; color: var(--text-main);">
                                    {{ $res->checkpoint_name }}
                                </td>
                                <td style="padding: 12px 16px; font-weight: 700; color: var(--primary);">
                                    {{ $res->standard_criteria ?? '-' }}
                                </td>
                                <td style="padding: 8px 16px;">
                                    <input type="text" name="results[{{ $idx }}][value]" value="{{ old("results.{$idx}.value", $res->observed_value) }}" placeholder="القراءة المسجلة..." style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 12px;">
                                </td>
                                <td style="padding: 8px 16px;">
                                    <select name="results[{{ $idx }}][is_passed]" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 12px; font-weight: 700;">
                                        <option value="1" {{ old("results.{$idx}.is_passed", $res->is_passed ? '1' : '0') == '1' ? 'selected' : '' }}>مطابق (Pass) ✓</option>
                                        <option value="0" {{ old("results.{$idx}.is_passed", $res->is_passed ? '1' : '0') == '0' ? 'selected' : '' }}>غير مطابق (Fail) ✗</option>
                                    </select>
                                </td>
                                <td style="padding: 8px 16px;">
                                    <input type="text" name="results[{{ $idx }}][defect_notes]" value="{{ old("results.{$idx}.defect_notes", $res->defect_notes) }}" placeholder="وصف الخلل إن وجد..." style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 12px;">
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding: 24px; text-align: center; color: var(--text-muted);">
                                    لم يتم ربط نقاط فحص قياسية بأمر الفحص هذا. يمكنك تقييم النتيجة الإجمالية مباشرة بالأسفل.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div style="padding: 24px; background: #F8FAFC; border-top: 2px solid var(--border-color);">
                <h4 style="font-size: 14px; font-weight: 800; color: var(--primary); margin-bottom: 16px;">
                    قرار الاعتماد النهائي وتحديد الكميات المقبولة والمرفوضة
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الكمية المقبولة والمطابقة <span style="color: #DC2626;">*</span></label>
                        <input type="number" step="0.001" name="passed_quantity" value="{{ old('passed_quantity', $inspection->passed_quantity ?: $inspection->total_quantity) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; font-weight: 800; color: #10B981;">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الكمية المرفوضة (المعيبة) <span style="color: #DC2626;">*</span></label>
                        <input type="number" step="0.001" name="failed_quantity" value="{{ old('failed_quantity', $inspection->failed_quantity) }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; font-weight: 800; color: #DC2626;">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">قرار الفحص والاعتماد النهائي <span style="color: #DC2626;">*</span></label>
                        <select name="status" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px; font-weight: 800;">
                            <option value="passed" {{ old('status', $inspection->status) == 'passed' ? 'selected' : '' }}>مطابق ومعتمد للإفراج (Passed)</option>
                            <option value="rejected" {{ old('status', $inspection->status) == 'rejected' ? 'selected' : '' }}>مرفوض وغير مطابق (Rejected)</option>
                            <option value="conditionally_passed" {{ old('status', $inspection->status) == 'conditionally_passed' ? 'selected' : '' }}>مقبول بتجاوز / استثناء (Conditional Pass)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 18px;">
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">تقرير وملاحظات مهندس الجودة النهائية</label>
                    <textarea name="notes" rows="2" placeholder="أدخل أسباب الرفض أو اشتراطات الاستثناء..." style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">{{ old('notes', $inspection->notes) }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 800; border: none; cursor: pointer;">
                        <i class="fa-solid fa-stamp"></i>
                        اعتماد نتائج الفحص وتحديث سجل الجودة
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Linked NCRs Section if any -->
    @if($inspection->ncrs->isNotEmpty())
        <div class="erp-card" style="padding: 0; overflow: hidden; border-top: 4px solid #DC2626;">
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); background: #FEF2F2;">
                <h3 style="font-size: 15px; font-weight: 800; color: #DC2626; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    تقارير حالات عدم المطابقة المرتبطة بهذا الفحص (Linked NCRs)
                </h3>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); font-weight: 800; color: var(--text-muted);">
                            <th style="padding: 12px 16px;">رقم التقرير NCR</th>
                            <th style="padding: 12px 16px;">عنوان الخلل</th>
                            <th style="padding: 12px 16px;">درجة الخطورة</th>
                            <th style="padding: 12px 16px;">الكمية المعيبة</th>
                            <th style="padding: 12px 16px;">قرار التصرف</th>
                            <th style="padding: 12px 16px;">الحالة</th>
                            <th style="padding: 12px 16px; text-align: center;">عرض</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inspection->ncrs as $ncr)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 12px 16px; font-weight: 800; font-family: monospace; color: #DC2626;">
                                    <a href="{{ route('quality.ncrs.show', $ncr->id) }}" style="text-decoration: none; color: #DC2626;">
                                        {{ $ncr->ncr_number }}
                                    </a>
                                </td>
                                <td style="padding: 12px 16px; font-weight: 700;">
                                    {{ $ncr->title }}
                                </td>
                                <td style="padding: 12px 16px;">
                                    <span style="font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: rgba(220, 38, 38, 0.1); color: #DC2626;">
                                        {{ $ncr->severity_label }}
                                    </span>
                                </td>
                                <td style="padding: 12px 16px; font-weight: 800; color: #DC2626;">
                                    {{ number_format($ncr->defect_quantity, 2) }}
                                </td>
                                <td style="padding: 12px 16px;">
                                    {{ $ncr->disposition_label }}
                                </td>
                                <td style="padding: 12px 16px;">
                                    <span style="font-size: 11px; font-weight: 800; padding: 2px 8px; border-radius: 6px; background: #F1F5F9;">
                                        {{ $ncr->status_label }}
                                    </span>
                                </td>
                                <td style="padding: 12px 16px; text-align: center;">
                                    <a href="{{ route('quality.ncrs.show', $ncr->id) }}" style="padding: 4px 10px; border-radius: 6px; background: #F1F5F9; color: var(--text-main); text-decoration: none; font-size: 12px; font-weight: 700;">
                                        عرض
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection
