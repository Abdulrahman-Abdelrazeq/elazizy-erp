@php
    $theme = currentTheme();
    $tenant = currentTenant();
    $activeBranch = activeBranch();
    $user = auth()->user();
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') | العزيزي ERP</title>

    <!-- Google Arabic Fonts (Cairo & Tajawal) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: {{ $theme['primary_color'] ?? '#0F5132' }};
            --primary-hover: {{ $theme['primary_hover'] ?? '#0B3B24' }};
            --primary-light: {{ $theme['primary_light'] ?? '#D1E7DD' }};
            --accent-gold: {{ $theme['accent_gold'] ?? '#D97706' }};
            --sidebar-bg: {{ $theme['sidebar_bg'] ?? '#062E20' }};
            --sidebar-text: {{ $theme['sidebar_text'] ?? '#E2E8F0' }};
            --body-bg: {{ $theme['body_bg'] ?? '#F4F8F6' }};
            --surface-card: #FFFFFF;
            --border-color: #E2E8F0;
            --text-main: #1E293B;
            --text-muted: #64748B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--body-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
            font-size: 14.5px;
        }

        /* Sidebar Styling */
        .erp-sidebar {
            width: 270px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            z-index: 100;
            transition: all 0.3s ease;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
        }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.04) 0%, transparent 100%);
        }

        .brand-logo-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--accent-gold) 0%, #B45309 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 22px;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
        }

        .brand-text h1 {
            font-size: 19px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: -0.3px;
        }

        .brand-text span {
            font-size: 11px;
            color: var(--accent-gold);
            font-weight: 600;
            display: block;
        }

        .sidebar-menu {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
            list-style: none;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        .menu-category {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: rgba(226, 232, 240, 0.45);
            padding: 12px 14px 6px;
            font-weight: 700;
        }

        .menu-item {
            margin-bottom: 4px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13.5px;
            transition: all 0.2s ease;
        }

        .menu-link i {
            width: 22px;
            font-size: 16px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.2s ease;
        }

        .menu-link:hover {
            background-color: rgba(255, 255, 255, 0.08);
            color: #FFFFFF;
        }

        .menu-link:hover i {
            color: var(--accent-gold);
            transform: scale(1.1);
        }

        .menu-link.active {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-hover) 100%);
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
            border-right: 4px solid var(--accent-gold);
        }

        .menu-link.active i {
            color: var(--accent-gold);
        }

        .menu-badge {
            margin-right: auto;
            background: rgba(255, 255, 255, 0.12);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10.5px;
            font-weight: 700;
        }

        /* Main Content Wrapper */
        .erp-main {
            margin-right: 270px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: calc(100% - 270px);
        }

        /* Top Header Navbar */
        .erp-header {
            background-color: #FFFFFF;
            height: 72px;
            padding: 0 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 90;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .header-left, .header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Tenant & Branch Pill */
        .branch-badge {
            display: flex;
            align-items: center;
            gap: 8px;
            background-color: #F8FAFC;
            border: 1px solid var(--border-color);
            padding: 6px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
        }

        .branch-badge i {
            color: var(--primary);
        }

        /* Subscription Status Pill */
        .subscription-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            background-color: var(--primary-light);
            color: var(--primary-hover);
        }

        .subscription-pill.trial {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .subscription-pill.expired {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        /* Action Buttons */
        .header-btn {
            background: #F8FAFC;
            border: 1px solid var(--border-color);
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .header-btn:hover {
            background: var(--primary-light);
            color: var(--primary);
            border-color: var(--primary);
        }

        /* User Profile Pill */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            background: #F8FAFC;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-main);
        }

        .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--primary);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .user-info .user-name {
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
        }

        .user-info .user-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Page Content */
        .page-body {
            padding: 28px;
            flex: 1;
        }

        /* Alerts */
        .alert-custom {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            font-size: 13.5px;
        }

        .alert-success {
            background-color: #D1E7DD;
            color: #0F5132;
            border: 1px solid #BADBCC;
        }

        .alert-danger {
            background-color: #F8D7DA;
            color: #842029;
            border: 1px solid #F5C2C7;
        }

        /* Common ERP Card */
        .erp-card {
            background: #FFFFFF;
            border-radius: 14px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-header-custom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title-custom {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title-custom i {
            color: var(--primary);
        }

        /* Buttons */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
            color: #FFFFFF;
            border: none;
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(15, 81, 50, 0.2);
        }

        .btn-primary-custom:hover {
            box-shadow: 0 6px 14px rgba(15, 81, 50, 0.35);
            transform: translateY(-1px);
            color: #FFFFFF;
        }

        .btn-gold-custom {
            background: linear-gradient(135deg, var(--accent-gold) 0%, #B45309 100%);
            color: #FFFFFF;
            border: none;
            padding: 9px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(217, 119, 6, 0.25);
        }

        .btn-gold-custom:hover {
            box-shadow: 0 6px 14px rgba(217, 119, 6, 0.4);
            transform: translateY(-1px);
            color: #FFFFFF;
        }

        /* Modal Styles */
        .theme-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .theme-modal.active {
            display: flex;
        }

        .modal-box {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 28px;
            width: 100%;
            max-width: 580px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            border: 1px solid var(--border-color);
        }

        .palette-option {
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: all 0.2s;
        }

        .palette-option:hover {
            border-color: var(--primary);
            background: #F8FAFC;
        }

        .palette-option.selected {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .color-dots {
            display: flex;
            gap: 8px;
        }

        .color-dot {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid #FFFFFF;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar -->
    <aside class="erp-sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo-icon">
                <i class="fa-solid fa-shapes"></i>
            </div>
            <div class="brand-text">
                <h1>العزيزي ERP</h1>
                <span>نظام إدارة الموارد السحابي</span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-category">الرئيسية والاشتراكات</li>
            <li class="menu-item">
                <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>لوحة المعلومات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('subscription.index') }}" class="menu-link {{ request()->routeIs('subscription.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-credit-card"></i>
                    <span>باقات واشتراكات SaaS</span>
                    <span class="menu-badge" style="background: var(--accent-gold); color: #fff;">SaaS</span>
                </a>
            </li>

            <li class="menu-category">موديول 1: الإعدادات والمناطق</li>
            <li class="menu-item">
                <a href="{{ route('settings.index') }}" class="menu-link {{ request()->routeIs('settings.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i>
                    <span>بيانات الشركة والإعدادات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('settings.taxes.index') }}" class="menu-link {{ request()->routeIs('settings.taxes.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-percent"></i>
                    <span>الضرائب والـ VAT</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('settings.currencies.index') }}" class="menu-link {{ request()->routeIs('settings.currencies.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-coins"></i>
                    <span>دليل العملات وأسعار الصرف</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('settings.locations.index') }}" class="menu-link {{ request()->routeIs('settings.locations.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>المناطق والمدن ومناطق الشحن</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('settings.prints.index') }}" class="menu-link {{ request()->routeIs('settings.prints.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-print"></i>
                    <span>قوالب طباعة الفواتير</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('settings.barcode.index') }}" class="menu-link {{ request()->routeIs('settings.barcode.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-barcode"></i>
                    <span>ملصقات الباركود</span>
                </a>
            </li>

            <li class="menu-category">موديول 2: الأصناف والمستودعات</li>
            <li class="menu-item">
                <a href="{{ route('inventory.items.index') }}" class="menu-link {{ request()->routeIs('inventory.items.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>دليل الأصناف والمنتجات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('inventory.stores.index') }}" class="menu-link {{ request()->routeIs('inventory.stores.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-warehouse"></i>
                    <span>المستودعات والمخازن</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('inventory.units.index') }}" class="menu-link {{ request()->routeIs('inventory.units.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>وحدات القياس</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('inventory.categories.index') }}" class="menu-link {{ request()->routeIs('inventory.categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-folder-tree"></i>
                    <span>الأقسام والماركات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('inventory.transfers.index') }}" class="menu-link {{ request()->routeIs('inventory.transfers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-right-left"></i>
                    <span>التحويلات المخزنية</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('inventory.adjustments.index') }}" class="menu-link {{ request()->routeIs('inventory.adjustments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>تسويات الجرد والهالك</span>
                </a>
            </li>

            <li class="menu-category">موديول 3: إدارة العملاء والموردين (CRM)</li>
            <li class="menu-item">
                <a href="{{ route('crm.customers.index') }}" class="menu-link {{ request()->routeIs('crm.customers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>سجل ودليل العملاء</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('crm.suppliers.index') }}" class="menu-link {{ request()->routeIs('crm.suppliers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-field"></i>
                    <span>سجل ودليل الموردين</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('crm.groups.index') }}" class="menu-link {{ request()->routeIs('crm.groups.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>شرائح وتصنيفات العملاء</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('crm.debts.index') }}" class="menu-link {{ request()->routeIs('crm.debts.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span>جدولة الديون والأقساط</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('crm.adjustments.index') }}" class="menu-link {{ request()->routeIs('crm.adjustments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>تسويات وشطب الديون</span>
                </a>
            </li>

            <li class="menu-category">موديول 4: المبيعات والمشتريات (Invoicing)</li>
            <li class="menu-item">
                <a href="{{ route('invoices.sales.index') }}" class="menu-link {{ request()->routeIs('invoices.sales.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>فواتير المبيعات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('invoices.purchases.index') }}" class="menu-link {{ request()->routeIs('invoices.purchases.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>فواتير المشتريات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('invoices.quotations.index') }}" class="menu-link {{ request()->routeIs('invoices.quotations.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-handshake"></i>
                    <span>عروض الأسعار للعملاء</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('invoices.returns.index') }}" class="menu-link {{ request()->routeIs('invoices.returns.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>مرتجعات البيع والشراء</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('invoices.orders.index') }}" class="menu-link {{ request()->routeIs('invoices.orders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-contract"></i>
                    <span>أوامر الشراء (PO)</span>
                </a>
            </li>

            <li class="menu-category">موديول 5: نقاط البيع والكاشير (POS)</li>
            <li class="menu-item">
                <a href="{{ route('pos.screen') }}" class="menu-link {{ request()->routeIs('pos.screen') ? 'active' : '' }}">
                    <i class="fa-solid fa-desktop"></i>
                    <span>شاشة الكاشير السريعة</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('pos.shifts.index') }}" class="menu-link {{ request()->routeIs('pos.shifts.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-cash-register"></i>
                    <span>ورديات وجرد الكاشير</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('pos.terminals.index') }}" class="menu-link {{ request()->routeIs('pos.terminals.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-server"></i>
                    <span>أجهزة ونقاط البيع</span>
                </a>
            </li>

            <li class="menu-category">موديول 6: الخزائن والبنوك والمصروفات</li>
            <li class="menu-item">
                <a href="{{ route('treasury.treasuries.index') }}" class="menu-link {{ request()->routeIs('treasury.treasuries.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-vault"></i>
                    <span>الخزائن والحسابات البنكية</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('treasury.vouchers.index') }}" class="menu-link {{ request()->routeIs('treasury.vouchers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>سندات القبض والصرف</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('treasury.transfers.index') }}" class="menu-link {{ request()->routeIs('treasury.transfers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-right-left"></i>
                    <span>التحويلات بين الخزائن</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('treasury.categories.index') }}" class="menu-link {{ request()->routeIs('treasury.categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i>
                    <span>بنود المصروفات والإيرادات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('treasury.reports.cash_flow') }}" class="menu-link {{ request()->routeIs('treasury.reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>تقرير التدفق والسيولة</span>
                </a>
            </li>

            <li class="menu-category">موديول 7: الحسابات العامة ودفتر الأستاذ</li>
            <li class="menu-item">
                <a href="{{ route('accounting.accounts.index') }}" class="menu-link {{ request()->routeIs('accounting.accounts.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>شجرة الحسابات (COA)</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('accounting.journal-entries.index') }}" class="menu-link {{ request()->routeIs('accounting.journal-entries.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-book"></i>
                    <span>دفتر القيود اليومية</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('accounting.cost_centers.index') }}" class="menu-link {{ request()->routeIs('accounting.cost_centers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-crosshairs"></i>
                    <span>مراكز التكلفة</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('accounting.ledger.index') }}" class="menu-link {{ request()->routeIs('accounting.ledger.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-book-open"></i>
                    <span>دفتر الأستاذ العام</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('accounting.trial_balance.index') }}" class="menu-link {{ request()->routeIs('accounting.trial_balance.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>ميزان المراجعة</span>
                </a>
            </li>

            <li class="menu-category">موديول 8: القوائم والتقارير المالية</li>
            <li class="menu-item">
                <a href="{{ route('reports.index') }}" class="menu-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>مركز التقارير المالية</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('reports.income_statement') }}" class="menu-link {{ request()->routeIs('reports.income_statement*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>قائمة الدخل والأرباح</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('reports.balance_sheet') }}" class="menu-link {{ request()->routeIs('reports.balance_sheet*') ? 'active' : '' }}">
                    <i class="fa-solid fa-scale-balanced"></i>
                    <span>الميزانية والمركز المالي</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('reports.cash_flow') }}" class="menu-link {{ request()->routeIs('reports.cash_flow*') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    <span>قائمة التدفقات النقدية</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('reports.ratios') }}" class="menu-link {{ request()->routeIs('reports.ratios') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i>
                    <span>المؤشرات والنسب المالية</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('reports.tax') }}" class="menu-link {{ request()->routeIs('reports.tax*') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt"></i>
                    <span>الإقرار الضريبي والـ VAT</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('reports.sales_profitability') }}" class="menu-link {{ request()->routeIs('reports.sales_profitability') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-column"></i>
                    <span>تحليل ربحية المبيعات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('reports.budgets.index') }}" class="menu-link {{ request()->routeIs('reports.budgets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bullseye"></i>
                    <span>الموازنات التقديرية</span>
                </a>
            </li>

            <li class="menu-category">الموارد البشرية والرواتب (HR & Payroll)</li>
            <li class="menu-item">
                <a href="{{ route('hr.employees.index') }}" class="menu-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i>
                    <span>دليل وسجلات الموظفين</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('hr.departments.index') }}" class="menu-link {{ request()->routeIs('hr.departments.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sitemap"></i>
                    <span>الأقسام والمسميات الوظيفية</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('hr.payrolls.index') }}" class="menu-link {{ request()->routeIs('hr.payrolls.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-money-check-dollar"></i>
                    <span>مسيرات الرواتب الشهرية</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('hr.loans.index') }}" class="menu-link {{ request()->routeIs('hr.loans.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                    <span>سلف الموظفين والعهد</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('hr.leaves.index') }}" class="menu-link {{ request()->routeIs('hr.leaves.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-person-walking-luggage"></i>
                    <span>طلبات وسجلات الإجازات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('hr.attendances.index') }}" class="menu-link {{ request()->routeIs('hr.attendances.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-user"></i>
                    <span>كشف الحضور والانصراف</span>
                </a>
            </li>

            <li class="menu-category">التصنيع وتكاليف الإنتاج (Manufacturing & BOM)</li>
            <li class="menu-item">
                <a href="{{ route('manufacturing.orders.index') }}" class="menu-link {{ request()->routeIs('manufacturing.orders.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>أوامر التصنيع والتشغيل</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('manufacturing.boms.index') }}" class="menu-link {{ request()->routeIs('manufacturing.boms.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>فواتير وقوائم المواد (BOM)</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('manufacturing.workstations.index') }}" class="menu-link {{ request()->routeIs('manufacturing.workstations.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gears"></i>
                    <span>محطات وخطوط الإنتاج</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('manufacturing.reports.index') }}" class="menu-link {{ request()->routeIs('manufacturing.reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>تقارير وتكاليف الإنتاج</span>
                </a>
            </li>

            <li class="menu-category">الفروع ومراكز التوزيع (Multi-Branch & Logistics)</li>
            <li class="menu-item">
                <a href="{{ route('branches.branches.index') }}" class="menu-link {{ request()->routeIs('branches.branches.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-network-wired"></i>
                    <span>دليل الفروع ومراكز التوزيع</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('branches.transfers.index') }}" class="menu-link {{ request()->routeIs('branches.transfers.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-truck-fast"></i>
                    <span>التحويلات البينية للشحنات</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('branches.users.index') }}" class="menu-link {{ request()->routeIs('branches.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-shield"></i>
                    <span>صلاحيات وتعيين الفروع</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('branches.analytics.index') }}" class="menu-link {{ request()->routeIs('branches.analytics.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i>
                    <span>مقارنات أداء الفروع</span>
                </a>
            </li>

            <li class="menu-category">الأصول الثابتة والإهلاك (Fixed Assets)</li>
            <li class="menu-item">
                <a href="{{ route('assets.assets.index') }}" class="menu-link {{ request()->routeIs('assets.assets.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-building-columns"></i>
                    <span>سجل وبطاقات الأصول</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('assets.categories.index') }}" class="menu-link {{ request()->routeIs('assets.categories.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-shapes"></i>
                    <span>تصنيفات الأصول</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('assets.depreciations.index') }}" class="menu-link {{ request()->routeIs('assets.depreciations.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-calculator"></i>
                    <span>قيود وجداول الإهلاك</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('assets.disposals.index') }}" class="menu-link {{ request()->routeIs('assets.disposals.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-recycle"></i>
                    <span>استبعاد وبيع الأصول</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('assets.reports.index') }}" class="menu-link {{ request()->routeIs('assets.reports.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>مصفوفة وتقارير الأصول</span>
                </a>
            </li>

            <li class="menu-category">المشاريع والمقاولات (Projects & Contracting)</li>
            <li class="menu-item">
                <a href="{{ route('projects.projects.index') }}" class="menu-link {{ request()->routeIs('projects.projects.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-helmet-safety"></i>
                    <span>دليل وسجل المشاريع</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('projects.projects.create') }}" class="menu-link {{ request()->routeIs('projects.projects.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>تسجيل مشروع وعقد جديد</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('projects.extracts.index') }}" class="menu-link {{ request()->routeIs('projects.extracts.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                    <span>المستخلصات الجارية والختامية</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('projects.analytics.index') }}" class="menu-link {{ request()->routeIs('projects.analytics.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-pie"></i>
                    <span>تحليلات ومؤشرات الأداء EVM</span>
                </a>
            </li>

            <li class="menu-category">الموديولات القادمة (Next Roadmap)</li>
            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link" style="opacity: 0.65;">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>الرقابة وضبط الجودة الشاملة (موديول 14)</span>
                    <span class="menu-badge" style="background: var(--accent-gold); color: #fff;">الموديول التالي</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link" style="opacity: 0.65;">
                    <i class="fa-solid fa-wrench"></i>
                    <span>إدارة الصيانة الوقائية والتشغيلية (موديول 15)</span>
                    <span class="menu-badge">قيد الجدولة</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content Area -->
    <div class="erp-main">
        <!-- Top Header Navbar -->
        <header class="erp-header">
            <div class="header-right">
                @if($tenant)
                    <div class="branch-badge">
                        <i class="fa-solid fa-building"></i>
                        <span>{{ $tenant->company_name }}</span>
                    </div>

                    @if($tenant->branches && $tenant->branches->count() > 1)
                        <form action="{{ route('switch.branch') }}" method="POST" style="margin: 0;">
                            @csrf
                            <select name="branch_id" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 8px; border: 1px solid var(--border-color); font-weight: 600; font-size: 13px; background: #fff; cursor: pointer;">
                                @foreach($tenant->branches as $b)
                                    <option value="{{ $b->id }}" {{ $activeBranch && $activeBranch->id == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @elseif($activeBranch)
                        <div class="branch-badge" style="background: #F1F5F9;">
                            <i class="fa-solid fa-code-branch"></i>
                            <span>{{ $activeBranch->name }}</span>
                        </div>
                    @endif
                @endif
            </div>

            <div class="header-left">
                <!-- Subscription Status -->
                @if($tenant)
                    @if($tenant->subscription_status === 'trial')
                        <div class="subscription-pill trial">
                            <i class="fa-solid fa-clock"></i>
                            <span>فترة تجريبية (متبقي {{ $tenant->remainingDays() }} يوم)</span>
                        </div>
                    @elseif($tenant->isSubscriptionActive())
                        <div class="subscription-pill">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>اشتراك ساري ({{ $tenant->remainingDays() }} يوم)</span>
                        </div>
                    @else
                        <div class="subscription-pill expired">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                            <span>الاشتراك منتهي</span>
                        </div>
                    @endif
                @endif

                <!-- Theme Palette Customizer Button -->
                <button type="button" class="header-btn" onclick="openThemeModal()" title="تخصيص ألوان وثيم النظام">
                    <i class="fa-solid fa-palette" style="color: var(--accent-gold);"></i>
                </button>

                <!-- User Profile -->
                <div class="user-dropdown">
                    <div class="user-avatar">
                        {{ mb_substr($user->name ?? 'ع', 0, 1) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ $user->name ?? 'المستخدم' }}</div>
                        <div class="user-role">{{ $user->role === 'superadmin' ? 'Super Admin' : 'مدير الشركة' }}</div>
                    </div>
                </div>

                <!-- Logout -->
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="header-btn" title="تسجيل الخروج">
                        <i class="fa-solid fa-power-off" style="color: #EF4444;"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Body Content -->
        <main class="page-body">
            @if(session('success'))
                <div class="alert-custom alert-success">
                    <i class="fa-solid fa-circle-check fa-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-custom alert-danger">
                    <i class="fa-solid fa-circle-xmark fa-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Theme Palette Switcher Modal -->
    <div id="themeModal" class="theme-modal">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-weight: 800; font-size: 18px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-palette" style="color: var(--accent-gold);"></i>
                    تخصيص ألوان وثيم نظام العزيزي ERP
                </h3>
                <button type="button" onclick="closeThemeModal()" style="border: none; background: none; font-size: 20px; cursor: pointer; color: var(--text-muted);">&times;</button>
            </div>

            <p style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 20px;">
                يمكنك التبديل بين اللوحات اللونية الفخمة المجهزة مسبقاً، أو تخصيص كود اللون الخاص بك وسيتم حفظ الإعدادات على مستوى حسابك وشركتك:
            </p>

            <form action="{{ route('theme.update') }}" method="POST">
                @csrf
                <div class="palette-option {{ ($theme['palette'] ?? '') === 'emerald' ? 'selected' : '' }}" onclick="selectPalette('emerald')">
                    <div>
                        <input type="radio" name="palette" id="p_emerald" value="emerald" {{ ($theme['palette'] ?? '') === 'emerald' ? 'checked' : '' }} style="margin-left: 8px;">
                        <label for="p_emerald" style="font-weight: 700; cursor: pointer;">الزمردي الملكي والذهبي (الافتراضي الفخم)</label>
                    </div>
                    <div class="color-dots">
                        <span class="color-dot" style="background: #0F5132;"></span>
                        <span class="color-dot" style="background: #D97706;"></span>
                        <span class="color-dot" style="background: #062E20;"></span>
                    </div>
                </div>

                <div class="palette-option {{ ($theme['palette'] ?? '') === 'navy' ? 'selected' : '' }}" onclick="selectPalette('navy')">
                    <div>
                        <input type="radio" name="palette" id="p_navy" value="navy" {{ ($theme['palette'] ?? '') === 'navy' ? 'checked' : '' }} style="margin-left: 8px;">
                        <label for="p_navy" style="font-weight: 700; cursor: pointer;">الكحلي الملوكي والنيلي</label>
                    </div>
                    <div class="color-dots">
                        <span class="color-dot" style="background: #1E3A8A;"></span>
                        <span class="color-dot" style="background: #F59E0B;"></span>
                        <span class="color-dot" style="background: #0F172A;"></span>
                    </div>
                </div>

                <div class="palette-option {{ ($theme['palette'] ?? '') === 'burgundy' ? 'selected' : '' }}" onclick="selectPalette('burgundy')">
                    <div>
                        <input type="radio" name="palette" id="p_burgundy" value="burgundy" {{ ($theme['palette'] ?? '') === 'burgundy' ? 'checked' : '' }} style="margin-left: 8px;">
                        <label for="p_burgundy" style="font-weight: 700; cursor: pointer;">العنابي الراقي والبرونزي</label>
                    </div>
                    <div class="color-dots">
                        <span class="color-dot" style="background: #831843;"></span>
                        <span class="color-dot" style="background: #D97706;"></span>
                        <span class="color-dot" style="background: #2A0818;"></span>
                    </div>
                </div>

                <div class="palette-option {{ ($theme['palette'] ?? '') === 'teal' ? 'selected' : '' }}" onclick="selectPalette('teal')">
                    <div>
                        <input type="radio" name="palette" id="p_teal" value="teal" {{ ($theme['palette'] ?? '') === 'teal' ? 'checked' : '' }} style="margin-left: 8px;">
                        <label for="p_teal" style="font-weight: 700; cursor: pointer;">التيل المحيطي العصري</label>
                    </div>
                    <div class="color-dots">
                        <span class="color-dot" style="background: #0F766E;"></span>
                        <span class="color-dot" style="background: #F59E0B;"></span>
                        <span class="color-dot" style="background: #042F2E;"></span>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                    <button type="button" onclick="closeThemeModal()" style="background: #F1F5F9; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 8px; font-weight: 600; cursor: pointer;">إلغاء</button>
                    <button type="submit" class="btn-primary-custom">حفظ وتطبيق الألوان</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openThemeModal() {
            document.getElementById('themeModal').classList.add('active');
        }
        function closeThemeModal() {
            document.getElementById('themeModal').classList.remove('active');
        }
        function selectPalette(key) {
            const radio = document.getElementById('p_' + key);
            if (radio) {
                radio.checked = true;
            }
            document.querySelectorAll('.palette-option').forEach(el => el.classList.remove('selected'));
            event.currentTarget.classList.add('selected');
        }
    </script>
    @yield('scripts')
</body>
</html>

