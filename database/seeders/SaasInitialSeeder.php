<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SaasInitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Subscription Plans
        $basicPlan = Plan::create([
            'name' => 'الباقة الأساسية',
            'slug' => 'basic',
            'description' => 'مثالية للمحلات التجارية والأنشطة الصغيرة مع إدارة المخزون ونقاط البيع والفواتير',
            'price_monthly' => 199.00,
            'price_yearly' => 1990.00,
            'trial_days' => 14,
            'max_users' => 3,
            'max_branches' => 1,
            'max_stores' => 2,
            'features' => ['items', 'units', 'clients', 'invoices', 'pos', 'basic_reports'],
            'is_active' => true,
        ]);

        $businessPlan = Plan::create([
            'name' => 'باقة الأعمال المتقدمة',
            'slug' => 'business',
            'description' => 'حل شامل للشركات المتوسطة يشمل الحسابات العامة، وإدارة الموظفين، وتقارير الأرباح والتحليلات',
            'price_monthly' => 399.00,
            'price_yearly' => 3990.00,
            'trial_days' => 14,
            'max_users' => 10,
            'max_branches' => 3,
            'max_stores' => 5,
            'features' => ['items', 'units', 'clients', 'invoices', 'pos', 'finance', 'accounting', 'hr', 'advanced_reports'],
            'is_active' => true,
        ]);

        $enterprisePlan = Plan::create([
            'name' => 'باقة المؤسسات والشركات الشاملة',
            'slug' => 'enterprise',
            'description' => 'المنظومة الكاملة مع فروع ومخازن غير محدودة، التصنيع، المطاعم، الفاتورة الإلكترونية والذكاء الاصطناعي',
            'price_monthly' => 799.00,
            'price_yearly' => 7990.00,
            'trial_days' => 30,
            'max_users' => 50,
            'max_branches' => 10,
            'max_stores' => 20,
            'features' => ['all_modules', 'manufacturing', 'restaurant', 'maintenance', 'rep_delivery', 'zatca', 'ai_assistant', 'multi_branch'],
            'is_active' => true,
        ]);

        // 2. Create the Flagship Tenant: "شركة العزيزي"
        $tenant = Tenant::create([
            'company_name' => 'مجموعة شركة العزيزي للتجارة والتوريدات',
            'slug' => 'elazizy',
            'domain' => 'elazizy.erp',
            'commercial_register' => '1010884499',
            'tax_number' => '310998877600003',
            'phone' => '+966 50 123 4567',
            'email' => 'info@elazizy-group.com',
            'address' => 'المملكة العربية السعودية - الرياض - طريق الملك فهد',
            'currency_code' => 'SAR',
            'plan_id' => $enterprisePlan->id,
            'subscription_status' => 'active',
            'subscription_starts_at' => Carbon::now(),
            'subscription_ends_at' => Carbon::now()->addYear(),
            'theme_settings' => [
                'palette' => 'emerald',
                'primary_color' => '#0F5132',       // أخضر زمردي راقي
                'primary_hover' => '#0B3B24',       // زمردي داكن
                'primary_light' => '#D1E7DD',       // نعناعي ناعم
                'accent_gold'   => '#D97706',       // ذهبي عنبري دافئ
                'sidebar_bg'    => '#062E20',       // غابة ليلية زمردية فاخرة
                'sidebar_text'  => '#E2E8F0',
                'body_bg'       => '#F4F8F6',       // خلفية ناصعة ناعمة
            ],
            'is_active' => true,
        ]);

        // 3. Create Branches for El-Azizy
        $mainBranch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'الفرع الرئيسي - الرياض (الإدارة والمبيعات)',
            'code' => 'BR-01',
            'phone' => '+966 50 123 4567',
            'email' => 'riyadh@elazizy-group.com',
            'address' => 'حي العليا - برج النخبة',
            'is_main' => true,
            'is_active' => true,
        ]);

        $jeddahBranch = Branch::create([
            'tenant_id' => $tenant->id,
            'name' => 'فرع المنطقة الغربية - جدة',
            'code' => 'BR-02',
            'phone' => '+966 50 765 4321',
            'email' => 'jeddah@elazizy-group.com',
            'address' => 'طريق المدينة المنورة',
            'is_main' => false,
            'is_active' => true,
        ]);

        // 4. Record Active Subscription
        Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $enterprisePlan->id,
            'billing_cycle' => 'yearly',
            'price' => $enterprisePlan->price_yearly,
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addYear(),
            'status' => 'active',
            'payment_method' => 'bank_transfer',
            'payment_reference' => 'TXN-ELAZIZY-2026-INIT',
            'notes' => 'الاشتراك التأسيسي السنوي الكامل لشركة العزيزي',
        ]);

        // 5. Create Super Admin User (System Owner)
        User::create([
            'name' => 'المدير العام للنظام (Super Admin)',
            'email' => 'admin@elazizy-erp.com',
            'password' => Hash::make('12345678'),
            'role' => 'superadmin',
            'phone' => '0500000000',
            'theme_palette' => 'emerald',
            'is_active' => true,
        ]);

        // 6. Create Tenant Admin User (El-Azizy Manager)
        User::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $mainBranch->id,
            'name' => 'أ. عبد الرحمن العزيزي',
            'email' => 'manager@elazizy.com',
            'password' => Hash::make('12345678'),
            'role' => 'tenant_admin',
            'phone' => '+966 50 123 4567',
            'theme_palette' => 'emerald',
            'is_active' => true,
        ]);
    }
}

