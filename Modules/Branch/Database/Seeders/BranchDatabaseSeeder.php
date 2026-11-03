<?php

namespace Modules\Branch\Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchTransfer;
use Modules\Branch\Entities\BranchTransferItem;
use Modules\Branch\Entities\BranchUser;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;

class BranchDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Module 11: Multi-Branch & Inter-Branch Transfers.
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

        $user1 = User::first();
        $user2 = User::skip(1)->first() ?? $user1;

        // 1. Seed / Update Branches
        $hqBranch = Branch::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'BR-01'],
            [
                'name'                    => 'المقر الرئيسي - الرياض (HQ)',
                'branch_type'             => 'hq',
                'phone'                   => '+966 11 456 7890',
                'email'                   => 'riyadh-hq@elazizy-group.com',
                'manager_name'            => 'م. أحمد العزيزي',
                'commercial_registration' => '1010892341',
                'tax_number'              => '310293847500003',
                'monthly_budget'          => 250000.00,
                'address'                 => 'طريق الملك فهد - حي العليا - برج النخبة',
                'city'                    => 'الرياض',
                'district'                => 'حي العليا',
                'is_main'                 => true,
                'is_active'               => true,
                'parent_id'               => null,
            ]
        );

        $jeddahBranch = Branch::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'BR-02'],
            [
                'name'                    => 'فرع المنطقة الغربية - جدة (تجزئة ومعرض)',
                'branch_type'             => 'retail',
                'phone'                   => '+966 12 654 3210',
                'email'                   => 'jeddah@elazizy-group.com',
                'manager_name'            => 'أ. فيصل الجهني',
                'commercial_registration' => '4030123456',
                'tax_number'              => '310293847500003',
                'monthly_budget'          => 120000.00,
                'address'                 => 'طريق المدينة المنورة - حي الروضة',
                'city'                    => 'جدة',
                'district'                => 'حي الروضة',
                'is_main'                 => false,
                'is_active'               => true,
                'parent_id'               => $hqBranch->id,
            ]
        );

        $dammamBranch = Branch::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'BR-03'],
            [
                'name'                    => 'مركز التوزيع الإقليمي - المنطقة الشرقية (الدمام)',
                'branch_type'             => 'distribution_center',
                'phone'                   => '+966 13 833 9900',
                'email'                   => 'dammam-dc@elazizy-group.com',
                'manager_name'            => 'أ. خالد الشمري',
                'commercial_registration' => '2050987654',
                'tax_number'              => '310293847500003',
                'monthly_budget'          => 180000.00,
                'address'                 => 'المنطقة الصناعية الأولى - مستودعات اللوجستيات',
                'city'                    => 'الدمام',
                'district'                => 'الخالدية الصناعية',
                'is_main'                 => false,
                'is_active'               => true,
                'parent_id'               => $hqBranch->id,
            ]
        );

        $qassimBranch = Branch::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'BR-04'],
            [
                'name'                    => 'فرع مبيعات الجملة والتوريدات - القصيم',
                'branch_type'             => 'wholesale',
                'phone'                   => '+966 16 385 4400',
                'email'                   => 'qassim@elazizy-group.com',
                'manager_name'            => 'أ. سليمان التويجري',
                'commercial_registration' => '1131056789',
                'tax_number'              => '310293847500003',
                'monthly_budget'          => 85000.00,
                'address'                 => 'طريق الملك عبد العزيز - حي الصفراء',
                'city'                    => 'بريدة',
                'district'                => 'حي الصفراء',
                'is_main'                 => false,
                'is_active'               => true,
                'parent_id'               => $hqBranch->id,
            ]
        );

        // 2. Ensure Branch Stores
        $storeHq = Store::firstOrCreate(
            ['tenant_id' => $tenantId, 'branch_id' => $hqBranch->id],
            ['name' => 'المستودع المركزي - الرياض', 'code' => 'STR-01', 'is_active' => true]
        );

        $storeJeddah = Store::firstOrCreate(
            ['tenant_id' => $tenantId, 'branch_id' => $jeddahBranch->id],
            ['name' => 'مستودع فرع جدة', 'code' => 'STR-02', 'is_active' => true]
        );

        $storeDammam = Store::firstOrCreate(
            ['tenant_id' => $tenantId, 'branch_id' => $dammamBranch->id],
            ['name' => 'مستودع مركز التوزيع اللوجستي بالدمام', 'code' => 'STR-03', 'is_active' => true]
        );

        $storeQassim = Store::firstOrCreate(
            ['tenant_id' => $tenantId, 'branch_id' => $qassimBranch->id],
            ['name' => 'مستودع القصيم المركزي', 'code' => 'STR-04', 'is_active' => true]
        );

        // 3. Branch Users Assignment
        if ($user1) {
            BranchUser::updateOrCreate(
                ['tenant_id' => $tenantId, 'branch_id' => $hqBranch->id, 'user_id' => $user1->id],
                ['role' => 'admin', 'is_default' => true, 'can_switch' => true]
            );

            BranchUser::updateOrCreate(
                ['tenant_id' => $tenantId, 'branch_id' => $jeddahBranch->id, 'user_id' => $user1->id],
                ['role' => 'manager', 'is_default' => false, 'can_switch' => true]
            );

            BranchUser::updateOrCreate(
                ['tenant_id' => $tenantId, 'branch_id' => $dammamBranch->id, 'user_id' => $user1->id],
                ['role' => 'admin', 'is_default' => false, 'can_switch' => true]
            );
        }

        if ($user2 && $user2->id !== $user1?->id) {
            BranchUser::updateOrCreate(
                ['tenant_id' => $tenantId, 'branch_id' => $jeddahBranch->id, 'user_id' => $user2->id],
                ['role' => 'staff', 'is_default' => true, 'can_switch' => false]
            );
        }

        // 4. Ensure initial stock for items in HQ Store
        $items = Item::where('tenant_id', $tenantId)->get();
        foreach ($items as $item) {
            ItemStock::firstOrCreate(
                ['tenant_id' => $tenantId, 'store_id' => $storeHq->id, 'item_id' => $item->id],
                ['quantity' => 150.00, 'avg_cost' => $item->avg_cost ?: ($item->buy_price ?: 50.00)]
            );
            ItemStock::firstOrCreate(
                ['tenant_id' => $tenantId, 'store_id' => $storeDammam->id, 'item_id' => $item->id],
                ['quantity' => 80.00, 'avg_cost' => $item->avg_cost ?: ($item->buy_price ?: 50.00)]
            );
        }

        // 5. Seed Inter-Branch Transfers
        $firstItem = $items->first();
        $secondItem = $items->skip(1)->first() ?? $firstItem;

        if ($firstItem) {
            // Transfer 1: Received (HQ -> Jeddah)
            $trf1 = BranchTransfer::updateOrCreate(
                ['tenant_id' => $tenantId, 'transfer_number' => 'TRF-2026-0001'],
                [
                    'from_branch_id'    => $hqBranch->id,
                    'to_branch_id'      => $jeddahBranch->id,
                    'from_store_id'     => $storeHq->id,
                    'to_store_id'       => $storeJeddah->id,
                    'status'            => 'received',
                    'transfer_date'     => Carbon::now()->subDays(5)->toDateString(),
                    'dispatch_date'     => Carbon::now()->subDays(4),
                    'received_date'     => Carbon::now()->subDays(3),
                    'shipping_company'  => 'أسطول شاحنات العزيزي اللوجستي',
                    'driver_name'       => 'سعيد الغامدي',
                    'vehicle_plate'     => 'د هـ و 5521',
                    'tracking_number'   => 'TRK-9812450',
                    'notes'             => 'تم تسليم الشحنة كاملة ومطابقة للمواصفات',
                    'total_items_count' => 2,
                    'total_cost'        => 3500.00,
                    'created_by'        => $user1?->id,
                    'dispatched_by'     => $user1?->id,
                    'received_by'       => $user2?->id,
                ]
            );

            BranchTransferItem::updateOrCreate(
                ['transfer_id' => $trf1->id, 'item_id' => $firstItem->id],
                [
                    'tenant_id'         => $tenantId,
                    'sent_quantity'     => 20.00,
                    'received_quantity' => 20.00,
                    'unit_cost'         => (float) ($firstItem->avg_cost ?: 100.00),
                    'total_cost'        => 20.00 * (float) ($firstItem->avg_cost ?: 100.00),
                ]
            );

            if ($secondItem && $secondItem->id !== $firstItem->id) {
                BranchTransferItem::updateOrCreate(
                    ['transfer_id' => $trf1->id, 'item_id' => $secondItem->id],
                    [
                        'tenant_id'         => $tenantId,
                        'sent_quantity'     => 15.00,
                        'received_quantity' => 15.00,
                        'unit_cost'         => (float) ($secondItem->avg_cost ?: 100.00),
                        'total_cost'        => 15.00 * (float) ($secondItem->avg_cost ?: 100.00),
                    ]
                );
            }

            // Transfer 2: In-Transit (HQ -> Dammam DC)
            $trf2 = BranchTransfer::updateOrCreate(
                ['tenant_id' => $tenantId, 'transfer_number' => 'TRF-2026-0002'],
                [
                    'from_branch_id'    => $hqBranch->id,
                    'to_branch_id'      => $dammamBranch->id,
                    'from_store_id'     => $storeHq->id,
                    'to_store_id'       => $storeDammam->id,
                    'status'            => 'dispatched',
                    'transfer_date'     => Carbon::now()->subDay()->toDateString(),
                    'dispatch_date'     => Carbon::now()->subHours(10),
                    'received_date'     => null,
                    'shipping_company'  => 'شركة ناقل إكسبريس',
                    'driver_name'       => 'محمد القحطاني',
                    'vehicle_plate'     => 'ص ر ق 8712',
                    'tracking_number'   => 'NQL-8842109',
                    'notes'             => 'شحنة إمداد عاجلة لمركز توزيع الشرقية - البضاعة في الطريق',
                    'total_items_count' => 1,
                    'total_cost'        => 2500.00,
                    'created_by'        => $user1?->id,
                    'dispatched_by'     => $user1?->id,
                    'received_by'       => null,
                ]
            );

            BranchTransferItem::updateOrCreate(
                ['transfer_id' => $trf2->id, 'item_id' => $firstItem->id],
                [
                    'tenant_id'         => $tenantId,
                    'sent_quantity'     => 25.00,
                    'received_quantity' => 0.00,
                    'unit_cost'         => (float) ($firstItem->avg_cost ?: 100.00),
                    'total_cost'        => 25.00 * (float) ($firstItem->avg_cost ?: 100.00),
                ]
            );

            // Transfer 3: Draft (Dammam DC -> Qassim Wholesale)
            $trf3 = BranchTransfer::updateOrCreate(
                ['tenant_id' => $tenantId, 'transfer_number' => 'TRF-2026-0003'],
                [
                    'from_branch_id'    => $dammamBranch->id,
                    'to_branch_id'      => $qassimBranch->id,
                    'from_store_id'     => $storeDammam->id,
                    'to_store_id'       => $storeQassim->id,
                    'status'            => 'draft',
                    'transfer_date'     => Carbon::now()->toDateString(),
                    'dispatch_date'     => null,
                    'received_date'     => null,
                    'shipping_company'  => 'أسطول شاحنات العزيزي',
                    'driver_name'       => 'عبدالله الحربي',
                    'vehicle_plate'     => 'ب ن ط 3341',
                    'tracking_number'   => 'TRK-9812499',
                    'notes'             => 'طلب تحويل بضائع أسبوعي مجدول لمعرض القصيم',
                    'total_items_count' => 1,
                    'total_cost'        => 1200.00,
                    'created_by'        => $user1?->id,
                    'dispatched_by'     => null,
                    'received_by'       => null,
                ]
            );

            BranchTransferItem::updateOrCreate(
                ['transfer_id' => $trf3->id, 'item_id' => $firstItem->id],
                [
                    'tenant_id'         => $tenantId,
                    'sent_quantity'     => 12.00,
                    'received_quantity' => 0.00,
                    'unit_cost'         => (float) ($firstItem->avg_cost ?: 100.00),
                    'total_cost'        => 12.00 * (float) ($firstItem->avg_cost ?: 100.00),
                ]
            );
        }
    }
}
