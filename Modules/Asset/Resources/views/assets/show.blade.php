@extends('layouts.app')

@section('title', 'بطاقة الأصل الثابت: ' . $asset->name)

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                    <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                        <i class="fa-solid fa-cube"></i>
                    </span>
                    {{ $asset->name }}
                </h2>
                <span style="font-family: monospace; font-size: 13px; font-weight: 800; background: #F1F5F9; color: var(--text-muted); padding: 4px 10px; border-radius: 6px;">
                    {{ $asset->asset_number }}
                </span>

                @if($asset->status === 'active')
                    <span style="background: #D1E7DD; color: #0F5132; font-weight: 800; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                        <i class="fa-solid fa-check"></i> نشط بالخدمة
                    </span>
                @elseif($asset->status === 'in_maintenance')
                    <span style="background: #FEF3C7; color: #D97706; font-weight: 800; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                        <i class="fa-solid fa-wrench"></i> تحت الصيانة
                    </span>
                @elseif($asset->status === 'fully_depreciated')
                    <span style="background: #E0E7FF; color: #4338CA; font-weight: 800; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                        <i class="fa-solid fa-flag-checkered"></i> مكتمل الإهلاك دفترياً
                    </span>
                @elseif($asset->status === 'disposed')
                    <span style="background: #FEE2E2; color: #DC2626; font-weight: 800; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                        <i class="fa-solid fa-trash"></i> مستبعد / مكهّن
                    </span>
                @elseif($asset->status === 'sold')
                    <span style="background: #E2E8F0; color: #475569; font-weight: 800; padding: 4px 12px; border-radius: 20px; font-size: 12px;">
                        <i class="fa-solid fa-handshake"></i> مباع
                    </span>
                @endif
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">
                تاريخ التسجيل: {{ $asset->created_at->format('Y-m-d H:i') }} | الفرع: {{ $asset->branch?->name }} | التصنيف: {{ $asset->category?->name }}
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('assets.assets.barcode', $asset->id) }}" target="_blank" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: #4338CA; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-barcode"></i>
                طباعة ملصق الباركود
            </a>
            @if(in_array($asset->status, ['active', 'in_maintenance', 'fully_depreciated']))
                <a href="{{ route('assets.disposals.create', ['asset_id' => $asset->id]) }}" style="background: #FEF3C7; border: 1px solid #FDE68A; padding: 9px 16px; border-radius: 10px; text-decoration: none; color: #B45309; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-recycle"></i>
                    استبعاد / بيع الأصل
                </a>
            @endif
            <a href="{{ route('assets.assets.edit', $asset->id) }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--accent-gold); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square"></i>
                تعديل البيانات
            </a>
            <a href="{{ route('assets.assets.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                رجوع
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div style="background: #D1E7DD; border: 1px solid #A3CFBB; color: #0F5132; padding: 14px 18px; border-radius: 10px; font-size: 13.5px; font-weight: 700;">
        <i class="fa-solid fa-circle-check" style="margin-left: 8px;"></i>
        {{ session('success') }}
    </div>
    @endif

    <!-- Financial Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">تكلفة الشراء التاريخية</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--primary);">{{ number_format($asset->purchase_cost, 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-trash-can-arrow-up"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">قيمة الخردة / النفاية</span>
                <strong style="font-size: 18px; font-weight: 900; color: #D97706;">{{ number_format($asset->salvage_value, 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #EF4444; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #EF4444; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-arrow-trend-down"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مجمع الإهلاك المتراكم</span>
                <strong style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ number_format($asset->total_depreciated_amount, 2) }} ر.س</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">صافي القيمة الدفترية الحالية</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ number_format($asset->current_book_value, 2) }} ر.س</strong>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        
        <!-- Metadata Details Card -->
        <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <i class="fa-solid fa-circle-info" style="color: var(--primary);"></i>
                المواصفات والبيانات الإدارية
            </h4>

            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">الفرع التابع له:</span>
                    <strong style="color: var(--text-main);">{{ $asset->branch?->name ?? '—' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">الموظف المستلم (العهدة):</span>
                    @if($asset->custodian)
                        <strong style="color: var(--primary);"><i class="fa-solid fa-user-shield"></i> {{ $asset->custodian->name }} ({{ $asset->custodian->emp_code }})</strong>
                    @else
                        <span style="color: var(--text-muted);">عهدة عامة</span>
                    @endif
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">الموقع / المبنى:</span>
                    <strong style="color: var(--text-main);">{{ $asset->location?->name ?? '—' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">مركز التكلفة:</span>
                    <strong style="color: var(--text-main);">{{ $asset->costCenter?->name ? ($asset->costCenter->code . ' - ' . $asset->costCenter->name) : '—' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">الرقم التسلسلي (S/N):</span>
                    <span style="font-family: monospace; font-weight: 700; color: var(--text-main);">{{ $asset->serial_number ?: '—' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">رقم الموديل / الطراز:</span>
                    <span style="color: var(--text-main);">{{ $asset->model_number ?: '—' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">المورد / جهة التوريد:</span>
                    <strong style="color: var(--text-main);">{{ $asset->supplier?->name ?? '—' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">تاريخ بدء الخدمة والتشغيل:</span>
                    <span style="color: var(--text-main);">{{ $asset->in_service_date ? $asset->in_service_date->format('Y-m-d') : '—' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">انتهاء فترة الضمان:</span>
                    <span style="color: var(--text-main);">{{ $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : 'لا يوجد ضمان' }}</span>
                </div>
            </div>
        </div>

        <!-- Depreciation & Accounting Engine Card -->
        <div class="erp-card" style="margin-bottom: 0; padding: 22px;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin-bottom: 16px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
                <i class="fa-solid fa-calculator" style="color: var(--accent-gold);"></i>
                معايير الإهلاك المحاسبي والأستاذ العام
            </h4>

            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">طريقة الإهلاك:</span>
                    <strong style="color: var(--primary);">{{ $asset->depreciation_method_label }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">العمر الافتراضي:</span>
                    <strong style="color: var(--text-main);">{{ $asset->useful_life_years }} سنوات</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">النسبة السنوية للإهلاك:</span>
                    <strong style="color: var(--primary);">{{ number_format($asset->depreciation_rate, 2) }}% سنوياً</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">قسط الإهلاك السنوي التقديري:</span>
                    <strong style="color: #0F5132; font-size: 14px;">{{ number_format($annualDepreciation, 2) }} ر.س</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">قسط الإهلاك الشهري التقديري:</span>
                    <strong style="color: #0F5132; font-size: 14px;">{{ number_format($monthlyDepreciation, 2) }} ر.س</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">حساب الأصل (شجرة الحسابات):</span>
                    <span style="color: var(--text-main); font-size: 12px;">{{ $asset->category?->assetAccount?->name ? ($asset->category->assetAccount->code . ' - ' . $asset->category->assetAccount->name) : 'تلقائي' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">حساب مجمع الإهلاك:</span>
                    <span style="color: var(--text-main); font-size: 12px;">{{ $asset->category?->depreciationAccount?->name ? ($asset->category->depreciationAccount->code . ' - ' . $asset->category->depreciationAccount->name) : 'تلقائي (1209)' }}</span>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--text-muted);">حساب مصروف الإهلاك:</span>
                    <span style="color: var(--text-main); font-size: 12px;">{{ $asset->category?->depreciationExpenseAccount?->name ? ($asset->category->depreciationExpenseAccount->code . ' - ' . $asset->category->depreciationExpenseAccount->name) : 'تلقائي (5105)' }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Depreciation History Table Card -->
    <div class="erp-card" style="padding: 0; overflow: hidden; margin-bottom: 0;">
        <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-size: 14px; font-weight: 800; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-history" style="color: var(--primary); margin-left: 6px;"></i>
                سجل قيود وإدخالات الإهلاك الدورية للأصل ({{ $asset->depreciations->count() }})
            </h4>
        </div>

        <div style="overflow-x: auto;">
            <table class="erp-table" style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                <thead style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12px;">
                    <tr>
                        <th style="padding: 12px 16px;">تاريخ الإهلاك</th>
                        <th style="padding: 12px 16px;">السنة والفترة المالية</th>
                        <th style="padding: 12px 16px;">قسط الإهلاك المخصوم</th>
                        <th style="padding: 12px 16px;">مجمع الإهلاك بعد القيد</th>
                        <th style="padding: 12px 16px;">صافي القيمة الدفترية بعد القيد</th>
                        <th style="padding: 12px 16px;">قيد اليومية العامة (GL)</th>
                        <th style="padding: 12px 16px; text-align: center;">حالة الترحيل</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asset->depreciations as $dep)
                    <tr style="border-bottom: 1px solid #EDF2F7;">
                        <td style="padding: 14px 16px; font-weight: 700; color: var(--text-main);">
                            {{ $dep->depreciation_date ? $dep->depreciation_date->format('Y-m-d') : '—' }}
                        </td>
                        <td style="padding: 14px 16px;">
                            <span style="font-weight: 800; color: var(--text-main);">{{ $dep->fiscal_period }}</span>
                            <small style="color: var(--text-muted); display: block;">سنة: {{ $dep->fiscal_year }}</small>
                        </td>
                        <td style="padding: 14px 16px; font-weight: 800; color: #DC2626;">
                            {{ number_format($dep->depreciation_amount, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; color: var(--text-main);">
                            {{ number_format($dep->accumulated_depreciation_after, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px; font-weight: 800; color: #0F5132;">
                            {{ number_format($dep->book_value_after, 2) }} ر.س
                        </td>
                        <td style="padding: 14px 16px;">
                            @if($dep->journalEntry)
                                <span style="font-family: monospace; font-weight: 800; color: #4338CA;">
                                    {{ $dep->journalEntry->entry_number }}
                                </span>
                            @else
                                <span style="color: var(--text-muted);">غير مرحل بعد</span>
                            @endif
                        </td>
                        <td style="padding: 14px 16px; text-align: center;">
                            @if($dep->is_posted)
                                <span style="background: #D1E7DD; color: #0F5132; font-weight: 700; padding: 4px 10px; border-radius: 8px; font-size: 11.5px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fa-solid fa-check-circle"></i> مؤثّر بالدفتر
                                </span>
                            @else
                                <form action="{{ route('assets.depreciations.post-gl', $dep->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" style="background: #FEF3C7; border: 1px solid #FDE68A; color: #B45309; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 11.5px; cursor: pointer;">
                                        <i class="fa-solid fa-cloud-arrow-up"></i> ترحيل الآن
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: var(--text-muted);">
                            لم يُسجّل أي قيد إهلاك دوري لهذا الأصل حتى الآن
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
