<?php

namespace Modules\Quality\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\CRM\Entities\Client;
use Modules\HR\Entities\Employee;
use Modules\Inventory\Entities\Category;
use Modules\Inventory\Entities\Item;
use Modules\Manufacturing\Entities\ManufacturingOrder;
use Modules\Project\Entities\Project;
use Modules\Quality\Entities\QualityCapa;
use Modules\Quality\Entities\QualityInspection;
use Modules\Quality\Entities\QualityInspectionResult;
use Modules\Quality\Entities\QualityNcr;
use Modules\Quality\Entities\QualityTemplate;
use Modules\Quality\Entities\QualityTemplateItem;

class QualityDatabaseSeeder extends Seeder
{
    /**
     * Seed Quality Control & Inspection records, templates, checkpoints, inspections, NCRs, and CAPAs.
     */
    public function run(): void
    {
        $tenant = Tenant::first() ?? Tenant::create([
            'name'      => 'مؤسسة العزيزي للمقاولات والتجارة',
            'domain'    => 'elazizy.local',
            'is_active' => true,
        ]);
        $tenantId = $tenant->id;

        $branch = Branch::where('tenant_id', $tenantId)->first() ?? Branch::create([
            'tenant_id' => $tenantId,
            'name'      => 'الفرع الرئيسي - الرياض',
            'is_active' => true,
        ]);
        $branchId = $branch->id;

        $user = User::where('tenant_id', $tenantId)->first() ?? User::first();
        $userId = $user?->id ?? 1;

        // Employees / Inspectors
        $employees = Employee::where('tenant_id', $tenantId)->get();
        $inspector1 = $employees->first() ?? Employee::create([
            'tenant_id'  => $tenantId,
            'name'       => 'أحمد الزهراني',
            'emp_code'   => 'EMP-QC-01',
            'email'      => 'qc.zahrani@elazizy.local',
            'status'     => 'active',
            'hire_date'  => Carbon::now()->subYears(2),
        ]);
        $inspector2 = $employees->skip(1)->first() ?? $inspector1;

        // Suppliers
        $suppliers = Client::where('tenant_id', $tenantId)->whereIn('type', ['supplier', 'both'])->get();
        $supplier1 = $suppliers->first() ?? Client::create([
            'tenant_id' => $tenantId,
            'name'      => 'شركة سابك للصناعات الأساسية والحديد',
            'type'      => 'supplier',
            'phone'     => '0112233445',
            'city'      => 'الرياض',
        ]);
        $supplier2 = $suppliers->skip(1)->first() ?? $supplier1;

        // Inventory Items & Categories
        $category = Category::where('tenant_id', $tenantId)->first();
        $itemSteel = Item::where('tenant_id', $tenantId)->where('name', 'like', '%حديد%')->first()
            ?? Item::where('tenant_id', $tenantId)->first()
            ?? Item::create([
                'tenant_id'   => $tenantId,
                'name'        => 'حديد تسليح عالي المقاومة ASTM A615 16mm',
                'sku'         => 'RAW-STL-16',
                'type'        => 'raw_material',
                'sale_price'  => 3200.00,
                'cost_price'  => 2800.00,
                'unit'        => 'طن',
                'is_active'   => true,
            ]);

        $itemConcrete = Item::where('tenant_id', $tenantId)->where('name', 'like', '%خرسانة%')->first()
            ?? Item::where('tenant_id', $tenantId)->skip(1)->first()
            ?? Item::create([
                'tenant_id'   => $tenantId,
                'name'        => 'خرسانة جاهزة مقاومة للكبريتات رتبة C35',
                'sku'         => 'RAW-CNC-35',
                'type'        => 'raw_material',
                'sale_price'  => 260.00,
                'cost_price'  => 210.00,
                'unit'        => 'متر مكعب',
                'is_active'   => true,
            ]);

        // Manufacturing Orders
        $mfgOrder = ManufacturingOrder::where('tenant_id', $tenantId)->first();

        // Projects
        $project = Project::where('tenant_id', $tenantId)->first();

        // =========================================================================
        // 1. Quality Inspection Templates & Checkpoints
        // =========================================================================

        // Template A: Incoming Raw Materials - Steel Rebar
        $tplIncoming = QualityTemplate::create([
            'tenant_id'   => $tenantId,
            'code'        => 'QT-IN-001',
            'name'        => 'معايير فحص استلام حديد التسليح الإنشائي (ASTM A615)',
            'type'        => 'incoming',
            'item_id'     => $itemSteel->id,
            'category_id' => $category?->id,
            'is_active'   => true,
            'description' => 'قائمة التحقق المعتمدة لفحص ومطابقة إرساليات حديد التسليح الواردة لمستودعات ومواقع الشركة.',
            'created_by'  => $userId,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplIncoming->id,
            'checkpoint_name'   => 'شهادة المنشأ والمطابقة المرفقة مع الإرسالية (MTR Certificate)',
            'standard_type'     => 'boolean',
            'expected_boolean'  => true,
            'inspection_method' => 'فحص مستندي وتدقيق رقم الصبة (Heat Number)',
            'is_mandatory'      => true,
            'sort_order'        => 1,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplIncoming->id,
            'checkpoint_name'   => 'القطر الاسمي لأسياخ الحديد (Nominal Diameter)',
            'standard_type'     => 'numeric_range',
            'min_value'         => 15.80,
            'max_value'         => 16.20,
            'target_value'      => 16.00,
            'unit_of_measure'   => 'مم (mm)',
            'inspection_method' => 'قياس بالقدمة ذات الورنية الإلكترونية (Vernier Caliper)',
            'is_mandatory'      => true,
            'sort_order'        => 2,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplIncoming->id,
            'checkpoint_name'   => 'إجهاد الشد والكسر المخبري (Tensile Strength)',
            'standard_type'     => 'numeric_range',
            'min_value'         => 620.00,
            'max_value'         => 750.00,
            'target_value'      => 650.00,
            'unit_of_measure'   => 'ميغاباسكال (MPa)',
            'inspection_method' => 'تقرير اختبار ماكينة الشد المحورية بالمختبر المعتمد',
            'is_mandatory'      => true,
            'sort_order'        => 3,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplIncoming->id,
            'checkpoint_name'   => 'خلو الأسطح والنتوءات من الصدأ والتآكل القشري والشروخ',
            'standard_type'     => 'boolean',
            'expected_boolean'  => true,
            'inspection_method' => 'فحص بصري دقيق لجميع حزم الشحنة مع التدقيق العشوائي',
            'is_mandatory'      => true,
            'sort_order'        => 4,
        ]);

        // Template B: Project Site Test - Slump & Temperature Test
        $tplSite = QualityTemplate::create([
            'tenant_id'   => $tenantId,
            'code'        => 'QT-ST-002',
            'name'        => 'بروتوكول اختبار هبوط وصب الخرسانة الجاهزة بالموقع (Slump Test)',
            'type'        => 'project_site',
            'item_id'     => $itemConcrete->id,
            'category_id' => $category?->id,
            'is_active'   => true,
            'description' => 'المعايير الهندسية لفحص قوام الخرسانة الطازجة وحرارتها قبل الإذن بالتفريغ والضخ بالمشروع.',
            'created_by'  => $userId,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplSite->id,
            'checkpoint_name'   => 'زمن وصول شاحنة الخلاطة بعد إضافة ماء الخلط بالمحطة',
            'standard_type'     => 'numeric_range',
            'min_value'         => 10.00,
            'max_value'         => 90.00,
            'target_value'      => 50.00,
            'unit_of_measure'   => 'دقيقة (min)',
            'inspection_method' => 'مراجعة بوليصة التسليم وساعة المغادرة من المحطة المركزية',
            'is_mandatory'      => true,
            'sort_order'        => 1,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplSite->id,
            'checkpoint_name'   => 'مقدار هبوط قمع الخرسانة (Slump Value)',
            'standard_type'     => 'numeric_range',
            'min_value'         => 100.00,
            'max_value'         => 150.00,
            'target_value'      => 125.00,
            'unit_of_measure'   => 'مم (mm)',
            'inspection_method' => 'اختبار مخروط أبرامز القياسي (ASTM C143)',
            'is_mandatory'      => true,
            'sort_order'        => 2,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplSite->id,
            'checkpoint_name'   => 'درجة حرارة الخرسانة الطازجة عند التفريغ',
            'standard_type'     => 'numeric_range',
            'min_value'         => 15.00,
            'max_value'         => 32.00,
            'target_value'      => 26.00,
            'unit_of_measure'   => 'درجة مئوية (°C)',
            'inspection_method' => 'مقياس حرارة رقمي مغموس في عمق الخلطة',
            'is_mandatory'      => true,
            'sort_order'        => 3,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplSite->id,
            'checkpoint_name'   => 'أخذ مكعبات اختبار مقاومة الضغط (6 مكعبات قياسية)',
            'standard_type'     => 'boolean',
            'expected_boolean'  => true,
            'inspection_method' => 'أخذ العينات وتعبئة القوالب وحفظها في حوض المعالجة الميداني',
            'is_mandatory'      => true,
            'sort_order'        => 4,
        ]);

        // Template C: In-Process Manufacturing QC
        $tplInProcess = QualityTemplate::create([
            'tenant_id'   => $tenantId,
            'code'        => 'QT-IP-003',
            'name'        => 'فحص جودة مراحل تشغيل وقص وتجميع المشغولات',
            'type'        => 'in_process',
            'item_id'     => null,
            'category_id' => $category?->id,
            'is_active'   => true,
            'description' => 'قائمة التحقق المرحلية لأعمال ورش النجارة والتشكيل والتجميع.',
            'created_by'  => $userId,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplInProcess->id,
            'checkpoint_name'   => 'دقة أبعاد ومقاسات القص بالليزر ومكائن CNC',
            'standard_type'     => 'numeric_range',
            'min_value'         => 99.80,
            'max_value'         => 100.20,
            'target_value'      => 100.00,
            'unit_of_measure'   => 'سم (cm)',
            'inspection_method' => 'شريط قياس ليزري دقيق ومقارنة مع المخطط التنفيذي',
            'is_mandatory'      => true,
            'sort_order'        => 1,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplInProcess->id,
            'checkpoint_name'   => 'جودة لصق شريط الحافة والتأكد من التصاق الغراء دون فجوات',
            'standard_type'     => 'boolean',
            'expected_boolean'  => true,
            'inspection_method' => 'فحص حسي وبصري لكافة زوايا وحواف القطع المشغولة',
            'is_mandatory'      => true,
            'sort_order'        => 2,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplInProcess->id,
            'checkpoint_name'   => 'استواء زوايا التجميع الهيكلي بزاوية قائمة 90 درجة',
            'standard_type'     => 'boolean',
            'expected_boolean'  => true,
            'inspection_method' => 'استخدام الزاوية المعدنية الدقيقة والميزان المائي',
            'is_mandatory'      => true,
            'sort_order'        => 3,
        ]);

        // Template D: Final Product QC
        $tplFinal = QualityTemplate::create([
            'tenant_id'   => $tenantId,
            'code'        => 'QT-FP-004',
            'name'        => 'الفحص النهائي ومعايير الإفراج وتسليم المنتج التام',
            'type'        => 'final_product',
            'item_id'     => null,
            'category_id' => $category?->id,
            'is_active'   => true,
            'description' => 'معايير الفحص الشامل للمنتجات قبل التغليف وإصدار شهادة الإفراج للمستودع التام أو العميل.',
            'created_by'  => $userId,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplFinal->id,
            'checkpoint_name'   => 'خلو الطلاء والدهان والأسطح الخارجية من أي خدوش أو فقاعات هوائية',
            'standard_type'     => 'boolean',
            'expected_boolean'  => true,
            'inspection_method' => 'فحص بصري تحت إضاءة بيضاء موحدة 1000 لوكس',
            'is_mandatory'      => true,
            'sort_order'        => 1,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplFinal->id,
            'checkpoint_name'   => 'سلاسة وكفاءة الحركة الميكانيكية للمفصلات والأدراج والمسارات',
            'standard_type'     => 'boolean',
            'expected_boolean'  => true,
            'inspection_method' => 'اختبار الفتح والإغلاق 5 مرات متتالية لكل درج ومفصلة',
            'is_mandatory'      => true,
            'sort_order'        => 2,
        ]);

        QualityTemplateItem::create([
            'tenant_id'         => $tenantId,
            'template_id'       => $tplFinal->id,
            'checkpoint_name'   => 'سلامة التغليف الواقي ووجود ملصقات الباركود ورقم الدفعة',
            'standard_type'     => 'boolean',
            'expected_boolean'  => true,
            'inspection_method' => 'مطابقة ملصق الباركود مع بطاقة أمر التشغيل',
            'is_mandatory'      => true,
            'sort_order'        => 3,
        ]);

        // =========================================================================
        // 2. Quality Inspections & Detailed Checkpoint Observations
        // =========================================================================

        // Inspection 1: Passed Incoming Raw Materials
        $insp1 = QualityInspection::create([
            'tenant_id'         => $tenantId,
            'branch_id'         => $branchId,
            'inspection_number' => QualityInspection::generateNumber($tenantId),
            'type'              => 'incoming',
            'template_id'       => $tplIncoming->id,
            'item_id'           => $itemSteel->id,
            'supplier_id'       => $supplier1->id,
            'batch_number'      => 'SABIC-HT-99214',
            'total_quantity'    => 50.000,
            'sample_size'       => 5.000,
            'passed_quantity'   => 50.000,
            'failed_quantity'   => 0.000,
            'status'            => 'passed',
            'inspector_id'      => $inspector1->id,
            'inspection_date'   => Carbon::now()->subDays(6)->toDateString(),
            'notes'             => 'تم استلام وفحص الشحنة بالكامل ومطابقة شهادة MTR الأصلية، الشحنة ممتازة وخالية من العيوب.',
            'created_by'        => $userId,
        ]);

        foreach ($tplIncoming->items as $item) {
            $obsVal = match ($item->checkpoint_name) {
                'شهادة المنشأ والمطابقة المرفقة مع الإرسالية (MTR Certificate)' => 'نعم (مطابق ومرفق الأصل)',
                'القطر الاسمي لأسياخ الحديد (Nominal Diameter)'                  => '16.02 مم',
                'إجهاد الشد والكسر المخبري (Tensile Strength)'                   => '658 ميغاباسكال',
                'خلو الأسطح والنتوءات من الصدأ والتآكل القشري والشروخ'          => 'نعم (نظيف وسليم)',
                default                                                        => 'مطابق',
            };
            QualityInspectionResult::create([
                'tenant_id'         => $tenantId,
                'inspection_id'     => $insp1->id,
                'template_item_id'  => $item->id,
                'checkpoint_name'   => $item->checkpoint_name,
                'standard_criteria' => $item->formatted_criteria,
                'observed_value'    => $obsVal,
                'is_passed'         => true,
                'defect_notes'      => null,
            ]);
        }

        // Inspection 2: Rejected Incoming Raw Material with Defect (Generates NCR)
        $insp2 = QualityInspection::create([
            'tenant_id'         => $tenantId,
            'branch_id'         => $branchId,
            'inspection_number' => QualityInspection::generateNumber($tenantId),
            'type'              => 'incoming',
            'template_id'       => $tplIncoming->id,
            'item_id'           => $itemSteel->id,
            'supplier_id'       => $supplier1->id,
            'batch_number'      => 'SABIC-HT-81109',
            'total_quantity'    => 40.000,
            'sample_size'       => 4.000,
            'passed_quantity'   => 0.000,
            'failed_quantity'   => 40.000,
            'status'            => 'rejected',
            'inspector_id'      => $inspector1->id,
            'inspection_date'   => Carbon::now()->subDays(4)->toDateString(),
            'notes'             => 'تم رفض الشحنة بالكامل لوجود تآكل وصدأ قشري كثيف ناتج عن سوء التخزين، بالإضافة لهبوط إجهاد الشد عن الحد الأدنى المسموح به في المواصفة ASTM A615.',
            'created_by'        => $userId,
        ]);

        foreach ($tplIncoming->items as $item) {
            $passed = true;
            $obsVal = 'مطابق';
            $defect = null;

            if (str_contains($item->checkpoint_name, 'إجهاد الشد')) {
                $passed = false;
                $obsVal = '585 ميغاباسكال (أقل من الحد الأدنى 620)';
                $defect = 'فشل العينات المخبرية الثلاث في تحقيق حد الخضوع ومقاومة الشد التصميمية.';
            } elseif (str_contains($item->checkpoint_name, 'الصدأ')) {
                $passed = false;
                $obsVal = 'لا (تآكل وصدأ قشري عميق)';
                $defect = 'وجود طبقة أكسدة عميقة وتقشر على سطح أسياخ الحزم الواردة.';
            } elseif (str_contains($item->checkpoint_name, 'القطر')) {
                $obsVal = '15.90 مم';
            } else {
                $obsVal = 'نعم (مرفق)';
            }

            QualityInspectionResult::create([
                'tenant_id'         => $tenantId,
                'inspection_id'     => $insp2->id,
                'template_item_id'  => $item->id,
                'checkpoint_name'   => $item->checkpoint_name,
                'standard_criteria' => $item->formatted_criteria,
                'observed_value'    => $obsVal,
                'is_passed'         => $passed,
                'defect_notes'      => $defect,
            ]);
        }

        // Inspection 3: Passed Site Quality Slump Test
        $insp3 = QualityInspection::create([
            'tenant_id'         => $tenantId,
            'branch_id'         => $branchId,
            'inspection_number' => QualityInspection::generateNumber($tenantId),
            'type'              => 'project_site',
            'template_id'       => $tplSite->id,
            'item_id'           => $itemConcrete->id,
            'supplier_id'       => $supplier2->id,
            'project_id'        => $project?->id,
            'batch_number'      => 'YAMAMA-MIX-7701',
            'total_quantity'    => 120.000,
            'sample_size'       => 6.000,
            'passed_quantity'   => 120.000,
            'failed_quantity'   => 0.000,
            'status'            => 'passed',
            'inspector_id'      => $inspector2->id,
            'inspection_date'   => Carbon::now()->subDays(2)->toDateString(),
            'notes'             => 'تم فحص 6 شاحنات خرسانة جاهزة لصب أعمدة وسقف الدور الأول بالمشروع، وجاءت نتائج الهبوط والحرارة ضمن الحدود المسموح بها كودياً.',
            'created_by'        => $userId,
        ]);

        foreach ($tplSite->items as $item) {
            $obsVal = match ($item->checkpoint_name) {
                'زمن وصول شاحنة الخلاطة بعد إضافة ماء الخلط بالمحطة' => '45 دقيقة',
                'مقدار هبوط قمع الخرسانة (Slump Value)'             => '120 مم',
                'درجة حرارة الخرسانة الطازجة عند التفريغ'           => '27 درجة مئوية',
                'أخذ مكعبات اختبار مقاومة الضغط (6 مكعبات قياسية)'  => 'نعم (تم أخذ 6 مكعبات ووضعها بحوض المعالجة)',
                default                                            => 'مطابق',
            };
            QualityInspectionResult::create([
                'tenant_id'         => $tenantId,
                'inspection_id'     => $insp3->id,
                'template_item_id'  => $item->id,
                'checkpoint_name'   => $item->checkpoint_name,
                'standard_criteria' => $item->formatted_criteria,
                'observed_value'    => $obsVal,
                'is_passed'         => true,
                'defect_notes'      => null,
            ]);
        }

        // Inspection 4: In-Process Manufacturing Inspection
        $insp4 = QualityInspection::create([
            'tenant_id'              => $tenantId,
            'branch_id'              => $branchId,
            'inspection_number'      => QualityInspection::generateNumber($tenantId),
            'type'                   => 'in_process',
            'template_id'            => $tplInProcess->id,
            'manufacturing_order_id' => $mfgOrder?->id,
            'batch_number'           => 'WO-BATCH-402',
            'total_quantity'         => 30.000,
            'sample_size'            => 5.000,
            'passed_quantity'        => 28.000,
            'failed_quantity'        => 2.000,
            'status'                 => 'conditionally_passed',
            'inspector_id'           => $inspector1->id,
            'inspection_date'        => Carbon::now()->subDay()->toDateString(),
            'notes'                  => 'اجتازت 28 وحدة مرحلة القص والتجميع بدقة عالية، ووحدتان بحاجة لإعادة لصق شريط الحافة.',
            'created_by'             => $userId,
        ]);

        foreach ($tplInProcess->items as $item) {
            $passed = !str_contains($item->checkpoint_name, 'شريط الحافة');
            $obsVal = match ($item->checkpoint_name) {
                'دقة أبعاد ومقاسات القص بالليزر ومكائن CNC'                   => '100.05 سم',
                'جودة لصق شريط الحافة والتأكد من التصاق الغراء دون فجوات'   => 'فجوة طفيفة بطرف قطعتين',
                'استواء زوايا التجميع الهيكلي بزاوية قائمة 90 درجة'        => 'نعم (90.0 درجة دقيقة)',
                default                                                     => 'مطابق',
            };
            QualityInspectionResult::create([
                'tenant_id'         => $tenantId,
                'inspection_id'     => $insp4->id,
                'template_item_id'  => $item->id,
                'checkpoint_name'   => $item->checkpoint_name,
                'standard_criteria' => $item->formatted_criteria,
                'observed_value'    => $obsVal,
                'is_passed'         => $passed,
                'defect_notes'      => $passed ? null : 'انخفاض ضغط بكرة شريط الحافة مما تسبب في عدم التصاق طرف الشريط.',
            ]);
        }

        // =========================================================================
        // 3. Non-Conformance Reports (NCR)
        // =========================================================================

        // NCR 1: Major/Critical defect linked to Inspection 2
        $ncr1 = QualityNcr::create([
            'tenant_id'             => $tenantId,
            'branch_id'             => $branchId,
            'ncr_number'            => QualityNcr::generateNumber($tenantId),
            'inspection_id'         => $insp2->id,
            'item_id'               => $itemSteel->id,
            'supplier_id'           => $supplier1->id,
            'title'                 => 'عدم مطابقة حديد تسليح 16مم لمواصفات الشد وظهور صدأ وتآكل قشري',
            'description'           => 'عند فحص إرسالية حديد التسليح رقم الباتش SABIC-HT-81109 بإجمالي 40 طن، تبين وجود صدأ قشري متعمق، وأظهرت الاختبارات الميكانيكية فشل العينات في تحقيق إجهاد الشد التصميمي الأدنى (585 MPa مقابل 620 MPa المطلوب).',
            'severity'              => 'critical',
            'root_cause_category'   => 'raw_material',
            'root_cause_analysis'   => 'سوء تخزين في الساحات المكشوفة لدى المورد وتعرض الحزم لرطوبة عالية وأمطار أثناء النقل البحري، إضافة إلى انخفاض نسبة الكربون في الصبة المصنعية.',
            'defect_quantity'       => 40.000,
            'estimated_loss_amount' => 112000.00,
            'disposition'           => 'return_to_vendor',
            'disposition_notes'     => 'تم رفض الإرسالية رسمياً وتحرير محضر رفض، ومخاطبة المورد لاسترداد الشحنة وتوريد كمية بديلة مطابقة خلال 48 ساعة على نفقته.',
            'status'                => 'under_investigation',
            'reported_by'           => $inspector1->id,
            'created_by'            => $userId,
        ]);

        // NCR 2: Minor Defect on In-process assembly
        $ncr2 = QualityNcr::create([
            'tenant_id'              => $tenantId,
            'branch_id'              => $branchId,
            'ncr_number'             => QualityNcr::generateNumber($tenantId),
            'inspection_id'          => $insp4->id,
            'manufacturing_order_id' => $mfgOrder?->id,
            'title'                  => 'انفصال موضعي في شريط الحافة لقطعتين من أسطح المكاتب',
            'description'            => 'لوحظ عدم التصاق طرف شريط القشاط (Edge Banding) بطول 8 سم في قطعتين نتيجة انخفاض حرارة سخان الغراء بماكينة الحواف عن 190 درجة مئوية.',
            'severity'               => 'minor',
            'root_cause_category'    => 'machinery',
            'root_cause_analysis'    => 'تراكم بقايا غراء محترق على حساس الحرارة لماكينة القشاط مما أعطى قراءة خاطئة وأدى لهبوط الحرارة الفعلية أثناء التشغيل.',
            'defect_quantity'        => 2.000,
            'estimated_loss_amount'  => 850.00,
            'disposition'            => 'rework',
            'disposition_notes'      => 'تنظيف فوهة الغراء وإعادة تمرير القطعتين على الماكينة بعد معايرة الحساس والحرارة.',
            'status'                 => 'closed',
            'reported_by'            => $inspector1->id,
            'closed_at'              => Carbon::now()->subHours(6),
            'created_by'             => $userId,
        ]);

        // =========================================================================
        // 4. Corrective & Preventive Actions (CAPA)
        // =========================================================================

        // CAPA 1: Corrective Action for NCR 1 (Raw Material Issue)
        QualityCapa::create([
            'tenant_id'              => $tenantId,
            'capa_number'            => QualityCapa::generateNumber($tenantId),
            'ncr_id'                 => $ncr1->id,
            'type'                   => 'corrective',
            'title'                  => 'اعتماد بروتوكول التدقيق المسبق لشهادات MTR وفحص الشحنات قبل الشحن من مستودعات المورد',
            'action_plan'            => "1. إلزام المورد بإرسال نسخة إلكترونية معتمدة من شهادة الفحص المخبري (MTR) قبل تحميل أي شاحنة.\n2. اشتراط تغطية حزم الحديد بأشرطة حماية وعوازل مانعة للرطوبة أثناء النقل.\n3. إجراء فحص ظاهري أولي عند بوابة الدخول بمصنعنا قبل السماح للشاحنة بالدخول لمنطقة التفريغ.\n4. تعليق أوامر الشراء المستقبلية للمورد في حال تكرار المخالفة.",
            'assigned_to'            => $inspector1->id,
            'due_date'               => Carbon::now()->addDays(14)->toDateString(),
            'completion_date'        => null,
            'effectiveness_criteria' => 'عدم تكرار رفض أي إرسالية حديد تسليح لنفس المورد لثلاث شحنات متتالية بنسبة مطابقة 100%.',
            'effectiveness_status'   => 'pending_evaluation',
            'status'                 => 'in_progress',
            'created_by'             => $userId,
        ]);

        // CAPA 2: Preventive Action for NCR 2 (Machinery Maintenance Issue)
        QualityCapa::create([
            'tenant_id'              => $tenantId,
            'capa_number'            => QualityCapa::generateNumber($tenantId),
            'ncr_id'                 => $ncr2->id,
            'type'                   => 'preventive',
            'title'                  => 'إدراج تنظيف ومعايرة حساس حرارة ماكينة القشاط ضمن خطة الصيانة الوقائية اليومية',
            'action_plan'            => "1. تركيب حساس رقمي إضافي مع جرس إنذار مرئي يوقف الماكينة إذا انخفضت حرارة الغراء عن 185 درجة مئوية.\n2. تحديث قائمة التحقق اليومية لمشغل الماكينة للتحقق من نظافة حوض الغراء وقراءة الحرارة في بداية كل وردية عمل.",
            'assigned_to'            => $inspector2->id,
            'due_date'               => Carbon::now()->subDays(2)->toDateString(),
            'completion_date'        => Carbon::now()->subDay()->toDateString(),
            'effectiveness_criteria' => 'فحص وتشغيل 100 قطعة متتالية دون تسجيل أي عيب في التصاق شريط الحواف.',
            'effectiveness_status'   => 'effective',
            'status'                 => 'verified_closed',
            'created_by'             => $userId,
        ]);
    }
}
