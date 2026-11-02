@extends('layouts.app')

@section('title', 'دليل وحدات القياس')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-scale-balanced" style="color: var(--primary); margin-left: 8px;"></i>
                دليل وحدات القياس (Units of Measurement)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تعريف الوحدات الأساسية للأصناف (قطعة، كرتونة، كيلوجرام، درزن، لتر...)
            </span>
        </div>

        <button type="button" onclick="document.getElementById('unitModal').style.display='flex'" class="btn-primary-custom">
            <i class="fa-solid fa-plus"></i>
            إضافة وحدة قياس جديدة
        </button>
    </div>

    <!-- Units Table Card -->
    <div class="erp-card">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: right; font-size: 14px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 2px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                        <th style="padding: 14px 18px;">اسم وحدة القياس</th>
                        <th style="padding: 14px 18px;">الكود المختصر</th>
                        <th style="padding: 14px 18px;">عدد الأصناف المرتبطة</th>
                        <th style="padding: 14px 18px;">الحالة</th>
                        <th style="padding: 14px 18px; text-align: center;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 14px 18px; font-weight: 800; color: var(--text-main);">
                                <i class="fa-solid fa-cube" style="color: var(--primary); margin-left: 8px;"></i>
                                {{ $unit->name }}
                            </td>
                            <td style="padding: 14px 18px; font-family: monospace; font-weight: 700; color: #4338CA;">
                                {{ $unit->code ?? '-' }}
                            </td>
                            <td style="padding: 14px 18px; font-weight: 700; color: var(--primary);">
                                {{ $unit->items_count }} صنف
                            </td>
                            <td style="padding: 14px 18px;">
                                <span style="background: #D1E7DD; color: #0F5132; padding: 3px 10px; border-radius: 12px; font-size: 11.5px; font-weight: 700;">
                                    نشط
                                </span>
                            </td>
                            <td style="padding: 14px 18px; text-align: center;">
                                <form action="{{ route('inventory.units.destroy', $unit->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الوحدة؟')" style="margin: 0; display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #FEE2E2; border: none; color: #DC2626; width: 32px; height: 32px; border-radius: 8px; cursor: pointer;" title="حذف">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="padding: 36px; text-align: center; color: var(--text-muted);">
                                لا توجد وحدات قياس مسجلة حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Create Unit Modal -->
<div id="unitModal" class="theme-modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 800; font-size: 18px; color: var(--text-main);">
                <i class="fa-solid fa-scale-balanced" style="color: var(--primary); margin-left: 8px;"></i>
                إضافة وحدة قياس جديدة
            </h3>
            <button type="button" onclick="document.getElementById('unitModal').style.display='none'" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ route('inventory.units.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">اسم وحدة القياس *</label>
                <input type="text" name="name" required placeholder="مثال: قطعة / كرتونة / كجم / درزن" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; font-size: 13.5px; margin-bottom: 6px;">الكود المختصر (رمز الوحدة)</label>
                <input type="text" name="code" placeholder="مثال: PCS / CTN / KG" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; text-transform: uppercase;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('unitModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">إلغاء</button>
                <button type="submit" class="btn-primary-custom">إضافة الوحدة</button>
            </div>
        </form>
    </div>
</div>
@endsection

