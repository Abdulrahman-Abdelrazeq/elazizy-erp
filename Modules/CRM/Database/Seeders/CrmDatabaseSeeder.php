<?php

namespace Modules\CRM\Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\CRM\Entities\BalanceAdjustment;
use Modules\CRM\Entities\Client;
use Modules\CRM\Entities\ClientGroup;
use Modules\CRM\Entities\DebtSchedule;
use Modules\Setting\Entities\Location;

class CrmDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        $tenantId = $tenant ? $tenant->id : 1;
        $user = User::first();
        $userId = $user ? $user->id : 1;

        $cairo = Location::where('name', 'القاهرة')->first();
        $giza = Location::where('name', 'الجيزة')->first();
        $alex = Location::where('name', 'الإسكندرية')->first();

        // 1. Client Groups
        $wholesaleGroup = ClientGroup::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'تجار الجملة المعتمدين'],
            [
                'price_tier'          => 'wholesale',
                'discount_percentage' => 5.00,
                'is_active'           => true,
            ]
        );

        $halfWholesaleGroup = ClientGroup::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'نصف جملة وموزعين فرعيين'],
            [
                'price_tier'          => 'half_wholesale',
                'discount_percentage' => 2.50,
                'is_active'           => true,
            ]
        );

        $retailGroup = ClientGroup::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'عملاء التجزئة (قطاعي)'],
            [
                'price_tier'          => 'retail',
                'discount_percentage' => 0.00,
                'is_active'           => true,
            ]
        );

        $vipGroup = ClientGroup::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'عملاء VIP مميزين'],
            [
                'price_tier'          => 'wholesale',
                'discount_percentage' => 10.00,
                'is_active'           => true,
            ]
        );

        // 2. Customers
        $c1 = Client::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CST-1001'],
            [
                'type'                 => 'customer',
                'name'                 => 'شركة الأهرام للتجارة والتوزيع',
                'company_name'         => 'مؤسسة الأهرام الدولية',
                'group_id'             => $wholesaleGroup->id,
                'phone'                => '0225678901',
                'mobile'               => '01011122334',
                'email'                => 'ahram@trade.com',
                'tax_number'           => '100-200-300',
                'commercial_register'  => '45892',
                'location_id'          => $cairo ? $cairo->id : null,
                'address'              => '15 شارع رمسيس، وسط البلد، القاهرة',
                'credit_limit'         => 150000.00,
                'credit_days'          => 45,
                'opening_balance'      => 45000.00,
                'opening_balance_type' => 'debit',
                'current_balance'      => 45000.00,
                'is_active'            => true,
                'notes'                => 'عميل جملة ممتاز، يسدد بشيكات دورية كل 30 يوم',
            ]
        );

        $c2 = Client::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CST-1002'],
            [
                'type'                 => 'customer',
                'name'                 => 'المهندس مصطفى محمود سالم',
                'company_name'         => 'مكتب الإتقان للمقاولات',
                'group_id'             => $halfWholesaleGroup->id,
                'phone'                => '0233445566',
                'mobile'               => '01122233445',
                'email'                => 'mostafa@itqan.com',
                'tax_number'           => '101-202-303',
                'commercial_register'  => '78912',
                'location_id'          => $giza ? $giza->id : null,
                'address'              => 'شارع الهرم، الجيزة',
                'credit_limit'         => 80000.00,
                'credit_days'          => 30,
                'opening_balance'      => 95000.00,
                'opening_balance_type' => 'debit',
                'current_balance'      => 95000.00, // Over credit limit
                'is_active'            => true,
                'notes'                => 'تجاوز الحد الائتماني المسموح به (80 ألف)',
            ]
        );

        $c3 = Client::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CST-1003'],
            [
                'type'                 => 'customer',
                'name'                 => 'معرض السعادة للإلكترونيات',
                'company_name'         => 'مؤسسة السعادة للتجارة',
                'group_id'             => $vipGroup->id,
                'phone'                => '034876543',
                'mobile'               => '01233344556',
                'email'                => 'saada@electronics.com',
                'tax_number'           => '102-303-404',
                'location_id'          => $alex ? $alex->id : null,
                'address'              => 'محطة الرمل، الإسكندرية',
                'credit_limit'         => 200000.00,
                'credit_days'          => 60,
                'opening_balance'      => 20000.00,
                'opening_balance_type' => 'credit',
                'current_balance'      => -20000.00, // Creditor (has prepayment)
                'is_active'            => true,
                'notes'                => 'عميل VIP له رصيد دائن دفعات مقدمة للتوريدات القادمة',
            ]
        );

        $c4 = Client::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CST-1004'],
            [
                'type'                 => 'customer',
                'name'                 => 'الاستاذ أحمد كمال عبد العزيز',
                'company_name'         => null,
                'group_id'             => $retailGroup->id,
                'mobile'               => '01544455667',
                'national_id'          => '29010151234567',
                'location_id'          => $cairo ? $cairo->id : null,
                'address'              => 'مدينة نصر، القاهرة',
                'credit_limit'         => 10000.00,
                'credit_days'          => 15,
                'opening_balance'      => 0.00,
                'opening_balance_type' => 'debit',
                'current_balance'      => 0.00,
                'is_active'            => true,
            ]
        );

        // 3. Suppliers
        $s1 = Client::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'SUP-2001'],
            [
                'type'                 => 'supplier',
                'name'                 => 'شركة التوريدات العالمية للاستيراد',
                'company_name'         => 'جلوبال للتجارة والصناعة',
                'phone'                => '0226900111',
                'mobile'               => '01099988776',
                'email'                => 'info@globalsupplies.eg',
                'tax_number'           => '500-600-700',
                'commercial_register'  => '99102',
                'location_id'          => $cairo ? $cairo->id : null,
                'address'              => 'المنطقة الصناعية الأولى، مدينة العبور',
                'opening_balance'      => 85000.00,
                'opening_balance_type' => 'credit',
                'current_balance'      => -85000.00, // Payable to supplier
                'is_active'            => true,
                'notes'                => 'المورد الرئيسي للأصناف المستوردة والأجهزة الإلكترونية',
            ]
        );

        $s2 = Client::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'SUP-2002'],
            [
                'type'                 => 'supplier',
                'name'                 => 'مصنع الشرق للأجهزة والمعدات',
                'company_name'         => 'مجموعة مصانع الشرق الحديثة',
                'phone'                => '0238334455',
                'mobile'               => '01188877665',
                'email'                => 'orders@orientfactory.com',
                'tax_number'           => '501-601-701',
                'commercial_register'  => '33412',
                'location_id'          => $giza ? $giza->id : null,
                'address'              => 'المنطقة الصناعية الثالثة، السادس من أكتوبر',
                'opening_balance'      => 32000.00,
                'opening_balance_type' => 'credit',
                'current_balance'      => -32000.00,
                'is_active'            => true,
                'notes'                => 'توريد خامات ومعدات محلية الصنع',
            ]
        );

        // 4. Debt Schedules & Installments
        DebtSchedule::updateOrCreate(
            ['tenant_id' => $tenantId, 'client_id' => $c1->id, 'amount' => 15000.00],
            [
                'due_date'    => now()->addDays(15)->toDateString(),
                'status'      => 'pending',
                'paid_amount' => 0.00,
                'notes'       => 'قسط فاتورة مبيعات شهر نوفمبر',
            ]
        );

        DebtSchedule::updateOrCreate(
            ['tenant_id' => $tenantId, 'client_id' => $c2->id, 'amount' => 25000.00],
            [
                'due_date'    => now()->subDays(10)->toDateString(),
                'status'      => 'overdue',
                'paid_amount' => 0.00,
                'notes'       => 'قسط متأخر عن موعد الاستحقاق - مطلوب اتخاذ إجراء تحصيل',
            ]
        );

        DebtSchedule::updateOrCreate(
            ['tenant_id' => $tenantId, 'client_id' => $c1->id, 'amount' => 10000.00],
            [
                'due_date'    => now()->subDays(20)->toDateString(),
                'status'      => 'paid',
                'paid_amount' => 10000.00,
                'paid_at'     => now()->subDays(18),
                'notes'       => 'تم التحصيل نقداً بخزينة المركز الرئيسي',
            ]
        );

        // 5. Balance Adjustments
        BalanceAdjustment::updateOrCreate(
            ['tenant_id' => $tenantId, 'client_id' => $c1->id, 'amount' => 500.00],
            [
                'type'       => 'discount',
                'date'       => now()->subDays(5)->toDateString(),
                'notes'      => 'خصم تسوية مالي مقابل سداد فوري مبكر',
                'created_by' => $userId,
            ]
        );
    }
}
