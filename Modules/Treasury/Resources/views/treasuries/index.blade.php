@extends('layouts.app')

@section('title', 'الخزائن والحسابات البنكية')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-vault"></i>
                </span>
                الخزائن والحسابات البنكية (Treasury & Bank Accounts)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدارة صناديق النقدية بالفرع، الحسابات البنكية الجارية، والمحافظ الإلكترونية، ومتابعة السيولة اللحظية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('treasury.vouchers.create') }}?type=receipt" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-circle-arrow-down"></i>
                سند قبض جديد
            </a>
            <a href="{{ route('treasury.vouchers.create') }}?type=payment" style="background: #DC2626; color: #fff; padding: 9px 16px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);">
                <i class="fa-solid fa-circle-arrow-up"></i>
                سند صرف جديد
            </a>
            <a href="{{ route('treasury.transfers.create') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-right-left" style="color: var(--accent-gold);"></i>
                تحويل بين الخزائن
            </a>
            <button type="button" onclick="openCreateTreasuryModal()" class="btn-gold-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">
                <i class="fa-solid fa-plus-circle"></i>
                تعريف خزينة / بنك
            </button>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid var(--primary); background: rgba(15, 81, 50, 0.02);">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-sack-dollar"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي السيولة المالية الحالية</span>
                <strong style="font-size: 22px; font-weight: 900; color: var(--primary);">{{ formatMoney($stats['total_balance']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid #10B981;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-money-bill-wave"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">نقدية الخزائن (كاش الصناديق)</span>
                <strong style="font-size: 19px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['cash_balance']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid #4338CA;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-building-columns"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أرصدة الحسابات البنكية</span>
                <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ formatMoney($stats['bank_balance']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px; border-top: 4px solid #D97706;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">المحافظ والمدفوعات الإلكترونية</span>
                <strong style="font-size: 19px; font-weight: 900; color: #D97706;">{{ formatMoney($stats['wallet_balance']) }}</strong>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form method="GET" action="{{ route('treasury.treasuries.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px 100px; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    بحث بالاسم أو الحساب البنكي
                </label>
                <input type="text" name="search" value="{{ request('search') }}" class="erp-input" placeholder="اسم الخزينة، رقم الحساب...">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    نوع الحساب
                </label>
                <select name="type" class="erp-input">
                    <option value="">جميع الأنواع</option>
                    <option value="cash" {{ request('type') === 'cash' ? 'selected' : '' }}>خزينة نقدية (كاش)</option>
                    <option value="bank" {{ request('type') === 'bank' ? 'selected' : '' }}>حساب بنكي</option>
                    <option value="wallet" {{ request('type') === 'wallet' ? 'selected' : '' }}>محفظة إلكترونية</option>
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i>
                تصفية
            </button>

            <a href="{{ route('treasury.treasuries.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 8px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                إعادة ضبط
            </a>
        </form>
    </div>

    <!-- Treasuries Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>اسم الخزينة / الحساب البنكي</th>
                        <th>النوع</th>
                        <th>الفرع التابع</th>
                        <th>بيانات الحساب / البنك</th>
                        <th>الرصيد الافتتاحي</th>
                        <th>الرصيد الحالي المتوفر</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 150px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($treasuries as $t)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <strong style="font-size: 14.5px; color: var(--text-main);">{{ $t->name }}</strong>
                                    @if($t->is_default)
                                        <span class="badge" style="background: #FEF3C7; color: #D97706; font-size: 10px; font-weight: 800;">افتراضية</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($t->type === 'cash')
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">
                                        <i class="fa-solid fa-money-bill"></i> خزينة كاش
                                    </span>
                                @elseif($t->type === 'bank')
                                    <span class="badge" style="background: #E0E7FF; color: #4338CA; font-weight: 700;">
                                        <i class="fa-solid fa-building-columns"></i> حساب بنكي
                                    </span>
                                @else
                                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700;">
                                        <i class="fa-solid fa-mobile-screen"></i> محفظة
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--text-muted);">
                                    {{ $t->branch->name ?? 'المركز الرئيسي' }}
                                </span>
                            </td>
                            <td>
                                @if($t->bank_name || $t->account_number)
                                    <div style="font-size: 12px; line-height: 1.4;">
                                        @if($t->bank_name)
                                            <strong style="color: var(--text-main);">{{ $t->bank_name }}</strong>
                                        @endif
                                        @if($t->account_number)
                                            <span style="font-family: monospace; color: var(--text-muted); display: block;">{{ $t->account_number }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span style="font-size: 12px; color: var(--text-muted);">—</span>
                                @endif
                            </td>
                            <td style="font-weight: 700; color: #64748B;">{{ formatMoney($t->opening_balance, $t->currency) }}</td>
                            <td>
                                <strong style="font-size: 16px; font-weight: 900; color: {{ $t->current_balance < 0 ? '#DC2626' : 'var(--primary)' }};">
                                    {{ formatMoney($t->current_balance, $t->currency) }}
                                </strong>
                            </td>
                            <td>
                                @if($t->is_active)
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 700;">نشطة</span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">معطلة</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <a href="{{ route('treasury.treasuries.statement', $t->id) }}" title="كشف حساب الخزينة" style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </a>
                                    <button type="button" 
                                            onclick="openEditTreasuryModal({{ json_encode($t) }})"
                                            title="تعديل الخزينة"
                                            style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--accent-gold); display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('treasury.treasuries.destroy', $t->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذه الخزينة؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="حذف"
                                                style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                                <i class="fa-solid fa-vault" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                <span style="font-size: 15px; font-weight: 700; display: block;">لا توجد خزائن أو حسابات بنكية مسجلة</span>
                                <span style="font-size: 13px;">قم بإضافة أول خزينة نقدية أو حساب بنكي لبدء العمليات المالية</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($treasuries->hasPages())
            <div style="padding: 16px; border-top: 1px solid var(--border-color);">
                {{ $treasuries->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Create Treasury -->
<div id="createTreasuryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 600px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-vault" style="color: var(--accent-gold);"></i>
                تعريف خزينة نقدية أو حساب بنكي جديد
            </h4>
            <button type="button" onclick="closeCreateTreasuryModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('treasury.treasuries.store') }}" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم الخزينة / الحساب <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" class="erp-input" placeholder="مثال: الخزينة الرئيسية، البنك الأهلي" required>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        النوع <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="type" class="erp-input" required>
                        <option value="cash">خزينة نقدية (كاش الصندوق)</option>
                        <option value="bank">حساب بنكي جاري</option>
                        <option value="wallet">محفظة إلكترونية (إنستاباي / فودافون كاش)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم البنك (إن وجد)
                    </label>
                    <input type="text" name="bank_name" class="erp-input" placeholder="مثال: البنك التجاري الدولي CIB">
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        رقم الحساب / المحفظة
                    </label>
                    <input type="text" name="account_number" class="erp-input" placeholder="رقم الحساب أو رقم هاتف المحفظة">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        الرصيد الافتتاحي <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="number" step="0.01" min="0" name="opening_balance" value="0.00" class="erp-input" style="font-weight: 800;" required>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        العملة <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="currency" value="EGP" class="erp-input" required>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_default" id="is_default_check" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                <label for="is_default_check" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    تعيين كخزينة افتراضية لمعاملات النظام والسندات
                </label>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    ملاحظات وبيانات إضافية
                </label>
                <textarea name="notes" rows="2" class="erp-input" placeholder="أي تفاصيل أو فرع البنك..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeCreateTreasuryModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-weight: 800;">
                    <i class="fa-solid fa-check-circle"></i>
                    حفظ وتعريف الخزينة
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Treasury -->
<div id="editTreasuryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 600px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--accent-gold);"></i>
                تعديل بيانات الخزينة / الحساب البنكي
            </h4>
            <button type="button" onclick="closeEditTreasuryModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form id="editTreasuryForm" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم الخزينة / الحساب <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" id="edit_name" class="erp-input" required>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        النوع <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="type" id="edit_type" class="erp-input" required>
                        <option value="cash">خزينة نقدية (كاش الصندوق)</option>
                        <option value="bank">حساب بنكي جاري</option>
                        <option value="wallet">محفظة إلكترونية (إنستاباي / فودافون كاش)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم البنك (إن وجد)
                    </label>
                    <input type="text" name="bank_name" id="edit_bank_name" class="erp-input">
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        رقم الحساب / المحفظة
                    </label>
                    <input type="text" name="account_number" id="edit_account_number" class="erp-input">
                </div>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    العملة <span style="color: #DC2626;">*</span>
                </label>
                <input type="text" name="currency" id="edit_currency" class="erp-input" required>
            </div>

            <div style="display: flex; gap: 20px; align-items: center;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <label for="edit_is_active" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                        خزينة نشطة وتعمل
                    </label>
                </div>

                <div style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="is_default" id="edit_is_default" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <label for="edit_is_default" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                        خزينة افتراضية
                    </label>
                </div>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    ملاحظات
                </label>
                <textarea name="notes" id="edit_notes" rows="2" class="erp-input"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeEditTreasuryModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-weight: 800;">
                    <i class="fa-solid fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateTreasuryModal() {
        document.getElementById('createTreasuryModal').style.display = 'flex';
    }
    function closeCreateTreasuryModal() {
        document.getElementById('createTreasuryModal').style.display = 'none';
    }

    function openEditTreasuryModal(t) {
        const form = document.getElementById('editTreasuryForm');
        form.action = "{{ url('treasury/accounts') }}/" + t.id;
        document.getElementById('edit_name').value = t.name;
        document.getElementById('edit_type').value = t.type;
        document.getElementById('edit_bank_name').value = t.bank_name || '';
        document.getElementById('edit_account_number').value = t.account_number || '';
        document.getElementById('edit_currency').value = t.currency || 'EGP';
        document.getElementById('edit_notes').value = t.notes || '';
        document.getElementById('edit_is_active').checked = t.is_active ? true : false;
        document.getElementById('edit_is_default').checked = t.is_default ? true : false;
        document.getElementById('editTreasuryModal').style.display = 'flex';
    }
    function closeEditTreasuryModal() {
        document.getElementById('editTreasuryModal').style.display = 'none';
    }
</script>
@endsection
