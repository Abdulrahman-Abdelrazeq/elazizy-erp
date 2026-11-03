<?php

namespace Modules\Accounting\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\CostCenter;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\JournalEntryLine;

class AccountingDatabaseSeeder extends Seeder
{
    /**
     * Run database seeds for Module 7: Accounting & General Ledger.
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        $tenantId = $tenant ? $tenant->id : 1;
        $user = User::first();
        $userId = $user ? $user->id : 1;
        $branch = Branch::first();
        $branchId = $branch ? $branch->id : 1;

        // 1. Seed Core Chart of Accounts (COA) Tree
        // Level 1: Roots
        $assets = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1'],
            ['name' => 'الأصول', 'name_en' => 'Assets', 'type' => 'asset', 'nature' => 'debit', 'level' => 1, 'is_parent' => true, 'is_active' => true]
        );

        $liabilities = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '2'],
            ['name' => 'الالتزامات', 'name_en' => 'Liabilities', 'type' => 'liability', 'nature' => 'credit', 'level' => 1, 'is_parent' => true, 'is_active' => true]
        );

        $equity = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '3'],
            ['name' => 'حقوق الملكية', 'name_en' => 'Equity', 'type' => 'equity', 'nature' => 'credit', 'level' => 1, 'is_parent' => true, 'is_active' => true]
        );

        $revenues = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '4'],
            ['name' => 'الإيرادات', 'name_en' => 'Revenues', 'type' => 'revenue', 'nature' => 'credit', 'level' => 1, 'is_parent' => true, 'is_active' => true]
        );

        $expenses = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '5'],
            ['name' => 'المصروفات', 'name_en' => 'Expenses', 'type' => 'expense', 'nature' => 'debit', 'level' => 1, 'is_parent' => true, 'is_active' => true]
        );

        // Level 2: Sub-Groups
        $currentAssets = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '11'],
            ['parent_id' => $assets->id, 'name' => 'الأصول المتداولة', 'name_en' => 'Current Assets', 'type' => 'asset', 'nature' => 'debit', 'level' => 2, 'is_parent' => true, 'is_active' => true]
        );

        $fixedAssets = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '12'],
            ['parent_id' => $assets->id, 'name' => 'الأصول الثابتة', 'name_en' => 'Fixed Assets', 'type' => 'asset', 'nature' => 'debit', 'level' => 2, 'is_parent' => true, 'is_active' => true]
        );

        $currentLiabilities = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '21'],
            ['parent_id' => $liabilities->id, 'name' => 'الالتزامات المتداولة', 'name_en' => 'Current Liabilities', 'type' => 'liability', 'nature' => 'credit', 'level' => 2, 'is_parent' => true, 'is_active' => true]
        );

        // Level 3 & 4: Sub-accounts & Analytical Leafs
        // Under Current Assets
        $cashGroup = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1101'],
            ['parent_id' => $currentAssets->id, 'name' => 'النقدية وما في حكمها', 'name_en' => 'Cash and Cash Equivalents', 'type' => 'asset', 'nature' => 'debit', 'level' => 3, 'is_parent' => true, 'is_active' => true]
        );

        $cashSafe = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '110101'],
            ['parent_id' => $cashGroup->id, 'name' => 'الخزينة الرئيسية - المقر', 'name_en' => 'Main Cash Safe', 'type' => 'asset', 'nature' => 'debit', 'level' => 4, 'is_parent' => false, 'is_active' => true]
        );

        $bankAccount = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '110102'],
            ['parent_id' => $cashGroup->id, 'name' => 'حساب البنك الأهلي المصري', 'name_en' => 'NBE Bank Account', 'type' => 'asset', 'nature' => 'debit', 'level' => 4, 'is_parent' => false, 'is_active' => true]
        );

        $receivablesGroup = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1102'],
            ['parent_id' => $currentAssets->id, 'name' => 'العملاء والمدينون', 'name_en' => 'Accounts Receivable', 'type' => 'asset', 'nature' => 'debit', 'level' => 3, 'is_parent' => true, 'is_active' => true]
        );

        $clientsAccount = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '110201'],
            ['parent_id' => $receivablesGroup->id, 'name' => 'حساب العملاء المحليين', 'name_en' => 'Local Clients', 'type' => 'asset', 'nature' => 'debit', 'level' => 4, 'is_parent' => false, 'is_active' => true]
        );

        $inventoryGroup = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1103'],
            ['parent_id' => $currentAssets->id, 'name' => 'مخزون البضائع', 'name_en' => 'Inventory', 'type' => 'asset', 'nature' => 'debit', 'level' => 3, 'is_parent' => true, 'is_active' => true]
        );

        $stockAccount = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '110301'],
            ['parent_id' => $inventoryGroup->id, 'name' => 'مخزون المستودع الرئيسي', 'name_en' => 'Main Warehouse Stock', 'type' => 'asset', 'nature' => 'debit', 'level' => 4, 'is_parent' => false, 'is_active' => true]
        );

        // Fixed Assets Leafs
        $furnitureAccount = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1201'],
            ['parent_id' => $fixedAssets->id, 'name' => 'الأثاث والتجهيزات المكتبية', 'name_en' => 'Furniture & Fixtures', 'type' => 'asset', 'nature' => 'debit', 'level' => 3, 'is_parent' => false, 'is_active' => true]
        );

        $equipmentAccount = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '1202'],
            ['parent_id' => $fixedAssets->id, 'name' => 'الأجهزة والمعدات الإلكترونية', 'name_en' => 'Computers & Equipment', 'type' => 'asset', 'nature' => 'debit', 'level' => 3, 'is_parent' => false, 'is_active' => true]
        );

        // Under Liabilities
        $payablesGroup = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '2101'],
            ['parent_id' => $currentLiabilities->id, 'name' => 'الموردون والدائنون', 'name_en' => 'Accounts Payable', 'type' => 'liability', 'nature' => 'credit', 'level' => 3, 'is_parent' => true, 'is_active' => true]
        );

        $suppliersAccount = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '210101'],
            ['parent_id' => $payablesGroup->id, 'name' => 'حساب الموردين المحليين', 'name_en' => 'Local Suppliers', 'type' => 'liability', 'nature' => 'credit', 'level' => 4, 'is_parent' => false, 'is_active' => true]
        );

        $accruedLiabilities = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '2102'],
            ['parent_id' => $currentLiabilities->id, 'name' => 'مصروفات مستحقة وأرصدة دائنة', 'name_en' => 'Accrued Expenses', 'type' => 'liability', 'nature' => 'credit', 'level' => 3, 'is_parent' => false, 'is_active' => true]
        );

        // Under Equity
        $capitalAccount = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '3101'],
            ['parent_id' => $equity->id, 'name' => 'رأس المال المدفوع', 'name_en' => 'Paid-in Capital', 'type' => 'equity', 'nature' => 'credit', 'level' => 2, 'is_parent' => false, 'is_active' => true]
        );

        $retainedEarnings = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '3102'],
            ['parent_id' => $equity->id, 'name' => 'الأرباح والخسائر المرحّلة', 'name_en' => 'Retained Earnings', 'type' => 'equity', 'nature' => 'credit', 'level' => 2, 'is_parent' => false, 'is_active' => true]
        );

        // Under Revenues
        $salesRevenue = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '4101'],
            ['parent_id' => $revenues->id, 'name' => 'إيرادات مبيعات البضائع', 'name_en' => 'Sales Revenue', 'type' => 'revenue', 'nature' => 'credit', 'level' => 2, 'is_parent' => false, 'is_active' => true]
        );

        $servicesRevenue = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '4102'],
            ['parent_id' => $revenues->id, 'name' => 'إيرادات خدمات وتوريدات', 'name_en' => 'Services Revenue', 'type' => 'revenue', 'nature' => 'credit', 'level' => 2, 'is_parent' => false, 'is_active' => true]
        );

        // Under Expenses
        $cogs = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '5101'],
            ['parent_id' => $expenses->id, 'name' => 'تكلفة البضاعة المباعة (COGS)', 'name_en' => 'Cost of Goods Sold', 'type' => 'expense', 'nature' => 'debit', 'level' => 2, 'is_parent' => false, 'is_active' => true]
        );

        $salariesExpense = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '5102'],
            ['parent_id' => $expenses->id, 'name' => 'مصروفات الرواتب والأجور', 'name_en' => 'Salaries & Wages', 'type' => 'expense', 'nature' => 'debit', 'level' => 2, 'is_parent' => false, 'is_active' => true]
        );

        $rentUtilitiesExpense = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '5103'],
            ['parent_id' => $expenses->id, 'name' => 'مصروفات الإيجار والمرافق', 'name_en' => 'Rent & Utilities', 'type' => 'expense', 'nature' => 'debit', 'level' => 2, 'is_parent' => false, 'is_active' => true]
        );

        $generalAdminExpense = Account::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => '5104'],
            ['parent_id' => $expenses->id, 'name' => 'مصروفات عمومية وإدارية', 'name_en' => 'General & Administrative', 'type' => 'expense', 'nature' => 'debit', 'level' => 2, 'is_parent' => false, 'is_active' => true]
        );

        // 2. Seed Default Cost Centers
        $ccAdmin = CostCenter::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CC-01'],
            ['name' => 'الإدارة العامة والمقر الرئيسي', 'is_active' => true, 'notes' => 'مركز تكلفة المركز الرئيسي']
        );

        $ccSales = CostCenter::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CC-02'],
            ['name' => 'قطاع المبيعات والتسويق', 'is_active' => true, 'notes' => 'مركز تكلفة عمليات المبيعات']
        );

        $ccOperations = CostCenter::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'CC-03'],
            ['name' => 'قطاع التشغيل والمستودعات', 'is_active' => true, 'notes' => 'مركز تكلفة التخزين واللوجستيات']
        );

        // 3. Seed Initial Balanced Opening Journal Entry (ENT-00001)
        $existingEntry = JournalEntry::where('tenant_id', $tenantId)->where('entry_number', 'ENT-00001')->first();
        if (!$existingEntry) {
            $entry = JournalEntry::create([
                'tenant_id'    => $tenantId,
                'branch_id'    => $branchId,
                'entry_number' => 'ENT-00001',
                'date'         => now()->startOfYear()->toDateString(),
                'reference'    => 'OPENING-2026',
                'description'  => 'القيد الافتتاحي لأرصدة بداية الفترة المالية وحقوق الملكية',
                'total_debit'  => 450000.00,
                'total_credit' => 450000.00,
                'status'       => 'posted',
                'source_type'  => 'opening',
                'created_by'   => $userId,
                'posted_by'    => $userId,
                'posted_at'    => now(),
            ]);

            // Debits: Total 450,000
            JournalEntryLine::create([
                'tenant_id'        => $tenantId,
                'journal_entry_id' => $entry->id,
                'account_id'       => $cashSafe->id,
                'cost_center_id'   => $ccAdmin->id,
                'debit'            => 50000.00,
                'credit'           => 0.00,
                'description'      => 'رصيد الخزينة النقدية الافتتاحي',
            ]);

            JournalEntryLine::create([
                'tenant_id'        => $tenantId,
                'journal_entry_id' => $entry->id,
                'account_id'       => $bankAccount->id,
                'cost_center_id'   => $ccAdmin->id,
                'debit'            => 250000.00,
                'credit'           => 0.00,
                'description'      => 'رصيد الحساب البنكي الجاري الافتتاحي',
            ]);

            JournalEntryLine::create([
                'tenant_id'        => $tenantId,
                'journal_entry_id' => $entry->id,
                'account_id'       => $stockAccount->id,
                'cost_center_id'   => $ccOperations->id,
                'debit'            => 100000.00,
                'credit'           => 0.00,
                'description'      => 'بضاعة أول المدة بالمستودع',
            ]);

            JournalEntryLine::create([
                'tenant_id'        => $tenantId,
                'journal_entry_id' => $entry->id,
                'account_id'       => $furnitureAccount->id,
                'cost_center_id'   => $ccAdmin->id,
                'debit'            => 50000.00,
                'credit'           => 0.00,
                'description'      => 'أثاث وتجهيزات المقر الإداري',
            ]);

            // Credits: Total 450,000
            JournalEntryLine::create([
                'tenant_id'        => $tenantId,
                'journal_entry_id' => $entry->id,
                'account_id'       => $capitalAccount->id,
                'cost_center_id'   => $ccAdmin->id,
                'debit'            => 0.00,
                'credit'           => 400000.00,
                'description'      => 'رأس المال المستثمر المدفوع',
            ]);

            JournalEntryLine::create([
                'tenant_id'        => $tenantId,
                'journal_entry_id' => $entry->id,
                'account_id'       => $suppliersAccount->id,
                'cost_center_id'   => $ccOperations->id,
                'debit'            => 0.00,
                'credit'           => 50000.00,
                'description'      => 'رصيد دائن افتتاحي للموردين',
            ]);

            // Recalculate balances for all touched accounts
            foreach ([$cashSafe, $bankAccount, $stockAccount, $furnitureAccount, $capitalAccount, $suppliersAccount] as $acc) {
                $acc->recalculateBalance();
            }
        }
    }
}

