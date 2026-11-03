@extends('layouts.app')

@section('title', 'مراكز التكلفة (Cost Centers)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-bullseye"></i>
                </span>
                مراكز التكلفة (Cost Centers)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                توزيع وتحميل المصروفات والإيرادات على الأقسام، المشاريع، وخطوط الإنتاج لقياس الربحية
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('accounting.accounts.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-sitemap" style="color: var(--accent-gold);"></i>
                شجرة الحسابات
            </a>
            <button type="button" onclick="openCreateCostCenterModal()" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-plus-circle"></i>
                إضافة مركز تكلفة جديد
            </button>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-bullseye"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي مراكز التكلفة</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $stats['total'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">مراكز نشطة ومفعلة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $stats['active'] }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">المراكز الرئيسية (الأب)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $parents->count() }}</strong>
            </div>
        </div>
    </div>

    <!-- Cost Centers Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">الكود (Code)</th>
                        <th>اسم مركز التكلفة</th>
                        <th>المركز الأب (التصنيف)</th>
                        <th>الملاحظات</th>
                        <th>الحالة</th>
                        <th style="text-align: center; width: 120px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($costCenters as $cc)
                        <tr>
                            <td>
                                <span class="badge" style="background: #F1F5F9; color: var(--text-main); font-weight: 800; font-family: monospace;">
                                    {{ $cc->code }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 14px; color: var(--text-main);">{{ $cc->name }}</strong>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--text-muted);">
                                    {{ $cc->parent->name ?? 'مركز رئيسي مستقل' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: var(--text-muted);">{{ $cc->notes ?: '—' }}</span>
                            </td>
                            <td>
                                @if($cc->is_active)
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 700;">نشط</span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">معطل</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button type="button" 
                                            onclick="openEditCostCenterModal({{ json_encode($cc) }})"
                                            title="تعديل المركز"
                                            style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('accounting.cost_centers.destroy', $cc->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا المركز؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="حذف"
                                                style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                                <i class="fa-solid fa-bullseye" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                <span style="font-size: 15px; font-weight: 700; display: block;">لا توجد مراكز تكلفة مسجلة</span>
                                <span style="font-size: 13px;">قم بإضافة مراكز تكلفة لتوزيع أعباء المصروفات بدقة</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($costCenters->hasPages())
            <div style="padding: 16px; border-top: 1px solid var(--border-color);">
                {{ $costCenters->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Modal: Create Cost Center -->
<div id="createCostCenterModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-plus-circle" style="color: var(--accent-gold);"></i>
                إضافة مركز تكلفة جديد
            </h4>
            <button type="button" onclick="closeCreateCostCenterModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('accounting.cost_centers.store') }}" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div style="display: grid; grid-template-columns: 140px 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        كود المركز <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="code" class="erp-input" placeholder="CC-01" required>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم مركز التكلفة <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" class="erp-input" placeholder="مثال: قسم التسويق، فرع المعادي" required>
                </div>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    المركز الأب (اختياري)
                </label>
                <select name="parent_id" class="erp-input">
                    <option value="">-- مركز تكلفة رئيسي --</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    ملاحظات
                </label>
                <textarea name="notes" rows="2" class="erp-input" placeholder="أي تفاصيل حول هذا المركز..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeCreateCostCenterModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-weight: 800;">
                    <i class="fa-solid fa-check-circle"></i>
                    حفظ المركز
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Cost Center -->
<div id="editCostCenterModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--accent-gold);"></i>
                تعديل بيانات مركز التكلفة
            </h4>
            <button type="button" onclick="closeEditCostCenterModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form id="editCostCenterForm" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 140px 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        كود المركز
                    </label>
                    <input type="text" id="edit_cc_code" class="erp-input" readonly style="background: #F1F5F9; color: var(--text-muted);">
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم مركز التكلفة <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" id="edit_cc_name" class="erp-input" required>
                </div>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    المركز الأب
                </label>
                <select name="parent_id" id="edit_cc_parent_id" class="erp-input">
                    <option value="">-- مركز تكلفة رئيسي --</option>
                    @foreach($parents as $p)
                        <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; align-items: center; gap: 8px;">
                <input type="checkbox" name="is_active" id="edit_cc_is_active" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                <label for="edit_cc_is_active" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    المركز مفعل ونشط
                </label>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    ملاحظات
                </label>
                <textarea name="notes" id="edit_cc_notes" rows="2" class="erp-input"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeEditCostCenterModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-weight: 800;">
                    <i class="fa-solid fa-save"></i>
                    حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateCostCenterModal() {
        document.getElementById('createCostCenterModal').style.display = 'flex';
    }
    function closeCreateCostCenterModal() {
        document.getElementById('createCostCenterModal').style.display = 'none';
    }

    function openEditCostCenterModal(cc) {
        const form = document.getElementById('editCostCenterForm');
        form.action = "{{ url('accounting/cost-centers') }}/" + cc.id;
        document.getElementById('edit_cc_code').value = cc.code;
        document.getElementById('edit_cc_name').value = cc.name;
        document.getElementById('edit_cc_parent_id').value = cc.parent_id || '';
        document.getElementById('edit_cc_notes').value = cc.notes || '';
        document.getElementById('edit_cc_is_active').checked = cc.is_active ? true : false;
        document.getElementById('editCostCenterModal').style.display = 'flex';
    }
    function closeEditCostCenterModal() {
        document.getElementById('editCostCenterModal').style.display = 'none';
    }
</script>
@endsection
