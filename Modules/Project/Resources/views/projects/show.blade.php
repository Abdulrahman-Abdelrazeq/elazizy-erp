@extends('layouts.app')

@section('title', 'مشروع: ' . $project->name . ' (' . $project->code . ')')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                <a href="{{ route('projects.projects.index') }}" style="color: var(--primary); text-decoration: none; font-size: 13px; font-weight: 700;">المشاريع والمقاولات</a>
                <i class="fa-solid fa-chevron-left" style="font-size: 10px; color: var(--text-muted);"></i>
                <span style="font-family: monospace; font-size: 13px; font-weight: 800; background: #E2E8F0; padding: 2px 8px; border-radius: 6px;">{{ $project->code }}</span>
                @php
                    $badgeStyle = match($project->status) {
                        'in_progress'       => 'background: rgba(15, 81, 50, 0.1); color: var(--primary); border: 1px solid rgba(15, 81, 50, 0.2);',
                        'completed'         => 'background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16, 185, 129, 0.2);',
                        'delivered_primary' => 'background: rgba(37, 99, 235, 0.1); color: #2563EB; border: 1px solid rgba(37, 99, 235, 0.2);',
                        'planning'          => 'background: rgba(217, 119, 6, 0.1); color: #D97706; border: 1px solid rgba(217, 119, 6, 0.2);',
                        'suspended'         => 'background: rgba(220, 38, 38, 0.1); color: #DC2626; border: 1px solid rgba(220, 38, 38, 0.2);',
                        default             => 'background: #F1F5F9; color: #64748B; border: 1px solid #E2E8F0;',
                    };
                @endphp
                <span style="display: inline-block; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $badgeStyle !!}">
                    {{ $project->status_label }}
                </span>
            </div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 38px; height: 38px; border-radius: 10px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-helmet-safety"></i>
                </span>
                {{ $project->name }}
            </h2>
            <div style="font-size: 13px; color: var(--text-muted); margin-top: 6px; display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <span><i class="fa-solid fa-user-tie" style="color: var(--accent-gold);"></i> <strong>العميل:</strong> {{ $project->client?->name ?? 'غير محدد' }}</span>
                <span><i class="fa-solid fa-code-branch" style="color: var(--primary);"></i> <strong>الفرع:</strong> {{ $project->branch?->name ?? '-' }}</span>
                @if($project->projectManager)
                    <span><i class="fa-solid fa-user-gear"></i> <strong>المهندس المسؤول:</strong> {{ $project->projectManager->name }}</span>
                @endif
                @if($project->location)
                    <span><i class="fa-solid fa-location-dot" style="color: #DC2626;"></i> {{ $project->location }}</span>
                @endif
            </div>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('projects.extracts.create', $project->id) }}" class="btn-primary-custom" style="padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 800; text-decoration: none;">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                إنشاء مستخلص جاري
            </a>
            <a href="{{ route('projects.projects.edit', $project->id) }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 8px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-pen-to-square"></i>
                تعديل العقد
            </a>
            <form method="POST" action="{{ route('projects.projects.destroy', $project->id) }}" onsubmit="return confirm('هل أنت متأكد تماماً من رغبتك في حذف هذا المشروع؟ لن يمكن حذفه إذا كان مرتبطاً بمستخلصات معتمدة.');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" style="background: #FEF2F2; border: 1px solid #FCA5A5; color: #DC2626; padding: 9px 14px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Financial & Progress Dashboard Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">القيمة التعاقدية المعدلة</div>
            <div style="font-size: 20px; font-weight: 900; color: var(--text-main);">
                {{ number_format($project->revised_value, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                أصل العقد: {{ number_format($project->contract_value, 2) }} ر.س
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">نسبة الإنجاز الكلية</span>
                <span style="font-size: 18px; font-weight: 900; color: #10B981;">{{ $project->completion_percentage }}%</span>
            </div>
            <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 999px; overflow: hidden; margin-top: 8px;">
                <div style="width: {{ min(100, $project->completion_percentage) }}%; height: 100%; background: #10B981; border-radius: 999px;"></div>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 6px;">
                تاريخ التسليم: {{ $project->expected_end_date?->format('Y-m-d') }}
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #2563EB;">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">إجمالي المفوتر بالمستخلصات</div>
            <div style="font-size: 20px; font-weight: 900; color: #2563EB;">
                {{ number_format($project->total_billed_amount, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: #10B981; font-weight: 700; margin-top: 4px;">
                المحصل: {{ number_format($project->total_paid_amount, 2) }} ر.س
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--accent-gold);">
            <div style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px;">محتجزات الضمان والخصومات</div>
            <div style="font-size: 20px; font-weight: 900; color: var(--accent-gold);">
                {{ number_format($project->retention_amount, 2) }} <small style="font-size: 11px;">ر.س</small>
            </div>
            <div style="font-size: 11px; color: var(--text-muted); margin-top: 4px;">
                نسبة الضمان: {{ $project->retention_percentage }}% | دفعة مقدمة: {{ $project->advance_payment_percentage }}%
            </div>
        </div>
    </div>

    <!-- Interactive Navigation Tabs -->
    <div style="display: flex; gap: 8px; border-bottom: 2px solid var(--border-color); padding-bottom: 2px; overflow-x: auto;">
        @php
            $activeTab = request('tab', 'boq');
        @endphp
        <button type="button" onclick="switchTab('boq')" id="tab-btn-boq" class="tab-nav-btn {{ $activeTab == 'boq' ? 'active-tab' : '' }}">
            <i class="fa-solid fa-list-ol"></i>
            جدول الكميات والمقايسة (BOQ)
            <span style="background: rgba(15, 81, 50, 0.15); color: var(--primary); padding: 2px 7px; border-radius: 999px; font-size: 11px; margin-right: 6px;">{{ $project->boqItems->count() }}</span>
        </button>

        <button type="button" onclick="switchTab('extracts')" id="tab-btn-extracts" class="tab-nav-btn {{ $activeTab == 'extracts' ? 'active-tab' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            المستخلصات الجارية
            <span style="background: rgba(37, 99, 235, 0.15); color: #2563EB; padding: 2px 7px; border-radius: 999px; font-size: 11px; margin-right: 6px;">{{ $project->extracts->count() }}</span>
        </button>

        <button type="button" onclick="switchTab('milestones')" id="tab-btn-milestones" class="tab-nav-btn {{ $activeTab == 'milestones' ? 'active-tab' : '' }}">
            <i class="fa-solid fa-bars-progress"></i>
            المراحل ومؤشرات الإنجاز
            <span style="background: rgba(217, 119, 6, 0.15); color: var(--accent-gold); padding: 2px 7px; border-radius: 999px; font-size: 11px; margin-right: 6px;">{{ $project->milestones->count() }}</span>
        </button>

        <button type="button" onclick="switchTab('change_orders')" id="tab-btn-change_orders" class="tab-nav-btn {{ $activeTab == 'change_orders' ? 'active-tab' : '' }}">
            <i class="fa-solid fa-shuffle"></i>
            أوامر التغيير والملحقات
            <span style="background: rgba(147, 51, 234, 0.15); color: #9333EA; padding: 2px 7px; border-radius: 999px; font-size: 11px; margin-right: 6px;">{{ $project->changeOrders->count() }}</span>
        </button>

        <button type="button" onclick="switchTab('overview')" id="tab-btn-overview" class="tab-nav-btn {{ $activeTab == 'overview' ? 'active-tab' : '' }}">
            <i class="fa-solid fa-circle-info"></i>
            بيانات العقد والموقع
        </button>
    </div>

    <style>
        .tab-nav-btn {
            background: transparent;
            border: none;
            padding: 10px 18px;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        .tab-nav-btn:hover {
            color: var(--primary);
        }
        .tab-nav-btn.active-tab {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 800;
            background: rgba(15, 81, 50, 0.04);
            border-radius: 8px 8px 0 0;
        }
        .tab-content-pane {
            display: none;
        }
        .tab-content-pane.active-pane {
            display: block;
        }
    </style>

    <!-- ================= TAB 1: BOQ ITEMS ================= -->
    <div id="pane-boq" class="tab-content-pane {{ $activeTab == 'boq' ? 'active-pane' : '' }}">
        <div class="erp-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-list-ol" style="color: var(--primary);"></i>
                        جدول الكميات وبنود المقايسة المسعرة (Bill of Quantities)
                    </h3>
                    <span style="font-size: 12px; color: var(--text-muted);">
                        البنود المعتمدة تعاقدياً مع الأسعار الإفرادية والكميات المنفذة المحتسبة بالمستخلصات
                    </span>
                </div>

                <button type="button" onclick="document.getElementById('add-boq-modal').style.display='flex'" class="btn-primary-custom" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 800; border: none; cursor: pointer;">
                    <i class="fa-solid fa-plus-circle"></i>
                    إضافة بند مقايسة جديد
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                            <th style="padding: 12px 16px;">كود البند</th>
                            <th style="padding: 12px 16px;">وصف بند الأعمال</th>
                            <th style="padding: 12px 16px;">الوحدة</th>
                            <th style="padding: 12px 16px;">الكمية التعاقدية</th>
                            <th style="padding: 12px 16px;">السعر الإفرادي</th>
                            <th style="padding: 12px 16px;">إجمالي البند</th>
                            <th style="padding: 12px 16px;">الكمية المنفذة</th>
                            <th style="padding: 12px 16px; min-width: 120px;">نسبة الإنجاز</th>
                            <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->boqItems as $item)
                            <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;">
                                <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                    {{ $item->item_code }}
                                </td>
                                <td style="padding: 14px 16px; font-weight: 700; color: var(--text-main);">
                                    {{ $item->description }}
                                </td>
                                <td style="padding: 14px 16px; color: var(--text-muted); font-weight: 600;">
                                    {{ $item->unit }}
                                </td>
                                <td style="padding: 14px 16px; font-weight: 800;">
                                    {{ number_format($item->revised_quantity, 2) }}
                                    @if($item->contract_quantity != $item->revised_quantity)
                                        <div style="font-size: 10px; color: var(--text-muted);">أصل: {{ number_format($item->contract_quantity, 2) }}</div>
                                    @endif
                                </td>
                                <td style="padding: 14px 16px; font-weight: 800; color: var(--text-main);">
                                    {{ number_format($item->unit_price, 2) }} <small style="font-size: 10px; color: var(--text-muted);">ر.س</small>
                                </td>
                                <td style="padding: 14px 16px; font-weight: 900; color: var(--primary);">
                                    {{ number_format($item->total_price, 2) }} <small style="font-size: 10px; color: var(--text-muted);">ر.س</small>
                                </td>
                                <td style="padding: 14px 16px; font-weight: 800; color: #2563EB;">
                                    {{ number_format($item->executed_quantity, 2) }}
                                </td>
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; justify-content: space-between; font-size: 11px; font-weight: 700; margin-bottom: 3px;">
                                        <span>{{ $item->completion_percentage }}%</span>
                                    </div>
                                    <div style="width: 100%; height: 6px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                        <div style="width: {{ min(100, $item->completion_percentage) }}%; height: 100%; background: {{ $item->completion_percentage >= 100 ? '#10B981' : '#2563EB' }}; border-radius: 999px;"></div>
                                    </div>
                                </td>
                                <td style="padding: 14px 16px; text-align: center;">
                                    <form method="POST" action="{{ route('projects.boq.destroy', $item->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا البند من المقايسة؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="background: transparent; border: none; color: #DC2626; cursor: pointer; padding: 4px;" title="حذف البند">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="padding: 30px; text-align: center; color: var(--text-muted);">
                                    لا توجد بنود مقايسة مضافة بعد. أضف بنود المقايسة لربطها بالمستخلصات ونسب الإنجاز.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($project->boqItems->isNotEmpty())
                        <tfoot>
                            <tr style="background: #F1F5F9; font-weight: 900; border-top: 2px solid var(--border-color);">
                                <td colspan="5" style="padding: 14px 16px; text-align: left;">إجمالي قيمة بنود المقايسة التعاقدية:</td>
                                <td style="padding: 14px 16px; color: var(--primary); font-size: 15px;">
                                    {{ number_format($project->boqItems->sum('total_price'), 2) }} ر.س
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- ================= TAB 2: PROGRESSIVE EXTRACTS ================= -->
    <div id="pane-extracts" class="tab-content-pane {{ $activeTab == 'extracts' ? 'active-pane' : '' }}">
        <div class="erp-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-file-invoice-dollar" style="color: #2563EB;"></i>
                        المستخلصات الجارية والختامية (Progressive Extracts & Billing)
                    </h3>
                    <span style="font-size: 12px; color: var(--text-muted);">
                        سجل المستخلصات المرفوعة والمعتمدة والمحصلة مع خصومات الدفعة المقدمة والضمان
                    </span>
                </div>

                <a href="{{ route('projects.extracts.create', $project->id) }}" class="btn-primary-custom" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 800; text-decoration: none;">
                    <i class="fa-solid fa-plus-circle"></i>
                    إنشاء مستخلص جاري جديد
                </a>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                            <th style="padding: 12px 16px;">رقم المستخلص</th>
                            <th style="padding: 12px 16px;">النوع والفترة</th>
                            <th style="padding: 12px 16px;">أعمال الفترة الحالية</th>
                            <th style="padding: 12px 16px;">الاستقطاعات (مقدمة + ضمان)</th>
                            <th style="padding: 12px 16px;">صافي المستحق (مع ض.ق.م)</th>
                            <th style="padding: 12px 16px;">المسدد</th>
                            <th style="padding: 12px 16px;">الحالة</th>
                            <th style="padding: 12px 16px; text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->extracts as $extract)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                    <a href="{{ route('projects.extracts.show', $extract->id) }}" style="text-decoration: none; color: var(--primary);">
                                        {{ $extract->extract_number }}
                                    </a>
                                    <div style="font-size: 11px; color: var(--text-muted); font-weight: normal;">
                                        تاريخ: {{ $extract->issue_date?->format('Y-m-d') }}
                                    </div>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <div style="font-weight: 700; color: var(--text-main);">{{ $extract->extract_type_label }}</div>
                                    <div style="font-size: 11px; color: var(--text-muted);">
                                        من {{ $extract->period_from?->format('Y-m-d') }} إلى {{ $extract->period_to?->format('Y-m-d') }}
                                    </div>
                                </td>
                                <td style="padding: 14px 16px; font-weight: 800; color: var(--text-main);">
                                    {{ number_format($extract->current_gross_amount, 2) }} <small style="font-size: 10px; color: var(--text-muted);">ر.س</small>
                                </td>
                                <td style="padding: 14px 16px; font-size: 12px; color: #DC2626;">
                                    <div>مقدمة: {{ number_format($extract->advance_payment_deduction, 2) }}</div>
                                    <div>ضمان: {{ number_format($extract->retention_deduction, 2) }}</div>
                                </td>
                                <td style="padding: 14px 16px; font-weight: 900; color: #2563EB;">
                                    {{ number_format($extract->net_amount, 2) }} <small style="font-size: 10px; color: var(--text-muted);">ر.س</small>
                                </td>
                                <td style="padding: 14px 16px; font-weight: 800; color: #10B981;">
                                    {{ number_format($extract->paid_amount, 2) }} <small style="font-size: 10px; color: var(--text-muted);">ر.س</small>
                                </td>
                                <td style="padding: 14px 16px;">
                                    @php
                                        $extStyle = match($extract->status) {
                                            'paid'      => 'background: rgba(16, 185, 129, 0.1); color: #059669;',
                                            'approved'  => 'background: rgba(37, 99, 235, 0.1); color: #2563EB;',
                                            'submitted' => 'background: rgba(217, 119, 6, 0.1); color: #D97706;',
                                            default     => 'background: #F1F5F9; color: #64748B;',
                                        };
                                    @endphp
                                    <span style="display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $extStyle !!}">
                                        {{ $extract->status_label }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px; text-align: center;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('projects.extracts.show', $extract->id) }}" style="width: 30px; height: 30px; border-radius: 6px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; text-decoration: none;" title="عرض المستخلص">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('projects.extracts.print', $extract->id) }}" target="_blank" style="width: 30px; height: 30px; border-radius: 6px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--text-main); display: flex; align-items: center; justify-content: center; text-decoration: none;" title="طباعة شهادة المستخلص">
                                            <i class="fa-solid fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="padding: 30px; text-align: center; color: var(--text-muted);">
                                    لا توجد مستخلصات مسجلة لهذا المشروع حتى الآن.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= TAB 3: MILESTONES ================= -->
    <div id="pane-milestones" class="tab-content-pane {{ $activeTab == 'milestones' ? 'active-pane' : '' }}">
        <div class="erp-card" style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-bars-progress" style="color: var(--accent-gold);"></i>
                        مراحل المشروع والمخرجات التعاقدية (Project Milestones)
                    </h3>
                    <span style="font-size: 12px; color: var(--text-muted);">
                        تقسيم أعمال المشروع إلى مراحل محددة بوزن نسبي ومواعيد تسليم ومخرجات
                    </span>
                </div>

                <button type="button" onclick="document.getElementById('add-milestone-modal').style.display='flex'" class="btn-primary-custom" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 800; border: none; cursor: pointer;">
                    <i class="fa-solid fa-plus-circle"></i>
                    إضافة مرحلة جديدة
                </button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                @forelse($project->milestones as $milestone)
                    <div style="border: 1px solid var(--border-color); border-radius: 12px; padding: 18px; background: #FFFFFF; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <h4 style="font-size: 15px; font-weight: 800; color: var(--text-main); margin: 0;">{{ $milestone->title }}</h4>
                                <span style="font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 6px; background: rgba(15, 81, 50, 0.1); color: var(--primary);">
                                    وزن {{ $milestone->weight_percentage }}%
                                </span>
                            </div>

                            @if($milestone->description)
                                <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">{{ $milestone->description }}</p>
                            @endif

                            <div style="margin-bottom: 12px;">
                                <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; margin-bottom: 4px;">
                                    <span>الإنجاز:</span>
                                    <span>{{ $milestone->completion_percentage }}%</span>
                                </div>
                                <div style="width: 100%; height: 8px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                                    <div style="width: {{ min(100, $milestone->completion_percentage) }}%; height: 100%; background: {{ $milestone->completion_percentage >= 100 ? '#10B981' : 'var(--primary)' }}; border-radius: 999px;"></div>
                                </div>
                            </div>

                            <div style="font-size: 11px; color: var(--text-muted); display: flex; flex-direction: column; gap: 4px;">
                                <span><i class="fa-solid fa-calendar-day"></i> من: {{ $milestone->start_date?->format('Y-m-d') }} إلى {{ $milestone->end_date?->format('Y-m-d') }}</span>
                                @if($milestone->deliverables)
                                    <span><i class="fa-solid fa-box-archive"></i> المخرجات: {{ $milestone->deliverables }}</span>
                                @endif
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; padding-top: 12px; border-top: 1px solid var(--border-color);">
                            <span style="font-size: 11px; font-weight: 700; color: var(--text-main);">
                                الحالة: {{ $milestone->status_label }}
                            </span>
                            <form method="POST" action="{{ route('projects.milestones.destroy', $milestone->id) }}" onsubmit="return confirm('هل تريد حذف هذه المرحلة؟');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: transparent; border: none; color: #DC2626; cursor: pointer; font-size: 12px;" title="حذف">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; padding: 30px; text-align: center; color: var(--text-muted);">
                        لم يتم تسجيل مراحل أو مؤشرات إنجاز للمشروع بعد.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- ================= TAB 4: CHANGE ORDERS ================= -->
    <div id="pane-change_orders" class="tab-content-pane {{ $activeTab == 'change_orders' ? 'active-pane' : '' }}">
        <div class="erp-card" style="padding: 0; overflow: hidden;">
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h3 style="font-size: 16px; font-weight: 800; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-shuffle" style="color: #9333EA;"></i>
                        أوامر التغيير والملحقات التعاقدية (Change Orders & Variations)
                    </h3>
                    <span style="font-size: 12px; color: var(--text-muted);">
                        تتبع التعديلات على قيمة العقد الأصلية والتمديدات الزمنية المعتمدة
                    </span>
                </div>

                <button type="button" onclick="document.getElementById('add-co-modal').style.display='flex'" class="btn-primary-custom" style="padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 800; border: none; cursor: pointer;">
                    <i class="fa-solid fa-plus-circle"></i>
                    طلب أمر تغيير جديد
                </button>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 13px;">
                    <thead>
                        <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-weight: 800;">
                            <th style="padding: 12px 16px;">رقم الأمر</th>
                            <th style="padding: 12px 16px;">بيان وموضوع أمر التغيير</th>
                            <th style="padding: 12px 16px;">تاريخ الطلب</th>
                            <th style="padding: 12px 16px;">الأثر المالي (+ / -)</th>
                            <th style="padding: 12px 16px;">التمديد الزمني</th>
                            <th style="padding: 12px 16px;">الحالة</th>
                            <th style="padding: 12px 16px; text-align: center;">الإجراء والاعتماد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($project->changeOrders as $co)
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 14px 16px; font-weight: 800; font-family: monospace; color: var(--primary);">
                                    {{ $co->order_number }}
                                </td>
                                <td style="padding: 14px 16px;">
                                    <div style="font-weight: 800; color: var(--text-main);">{{ $co->title }}</div>
                                    @if($co->justification)
                                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">
                                            المبررات: {{ $co->justification }}
                                        </div>
                                    @endif
                                </td>
                                <td style="padding: 14px 16px; color: var(--text-muted);">
                                    {{ $co->request_date?->format('Y-m-d') }}
                                </td>
                                <td style="padding: 14px 16px; font-weight: 900; color: {{ $co->amount_change >= 0 ? '#10B981' : '#DC2626' }};">
                                    {{ $co->amount_change >= 0 ? '+' : '' }}{{ number_format($co->amount_change, 2) }} ر.س
                                </td>
                                <td style="padding: 14px 16px; font-weight: 700; color: var(--text-main);">
                                    {{ $co->time_extension_days > 0 ? "+{$co->time_extension_days} يوم" : 'بدون تمديد' }}
                                </td>
                                <td style="padding: 14px 16px;">
                                    @php
                                        $coStyle = match($co->status) {
                                            'approved' => 'background: rgba(16, 185, 129, 0.1); color: #059669;',
                                            'rejected' => 'background: rgba(220, 38, 38, 0.1); color: #DC2626;',
                                            default    => 'background: rgba(217, 119, 6, 0.1); color: #D97706;',
                                        };
                                    @endphp
                                    <span style="display: inline-block; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; {!! $coStyle !!}">
                                        {{ $co->status_label }}
                                    </span>
                                </td>
                                <td style="padding: 14px 16px; text-align: center;">
                                    @if($co->status === 'draft')
                                        <div style="display: inline-flex; gap: 6px;">
                                            <form method="POST" action="{{ route('projects.change-orders.approve', $co->id) }}">
                                                @csrf
                                                <button type="submit" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #059669; border-radius: 6px; padding: 5px 10px; font-size: 11px; font-weight: 800; cursor: pointer;">
                                                    <i class="fa-solid fa-check"></i> اعتماد
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('projects.change-orders.reject', $co->id) }}">
                                                @csrf
                                                <button type="submit" style="background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.3); color: #DC2626; border-radius: 6px; padding: 5px 10px; font-size: 11px; font-weight: 800; cursor: pointer;">
                                                    <i class="fa-solid fa-xmark"></i> رفض
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span style="font-size: 11px; color: var(--text-muted);">
                                            معتمد في {{ $co->approval_date?->format('Y-m-d') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 30px; text-align: center; color: var(--text-muted);">
                                    لا توجد أوامر تغيير مسجلة لهذا المشروع.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= TAB 5: OVERVIEW & CONTRACT INFO ================= -->
    <div id="pane-overview" class="tab-content-pane {{ $activeTab == 'overview' ? 'active-pane' : '' }}">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
            <div class="erp-card" style="padding: 20px;">
                <h4 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-file-contract"></i>
                    بيانات التعاقد والاطراف
                </h4>
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">اسم المشروع:</span> <strong style="color: var(--text-main);">{{ $project->name }}</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">كود المشروع:</span> <strong style="font-family: monospace;">{{ $project->code }}</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">رقم العقد:</span> <strong>{{ $project->contract_number ?? '-' }}</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">العميل:</span> <strong>{{ $project->client?->name ?? '-' }}</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">الفرع المسؤول:</span> <strong>{{ $project->branch?->name ?? '-' }}</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">مركز التكلفة:</span> <strong>{{ $project->costCenter?->name ?? '-' }}</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">المهندس المسؤول:</span> <strong>{{ $project->projectManager?->name ?? '-' }}</strong></div>
                </div>
            </div>

            <div class="erp-card" style="padding: 20px;">
                <h4 style="font-size: 15px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border-color); padding-bottom: 10px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-clock"></i>
                    الجدول الزمني والشروط المالية
                </h4>
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">قيمة العقد الأصلية:</span> <strong>{{ number_format($project->contract_value, 2) }} ر.س</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">القيمة المعدلة بأوامر التغيير:</span> <strong style="color: var(--primary);">{{ number_format($project->revised_value, 2) }} ر.س</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">تاريخ البدء:</span> <strong>{{ $project->start_date?->format('Y-m-d') }}</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">تاريخ الانتهاء المتوقع:</span> <strong>{{ $project->expected_end_date?->format('Y-m-d') }}</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">نسبة الدفعة المقدمة:</span> <strong>{{ $project->advance_payment_percentage }}% ({{ number_format($project->advance_payment_amount, 2) }} ر.س)</strong></div>
                    <div style="display: flex; justify-content: space-between;"><span style="color: var(--text-muted);">نسبة ضمان الأعمال:</span> <strong>{{ $project->retention_percentage }}% ({{ number_format($project->retention_amount, 2) }} ر.س)</strong></div>
                </div>
            </div>

            @if($project->description)
                <div class="erp-card" style="grid-column: 1 / -1; padding: 20px;">
                    <h4 style="font-size: 15px; font-weight: 800; color: var(--primary); margin-bottom: 10px;">نطاق العمل وملاحظات المشروع</h4>
                    <p style="font-size: 13px; color: var(--text-main); line-height: 1.8; margin: 0;">
                        {{ $project->description }}
                    </p>
                </div>
            @endif
        </div>
    </div>

</div>

<!-- ================= MODALS ================= -->

<!-- Modal: Add BOQ Item -->
<div id="add-boq-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 12px; max-width: 550px; width: 100%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0;">إضافة بند مقايسة جديد (BOQ)</h3>
            <button type="button" onclick="document.getElementById('add-boq-modal').style.display='none'" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('projects.boq.store', $project->id) }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">كود البند (اختياري)</label>
                    <input type="text" name="item_code" placeholder="مثال: BOQ-01" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">وصف بند الأعمال <span style="color: #DC2626;">*</span></label>
                    <input type="text" name="description" required placeholder="مثال: توريد وصب خرسانة مسلحة للقواعد والأساسات عيار 350" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الوحدة <span style="color: #DC2626;">*</span></label>
                        <input type="text" name="unit" required placeholder="مثال: م3 أو م2 أو طن" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>

                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الكمية التعاقدية <span style="color: #DC2626;">*</span></label>
                        <input type="number" step="0.001" name="contract_quantity" required placeholder="0.00" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">سعر الوحدة الإفرادي (ر.س) <span style="color: #DC2626;">*</span></label>
                    <input type="number" step="0.01" name="unit_price" required placeholder="0.00" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('add-boq-modal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">حفظ البند</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Milestone -->
<div id="add-milestone-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 12px; max-width: 550px; width: 100%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0;">إضافة مرحلة إنشائية جديدة</h3>
            <button type="button" onclick="document.getElementById('add-milestone-modal').style.display='none'" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('projects.milestones.store', $project->id) }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">عنوان المرحلة <span style="color: #DC2626;">*</span></label>
                    <input type="text" name="title" required placeholder="مثال: الانتهاء من أعمال الهيكل الخرساني والعظم" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">تاريخ البدء <span style="color: #DC2626;">*</span></label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">تاريخ الانتهاء <span style="color: #DC2626;">*</span></label>
                        <input type="date" name="end_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الوزن النسبي (%)</label>
                        <input type="number" step="0.1" name="weight_percentage" value="25" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الحالة <span style="color: #DC2626;">*</span></label>
                        <select name="status" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                            <option value="pending">قيد الانتظار</option>
                            <option value="in_progress">جاري العمل</option>
                            <option value="completed">مكتملة ومسلّمة</option>
                            <option value="delayed">متأخرة عن الجدول</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">المخرجات ومحاضر الاستلام</label>
                    <input type="text" name="deliverables" placeholder="مثال: محضر صب وسحب عينات اختبار مقاومة الضغط" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('add-milestone-modal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">حفظ المرحلة</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Change Order -->
<div id="add-co-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #FFFFFF; border-radius: 12px; max-width: 550px; width: 100%; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 10px;">
            <h3 style="font-size: 16px; font-weight: 800; color: var(--primary); margin: 0;">تسجيل طلب أمر تغيير (Change Order)</h3>
            <button type="button" onclick="document.getElementById('add-co-modal').style.display='none'" style="background: none; border: none; font-size: 18px; color: var(--text-muted); cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('projects.change-orders.store', $project->id) }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">موضوع وعنوان أمر التغيير <span style="color: #DC2626;">*</span></label>
                    <input type="text" name="title" required placeholder="مثال: إضافة بند تشطيب رخام إضافي للبهو الرئيسي" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">تاريخ الطلب <span style="color: #DC2626;">*</span></label>
                        <input type="date" name="request_date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">التمديد الزمني (بالأيام)</label>
                        <input type="number" name="time_extension_days" value="0" min="0" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                    </div>
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">الأثر المالي (+ زيادة أو - تخفيض) بالريال <span style="color: #DC2626;">*</span></label>
                    <input type="number" step="0.01" name="amount_change" required placeholder="0.00" style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;">
                </div>

                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 4px; display: block;">المبررات الفنية والتعاقدية</label>
                    <textarea name="justification" rows="2" placeholder="بناءً على طلب المالك أو تقرير الاستشاري المشرف..." style="width: 100%; padding: 8px 12px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 13px;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('add-co-modal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 800; border: none; cursor: pointer;">تسجيل أمر التغيير</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content-pane').forEach(el => el.classList.remove('active-pane'));
    document.querySelectorAll('.tab-nav-btn').forEach(el => el.classList.remove('active-tab'));

    const targetPane = document.getElementById('pane-' + tabId);
    const targetBtn = document.getElementById('tab-btn-' + tabId);

    if (targetPane) targetPane.classList.add('active-pane');
    if (targetBtn) targetBtn.classList.add('active-tab');

    // Update URL hash without scroll
    history.replaceState(null, null, '?tab=' + tabId);
}
</script>
@endsection
