@extends('layouts.app')

@section('title', 'ورديات وجرد الكاشير')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-cash-register"></i>
                </span>
                ورديات وجرد الكاشير (Cashier Shifts & Drawer Log)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                متابعة فتح وإغلاق ورديات الكاشير، عهدة البداية، مبيعات النقدي والشبكة، وحساب العجز والزيادة (تقرير Z)
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('pos.screen') }}" style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 10px 18px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(15, 81, 50, 0.25);">
                <i class="fa-solid fa-desktop"></i>
                دخول شاشة الكاشير السريعة
            </a>
            <a href="{{ route('pos.terminals.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-server" style="color: var(--accent-gold);"></i>
                نقاط البيع والأجهزة
            </a>
            <button type="button" onclick="openStartShiftModal()" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-door-open"></i>
                فتح وردية جديدة
            </button>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-lock-open"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">ورديات نشطة مفتوحة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #10B981;">{{ $stats['active_shifts'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مبيعات الكاش لليوم</span>
                <strong style="font-size: 19px; font-weight: 900; color: var(--primary);">{{ formatMoney($stats['today_sales']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-history"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الورديات المسجلة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['total_shifts'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أجهزة نقاط البيع النشطة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $terminals->count() }}</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('pos.shifts.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px 100px; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    حالة الوردية
                </label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوحة (قيد العمل)</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>مغلقة (تم الجرد)</option>
                </select>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    نقطة البيع / الكاشير
                </label>
                <select name="terminal_id" class="erp-input">
                    <option value="">جميع الأجهزة</option>
                    @foreach($terminals as $term)
                        <option value="{{ $term->id }}" {{ request('terminal_id') == $term->id ? 'selected' : '' }}>
                            {{ $term->name }} ({{ $term->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i>
                تصفية
            </button>

            <a href="{{ route('pos.shifts.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 8px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                إعادة ضبط
            </a>
        </form>
    </div>

    <!-- Shifts Data Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 130px;">رقم الوردية</th>
                        <th>نقطة البيع</th>
                        <th>الكاشير المستلم</th>
                        <th>وقت البداية</th>
                        <th>وقت الإغلاق</th>
                        <th>عهدة الافتتاح</th>
                        <th>مبيعات الكاش</th>
                        <th>مبيعات الشبكة</th>
                        <th>المتوقع بالدرج</th>
                        <th>الفعلي بالجرد</th>
                        <th>الفارق (عجز/زيادة)</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 130px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shifts as $shift)
                        <tr>
                            <td>
                                <a href="{{ route('pos.shifts.show', $shift->id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none;">
                                    {{ $shift->shift_number }}
                                </a>
                            </td>
                            <td>
                                <strong style="color: var(--text-main);">{{ $shift->terminal->name ?? '-' }}</strong>
                                <span style="display: block; font-size: 11px; color: var(--text-muted);">{{ $shift->terminal->code ?? '' }}</span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 800;">
                                        {{ mb_substr($shift->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <span style="font-weight: 600;">{{ $shift->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 12px; font-weight: 600;">{{ $shift->started_at ? $shift->started_at->format('Y-m-d H:i') : '-' }}</span>
                            </td>
                            <td>
                                <span style="font-size: 12px; font-weight: 600;">
                                    {{ $shift->ended_at ? $shift->ended_at->format('Y-m-d H:i') : '—' }}
                                </span>
                            </td>
                            <td style="font-weight: 700;">{{ formatMoney($shift->opening_balance) }}</td>
                            <td style="font-weight: 700; color: #0F5132;">{{ formatMoney($shift->cash_sales) }}</td>
                            <td style="font-weight: 700; color: #4338CA;">{{ formatMoney($shift->card_sales) }}</td>
                            <td style="font-weight: 800;">{{ formatMoney($shift->expected_cash) }}</td>
                            <td>
                                @if($shift->isClosed())
                                    <strong style="color: var(--text-main);">{{ formatMoney($shift->actual_cash) }}</strong>
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">قيد التداول</span>
                                @endif
                            </td>
                            <td>
                                @if($shift->isClosed())
                                    @php $diff = (float) $shift->cash_difference; @endphp
                                    @if(abs($diff) < 0.01)
                                        <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">متطابق (0.00)</span>
                                    @elseif($diff < 0)
                                        <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">عجز: {{ formatMoney(abs($diff)) }}</span>
                                    @else
                                        <span class="badge" style="background: #D1FAE5; color: #059669; font-weight: 700;">زيادة: +{{ formatMoney($diff) }}</span>
                                    @endif
                                @else
                                    <span style="color: var(--text-muted); font-size: 12px;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($shift->isOpen())
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-circle" style="font-size: 8px;"></i>
                                        مفتوحة
                                    </span>
                                @else
                                    <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 700;">
                                        مغلقة
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="{{ route('pos.shifts.show', $shift->id) }}" title="عرض التقرير Z" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pos.shifts.print_z', $shift->id) }}" target="_blank" title="طباعة إيصال Z الحراري" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--accent-gold); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                                <i class="fa-solid fa-cash-register" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                <span style="font-size: 15px; font-weight: 700; display: block;">لا توجد ورديات مسجلة مطابقة للبحث</span>
                                <span style="font-size: 13px;">قم بفتح وردية جديدة لبدء عمليات البيع وإدارة الدرج</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($shifts->hasPages())
            <div style="padding: 16px; border-top: 1px solid var(--border-color);">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Start New Shift -->
<div id="startShiftModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-door-open" style="color: var(--accent-gold);"></i>
                فتح وردية كاشير جديدة
            </h4>
            <button type="button" onclick="closeStartShiftModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('pos.shifts.open') }}" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div>
                <label style="font-size: 13px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    اختر نقطة البيع / الجهاز <span style="color: #DC2626;">*</span>
                </label>
                <select name="terminal_id" class="erp-input" required>
                    <option value="">-- حدد نقطة البيع --</option>
                    @foreach($terminals as $term)
                        <option value="{{ $term->id }}">{{ $term->name }} ({{ $term->code }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    عهدة الافتتاح النقدية في الدرج <span style="color: #DC2626;">*</span>
                </label>
                <div style="position: relative;">
                    <input type="number" step="0.01" min="0" name="opening_balance" value="0.00" class="erp-input" style="padding-left: 45px; font-weight: 800; font-size: 16px;" required>
                    <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; font-size: 12px; color: var(--text-muted);">ج.م</span>
                </div>
                <small style="font-size: 11.5px; color: var(--text-muted); margin-top: 4px; display: block;">
                    أدخل المبلغ النقدي (الفكة / الصرافة) المتواجد في الدرج قبل بدء البيع
                </small>
            </div>

            <div>
                <label style="font-size: 13px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    ملاحظات بدء الوردية
                </label>
                <textarea name="start_notes" rows="2" class="erp-input" placeholder="أي ملاحظات حول استلام العهدة أو الجهاز..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeStartShiftModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-weight: 800;">
                    <i class="fa-solid fa-check-circle"></i>
                    تأكيد وفتح الوردية
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openStartShiftModal() {
        const modal = document.getElementById('startShiftModal');
        modal.style.display = 'flex';
    }
    function closeStartShiftModal() {
        const modal = document.getElementById('startShiftModal');
        modal.style.display = 'none';
    }
</script>
@endsection

