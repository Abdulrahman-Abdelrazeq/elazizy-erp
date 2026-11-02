@extends('layouts.app')

@section('title', 'تفاصيل السند المالي - ' . $voucher->voucher_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 900px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <a href="{{ route('treasury.vouchers.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 700;">
                    <i class="fa-solid fa-arrow-right"></i> سندات القبض والصرف
                </a>
                <span style="color: var(--border-color);">/</span>
                <span style="font-size: 14px; font-weight: 800; color: {{ $voucher->type === 'receipt' ? 'var(--primary)' : '#DC2626' }};">
                    {{ $voucher->voucher_number }}
                </span>
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                {{ $voucher->type_label }} - {{ $voucher->voucher_number }}
                @if($voucher->status === 'approved')
                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-size: 12px; font-weight: 700;">
                        <i class="fa-solid fa-check-circle"></i> معتمد ومؤثر بالخزينة
                    </span>
                @else
                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-size: 12px; font-weight: 700;">
                        <i class="fa-solid fa-ban"></i> ملغي
                    </span>
                @endif
            </h2>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('treasury.vouchers.print', $voucher->id) }}" target="_blank" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 18px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-print" style="color: var(--accent-gold);"></i>
                طباعة السند الرسمي
            </a>
            <a href="{{ route('treasury.vouchers.create') }}?type={{ $voucher->type }}" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-plus-circle"></i>
                تحرير سند جديد
            </a>
        </div>
    </div>

    <!-- Main Voucher Details Card -->
    <div class="erp-card" style="padding: 30px; margin-bottom: 0; border-top: 5px solid {{ $voucher->type === 'receipt' ? 'var(--primary)' : '#DC2626' }};">
        
        <!-- Big Amount Banner -->
        <div style="background: {{ $voucher->type === 'receipt' ? 'rgba(15, 81, 50, 0.04)' : 'rgba(220, 38, 38, 0.04)' }}; border: 1.5px dashed {{ $voucher->type === 'receipt' ? 'var(--primary)' : '#DC2626' }}; border-radius: 14px; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
            <div>
                <span style="font-size: 13px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">
                    المبلغ الإجمالي المسجل بالسند:
                </span>
                <strong style="font-size: 30px; font-weight: 900; color: {{ $voucher->type === 'receipt' ? '#0F5132' : '#DC2626' }};">
                    {{ formatMoney($voucher->amount) }}
                </strong>
            </div>
            <div style="text-align: left;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">
                    الخزينة / الحساب المعني:
                </span>
                <strong style="font-size: 17px; color: var(--text-main);">
                    {{ $voucher->treasury->name ?? '-' }}
                </strong>
                <span style="display: block; font-size: 12px; color: var(--text-muted);">
                    (الرصيد الحالي: {{ formatMoney($voucher->treasury->current_balance) }})
                </span>
            </div>
        </div>

        <!-- Meta Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color);">
            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">رقم السند</span>
                <strong style="font-size: 16px; color: var(--primary); font-family: monospace;">{{ $voucher->voucher_number }}</strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">تاريخ السند</span>
                <strong style="font-size: 15px; color: var(--text-main);">{{ $voucher->date->format('Y-m-d') }}</strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">طريقة الدفع</span>
                <span class="badge" style="background: #F1F5F9; color: var(--text-main); font-weight: 700; font-size: 13px;">
                    {{ $voucher->payment_method_label }}
                </span>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">رقم المرجع / الشيك</span>
                <strong style="font-size: 14px; color: var(--text-main);">{{ $voucher->reference_number ?: '—' }}</strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">
                    {{ $voucher->type === 'receipt' ? 'استلمنا من السيد / الجهة' : 'صرفنا إلى السيد / الجهة' }}
                </span>
                <strong style="font-size: 15px; color: var(--text-main);">
                    {{ $voucher->client->name ?? ($voucher->received_from_or_paid_to ?: '—') }}
                </strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">البند المالي / التصنيف</span>
                <strong style="font-size: 15px; color: var(--text-main);">{{ $voucher->category->name ?? 'غير مصنف' }}</strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">حرر بواسطة</span>
                <strong style="font-size: 14px; color: var(--text-main);">{{ $voucher->creator->name ?? 'مستخدم النظام' }}</strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); font-weight: 700; display: block; margin-bottom: 4px;">تاريخ الإنشاء في النظام</span>
                <strong style="font-size: 13px; color: var(--text-muted);">{{ $voucher->created_at->format('Y-m-d h:i A') }}</strong>
            </div>
        </div>

        <!-- Statement / Notes -->
        <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 10px; padding: 18px; margin-bottom: 24px;">
            <strong style="font-size: 13px; color: var(--text-muted); display: block; margin-bottom: 6px;">
                وذلك عن (البيان والغرض من السند):
            </strong>
            <p style="font-size: 15px; font-weight: 700; color: var(--text-main); margin: 0; line-height: 1.6;">
                {{ $voucher->notes }}
            </p>
        </div>

        <!-- Signatures Area -->
        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; padding-top: 20px; border-top: 1px dashed var(--border-color); text-align: center;">
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">أمين الخزينة / الصندوق</span>
                <div style="margin-top: 35px; border-bottom: 1px dotted var(--border-color); width: 80%; margin-inline: auto;"></div>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">المحاسب المالي</span>
                <div style="margin-top: 35px; border-bottom: 1px dotted var(--border-color); width: 80%; margin-inline: auto;"></div>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">
                    {{ $voucher->type === 'receipt' ? 'توقيع المستلم' : 'توقيع المستلم / المورد' }}
                </span>
                <div style="margin-top: 35px; border-bottom: 1px dotted var(--border-color); width: 80%; margin-inline: auto;"></div>
            </div>
        </div>

    </div>

</div>
@endsection
