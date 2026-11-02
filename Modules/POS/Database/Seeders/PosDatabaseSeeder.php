<?php

namespace Modules\POS\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\CRM\Entities\Client;
use Modules\Inventory\Entities\Store;
use Modules\Invoice\Entities\Invoice;
use Modules\POS\Entities\CashierShift;
use Modules\POS\Entities\PosTerminal;

class PosDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Module 5 (POS & Cashier).
     */
    public function run(): void
    {
        $tenant = Tenant::first();
        $tenantId = $tenant ? $tenant->id : 1;
        $user = User::first();
        $userId = $user ? $user->id : 1;
        $branch = Branch::first();
        $branchId = $branch ? $branch->id : 1;
        $store = Store::first();
        $storeId = $store ? $store->id : 1;
        $customer = Client::customers()->first();
        $customerId = $customer ? $customer->id : null;

        // 1. Seed POS Terminals
        $terminal1 = PosTerminal::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'POS-01'],
            [
                'branch_id'         => $branchId,
                'store_id'          => $storeId,
                'name'              => 'كاشير 1 - الصالة الرئيسية',
                'printer_type'      => 'thermal_80mm',
                'default_client_id' => $customerId,
                'is_active'         => true,
            ]
        );

        $terminal2 = PosTerminal::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'POS-02'],
            [
                'branch_id'         => $branchId,
                'store_id'          => $storeId,
                'name'              => 'كاشير 2 - الطلبات السريعة',
                'printer_type'      => 'thermal_80mm',
                'default_client_id' => $customerId,
                'is_active'         => true,
            ]
        );

        $terminal3 = PosTerminal::updateOrCreate(
            ['tenant_id' => $tenantId, 'code' => 'POS-03'],
            [
                'branch_id'         => $branchId,
                'store_id'          => $storeId,
                'name'              => 'كاشير 3 - قسم المبيعات الخارجية',
                'printer_type'      => 'a4',
                'default_client_id' => $customerId,
                'is_active'         => true,
            ]
        );

        // 2. Closed Historical Shift (SHF-00101)
        $shiftClosed = CashierShift::updateOrCreate(
            ['tenant_id' => $tenantId, 'shift_number' => 'SHF-00101'],
            [
                'branch_id'       => $branchId,
                'terminal_id'     => $terminal1->id,
                'user_id'         => $userId,
                'started_at'      => now()->subDay()->setTime(8, 0),
                'ended_at'        => now()->subDay()->setTime(16, 0),
                'status'          => 'closed',
                'opening_balance' => 500.00,
                'cash_sales'      => 1250.00,
                'card_sales'      => 800.00,
                'credit_sales'    => 350.00,
                'total_returns'   => 0.00,
                'expected_cash'   => 1750.00, // 500 + 1250
                'actual_cash'     => 1750.00,
                'cash_difference' => 0.00,
                'start_notes'     => 'بدء الوردية الصباحية - الدرج وورق الطابعة جاهز',
                'end_notes'       => 'تم الجرد اليومي والمطابقة مع المشرف، الدرج متطابق تماماً.',
                'closed_by'       => $userId,
            ]
        );

        // 3. Current Open Shift for Cashier (SHF-00102)
        $shiftOpen = CashierShift::updateOrCreate(
            ['tenant_id' => $tenantId, 'shift_number' => 'SHF-00102'],
            [
                'branch_id'       => $branchId,
                'terminal_id'     => $terminal1->id,
                'user_id'         => $userId,
                'started_at'      => now()->setTime(9, 0),
                'ended_at'        => null,
                'status'          => 'open',
                'opening_balance' => 500.00,
                'cash_sales'      => 0.00,
                'card_sales'      => 0.00,
                'credit_sales'    => 0.00,
                'total_returns'   => 0.00,
                'expected_cash'   => 500.00,
                'actual_cash'     => 0.00,
                'cash_difference' => 0.00,
                'start_notes'     => 'تم استلام عهدة الصرافة 500 جنيه لبدء العمل اليومي.',
                'closed_by'       => null,
            ]
        );

        // Associate existing sales invoice to the closed shift if any exist
        $firstSale = Invoice::where('type', 'sale')->first();
        if ($firstSale) {
            $firstSale->update(['shift_id' => $shiftClosed->id]);
        }
    }
}

