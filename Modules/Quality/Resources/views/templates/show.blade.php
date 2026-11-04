@extends('layouts.app')

@section('title', 'نموذج فحص: ' . $template->name . ' (' . $template->code . ')')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <a href="{{ route('quality.templates.index') }}" style="color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 700;">نماذج الفحص</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px; color: var(--text-muted);"></i>
                <span style="font-family: monospace; font-size: 13px; font-weight: 800; background: #E2E8F0; padding: 2px 8px; border-radius: 6px;">{{ $template->code }}</span>
                <span style="display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; background: rgba(15, 81, 50, 0.1); color: var(--primary);">
                    {{ $template->type_label }}
                </span>
                @if($template->is_active)
                    <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: rgba(16, 185, 129, 0.1); color: #059669;">
                        نشط ومعتمد
                    </span>
                @endif
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 38px; height: 38px; border-radius: 10px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-list-check"></i>
                </span>
                {{ $template->name }}
            </h2>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 6px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                @if($template->item)
                    <span><i class="fa-solid fa-box" style="color: var(--accent-gold);"></i> <strong>الصنف:</strong> {{ $template->item->name }}</span>
                @endif
                @if($template->category)
                    <span><i class="fa-solid fa-shapes"></i> <strong>التصنيف:</strong> {{ $template->category->name }}</span>
                @endif
                <span><i class="fa-solid fa-check-double" style="color: var(--primary);"></i> <strong>عدد المعايير:</strong> {{ $template->items->count() }} معيار قياسي</span>
            </div>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('quality.inspections.create', ['template_id' => $template->id, 'type' => $template->type, 'item_id' => $template->item_id]) }}" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-clipboard-check"></i>
                إصدار أمر فحص جديد بهذا النموذج
            </a>
            <a href="{{ route('quality.templates.edit', $template->id) }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-pen-to-square"></i>
                تعديل النموذج
            </a>
        </div>
    </div>

    <!-- Description & Scope if available -->
    @if($template->description)
        <div class="erp-card" style="padding: 18px; margin-bottom: 0; background: #F8FAFC;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">
                <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                المواصفة الفنية والاشتراطات المرجعية:
            </div>
            <div style="font-size: 13px; color: var(--text-main); line-height: 1.7;">
                {{ $template->description }}
            </div>
        </div>
    @endif

    <!-- Checkpoints / Inspection Criteria Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-tasks" style="color: var(--primary);"></i>
                    قائمة معايير الفحص ونقاط المراقبة والتفتيش (Inspection Checkpoints)
                </h3>
                <span style="font-size: 12px; color: var(--text-muted);">
                    نقاط التحقق التي يُلزم مهندس/مفتش الجودة بقياسها وإثبات مطابقتها في كل أمر فحص
                </span>
            </div>

            <button type="button" onclick="document.getElementById('add-checkpoint-modal').style.display='flex'" class="btn-primary-custom" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 800; border: none; cursor: pointer;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة معيار / نقطة فحص
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px; width: 50px;">#</th>
                        <th style="padding: 12px 16px;">نقطة الفحص / الاختبار</th>
                        <th style="padding: 12px 16px;">نوع المعيار</th>
                        <th style="padding: 12px 16px;">المواصفة والحدود المقبولة</th>
                        <th style="padding: 12px 16px;">طريقة وأداة الفحص</th>
                        <th style="padding: 12px 16px;">إلزامي؟</th>
                        <th style="padding: 12px 16px; text-align: center;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($template->items as $idx => $item)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 800; color: var(--text-muted);">
                                {{ $idx + 1 }}
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: var(--text-main);">
                                {{ $item->checkpoint_name }}
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($item->standard_type === 'boolean')
                                    <span style="background: rgba(37, 99, 235, 0.1); color: #2563EB; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">منطقي (مطابق / غير مطابق)</span>
                                @elseif($item->standard_type === 'numeric_range')
                                    <span style="background: rgba(217, 119, 6, 0.1); color: var(--accent-gold); padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">رقمي بنطاق وتفاوت</span>
                                @else
                                    <span style="background: #F1F5F9; color: #475569; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">وصفي / بصري</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; font-weight: 700; color: var(--primary);">
                                {{ $item->formatted_criteria }}
                            </td>
                            <td style="padding: 14px 16px; color: var(--text-muted);">
                                {{ $item->inspection_method ?? 'فحص واختبار قياسي' }}
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($item->is_mandatory)
                                    <span style="color: #DC2626; font-weight: 800; font-size: 11px;">إلزامي للاعتماد</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: 11px;">استرشادي</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <form method="POST" action="{{ route('quality.templates.items.destroy', $item->id) }}" onsubmit="return confirm('هل تريد حذف هذا المعيار من قائمة الفحص؟');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: transparent; border: none; color: #DC2626; cursor: pointer;" title="حذف المعيار">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 30px; text-align: center; color: var(--text-muted);">
                                لم تتم إضافة أي معايير أو نقاط فحص لهذا النموذج بعد. انقر على زر "إضافة معيار / نقطة فحص" بالأعلى.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Inspections using this template -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); background: #F8FAFC;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-history" style="color: var(--accent-gold);"></i>
                سجل الفحوصات المنفذة بناءً على هذا النموذج
            </h3>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F1F5F9; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">رقم أمر الفحص</th>
                        <th style="padding: 12px 16px;">تاريخ الفحص</th>
                        <th style="padding: 12px 16px;">الدفعة / التشغيلة</th>
                        <th style="padding: 12px 16px;">المفتش المسؤول</th>
                        <th style="padding: 12px 16px;">الكمية المفحوصة</th>
                        <th style="padding: 12px 16px;">الحالة والنتيجة</th>
                        <th style="padding: 12px 16px; text-align: center;">عرض</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($template->inspections as $ins)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 16px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                <a href="{{ route('quality.inspections.show', $ins->id) }}" style="text-decoration: none; color: var(--primary);">
                                    {{ $ins->inspection_number }}
                                </a>
                            </td>
                            <td style="padding: 12px 16px; color: var(--text-muted);">
                                {{ $ins->inspection_date?->format('Y-m-d') }}
                            </td>
                            <td style="padding: 12px 16px; font-weight: 600;">
                                {{ $ins->batch_number ?? '-' }}
                            </td>
                            <td style="padding: 12px 16px;">
                                {{ $ins->inspector?->name ?? '-' }}
                            </td>
                            <td style="padding: 12px 16px; font-weight: 700;">
                                {{ number_format($ins->total_quantity, 2) }}
                            </td>
                            <td style="padding: 12px 16px;">
                                <span style="font-size: 11px; font-weight: 800; padding: 3px 8px; border-radius: 6px; background: #F1F5F9;">
                                    {{ $ins->status_label }}
                                </span>
                            </td>
                            <td style="padding: 12px 16px; text-align: center;">
                                <a href="{{ route('quality.inspections.show', $ins->id) }}" style="padding: 4px 10px; border-radius: 6px; background: rgba(15, 81, 50, 0.1); color: var(--primary); text-decoration: none; font-size: 12px; font-weight: 700;">
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 20px; text-align: center; color: var(--text-muted);">
                                لم يتم تنفيذ أي فحص بهذا النموذج بعد.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Add Checkpoint Item -->
<div id="add-checkpoint-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 12px; max-width: 550px; width: 100%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0;">إضافة معيار فحص ونقطة مراقبة جديدة</h3>
            <button type="button" onclick="document.getElementById('add-checkpoint-modal').style.display='none'" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('quality.templates.items.store', $template->id) }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">اسم نقطة الفحص أو الاختبار <span style="color: #DC2626;">*</span></label>
                    <input type="text" name="checkpoint_name" required placeholder="مثال: اختبار هبوط الخرسانة Slump Test أو فحص الأبعاد الخارجية" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">نوع المعيار <span style="color: #DC2626;">*</span></label>
                    <select name="standard_type" id="modal-standard-type" required onchange="toggleStandardTypeInputs()" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="boolean">منطقي (مطابق / غير مطابق)</option>
                        <option value="numeric_range">رقمي محدد بنطاق وتفاوت مسموح</option>
                        <option value="text">وصفي / فحص ظاهري وبصري</option>
                    </select>
                </div>

                <!-- Numeric Range Specific Fields -->
                <div id="numeric-fields" style="display: none; flex-direction: column; gap: 10px; background: #F8FAFC; padding: 12px; border-radius: 8px; border: 1px dashed var(--border-color);">
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <div>
                            <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 2px; display: block;">الحد الأدنى</label>
                            <input type="number" step="0.0001" name="min_value" placeholder="مثال: 100" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 12px;">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 2px; display: block;">المستهدف</label>
                            <input type="number" step="0.0001" name="target_value" placeholder="مثال: 110" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 12px;">
                        </div>
                        <div>
                            <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 2px; display: block;">الحد الأقصى</label>
                            <input type="number" step="0.0001" name="max_value" placeholder="مثال: 120" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 12px;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 2px; display: block;">وحدة القياس</label>
                        <input type="text" name="unit_of_measure" placeholder="مثال: مم أو سم أو كجم/سم2 أو %" style="width: 100%; padding: 6px 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 12px;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">طريقة الفحص والأداة المستخدمة</label>
                    <input type="text" name="inspection_method" placeholder="مثال: جهاز مخروطي للخرسانة / قدمة ذات ورنية / اختبار شد" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_mandatory" id="modal-is-mandatory" value="1" checked style="width: 16px; height: 16px; accent-color: var(--primary);">
                    <label for="modal-is-mandatory" style="font-size: 12px; font-weight: 700; color: var(--text-main); cursor: pointer;">معيار إلزامي (عدم اجتيازه يؤدي لرفض الدفعة)</label>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('add-checkpoint-modal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">حفظ المعيار</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleStandardTypeInputs() {
    const type = document.getElementById('modal-standard-type').value;
    const numFields = document.getElementById('numeric-fields');
    if (type === 'numeric_range') {
        numFields.style.display = 'flex';
    } else {
        numFields.style.display = 'none';
    }
}
</script>
@endsection
