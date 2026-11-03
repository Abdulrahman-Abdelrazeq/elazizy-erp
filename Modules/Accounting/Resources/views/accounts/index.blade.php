@extends('layouts.app')

@section('title', 'دليل وشجرة الحسابات (Chart of Accounts)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-sitemap"></i>
                </span>
                شجرة الحسابات المالية (Chart of Accounts)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                الدليل المحاسبي الشجري خماسي المستويات، تصنيف الأصول، الالتزامات، حقوق الملكية، الإيرادات، والمصروفات
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('accounting.journal-entries.create') }}" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-plus-circle"></i>
                إنشاء قيد يومية مزدوج
            </a>
            <a href="{{ route('accounting.ledger.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-book-open" style="color: var(--accent-gold);"></i>
                دفتر الأستاذ العام
            </a>
            <a href="{{ route('accounting.trial_balance.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-scale-balanced" style="color: #4338CA;"></i>
                ميزان المراجعة
            </a>
            <button type="button" onclick="openCreateAccountModal()" class="btn-gold-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">
                <i class="fa-solid fa-folder-plus"></i>
                إضافة حساب جديد
            </button>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 16px; border-top: 4px solid var(--primary);">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الحسابات المالية</span>
            <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 16px; border-top: 4px solid #10B981;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">1. الأصول (Assets)</span>
            <strong style="font-size: 19px; font-weight: 900; color: #0F5132;">{{ $stats['assets'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 16px; border-top: 4px solid #DC2626;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">2. الالتزامات (Liabilities)</span>
            <strong style="font-size: 19px; font-weight: 900; color: #DC2626;">{{ $stats['liabilities'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 16px; border-top: 4px solid #D97706;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">3. حقوق الملكية (Equity)</span>
            <strong style="font-size: 19px; font-weight: 900; color: #D97706;">{{ $stats['equity'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 16px; border-top: 4px solid #4338CA;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">4. الإيرادات (Revenues)</span>
            <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ $stats['revenues'] }}</strong>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 16px; border-top: 4px solid #8B5CF6;">
            <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">5. المصروفات (Expenses)</span>
            <strong style="font-size: 19px; font-weight: 900; color: #8B5CF6;">{{ $stats['expenses'] }}</strong>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="erp-card" style="padding: 16px 20px; margin-bottom: 0;">
        <form method="GET" action="{{ route('accounting.accounts.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 120px 100px; gap: 14px; align-items: flex-end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    بحث برقم الحساب أو الاسم
                </label>
                <input type="text" name="search" value="{{ request('search') }}" class="erp-input" placeholder="مثال: 100000 أو الخزينة...">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">
                    النوع الرئيسي
                </label>
                <select name="type" class="erp-input">
                    <option value="">جميع الحسابات</option>
                    <option value="asset" {{ request('type') === 'asset' ? 'selected' : '' }}>الأصول (Assets)</option>
                    <option value="liability" {{ request('type') === 'liability' ? 'selected' : '' }}>الالتزامات (Liabilities)</option>
                    <option value="equity" {{ request('type') === 'equity' ? 'selected' : '' }}>حقوق الملكية (Equity)</option>
                    <option value="revenue" {{ request('type') === 'revenue' ? 'selected' : '' }}>الإيرادات (Revenues)</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>المصروفات (Expenses)</option>
                </select>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>

            <a href="{{ route('accounting.accounts.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); color: var(--text-muted); border-radius: 8px; font-size: 13px; font-weight: 700; height: 42px; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                إعادة ضبط
            </a>
        </form>
    </div>

    <!-- Accounts Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">رقم الحساب (Code)</th>
                        <th>اسم الحساب المالي</th>
                        <th>التصنيف والنوع</th>
                        <th>طبيعة الحساب</th>
                        <th>المستوى</th>
                        <th>الرصيد اللحظي الحالي</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 150px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $acc)
                        <tr style="{{ $acc->is_parent ? 'background: #FAFBFB; font-weight: 800;' : '' }}">
                            <td>
                                <span style="font-family: monospace; font-weight: 900; font-size: 13.5px; color: var(--primary);">
                                    {{ $acc->code }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px; padding-right: {{ ($acc->level - 1) * 16 }}px;">
                                    @if($acc->is_parent)
                                        <i class="fa-solid fa-folder" style="color: var(--accent-gold); font-size: 14px;"></i>
                                        <strong style="color: var(--text-main); font-size: 14px;">{{ $acc->name }}</strong>
                                    @else
                                        <i class="fa-regular fa-file-lines" style="color: var(--text-muted); font-size: 13px;"></i>
                                        <span style="color: var(--text-main); font-size: 13.5px;">{{ $acc->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge" style="background: #F1F5F9; color: var(--text-main); font-weight: 700;">
                                    {{ $acc->type_label }}
                                </span>
                            </td>
                            <td>
                                @if($acc->nature === 'debit')
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">مدين (Debit)</span>
                                @else
                                    <span class="badge" style="background: #E0E7FF; color: #4338CA; font-weight: 700;">دائن (Credit)</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background: #F8FAFC; border: 1px solid var(--border-color); color: var(--text-muted); font-size: 11px;">
                                    مستوى {{ $acc->level }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 14px; font-weight: 900; color: {{ $acc->current_balance < 0 ? '#DC2626' : 'var(--text-main)' }};">
                                    {{ formatMoney($acc->current_balance) }}
                                </strong>
                            </td>
                            <td>
                                @if($acc->is_active)
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 700;">نشط</span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">معطل</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    @if($acc->is_parent)
                                        <button type="button" 
                                                onclick="openAddChildAccountModal({{ $acc->id }}, '{{ $acc->code }}', '{{ $acc->name }}')"
                                                title="إضافة حساب فرعي تحته"
                                                style="width: 30px; height: 30px; border-radius: 6px; background: var(--primary-light); border: 1px solid var(--primary); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('accounting.ledger.index') }}?account_id={{ $acc->id }}" title="كشف حساب الأستاذ" style="width: 30px; height: 30px; border-radius: 6px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                                            <i class="fa-solid fa-book-open"></i>
                                        </a>
                                    @endif

                                    <button type="button" 
                                            onclick="openEditAccountModal({{ json_encode($acc) }})"
                                            title="تعديل الحساب"
                                            style="width: 30px; height: 30px; border-radius: 6px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--accent-gold); display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('accounting.accounts.destroy', $acc->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف هذا الحساب؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="حذف"
                                                style="width: 30px; height: 30px; border-radius: 6px; background: #F8FAFC; border: 1px solid var(--border-color); color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                                <i class="fa-solid fa-sitemap" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                <span style="font-size: 15px; font-weight: 700; display: block;">لا توجد حسابات مالية مطابقة</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($accounts->hasPages())
            <div style="padding: 16px; border-top: 1px solid var(--border-color);">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Create Account -->
<div id="createAccountModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 600px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-folder-plus" style="color: var(--accent-gold);"></i>
                إضافة حساب مالي جديد إلى شجرة الحسابات
            </h4>
            <button type="button" onclick="closeCreateAccountModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('accounting.accounts.store') }}" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    الحساب الأب (المتفرع منه)
                </label>
                <select name="parent_id" id="account_parent_select" class="erp-input" onchange="fetchNextCode(this.value)">
                    <option value="">-- حساب رئيسي مستقل (مستوى 1) --</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 140px 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        رقم الحساب (Code) <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="code" id="account_code_input" class="erp-input" style="font-family: monospace; font-weight: 800;" placeholder="110001" required>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم الحساب بالعربية <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" class="erp-input" placeholder="مثال: البنك الأهلي - حساب جاري" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        النوع الرئيسي
                    </label>
                    <select name="type" id="account_type_select" class="erp-input">
                        <option value="asset">الأصول (Assets)</option>
                        <option value="liability">الالتزامات (Liabilities)</option>
                        <option value="equity">حقوق الملكية (Equity)</option>
                        <option value="revenue">الإيرادات (Revenues)</option>
                        <option value="expense">المصروفات (Expenses)</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        طبيعة الحساب المالي
                    </label>
                    <select name="nature" id="account_nature_select" class="erp-input">
                        <option value="debit">مدين (Debit)</option>
                        <option value="credit">دائن (Credit)</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        الرصيد الافتتاحي
                    </label>
                    <input type="number" step="0.01" name="opening_balance" value="0.00" class="erp-input" style="font-weight: 800;">
                </div>
                <div style="display: flex; align-items: center; gap: 8px; margin-top: 22px;">
                    <input type="checkbox" name="is_parent" id="is_parent_check" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                    <label for="is_parent_check" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                        حساب رئيسي (تتفرع منه حسابات فرعية)
                    </label>
                </div>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    ملاحظات وبيان الحساب
                </label>
                <textarea name="notes" rows="2" class="erp-input" placeholder="شرح لطبيعة الحساب والمعاملات التي تقيد عليه..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeCreateAccountModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-weight: 800;">
                    <i class="fa-solid fa-check-circle"></i>
                    حفظ الحساب
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Account -->
<div id="editAccountModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--accent-gold);"></i>
                تعديل بيانات الحساب المالي
            </h4>
            <button type="button" onclick="closeEditAccountModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form id="editAccountForm" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    اسم الحساب بالعربية <span style="color: #DC2626;">*</span>
                </label>
                <input type="text" name="name" id="edit_acc_name" class="erp-input" required>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    الاسم بالإنجليزية (اختياري)
                </label>
                <input type="text" name="name_en" id="edit_acc_name_en" class="erp-input">
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" id="edit_acc_is_active" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                <label for="edit_acc_is_active" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    الحساب مفعل ونشط
                </label>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    ملاحظات
                </label>
                <textarea name="notes" id="edit_acc_notes" rows="2" class="erp-input"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeEditAccountModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
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
    function openCreateAccountModal() {
        document.getElementById('createAccountModal').style.display = 'flex';
    }
    function closeCreateAccountModal() {
        document.getElementById('createAccountModal').style.display = 'none';
    }

    function openAddChildAccountModal(parentId, parentCode, parentName) {
        document.getElementById('account_parent_select').value = parentId;
        fetchNextCode(parentId);
        openCreateAccountModal();
    }

    function fetchNextCode(parentId) {
        if (!parentId) return;
        fetch("{{ route('accounting.accounts.next_code') }}?parent_id=" + parentId)
            .then(res => res.json())
            .then(data => {
                if (data.code) {
                    document.getElementById('account_code_input').value = data.code;
                }
                if (data.type) {
                    document.getElementById('account_type_select').value = data.type;
                }
                if (data.nature) {
                    document.getElementById('account_nature_select').value = data.nature;
                }
            });
    }

    function openEditAccountModal(acc) {
        const form = document.getElementById('editAccountForm');
        form.action = "{{ url('accounting/accounts') }}/" + acc.id;
        document.getElementById('edit_acc_name').value = acc.name;
        document.getElementById('edit_acc_name_en').value = acc.name_en || '';
        document.getElementById('edit_acc_notes').value = acc.notes || '';
        document.getElementById('edit_acc_is_active').checked = acc.is_active ? true : false;
        document.getElementById('editAccountModal').style.display = 'flex';
    }
    function closeEditAccountModal() {
        document.getElementById('editAccountModal').style.display = 'none';
    }
</script>
@endsection

