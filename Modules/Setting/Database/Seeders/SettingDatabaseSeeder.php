<?php

namespace Modules\Setting\Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Modules\Setting\Entities\BarcodeSetting;
use Modules\Setting\Entities\CompanySetting;
use Modules\Setting\Entities\Currency;
use Modules\Setting\Entities\Location;
use Modules\Setting\Entities\PrintTemplate;
use Modules\Setting\Entities\Tax;

class SettingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Setting module
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) {
            return;
        }

        // 1. Seed Default Taxes
        Tax::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'ضريبة القيمة المضافة القياسية (15%)'],
            [
                'rate'       => 15.00,
                'code'       => 'VAT-15',
                'type'       => 'percentage',
                'applies_to' => 'all',
                'is_default' => true,
                'is_active'  => true,
            ]
        );

        Tax::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'معفي من الضريبة (0%)'],
            [
                'rate'       => 0.00,
                'code'       => 'VAT-0',
                'type'       => 'percentage',
                'applies_to' => 'all',
                'is_default' => false,
                'is_active'  => true,
            ]
        );

        // 2. Seed Default Currencies
        Currency::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'SAR'],
            [
                'name'          => 'ريال سعودي',
                'symbol'        => 'ر.س',
                'exchange_rate' => 1.0000,
                'subunit'       => 'هللة',
                'is_default'    => true,
                'is_active'     => true,
            ]
        );

        Currency::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'USD'],
            [
                'name'          => 'دولار أمريكي',
                'symbol'        => '$',
                'exchange_rate' => 3.7500,
                'subunit'       => 'سنت',
                'is_default'    => false,
                'is_active'     => true,
            ]
        );

        Currency::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'EGP'],
            [
                'name'          => 'جنيه مصري',
                'symbol'        => 'ج.م',
                'exchange_rate' => 0.0770,
                'subunit'       => 'قرش',
                'is_default'    => false,
                'is_active'     => true,
            ]
        );

        // 3. Seed Default Locations (Saudi Arabia -> Riyadh / Jeddah -> Districts)
        $country = Location::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'المملكة العربية السعودية', 'type' => 'country'],
            ['delivery_cost' => 0, 'is_active' => true]
        );

        $riyadh = Location::firstOrCreate(
            ['tenant_id' => $tenant->id, 'parent_id' => $country->id, 'name' => 'الرياض', 'type' => 'city'],
            ['delivery_cost' => 25.00, 'is_active' => true]
        );

        Location::firstOrCreate(
            ['tenant_id' => $tenant->id, 'parent_id' => $riyadh->id, 'name' => 'حي العليا', 'type' => 'zone'],
            ['delivery_cost' => 25.00, 'is_active' => true]
        );

        Location::firstOrCreate(
            ['tenant_id' => $tenant->id, 'parent_id' => $riyadh->id, 'name' => 'حي الملز', 'type' => 'zone'],
            ['delivery_cost' => 25.00, 'is_active' => true]
        );

        $jeddah = Location::firstOrCreate(
            ['tenant_id' => $tenant->id, 'parent_id' => $country->id, 'name' => 'جدة', 'type' => 'city'],
            ['delivery_cost' => 30.00, 'is_active' => true]
        );

        Location::firstOrCreate(
            ['tenant_id' => $tenant->id, 'parent_id' => $jeddah->id, 'name' => 'حي الروضة', 'type' => 'zone'],
            ['delivery_cost' => 30.00, 'is_active' => true]
        );

        // 4. Seed Print Templates
        PrintTemplate::firstOrCreate(
            ['tenant_id' => $tenant->id, 'paper_size' => 'thermal_80mm'],
            [
                'title'                => 'فاتورة كاشير حرارية (80 مم)',
                'invoice_type'         => 'sales',
                'show_logo'            => true,
                'show_qr'              => true,
                'show_tax_number'      => true,
                'show_terms'           => true,
                'terms_and_conditions' => 'البضاعة المباعة ترد وتستبدل خلال 14 يوماً بموجب أصل الفاتورة.',
                'footer_text'          => 'شكراً لتعاملكم مع شركة العزيزي للتجارة والتوريدات',
                'is_default'           => true,
            ]
        );

        PrintTemplate::firstOrCreate(
            ['tenant_id' => $tenant->id, 'paper_size' => 'A4'],
            [
                'title'                => 'فاتورة ضريبية رسمية قياسية (A4)',
                'invoice_type'         => 'sales',
                'show_logo'            => true,
                'show_qr'              => true,
                'show_tax_number'      => true,
                'show_terms'           => true,
                'terms_and_conditions' => 'تعتبر هذه الفاتورة سند استلام نظامي للبضاعة الموضحة أعلاه.',
                'footer_text'          => 'شركة العزيزي للتجارة والتوريدات - جميع الحقوق محفوظة',
                'is_default'           => false,
            ]
        );

        // 5. Seed Barcode Settings
        BarcodeSetting::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'name'                => 'ملصق الباركود الافتراضي (38x25 مم)',
                'barcode_type'        => 'code128',
                'label_width'         => 38,
                'label_height'        => 25,
                'show_product_name'   => true,
                'show_price'          => true,
                'show_company_name'   => true,
                'show_barcode_number' => true,
                'is_default'          => true,
            ]
        );

        // 6. Seed Operational Company Settings
        $defaults = [
            'invoice_auto_print'       => '1',
            'invoice_default_type'     => 'cash',
            'invoice_show_tax_details' => '1',
            'invoice_show_qr_code'     => '1',
            'allow_negative_stock'     => '0',
            'update_price_on_purchase' => '1',
            'pos_enable_shifts'        => '1',
            'pos_auto_open_drawer'     => '1',
            'pos_barcode_sound'        => '1',
            'default_tax_rate'         => '15',
        ];

        foreach ($defaults as $k => $v) {
            CompanySetting::set($k, $v, 'operational');
        }
    }
}

