@extends('layouts.app')

@section('title', $type === 'receipt' ? 'إنشاء سند قبض جديد' : 'إنشاء سند صرف جديد')

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
                <span style="font-size: 14px; font-weight: 800; color: {{ $type === 'receipt' ? 'var(--primary)' : '#DC2626' }};">
                    {{ $type === 'receipt' ? 'سند قبض جديد' : 'سند صرف جديد' }}
                </span>
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: {{ $type === 'receipt' ? 'rgba(15, 81, 50, 0.1)' : 'rgba(220, 38, 38, 0.1)' }}; color: {{ $type === 'receipt' ? 'var(--primary)' : '#DC2626' }}; align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid {{ $type === 'receipt' ? 'fa-circle-arrow-down' : 'fa-circle-arrow-up' }}"></i>
                </span>
                {{ $type === 'receipt' ? 'تحرير سند قبض / تحصيل نقدي أو بنكي' : 'تحرير سند صرف / دفع نقدي أو بنكي' }}
            </h2>
        </div>

        <div style="display: flex; gap: 10px;">
            <a href="{{ route('treasury.vouchers.create') }}?type={{ $type === 'receipt' ? 'payment' : 'receipt' }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-repeat" style="color: var(--accent-gold);"></i>
                التحويل إلى {{ $type === 'receipt' ? 'سند صرف' : 'سند قبض' }}
            </a>
        </div>
    </div>

    <!-- Main Voucher Form Card -->
    <div class="erp-card" style="padding: 28px; margin-bottom: 0; border-top: 4px solid {{ $type === 'receipt' ? 'var(--primary)' : '#DC2626' }};">
        <form action="{{ route('treasury.vouchers.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px;">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            <!-- Header Info Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; padding-bottom: 20px; border-bottom: 1px dashed var(--border-color);">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        رقم السند (تلقائي)
                    </label>
                    <input type="text" value="{{ $voucherNumber }}" class="erp-input" readonly style="background: #F8FAFC; font-family: monospace; font-weight: 800; font-size: 15px; color: {{ $type === 'receipt' ? 'var(--primary)' : '#DC2626' }};">
                </div>

                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        تاريخ المعاملة <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" class="erp-input" required>
                </div>

                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        {{ $type === 'receipt' ? 'الخزينة المودع بها (إيداع)' : 'الخزينة المسحوب منها (صرف)' }} <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="treasury_id" class="erp-input" required id="treasurySelect">
                        @foreach($treasuries as $tr)
                            <option value="{{ $tr->id }}" {{ $tr->is_default ? 'selected' : '' }} data-balance="{{ $tr->current_balance }}">
                                {{ $tr->name }} (الرصيد المتاح: {{ formatMoney($tr->current_balance) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Financial Amount & Payment Method -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; background: {{ $type === 'receipt' ? 'rgba(15, 81, 50, 0.03)' : 'rgba(220, 38, 38, 0.03)' }}; padding: 18px; border-radius: 12px; border: 1px solid {{ $type === 'receipt' ? 'rgba(15, 81, 50, 0.15)' : 'rgba(220, 38, 38, 0.15)' }};">
                <div>
                    <label style="font-size: 13px; font-weight: 800; color: var(--text-main); display: block; margin-bottom: 6px;">
                        مبلغ السند <span style="color: #DC2626;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="erp-input" style="font-size: 20px; font-weight: 900; color: {{ $type === 'receipt' ? '#0F5132' : '#DC2626' }}; padding-left: 48px;" placeholder="0.00" required>
                        <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 800; font-size: 13px; color: var(--text-muted);">ج.م</span>
                    </div>
                </div>

                <div>
                    <label style="font-size: 13px; font-weight: 800; color: var(--text-main); display: block; margin-bottom: 6px;">
                        طريقة السداد / التحصيل <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="payment_method" class="erp-input" required>
                        <option value="cash">نقداً (كاش الصندوق)</option>
                        <option value="bank_transfer">تحويل بنكي جاري</option>
                        <option value="check">شيك بنكي مقاصة</option>
                        <option value="online">محفظة إلكترونية / إنستاباي</option>
                        <option value="card">بطاقة بنكية / POS</option>
                    </select>
                </div>

                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        رقم الشيك أو الحوالة المرجعية (اختياري)
                    </label>
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="erp-input" placeholder="رقم الشيك أو إشعار التحويل">
                </div>
            </div>

            <!-- Party & Category Details -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px;">
                @if($type === 'receipt')
                    <div>
                        <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                            العميل المرتبط (تحصيل مديونية)
                        </label>
                        <select name="client_id" class="erp-input">
                            <option value="">-- اختياري: حدد العميل إن كان تحصيلاً --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} (مديونية: {{ formatMoney($c->balance) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div>
                        <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                            المورد المرتبط (سداد مستحقات)
                        </label>
                        <select name="client_id" class="erp-input">
                            <option value="">-- اختياري: حدد المورد إن كان سداداً --</option>
                            @foreach($suppliers as $s)
                                <option value="{{ $s->id }}" {{ old('client_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->name }} (مستحقات له: {{ formatMoney($s->balance) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        {{ $type === 'receipt' ? 'اسم المقبوض منه (كتابة حرة)' : 'اسم المصروف له / المستلم (كتابة حرة)' }}
                    </label>
                    <input type="text" name="received_from_or_paid_to" value="{{ old('received_from_or_paid_to') }}" class="erp-input" placeholder="اسم الشخص أو الجهة...">
                </div>

                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        بند التصنيف المالي
                    </label>
                    <select name="category_id" class="erp-input">
                        <option value="">-- اختياري: اختر البند --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Notes & Statement Description -->
            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    البيان / تفاصيل وسبب السند <span style="color: #DC2626;">*</span>
                </label>
                <textarea name="notes" rows="3" class="erp-input" placeholder="وصف كامل ومفصل لسبب الصرف أو التحصيل والعملية المترتبة عليها..." required>{{ old('notes') }}</textarea>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 10px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                <a href="{{ route('treasury.vouchers.index') }}" style="padding: 10px 20px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; text-decoration: none;">
                    إلغاء والعودة
                </a>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 32px; font-weight: 800; font-size: 14px; background: {{ $type === 'receipt' ? 'var(--primary)' : '#DC2626' }}; border: none;">
                    <i class="fa-solid fa-check-circle"></i>
                    تأكيد واعتِماد السند المالي
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
