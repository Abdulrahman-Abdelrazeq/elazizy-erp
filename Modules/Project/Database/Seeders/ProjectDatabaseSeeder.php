<?php

namespace Modules\Project\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Accounting\Entities\CostCenter;
use Modules\CRM\Entities\Client;
use Modules\HR\Entities\Employee;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectBoqItem;
use Modules\Project\Entities\ProjectChangeOrder;
use Modules\Project\Entities\ProjectExtract;
use Modules\Project\Entities\ProjectMilestone;

class ProjectDatabaseSeeder extends Seeder
{
    /**
     * Seed realistic Contracting Projects, BOQ, Milestones, Change Orders & Progressive Extracts.
     */
    public function run(): void
    {
        $tenant = Tenant::first() ?? Tenant::create([
            'name'      => 'مؤسسة العزيزي للمقاولات والتجارة',
            'domain'    => 'elazizy.local',
            'is_active' => true,
        ]);

        $branch = Branch::first() ?? Branch::create([
            'tenant_id' => $tenant->id,
            'name'      => 'فرع الرياض الرئيسي',
            'is_active' => true,
        ]);

        $user = User::first();
        $userId = $user?->id ?? 1;

        $client1 = Client::where('type', 'customer')->orWhere('type', 'both')->first() ?? Client::create([
            'tenant_id' => $tenant->id,
            'name'      => 'شركة إعمار نجد للتطوير والاستثمار العقاري',
            'type'      => 'customer',
            'phone'     => '0501234567',
            'email'     => 'projects@eamaarnajd.sa',
            'city'      => 'الرياض',
        ]);

        $client2 = Client::where('id', '!=', $client1->id)->whereIn('type', ['customer', 'both'])->first() ?? Client::create([
            'tenant_id' => $tenant->id,
            'name'      => 'مجموعة المدى اللوجستية المتحدة',
            'type'      => 'customer',
            'phone'     => '0559876543',
            'email'     => 'contracts@almadaa.sa',
            'city'      => 'الدمام',
        ]);

        $projectManager = Employee::first();
        $pmId = $projectManager?->id ?? null;

        $costCenter = CostCenter::first();
        $costCenterId = $costCenter?->id ?? null;

        // -------------------------------------------------------------
        // PROJECT 1: برج العزيزي للأعمال والمكاتب التجارية بالرياض
        // -------------------------------------------------------------
        $project1 = Project::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'PRJ-2026-0001'],
            [
                'branch_id'                  => $branch->id,
                'client_id'                  => $client1->id,
                'cost_center_id'             => $costCenterId,
                'project_manager_id'         => $pmId,
                'name'                       => 'مشروع إنشاء برج العزيزي للأعمال والمكاتب بالرياض',
                'contract_number'            => 'CNT-2026-0881',
                'contract_date'              => Carbon::parse('2026-01-15'),
                'start_date'                 => Carbon::parse('2026-02-01'),
                'expected_end_date'          => Carbon::parse('2026-12-31'),
                'contract_value'             => 2500000.00,
                'revised_value'              => 2500000.00,
                'advance_payment_percentage' => 10.0,
                'advance_payment_amount'     => 250000.00,
                'retention_percentage'       => 5.0,
                'retention_amount'           => 125000.00,
                'completion_percentage'      => 44.80,
                'status'                     => 'in_progress',
                'location'                   => 'الرياض - حي العليا - تقاطع طريق الملك فهد',
                'description'                => 'أعمال المقاولة العامة لإنشاء برج مكاتب تجاري مكون من قبو ودور أرضي و8 أدوار متكررة متضمناً الأعمال المدنية والإنشائية والتشطيبات.',
                'created_by'                 => $userId,
            ]
        );

        // BOQ Items for Project 1
        $boqData = [
            [
                'item_code'         => 'BOQ-001',
                'description'       => 'حفر وتسوية الموقع العام وترحيل ناتج الحفر للمقالب العمومية',
                'unit'              => 'م3',
                'contract_quantity' => 4000.0,
                'unit_price'        => 35.00,
                'executed_quantity' => 4000.0,
                'completion'        => 100.0,
            ],
            [
                'item_code'         => 'BOQ-002',
                'description'       => 'توريد وصب خرسانة عادية للنظافة سمك 10 سم مقاومة للأملاح',
                'unit'              => 'م3',
                'contract_quantity' => 600.0,
                'unit_price'        => 260.00,
                'executed_quantity' => 600.0,
                'completion'        => 100.0,
            ],
            [
                'item_code'         => 'BOQ-003',
                'description'       => 'توريد وصب خرسانة مسلحة للقواعد والأساسات عيار 350 كجم/سم2',
                'unit'              => 'م3',
                'contract_quantity' => 1800.0,
                'unit_price'        => 420.00,
                'executed_quantity' => 1200.0,
                'completion'        => 66.67,
            ],
            [
                'item_code'         => 'BOQ-004',
                'description'       => 'توريد وتجهيز وتركيب حديد تسليح عالي المقاومة مواصفات سابك',
                'unit'              => 'طن',
                'contract_quantity' => 220.0,
                'unit_price'        => 3200.00,
                'executed_quantity' => 100.0,
                'completion'        => 45.45,
            ],
            [
                'item_code'         => 'BOQ-005',
                'description'       => 'توريد وصب خرسانة مسلحة للأعمدة والحوائط والأسقف المفرغة',
                'unit'              => 'م3',
                'contract_quantity' => 1200.0,
                'unit_price'        => 450.00,
                'executed_quantity' => 0.0,
                'completion'        => 0.0,
            ],
            [
                'item_code'         => 'BOQ-006',
                'description'       => 'أعمال العزل المائي والحراري المعتمد والمباني واللياسة',
                'unit'              => 'م2',
                'contract_quantity' => 2500.0,
                'unit_price'        => 81.60,
                'executed_quantity' => 0.0,
                'completion'        => 0.0,
            ],
        ];

        $createdBoq = [];
        foreach ($boqData as $b) {
            $createdBoq[$b['item_code']] = ProjectBoqItem::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'project_id'=> $project1->id,
                    'item_code' => $b['item_code']
                ],
                [
                    'description'          => $b['description'],
                    'unit'                 => $b['unit'],
                    'contract_quantity'    => $b['contract_quantity'],
                    'revised_quantity'     => $b['contract_quantity'],
                    'unit_price'           => $b['unit_price'],
                    'total_price'          => round($b['contract_quantity'] * $b['unit_price'], 2),
                    'executed_quantity'    => $b['executed_quantity'],
                    'completion_percentage'=> $b['completion'],
                ]
            );
        }

        // Milestones for Project 1
        $milestones = [
            [
                'title'         => 'تسليم الموقع وأعمال الحفر والأساسات الخرسانية',
                'start_date'    => Carbon::parse('2026-02-01'),
                'end_date'      => Carbon::parse('2026-04-30'),
                'weight'        => 25.0,
                'completion'    => 100.0,
                'status'        => 'completed',
                'deliverables'  => 'محاضر تسليم الحفر واختبارات تكسير مكعبات الخرسانة للقواعد',
            ],
            [
                'title'         => 'أعمال الهيكل الإنشائي والخرساني والأعمدة للبرج',
                'start_date'    => Carbon::parse('2026-05-01'),
                'end_date'      => Carbon::parse('2026-08-31'),
                'weight'        => 35.0,
                'completion'    => 55.0,
                'status'        => 'in_progress',
                'deliverables'  => 'محاضر صب الأسقف والأعمدة لجميع الأدوار',
            ],
            [
                'title'         => 'أعمال التأسيسات الكهروميكانيكية والتشطيبات المعمارية',
                'start_date'    => Carbon::parse('2026-09-01'),
                'end_date'      => Carbon::parse('2026-11-30'),
                'weight'        => 25.0,
                'completion'    => 0.0,
                'status'        => 'pending',
                'deliverables'  => 'شهادات اختبار ضغط شبكات السباكة وتشغيل التكييف المركزي',
            ],
            [
                'title'         => 'الفحص النهائي والتسليم الابتدائي وإطلاق التيار',
                'start_date'    => Carbon::parse('2026-12-01'),
                'end_date'      => Carbon::parse('2026-12-31'),
                'weight'        => 15.0,
                'completion'    => 0.0,
                'status'        => 'pending',
                'deliverables'  => 'محضر استلام ابتدائي موقّع من الاستشاري والمالك',
            ],
        ];

        foreach ($milestones as $m) {
            ProjectMilestone::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'project_id'=> $project1->id,
                    'title'     => $m['title']
                ],
                [
                    'start_date'            => $m['start_date'],
                    'end_date'              => $m['end_date'],
                    'weight_percentage'     => $m['weight'],
                    'completion_percentage' => $m['completion'],
                    'status'                => $m['status'],
                    'deliverables'          => $m['deliverables'],
                ]
            );
        }

        // Change Order for Project 1
        $co1 = ProjectChangeOrder::updateOrCreate(
            [
                'tenant_id'   => $tenant->id,
                'project_id'  => $project1->id,
                'order_number'=> 'CO-001'
            ],
            [
                'title'               => 'إضافة نظام عزل مائي إضافي متعدد الطبقات للقبو',
                'request_date'        => Carbon::parse('2026-03-10'),
                'approval_date'       => Carbon::parse('2026-03-15'),
                'amount_change'       => 75000.00,
                'time_extension_days' => 15,
                'status'              => 'approved',
                'justification'       => 'بناء على تقرير فحص التربة والمناسيب الجوفية المعتمد من الاستشاري المشرف لحماية القبو من الرشح المائي.',
                'approved_by'         => $userId,
            ]
        );

        // Recalculate Project 1 financials
        $project1->recalculateFinancials();

        // Progressive Extract 1 for Project 1 (Paid)
        $extract1 = ProjectExtract::updateOrCreate(
            [
                'tenant_id'     => $tenant->id,
                'project_id'    => $project1->id,
                'extract_number'=> 'EXT-PRJ-2026-0001-01'
            ],
            [
                'extract_type'              => 'progress',
                'period_from'               => Carbon::parse('2026-02-01'),
                'period_to'                 => Carbon::parse('2026-04-30'),
                'issue_date'                => Carbon::parse('2026-05-05'),
                'gross_amount'              => 1120000.00,
                'previous_gross_amount'     => 0.00,
                'current_gross_amount'      => 1120000.00,
                'advance_payment_deduction' => 112000.00,
                'retention_deduction'       => 56000.00,
                'other_deductions'          => 0.00,
                'net_subtotal'              => 952000.00,
                'tax_rate'                  => 15.00,
                'tax_amount'                => 142800.00,
                'net_amount'                => 1094800.00,
                'paid_amount'               => 1094800.00,
                'status'                    => 'paid',
                'consultant_engineer_name'  => 'م/ خالد عبد الرحمن (دار الهندسة والاستشارات)',
                'notes'                     => 'مستخلص جاري رقم 1 معتمد للأعمال المنجزة للأساسات والحفر والقواعد.',
                'created_by'                => $userId,
            ]
        );

        // Items for Extract 1
        $extract1Items = [
            [
                'boq_code'     => 'BOQ-001',
                'prev_qty'     => 0.0,
                'curr_qty'     => 4000.0,
                'unit_price'   => 35.00,
                'unit'         => 'م3',
                'desc'         => 'حفر وتسوية الموقع العام وترحيل المخلفات',
            ],
            [
                'boq_code'     => 'BOQ-002',
                'prev_qty'     => 0.0,
                'curr_qty'     => 600.0,
                'unit_price'   => 260.00,
                'unit'         => 'م3',
                'desc'         => 'توريد وصب خرسانة عادية للنظافة',
            ],
            [
                'boq_code'     => 'BOQ-003',
                'prev_qty'     => 0.0,
                'curr_qty'     => 1200.0,
                'unit_price'   => 420.00,
                'unit'         => 'م3',
                'desc'         => 'توريد وصب خرسانة مسلحة للقواعد',
            ],
            [
                'boq_code'     => 'BOQ-004',
                'prev_qty'     => 0.0,
                'curr_qty'     => 100.0,
                'unit_price'   => 3200.00,
                'unit'         => 'طن',
                'desc'         => 'توريد وتركيب حديد تسليح عالي المقاومة',
            ],
        ];

        foreach ($extract1Items as $ei) {
            $boqObj = $createdBoq[$ei['boq_code']] ?? null;
            $extract1->items()->updateOrCreate(
                [
                    'tenant_id'   => $tenant->id,
                    'boq_item_id' => $boqObj?->id
                ],
                [
                    'item_description'      => $ei['desc'],
                    'unit'                  => $ei['unit'],
                    'unit_price'            => $ei['unit_price'],
                    'previous_quantity'     => $ei['prev_qty'],
                    'current_quantity'      => $ei['curr_qty'],
                    'total_quantity'        => $ei['curr_qty'],
                    'current_amount'        => round($ei['curr_qty'] * $ei['unit_price'], 2),
                    'total_amount'          => round($ei['curr_qty'] * $ei['unit_price'], 2),
                    'completion_percentage' => $boqObj ? min(100.0, round(($ei['curr_qty'] / $boqObj->revised_quantity) * 100, 2)) : 100.0,
                ]
            );
        }

        // -------------------------------------------------------------
        // PROJECT 2: مجمع مستودعات ومستودع مركزي لوجستي بالدمام
        // -------------------------------------------------------------
        $project2 = Project::updateOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'PRJ-2026-0002'],
            [
                'branch_id'                  => $branch->id,
                'client_id'                  => $client2->id,
                'cost_center_id'             => $costCenterId,
                'project_manager_id'         => $pmId,
                'name'                       => 'مشروع مجمع مستودعات لوجستية ومبنى إدارة بالدمام',
                'contract_number'            => 'CNT-2026-0940',
                'contract_date'              => Carbon::parse('2026-03-01'),
                'start_date'                 => Carbon::parse('2026-03-15'),
                'expected_end_date'          => Carbon::parse('2026-10-31'),
                'contract_value'             => 1800000.00,
                'revised_value'              => 1800000.00,
                'advance_payment_percentage' => 10.0,
                'advance_payment_amount'     => 180000.00,
                'retention_percentage'       => 5.0,
                'retention_amount'           => 90000.00,
                'completion_percentage'      => 20.00,
                'status'                     => 'in_progress',
                'location'                   => 'المنطقة الشرقية - الدمام - المدينة الصناعية الثانية',
                'description'                => 'إنشاء هنجر ومستودع تخزين مركزي بنظام الهيكل المعدني الجملوني Steel Structure ومبنى إداري ملحق.',
                'created_by'                 => $userId,
            ]
        );

        $p2Boq = [
            [
                'item_code'         => 'P2-BOQ-01',
                'description'       => 'حفر وتسوية دك الأرضيات وتوريد طبقة إحلال بيس كورس',
                'unit'              => 'م2',
                'contract_quantity' => 10000.0,
                'unit_price'        => 36.00,
                'executed_quantity' => 10000.0,
                'completion'        => 100.0,
            ],
            [
                'item_code'         => 'P2-BOQ-02',
                'description'       => 'توريد وتصنيع وتركيب هيكل حديد جملوني جاهز Steel Structure',
                'unit'              => 'طن',
                'contract_quantity' => 180.0,
                'unit_price'        => 5800.00,
                'executed_quantity' => 0.0,
                'completion'        => 0.0,
            ],
            [
                'item_code'         => 'P2-BOQ-03',
                'description'       => 'توريد وتركيب ألواح صاج ساندوتش بانل معزول للأسقف والحوائط',
                'unit'              => 'م2',
                'contract_quantity' => 4500.0,
                'unit_price'        => 88.00,
                'executed_quantity' => 0.0,
                'completion'        => 0.0,
            ],
        ];

        foreach ($p2Boq as $pb) {
            ProjectBoqItem::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'project_id'=> $project2->id,
                    'item_code' => $pb['item_code']
                ],
                [
                    'description'          => $pb['description'],
                    'unit'                 => $pb['unit'],
                    'contract_quantity'    => $pb['contract_quantity'],
                    'revised_quantity'     => $pb['contract_quantity'],
                    'unit_price'           => $pb['unit_price'],
                    'total_price'          => round($pb['contract_quantity'] * $pb['unit_price'], 2),
                    'executed_quantity'    => $pb['executed_quantity'],
                    'completion_percentage'=> $pb['completion'],
                ]
            );
        }

        $project2->recalculateFinancials();
    }
}
