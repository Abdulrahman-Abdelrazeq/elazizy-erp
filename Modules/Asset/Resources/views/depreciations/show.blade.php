@extends('layouts.app')

@section('title', 'تفاصيل قيد إهلاك الأصل ' . $depreciation->asset?->asset_number)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 950px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-calculator"></i>
                    </span>
                    قيد إهلاك أصل: {{ $depreciation->asset?->name }}
                </h2>

                @if($depreciation->is_posted)
                    <span style="background: #D1E7DD; color: #0F5132; font-weight: 800; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-check-circle"></i> معتمد ومرحل للأستاذ العام
                    </span>
                @else
                    <span style="background: #FEF3C7; color: #D97706; font-weight: 800; padding: 4px 12px; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-clock"></i> بانتظار الترحيل
                    </span>
                @endif
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">
                الفترة المالية: {{ $depreciation->fiscal_period }} | التاريخ: {{ $depreciation->depreciation_date?->format('Y-m-d') }}
            </span>
        </div>

        <div style="display: flex; gap: 10px;">
            @if(!$depreciation->is_posted)
                <form action="{{ route('assets.depreciations.post-gl', $depreciation->id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer;">
                        <i class="fa-solid fa-cloud-arrow-up"></i> ترحيل القيد لليومية العامة
                    </button>
                </form>
            @endif
            <a href="{{ route('assets.depreciations.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div style="background: #D1E7DD; border: 1px solid #A3CFBB; color: #0F5132; padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
        <i class="fa-solid fa-circle-check" style="margin-left: 8px;"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Financial Card -->
    <div class="erp-card" style="padding: 22px; margin-bottom: 0;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div>
                <span style="font-size: 12px; color: var(--text-muted); display: block;">قسط الإهلاك المخصوم:</span>
                <strong style="font-size: 20px; font-weight: 900; color: #DC2626;">{{ number_format($depreciation->depreciation_amount, 2) }} ر.س</strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); display: block;">مجمع الإهلاك بعد القيد:</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--text-main);">{{ number_format($depreciation->accumulated_depreciation_after, 2) }} ر.س</strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); display: block;">صافي القيمة الدفترية المتبقية:</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ number_format($depreciation->book_value_after, 2) }} ر.س</strong>
            </div>

            <div>
                <span style="font-size: 12px; color: var(--text-muted); display: block;">رقم قيد اليومية العامة:</span>
                @if($depreciation->journalEntry)
                    <strong style="font-size: 18px; font-weight: 900; color: #4338CA; font-family: monospace;">{{ $depreciation->journalEntry->entry_number }}</strong>
                @else
                    <span style="color: var(--text-muted); font-size: 14px;">لم يُرحّل بعد</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Journal Entry Details Card (if posted) -->
    @if($depreciation->journalEntry)
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-file-invoice" style="color: #4338CA; margin-left: 6px;"></i>
                تفاصيل قيد اليومية العامة المرتبط (Journal Entry #{{ $depreciation->journalEntry->entry_number }})
            </h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">كود الحساب</th>
                        <th style="padding: 12px 16px;">اسم الحساب</th>
                        <th style="padding: 12px 16px;">البيان</th>
                        <th style="padding: 12px 16px;">مدين (Debit)</th>
                        <th style="padding: 12px 16px;">دائن (Credit)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($depreciation->journalEntry->lines as $line)
                    <tr style="border-bottom: 1px solid #EDF2F7;">
                        <td style="padding: 12px 16px; font-family: monospace; font-weight: 700;">{{ $line->account?->code }}</td>
                        <td style="padding: 12px 16px; font-weight: 800; color: var(--text-main);">{{ $line->account?->name }}</td>
                        <td style="padding: 12px 16px; color: var(--text-muted);">{{ $line->description }}</td>
                        <td style="padding: 12px 16px; font-weight: 800; color: {{ $line->debit > 0 ? '#0F5132' : 'var(--text-muted)' }};">
                            {{ $line->debit > 0 ? number_format($line->debit, 2) . ' ر.س' : '—' }}
                        </td>
                        <td style="padding: 12px 16px; font-weight: 800; color: {{ $line->credit > 0 ? '#4338CA' : 'var(--text-muted)' }};">
                            {{ $line->credit > 0 ? number_format($line->credit, 2) . ' ر.س' : '—' }}
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
