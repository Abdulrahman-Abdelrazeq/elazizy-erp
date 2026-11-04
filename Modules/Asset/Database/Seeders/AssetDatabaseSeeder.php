<?php

namespace Modules\Asset\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\CostCenter;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetCategory;
use Modules\Asset\Entities\AssetDepreciation;
use Modules\Asset\Entities\AssetDisposal;
use Modules\HR\Entities\Employee;
use Modules\Setting\Entities\Location;
use Modules\Treasury\Entities\Treasury;

class AssetDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Module 12: Fixed Assets & Depreciation.
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

        $user = User::first();
        $userId = $user ? $user->id : null;

        $branches = Branch::where('tenant_id', $tenantId)->get();
        $hqBranch = $branches->firstWhere('code', 'BR-01') ?? $branches->first();
        $jeddahBranch = $branches->firstWhere('code', 'BR-02') ?? $hqBranch;
        $dammamBranch = $branches->firstWhere('code', 'BR-03') ?? $hqBranch;

        $employees = Employee::where('tenant_id', $tenantId)->get();
        $emp1 = $employees->first();
        $emp2 = $employees->skip(1)->first() ?? $emp1;

        $locations = Location::all();
        $locHq = $locations->first();

        $costCenters = CostCenter::all();
        $ccGeneral = $costCenters->first();

        $treasury = Treasury::where('tenant_id', $tenantId)->first();

        // 1. Ensure Fixed Assets and Depreciation Accounts in Chart of Accounts
        $accFixedRoot = Account::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => '12'],
            ['name' => 'الأصول الثابتة', 'type' => 'asset', 'parent_id' => 1]
        );

        $accFurn = Account::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1201'],
            ['name' => 'الأثاث والتجهيزات المكتبية', 'type' => 'asset', 'parent_id' => $accFixedRoot->id]
        );

        $accIT = Account::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1202'],
            ['name' => 'الأجهزة والمعدات الإلكترونية وتقنية المعلومات', 'type' => 'asset', 'parent_id' => $accFixedRoot->id]
        );

        $accVeh = Account::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1203'],
            ['name' => 'السيارات وشاحنات النقل اللوجستي', 'type' => 'asset', 'parent_id' => $accFixedRoot->id]
        );

        $accMach = Account::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1204'],
            ['name' => 'الآلات ومعدات خطوط الإنتاج الصناعي', 'type' => 'asset', 'parent_id' => $accFixedRoot->id]
        );

        $accAccumDep = Account::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1209'],
            ['name' => 'مجمع إهلاك الأصول الثابتة', 'type' => 'asset', 'parent_id' => $accFixedRoot->id]
        );

        $accDepExpense = Account::firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => '5105'],
            ['name' => 'مصروف إهلاك الأصول الثابتة', 'type' => 'expense', 'parent_id' => 5]
        );

        // 2. Asset Categories
        $catFurn = AssetCategory::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CAT-FURN'],
            [
                'name'                            => 'الأثاث والتجهيزات المكتبية',
                'depreciation_method'             => 'straight_line',
                'depreciation_rate'               => 10.00,
                'useful_life_years'               => 10.00,
                'asset_account_id'                => $accFurn->id,
                'depreciation_account_id'         => $accAccumDep->id,
                'depreciation_expense_account_id' => $accDepExpense->id,
                'description'                     => 'مكاتب، كراسي، غرف اجتماعات وتجهيزات الديكور المكتبي',
                'is_active'                       => true,
            ]
        );

        $catIT = AssetCategory::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CAT-IT'],
            [
                'name'                            => 'أجهزة الحاسب الآلي وتكنولوجيا المعلومات',
                'depreciation_method'             => 'straight_line',
                'depreciation_rate'               => 25.00,
                'useful_life_years'               => 4.00,
                'asset_account_id'                => $accIT->id,
                'depreciation_account_id'         => $accAccumDep->id,
                'depreciation_expense_account_id' => $accDepExpense->id,
                'description'                     => 'الخوادم، محطات العمل، شبكات الاتصال، وأجهزة الكمبيوتر المحمولة',
                'is_active'                       => true,
            ]
        );

        $catVeh = AssetCategory::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CAT-VEH'],
            [
                'name'                            => 'أسطول السيارات وشاحنات النقل',
                'depreciation_method'             => 'straight_line',
                'depreciation_rate'               => 20.00,
                'useful_life_years'               => 5.00,
                'asset_account_id'                => $accVeh->id,
                'depreciation_account_id'         => $accAccumDep->id,
                'depreciation_expense_account_id' => $accDepExpense->id,
                'description'                     => 'شاحنات نقل البضائع بين الفروع، سيارات التوزيع، وسيارات الإدارة',
                'is_active'                       => true,
            ]
        );

        $catMach = AssetCategory::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CAT-MACH'],
            [
                'name'                            => 'الآلات ومعدات خطوط الإنتاج والتصنيع',
                'depreciation_method'             => 'declining_balance',
                'depreciation_rate'               => 15.00,
                'useful_life_years'               => 8.00,
                'asset_account_id'                => $accMach->id,
                'depreciation_account_id'         => $accAccumDep->id,
                'depreciation_expense_account_id' => $accDepExpense->id,
                'description'                     => 'ماكينات CNC، أفران الطلاء، وخطوط التجميع الآلية',
                'is_active'                       => true,
            ]
        );

        // 3. Fixed Assets Registry
        $assetsData = [
            [
                'asset_number'             => 'AST-2026-0001',
                'name'                     => 'خادم بيانات مركزي Dell PowerEdge R750 Enterprise',
                'category_id'              => $catIT->id,
                'branch_id'                => $hqBranch->id,
                'custodian_employee_id'    => $emp1?->id,
                'location_id'              => $locHq?->id,
                'cost_center_id'           => $ccGeneral?->id,
                'barcode'                  => 'AST-2026-0001',
                'serial_number'            => 'DL-R750-984210',
                'model_number'             => 'PowerEdge R750 Rack',
                'purchase_date'            => '2025-01-15',
                'in_service_date'          => '2025-02-01',
                'purchase_cost'            => 48000.00,
                'salvage_value'            => 3000.00,
                'depreciable_cost'         => 45000.00,
                'depreciation_method'      => 'straight_line',
                'depreciation_rate'        => 25.00,
                'useful_life_years'        => 4.00,
                'total_depreciated_amount' => 11250.00,
                'current_book_value'       => 36750.00,
                'status'                   => 'active',
                'warranty_expiry'          => '2028-01-15',
                'notes'                    => 'خادم إدارة قواعد البيانات المركزية للشركة وبرنامج ERP',
            ],
            [
                'asset_number'             => 'AST-2026-0002',
                'name'                     => 'شاحنة نقل مبردة إيسوزو Forward FVR حمولة 12 طن',
                'category_id'              => $catVeh->id,
                'branch_id'                => $dammamBranch->id,
                'custodian_employee_id'    => $emp2?->id,
                'location_id'              => null,
                'cost_center_id'           => $ccGeneral?->id,
                'barcode'                  => 'AST-2026-0002',
                'serial_number'            => 'ISZ-FVR-774921',
                'model_number'             => 'Forward FVR 2024',
                'purchase_date'            => '2025-03-20',
                'in_service_date'          => '2025-04-01',
                'purchase_cost'            => 240000.00,
                'salvage_value'            => 25000.00,
                'depreciable_cost'         => 215000.00,
                'depreciation_method'      => 'straight_line',
                'depreciation_rate'        => 20.00,
                'useful_life_years'        => 5.00,
                'total_depreciated_amount' => 35833.33,
                'current_book_value'       => 204166.67,
                'status'                   => 'active',
                'warranty_expiry'          => '2027-03-20',
                'notes'                    => 'شاحنة النقل اللوجستي الإقليمي بين مركز توزيع الدمام ومستودعات الفروع',
            ],
            [
                'asset_number'             => 'AST-2026-0003',
                'name'                     => 'ماكينة قص وتشكيل المعادن CNC ليزر ألمانية Trumpf',
                'category_id'              => $catMach->id,
                'branch_id'                => $hqBranch->id,
                'custodian_employee_id'    => $emp1?->id,
                'location_id'              => $locHq?->id,
                'cost_center_id'           => $ccGeneral?->id,
                'barcode'                  => 'AST-2026-0003',
                'serial_number'            => 'TRM-CNC-2024-88',
                'model_number'             => 'TruLaser 3030 Fiber',
                'purchase_date'            => '2025-06-10',
                'in_service_date'          => '2025-07-01',
                'purchase_cost'            => 380000.00,
                'salvage_value'            => 35000.00,
                'depreciable_cost'         => 345000.00,
                'depreciation_method'      => 'declining_balance',
                'depreciation_rate'        => 15.00,
                'useful_life_years'        => 8.00,
                'total_depreciated_amount' => 38000.00,
                'current_book_value'       => 342000.00,
                'status'                   => 'active',
                'warranty_expiry'          => '2027-06-10',
                'notes'                    => 'ماكينة خط الإنتاج والتصنيع الرئيسية بالورشة المركزية',
            ],
            [
                'asset_number'             => 'AST-2026-0004',
                'name'                     => 'سيارة مبيعات وتوزيع تويوتا هايلكس غمارة مزدوجة 4x4',
                'category_id'              => $catVeh->id,
                'branch_id'                => $jeddahBranch->id,
                'custodian_employee_id'    => $emp2?->id,
                'location_id'              => null,
                'cost_center_id'           => $ccGeneral?->id,
                'barcode'                  => 'AST-2026-0004',
                'serial_number'            => 'TYT-HLX-441029',
                'model_number'             => 'Hilux 2.8 D-4D',
                'purchase_date'            => '2025-08-01',
                'in_service_date'          => '2025-08-15',
                'purchase_cost'            => 118000.00,
                'salvage_value'            => 18000.00,
                'depreciable_cost'         => 100000.00,
                'depreciation_method'      => 'straight_line',
                'depreciation_rate'        => 20.00,
                'useful_life_years'        => 5.00,
                'total_depreciated_amount' => 15000.00,
                'current_book_value'       => 103000.00,
                'status'                   => 'active',
                'warranty_expiry'          => '2028-08-01',
                'notes'                    => 'سيارة توزيع مناديب مبيعات الجملة والتجزئة لفرع المنطقة الغربية',
            ],
            [
                'asset_number'             => 'AST-2026-0005',
                'name'                     => 'أثاث وتجهيزات قاعات اجتماعات الإدارة التنفيذية الفاخرة',
                'category_id'              => $catFurn->id,
                'branch_id'                => $hqBranch->id,
                'custodian_employee_id'    => $emp1?->id,
                'location_id'              => $locHq?->id,
                'cost_center_id'           => $ccGeneral?->id,
                'barcode'                  => 'AST-2026-0005',
                'serial_number'            => 'FRN-EXE-2025-01',
                'model_number'             => 'Luxury Boardroom Table & Chairs',
                'purchase_date'            => '2025-02-10',
                'in_service_date'          => '2025-02-20',
                'purchase_cost'            => 72000.00,
                'salvage_value'            => 6000.00,
                'depreciable_cost'         => 66000.00,
                'depreciation_method'      => 'straight_line',
                'depreciation_rate'        => 10.00,
                'useful_life_years'        => 10.00,
                'total_depreciated_amount' => 6600.00,
                'current_book_value'       => 65400.00,
                'status'                   => 'active',
                'warranty_expiry'          => '2027-02-10',
                'notes'                    => 'طاولة اجتماعات رئيسية تتسع لـ 20 شخصاً مع 20 كرسي جلد فاخر',
            ],
            [
                'asset_number'             => 'AST-2026-0006',
                'name'                     => 'محطة عمل هندسية وتصميم HP Z4 Workstation',
                'category_id'              => $catIT->id,
                'branch_id'                => $hqBranch->id,
                'custodian_employee_id'    => $emp1?->id,
                'location_id'              => $locHq?->id,
                'cost_center_id'           => $ccGeneral?->id,
                'barcode'                  => 'AST-2026-0006',
                'serial_number'            => 'HP-Z4-551092',
                'model_number'             => 'HP Z4 G4 Xeon',
                'purchase_date'            => '2025-05-15',
                'in_service_date'          => '2025-06-01',
                'purchase_cost'            => 22500.00,
                'salvage_value'            => 1500.00,
                'depreciable_cost'         => 21000.00,
                'depreciation_method'      => 'straight_line',
                'depreciation_rate'        => 25.00,
                'useful_life_years'        => 4.00,
                'total_depreciated_amount' => 4375.00,
                'current_book_value'       => 18125.00,
                'status'                   => 'active',
                'warranty_expiry'          => '2028-05-15',
                'notes'                    => 'جهاز مخصص لمهندس التصميمات الصناعية والمخططات الهندسية CAD/CAM',
            ],
        ];

        foreach ($assetsData as $data) {
            $data['tenant_id'] = $tenantId;
            $data['created_by'] = $userId;
            $asset = Asset::updateOrCreate(
                ['tenant_id' => $tenantId, 'asset_number' => $data['asset_number']],
                $data
            );

            // 4. Seed Depreciation Entries for Asset 1
            if ($asset->asset_number === 'AST-2026-0001') {
                $dep = AssetDepreciation::updateOrCreate(
                    ['tenant_id' => $tenantId, 'asset_id' => $asset->id, 'fiscal_period' => '2025-Annual'],
                    [
                        'depreciation_date'              => '2025-12-31',
                        'fiscal_year'                    => 2025,
                        'depreciation_amount'            => 11250.00,
                        'accumulated_depreciation_after' => 11250.00,
                        'book_value_after'               => 36750.00,
                        'is_posted'                      => false,
                        'notes'                          => 'إهلاك القسط السنوي الأول لعام 2025',
                        'created_by'                     => $userId,
                    ]
                );

                // Post to GL
                $dep->postToGeneralLedger($userId);
            }
        }

        // 5. Seed a Sample Disposed Asset
        $disposedAsset = Asset::updateOrCreate(
            ['tenant_id' => $tenantId, 'asset_number' => 'AST-2024-0099'],
            [
                'name'                     => 'طابعة مكتبية ليزر ملونة قديمة HP LaserJet Pro',
                'category_id'              => $catIT->id,
                'branch_id'                => $hqBranch->id,
                'custodian_employee_id'    => null,
                'location_id'              => $locHq?->id,
                'cost_center_id'           => $ccGeneral?->id,
                'barcode'                  => 'AST-2024-0099',
                'serial_number'            => 'HP-LJ-112233',
                'model_number'             => 'LaserJet 400',
                'purchase_date'            => '2022-01-10',
                'in_service_date'          => '2022-01-15',
                'purchase_cost'            => 4500.00,
                'salvage_value'            => 300.00,
                'depreciable_cost'         => 4200.00,
                'depreciation_method'      => 'straight_line',
                'depreciation_rate'        => 25.00,
                'useful_life_years'        => 4.00,
                'total_depreciated_amount' => 4200.00,
                'current_book_value'       => 0.00,
                'status'                   => 'sold',
                'warranty_expiry'          => null,
                'notes'                    => 'تم بيعها كأصل مستعمل بعد انتهاء عمرها الافتراضي',
                'created_by'               => $userId,
            ]
        );

        AssetDisposal::updateOrCreate(
            ['tenant_id' => $tenantId, 'asset_id' => $disposedAsset->id],
            [
                'disposal_type'          => 'sale',
                'disposal_date'          => '2026-02-15',
                'sale_price'             => 550.00,
                'book_value_at_disposal' => 300.00,
                'gain_or_loss'           => 250.00, // Capital Gain
                'treasury_id'            => $treasury?->id,
                'buyer_name'             => 'شركة التدوير والتقنية الحديثة',
                'notes'                  => 'بيع أصل مستعمل مع تحقيق أرباح رأسمالية 250 ر.س',
                'created_by'             => $userId,
            ]
        );
    }
}
