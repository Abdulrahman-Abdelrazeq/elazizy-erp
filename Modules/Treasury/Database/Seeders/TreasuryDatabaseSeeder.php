<?php

namespace Modules\Treasury\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\CRM\Entities\Client;
use Modules\Treasury\Entities\ExpenseCategory;
use Modules\Treasury\Entities\Treasury;
use Modules\Treasury\Entities\TreasuryTransaction;
use Modules\Treasury\Entities\TreasuryTransfer;
use Modules\Treasury\Entities\TreasuryVoucher;

class TreasuryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Module 6 (Treasury & Expenses).
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        $tenantId = $tenant ? $tenant->id : 1;
        $user = User::first();
        $userId = $user ? $user->id : 1;
        $branch = Branch::first();
        $branchId = $branch ? $branch->id : 1;

        // 1. Seed Treasuries & Banks
        $safe1 = Treasury::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'الخزينة الرئيسية - الإدارة'],
            [
                'branch_id'       => $branchId,
                'type'            => 'cash',
                'opening_balance' => 50000.00,
                'current_balance' => 50000.00,
                'currency'        => 'EGP',
                'is_active'       => true,
                'is_default'      => true,
                'notes'           => 'الصندوق النقدي الرئيسي بالمقر',
                'created_by'      => $userId,
            ]
        );

        $bank1 = Treasury::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'البنك الأهلي المصري - حساب جاري'],
            [
                'branch_id'       => $branchId,
                'type'            => 'bank',
                'bank_name'       => 'البنك الأهلي المصري',
                'account_number'  => '10023456789012',
                'iban'            => 'EG38000200010023456789012',
                'swift'           => 'NBEGEGCX',
                'opening_balance' => 250000.00,
                'current_balance' => 250000.00,
                'currency'        => 'EGP',
                'is_active'       => true,
                'is_default'      => false,
                'notes'           => 'حساب الشركات والتحويلات الرسمية',
                'created_by'      => $userId,
            ]
        );

        $bank2 = Treasury::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'بنك مصر - حساب العمليات'],
            [
                'branch_id'       => $branchId,
                'type'            => 'bank',
                'bank_name'       => 'بنك مصر',
                'account_number'  => '20098765432100',
                'opening_balance' => 100000.00,
                'current_balance' => 100000.00,
                'currency'        => 'EGP',
                'is_active'       => true,
                'is_default'      => false,
                'notes'           => 'حساب المشتريات والموردين',
                'created_by'      => $userId,
            ]
        );

        $wallet1 = Treasury::updateOrCreate(
            ['tenant_id' => $tenantId, 'name' => 'محفظة إنستاباي وفودافون كاش'],
            [
                'branch_id'       => $branchId,
                'type'            => 'wallet',
                'account_number'  => '01099887766',
                'opening_balance' => 15000.00,
                'current_balance' => 15000.00,
                'currency'        => 'EGP',
                'is_active'       => true,
                'is_default'      => false,
                'notes'           => 'مخصصة للمبيعات السريعة وتحصيلات الأونلاين',
                'created_by'      => $userId,
            ]
        );

        // Record opening balance transactions if not recorded
        foreach ([$safe1, $bank1, $bank2, $wallet1] as $t) {
            TreasuryTransaction::firstOrCreate(
                ['tenant_id' => $tenantId, 'treasury_id' => $t->id, 'source_type' => 'opening'],
                [
                    'type'             => 'in',
                    'amount'           => $t->opening_balance,
                    'balance_before'   => 0.00,
                    'balance_after'    => $t->opening_balance,
                    'transaction_date' => now()->startOfMonth(),
                    'description'      => 'رصيد افتتاحي عند التأسيس',
                    'created_by'       => $userId,
                ]
            );
        }

        // 2. Seed Expense & Revenue Categories
        $categoriesData = [
            ['name' => 'إيجار المقرات والفروع', 'code' => 'EXP-RENT', 'type' => 'expense', 'description' => 'إيجارات المكاتب والمخازن والمعارض'],
            ['name' => 'رواتب ومستحقات الموظفين', 'code' => 'EXP-SAL', 'type' => 'expense', 'description' => 'أجور شهرية ومكافآت'],
            ['name' => 'فواتير كهرباء ومياه وغاز', 'code' => 'EXP-UTIL', 'type' => 'expense', 'description' => 'المرافق والخدمات الشهرية'],
            ['name' => 'تسويق وحملات إعلانية', 'code' => 'EXP-MKT', 'type' => 'expense', 'description' => 'إعلانات السوشيال ميديا والمطبوعات'],
            ['name' => 'صيانة وضيافة ومطبوعات', 'code' => 'EXP-MAIN', 'type' => 'expense', 'description' => 'نظافة وضيافة ومستهلكات مكتبية'],
            ['name' => 'شحن وتوصيل البضائع', 'code' => 'EXP-SHIP', 'type' => 'expense', 'description' => 'تكاليف شركات الشحن والنقل'],
            ['name' => 'إيرادات خدمات واستشارات', 'code' => 'REV-SERV', 'type' => 'revenue', 'description' => 'خدمات فنية ودعم متخصص'],
            ['name' => 'عوائد وأرباح مالية متنوعة', 'code' => 'REV-BANK', 'type' => 'revenue', 'description' => 'فروق عملة وعوائد بنكية'],
        ];

        foreach ($categoriesData as $c) {
            ExpenseCategory::updateOrCreate(
                ['tenant_id' => $tenantId, 'code' => $c['code']],
                [
                    'name'        => $c['name'],
                    'type'        => $c['type'],
                    'is_active'   => true,
                    'description' => $c['description'],
                ]
            );
        }

        $customer = Client::customers()->first();
        $supplier = Client::suppliers()->first();
        $catRent = ExpenseCategory::where('code', 'EXP-RENT')->first();
        $catUtil = ExpenseCategory::where('code', 'EXP-UTIL')->first();

        // 3. Sample Vouchers
        // Voucher 1: Receipt Voucher (قبض من عميل)
        $recNumber = 'REC-01001';
        $vReceipt = TreasuryVoucher::updateOrCreate(
            ['tenant_id' => $tenantId, 'voucher_number' => $recNumber],
            [
                'branch_id'                => $branchId,
                'treasury_id'              => $safe1->id,
                'type'                     => 'receipt',
                'client_id'                => $customer ? $customer->id : null,
                'amount'                   => 5000.00,
                'date'                     => now()->subDays(2)->toDateString(),
                'payment_method'           => 'cash',
                'received_from_or_paid_to' => $customer ? $customer->name : 'عميل تجريبي',
                'notes'                    => 'دفعة تحت حساب توريدات سابقة - تحصيل نقدي',
                'status'                   => 'approved',
                'created_by'               => $userId,
            ]
        );

        // Voucher 2: Payment Voucher (صرف فاتورة كهرباء)
        $payNumber1 = 'PAY-01001';
        $vPay1 = TreasuryVoucher::updateOrCreate(
            ['tenant_id' => $tenantId, 'voucher_number' => $payNumber1],
            [
                'branch_id'                => $branchId,
                'treasury_id'              => $safe1->id,
                'type'                     => 'payment',
                'category_id'              => $catUtil ? $catUtil->id : null,
                'amount'                   => 1250.00,
                'date'                     => now()->subDay()->toDateString(),
                'payment_method'           => 'cash',
                'received_from_or_paid_to' => 'شركة جنوب القاهرة لتوزيع الكهرباء',
                'notes'                    => 'سداد فاتورة استهلاك الكهرباء لشهر أكتوبر',
                'status'                   => 'approved',
                'created_by'               => $userId,
            ]
        );

        // Voucher 3: Payment Voucher (سداد مستحقات مورد بنكياً)
        $payNumber2 = 'PAY-01002';
        $vPay2 = TreasuryVoucher::updateOrCreate(
            ['tenant_id' => $tenantId, 'voucher_number' => $payNumber2],
            [
                'branch_id'                => $branchId,
                'treasury_id'              => $bank1->id,
                'type'                     => 'payment',
                'client_id'                => $supplier ? $supplier->id : null,
                'amount'                   => 25000.00,
                'date'                     => now()->toDateString(),
                'payment_method'           => 'bank_transfer',
                'reference_number'         => 'TRANS-98745',
                'received_from_or_paid_to' => $supplier ? $supplier->name : 'مورد مواد خام',
                'notes'                    => 'تحويل بنكي لسداد دفعة من فاتورة المشتريات الآجلة',
                'status'                   => 'approved',
                'created_by'               => $userId,
            ]
        );

        // 4. Sample Transfer (تحويل من الخزينة الرئيسية إلى البنك الأهلي)
        $trfNumber = 'TRF-01001';
        $transfer = TreasuryTransfer::updateOrCreate(
            ['tenant_id' => $tenantId, 'transfer_number' => $trfNumber],
            [
                'from_treasury_id' => $safe1->id,
                'to_treasury_id'   => $bank1->id,
                'amount'           => 10000.00,
                'transfer_fee'     => 0.00,
                'transfer_date'    => now()->toDateString(),
                'notes'            => 'إيداع نقدي لحصيلة المبيعات في الحساب الجاري بالبنك',
                'status'           => 'completed',
                'created_by'       => $userId,
            ]
        );

        // Update Treasury current balances from seeded operations
        $safe1->recalculateBalance();
        $bank1->recalculateBalance();
    }
}
