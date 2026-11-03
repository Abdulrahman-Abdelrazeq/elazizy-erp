@extends('layouts.app')

@section('title', 'محطات العمل وخطوط الإنتاج (Workstations & Lines)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-gears"></i>
                </span>
                محطات العمل وخطوط الإنتاج (Workstations)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدارة خطوط التشغيل الصناعية، ومعدل تكلفة الساعة، والقدرات التشغيلية للمصنع
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('manufacturing.boms.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-layer-group" style="color: var(--primary);"></i>
                قوائم المواد (BOM)
            </a>
            <a href="{{ route('manufacturing.orders.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-clipboard-check" style="color: var(--accent-gold);"></i>
                أوامر التصنيع
            </a>
            <button type="button" onclick="document.getElementById('createModal').style.display='flex'" class="btn-primary-custom" style="padding: 10px 18px; border-radius: 10px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة محطة عمل جديدة
            </button>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid var(--primary); display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-industry"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي محطات العمل</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total'] }} محطة</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #10B981; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">محطات نشطة وجاهزة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['active'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #4338CA; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي الطاقة اليومية</span>
                <strong style="font-size: 19px; font-weight: 900; color: #4338CA;">{{ number_format($stats['total_hours'], 1) }} ساعة/يوم</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; border-top: 4px solid #D97706; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">متوسط تكلفة الساعة التشغيلية</span>
                <strong style="font-size: 20px; font-weight: 900; color: #D97706;">{{ formatMoney($stats['avg_cost']) }}</strong>
            </div>
        </div>
    </div>

    <!-- Workstations Table Card -->
    <div class="erp-card" style="padding: 24px; margin-bottom: 0;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13px;">
                <thead>
                    <tr style="background: #F8FAFC; color: var(--text-muted); font-weight: 800;">
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 110px;">الكود</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color);">اسم محطة العمل / خط الإنتاج</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 140px;">الفرع</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">تكلفة الساعة</th>
                        <th style="padding: 12px; text-align: right; border-bottom: 2px solid var(--border-color); width: 130px;">الطاقة اليومية</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 100px;">الحالة</th>
                        <th style="padding: 12px; text-align: center; border-bottom: 2px solid var(--border-color); width: 110px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($workstations as $ws)
                    <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFC'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-family: monospace; font-weight: 800; color: var(--primary);">
                            {{ $ws->code }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: var(--text-main);">
                            {{ $ws->name }}
                            @if($ws->description)
                                <span style="display: block; font-size: 11px; font-weight: 400; color: var(--text-muted); margin-top: 2px;">{{ $ws->description }}</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); color: var(--text-muted); font-size: 12.5px;">
                            <i class="fa-solid fa-code-branch" style="color: var(--accent-gold); margin-left: 4px;"></i>
                            {{ $ws->branch?->name ?? 'المركز الرئيسي' }}
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 800; color: #0F5132;">
                            {{ formatMoney($ws->hourly_cost) }} / س
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); font-weight: 700; color: var(--text-main);">
                            {{ $ws->capacity_per_day }} ساعة
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            @if($ws->is_active)
                                <span style="background: #D1E7DD; color: #0F5132; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">نشط</span>
                            @else
                                <span style="background: #FEE2E2; color: #DC2626; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 800;">معطل</span>
                            @endif
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid var(--border-color); text-align: center;">
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <button type="button" onclick="editWorkstation({{ json_encode($ws) }})" style="width: 32px; height: 32px; border-radius: 8px; background: #FEF3C7; color: #D97706; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="تعديل">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form method="POST" action="{{ route('manufacturing.workstations.destroy', $ws->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف محطة العمل هذه؟')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="width: 32px; height: 32px; border-radius: 8px; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; display: inline-flex; align-items: center; justify-content: center;" title="حذف">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: var(--text-muted);">
                            <i class="fa-solid fa-industry" style="font-size: 36px; display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                            لا توجد محطات عمل أو خطوط إنتاج مسجلة حالياً.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($workstations->hasPages())
            <div style="margin-top: 20px;">
                {{ $workstations->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Create Workstation -->
<div id="createModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: modalFadeIn 0.2s ease;">
        <div style="background: linear-gradient(135deg, var(--primary) 0%, #062E20 100%); padding: 18px 24px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-industry"></i>
                </span>
                <h3 style="margin: 0; font-size: 16px; font-weight: 800;">إضافة محطة عمل / خط إنتاج جديد</h3>
            </div>
            <button type="button" onclick="document.getElementById('createModal').style.display='none'" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('manufacturing.workstations.store') }}" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اسم المحطة / الخط *</label>
                    <input type="text" name="name" class="erp-input" placeholder="مثال: خط التجميع الآلي 1" required>
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الكود</label>
                    <input type="text" name="code" class="erp-input" placeholder="WS-001">
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الفرع</label>
                <select name="branch_id" class="erp-input">
                    <option value="">المركز الرئيسي / عام</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">معدل تكلفة الساعة (SAR) *</label>
                    <input type="number" step="0.01" min="0" name="hourly_cost" class="erp-input" value="50.00" required>
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">القدرة اليومية (ساعات) *</label>
                    <input type="number" step="0.5" min="1" max="24" name="capacity_per_day" class="erp-input" value="8.0" required>
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الوصف / ملاحظات</label>
                <textarea name="description" class="erp-input" rows="2" placeholder="أية تفاصيل أو مواصفات خاصة بالمحطة..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('createModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--text-muted);">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                    <i class="fa-solid fa-check"></i> حفظ المحطة
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Workstation -->
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: modalFadeIn 0.2s ease;">
        <div style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); padding: 18px 24px; color: #fff; display: flex; justify-content: space-between; align-items: center;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="width: 36px; height: 36px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fa-solid fa-pen-to-square"></i>
                </span>
                <h3 style="margin: 0; font-size: 16px; font-weight: 800;">تعديل محطة العمل</h3>
            </div>
            <button type="button" onclick="document.getElementById('editModal').style.display='none'" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <form id="editForm" method="POST" action="" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">اسم المحطة / الخط *</label>
                    <input type="text" id="edit_name" name="name" class="erp-input" required>
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الكود</label>
                    <input type="text" id="edit_code" name="code" class="erp-input">
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الفرع</label>
                <select id="edit_branch_id" name="branch_id" class="erp-input">
                    <option value="">المركز الرئيسي / عام</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">معدل تكلفة الساعة (SAR) *</label>
                    <input type="number" step="0.01" min="0" id="edit_hourly_cost" name="hourly_cost" class="erp-input" required>
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">القدرة اليومية (ساعات) *</label>
                    <input type="number" step="0.5" min="1" max="24" id="edit_capacity_per_day" name="capacity_per_day" class="erp-input" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; align-items: center;">
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الوصف</label>
                    <input type="text" id="edit_description" name="description" class="erp-input">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block; margin-bottom: 6px;">الحالة</label>
                    <select id="edit_is_active" name="is_active" class="erp-input">
                        <option value="1">نشط</option>
                        <option value="0">معطل</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; color: var(--text-muted);">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 22px; border-radius: 8px; font-size: 13px; font-weight: 800; cursor: pointer; border: none;">
                    <i class="fa-solid fa-save"></i> حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editWorkstation(ws) {
    document.getElementById('editForm').action = '/manufacturing/workstations/' + ws.id;
    document.getElementById('edit_name').value = ws.name;
    document.getElementById('edit_code').value = ws.code || '';
    document.getElementById('edit_branch_id').value = ws.branch_id || '';
    document.getElementById('edit_hourly_cost').value = ws.hourly_cost;
    document.getElementById('edit_capacity_per_day').value = ws.capacity_per_day;
    document.getElementById('edit_description').value = ws.description || '';
    document.getElementById('edit_is_active').value = ws.is_active ? '1' : '0';
    document.getElementById('editModal').style.display = 'flex';
}
</script>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection
