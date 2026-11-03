@extends('layouts.app')

@section('title', 'تفاصيل القيد اليومي #' . $entry->entry_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </span>
                    سند قيد يومية مزدوج #{{ $entry->entry_number }}
                </h2>
                @if ($entry->status === 'posted')
                    <span style="background: #D1E7DD; color: #0F5132; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; border: 1px solid #A3CFBB;">
                        <i class="fa-solid fa-circle-check"></i> معتمد ومؤثر
                    </span>
                @else
                    <span style="background: #FEF3C7; color: #D97706; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 800; border: 1px solid #FDE68A;">
                        <i class="fa-solid fa-hourglass-half"></i> مسودة قيد
                    </span>
                @endif
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">
                تاريخ التسجيل: {{ \Carbon\Carbon::parse($entry->date)->format('Y-m-d') }} | مسجل بواسطة: {{ $entry->creator?->name ?? 'النظام' }}
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('accounting.journal-entries.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة للدفتر
            </a>

            <a href="{{ route('accounting.journal-entries.print', $entry->id) }}" target="_blank" style="background: #4338CA; color: #fff; padding: 9px 18px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(67, 56, 202, 0.2);">
                <i class="fa-solid fa-print"></i>
                طباعة سند القيد
            </a>

            <form method="POST" action="{{ route('accounting.journal-entries.destroy', $entry->id) }}" onsubmit="return confirm('تحذير محاسبي: هل أنت متأكد من حذف/إلغاء هذا القيد؟ سيتم عكس تأثيره على أرصدة الحسابات فوراً!')" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: #FEE2E2; border: 1px solid #FCA5A5; color: #991B1B; padding: 9px 16px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-trash-can"></i>
                    حذف القيد
                </button>
            </form>
        </div>
    </div>

    <!-- Metadata Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px;">
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">رقم القيد الدفتري</span>
                <strong style="font-size: 15px; font-family: monospace; color: var(--primary);">{{ $entry->entry_number }}</strong>
            </div>

            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">تاريخ الاستحقاق / الحركة</span>
                <strong style="font-size: 15px; color: var(--text-main);">{{ \Carbon\Carbon::parse($entry->date)->format('Y-m-d') }}</strong>
            </div>

            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">المستند المرجعي</span>
                <strong style="font-size: 15px; color: var(--text-main);">{{ $entry->reference ?: 'بدون مرجع' }}</strong>
            </div>

            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">المصدر المحاسبي</span>
                <span style="font-size: 13px; font-weight: 800; color: #4338CA;">
                    @if($entry->source_type === 'manual')
                        قيد يدوي عام
                    @elseif($entry->source_type === 'invoice')
                        فاتورة مبيعات/مشتريات
                    @elseif($entry->source_type === 'voucher')
                        سند قبض/صرف
                    @elseif($entry->source_type === 'opening')
                        قيد افتتاحي
                    @else
                        {{ $entry->source_type }}
                    @endif
                </span>
            </div>

            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">الفرع</span>
                <span style="font-size: 14px; font-weight: 700; color: var(--text-main);">{{ $entry->branch?->name ?? 'المركز الرئيسي' }}</span>
            </div>

            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">الاعتماد والترحيل</span>
                <span style="font-size: 13px; color: var(--text-main);">
                    @if($entry->poster)
                        {{ $entry->poster->name }} ({{ \Carbon\Carbon::parse($entry->posted_at)->format('Y-m-d H:i') }})
                    @else
                        <span style="color: var(--text-muted);">غير مرحل بعد</span>
                    @endif
                </span>
            </div>
        </div>

        <div style="margin-top: 18px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 4px;">البيان العام والشرح المحاسبي:</span>
            <div style="font-size: 14px; font-weight: 700; color: var(--text-main); line-height: 1.6; background: #F8FAFC; padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border-color);">
                {{ $entry->description }}
            </div>
        </div>
    </div>

    <!-- Journal Lines Details -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 18px; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-list-check"></i>
            جدول أطراف القيد المزدوج (Debit & Credit Lines)
        </h3>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 45px;">#</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">كود الحساب</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم الحساب</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 180px;">مركز التكلفة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">البيان التفصيلي</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 150px;">مدين (Debit)</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 150px;">دائن (Credit)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entry->lines as $idx => $line)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; text-align: center; font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border-color);">
                            {{ $idx + 1 }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            {{ $line->account?->code ?? '-' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color);">
                            <a href="{{ route('accounting.ledger.index', ['account_id' => $line->account_id]) }}" style="font-weight: 800; color: var(--text-main); text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                {{ $line->account?->name ?? 'حساب غير محدد' }}
                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px; color: var(--text-muted);"></i>
                            </a>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                            @if($line->costCenter)
                                <span style="background: rgba(217, 119, 6, 0.1); color: #B45309; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size: 11.5px;">
                                    <i class="fa-solid fa-crosshairs"></i> {{ $line->costCenter->name }}
                                </span>
                            @else
                                <span style="color: #94A3B8;">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-main);">
                            {{ $line->description ?: $entry->description }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: right; font-weight: 800; font-size: 14px; color: {{ $line->debit > 0 ? '#0F5132' : '#94A3B8' }};">
                            {{ $line->debit > 0 ? formatMoney($line->debit) : '-' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: right; font-weight: 800; font-size: 14px; color: {{ $line->credit > 0 ? '#4338CA' : '#94A3B8' }};">
                            {{ $line->credit > 0 ? formatMoney($line->credit) : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #F1F5F9; font-weight: 900; font-size: 14px;">
                        <td colspan="5" style="padding: 14px; text-align: left; color: var(--text-main);">
                            الإجمالي العام المتوازن:
                        </td>
                        <td style="padding: 14px; text-align: right; color: #0F5132; font-size: 16px;">
                            {{ formatMoney($entry->total_debit) }}
                        </td>
                        <td style="padding: 14px; text-align: right; color: #4338CA; font-size: 16px;">
                            {{ formatMoney($entry->total_credit) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
            <div style="background: #D1E7DD; color: #0F5132; padding: 10px 24px; border-radius: 10px; font-weight: 800; font-size: 14px; display: inline-flex; align-items: center; gap: 10px; border: 1px solid #A3CFBB;">
                <i class="fa-solid fa-scale-balanced" style="font-size: 18px;"></i>
                <span>القيد متوازن محاسبياً (المدين = الدائن)</span>
            </div>
        </div>
    </div>
</div>
@endsection
