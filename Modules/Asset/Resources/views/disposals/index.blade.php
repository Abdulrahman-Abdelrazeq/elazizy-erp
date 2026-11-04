@extends('layouts.app')

@section('title', 'سجل استبعاد وتكهين وبيع الأصول الثابتة (Asset Disposals & Sales)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(220, 38, 38, 0.1); color: #DC2626; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-recycle"></i>
                </span>
                سجل استبعاد وتكهين وبيع الأصول (Asset Disposals & Sales)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                معالجة استبعاد الأصول الثابتة، حساب الأرباح والخسائر الرأسمالية الناتجة عن البيع أو التكهين، وإقفال الحسابات
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('assets.assets.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-building-columns" style="color: var(--primary);"></i>
                سجل الأصول
            </a>
            <a href="{{ route('assets.disposals.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none; background: #DC2626;">
                <i class="fa-solid fa-plus-circle"></i>
                تسجيل استبعاد / بيع أصل
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-list-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الأصول المستبعدة</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_disposals'] }} أصل</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي حصيلة المبيعات</span>
                <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ number_format($stats['total_sales_val'], 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أرباح رأسمالية محققة</span>
                <strong style="font-size: 19px; font-weight: 900; color: #0F5132;">{{ number_format($stats['total_gains'], 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #EF4444; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #EF4444; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-arrow-trend-down"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">خسائر استبعاد رأسمالية</span>
                <strong style="font-size: 19px; font-weight: 900; color: #DC2626;">{{ number_format($stats['total_losses'], 2) }} ر.س</strong>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
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

    <!-- Disposals Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-list-check" style="color: #DC2626; margin-left: 6px;"></i>
                سجل عمليات استبعاد وتكهين الأصول ({{ $disposals->total() }})
            </h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">الأصل المستبعد</th>
                        <th style="padding: 12px 16px;">الفرع والتصنيف</th>
                        <th style="padding: 12px 16px;">تاريخ الاستبعاد</th>
                        <th style="padding: 12px 16px; text-align: center;">نوع العملية</th>
                        <th style="padding: 12px 16px;">القيمة الدفترية وقتها</th>
                        <th style="padding: 12px 16px;">سعر البيع المحقق</th>
                        <th style="padding: 12px 16px;">الأرباح / الخسائر</th>
                        <th style="padding: 12px 16px;">الخزينة المودع بها</th>
                        <th style="padding: 12px 16px;">المشتري / الملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disposals as $disp)
                    <tr style="border-bottom: 1px solid #EDF2F7; transition: background 0.2s ease;">
                        <td style="padding: 14px 16px;">
                            <a href="{{ route('assets.assets.show', $disp->asset_id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none; display: block;">
                                {{ $disp->asset?->name ?? 'أصل محذوف' }}
                            </a>
                            <small style="color: var(--text-muted); font-family: monospace;">{{ $disp->asset?->asset_number }}</small>
                        </td>
                        <td style="padding: 14px 16px;">
                            <strong style="color: var(--text-main); display: block;">{{ $disp->asset?->category?->name ?? '—' }}</strong>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-network-wired"></i> {{ $disp->asset?->branch?->name ?? '—' }}</small>
                        </td>
                        <td style="padding: 14px 16px; font-weight: 600; color: var(--text-main);">
                            {{ $disp->disposal_date ? $disp->disposal_date->format('Y-m-d') : '—' }}
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            @if($disp->disposal_type === 'sale')
                                <span style="background: #E0E7FF; color: #4338CA; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-handshake"></i> بيع أصل
                                </span>
                            @elseif($disp->disposal_type === 'scrap')
                                <span style="background: #FEF3C7; color: #D97706; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-recycle"></i> تكهين / خردة
                                </span>
                            @elseif($disp->disposal_type === 'loss')
                                <span style="background: #FEE2E2; color: #DC2626; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-circle-exclamation"></i> إتلاف / فقد
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; font-weight: 700; color: var(--text-main);">
                            {{ number_format($disp->book_value_at_disposal, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; font-weight: 800; color: #4338CA;">
                            {{ number_format($disp->sale_price, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; font-weight: 900;">
                            @if($disp->gain_or_loss > 0)
                                <span style="color: #0F5132;"><i class="fa-solid fa-arrow-up"></i> +{{ number_format($disp->gain_or_loss, 2) }} ر.س (ربح)</span>
                            @elseif($disp->gain_or_loss < 0)
                                <span style="color: #DC2626;"><i class="fa-solid fa-arrow-down"></i> {{ number_format($disp->gain_or_loss, 2) }} ر.س (خسارة)</span>
                            @else
                                <span style="color: var(--text-muted);">0.00 ر.س</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; font-size: 12px;">
                            @if($disp->treasury)
                                <strong style="color: var(--primary);"><i class="fa-solid fa-vault"></i> {{ $disp->treasury->name }}</strong>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; font-size: 12px; color: var(--text-muted);">
                            {{ $disp->buyer_name ?: ($disp->notes ?: '—') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-recycle" style="font-size: 40px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                            لا توجد عمليات استبعاد أو بيع أصول مسجلة حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($disposals->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $disposals->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
