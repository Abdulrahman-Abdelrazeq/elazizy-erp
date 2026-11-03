<?php

namespace Modules\Manufacturing\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;
use Modules\Inventory\Entities\Unit;
use Modules\Manufacturing\Entities\Bom;
use Modules\Manufacturing\Entities\BomItem;
use Modules\Manufacturing\Entities\BomOperation;
use Modules\Manufacturing\Entities\ManufacturingOrder;
use Modules\Manufacturing\Entities\ManufacturingOrderItem;
use Modules\Manufacturing\Entities\Workstation;

class ManufacturingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Module 10: Manufacturing & BOM.
     */
    public function run(): void
    {
        $tenant = Tenant::first() ?? Tenant::create([
            'name'         => 'مؤسسة العزيزي للتجارة والمقاولات',
            'company_name' => 'شركة العزيزي للأنظمة والحلول المتكاملة',
            'domain'       => 'elazizy',
            'is_active'    => true,
        ]);

        $tenantId = $tenant->id;
        $branch = Branch::where('tenant_id', $tenantId)->first();
        $branchId = $branch ? $branch->id : null;
        $user = User::where('tenant_id', $tenantId)->first() ?? User::first();
        $userId = $user ? $user->id : null;
        $store = Store::where('tenant_id', $tenantId)->first();
        $storeId = $store ? $store->id : 1;
        $unit = Unit::first();
        $unitId = $unit ? $unit->id : null;

        // 1. Workstations (محطات العمل وخطوط الإنتاج)
        $wsData = [
            ['name' => 'خط التجميع والتركيب الآلي', 'code' => 'WS-001', 'hourly_cost' => 60.00, 'capacity_per_day' => 8.0, 'description' => 'تجميع الهيكل والأجزاء الميكانيكية'],
            ['name' => 'محطة القص والتشكيل الميكانيكي CNC', 'code' => 'WS-002', 'hourly_cost' => 45.00, 'capacity_per_day' => 8.0, 'description' => 'قص وتجهيز المواد الخام بدقة عالية'],
            ['name' => 'خط الطلاء والمعالجة السطحية', 'code' => 'WS-003', 'hourly_cost' => 35.00, 'capacity_per_day' => 8.0, 'description' => 'الدهان الإلكتروستاتيكي والمعالجة الكيميائية'],
            ['name' => 'محطة الفحص النهائي والجودة والتغليف', 'code' => 'WS-004', 'hourly_cost' => 25.00, 'capacity_per_day' => 8.0, 'description' => 'مراقبة الجودة والتعبئة والتغليف النهائي'],
        ];

        $workstations = [];
        foreach ($wsData as $ws) {
            $workstations[$ws['code']] = Workstation::updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => $ws['code']],
                [
                    'branch_id'        => $branchId,
                    'name'             => $ws['name'],
                    'hourly_cost'      => $ws['hourly_cost'],
                    'capacity_per_day' => $ws['capacity_per_day'],
                    'description'      => $ws['description'],
                    'is_active'        => true,
                ]
            );
        }

        // 2. Raw Materials Items (المواد الخام الداخلة في التصنيع)
        $rawItemsData = [
            ['sku' => 'RAW-MDF-18', 'name' => 'ألواح خشب معالج سماكة 18 مم', 'buy_price' => 45.00, 'avg_cost' => 45.00],
            ['sku' => 'RAW-STEEL-01', 'name' => 'قواعد وهياكل حديد صلب مدهون', 'buy_price' => 60.00, 'avg_cost' => 60.00],
            ['sku' => 'RAW-ACC-01', 'name' => 'طقم إكسسوارات ومسامير تثبيت هندسية', 'buy_price' => 15.00, 'avg_cost' => 15.00],
        ];

        $rawModels = [];
        foreach ($rawItemsData as $ritem) {
            $rawModels[$ritem['sku']] = Item::updateOrCreate(
                ['tenant_id' => $tenantId, 'sku' => $ritem['sku']],
                [
                    'name'       => $ritem['name'],
                    'buy_price'  => $ritem['buy_price'],
                    'avg_cost'   => $ritem['avg_cost'],
                    'sell_price' => $ritem['buy_price'] * 1.3,
                    'unit_id'    => $unitId,
                    'is_service' => false,
                    'is_active'  => true,
                ]
            );

            // Seed stock for raw materials
            ItemStock::updateOrCreate(
                ['tenant_id' => $tenantId, 'store_id' => $storeId, 'item_id' => $rawModels[$ritem['sku']]->id],
                ['quantity' => 500.000, 'avg_cost' => $ritem['avg_cost']]
            );
        }

        // 3. Finished Good Item (المنتج تام الصنع)
        $finishedGood = Item::updateOrCreate(
            ['tenant_id' => $tenantId, 'sku' => 'PROD-DESK-160'],
            [
                'name'               => 'مكتب خشبي تنفيذي فاخر 160 سم',
                'buy_price'          => 220.00,
                'avg_cost'           => 220.00,
                'sell_price'         => 450.00,
                'wholesale_price'    => 380.00,
                'minimum_sell_price' => 350.00,
                'unit_id'            => $unitId,
                'is_service'         => false,
                'is_active'          => true,
            ]
        );

        // 4. Bill of Materials (BOM)
        $bomNumber = 'BOM-' . date('Y') . '-0001';
        $bom = Bom::updateOrCreate(
            ['tenant_id' => $tenantId, 'bom_number' => $bomNumber],
            [
                'name'              => 'تركيبة تصنيع مكتب خشبي تنفيذي 160 سم',
                'product_id'        => $finishedGood->id,
                'output_quantity'   => 1.000,
                'unit_id'           => $unitId,
                'raw_material_cost' => 165.00,
                'labor_cost'        => 35.00,
                'overhead_cost'     => 20.00,
                'total_cost'        => 220.00,
                'cost_per_unit'     => 220.00,
                'is_active'         => true,
                'is_default'        => true,
                'notes'             => 'المواصفات القياسية المعتمدة للمكاتب التنفيذية الفاخرة',
                'created_by'        => $userId,
            ]
        );

        // BOM Items
        BomItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'bom_id' => $bom->id, 'item_id' => $rawModels['RAW-MDF-18']->id],
            ['quantity' => 2.000, 'unit_cost' => 45.00, 'scrap_percentage' => 0.00, 'total_cost' => 90.00]
        );
        BomItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'bom_id' => $bom->id, 'item_id' => $rawModels['RAW-STEEL-01']->id],
            ['quantity' => 1.000, 'unit_cost' => 60.00, 'scrap_percentage' => 0.00, 'total_cost' => 60.00]
        );
        BomItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'bom_id' => $bom->id, 'item_id' => $rawModels['RAW-ACC-01']->id],
            ['quantity' => 1.000, 'unit_cost' => 15.00, 'scrap_percentage' => 0.00, 'total_cost' => 15.00]
        );

        // BOM Operations
        BomOperation::updateOrCreate(
            ['tenant_id' => $tenantId, 'bom_id' => $bom->id, 'sequence_order' => 1],
            [
                'operation_name'        => 'مرحلة قص وتجهيز الألواح',
                'workstation_id'        => $workstations['WS-002']->id ?? null,
                'standard_time_minutes' => 20.00,
                'operation_cost'        => 15.00,
            ]
        );
        BomOperation::updateOrCreate(
            ['tenant_id' => $tenantId, 'bom_id' => $bom->id, 'sequence_order' => 2],
            [
                'operation_name'        => 'مرحلة التجميع والتثبيت النهائي',
                'workstation_id'        => $workstations['WS-001']->id ?? null,
                'standard_time_minutes' => 20.00,
                'operation_cost'        => 20.00,
            ]
        );

        $bom->recalculateCosts();

        // 5. Seed a Completed Manufacturing Order (أمر تشغيل مكتمل ومورد)
        $completedOrderNum = 'MO-' . date('Y') . '-0001';
        $completedOrder = ManufacturingOrder::updateOrCreate(
            ['tenant_id' => $tenantId, 'order_number' => $completedOrderNum],
            [
                'branch_id'                => $branchId,
                'bom_id'                   => $bom->id,
                'product_id'               => $finishedGood->id,
                'from_store_id'            => $storeId,
                'to_store_id'              => $storeId,
                'planned_quantity'         => 10.000,
                'produced_quantity'        => 10.000,
                'rejected_quantity'        => 0.000,
                'status'                   => 'completed',
                'order_date'               => Carbon::now()->subDays(5)->format('Y-m-d'),
                'expected_completion_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'actual_completion_date'   => Carbon::now()->subDays(2)->format('Y-m-d H:i:s'),
                'estimated_material_cost'  => 1650.00,
                'estimated_labor_cost'     => 350.00,
                'estimated_overhead_cost'  => 200.00,
                'estimated_total_cost'     => 2200.00,
                'actual_material_cost'     => 1650.00,
                'actual_labor_cost'        => 350.00,
                'actual_overhead_cost'     => 200.00,
                'actual_total_cost'        => 2200.00,
                'unit_cost'                => 220.00,
                'notes'                    => 'تشغيل طلبية توريد مكاتب للمركز الإداري',
                'created_by'               => $userId,
                'completed_by'             => $userId,
            ]
        );

        // Seed order items for the completed order
        ManufacturingOrderItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'order_id' => $completedOrder->id, 'item_id' => $rawModels['RAW-MDF-18']->id],
            ['planned_quantity' => 20.000, 'actual_quantity' => 20.000, 'unit_cost' => 45.00, 'total_cost' => 900.00]
        );
        ManufacturingOrderItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'order_id' => $completedOrder->id, 'item_id' => $rawModels['RAW-STEEL-01']->id],
            ['planned_quantity' => 10.000, 'actual_quantity' => 10.000, 'unit_cost' => 60.00, 'total_cost' => 600.00]
        );
        ManufacturingOrderItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'order_id' => $completedOrder->id, 'item_id' => $rawModels['RAW-ACC-01']->id],
            ['planned_quantity' => 10.000, 'actual_quantity' => 10.000, 'unit_cost' => 15.00, 'total_cost' => 150.00]
        );

        // Add finished goods stock from the completed order
        ItemStock::updateOrCreate(
            ['tenant_id' => $tenantId, 'store_id' => $storeId, 'item_id' => $finishedGood->id],
            ['quantity' => 10.000, 'avg_cost' => 220.00]
        );

        // 6. Seed an In-Progress Manufacturing Order (أمر تشغيل قيد التنفيذ)
        $inProgressOrderNum = 'MO-' . date('Y') . '-0002';
        $inProgressOrder = ManufacturingOrder::updateOrCreate(
            ['tenant_id' => $tenantId, 'order_number' => $inProgressOrderNum],
            [
                'branch_id'                => $branchId,
                'bom_id'                   => $bom->id,
                'product_id'               => $finishedGood->id,
                'from_store_id'            => $storeId,
                'to_store_id'              => $storeId,
                'planned_quantity'         => 5.000,
                'produced_quantity'        => 0.000,
                'rejected_quantity'        => 0.000,
                'status'                   => 'in_progress',
                'order_date'               => Carbon::now()->format('Y-m-d'),
                'expected_completion_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'estimated_material_cost'  => 825.00,
                'estimated_labor_cost'     => 175.00,
                'estimated_overhead_cost'  => 100.00,
                'estimated_total_cost'     => 1100.00,
                'actual_material_cost'     => 0.00,
                'actual_labor_cost'        => 175.00,
                'actual_overhead_cost'     => 100.00,
                'actual_total_cost'        => 0.00,
                'unit_cost'                => 220.00,
                'notes'                    => 'تشغيل دفعة مكاتب جديدة للمعرض الرئيسي',
                'created_by'               => $userId,
            ]
        );

        ManufacturingOrderItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'order_id' => $inProgressOrder->id, 'item_id' => $rawModels['RAW-MDF-18']->id],
            ['planned_quantity' => 10.000, 'actual_quantity' => 10.000, 'unit_cost' => 45.00, 'total_cost' => 450.00]
        );
        ManufacturingOrderItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'order_id' => $inProgressOrder->id, 'item_id' => $rawModels['RAW-STEEL-01']->id],
            ['planned_quantity' => 5.000, 'actual_quantity' => 5.000, 'unit_cost' => 60.00, 'total_cost' => 300.00]
        );
        ManufacturingOrderItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'order_id' => $inProgressOrder->id, 'item_id' => $rawModels['RAW-ACC-01']->id],
            ['planned_quantity' => 5.000, 'actual_quantity' => 5.000, 'unit_cost' => 15.00, 'total_cost' => 75.00]
        );
    }
}
