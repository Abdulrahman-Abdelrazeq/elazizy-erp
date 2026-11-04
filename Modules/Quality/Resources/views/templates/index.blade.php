@extends('layouts.app')

@section('title', 'معايير ونماذج الفحص وقوائم التحقق (QC Templates & Checklists)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-list-check"></i>
                </span>
                معايير ونماذج فحص الجودة (QC Templates & Standards)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد مواصفات الفحص القياسية، نقاط التفتيش والاختبار، والحدود المسموح بها للمواد والمنتجات والمشاريع
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('quality.inspections.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-clipboard-check" style="color: var(--primary);"></i>
                سجل الفحوصات
            </a>
            <a href="{{ route('quality.ncrs.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #DC2626;"></i>
                عدم المطابقة (NCR)
            </a>
            <a href="{{ route('quality.analytics.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-chart-pie" style="color: var(--accent-gold);"></i>
                مؤشرات الجودة FPY
            </a>
            <a href="{{ route('quality.templates.create') }}" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة نموذج فحص جديد
            </a>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-list-ul"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي النماذج القياسية</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--text-main);">{{ number_format($stats['total']) }}</span>
                <span style="font-size: 11px; color: var(--primary); display: block;">قوائم فحص معتمدة</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-truck-ramp-box"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">فحص استلام مواد خام</span>
                <span style="font-size: 20px; font-weight: 900; color: #2563EB;">{{ number_format($stats['incoming']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">توريدات وفحص موردين</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--accent-gold); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(217, 119, 6, 0.1); color: var(--accent-gold); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-industry"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">فحص أثناء التشغيل</span>
                <span style="font-size: 20px; font-weight: 900; color: var(--accent-gold);">{{ number_format($stats['in_process']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">خطوط التصنيع والإنتاج</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-box-check"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">فحص المنتجات النهائية</span>
                <span style="font-size: 20px; font-weight: 900; color: #10B981;">{{ number_format($stats['final_product']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">إفراج قبل الشحن والتسليم</span>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #8B5CF6; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(139, 92, 246, 0.1); color: #8B5CF6; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-helmet-safety"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">فحوصات مواقع المشاريع</span>
                <span style="font-size: 20px; font-weight: 900; color: #8B5CF6;">{{ number_format($stats['project_site']) }}</span>
                <span style="font-size: 11px; color: var(--text-muted); display: block;">اختبارات الخرسانة والتربة</span>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="erp-card" style="padding: 16px;">
        <form method="GET" action="{{ route('quality.templates.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) 100px; gap: 12px; align-items: end;">
            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">بحث بالاسم أو الكود</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم النموذج أو الكود..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">مرحلة / نوع الفحص</label>
                <select name="type" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    <option value="">جميع المراحل</option>
                    <option value="incoming" {{ request('type') == 'incoming' ? 'selected' : '' }}>استلام مواد خام (Incoming)</option>
                    <option value="in_process" {{ request('type') == 'in_process' ? 'selected' : '' }}>أثناء التشغيل (In-Process)</option>
                    <option value="final_product" {{ request('type') == 'final_product' ? 'selected' : '' }}>منتج نهائي تام (Final Product)</option>
                    <option value="project_site" {{ request('type') == 'project_site' ? 'selected' : '' }}>موقع المشروع (Site Test)</option>
                </select>
            </div>

            <div style="display: flex; gap: 6px;">
                <button type="submit" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; border: none; cursor: pointer; flex: 1;">
                    <i class="fa-solid fa-filter"></i>
                    تصفية
                </button>
                @if(request()->hasAny(['search', 'type']))
                    <a href="{{ route('quality.templates.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); border-radius: 8px; padding: 9px 12px; color: var(--text-muted); text-decoration: none; display: flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Templates Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-list-check" style="color: var(--primary);"></i>
                دليل نماذج الفحص وقوائم التحقق
            </h3>
            <span style="font-size: 12px; color: var(--text-muted); font-weight: 600;">
                عرض {{ $templates->firstItem() ?? 0 }} إلى {{ $templates->lastItem() ?? 0 }} من أصل {{ $templates->total() }} نموذج
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px 16px;">كود النموذج</th>
                        <th style="padding: 12px 16px;">اسم النموذج القياسي</th>
                        <th style="padding: 12px 16px;">المرحلة والتصنيف</th>
                        <th style="padding: 12px 16px;">الصنف أو التصنيف المرتبط</th>
                        <th style="padding: 12px 16px;">عدد نقاط الفحص</th>
                        <th style="padding: 12px 16px;">الحالة</th>
                        <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                <a href="{{ route('quality.templates.show', $template->id) }}" style="text-decoration: none; color: var(--primary);">
                                    {{ $template->code }}
                                </a>
                            </td>
                            <td style="padding: 14px 16px;">
                                <a href="{{ route('quality.templates.show', $template->id) }}" style="font-weight: 800; color: var(--text-main); text-decoration: none; display: block;">
                                    {{ $template->name }}
                                </a>
                                @if($template->description)
                                    <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                        {{ Str::limit($template->description, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td style="padding: 14px 16px;">
                                <span style="font-weight: 700; color: var(--text-main);">{{ $template->type_label }}</span>
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($template->item)
                                    <div style="font-weight: 600; color: var(--primary);">{{ $template->item->name }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted); font-family: monospace;">SKU: {{ $template->item->sku }}</div>
                                @elseif($template->category)
                                    <span style="background: #F1F5F9; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700;">{{ $template->category->name }}</span>
                                @else
                                    <span style="color: var(--text-muted);">نموذج فحص عام</span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; font-weight: 800; color: var(--primary);">
                                <span style="background: rgba(15, 81, 50, 0.1); padding: 3px 10px; border-radius: 999px; font-size: 12px;">
                                    {{ $template->items->count() }} معيار
                                </span>
                            </td>
                            <td style="padding: 14px 16px;">
                                @if($template->is_active)
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: rgba(16, 185, 129, 0.1); color: #059669;">
                                        نشط ومعتمد
                                    </span>
                                @else
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; background: #F1F5F9; color: #64748B;">
                                        معطل مؤقتاً
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 14px 16px; text-align: center;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <a href="{{ route('quality.templates.show', $template->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; text-decoration: none;" title="عرض وإدارة المعايير">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('quality.templates.edit', $template->id) }}" style="width: 32px; height: 32px; border-radius: 8px; background: #F1F5F9; color: var(--text-main); display: flex; align-items: center; justify-content: center; text-decoration: none;" title="تعديل النموذج">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('quality.templates.destroy', $template->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا النموذج القياسي؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; background: #FEF2F2; border: 1px solid #FCA5A5; color: #DC2626; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="حذف">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted);">
                                <i class="fa-solid fa-list-check" style="font-size: 40px; margin-bottom: 12px; color: #CBD5E1; display: block;"></i>
                                لا توجد نماذج فحص مسجلة. قم بإنشاء أول قائمة تحقق ومعيار فحص الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($templates->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $templates->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
