@extends('layouts.app')

@section('title', 'تقرير وردية كاشير Z - ' . $shift->shift_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <a href="{{ route('pos.shifts.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 700;">
                    <i class="fa-solid fa-arrow-right"></i> ورديات الكاشير
                </a>
                <span style="color: var(--border-color);">/</span>
                <span style="font-size: 14px; font-weight: 800; color: var(--primary);">{{ $shift->shift_number }}</span>
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                تقرير إغلاق الوردية وجرد الدرج (Shift Z-Report)
                @if($shift->isOpen())
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-size: 12px; font-weight: 800;">
                        <i class="fa-solid fa-circle" style="font-size: 8px;"></i> وردية مفتوحة
                    </span>
                @else
                    <span class="badge" style="background: #F1F5F9; color: #475569; font-size: 12px; font-weight: 700;">
                        وردية مغلقة
                    </span>
                @endif
            </h2>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            @if($shift->isOpen())
                <a href="{{ route('pos.screen') }}" style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-desktop"></i>
                    متابعة البيع بالكاشير
                </a>
                <button type="button" onclick="openCloseShiftModal()" style="background: #DC2626; color: #fff; border: none; padding: 9px 18px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">
                    <i class="fa-solid fa-lock"></i>
                    إغلاق الوردية وجرد الدرج
                </button>
            @endif

            <a href="{{ route('pos.shifts.print_z', $shift->id) }}" target="_blank" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print" style="color: var(--accent-gold);"></i>
                طباعة إيصال Z الحراري (80mm)
            </a>
        </div>
    </div>

    <!-- Meta Details Card -->
    <div class="erp-card" style="margin-bottom: 0; padding: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px;">
            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">رقم الوردية</span>
                <strong style="font-size: 16px; color: var(--primary);">{{ $shift->shift_number }}</strong>
            </div>
            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">نقطة البيع (الكاشير)</span>
                <strong style="font-size: 15px; color: var(--text-main);">{{ $shift->terminal->name ?? '-' }} ({{ $shift->terminal->code ?? '' }})</strong>
            </div>
            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">الكاشير المسؤول</span>
                <strong style="font-size: 15px; color: var(--text-main);">{{ $shift->user->name ?? '-' }}</strong>
            </div>
            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">تاريخ ووقت الفتح</span>
                <strong style="font-size: 14px; color: var(--text-main);">{{ $shift->started_at ? $shift->started_at->format('Y-m-d h:i A') : '-' }}</strong>
            </div>
            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">تاريخ ووقت الإغلاق</span>
                <strong style="font-size: 14px; color: var(--text-main);">
                    {{ $shift->ended_at ? $shift->ended_at->format('Y-m-d h:i A') : 'ما زالت قيد العمل' }}
                </strong>
            </div>
            @if($shift->closedBy)
            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">تم الإغلاق بواسطة</span>
                <strong style="font-size: 14px; color: var(--text-main);">{{ $shift->closedBy->name }}</strong>
            </div>
            @endif
        </div>

        @if($shift->start_notes || $shift->end_notes)
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px dashed var(--border-color); display: flex; flex-direction: column; gap: 8px;">
                @if($shift->start_notes)
                    <div style="font-size: 12.5px; color: var(--text-muted);">
                        <strong style="color: var(--text-main);">ملاحظات الاستلام:</strong> {{ $shift->start_notes }}
                    </div>
                @endif
                @if($shift->end_notes)
                    <div style="font-size: 12.5px; color: var(--text-muted);">
                        <strong style="color: var(--text-main);">ملاحظات التسليم / الإغلاق:</strong> {{ $shift->end_notes }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Financial Breakdown Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #64748B;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">عهدة الافتتاح (الرصيد الافتتاحي)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #334155;">{{ formatMoney($shift->opening_balance) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #0F5132;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مبيعات النقد (كاش)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ formatMoney($shift->cash_sales) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مبيعات الشبكة / الفيزا</span>
            <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ formatMoney($shift->card_sales) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مبيعات الآجل (ذمم)</span>
            <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ formatMoney($shift->credit_sales) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #EF4444;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي المرتجعات</span>
            <strong style="font-size: 20px; font-weight: 900; color: #EF4444;">{{ formatMoney($shift->total_returns) }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); background: rgba(15, 81, 50, 0.03);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي مبيعات الوردية</span>
            <strong style="font-size: 22px; font-weight: 900; color: var(--primary);">
                {{ formatMoney($shift->cash_sales + $shift->card_sales + $shift->credit_sales) }}
            </strong>
        </div>
    </div>

    <!-- Drawer Settlement Card (الجرد ومطابقة الدرج) -->
    <div class="erp-card" style="margin-bottom: 0; padding: 22px; background: linear-gradient(to right, #FAF5FF, #F0FDF4);">
        <h3 style="font-size: 16px; font-weight: 900; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-scale-balanced" style="color: var(--accent-gold);"></i>
            تسوية وجرد النقدية بالدرج (Cash Drawer Settlement)
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
            <div style="background: #fff; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">
                    النقدية المتوقعة بالدرج (Expected Cash)
                </span>
                <span style="font-size: 11px; color: var(--text-muted); display: block; margin-bottom: 8px;">
                    العهدة + مبيعات الكاش - المرتجعات
                </span>
                <strong style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                    {{ formatMoney($shift->expected_cash) }}
                </strong>
            </div>

            <div style="background: #fff; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">
                    النقدية الفعلية المحصية بالجرد (Actual Cash)
                </span>
                <span style="font-size: 11px; color: var(--text-muted); display: block; margin-bottom: 8px;">
                    المبلغ الذي عده الكاشير عند الإغلاق
                </span>
                @if($shift->isClosed())
                    <strong style="font-size: 22px; font-weight: 900; color: #0F5132;">
                        {{ formatMoney($shift->actual_cash) }}
                    </strong>
                @else
                    <span style="color: var(--text-muted); font-size: 14px; font-weight: 700;">الوردية ما زالت مفتوحة</span>
                @endif
            </div>

            <div style="background: #fff; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color);">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">
                    الفارق النهائي في الدرج (Variance)
                </span>
                <span style="font-size: 11px; color: var(--text-muted); display: block; margin-bottom: 8px;">
                    الفعلي - المتوقع
                </span>
                @if($shift->isClosed())
                    @php $variance = (float) $shift->cash_difference; @endphp
                    @if(abs($variance) < 0.01)
                        <strong style="font-size: 22px; font-weight: 900; color: #0F5132;">
                            متطابق تماماً (0.00 ج.م)
                        </strong>
                    @elseif($variance < 0)
                        <strong style="font-size: 22px; font-weight: 900; color: #DC2626;">
                            عجز نقدي: {{ formatMoney(abs($variance)) }}
                        </strong>
                    @else
                        <strong style="font-size: 22px; font-weight: 900; color: #059669;">
                            فائض نقدي: +{{ formatMoney($variance) }}
                        </strong>
                    @endif
                @else
                    <span style="color: var(--text-muted); font-size: 14px; font-weight: 700;">يحسب عند الإغلاق</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Invoices Issued in this Shift -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 18px 22px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-receipt" style="color: var(--primary);"></i>
                سجل الفواتير الصادرة أثناء هذه الوردية ({{ $shift->invoices->count() }} فاتورة)
            </h3>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">رقم الفاتورة</th>
                        <th>الوقت</th>
                        <th>العميل</th>
                        <th>طريقة الدفع</th>
                        <th>المجموع الإجمالي</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 100px;">الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shift->invoices as $inv)
                        <tr>
                            <td>
                                <a href="{{ route('invoices.sales.show', $inv->id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none;">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td style="font-size: 12px; font-weight: 600;">
                                {{ $inv->created_at->format('h:i:s A') }}
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--text-main);">
                                    {{ $inv->client->name ?? 'عميل نقدي سريع' }}
                                </span>
                            </td>
                            <td>
                                @if($inv->payment_type === 'cash')
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">نقداً</span>
                                @elseif($inv->payment_type === 'card')
                                    <span class="badge" style="background: #E0E7FF; color: #4338CA; font-weight: 700;">شبكة/بطاقة</span>
                                @elseif($inv->payment_type === 'credit')
                                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700;">آجل</span>
                                @else
                                    <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700;">{{ $inv->payment_type }}</span>
                                @endif
                            </td>
                            <td style="font-weight: 800; color: var(--text-main);">{{ formatMoney($inv->grand_total) }}</td>
                            <td style="font-weight: 700; color: #0F5132;">{{ formatMoney($inv->paid_amount) }}</td>
                            <td style="font-weight: 700; color: {{ $inv->remaining_amount > 0 ? '#DC2626' : 'var(--text-muted)' }};">
                                {{ formatMoney($inv->remaining_amount) }}
                            </td>
                            <td>
                                @if($inv->status === 'paid')
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">مدفوعة</span>
                                @elseif($inv->status === 'partial')
                                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700;">جزئي</span>
                                @elseif($inv->status === 'cancelled')
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">ملغاة</span>
                                @else
                                    <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700;">غير مدفوعة</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('pos.receipt', $inv->id) }}" target="_blank" title="طباعة الإيصال الحراري" style="width: 30px; height: 30px; border-radius: 6px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                    <i class="fa-solid fa-receipt"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 36px 16px; color: var(--text-muted);">
                                لا توجد فواتير صادرة حتى الآن في هذه الوردية.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@if($shift->isOpen())
<!-- Modal: Close Shift & Count Cash Drawer -->
<div id="closeShiftModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: #DC2626; color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-lock"></i>
                إغلاق الوردية وجرد الدرج النقدي
            </h4>
            <button type="button" onclick="closeCloseShiftModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('pos.shifts.close', $shift->id) }}" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div style="background: #FEF2F2; border: 1px solid #FECACA; padding: 14px; border-radius: 10px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                    <span style="font-size: 12.5px; color: #991B1B; font-weight: 700;">النقدية المتوقعة دفترياً بالدرج:</span>
                    <strong style="font-size: 15px; color: #991B1B;">{{ formatMoney($shift->expected_cash) }}</strong>
                </div>
                <small style="font-size: 11.5px; color: #B91C1C; display: block;">
                    يشمل عهدة البداية ({{ formatMoney($shift->opening_balance) }}) + مبيعات الكاش ({{ formatMoney($shift->cash_sales) }})
                </small>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: 800; color: var(--text-main); display: block; margin-bottom: 6px;">
                    المبلغ النقدي الفعلي المحصى بالدرج <span style="color: #DC2626;">*</span>
                </label>
                <div style="position: relative;">
                    <input type="number" step="0.01" min="0" name="actual_cash" id="actualCashInput" class="erp-input" style="padding-left: 45px; font-weight: 900; font-size: 18px; color: #0F5132;" placeholder="0.00" required>
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; font-size: 12px; color: var(--text-muted);">ج.م</span>
                </div>
                <small style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px; display: block;">
                    قم بعد جميع الأوراق والعملات النقدية داخل الدرج وأدخل الإجمالي الفعلي هنا
                </small>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    ملاحظات الإغلاق والتسليم
                </label>
                <textarea name="end_notes" rows="2" class="erp-input" placeholder="أي ملاحظات حول الجرد أو التسليم للمسؤول..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeCloseShiftModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" style="background: #DC2626; color: #fff; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 800; cursor: pointer;">
                    <i class="fa-solid fa-lock"></i>
                    تأكيد إغلاق الوردية
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCloseShiftModal() {
        document.getElementById('closeShiftModal').style.display = 'flex';
        document.getElementById('actualCashInput').focus();
    }
    function closeCloseShiftModal() {
        document.getElementById('closeShiftModal').style.display = 'none';
    }
</script>
@endif

@endsection

