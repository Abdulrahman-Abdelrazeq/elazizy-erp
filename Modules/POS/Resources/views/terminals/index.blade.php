@extends('layouts.app')

@section('title', 'أجهزة ونقاط البيع (POS Terminals)')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="display: inline-flex; width: 40px; height: 40px; border-radius: 12px; background: rgba(15, 81, 50, 0.1); color: var(--primary); align-items: center; justify-content: center; font-size: 20px;">
                    <i class="fa-solid fa-server"></i>
                </span>
                أجهزة ونقاط البيع والكاشير (POS Terminals & Hardware)
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                إدارة شاشات ونقاط بيع الفروع والمخازن المرتبطة، تعيين الطابعات الحرارية، والعملاء الافتراضيين
            </span>
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('pos.screen') }}" style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 10px 18px; border-radius: 10px; text-decoration: none; font-weight: 800; font-size: 13px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(15, 81, 50, 0.25);">
                <i class="fa-solid fa-desktop"></i>
                دخول شاشة الكاشير السريعة
            </a>
            <a href="{{ route('pos.shifts.index') }}" style="background: #F8FAFC; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-cash-register" style="color: var(--accent-gold);"></i>
                ورديات الكاشير وجرد الدرج
            </a>
            <button type="button" onclick="openCreateTerminalModal()" class="btn-primary-custom" style="padding: 9px 16px; border-radius: 10px; font-size: 13px; font-weight: 800;">
                <i class="fa-solid fa-plus-circle"></i>
                تعريف جهاز نقطة بيع جديد
            </button>
        </div>
    </div>

    <!-- Stats Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-desktop"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">إجمالي أجهزة الكاشير</span>
                <strong style="font-size: 20px; font-weight: 900; color: var(--primary);">{{ $terminals->count() }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #D1E7DD; color: #0F5132; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-check-circle"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أجهزة مفعلة ونشطة</span>
                <strong style="font-size: 20px; font-weight: 900; color: #0F5132;">{{ $terminals->where('is_active', true)->count() }}</strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.15); color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">أجهزة قيد العمل (وردية نشطة)</span>
                <strong style="font-size: 20px; font-weight: 900; color: #10B981;">
                    {{ $terminals->filter(fn($t) => $t->shifts->isNotEmpty())->count() }}
                </strong>
            </div>
        </div>

        <div class="erp-card" style="margin-bottom: 0; padding: 18px; display: flex; align-items: center; gap: 14px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-warehouse"></i>
            </div>
            <div>
                <span style="font-size: 11.5px; font-weight: 700; color: var(--text-muted); display: block;">المستودعات المغذية</span>
                <strong style="font-size: 20px; font-weight: 900; color: #4338CA;">{{ $stores->count() }}</strong>
            </div>
        </div>
    </div>

    <!-- Terminals Table -->
    <div class="erp-card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="erp-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">كود الجهاز</th>
                        <th>اسم نقطة البيع / الكاشير</th>
                        <th>المستودع المرتبط (خصم المخزون)</th>
                        <th>العميل الافتراضي</th>
                        <th>نوع الطابعة المحددة</th>
                        <th>حالة الجهاز</th>
                        <th>الوردية الحالية</th>
                        <th style="text-align: center; width: 130px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($terminals as $terminal)
                        <tr>
                            <td>
                                <span class="badge" style="background: #F1F5F9; color: var(--text-main); font-weight: 800; font-family: monospace;">
                                    {{ $terminal->code }}
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 14px; color: var(--text-main);">{{ $terminal->name }}</strong>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <i class="fa-solid fa-warehouse" style="color: var(--primary); font-size: 12px;"></i>
                                    <span style="font-weight: 600;">{{ $terminal->store->name ?? 'غير محدد' }}</span>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: var(--text-muted);">
                                    {{ $terminal->defaultClient->name ?? 'عميل نقدي سريع (افتراضي)' }}
                                </span>
                            </td>
                            <td>
                                @if($terminal->printer_type === 'thermal_80mm')
                                    <span class="badge" style="background: #D1E7DD; color: #0F5132; font-weight: 700;">طابعة حرارية 80mm</span>
                                @elseif($terminal->printer_type === 'thermal_57mm')
                                    <span class="badge" style="background: #FEF3C7; color: #D97706; font-weight: 700;">طابعة حرارية 57mm</span>
                                @else
                                    <span class="badge" style="background: #E0E7FF; color: #4338CA; font-weight: 700;">طابعة عادية A4</span>
                                @endif
                            </td>
                            <td>
                                @if($terminal->is_active)
                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 700;">
                                        نشط ويعمل
                                    </span>
                                @else
                                    <span class="badge" style="background: #FEE2E2; color: #DC2626; font-weight: 700;">
                                        معطل
                                    </span>
                                @endif
                            </td>
                            <td>
                                @php $activeShift = $terminal->shifts->first(); @endphp
                                @if($activeShift)
                                    <a href="{{ route('pos.shifts.show', $activeShift->id) }}" style="text-decoration: none;">
                                        <span class="badge" style="background: rgba(15, 81, 50, 0.12); color: var(--primary); font-weight: 800; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-circle" style="font-size: 7px; color: #10B981;"></i>
                                            وردية مفتوحة: {{ $activeShift->shift_number }}
                                        </span>
                                    </a>
                                @else
                                    <span style="font-size: 12px; color: var(--text-muted);">لا توجد وردية مفتوحة</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button type="button" 
                                            onclick="openEditTerminalModal({{ json_encode($terminal) }})"
                                            title="تعديل الجهاز"
                                            style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>

                                    <form action="{{ route('pos.terminals.destroy', $terminal->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في حذف نقطة البيع هذه؟');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="حذف الجهاز"
                                                style="width: 32px; height: 32px; border-radius: 8px; background: #F8FAFC; border: 1px solid var(--border-color); color: #DC2626; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 48px 16px; color: var(--text-muted);">
                                <i class="fa-solid fa-server" style="font-size: 48px; opacity: 0.3; margin-bottom: 12px; display: block;"></i>
                                <span style="font-size: 15px; font-weight: 700; display: block;">لا توجد أجهزة كاشير معرفة حتى الآن</span>
                                <span style="font-size: 13px;">قم بإضافة جهاز نقطة بيع لربط شاشات الكاشير والمستودعات</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal: Create New POS Terminal -->
<div id="createTerminalModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 540px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-plus-circle" style="color: var(--accent-gold);"></i>
                تعريف جهاز نقطة بيع جديد
            </h4>
            <button type="button" onclick="closeCreateTerminalModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form action="{{ route('pos.terminals.store') }}" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم نقطة البيع / الكاشير <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" class="erp-input" placeholder="مثال: كاشير 1 الصالة الرئيسية" required>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        كود الجهاز / المعرف
                    </label>
                    <input type="text" name="code" class="erp-input" placeholder="تلقائي: POS-01">
                </div>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    المستودع المرتبط (خصم المخزون منه) <span style="color: #DC2626;">*</span>
                </label>
                <select name="store_id" class="erp-input" required>
                    <option value="">-- حدد مستودع البضاعة --</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->branch->name ?? 'الفرع الرئيسي' }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        نوع الطابعة
                    </label>
                    <select name="printer_type" class="erp-input">
                        <option value="thermal_80mm">طابعة حرارية 80mm (قياسي)</option>
                        <option value="thermal_57mm">طابعة حرارية 57mm</option>
                        <option value="a4">طابعة فواتير A4</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        العميل الافتراضي
                    </label>
                    <select name="default_client_id" class="erp-input">
                        <option value="">عميل نقدي سريع (افتراضي)</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeCreateTerminalModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
                    إلغاء
                </button>
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px; font-weight: 800;">
                    <i class="fa-solid fa-check-circle"></i>
                    حفظ وتعريف الجهاز
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit POS Terminal -->
<div id="editTerminalModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: #fff; width: 100%; max-width: 540px; border-radius: 16px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); overflow: hidden; animation: slideUp 0.25s ease;">
        <div style="background: linear-gradient(135deg, #0F5132, #0A3622); color: #fff; padding: 18px 24px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; font-size: 17px; font-weight: 800; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-pen-to-square" style="color: var(--accent-gold);"></i>
                تعديل بيانات نقطة البيع
            </h4>
            <button type="button" onclick="closeEditTerminalModal()" style="background: none; border: none; color: #fff; font-size: 18px; cursor: pointer;">&times;</button>
        </div>

        <form id="editTerminalForm" method="POST" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        اسم نقطة البيع / الكاشير <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="text" name="name" id="edit_name" class="erp-input" required>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        كود الجهاز
                    </label>
                    <input type="text" id="edit_code" class="erp-input" readonly style="background: #F1F5F9; color: var(--text-muted);">
                </div>
            </div>

            <div>
                <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                    المستودع المرتبط <span style="color: #DC2626;">*</span>
                </label>
                <select name="store_id" id="edit_store_id" class="erp-input" required>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->branch->name ?? 'الفرع الرئيسي' }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        نوع الطابعة
                    </label>
                    <select name="printer_type" id="edit_printer_type" class="erp-input">
                        <option value="thermal_80mm">طابعة حرارية 80mm (قياسي)</option>
                        <option value="thermal_57mm">طابعة حرارية 57mm</option>
                        <option value="a4">طابعة فواتير A4</option>
                    </select>
                </div>
                <div>
                    <label style="font-size: 12.5px; font-weight: 700; color: var(--text-main); display: block; margin-bottom: 6px;">
                        العميل الافتراضي
                    </label>
                    <select name="default_client_id" id="edit_default_client_id" class="erp-input">
                        <option value="">عميل نقدي سريع (افتراضي)</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px;">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1" style="width: 18px; height: 18px; accent-color: var(--primary);">
                <label for="edit_is_active" style="font-size: 13px; font-weight: 700; color: var(--text-main); cursor: pointer;">
                    نقطة البيع مفعلة وجاهزة للعمل
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px;">
                <button type="button" onclick="closeEditTerminalModal()" style="padding: 10px 18px; border-radius: 8px; border: 1px solid var(--border-color); background: #F8FAFC; color: var(--text-muted); font-weight: 700; cursor: pointer;">
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
    function openCreateTerminalModal() {
        document.getElementById('createTerminalModal').style.display = 'flex';
    }
    function closeCreateTerminalModal() {
        document.getElementById('createTerminalModal').style.display = 'none';
    }

    function openEditTerminalModal(terminal) {
        const form = document.getElementById('editTerminalForm');
        form.action = "{{ url('pos/terminals') }}/" + terminal.id;
        document.getElementById('edit_name').value = terminal.name;
        document.getElementById('edit_code').value = terminal.code;
        document.getElementById('edit_store_id').value = terminal.store_id;
        document.getElementById('edit_printer_type').value = terminal.printer_type;
        document.getElementById('edit_default_client_id').value = terminal.default_client_id || '';
        document.getElementById('edit_is_active').checked = terminal.is_active ? true : false;
        document.getElementById('editTerminalModal').style.display = 'flex';
    }
    function closeEditTerminalModal() {
        document.getElementById('editTerminalModal').style.display = 'none';
    }
</script>
@endsection

