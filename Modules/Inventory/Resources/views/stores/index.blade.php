@extends('layouts.app')

@section('title', 'دليل المستودعات والمخازن')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-warehouse" style="color: var(--primary); margin-left: 8px;"></i>
                دليل المستودعات وإدارة المخازن
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدارة مستودعات الفروع المركزية والفرعية ومتابعة الأرصدة الفعلية
            </span>
        </div>

        <button type="button" onclick="document.getElementById('storeModal').style.display='flex'" class="btn-primary-custom">
            <i class="fa-solid fa-plus"></i>
            إضافة مستودع جديد
        </button>
    </div>

    <!-- Stores Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
        @forelse($stores as $st)
            <div class="erp-card" style="margin-bottom: 0; position: relative; border-right: 4px solid var(--primary);">
                @if($st->is_main)
                    <span style="position: absolute; top: 16px; left: 16px; background: var(--primary-light); color: var(--primary); font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 20px;">
                        المستودع الرئيسي
                    </span>
                @endif

                <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 16px;">
                    <div style="width: 50px; height: 50px; border-radius: 12px; background: #F8FAFC; border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--primary);">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin-bottom: 2px;">{{ $st->name }}</h3>
                        <span style="font-size: 12px; color: var(--text-muted); font-family: monospace;">كود: {{ $st->code }}</span>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); padding: 12px 0; margin-bottom: 16px; display: flex; flex-direction: column; gap: 6px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">الفرع التابع له:</span>
                        <strong style="color: var(--text-main);">{{ $st->branch->name ?? 'المركز الرئيسي' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">أمين المستودع:</span>
                        <strong style="color: var(--text-main);">{{ $st->keeper_name ?? 'غير محدد' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">الأصناف المتوفرة:</span>
                        <strong style="color: var(--primary); font-size: 14px;">{{ $st->totalItemsCount() }} صنف</strong>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('inventory.stores.show', $st->id) }}" class="btn-primary-custom" style="padding: 8px 16px; font-size: 12.5px;">
                        <i class="fa-solid fa-boxes-stacked"></i>
                        عرض جرد وأرصدة المستودع
                    </a>
                    <span style="font-size: 12px; color: var(--text-muted);">{{ $st->phone }}</span>
                </div>
            </div>
        @empty
            <div class="erp-card" style="grid-column: 1 / -1; text-align: center; padding: 40px; color: var(--text-muted);">
                لا توجد مستودعات مسجلة حالياً.
            </div>
        @endforelse
    </div>

</div>

<!-- Create Store Modal -->
<div id="storeModal" class="theme-modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 800; font-size: 18px; color: var(--text-main);">إضافة مستودع جديد</h3>
            <button type="button" onclick="document.getElementById('storeModal').style.display='none'" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ route('inventory.stores.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">اسم المستودع / المخزن *</label>
                <input type="text" name="name" required placeholder="مثال: مستودع جدة المركزي" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">كود المستودع</label>
                    <input type="text" name="code" placeholder="STR-02" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">الفرع التابع له</label>
                    <select name="branch_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13px;">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">أمين المستودع (المسؤول)</label>
                    <input type="text" name="keeper_name" placeholder="اسم الموظف المسئول" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">رقم هاتف المستودع</label>
                    <input type="text" name="phone" placeholder="0500000000" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">العنوان والموقع</label>
                <input type="text" name="address" placeholder="المنطقة الصناعية - الرياض" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
                <input type="checkbox" name="is_main" id="store_main" value="1" style="accent-color: var(--primary); width: 16px; height: 16px;">
                <label for="store_main" style="font-weight: 700; font-size: 13px; cursor: pointer;">تعيين كمستودع رئيسي افتراضي للمبيعات والمشتريات</label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('storeModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">إلغاء</button>
                <button type="submit" class="btn-primary-custom">إضافة المستودع</button>
            </div>
        </form>
    </div>
</div>
@endsection

