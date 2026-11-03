@extends('layouts.app')

@section('title', 'تفاصيل إذن التحويل البيني ' . $transfer->transfer_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </span>
                    إذن تحويل بين الفروع: {{ $transfer->transfer_number }}
                </h2>

                @if($transfer->status === 'draft')
                    <span style="background: #F1F5F9; color: #475569; font-weight: 800; padding: 5px 12px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-regular fa-clock"></i> مسودة (Draft)
                    </span>
                @elseif($transfer->status === 'dispatched')
                    <span style="background: #FEF3C7; color: #D97706; font-weight: 800; padding: 5px 12px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-truck-moving"></i> تم الشحن - في الطريق (In-Transit)
                    </span>
                @elseif($transfer->status === 'received')
                    <span style="background: #D1E7DD; color: #0F5132; font-weight: 800; padding: 5px 12px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-circle-check"></i> تم الاستلام والتوريد بالكامل (Received)
                    </span>
                @elseif($transfer->status === 'cancelled')
                    <span style="background: #FEE2E2; color: #DC2626; font-weight: 800; padding: 5px 12px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-ban"></i> ملغي (Cancelled)
                    </span>
                @endif
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">
                تاريخ الإنشاء: {{ $transfer->created_at->format('Y-m-d H:i') }} بواسطة {{ $transfer->creator?->name ?? 'النظام' }}
            </span>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('branches.transfers.print', $transfer->id) }}" target="_blank" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: #4338CA; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print"></i>
                طباعة بوليصة التحويل
            </a>

            @if($transfer->status === 'draft')
                <form action="{{ route('branches.transfers.dispatch', $transfer->id) }}" method="POST" onsubmit="return confirm('تأكيد شحن البضاعة؟ سيتم خصم الكميات فوراً من المخزن المصدر.');" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                        <i class="fa-solid fa-truck-fast"></i>
                        اعتماد وشحن الشحنة (Dispatch)
                    </button>
                </form>

                <form action="{{ route('branches.transfers.cancel', $transfer->id) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد إلغاء هذا التحويل؟');" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 10px 16px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer;">
                        <i class="fa-solid fa-ban"></i>
                        إلغاء التحويل
                    </button>
                </form>
            @elseif($transfer->status === 'dispatched')
                <form action="{{ route('branches.transfers.receive', $transfer->id) }}" method="POST" onsubmit="return confirm('تأكيد استلام وتوريد البضاعة؟ سيتم إضافة الكميات فوراً لمخزن الفرع المستلم.');" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #0F5132; color: #fff; border: none; padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-box-open"></i>
                        تأكيد الاستلام والتوريد للمخزن (Receive)
                    </button>
                </form>

                <form action="{{ route('branches.transfers.cancel', $transfer->id) }}" method="POST" onsubmit="return confirm('تأكيد إلغاء الشحنة؟ سيتم إعادة الكميات المخصومة إلى المخزن المصدر.');" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 10px 16px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer;">
                        <i class="fa-solid fa-arrow-rotate-left"></i>
                        إلغاء واسترجاع للمصدر
                    </button>
                </form>
            @endif

            <a href="{{ route('branches.transfers.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <!-- Alert / Status Notifications -->
    @if(session('success'))
    <div style="background: #D1E7DD; border: 1px solid #A3CFBB; color: #0F5132; padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
        <i class="fa-solid fa-circle-check" style="margin-left: 8px;"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
        <i class="fa-solid fa-triangle-exclamation" style="margin-left: 8px;"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Workflow Progress Visualizer -->
    <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
        <div style="display: flex; align-items: center; justify-content: space-between; position: relative;">
            
            <!-- Step 1: Draft -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; z-index: 2; flex: 1;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: #0F5132; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 900; box-shadow: 0 4px 10px rgba(15, 81, 50, 0.2);">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <strong style="font-size: 13px; color: var(--text-main);">1. إنشاء الطلب</strong>
                <span style="font-size: 11px; color: var(--text-muted);">{{ $transfer->transfer_date ? \Carbon\Carbon::parse($transfer->transfer_date)->format('Y-m-d') : '—' }}</span>
            </div>

            <!-- Step 2: Dispatched -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; z-index: 2; flex: 1;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: {{ in_array($transfer->status, ['dispatched', 'received']) ? '#D97706' : '#E2E8F0' }}; color: {{ in_array($transfer->status, ['dispatched', 'received']) ? '#fff' : '#64748B' }}; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 900;">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <strong style="font-size: 13px; color: var(--text-main);">2. الشحن (In-Transit)</strong>
                <span style="font-size: 11px; color: var(--text-muted);">
                    {{ $transfer->dispatch_date ? \Carbon\Carbon::parse($transfer->dispatch_date)->format('Y-m-d H:i') : 'في انتظار الشحن' }}
                </span>
            </div>

            <!-- Step 3: Received -->
            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; z-index: 2; flex: 1;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: {{ $transfer->status === 'received' ? '#10B981' : '#E2E8F0' }}; color: {{ $transfer->status === 'received' ? '#fff' : '#64748B' }}; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 900;">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <strong style="font-size: 13px; color: var(--text-main);">3. الاستلام والتوريد</strong>
                <span style="font-size: 11px; color: var(--text-muted);">
                    {{ $transfer->received_date ? \Carbon\Carbon::parse($transfer->received_date)->format('Y-m-d H:i') : 'في انتظار الوصول' }}
                </span>
            </div>

        </div>
    </div>

    <!-- Details Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        
        <!-- Source Details -->
        <div class="erp-card" style="margin-bottom: 0; padding: 20px; border-right: 4px solid var(--primary);">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--primary); margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                الطرف المصدر (الشاحن)
            </h4>
            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">الفرع المصدر:</span>
                    <strong style="color: var(--text-main);">{{ $transfer->fromBranch?->name ?? '—' }} ({{ $transfer->fromBranch?->code }})</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">المستودع المصدر:</span>
                    <strong style="color: var(--text-main);">{{ $transfer->fromStore?->name ?? '—' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">القائم بالشحن:</span>
                    <strong style="color: var(--text-main);">{{ $transfer->dispatcher?->name ?? '—' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">تاريخ ووقت الشحن:</span>
                    <span style="color: var(--text-main);">{{ $transfer->dispatch_date ? \Carbon\Carbon::parse($transfer->dispatch_date)->format('Y-m-d H:i') : 'لم يتم بعد' }}</span>
                </div>
            </div>
        </div>

        <!-- Destination Details -->
        <div class="erp-card" style="margin-bottom: 0; padding: 20px; border-right: 4px solid #4338CA;">
            <h4 style="font-size: 14px; font-weight: 800; color: #4338CA; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-down-to-bracket"></i>
                الطرف المستلم (الوجهة)
            </h4>
            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">فرع الوجهة:</span>
                    <strong style="color: var(--text-main);">{{ $transfer->toBranch?->name ?? '—' }} ({{ $transfer->toBranch?->code }})</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">مستودع الاستلام:</span>
                    <strong style="color: var(--text-main);">{{ $transfer->toStore?->name ?? '—' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">القائم بالاستلام:</span>
                    <strong style="color: var(--text-main);">{{ $transfer->receiver?->name ?? '—' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">تاريخ ووقت الاستلام:</span>
                    <span style="color: var(--text-main);">{{ $transfer->received_date ? \Carbon\Carbon::parse($transfer->received_date)->format('Y-m-d H:i') : 'في الانتظار' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Logistics Card -->
    <div class="erp-card" style="margin-bottom: 0; padding: 18px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 13px;">
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 11.5px;">الناقل / شركة الشحن:</span>
                <strong style="color: var(--text-main); font-size: 14px;">{{ $transfer->shipping_company ?: 'النقل الداخلي للشركة' }}</strong>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 11.5px;">اسم السائق المفوّض:</span>
                <strong style="color: var(--text-main); font-size: 14px;">{{ $transfer->driver_name ?: '—' }}</strong>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 11.5px;">رقم لوحة المركبة:</span>
                <strong style="color: var(--text-main); font-size: 14px;">{{ $transfer->vehicle_plate ?: '—' }}</strong>
            </div>
            <div>
                <span style="color: var(--text-muted); display: block; font-size: 11.5px;">رقم البوليصة والتتبع:</span>
                <strong style="color: #4338CA; font-family: monospace; font-size: 14px;">{{ $transfer->tracking_number ?: '—' }}</strong>
            </div>
        </div>
        @if($transfer->notes)
        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--border-color); font-size: 12.5px;">
            <strong style="color: var(--text-main);">ملاحظات:</strong> {{ $transfer->notes }}
        </div>
        @endif
    </div>

    <!-- Items List Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-boxes-stacked" style="color: var(--primary); margin-left: 6px;"></i>
                قائمة الأصناف المحولة في هذه الشحنة ({{ $transfer->items->count() }})
            </h4>
            <span style="font-size: 13px; font-weight: 800; color: var(--primary);">
                القيمة الإجمالية: {{ number_format($transfer->total_cost, 2) }} ر.س
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">#</th>
                        <th style="padding: 12px 16px;">كود الصنف</th>
                        <th style="padding: 12px 16px;">اسم الصنف</th>
                        <th style="padding: 12px 16px;">الوحدة</th>
                        <th style="padding: 12px 16px; text-align: center;">الكمية المرسلة</th>
                        <th style="padding: 12px 16px; text-align: center;">الكمية المستلمة</th>
                        <th style="padding: 12px 16px;">متوسط التكلفة</th>
                        <th style="padding: 12px 16px;">إجمالي القيمة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfer->items as $idx => $it)
                    <tr style="border-bottom: 1px solid #EDF2F7;">
                        <td style="padding: 12px 16px; color: var(--text-muted);">{{ $idx + 1 }}</td>
                        <td style="padding: 12px 16px; font-family: monospace; font-weight: 700; color: var(--text-muted);">
                            {{ $it->item?->code ?? '—' }}
                        </td>
                        <td style="padding: 12px 16px; font-weight: 800; color: var(--text-main);">
                            {{ $it->item?->name ?? 'صنف محذوف' }}
                        </td>
                        <td style="padding: 12px 16px; color: var(--text-muted);">
                            {{ $it->item?->unit?->name ?? 'قطعة' }}
                        </td>
                        <td style="padding: 12px 16px; text-align: center; font-weight: 800; color: var(--text-main); font-size: 14px;">
                            {{ number_format($it->sent_quantity, 2) }}
                        </td>
                        <td style="padding: 12px 16px; text-align: center; font-weight: 800; font-size: 14px;">
                            @if($transfer->status === 'received')
                                <span style="color: #0F5132;">{{ number_format($it->received_quantity, 2) }}</span>
                            @else
                                <span style="color: var(--text-muted);">قيد الشحن</span>
                            @endif
                        </td>
                        <td style="padding: 12px 16px; color: var(--text-main);">
                            {{ number_format($it->unit_cost, 2) }} ر.س
                        </td>
                        <td style="padding: 12px 16px; font-weight: 800; color: var(--primary);">
                            {{ number_format($it->total_cost, 2) }} ر.س
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot style="background: #F8FAFC; border-top: 2px solid var(--border-color); font-weight: 800;">
                    <tr>
                        <td colspan="4" style="padding: 12px 16px; text-align: left;">الإجمالي:</td>
                        <td style="padding: 12px 16px; text-align: center; color: var(--text-main); font-size: 14px;">
                            {{ number_format($transfer->items->sum('sent_quantity'), 2) }}
                        </td>
                        <td style="padding: 12px 16px; text-align: center; color: #0F5132; font-size: 14px;">
                            {{ $transfer->status === 'received' ? number_format($transfer->items->sum('received_quantity'), 2) : '—' }}
                        </td>
                        <td></td>
                        <td style="padding: 12px 16px; color: var(--primary); font-size: 15px;">
                            {{ number_format($transfer->total_cost, 2) }} ر.س
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
