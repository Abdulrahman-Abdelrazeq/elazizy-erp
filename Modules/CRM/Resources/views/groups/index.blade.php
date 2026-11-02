@extends('layouts.app')

@section('title', 'شرائح تصنيف العملاء والأسعار')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-layer-group" style="color: var(--primary); margin-left: 8px;"></i>
                شرائح وتصنيفات العملاء (Customer Groups & Pricing Tiers)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تحديد فئات العملاء (جملة، نصف جملة، تجزئة، VIP) ونسب الخصم التلقائية المطبقة في فواتير المبيعات
            </span>
        </div>

        <button onclick="document.getElementById('addGroupModal').style.display='flex'" class="btn-primary-custom">
            <i class="fa-solid fa-plus-circle"></i>
            إضافة شريحة جديدة
        </button>
    </div>

    <!-- Info Banner -->
    <div style="background: var(--primary-light); border-right: 4px solid var(--primary); padding: 14px 20px; border-radius: 12px; display: flex; align-items: center; gap: 14px;">
        <i class="fa-solid fa-circle-info" style="font-size: 24px; color: var(--primary);"></i>
        <div style="font-size: 13px; color: var(--text-main); line-height: 1.6;">
            <strong>نظام التسعير التلقائي:</strong> عند اختيار شريحة للعميل (مثل "عملاء الجملة")، سيقوم نظام نقاط البيع والفواتير تلقائياً بتطبيق سعر الجملة ونسبة الخصم المحددة على أصناف الفاتورة عند اختيار هذا العميل.
        </div>
    </div>

    <!-- Groups Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 18px;">
        @forelse($groups as $group)
            <div class="erp-card" style="padding: 22px; display: flex; flex-direction: column; justify-content: space-between; gap: 16px; border: 1.5px solid var(--border-color); position: relative;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <div>
                            <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin-bottom: 4px;">
                                {{ $group->name }}
                            </h3>
                            <span style="font-size: 12px; color: var(--text-muted);">
                                كود المجموعة: #GRP-{{ $group->id }}
                            </span>
                        </div>

                        <div>
                            @if($group->price_tier === 'wholesale')
                                <span class="badge" style="background: #E0E7FF; color: #4338CA; font-weight: 700; font-size: 12px;">
                                    سعر الجملة
                                </span>
                            @elseif($group->price_tier === 'half_wholesale')
                                <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700; font-size: 12px;">
                                    سعر نصف الجملة
                                </span>
                            @else
                                <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700; font-size: 12px;">
                                    سعر التجزئة
                                </span>
                            @endif
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; background: #F8FAFC; padding: 12px; border-radius: 10px; border: 1px solid var(--border-color); margin-bottom: 14px;">
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); font-weight: 700; display: block;">نسبة الخصم الإضافي:</span>
                            <strong style="font-size: 16px; color: var(--accent-gold); font-weight: 900;">
                                {{ $group->discount_percentage }}%
                            </strong>
                        </div>
                        <div>
                            <span style="font-size: 11px; color: var(--text-muted); font-weight: 700; display: block;">العملاء المنضمين:</span>
                            <strong style="font-size: 16px; color: var(--text-main); font-weight: 900;">
                                {{ $group->clients_count }} عميل
                            </strong>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border-color); padding-top: 12px;">
                    <a href="{{ route('crm.customers.index') }}?group_id={{ $group->id }}" style="font-size: 12.5px; font-weight: 700; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 4px;">
                        <i class="fa-solid fa-users"></i> عرض العملاء ({{ $group->clients_count }})
                    </a>

                    <div style="display: flex; gap: 6px;">
                        <button onclick="editGroup({{ $group->id }}, '{{ addslashes($group->name) }}', '{{ $group->price_tier }}', {{ $group->discount_percentage }}, {{ $group->is_active ? 1 : 0 }})" style="background: #FEF3C7; color: #D97706; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;" title="تعديل الشريحة">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>

                        <form action="{{ route('crm.groups.destroy', $group->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الشريحة؟');" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: #FEE2E2; color: #DC2626; border: none; width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 13px;" title="حذف">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="erp-card" style="grid-column: 1 / -1; padding: 40px; text-align: center; color: var(--text-muted);">
                <i class="fa-solid fa-layer-group" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                لم يتم تعريف أي شرائح للعملاء بعد. قم بإضافة أول شريحة (مثل: عملاء الجملة، قطاعي، VIP).
            </div>
        @endforelse
    </div>

</div>

<!-- Modal: Add Group -->
<div id="addGroupModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> إضافة شريحة تصنيف جديدة
            </h3>
            <button onclick="document.getElementById('addGroupModal').style.display='none'" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('crm.groups.store') }}" method="POST">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">اسم الشريحة <span style="color: #DC2626;">*</span></label>
                    <input type="text" name="name" required placeholder="مثال: تجار الجملة، عملاء الـ VIP، موزعين المحافظات" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">مستوى السعر المعتمد <span style="color: #DC2626;">*</span></label>
                    <select name="price_tier" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                        <option value="retail">سعر التجزئة (العادي)</option>
                        <option value="wholesale">سعر الجملة</option>
                        <option value="half_wholesale">سعر نصف الجملة</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">نسبة خصم إضافية تلقائية (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="discount_percentage" value="0.00" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                    <small style="font-size: 11.5px; color: var(--text-muted); display: block; margin-top: 4px;">يتم خصم هذه النسبة تلقائياً من إجمالي الفاتورة لعملاء هذه الشريحة.</small>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('addGroupModal').style.display='none'" style="background: #E2E8F0; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; color: #475569; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 24px;">حفظ الشريحة</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Group -->
<div id="editGroupModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1050; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #fff; width: 100%; max-width: 500px; border-radius: 16px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            <h3 style="font-size: 17px; font-weight: 900; color: var(--text-main); margin: 0;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--accent-gold);"></i> تعديل شريحة العملاء
            </h3>
            <button onclick="document.getElementById('editGroupModal').style.display='none'" style="background: none; border: none; font-size: 18px; color: #94A3B8; cursor: pointer;">&times;</button>
        </div>

        <form id="editGroupForm" method="POST">
            @csrf
            @method('PUT')
            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">اسم الشريحة <span style="color: #DC2626;">*</span></label>
                    <input type="text" id="edit_name" name="name" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">مستوى السعر المعتمد <span style="color: #DC2626;">*</span></label>
                    <select id="edit_price_tier" name="price_tier" required style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px; font-weight: 700;">
                        <option value="retail">سعر التجزئة (العادي)</option>
                        <option value="wholesale">سعر الجملة</option>
                        <option value="half_wholesale">سعر نصف الجملة</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 6px;">نسبة خصم إضافية تلقائية (%)</label>
                    <input type="number" step="0.01" min="0" max="100" id="edit_discount_percentage" name="discount_percentage" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                    <button type="button" onclick="document.getElementById('editGroupModal').style.display='none'" style="background: #E2E8F0; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; color: #475569; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom" style="padding: 10px 24px;">تحديث التغييرات</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function editGroup(id, name, tier, discount, isActive) {
    document.getElementById('editGroupForm').action = "{{ url('crm/groups') }}/" + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_price_tier').value = tier;
    document.getElementById('edit_discount_percentage').value = discount;
    document.getElementById('editGroupModal').style.display = 'flex';
}
</script>
@endsection

