@extends('layouts.app')

@section('title', 'مستخلص رقم: ' . $extract->extract_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px; max-width: 1200px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <a href="{{ route('projects.extracts.index') }}" style="color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 700;">المستخلصات</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px; color: var(--text-muted);"></i>
                <a href="{{ route('projects.projects.show', $extract->project_id) }}" style="color: var(--primary); text-decoration: none; font-size: 13px;">{{ $extract->project?->name }}</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px; color: var(--text-muted);"></i>
                <span style="font-family: monospace; font-size: 13px; font-weight: 800; background: #E2E8F0; padding: 2px 8px; border-radius: 6px;">{{ $extract->extract_number }}</span>
                @php
                    $extBadge = match($extract->status) {
                        'paid'      => 'background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);',
                        'approved'  => 'background: rgba(37, 99, 235, 0.1); color: #2563EB; border: 1px solid rgba(37, 99, 235, 0.2);',
                        'submitted' => 'background: rgba(217, 119, 6, 0.1); color: #D97706; border: 1px solid rgba(217, 119, 6, 0.2);',
                        default     => 'background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0;',
                    };
                @endphp
                <span style="display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $extBadge !!}">
                    {{ $extract->status_label }}
                </span>
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 38px; height: 38px; border-radius: 10px; background: rgba(37, 99, 235, 0.1); color: #2563EB; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </span>
                مستخلص أعمال مقاولة: {{ $extract->extract_number }}
            </h2>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 6px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <span><i class="fa-solid fa-calendar-day"></i> <strong>تاريخ الإصدار:</strong> {{ $extract->issue_date?->format('Y-m-d') }}</span>
                <span><i class="fa-solid fa-clock"></i> <strong>الفترة:</strong> من {{ $extract->period_from?->format('Y-m-d') }} إلى {{ $extract->period_to?->format('Y-m-d') }}</span>
                @if($extract->consultant_engineer_name)
                    <span><i class="fa-solid fa-user-check" style="color: var(--accent-gold);"></i> <strong>الاستشاري:</strong> {{ $extract->consultant_engineer_name }}</span>
                @endif
            </div>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('projects.extracts.print', $extract->id) }}" target="_blank" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-print"></i>
                طباعة الشهادة الرسمية
            </a>

            @if($extract->status === 'draft' || $extract->status === 'submitted')
                <form method="POST" action="{{ route('projects.extracts.approve', $extract->id) }}" onsubmit="return confirm('هل أنت متأكد من اعتماد هذا المستخلص رسمياً؟ سيتم تحديث الكميات ونسب الإنجاز بالمشروع.');">
                    @csrf
                    <button type="submit" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">
                        <i class="fa-solid fa-check-circle"></i>
                        اعتماد المستخلص رسمياً
                    </button>
                </form>
            @endif

            @if(in_array($extract->status, ['approved', 'paid']) && $extract->paid_amount < $extract->net_amount)
                <button type="button" onclick="document.getElementById('payment-modal').style.display='flex'" style="background: #10B981; color: #FFFFFF; padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    تسجيل سداد / تحصيل
                </button>
            @endif
        </div>
    </div>

    <!-- Financial Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">أعمال الفترة الحالية</div>
            <div style="font-size: 20px; font-weight: 900; color: var(--primary);">
                {{ number_format($extract->current_gross_amount, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                التراكمي: {{ number_format($extract->gross_amount, 2) }} ر.س
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #DC2626;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">الاستقطاعات الإجمالية</div>
            <div style="font-size: 20px; font-weight: 900; color: #DC2626;">
                {{ number_format($extract->advance_payment_deduction + $extract->retention_deduction + $extract->other_deductions, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                مقدمة: {{ number_format($extract->advance_payment_deduction, 2) }} | ضمان: {{ number_format($extract->retention_deduction, 2) }}
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">صافي المستحق للصرف</div>
            <div style="font-size: 20px; font-weight: 900; color: #2563EB;">
                {{ number_format($extract->net_amount, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                يشمل ض.ق.م ({{ $extract->tax_rate }}%): {{ number_format($extract->tax_amount, 2) }} ر.س
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">المحصل والمتبقي</div>
            <div style="font-size: 20px; font-weight: 900; color: #10B981;">
                {{ number_format($extract->paid_amount, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: {{ $extract->net_amount > $extract->paid_amount ? '#DC2626' : '#10B981' }}; font-weight: 700; margin-top: 4px;">
                متبقي: {{ number_format(max(0, $extract->net_amount - $extract->paid_amount), 2) }} ر.س
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); background: #F8FAFC;">
            <h3 style="font-size: 15px; font-weight: 800; color: var(--primary); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-ol"></i>
                تفاصيل الأعمال المنجزة والكميات المحتسبة بالمستخلص
            </h3>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F1F5F9; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 14px;">كود البند</th>
                        <th style="padding: 12px 14px;">بيان الأعمال</th>
                        <th style="padding: 12px 14px;">الوحدة</th>
                        <th style="padding: 12px 14px;">السعر الإفرادي</th>
                        <th style="padding: 12px 14px;">السابق تنفيذه</th>
                        <th style="padding: 12px 14px; background: rgba(37, 99, 235, 0.05); color: #2563EB;">كمية الفترة الحالية</th>
                        <th style="padding: 12px 14px;">الكمية التراكمية</th>
                        <th style="padding: 12px 14px;">قيمة الفترة الحالية</th>
                        <th style="padding: 12px 14px;">القيمة التراكمية</th>
                        <th style="padding: 12px 14px;">نسبة الإنجاز</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($extract->items as $item)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 12px 14px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                {{ $item->boqItem?->item_code ?? '-' }}
                            </td>
                            <td style="padding: 12px 14px; font-weight: 700; color: var(--text-main);">
                                {{ $item->item_description }}
                            </td>
                            <td style="padding: 12px 14px; color: var(--text-muted);">
                                {{ $item->unit }}
                            </td>
                            <td style="padding: 12px 14px; font-weight: 800;">
                                {{ number_format($item->unit_price, 2) }}
                            </td>
                            <td style="padding: 12px 14px; font-weight: 700; color: var(--text-muted);">
                                {{ number_format($item->previous_quantity, 2) }}
                            </td>
                            <td style="padding: 12px 14px; font-weight: 900; color: #2563EB; background: rgba(37, 99, 235, 0.03);">
                                {{ number_format($item->current_quantity, 2) }}
                            </td>
                            <td style="padding: 12px 14px; font-weight: 800;">
                                {{ number_format($item->total_quantity, 2) }}
                            </td>
                            <td style="padding: 12px 14px; font-weight: 900; color: var(--primary);">
                                {{ number_format($item->current_amount, 2) }} <small style="font-size: 10px;">ر.س</small>
                            </td>
                            <td style="padding: 12px 14px; font-weight: 800;">
                                {{ number_format($item->total_amount, 2) }} <small style="font-size: 10px;">ر.س</small>
                            </td>
                            <td style="padding: 12px 14px; font-weight: 800; color: #10B981;">
                                {{ $item->completion_percentage }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #F8FAFC; font-weight: 900; border-top: 2px solid var(--border-color);">
                        <td colspan="7" style="padding: 14px 16px; text-align: left;">إجمالي أعمال الفترة الحالية:</td>
                        <td style="padding: 14px 16px; color: var(--primary); font-size: 14px;">
                            {{ number_format($extract->current_gross_amount, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; color: var(--text-main); font-size: 14px;">
                            {{ number_format($extract->gross_amount, 2) }} ر.س
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Record Payment -->
<div id="payment-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 12px; max-width: 500px; width: 100%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: #10B981; margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
                تسجيل سداد / تحصيل مستخلص
            </h3>
            <button type="button" onclick="document.getElementById('payment-modal').style.display='none'" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        @php
            $remainingAmount = max(0, $extract->net_amount - $extract->paid_amount);
        @endphp

        <form method="POST" action="{{ route('projects.extracts.record-payment', $extract->id) }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="background: #F1F5F9; border-radius: 8px; padding: 12px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--text-muted);">صافي المستخلص:</span>
                        <strong>{{ number_format($extract->net_amount, 2) }} ر.س</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                        <span style="color: var(--text-muted);">المسدد سابقاً:</span>
                        <strong style="color: #10B981;">{{ number_format($extract->paid_amount, 2) }} ر.س</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; border-top: 1px solid var(--border-color); padding-top: 4px;">
                        <span style="font-weight: 700; color: #DC2626;">المتبقي للتحصيل:</span>
                        <strong style="color: #DC2626; font-size: 14px;">{{ number_format($remainingAmount, 2) }} ر.س</strong>
                    </div>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">المبلغ المحصل (ر.س) <span style="color: #DC2626;">*</span></label>
                    <input type="number" step="0.01" name="amount" value="{{ $remainingAmount }}" max="{{ $remainingAmount + 0.01 }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 14px; font-weight: 800; color: #10B981;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">طريقة السداد <span style="color: #DC2626;">*</span></label>
                    <select name="payment_method" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                        <option value="bank_transfer">تحويل بنكي / شيك إيداع</option>
                        <option value="cash">نقداً بالخزينة</option>
                        <option value="cheque">شيك مصرفي</option>
                    </select>
                </div>

                @if($treasuries->isNotEmpty())
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الخزينة أو الحساب البنكي للإيداع</label>
                        <select name="treasury_id" style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                            <option value="">-- اختياري: إيداع بالخزينة --</option>
                            @foreach($treasuries as $tr)
                                <option value="{{ $tr->id }}">{{ $tr->name }} (رصيد: {{ number_format($tr->current_balance, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">تاريخ التحصيل <span style="color: #DC2626;">*</span></label>
                    <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">ملاحظات السداد</label>
                    <input type="text" name="notes" placeholder="رقم الشيك أو الحوالة البنكية..." style="width: 100%; padding: 10px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('payment-modal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">إلغاء</button>
                    <button type="submit" style="background: #10B981; color: #FFFFFF; padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">تأكيد التحصيل</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
