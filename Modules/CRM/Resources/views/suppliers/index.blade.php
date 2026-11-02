@extends('layouts.app')

@section('title', 'دليل وسجل الموردين')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-truck-field" style="color: var(--primary); margin-left: 8px;"></i>
                إدارة وسجل الموردين (Suppliers Management)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                دليل الموردين، متابعة المستحقات والأرصدة الدائنة، وكشوفات الحسابات التفصيلية
            </span>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <a href="{{ route('crm.suppliers.create') }}" class="btn-primary-custom">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة مورد جديد
            </a>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-truck-field"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الموردين المسجلين</span>
                <strong style="font-size: 18px; font-weight: 900; color: var(--text-main);">{{ $stats['total_suppliers'] }} مورد</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مستحقات الموردين (دائن - علينا)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #DC2626;">{{ formatMoney($stats['total_payable']) }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-dollar-to-slot"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">دفعات مقدمة للموردين (مدين - لنا)</span>
                <strong style="font-size: 18px; font-weight: 900; color: #0F5132;">{{ formatMoney($stats['total_receivable']) }}</strong>
            </div>
        </div>
    </div>

    <!-- Search Toolbar -->
    <div class="erp-card" style="padding: 18px; margin-bottom: 0;">
        <form action="{{ route('crm.suppliers.index') }}" method="GET" style="display: flex; gap: 14px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 280px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث باسم المورد، كود المورد، الهاتف، أو الرقم الضريبي..." style="width: 100%; padding: 10px 38px 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                <i class="fa-solid fa-search" style="position: absolute; right: 14px; top: 13px; color: #94A3B8;"></i>
            </div>

            <button type="submit" class="btn-primary-custom" style="padding: 10px 24px;">
                <i class="fa-solid fa-filter"></i> تصفية
            </button>

            @if(request()->filled('search'))
                <a href="{{ route('crm.suppliers.index') }}" style="background: #E2E8F0; padding: 10px 18px; border-radius: 10px; text-decoration: none; color: #475569; font-weight: 700; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-times"></i> إلغاء الفلتر
                </a>
            @endif
        </form>
    </div>

    <!-- Suppliers Table Card -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th>كود المورد</th>
                        <th>اسم المورد / الشركة</th>
                        <th>بيانات الاتصال</th>
                        <th>المنطقة / العنوان</th>
                        <th>الرقم الضريبي</th>
                        <th>الرصيد المالي الحالي</th>
                        <th>الحالة</th>
                        <th style="text-align: center;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                        <tr>
                            <td>
                                <span style="font-weight: 800; color: var(--primary); font-family: monospace; font-size: 13px;">
                                    {{ $supplier->code }}
                                </span>
                            </td>
                            <td>
                                <div>
                                    <strong style="font-size: 14.5px; color: var(--text-main); display: block;">{{ $supplier->name }}</strong>
                                    @if($supplier->company_name)
                                        <small style="color: var(--text-muted); font-size: 12px;">{{ $supplier->company_name }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px; font-size: 12.5px;">
                                    @if($supplier->mobile)
                                        <span><i class="fa-brands fa-whatsapp" style="color: #22C55E; margin-left: 4px;"></i> {{ $supplier->mobile }}</span>
                                    @endif
                                    @if($supplier->phone)
                                        <span style="color: var(--text-muted);"><i class="fa-solid fa-phone" style="margin-left: 4px;"></i> {{ $supplier->phone }}</span>
                                    @endif
                                    @if(!$supplier->mobile && !$supplier->phone)
                                        <span style="color: #94A3B8;">لا يوجد هاتف</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-size: 13px; color: var(--text-main);">
                                    {{ $supplier->location ? $supplier->location->name : ($supplier->address ?: '-') }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12.5px; font-family: monospace; color: #475569;">
                                    {{ $supplier->tax_number ?: '-' }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 900; font-size: 14px; color: {{ $supplier->current_balance < 0 ? '#DC2626' : ($supplier->current_balance > 0 ? '#0F5132' : '#64748B') }};">
                                    {{ formatMoney(abs($supplier->current_balance)) }}
                                    <small style="font-size: 11px; font-weight: 700; display: block;">
                                        @if($supplier->current_balance < 0)
                                            (دائن - له مستحقات)
                                        @elseif($supplier->current_balance > 0)
                                            (مدين - دفعات مقدمة له)
                                        @else
                                            (رصيد خالص 0.00)
                                        @endif
                                    </small>
                                </div>
                            </td>
                            <td>
                                @if($supplier->is_active)
                                    <span class="badge badge-success">نشط</span>
                                @else
                                    <span class="badge badge-danger">موقف</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; justify-content: center; gap: 6px;">
                                    <a href="{{ route('crm.suppliers.statement', $supplier->id) }}" title="كشف الحساب" style="background: var(--primary-light); color: var(--primary); width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 13px;">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </a>
                                    <a href="{{ route('crm.suppliers.edit', $supplier->id) }}" title="تعديل" style="background: #FEF3C7; color: #D97706; width: 32px; height: 32px; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; font-size: 13px;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('crm.suppliers.destroy', $supplier->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="حذف" style="background: #FEE2E2; color: #DC2626; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                <i class="fa-solid fa-truck-field" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                                لا يوجد موردين مسجلين بعد في النظام
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($suppliers->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border-color);">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
