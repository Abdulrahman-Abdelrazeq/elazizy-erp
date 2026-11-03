<?php

namespace Modules\FinancialReports\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Accounting\Entities\Account;
use Modules\FinancialReports\Entities\FinancialBudget;

class FinancialReportsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Module 8: Financial & Analytical Reports.
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        $tenantId = $tenant ? $tenant->id : 1;
        $user = User::first();
        $userId = $user ? $user->id : 1;
        $branch = Branch::first();
        $branchId = $branch ? $branch->id : 1;

        $year = (int) date('Y');

        $budgetPlan = [
            '4101' => ['target' => 2500000.00, 'notes' => 'المستهدف البيعي السنوي للمبيعات العامة'],
            '4102' => ['target' => 500000.00,  'notes' => 'مستهدف إيرادات الخدمات والمقاولات'],
            '5101' => ['target' => 1500000.00, 'notes' => 'سقف تكلفة البضاعة المباعة التقديرية'],
            '5102' => ['target' => 300000.00,  'notes' => 'موازنة الرواتب والأجور والبدلات السنوية'],
            '5103' => ['target' => 120000.00,  'notes' => 'موازنة الإيجار والمرافق والكهرباء والمياه'],
            '5104' => ['target' => 80000.00,   'notes' => 'موازنة المصروفات العمومية والإدارية والضيافة'],
        ];

        foreach ($budgetPlan as $code => $data) {
            $account = Account::where('tenant_id', $tenantId)->where('code', $code)->first();
            if ($account) {
                FinancialBudget::updateOrCreate(
                    [
                        'tenant_id'   => $tenantId,
                        'fiscal_year' => $year,
                        'account_id'  => $account->id,
                        'period_type' => 'annual',
                    ],
                    [
                        'branch_id'     => $branchId,
                        'target_amount' => $data['target'],
                        'notes'         => $data['notes'],
                        'created_by'    => $userId,
                    ]
                );
            }
        }
    }
}

