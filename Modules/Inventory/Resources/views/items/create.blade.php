@extends('layouts.app')

@section('title', 'إضافة صنف جديد')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px; max-width: 1000px; margin: 0 auto;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-plus-circle" style="color: var(--primary); margin-left: 8px;"></i>
                إضافة صنف / منتج جديد إلى الدليل
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                أدخل مواصفات الصنف، الأسعار المتعددة، ورصيد بداية المدة
            </span>
        </div>

        <a href="{{ route('inventory.items.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-arrow-right"></i>
            العودة لقائمة الأصناف
        </a>
    </div>

    <!-- Create Item Form Card -->
    <div class="erp-card">
        <form action="{{ route('inventory.items.store') }}" method="POST">
            @csrf

            <!-- Section 1: Basic Information -->
            <div style="margin-bottom: 28px;">
                <h4 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-tag"></i>
                    البيانات الأساسية للصنف
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
                    <div style="grid-column: span 2;">
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اسم الصنف / المنتج *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="مثال: حليب المراعي كامل الدسم 1 لتر" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">
                            الباركود (Barcode)
                            <a href="javascript:void(0)" onclick="generateBarcode()" style="font-size: 11px; color: var(--primary); margin-right: 6px; text-decoration: underline;">توليد عشوائي</a>
                        </label>
                        <input type="text" id="barcode_input" name="barcode" value="{{ old('barcode') }}" placeholder="اتركه فارغاً للتوليد التلقائي" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">كود الصنف (SKU)</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" placeholder="SKU-XXXX" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">القسم / التصنيف</label>
                        <select name="category_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                            <option value="">-- بدون قسم (عام) --</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">الماركة التجارية (Brand)</label>
                        <select name="brand_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                            <option value="">-- بدون ماركة --</option>
                            @foreach($brands as $b)
                                <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">وحدة القياس الأساسية *</label>
                        <select name="unit_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">الضريبة المطبقة (VAT)</label>
                        <select name="tax_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                            <option value="">-- بدون ضريبة --</option>
                            @foreach($taxes as $t)
                                <option value="{{ $t->id }}" {{ (old('tax_id') == $t->id || $t->is_default) ? 'selected' : '' }}>{{ $t->name }} ({{ $t->rate }}%)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 24px 0;">

            <!-- Section 2: Pricing Matrix -->
            <div style="margin-bottom: 28px;">
                <h4 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-coins"></i>
                    شجرة الأسعار المتعددة وتكلفة الشراء
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                    <div style="background: #FEF3C7; padding: 14px; border-radius: 12px; border: 1px solid #FDE68A;">
                        <label style="display: block; font-weight: 700; font-size: 13px; color: #92400E; margin-bottom: 6px;">سعر الشراء والتكلفة *</label>
                        <input type="number" step="0.01" name="buy_price" value="{{ old('buy_price', '0.00') }}" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1.5px solid #FCD34D; font-size: 15px; font-weight: 800;">
                    </div>

                    <div style="background: var(--primary-light); padding: 14px; border-radius: 12px; border: 1px solid #A7F3D0;">
                        <label style="display: block; font-weight: 700; font-size: 13px; color: var(--primary); margin-bottom: 6px;">سعر البيع قطاعي (تجزئة) *</label>
                        <input type="number" step="0.01" name="sell_price" value="{{ old('sell_price', '0.00') }}" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1.5px solid var(--primary); font-size: 15px; font-weight: 800; color: var(--primary);">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">سعر البيع جملة</label>
                        <input type="number" step="0.01" name="wholesale_price" value="{{ old('wholesale_price', '0.00') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">سعر البيع نصف جملة</label>
                        <input type="number" step="0.01" name="half_wholesale_price" value="{{ old('half_wholesale_price', '0.00') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">أدنى سعر بيع (للكاشير)</label>
                        <input type="number" step="0.01" name="minimum_sell_price" value="{{ old('minimum_sell_price', '0.00') }}" style="width: 100%; padding: 10px; border-radius: 8px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 24px 0;">

            <!-- Section 3: Initial Stock & Inventory Settings -->
            <div style="margin-bottom: 28px;">
                <h4 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-warehouse"></i>
                    رصيد بداية المدة وإعدادات التخزين
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 18px;">
                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">إيداع رصيد بداية المدة في مستودع</label>
                        <select name="initial_store_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                            <option value="">-- بدون رصيد افتتاحي حالياً --</option>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" {{ $st->is_main ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">كمية الرصيد الافتتاحي</label>
                        <input type="number" step="1" name="initial_quantity" value="{{ old('initial_quantity', '0') }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">حد الطلب (تنبيه نقص المخزون)</label>
                        <input type="number" step="1" name="min_quantity_alert" value="{{ old('min_quantity_alert', '5') }}" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>

                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="is_service" value="1" {{ old('is_service') ? 'checked' : '' }} style="accent-color: var(--primary); width: 18px; height: 18px;">
                        <span>صنف خدمي (لا يتطلب جرد أو خصم مخزون)</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="has_expiration" id="exp_check" value="1" onchange="toggleExp(this.checked)" {{ old('has_expiration') ? 'checked' : '' }} style="accent-color: var(--primary); width: 18px; height: 18px;">
                        <span>تتبع تاريخ الصلاحية</span>
                    </label>

                    <div id="exp_date_box" style="display: none;">
                        <input type="date" name="expire_date" style="padding: 8px 12px; border-radius: 8px; border: 1.5px solid var(--border-color); font-size: 13px;">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="{{ route('inventory.items.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 12px 24px; border-radius: 10px; font-weight: 700; text-decoration: none; color: #475569;">إلغاء</a>
                <button type="submit" class="btn-primary-custom" style="padding: 12px 32px; font-size: 15px;">
                    <i class="fa-solid fa-check"></i>
                    حفظ وإضافة الصنف
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function generateBarcode() {
        const rand = Math.floor(100000000000 + Math.random() * 900000000000);
        document.getElementById('barcode_input').value = rand;
    }
    function toggleExp(checked) {
        document.getElementById('exp_date_box').style.display = checked ? 'block' : 'none';
    }
</script>
@endsection

