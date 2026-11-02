<?php

namespace Modules\Inventory\Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\Brand;
use Modules\Inventory\Entities\Category;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;
use Modules\Inventory\Entities\Unit;
use Modules\Setting\Entities\Tax;

class InventoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Inventory module
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        if (!$tenant) {
            return;
        }

        // 1. Units of measurement
        $unitPcs = Unit::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'قطعة'],
            ['code' => 'PCS', 'is_active' => true]
        );

        $unitCtn = Unit::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'كرتونة'],
            ['code' => 'CTN', 'is_active' => true]
        );

        $unitKg = Unit::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'كيلوجرام'],
            ['code' => 'KG', 'is_active' => true]
        );

        $unitBox = Unit::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'علبة'],
            ['code' => 'BOX', 'is_active' => true]
        );

        // 2. Categories
        $catFood = Category::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'المواد الغذائية والاستهلاكية'],
            ['code' => 'FOOD-01', 'is_active' => true]
        );

        $catBeverages = Category::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'المشروبات والعصائر', 'parent_id' => $catFood->id],
            ['code' => 'BEV-01', 'is_active' => true]
        );

        $catClean = Category::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'المنظفات ومستلزمات النظافة'],
            ['code' => 'CLEAN-01', 'is_active' => true]
        );

        // 3. Brands
        $brandAzizy = Brand::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'العزيزي بريميوم'],
            ['is_active' => true]
        );

        $brandAlmarai = Brand::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'المراعي'],
            ['is_active' => true]
        );

        // 4. Stores / Warehouses
        $mainBranch = $tenant->mainBranch;
        $riyadhStore = Store::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'المستودع المركزي الرئيسي - الرياض'],
            [
                'code'        => 'STR-RYD-01',
                'branch_id'   => $mainBranch ? $mainBranch->id : null,
                'keeper_name' => 'محمد السالم',
                'phone'       => '0501112233',
                'address'     => 'الرياض - حي المناخ الصناعي',
                'is_main'     => true,
                'is_active'   => true,
            ]
        );

        $otherBranch = $tenant->branches()->where('is_main', false)->first();
        $jeddahStore = Store::firstOrCreate(
            ['tenant_id' => $tenant->id, 'name' => 'مستودع توزيع الغربية - جدة'],
            [
                'code'        => 'STR-JED-02',
                'branch_id'   => $otherBranch ? $otherBranch->id : null,
                'keeper_name' => 'أحمد الغامدي',
                'phone'       => '0502223344',
                'address'     => 'جدة - مستودعات طريق مكة',
                'is_main'     => false,
                'is_active'   => true,
            ]
        );

        $vat = Tax::where('tenant_id', $tenant->id)->where('is_default', true)->first();

        // 5. Initial Sample Items
        $item1 = Item::firstOrCreate(
            ['tenant_id' => $tenant->id, 'barcode' => '6281001122334'],
            [
                'name'                 => 'أرز بسمتي هندي فاخر 5 كجم',
                'sku'                  => 'RICE-5KG-01',
                'category_id'          => $catFood->id,
                'brand_id'             => $brandAzizy->id,
                'unit_id'              => $unitBox->id,
                'tax_id'               => $vat ? $vat->id : null,
                'buy_price'            => 42.00,
                'avg_cost'             => 42.00,
                'sell_price'           => 58.00,
                'wholesale_price'      => 52.00,
                'half_wholesale_price' => 54.00,
                'minimum_sell_price'   => 48.00,
                'min_quantity_alert'   => 15.00,
                'is_service'           => false,
                'has_expiration'       => true,
                'expire_date'          => now()->addYears(2),
                'is_active'            => true,
            ]
        );

        $item2 = Item::firstOrCreate(
            ['tenant_id' => $tenant->id, 'barcode' => '6281002233445'],
            [
                'name'                 => 'عصير برتقال طبيعي طازج 1.5 لتر',
                'sku'                  => 'JUICE-ORG-02',
                'category_id'          => $catBeverages->id,
                'brand_id'             => $brandAlmarai->id,
                'unit_id'              => $unitPcs->id,
                'tax_id'               => $vat ? $vat->id : null,
                'buy_price'            => 8.50,
                'avg_cost'             => 8.50,
                'sell_price'           => 12.00,
                'wholesale_price'      => 10.50,
                'half_wholesale_price' => 11.00,
                'minimum_sell_price'   => 9.50,
                'min_quantity_alert'   => 20.00,
                'is_service'           => false,
                'has_expiration'       => true,
                'expire_date'          => now()->addMonths(6),
                'is_active'            => true,
            ]
        );

        $item3 = Item::firstOrCreate(
            ['tenant_id' => $tenant->id, 'barcode' => '6281003344556'],
            [
                'name'                 => 'سائل غسيل الأطباق المركز 1 لتر',
                'sku'                  => 'DISH-1L-03',
                'category_id'          => $catClean->id,
                'brand_id'             => $brandAzizy->id,
                'unit_id'              => $unitPcs->id,
                'tax_id'               => $vat ? $vat->id : null,
                'buy_price'            => 11.00,
                'avg_cost'             => 11.00,
                'sell_price'           => 16.50,
                'wholesale_price'      => 14.00,
                'half_wholesale_price' => 15.00,
                'minimum_sell_price'   => 12.50,
                'min_quantity_alert'   => 10.00,
                'is_service'           => false,
                'has_expiration'       => false,
                'is_active'            => true,
            ]
        );

        // 6. Seed Stock Balances
        ItemStock::firstOrCreate(
            ['tenant_id' => $tenant->id, 'item_id' => $item1->id, 'store_id' => $riyadhStore->id],
            ['quantity' => 120.000, 'avg_cost' => 42.00]
        );

        ItemStock::firstOrCreate(
            ['tenant_id' => $tenant->id, 'item_id' => $item1->id, 'store_id' => $jeddahStore->id],
            ['quantity' => 45.000, 'avg_cost' => 42.00]
        );

        ItemStock::firstOrCreate(
            ['tenant_id' => $tenant->id, 'item_id' => $item2->id, 'store_id' => $riyadhStore->id],
            ['quantity' => 200.000, 'avg_cost' => 8.50]
        );

        ItemStock::firstOrCreate(
            ['tenant_id' => $tenant->id, 'item_id' => $item3->id, 'store_id' => $riyadhStore->id],
            ['quantity' => 80.000, 'avg_cost' => 11.00]
        );
    }
}

