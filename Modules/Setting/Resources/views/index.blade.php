@extends('layouts.app')

@section('title', 'الإعدادات العامة للشركة والنظام')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Navigation Cards for Sub-modules -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <a href="{{ route('settings.index') }}" style="text-decoration: none; border: 2px solid var(--primary); border-radius: 12px; padding: 16px; background: var(--primary-light); display: flex; align-items: center; gap: 14px;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <strong style="font-size: 14px; color: var(--primary); display: block;">الإعدادات العامة</strong>
                <span style="font-size: 11.5px; color: #475569;">بيانات الشركة والسياسات</span>
            </div>
        </a>

        <a href="{{ route('settings.taxes.index') }}" style="text-decoration: none; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; background: #fff; display: flex; align-items: center; gap: 14px; transition: all 0.2s;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-percent"></i>
            </div>
            <div>
                <strong style="font-size: 14px; color: var(--text-main); display: block;">الضرائب والـ VAT</strong>
                <span style="font-size: 11.5px; color: var(--text-muted);">ضريبة القيمة المضافة</span>
            </div>
        </a>

        <a href="{{ route('settings.currencies.index') }}" style="text-decoration: none; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; background: #fff; display: flex; align-items: center; gap: 14px; transition: all 0.2s;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <strong style="font-size: 14px; color: var(--text-main); display: block;">دليل العملات</strong>
                <span style="font-size: 11.5px; color: var(--text-muted);">أسعار الصرف والرموز</span>
            </div>
        </a>

        <a href="{{ route('settings.locations.index') }}" style="text-decoration: none; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; background: #fff; display: flex; align-items: center; gap: 14px; transition: all 0.2s;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #CCFBF1; color: #0F766E; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-map-location-dot"></i>
            </div>
            <div>
                <strong style="font-size: 14px; color: var(--text-main); display: block;">المناطق والمدن</strong>
                <span style="font-size: 11.5px; color: var(--text-muted);">شجرة الشحن والمواقع</span>
            </div>
        </a>

        <a href="{{ route('settings.prints.index') }}" style="text-decoration: none; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; background: #fff; display: flex; align-items: center; gap: 14px; transition: all 0.2s;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #FCE7F3; color: #BE185D; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-print"></i>
            </div>
            <div>
                <strong style="font-size: 14px; color: var(--text-main); display: block;">قوالب الطباعة</strong>
                <span style="font-size: 11.5px; color: var(--text-muted);">الفواتير الحرارية وA4</span>
            </div>
        </a>

        <a href="{{ route('settings.barcode.index') }}" style="text-decoration: none; border: 1px solid var(--border-color); border-radius: 12px; padding: 16px; background: #fff; display: flex; align-items: center; gap: 14px; transition: all 0.2s;">
            <div style="width: 44px; height: 44px; border-radius: 10px; background: #F1F5F9; color: #334155; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                <i class="fa-solid fa-barcode"></i>
            </div>
            <div>
                <strong style="font-size: 14px; color: var(--text-main); display: block;">ملصقات الباركود</strong>
                <span style="font-size: 11.5px; color: var(--text-muted);">مقاسات ملصق الصنف</span>
            </div>
        </a>
    </div>

    <!-- Main Settings Form Container -->
    <div class="erp-card">
        <div class="card-header-custom">
            <h3 class="card-title-custom">
                <i class="fa-solid fa-gear"></i>
                إعدادات وهوية شركة: {{ $tenant->company_name ?? 'شركة العزيزي' }}
            </h3>
            <span style="font-size: 12px; background: var(--primary-light); color: var(--primary); padding: 4px 12px; border-radius: 20px; font-weight: 700;">
                موديول 1 (Setting & Location)
            </span>
        </div>

        <form action="{{ route('settings.update') }}" method="POST">
            @csrf

            <!-- Section 1: Company Profile -->
            <div style="margin-bottom: 30px;">
                <h4 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-building"></i>
                    بيانات الشركة الرسمية والفواتير الضريبية
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px;">
                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اسم المنشأة / الشركة *</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $tenant->company_name) }}" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">الرقم الضريبي (VAT Number)</label>
                        <input type="text" name="tax_number" value="{{ old('tax_number', $tenant->tax_number) }}" placeholder="مثال: 310998877600003" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">رقم السجل التجاري (CR)</label>
                        <input type="text" name="commercial_register" value="{{ old('commercial_register', $tenant->commercial_register) }}" placeholder="مثال: 1010884499" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">رقم الهاتف / الجوال</label>
                        <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" placeholder="+966 50 123 4567" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">البريد الإلكتروني الرسمي</label>
                        <input type="email" name="email" value="{{ old('email', $tenant->email) }}" placeholder="info@elazizy-group.com" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">العنوان والمقر الرئيسي</label>
                        <input type="text" name="address" value="{{ old('address', $tenant->address) }}" placeholder="الرياض - حي العليا" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    </div>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid var(--border-color); margin: 24px 0;">

            <!-- Section 2: Invoicing & Sales Rules -->
            <div style="margin-bottom: 30px;">
                <h4 style="font-size: 16px; font-weight: 800; color: var(--primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    سياسات الفواتير والمبيعات ونقاط البيع (Commercial Policies)
                </h4>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                    <div style="background: #F8FAFC; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 12px;">
                        <input type="checkbox" name="invoice_auto_print" id="invoice_auto_print" value="1" {{ $settings['invoice_auto_print'] == '1' ? 'checked' : '' }} style="accent-color: var(--primary); width: 18px; height: 18px; margin-top: 2px;">
                        <div>
                            <label for="invoice_auto_print" style="font-weight: 700; cursor: pointer; display: block; font-size: 14px;">طباعة الفاتورة تلقائياً عند الحفظ</label>
                            <span style="font-size: 12px; color: var(--text-muted);">إرسال أمر الطباعة المباشر لطابعة الإيصالات فور حفظ الفاتورة</span>
                        </div>
                    </div>

                    <div style="background: #F8FAFC; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 12px;">
                        <input type="checkbox" name="invoice_show_qr_code" id="invoice_show_qr_code" value="1" {{ $settings['invoice_show_qr_code'] == '1' ? 'checked' : '' }} style="accent-color: var(--primary); width: 18px; height: 18px; margin-top: 2px;">
                        <div>
                            <label for="invoice_show_qr_code" style="font-weight: 700; cursor: pointer; display: block; font-size: 14px;">تضمين رمز الاستجابة السريع (QR Code)</label>
                            <span style="font-size: 12px; color: var(--text-muted);">متوافق مع اشتراطات هيئة الزكاة والضريبة والجمارك (ZATCA)</span>
                        </div>
                    </div>

                    <div style="background: #F8FAFC; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 12px;">
                        <input type="checkbox" name="allow_negative_stock" id="allow_negative_stock" value="1" {{ $settings['allow_negative_stock'] == '1' ? 'checked' : '' }} style="accent-color: var(--primary); width: 18px; height: 18px; margin-top: 2px;">
                        <div>
                            <label for="allow_negative_stock" style="font-weight: 700; cursor: pointer; display: block; font-size: 14px;">السماح بالبيع بالسالب (الرصيد الصفري)</label>
                            <span style="font-size: 12px; color: var(--text-muted);">إمكانية إصدار فواتير مبيعات حتى لو نفذ رصيد الصنف من المستودع</span>
                        </div>
                    </div>

                    <div style="background: #F8FAFC; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 12px;">
                        <input type="checkbox" name="update_price_on_purchase" id="update_price_on_purchase" value="1" {{ $settings['update_price_on_purchase'] == '1' ? 'checked' : '' }} style="accent-color: var(--primary); width: 18px; height: 18px; margin-top: 2px;">
                        <div>
                            <label for="update_price_on_purchase" style="font-weight: 700; cursor: pointer; display: block; font-size: 14px;">تحديث متوسط سعر التكلفة آلياً</label>
                            <span style="font-size: 12px; color: var(--text-muted);">إعادة احتساب متوسط سعر الشراء للصنف مع كل فاتورة مشتريات واردة</span>
                        </div>
                    </div>

                    <div style="background: #F8FAFC; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 12px;">
                        <input type="checkbox" name="pos_enable_shifts" id="pos_enable_shifts" value="1" {{ $settings['pos_enable_shifts'] == '1' ? 'checked' : '' }} style="accent-color: var(--primary); width: 18px; height: 18px; margin-top: 2px;">
                        <div>
                            <label for="pos_enable_shifts" style="font-weight: 700; cursor: pointer; display: block; font-size: 14px;">إلزامية شيفتات الكاشير ونقاط البيع (POS)</label>
                            <span style="font-size: 12px; color: var(--text-muted);">اشتراط فتح وإغلاق الشيفت وتسليم العهد النقدية لكل مستخدم</span>
                        </div>
                    </div>

                    <div style="background: #F8FAFC; padding: 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: flex-start; gap: 12px;">
                        <input type="checkbox" name="pos_auto_open_drawer" id="pos_auto_open_drawer" value="1" {{ $settings['pos_auto_open_drawer'] == '1' ? 'checked' : '' }} style="accent-color: var(--primary); width: 18px; height: 18px; margin-top: 2px;">
                        <div>
                            <label for="pos_auto_open_drawer" style="font-weight: 700; cursor: pointer; display: block; font-size: 14px;">فتح درج النقدية آلياً مع كل عملية دفع</label>
                            <span style="font-size: 12px; color: var(--text-muted);">إرسال نبضة لدرج الكاشير المتصل بالطابعة الحرارية</span>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="submit" class="btn-primary-custom" style="padding: 12px 28px; font-size: 15px;">
                    <i class="fa-solid fa-floppy-disk"></i>
                    حفظ كافة الإعدادات
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

