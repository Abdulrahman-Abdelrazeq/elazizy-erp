@extends('layouts.app')

@section('title', 'تحويل مالي بين الخزائن والبنوك')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 800px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <a href="{{ route('treasury.transfers.index') }}" style="color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 700;">
                    <i class="fa-solid fa-arrow-right"></i> التحويلات المالية
                </a>
                <span style="color: var(--border-color);">/</span>
                <span style="font-size: 14px; font-weight: 800; color: var(--primary);">إجراء تحويل جديد</span>
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-right-left"></i>
                </span>
                تحويل نقدية بين الخزائن والحسابات البنكية
            </h2>
        </div>
    </div>

    <!-- Form Card -->
    <div class="erp-card" style="padding: 28px; margin-bottom: 0; border-top: 4px solid var(--primary);">
        <form action="{{ route('treasury.transfers.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf

            <!-- Transfer Number & Date -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        رقم التحويل (تلقائي)
                    </label>
                    <input type="text" value="{{ $transferNumber }}" class="erp-input" readonly style="background: #F8FAFC; font-family: monospace; font-weight: 800; color: var(--primary);">
                </div>

                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        تاريخ التحويل <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" class="erp-input" required>
                </div>
            </div>

            <!-- From & To Treasuries -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px; background: #F8FAFC; padding: 18px; border-radius: 12px; border: 1px solid var(--border-color);">
                <div>
                    <label style="font-size: 13px; font-weight: 800; color: #DC2626; display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                        <i class="fa-solid fa-arrow-up"></i>
                        من خزينة / بنك (المصدر) <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="from_treasury_id" class="erp-input" required id="fromTreasurySelect">
                        <option value="">-- اختر الخزينة المصدرة --</option>
                        @foreach($treasuries as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->name }} (الرصيد: {{ formatMoney($t->current_balance) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 800; color: #0F5132; display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                        <i class="fa-solid fa-arrow-down"></i>
                        إلى خزينة / بنك (المستلم) <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="to_treasury_id" class="erp-input" required id="toTreasurySelect">
                        <option value="">-- اختر الخزينة المستلمة --</option>
                        @foreach($treasuries as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->name }} (الرصيد: {{ formatMoney($t->current_balance) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Amount and Fees -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 18px;">
                <div>
                    <label style="font-size: 13px; font-weight: 800; color: var(--text-main); display: block; margin-bottom: 6px;">
                        المبلغ المراد تحويله <span style="color: #DC2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="erp-input" style="font-size: 18px; font-weight: 900; color: var(--primary); padding-left: 45px;" placeholder="0.00" required>
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; font-size: 12px; color: var(--text-muted);">ج.م</span>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        عمولة أو مصاريف التحويل البنكي (إن وجدت)
                    </label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0" name="transfer_fee" value="{{ old('transfer_fee', '0.00') }}" class="erp-input" style="font-size: 16px; font-weight: 700; padding-left: 45px;">
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; font-size: 12px; color: var(--text-muted);">ج.م</span>
                    </div>
                    <small style="font-size: 11px; color: var(--text-muted); margin-top: 4px; display: block;">تخصم العمولة من رصيد الخزينة المصدرة فقط</small>
                </div>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    سبب التحويل / ملاحظات
                </label>
                <textarea name="notes" rows="3" class="erp-input" placeholder="سبب نقل السيولة أو تغذية الخزينة..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                <a href="{{ route('treasury.transfers.index') }}" style="padding: 10px 20px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; text-decoration: none;">
                    إلغاء
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 32px; font-weight: 800; font-size: 14px;">
                    <i class="fa-solid fa-check-circle"></i>
                    تنفيذ وتأكيد التحويل المالي
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

