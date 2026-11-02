@extends('layouts.app')

@section('title', 'إعدادات طباعة ملصقات الباركود')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Breadcrumb & Actions Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-barcode" style="color: var(--primary); margin-left: 8px;"></i>
                إعدادات وتخصيص ملصقات الباركود (Barcode Labels)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد أبعاد الملصق الحراري والمعلومات المطبوعة على المنتج
            </span>
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('settings.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة للإعدادات
            </a>
        </div>
    </div>

    <!-- Barcode Config & Preview Card -->
    <div class="erp-card">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; align-items: start;">
            <!-- Settings Form -->
            <form action="{{ route('settings.barcode.update') }}" method="POST">
                @csrf

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اسم القالب / التكوين</label>
                    <input type="text" name="name" value="{{ $setting->name }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">عرض الملصق (مم)</label>
                        <input type="number" id="label_w" name="label_width" value="{{ $setting->label_width }}" oninput="updateLivePreview()" required min="20" max="100" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">ارتفاع الملصق (مم)</label>
                        <input type="number" id="label_h" name="label_height" value="{{ $setting->label_height }}" oninput="updateLivePreview()" required min="15" max="100" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">نوع الترميز (Barcode Type)</label>
                    <select name="barcode_type" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                        <option value="code128" {{ $setting->barcode_type === 'code128' ? 'selected' : '' }}>Code 128 (شائع لمعظم الأصناف)</option>
                        <option value="ean13" {{ $setting->barcode_type === 'ean13' ? 'selected' : '' }}>EAN-13 (الباركود الدولي المكون من 13 رقم)</option>
                        <option value="qr" {{ $setting->barcode_type === 'qr' ? 'selected' : '' }}>رمز QR Code</option>
                    </select>
                </div>

                <div style="background: #F8FAFC; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; margin-bottom: 24px; display: flex; flex-direction: column; gap: 10px;">
                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="show_company_name" value="1" {{ $setting->show_company_name ? 'checked' : '' }} onchange="toggleElem('prev_company', this.checked)" style="accent-color: var(--primary); width: 16px; height: 16px;">
                        إظهار اسم الشركة في رأس الملصق
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="show_product_name" value="1" {{ $setting->show_product_name ? 'checked' : '' }} onchange="toggleElem('prev_item', this.checked)" style="accent-color: var(--primary); width: 16px; height: 16px;">
                        إظهار اسم الصنف بالكامل
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="show_price" value="1" {{ $setting->show_price ? 'checked' : '' }} onchange="toggleElem('prev_price', this.checked)" style="accent-color: var(--primary); width: 16px; height: 16px;">
                        إظهار سعر البيع للمستهلك
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 13.5px; cursor: pointer;">
                        <input type="checkbox" name="show_barcode_number" value="1" {{ $setting->show_barcode_number ? 'checked' : '' }} onchange="toggleElem('prev_code', this.checked)" style="accent-color: var(--primary); width: 16px; height: 16px;">
                        إظهار أرقام الباركود أسفل الخطوط
                    </label>
                </div>

                <button type="submit" class="btn-primary-custom" style="padding: 12px 28px;">
                    <i class="fa-solid fa-save"></i>
                    حفظ إعدادات الباركود
                </button>
            </form>

            <!-- Live Label Visualizer -->
            <div>
                <h4 style="font-weight: 800; font-size: 15px; color: var(--text-main); margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-eye" style="color: var(--accent-gold);"></i>
                    معاينة حية للملصق الحراري (Live Visualizer)
                </h4>

                <div style="background: #F1F5F9; border: 2px dashed #CBD5E1; border-radius: 14px; padding: 40px; display: flex; align-items: center; justify-content: center; min-height: 280px;">
                    <div id="barcodePreviewSticker" style="background: #FFFFFF; border: 1px solid #94A3B8; border-radius: 6px; box-shadow: 0 6px 16px rgba(0,0,0,0.12); width: 220px; height: 150px; padding: 12px; display: flex; flex-direction: column; justify-content: space-between; text-align: center; font-size: 11px;">
                        <div>
                            <div id="prev_company" style="font-weight: 800; font-size: 11px; color: #0F172A; display: {{ $setting->show_company_name ? 'block' : 'none' }};">
                                شركة العزيزي للتجارة
                            </div>
                            <div id="prev_item" style="font-weight: 700; font-size: 12px; color: var(--primary); margin: 2px 0; display: {{ $setting->show_product_name ? 'block' : 'none' }};">
                                صنف تجريبي فاخر 250 مل
                            </div>
                        </div>

                        <!-- Barcode lines mockup -->
                        <div style="padding: 4px 0;">
                            <div style="height: 42px; background: repeating-linear-gradient(90deg, #000 0px, #000 2px, transparent 2px, transparent 4px, #000 4px, #000 7px, transparent 7px, transparent 9px, #000 9px, #000 10px, transparent 10px, transparent 13px); width: 85%; margin: 0 auto;"></div>
                            <div id="prev_code" style="font-family: monospace; font-size: 11px; font-weight: 700; color: #1E293B; margin-top: 2px; display: {{ $setting->show_barcode_number ? 'block' : 'none' }};">
                                6281002345678
                            </div>
                        </div>

                        <div id="prev_price" style="font-weight: 900; font-size: 14px; color: #DC2626; display: {{ $setting->show_price ? 'block' : 'none' }};">
                            السعر: 45.00 ر.س
                        </div>
                    </div>
                </div>

                <div style="margin-top: 12px; text-align: center; font-size: 12px; color: var(--text-muted);">
                    المقاس المطبق: <strong>{{ $setting->label_width }} مم × {{ $setting->label_height }} مم</strong>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function toggleElem(id, show) {
        document.getElementById(id).style.display = show ? 'block' : 'none';
    }
</script>
@endsection

