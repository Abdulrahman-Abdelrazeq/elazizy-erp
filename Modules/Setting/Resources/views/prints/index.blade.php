@extends('layouts.app')

@section('title', 'قوالب وتصميم طباعة الفواتير')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Breadcrumb & Actions Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-print" style="color: var(--primary); margin-left: 8px;"></i>
                إعدادات وتخصيص قوالب طباعة الفواتير
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تخصيص الفواتير الحرارية (80 مم) وفواتير المقاس الكامل (A4) وتضمين شعار المنشأة والباركود
            </span>
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('settings.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة للإعدادات
            </a>
        </div>
    </div>

    <!-- Templates Grid & Visual Preview -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 24px;">
        @foreach($templates as $template)
            <div class="erp-card">
                <div class="card-header-custom">
                    <h3 class="card-title-custom">
                        <i class="fa-solid fa-file-invoice"></i>
                        {{ $template->title }}
                    </h3>
                    <span style="font-size: 11.5px; background: var(--primary-light); color: var(--primary); padding: 3px 10px; border-radius: 10px; font-weight: 700;">
                        {{ $template->paper_size === 'thermal_80mm' ? 'حراري 80 مم' : 'مقاس A4' }}
                    </span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <!-- Edit Form -->
                    <form action="{{ route('settings.prints.update', $template->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px;">عنوان القالب</label>
                            <input type="text" name="title" value="{{ $template->title }}" required style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px;">
                        </div>

                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px;">مقاس الورق</label>
                            <select name="paper_size" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px; font-weight: 600;">
                                <option value="thermal_80mm" {{ $template->paper_size === 'thermal_80mm' ? 'selected' : '' }}>طابعة حرارية (80 مم) - الكاشير</option>
                                <option value="thermal_57mm" {{ $template->paper_size === 'thermal_57mm' ? 'selected' : '' }}>طابعة حرارية صغيرة (57 مم)</option>
                                <option value="A4" {{ $template->paper_size === 'A4' ? 'selected' : '' }}>ورق قياسي (A4) - فاتورة رسمية</option>
                                <option value="A5" {{ $template->paper_size === 'A5' ? 'selected' : '' }}>ورق نصف قياسي (A5)</option>
                            </select>
                        </div>

                        <div style="margin-bottom: 14px; display: flex; flex-direction: column; gap: 8px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="show_logo" value="1" {{ $template->show_logo ? 'checked' : '' }} style="accent-color: var(--primary);">
                                إظهار شعار واسم الشركة
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="show_qr" value="1" {{ $template->show_qr ? 'checked' : '' }} style="accent-color: var(--primary);">
                                إظهار رمز الاستجابة السريع (QR Code)
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="show_tax_number" value="1" {{ $template->show_tax_number ? 'checked' : '' }} style="accent-color: var(--primary);">
                                إظهار الرقم الضريبي والسجل التجاري
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                                <input type="checkbox" name="show_terms" value="1" {{ $template->show_terms ? 'checked' : '' }} style="accent-color: var(--primary);">
                                إظهار الشروط والأحكام أسفل الفاتورة
                            </label>
                        </div>

                        <div style="margin-bottom: 14px;">
                            <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px;">نص الشروط والأحكام</label>
                            <textarea name="terms_and_conditions" rows="3" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 12.5px;">{{ $template->terms_and_conditions }}</textarea>
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 5px;">الرسالة الترحيبية في التذييل</label>
                            <input type="text" name="footer_text" value="{{ $template->footer_text }}" style="width: 100%; padding: 8px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-size: 13px;">
                        </div>

                        <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center;">
                            <i class="fa-solid fa-check"></i>
                            تحديث هذا القالب
                        </button>
                    </form>

                    <!-- Mockup Preview Box -->
                    <div style="background: #FFF; border: 1.5px dashed var(--border-color); border-radius: 12px; padding: 18px; font-size: 11.5px; box-shadow: inset 0 0 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="text-align: center; border-bottom: 1px dashed #CBD5E1; padding-bottom: 12px; margin-bottom: 10px;">
                                <strong style="font-size: 14px; color: #0F172A; display: block;">شركة العزيزي للتجارة والتوريدات</strong>
                                <span style="font-size: 10.5px; color: #64748B;">الرقم الضريبي: 310998877600003</span>
                                <div style="font-size: 11px; font-weight: 700; color: #1E293B; margin-top: 4px;">فاتورة ضريبية مبسطة</div>
                                <div style="font-size: 10px; color: #94A3B8;">رقم الفاتورة: #INV-2026-0042</div>
                            </div>

                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 11px;">
                                <tr style="border-bottom: 1px solid #E2E8F0; color: #64748B;">
                                    <th style="padding: 4px 0; text-align: right;">الصنف</th>
                                    <th style="padding: 4px 0; text-align: center;">الكمية</th>
                                    <th style="padding: 4px 0; text-align: left;">المجموع</th>
                                </tr>
                                <tr style="border-bottom: 1px dashed #F1F5F9;">
                                    <td style="padding: 4px 0;">منتج تجريبي نموذج</td>
                                    <td style="padding: 4px 0; text-align: center;">2</td>
                                    <td style="padding: 4px 0; text-align: left;">150.00</td>
                                </tr>
                            </table>

                            <div style="border-top: 1px dashed #CBD5E1; padding-top: 8px; margin-bottom: 12px;">
                                <div style="display: flex; justify-content: space-between; font-weight: 700;">
                                    <span>المجموع قبل الضريبة:</span>
                                    <span>150.00 ر.س</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; color: #475569;">
                                    <span>ضريبة القيمة المضافة (15%):</span>
                                    <span>22.50 ر.س</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-weight: 900; font-size: 13px; color: var(--primary); margin-top: 4px;">
                                    <span>الإجمالي الكلي:</span>
                                    <span>172.50 ر.س</span>
                                </div>
                            </div>
                        </div>

                        <div style="text-align: center; border-top: 1px dashed #CBD5E1; padding-top: 10px;">
                            @if($template->show_qr)
                                <div style="margin-bottom: 6px;">
                                    <i class="fa-solid fa-qrcode fa-3x" style="color: #1E293B;"></i>
                                </div>
                            @endif
                            <p style="font-size: 10px; color: #64748B; margin-bottom: 4px;">
                                {{ $template->terms_and_conditions }}
                            </p>
                            <strong style="font-size: 10.5px; color: var(--primary);">
                                {{ $template->footer_text }}
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

