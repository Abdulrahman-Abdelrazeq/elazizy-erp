@extends('layouts.app')

@section('title', 'تسجيل محضر تسوية جرد جديدة')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 900px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-clipboard-check" style="color: var(--primary); margin-left: 8px;"></i>
                تسجيل محضر تسوية جرد جديدة
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تعديل رصيد المستودع بناءً على الجرد الفعلي الفعلي (زيادة، عجز، أو بضاعة هالكة)
            </span>
        </div>

        <a href="{{ route('inventory.adjustments.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة للمحاضر
        </a>
    </div>

    <!-- Adjustment Form Card -->
    <div class="erp-card">
        <form action="{{ route('inventory.adjustments.store') }}" method="POST">
            @csrf

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 18px; margin-bottom: 24px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">المستودع المعني بالجرد *</label>
                    <select name="store_id" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                        @foreach($stores as $st)
                            <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->branch->name ?? 'الفرع' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">نوع التسوية *</label>
                    <select name="type" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 700;">
                        <option value="damage" selected>هالك وتالف (شطب وإتلاف كميات)</option>
                        <option value="deficit">عجز جرد (خصم كميات مفقودة)</option>
                        <option value="addition">زيادة جرد (إضافة كميات زائدة)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">تاريخ الجرد والتسوية *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                </div>
            </div>

            <!-- Items Table Section -->
            <div style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h4 style="font-size: 15px; font-weight: 800; color: var(--primary);">
                        الأصناف المعنية بالتسوية
                    </h4>
                    <button type="button" onclick="addRow()" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 6px 14px; border-radius: 8px; font-size: 12.5px; font-weight: 700; cursor: pointer;">
                        <i class="fa-solid fa-plus"></i> إضافة سطر آخر
                    </button>
                </div>

                <div id="itemsContainer" style="display: flex; flex-direction: column; gap: 12px;">
                    <div class="item-row" style="display: grid; grid-template-columns: 3fr 1.5fr auto; gap: 12px; align-items: center; background: #F8FAFC; padding: 12px; border-radius: 10px; border: 1px solid var(--border-color);">
                        <div>
                            <select name="items[0][item_id]" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px;">
                                <option value="">-- اختر الصنف --</option>
                                @foreach($items as $it)
                                    <option value="{{ $it->id }}">{{ $it->name }} ({{ $it->barcode }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <input type="number" step="1" min="1" name="items[0][quantity]" value="1" placeholder="الكمية المراد تسويتها" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 700;">
                        </div>
                        <div>
                            <button type="button" onclick="removeRow(this)" style="background: none; border: none; color: #DC2626; cursor: pointer; padding: 4px;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">ملاحظات وسبب التسوية</label>
                <textarea name="notes" rows="2" placeholder="أدخل أسباب الهالك أو الفروقات الجردية..." style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <a href="{{ route('inventory.adjustments.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; font-weight: 700; text-decoration: none; color: #475569;">إلغاء</a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 32px; font-size: 15px;">
                    <i class="fa-solid fa-check"></i>
                    تأكيد وتطبيق تسوية الرصيد
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    let rowIndex = 1;
    function addRow() {
        const container = document.getElementById('itemsContainer');
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);

        const select = newRow.querySelector('select');
        select.name = `items[${rowIndex}][item_id]`;
        select.value = '';

        const input = newRow.querySelector('input');
        input.name = `items[${rowIndex}][quantity]`;
        input.value = '1';

        container.appendChild(newRow);
        rowIndex++;
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('.item-row').remove();
        } else {
            alert('يجب أن يحتوي المحضر على صنف واحد على الأقل.');
        }
    }
</script>
@endsection

