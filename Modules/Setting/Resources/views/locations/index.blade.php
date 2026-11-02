@extends('layouts.app')

@section('title', 'المناطق الجغرافية ومناطق الشحن والتوصيل')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Breadcrumb & Actions Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 22px; font-weight: 900; color: var(--text-main);">
                <i class="fa-solid fa-map-location-dot" style="color: var(--primary); margin-left: 8px;"></i>
                المناطق الجغرافية ومناطق التوصيل والشحن
            </h2>
            <span style="font-size: 13px; color: var(--text-muted);">
                تنظيم الدول والمدن والأحياء وتحديد تكاليف التوصيل للمناديب وشركات الشحن
            </span>
        </div>

        <div style="display: flex; gap: 12px;">
            <a href="{{ route('settings.index') }}" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 9px 16px; border-radius: 10px; text-decoration: none; color: var(--text-main); font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-arrow-right"></i>
                العودة للإعدادات
            </a>
            <button type="button" onclick="document.getElementById('locationModal').style.display='flex'" class="btn-primary-custom">
                <i class="fa-solid fa-plus"></i>
                إضافة منطقة / مدينة جديدة
            </button>
        </div>
    </div>

    <!-- Tree View & Locations Listing -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <!-- Left: Tree Card -->
        <div class="erp-card">
            <div class="card-header-custom">
                <h3 class="card-title-custom">
                    <i class="fa-solid fa-network-wired"></i>
                    الشجرة الجغرافية الحالية
                </h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 16px;">
                @forelse($countries as $country)
                    <div style="border: 1.5px solid var(--border-color); border-radius: 12px; padding: 16px; background: #FAFDFB;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 16px; font-weight: 800; color: var(--primary);">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-flag"></i>
                                <span>{{ $country->name }} (دولة)</span>
                            </div>
                            <form action="{{ route('settings.locations.destroy', $country->id) }}" method="POST" onsubmit="return confirm('حذف الدولة سيحذف كافة المدن والأحياء التابعة لها!')" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #DC2626; cursor: pointer;" title="حذف الدولة">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>

                        <!-- Cities under country -->
                        <div style="margin-top: 14px; padding-right: 20px; display: flex; flex-direction: column; gap: 10px; border-right: 2px dashed var(--primary-light);">
                            @forelse($country->children as $city)
                                <div style="background: #FFFFFF; border: 1px solid var(--border-color); border-radius: 10px; padding: 12px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 14px;">
                                        <div style="display: flex; align-items: center; gap: 8px; color: var(--text-main);">
                                            <i class="fa-solid fa-city" style="color: var(--accent-gold);"></i>
                                            <span>{{ $city->name }}</span>
                                            @if($city->delivery_cost > 0)
                                                <span style="font-size: 11px; background: #FEF3C7; color: #92400E; padding: 2px 8px; border-radius: 10px;">
                                                    شحن: {{ formatMoney($city->delivery_cost) }}
                                                </span>
                                            @endif
                                        </div>
                                        <form action="{{ route('settings.locations.destroy', $city->id) }}" method="POST" onsubmit="return confirm('حذف هذه المدينة؟')" style="margin: 0;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: none; border: none; color: #EF4444; cursor: pointer; font-size: 13px;">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Zones under city -->
                                    @if($city->children->isNotEmpty())
                                        <div style="margin-top: 8px; padding-right: 16px; display: flex; flex-wrap: wrap; gap: 6px;">
                                            @foreach($city->children as $zone)
                                                <span style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 3px 10px; border-radius: 8px; font-size: 12px; display: inline-flex; align-items: center; gap: 6px;">
                                                    <i class="fa-solid fa-location-dot" style="font-size: 10px; color: var(--primary);"></i>
                                                    {{ $zone->name }}
                                                    @if($zone->delivery_cost > 0)
                                                        ({{ formatMoney($zone->delivery_cost) }})
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <span style="font-size: 12.5px; color: var(--text-muted);">لا توجد مدن مسجلة تابعة لهذه الدولة.</span>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fa-solid fa-earth-americas fa-3x" style="opacity: 0.3; margin-bottom: 12px;"></i>
                        <p>لم يتم تسجيل دول أو مدن بعد. أضف دولتك ومدينتك الأولى الآن.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Quick Add Form Card -->
        <div class="erp-card">
            <div class="card-header-custom">
                <h3 class="card-title-custom">
                    <i class="fa-solid fa-plus-circle"></i>
                    إضافة سريعة لموقع
                </h3>
            </div>

            <form action="{{ route('settings.locations.store') }}" method="POST">
                @csrf

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">نوع الموقع الجغرافي *</label>
                    <select name="type" id="quick_type" onchange="toggleParentSelector(this.value)" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                        <option value="country">دولة جديدة (مستوى رئيسي)</option>
                        <option value="city" selected>مدينة (تابعة لدولة)</option>
                        <option value="zone">حي / منطقة توصيل (تابعة لمدينة)</option>
                    </select>
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">اسم الدولة / المدينة / الحي *</label>
                    <input type="text" name="name" required placeholder="مثال: الرياض" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>

                <div id="parent_group" style="margin-bottom: 16px;">
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">الموقع الأب (الدولة أو المدينة التابعة لها)</label>
                    <select name="parent_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                        <option value="">-- اختر الموقع التابع له --</option>
                        @foreach($countries as $c)
                            <option value="{{ $c->id }}">دولة: {{ $c->name }}</option>
                            @foreach($c->children as $city)
                                <option value="{{ $city->id }}">&nbsp;&nbsp;&nbsp;&nbsp;↳ مدينة: {{ $city->name }}</option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">تكلفة التوصيل / الشحن الافتراضية</label>
                    <input type="number" step="0.5" name="delivery_cost" value="0.00" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
                </div>

                <button type="submit" class="btn-primary-custom" style="width: 100%; justify-content: center;">
                    <i class="fa-solid fa-save"></i>
                    حفظ وإضافة للمخطط
                </button>
            </form>
        </div>
    </div>

</div>

<!-- Modal for adding location -->
<div id="locationModal" class="theme-modal">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="font-weight: 800; font-size: 18px; color: var(--text-main);">
                <i class="fa-solid fa-map-location-dot" style="color: var(--primary); margin-left: 8px;"></i>
                إضافة موقع جغرافي جديد
            </h3>
            <button type="button" onclick="document.getElementById('locationModal').style.display='none'" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="{{ route('settings.locations.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">النوع *</label>
                <select name="type" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px; font-weight: 600;">
                    <option value="country">دولة جديدة</option>
                    <option value="city" selected>مدينة جديدة</option>
                    <option value="zone">حي / منطقة توصيل</option>
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">الاسم *</label>
                <input type="text" name="name" required placeholder="مثال: المملكة العربية السعودية" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="margin-bottom: 16px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">الموقع الأب (اختياري للدول)</label>
                <select name="parent_id" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 13.5px;">
                    <option value="">بدون (مستوى أول رئيسي)</option>
                    @foreach($countries as $c)
                        <option value="{{ $c->id }}">دولة: {{ $c->name }}</option>
                        @foreach($c->children as $city)
                            <option value="{{ $city->id }}">&nbsp;&nbsp;&nbsp;&nbsp;↳ مدينة: {{ $city->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px;">تكلفة التوصيل الافتراضية</label>
                <input type="number" step="0.5" name="delivery_cost" value="0.00" style="width: 100%; padding: 10px 14px; border-radius: 10px; border: 1.5px solid var(--border-color); font-size: 14px;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="button" onclick="document.getElementById('locationModal').style.display='none'" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">إلغاء</button>
                <button type="submit" class="btn-primary-custom">إضافة</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleParentSelector(type) {
        const group = document.getElementById('parent_group');
        if (type === 'country') {
            group.style.opacity = '0.5';
        } else {
            group.style.opacity = '1';
        }
    }
</script>
@endsection

