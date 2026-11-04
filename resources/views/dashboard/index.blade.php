@extends('layouts.app')

@section('title', 'لوحة التحكم الرئيسية')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Welcome & Company Overview Hero -->
    <div style="background: linear-gradient(135deg, var(--sidebar-bg) 0%, var(--primary) 100%); border-radius: 18px; padding: 32px; color: #FFFFFF; position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.12);">
        <div style="position: absolute; left: -30px; bottom: -30px; font-size: 180px; opacity: 0.05; transform: rotate(-15deg); pointer-events: none;">
            <i class="fa-solid fa-shapes"></i>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; position: relative; z-index: 2;">
            <div>
                <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.12); padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; color: var(--accent-gold); margin-bottom: 12px;">
                    <i class="fa-solid fa-shield-halved"></i>
                    منظومة العزيزي ERP متعددة المستأجرين (SaaS)
                </div>
                <h2 style="font-size: 26px; font-weight: 900; margin-bottom: 6px;">
                    مرحباً بك، {{ $user->name }}
                </h2>
                <p style="font-size: 14px; opacity: 0.85; max-width: 600px;">
                    أنت مسجل حالياً على: <strong style="color: #FFFFFF;">{{ $tenant->company_name ?? 'شركة العزيزي' }}</strong>
                    @if($activeBranch)
                        | الفرع النشط: <strong style="color: var(--accent-gold);">{{ $activeBranch->name }}</strong>
                    @endif
                </p>
            </div>

            <div style="display: flex; gap: 12px; align-items: center;">
                <button type="button" onclick="openThemeModal()" class="btn-gold-custom">
                    <i class="fa-solid fa-palette"></i>
                    تغيير ألوان وهوية النظام
                </button>
                <a href="{{ route('subscription.index') }}" class="btn-primary-custom" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.25);">
                    <i class="fa-solid fa-gem"></i>
                    إدارة وتجديد الاشتراك
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
        <!-- Card 1: Subscription Status -->
        <div class="erp-card" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px;">
            <div style="width: 54px; height: 54px; border-radius: 14px; background: var(--primary-light); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa-solid fa-credit-card"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">خطة الاشتراك السحابي</span>
                <strong style="font-size: 18px; font-weight: 800; color: var(--text-main);">{{ $stats['plan_name'] }}</strong>
                <span style="display: block; font-size: 11.5px; color: var(--primary); font-weight: 700;">متبقي {{ $stats['subscription_days'] }} يوماً</span>
            </div>
        </div>

        <!-- Card 2: Branches -->
        <div class="erp-card" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px;">
            <div style="width: 54px; height: 54px; border-radius: 14px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa-solid fa-code-branch"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">الفروع المفعلة</span>
                <strong style="font-size: 18px; font-weight: 800; color: var(--text-main);">{{ $stats['branches_count'] }} فرع</strong>
                <span style="display: block; font-size: 11.5px; color: #D97706; font-weight: 700;">متعدد الفروع (Multi-Branch)</span>
            </div>
        </div>

        <!-- Card 3: Users -->
        <div class="erp-card" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px;">
            <div style="width: 54px; height: 54px; border-radius: 14px; background: #E0E7FF; color: #4338CA; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">المستخدمين والموظفين</span>
                <strong style="font-size: 18px; font-weight: 800; color: var(--text-main);">{{ $stats['users_count'] }} مستخدم</strong>
                <span style="display: block; font-size: 11.5px; color: #4338CA; font-weight: 700;">أدوار وصلاحيات مخصصة</span>
            </div>
        </div>

        <!-- Card 4: Theme Status -->
        <div class="erp-card" style="margin-bottom: 0; display: flex; align-items: center; gap: 18px;">
            <div style="width: 54px; height: 54px; border-radius: 14px; background: #FCE7F3; color: #BE185D; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                <i class="fa-solid fa-palette"></i>
            </div>
            <div>
                <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); display: block;">الثيم واللون المعتمد</span>
                <strong style="font-size: 18px; font-weight: 800; color: var(--text-main);">{{ currentTheme()['name'] ?? 'الزمردي الملكي' }}</strong>
                <span style="display: block; font-size: 11.5px; color: #BE185D; font-weight: 700;">هوية خاصة بشركة العزيزي</span>
            </div>
        </div>
    </div>

    <!-- Active Palette Switcher Section -->
    <div class="erp-card">
        <div class="card-header-custom">
            <h3 class="card-title-custom">
                <i class="fa-solid fa-swatchbook"></i>
                إعدادات هوية وألوان النظام الفورية (Live Theme Palette)
            </h3>
            <span style="font-size: 12.5px; color: var(--text-muted);">الأخضر الزمردي الراقي هو الافتراضي، مع إمكانية التبديل بنقرة واحدة</span>
        </div>

        <form action="{{ route('theme.update') }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                <button type="submit" name="palette" value="emerald" style="border: 2px solid {{ currentTheme()['palette'] == 'emerald' ? 'var(--primary)' : 'var(--border-color)' }}; border-radius: 12px; padding: 16px; background: {{ currentTheme()['palette'] == 'emerald' ? 'var(--primary-light)' : '#fff' }}; cursor: pointer; text-align: right; transition: all 0.2s;">
                    <div style="display: flex; gap: 6px; margin-bottom: 10px;">
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #0F5132; display: inline-block;"></span>
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #D97706; display: inline-block;"></span>
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #062E20; display: inline-block;"></span>
                    </div>
                    <strong style="font-size: 14px; color: #0F5132; display: block;">الزمردي الملكي والذهبي</strong>
                    <span style="font-size: 11.5px; color: #64748B;">الخيار الفخم المعتمد لشركة العزيزي</span>
                </button>

                <button type="submit" name="palette" value="navy" style="border: 2px solid {{ currentTheme()['palette'] == 'navy' ? '#1E3A8A' : 'var(--border-color)' }}; border-radius: 12px; padding: 16px; background: {{ currentTheme()['palette'] == 'navy' ? '#DBEAFE' : '#fff' }}; cursor: pointer; text-align: right; transition: all 0.2s;">
                    <div style="display: flex; gap: 6px; margin-bottom: 10px;">
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #1E3A8A; display: inline-block;"></span>
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #0F172A; display: inline-block;"></span>
                    </div>
                    <strong style="font-size: 14px; color: #1E3A8A; display: block;">الكحلي الملوكي والنيلي</strong>
                    <span style="font-size: 11.5px; color: #64748B;">طابع رسمي هادئ واحترافي</span>
                </button>

                <button type="submit" name="palette" value="burgundy" style="border: 2px solid {{ currentTheme()['palette'] == 'burgundy' ? '#831843' : 'var(--border-color)' }}; border-radius: 12px; padding: 16px; background: {{ currentTheme()['palette'] == 'burgundy' ? '#FCE7F3' : '#fff' }}; cursor: pointer; text-align: right; transition: all 0.2s;">
                    <div style="display: flex; gap: 6px; margin-bottom: 10px;">
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #831843; display: inline-block;"></span>
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #D97706; display: inline-block;"></span>
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #2A0818; display: inline-block;"></span>
                    </div>
                    <strong style="font-size: 14px; color: #831843; display: block;">العنابي الراقي والبرونزي</strong>
                    <span style="font-size: 11.5px; color: #64748B;">هوية فاخرة بألوان عميقة</span>
                </button>

                <button type="submit" name="palette" value="teal" style="border: 2px solid {{ currentTheme()['palette'] == 'teal' ? '#0F766E' : 'var(--border-color)' }}; border-radius: 12px; padding: 16px; background: {{ currentTheme()['palette'] == 'teal' ? '#CCFBF1' : '#fff' }}; cursor: pointer; text-align: right; transition: all 0.2s;">
                    <div style="display: flex; gap: 6px; margin-bottom: 10px;">
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #0F766E; display: inline-block;"></span>
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
                        <span style="width: 22px; height: 22px; border-radius: 50%; background: #042F2E; display: inline-block;"></span>
                    </div>
                    <strong style="font-size: 14px; color: #0F766E; display: block;">التيل المحيطي العصري</strong>
                    <span style="font-size: 11.5px; color: #64748B;">ألوان حديثة مشرقة للأنشطة الرقمية</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Modules Architecture Roadmap -->
    <div class="erp-card">
        <div class="card-header-custom">
            <h3 class="card-title-custom">
                <i class="fa-solid fa-cubes"></i>
                خطة بناء موديولات نظام العزيزي ERP (خطوة بخطوة)
            </h3>
            <span style="background: var(--primary-light); color: var(--primary); padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;">
                تم إنجاز: النواة (0) + الإعدادات (1) + الأصناف والمستودعات (2) + العملاء والموردين (3) + المبيعات والمشتريات (4)
            </span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 16px;">
            <!-- Module 0: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> المرحلة 0: النواة وSaaS والألوان</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    بنية المستأجرين (Tenants)، إدارة الاشتراكات، الفروع، المستخدمين، الثيم الزمردي الراقي، ونظام تخصيص الألوان المباشر.
                </p>
            </div>

            <!-- Module 1: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 1: الإعدادات والمناطق</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    بيانات الشركة الرسمية، الضرائب (VAT)، دليل العملات وأسعار الصرف، الشجرة الجغرافية ومناطق التوصيل، قوالب الفواتير، والباركود.
                </p>
            </div>

            <!-- Module 2: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 2: الأصناف والمستودعات</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    دليل الأصناف، شجرة الأسعار المتعددة، وحدات القياس، الأقسام والماركات، المستودعات، التحويلات المخزنية، وتسويات الجرد والهالك.
                </p>
            </div>

            <!-- Module 3: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 3: العملاء والموردين (CRM)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    دليل العملاء والموردين، حدود الائتمان، شرائح الأسعار والخصومات، جدولة الديون والأقساط، شطب وتسوية الحسابات، وكشوف الحسابات الرسمية.
                </p>
            </div>

            <!-- Module 4: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 4: المبيعات والمشتريات (Invoicing)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    فواتير المبيعات والمشتريات، عروض الأسعار، أوامر الشراء، المرتجعات، الخصومات المتعددة، الربط المالي والمخزني التلقائي، والطباعة الضريبية.
                </p>
            </div>

            <!-- Module 5: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 5: نقاط البيع والكاشير (POS)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    شاشة الكاشير السريعة اللمسية، مسدس الباركود، إدارة وتدوير الورديات، تعليق واسترجاع الفواتير، جرد الدرج ومطابقة العجز والزيادة (تقرير Z)، وطباعة الإيصالات الحرارية (80mm).
                </p>
            </div>

            <!-- Module 6: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 6: الخزائن والمصروفات (Treasury & Expenses)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    الخزائن النقدية، الحسابات البنكية، المحافظ الإلكترونية، سندات القبض والصرف، التحويلات الداخلية، شجرة بنود المصروفات، وكشوف حسابات السيولة اللحظية.
                </p>
            </div>

            <!-- Module 7: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 7: شجرة الحسابات والقيود (Accounting & GL)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    دليل الحسابات الشجري (COA) خماسي المستويات، القيود اليومية المزدوجة المتوازنة، مراكز التكلفة، دفتر الأستاذ العام، وميزان المراجعة بالأرصدة والمجاميع.
                </p>
            </div>

            <!-- Module 8: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 8: القوائم والتقارير المالية (Financial Reports)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    قائمة الدخل والأرباح والخسائر، الميزانية العمومية والمركز المالي، قائمة التدفقات النقدية (IAS 7)، النسب والمؤشرات المالية التحليلية، الإقرار الضريبي للـ VAT، وتحليل ربحية المبيعات والأصناف.
                </p>
            </div>

            <!-- Module 9: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 9: الموارد البشرية والرواتب (HR & Payroll)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    دليل وسجلات الموظفين، الأقسام والوظائف، مسيرات الرواتب الشهرية الآلية، تسوية واستقطاع أقساط السلف، كشوف الحضور والانصراف، وإدارة الإجازات وقسائم الصرف المعتمدة.
                </p>
            </div>

            <!-- Module 10: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 10: التصنيع وتكاليف الإنتاج (Manufacturing & BOM)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    أوامر التشغيل والتصنيع (MO)، فواتير وقوائم المواد والتراكيب (BOM)، محطات وخطوط الإنتاج، دورة حجز وخصم المواد الخام وتوريد المنتجات التامة للمخازن، وتحليلات تكلفة البضاعة المصنعة (COGM).
                </p>
            </div>

            <!-- Module 11: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 11: الفروع ومراكز التوزيع والتحويلات البينية (Multi-Branch & Logistics)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    إدارة شبكة الفروع الجغرافية ومراكز التوزيع الإقليمية، تخصيص وصلاحيات مستخدمي الفروع مع التبديل الفوري، دورة التحويلات اللوجستية والمخزنية بين الفروع (مسودة، شحن بخصم المصدر، استلام بتوريد الوجهة، وإلغاء باسترجاع البضاعة)، بوالص الشحن المعتمدة، والمقارنات التحليلية للأداء.
                </p>
            </div>

            <!-- Module 12: Done -->
            <div style="border: 1.5px solid var(--primary); border-radius: 14px; padding: 18px; background: var(--primary-light);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 12: الأصول الثابتة والإهلاك المحاسبي (Fixed Assets & Depreciation)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    سجلات وبطاقات الأصول الثابتة، تصنيفات الأصول وربط شجرة الحسابات، محرك الإهلاك المحاسبي الدوري (القسط الثابت والمتناقص) مع الترحيل التلقائي لدفتر اليومية العامة، طباعة ملصقات الباركود، معالجة الاستبعاد والتكهين والبيع وحساب الأرباح والخسائر الرأسمالية.
                </p>
            </div>

            <!-- Module 13: Completed -->
            <div style="border: 1px solid var(--border-color); border-radius: 14px; padding: 18px; background: #FFFFFF;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: var(--primary);"><i class="fa-solid fa-circle-check"></i> موديول 13: إدارة المشاريع وعقود المقاولات (Project Management & Contracting)</strong>
                    <span style="background: var(--primary); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مكتمل وجاهز</span>
                </div>
                <p style="font-size: 13px; color: var(--text-main); margin-bottom: 0;">
                    دليل وسجل المشاريع، جداول الكميات وبنود المقايسات المسعرة (BOQ)، المراحل الإنشائية ومؤشرات الإنجاز، أوامر التغيير والتمديد الزمني، المستخلصات الجارية والختامية مع استقطاعات الدفعة المقدمة والضمان وضريبة القيمة المضافة، إصدار الشهادات الرسمية والتحصيل بالخزائن.
                </p>
            </div>

            <!-- Module 14: Next -->
            <div style="border: 1.5px dashed var(--accent-gold); border-radius: 14px; padding: 18px; background: #FFFBEB;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: #B45309;"><i class="fa-solid fa-hourglass-start"></i> موديول 14: إدارة الجودة والرقابة التفتيشية (Quality Control & Assurance)</strong>
                    <span style="background: var(--accent-gold); color: #fff; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">المحطة القادمة</span>
                </div>
                <p style="font-size: 13px; color: #78350F; margin-bottom: 0;">
                    معايير الفحص واستلام المواد الخام، خطط التفتيش الدوري، أوامر عدم المطابقة (NCR)، ومتابعة كفاءة الجودة في التصنيع والمشاريع.
                </p>
            </div>

            <!-- Module 15: Scheduled -->
            <div style="border: 1px solid var(--border-color); border-radius: 14px; padding: 18px; background: #F8FAFC;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="font-size: 15px; color: #475569;"><i class="fa-solid fa-wrench"></i> موديول 15: إدارة الصيانة الوقائية والتشغيلية (Preventive & Operational Maintenance)</strong>
                    <span style="background: #E2E8F0; color: #475569; font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 10px;">مجدول</span>
                </div>
                <p style="font-size: 13px; color: #64748B; margin-bottom: 0;">
                    خطط الصيانة الدورية للأصول والمعدات، تذاكر وأوامر العمل الطارئة، استهلاك قطع الغيار، وربط تكاليف الصيانة بالحسابات العامة.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection

