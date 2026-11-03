@extends('layouts.app')

@section('title', 'التحويلات البينية للشحنات والمخزون (Inter-Branch Transfers)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-truck-fast"></i>
                </span>
                التحويلات اللوجستية والمخزنية بين الفروع (Inter-Branch Transfers)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تتبع مسارات الشحن والاستلام، حركة البضائع في الطريق (In-Transit)، وبوالص التحويل المخزني
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('branches.branches.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-network-wired" style="color: var(--primary);"></i>
                دليل الفروع
            </a>
            <a href="{{ route('branches.analytics.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-line" style="color: #4338CA;"></i>
                مقارنات أداء الفروع
            </a>
            <a href="{{ route('branches.transfers.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-plus-circle"></i>
                إنشاء إذن تحويل بيني جديد
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clipboard-list"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي التحويلات</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total'] }} تحويل</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-truck-moving"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">في الطريق (In-Transit)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['in_transit'] }} شحنة</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">شحنات تم استلامها</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['received'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">قيمة البضاعة المستلمة</span>
                <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ number_format($stats['total_value'], 2) }} ر.س</strong>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('branches.transfers.index') }}" style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr 1fr 90px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">حالة التحويل</label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة (Draft)</option>
                    <option value="dispatched" {{ request('status') === 'dispatched' ? 'selected' : '' }}>تم الشحن (In-Transit)</option>
                    <option value="received" {{ request('status') === 'received' ? 'selected' : '' }}>تم الاستلام والتوريد (Received)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي (Cancelled)</option>
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الفرع المصدر (الشاحن)</label>
                <select name="from_branch_id" class="erp-input">
                    <option value="">كل الفروع المصدر</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('from_branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">الفرع الوجهة (المستلم)</label>
                <select name="to_branch_id" class="erp-input">
                    <option value="">كل الفروع المستلمة</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('to_branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="date_from" class="erp-input" value="{{ request('date_from') }}">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="date_to" class="erp-input" value="{{ request('date_to') }}">
            </div>

            <div>
                <button type="submit" class="btn-primary-custom" style="width: 100%; padding: 10px; font-size: 13px; font-weight: 800; justify-content: center;">
                    <i class="fa-solid fa-filter"></i> تصفية
                </button>
            </div>
        </form>
    </div>

    <!-- Transfers Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-list-check" style="color: var(--primary); margin-left: 6px;"></i>
                سجل شحنات وأوامر التحويل البينية ({{ $transfers->total() }})
            </h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">رقم التحويل</th>
                        <th style="padding: 12px 16px;">التاريخ</th>
                        <th style="padding: 12px 16px;">الفرع والمستودع المصدر</th>
                        <th style="padding: 12px 16px;">الفرع والمستودع المستلم</th>
                        <th style="padding: 12px 16px;">اللوجستيات / السائق</th>
                        <th style="padding: 12px 16px; text-align: center;">الأصناف</th>
                        <th style="padding: 12px 16px;">التكلفة الإجمالية</th>
                        <th style="padding: 12px 16px; text-align: center;">الحالة</th>
                        <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transfers as $t)
                    <tr style="border-bottom: 1px solid #EDF2F7; transition: background 0.2s ease;">
                        <td style="padding: 14px 16px;">
                            <a href="{{ route('branches.transfers.show', $t->id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-file-invoice"></i>
                                {{ $t->transfer_number }}
                            </a>
                        </td>
                        <td style="padding: 14px 16px; color: var(--text-main); font-weight: 600;">
                            {{ $t->transfer_date ? \Carbon\Carbon::parse($t->transfer_date)->format('Y-m-d') : '—' }}
                        </td>
                        <td style="padding: 14px 16px;">
                            <strong style="color: var(--text-main); display: block;">{{ $t->fromBranch?->name ?? '—' }}</strong>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-warehouse"></i> {{ $t->fromStore?->name ?? '—' }}</small>
                        </td>
                        <td style="padding: 14px 16px;">
                            <strong style="color: var(--text-main); display: block;">{{ $t->toBranch?->name ?? '—' }}</strong>
                            <small style="color: var(--text-muted);"><i class="fa-solid fa-warehouse"></i> {{ $t->toStore?->name ?? '—' }}</small>
                        </td>
                        <td style="padding: 14px 16px; font-size: 12px;">
                            @if($t->driver_name || $t->vehicle_plate || $t->shipping_company)
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    @if($t->driver_name)
                                        <span style="font-weight: 700; color: var(--text-main);"><i class="fa-solid fa-user-tie" style="color: var(--accent-gold);"></i> {{ $t->driver_name }}</span>
                                    @endif
                                    @if($t->vehicle_plate)
                                        <span style="color: var(--text-muted);"><i class="fa-solid fa-truck"></i> {{ $t->vehicle_plate }}</span>
                                    @endif
                                    @if($t->tracking_number)
                                        <span style="color: #4338CA; font-family: monospace;">#{{ $t->tracking_number }}</span>
                                    @endif
                                </div>
                            @else
                                <span style="color: var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            <span style="background: #F1F5F9; color: var(--text-main); font-weight: 800; padding: 4px 10px; border-radius: 8px; font-size: 12px;">
                                {{ $t->total_items_count }} صنف
                            </span>
                        </td>
                        <td style="padding: 14px 16px; font-weight: 800; color: var(--primary);">
                            {{ number_format($t->total_cost, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            @if($t->status === 'draft')
                                <span style="background: #F1F5F9; color: #475569; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-regular fa-clock"></i> مسودة
                                </span>
                            @elseif($t->status === 'dispatched')
                                <span style="background: #FEF3C7; color: #D97706; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-truck-moving"></i> في الطريق (In-Transit)
                                </span>
                            @elseif($t->status === 'received')
                                <span style="background: #D1E7DD; color: #0F5132; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-check-circle"></i> تم الاستلام
                                </span>
                            @elseif($t->status === 'cancelled')
                                <span style="background: #FEE2E2; color: #DC2626; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-ban"></i> ملغي
                                </span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('branches.transfers.show', $t->id) }}" title="عرض وتتبع" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('branches.transfers.print', $t->id) }}" target="_blank" title="طباعة بوليصة التحويل" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: #4338CA; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @if($t->status === 'draft')
                                    <form action="{{ route('branches.transfers.destroy', $t->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف مسودة التحويل؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="حذف" style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; border: 1px solid #FCA5A5; color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fa-solid fa-boxes-packing" style="font-size: 40px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                            لا توجد تحويلات بينية مسجلة حالياً
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transfers->hasPages())
        <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
            {{ $transfers->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
