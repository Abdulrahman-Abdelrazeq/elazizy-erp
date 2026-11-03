@extends('layouts.app')

@section('title', 'دفتر القيود اليومية (Journal Entries)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-book"></i>
                </span>
                دفتر القيود اليومية المزدوجة (Journal Entries)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                توثيق القيود المحاسبية الآلية واليدوية، مطابقة أطراف القيد (من حـ/ - إلى حـ/) ومتابعة الحركات المعتمدة
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('accounting.accounts.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-sitemap" style="color: var(--accent-gold);"></i>
                شجرة الحسابات
            </a>
            <a href="{{ route('accounting.ledger.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-book-open" style="color: #0F5132;"></i>
                دفتر الأستاذ
            </a>
            <a href="{{ route('accounting.journal-entries.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-plus-circle"></i>
                إنشاء قيد يومية جديد
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-book"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي عدد القيود المسجلة</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total_entries'] }} قيد</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الحركات المدينة (مدين)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['total_debit']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الحركات الدائنة (دائن)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['total_credit']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">قيود معتمدة ومؤثرة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ $stats['posted_count'] }}</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('accounting.journal-entries.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) 120px 100px; gap: 12px; align-items: flex-end;">
            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">حالة القيد</label>
                <select name="status" class="erp-input">
                    <option value="">جميع الحالات</option>
                    <option value="posted" {{ request('status') === 'posted' ? 'selected' : '' }}>معتمد ومؤثر (Posted)</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة قيد (Draft)</option>
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">مصدر القيد</label>
                <select name="source_type" class="erp-input">
                    <option value="">جميع المصادر</option>
                    <option value="manual" {{ request('source_type') === 'manual' ? 'selected' : '' }}>قيد يدوي عام</option>
                    <option value="invoice" {{ request('source_type') === 'invoice' ? 'selected' : '' }}>فاتورة مبيعات/مشتريات</option>
                    <option value="voucher" {{ request('source_type') === 'voucher' ? 'selected' : '' }}>سند قبض/صرف</option>
                    <option value="opening" {{ request('source_type') === 'opening' ? 'selected' : '' }}>قيد افتتاحي</option>
                </select>
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">من تاريخ</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}" class="erp-input">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}" class="erp-input">
            </div>

            <div>
                <label style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 5px;">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" class="erp-input" placeholder="رقم القيد، البيان، المرجع...">
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>

            <a href="{{ route('accounting.journal-entries.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 8px; font-size: 12.5px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                إعادة ضبط
            </a>
        </form>
    </div>

    <!-- Journal Entries Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 130px;">رقم القيد</th>
                        <th>التاريخ</th>
                        <th>البيان العام للقيد</th>
                        <th>المرجع</th>
                        <th>مصدر القيد</th>
                        <th style="color: #0F5132;">إجمالي المدين</th>
                        <th style="color: #4338CA;">إجمالي الدائن</th>
                        <th>التوازن</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 130px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $e)
                        <tr>
                            <td>
                                <a href="{{ route('accounting.journal-entries.show', $e->id) }}" style="font-weight: 800; color: var(--primary); text-decoration: none; font-family: monospace; font-size: 13.5px;">
                                    {{ $e->entry_number }}
                                </a>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; font-weight: 600;">{{ $e->date->format('Y-m-d') }}</span>
                            </td>
                            <td>
                                <strong style="color: var(--text-main); font-size: 13px;">{{ Str::limit($e->description, 50) }}</strong>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--text-muted);">{{ $e->reference ?: '—' }}</span>
                            </td>
                            <td>
                                <span class="badge" style="background: #F1F5F9; color: var(--text-main); font-weight: 600;">
                                    {{ $e->source_label }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 14.5px; font-weight: 800; color: #0F5132;">
                                    {{ formatMoney($e->total_debit) }}
                                </strong>
                            </td>
                            <td>
                                <strong style="font-size: 14.5px; font-weight: 800; color: #4338CA;">
                                    {{ formatMoney($e->total_credit) }}
                                </strong>
                            </td>
                            <td>
                                @if($e->isBalanced())
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">
                                        <i class="fa-solid fa-check"></i> متوازن
                                    </span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">
                                        <i class="fa-solid fa-triangle-exclamation"></i> غير متوازن
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($e->status === 'posted')
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 700;">
                                        معتمد
                                    </span>
                                @else
                                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700;">
                                        مسودة
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="{{ route('accounting.journal-entries.show', $e->id) }}" title="عرض القيد" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('accounting.journal-entries.print', $e->id) }}" target="_blank" title="طباعة سند القيد" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--accent-gold); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <form action="{{ route('accounting.journal-entries.destroy', $e->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا القيد المحاسبي؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="حذف" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                                <i class="fa-solid fa-book" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                <span style="font-size: 15px; font-weight: 700; display: block;">لا توجد قيود يومية مسجلة</span>
                                <span style="font-size: 13px;">قم بتحرير أول قيد يومية مزدوج لإثبات القيود المحاسبية</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
            <div style="padding: 16px; border-top: 1px solid var(--border-color);">
                {{ $entries->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

