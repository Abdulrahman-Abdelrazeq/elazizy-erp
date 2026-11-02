<?php

namespace Modules\Invoice\Database\Seeders;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\CRM\Entities\Client;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;
use Modules\Invoice\Entities\Invoice;
use Modules\Invoice\Entities\InvoiceItem;
use Modules\Invoice\Entities\InvoicePayment;
use Modules\Setting\Entities\Tax;

class InvoiceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
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
        $tax = Tax::where('rate', '>', 0)->first();
        $taxId = $tax ? $tax->id : null;
        $taxRate = $tax ? (float) $tax->rate : 15.00;

        $customer1 = Client::customers()->first();
        $supplier1 = Client::suppliers()->first();
        $items = Item::take(4)->get();

        if ($items->count() < 2) {
            return;
        }

        $item1 = $items[0];
        $item2 = $items[1];

        $sellPrice1 = (float) ($item1->sell_price ?: 150.00);
        $costPrice1 = (float) ($item1->buy_price ?: 100.00);
        $sellPrice2 = (float) ($item2->sell_price ?: 350.00);
        $costPrice2 = (float) ($item2->buy_price ?: 220.00);

        // 1. Sales Invoice 1 (Cash Sale)
        $salNumber1 = 'INV-SAL-01001';
        $subtotal1 = ($sellPrice1 * 2) + ($sellPrice2 * 1);
        $taxAmount1 = ($subtotal1 * $taxRate) / 100;
        $grandTotal1 = $subtotal1 + $taxAmount1;

        $sale1 = Invoice::updateOrCreate(
            ['tenant_id' => $tenantId, 'type' => 'sale', 'invoice_number' => $salNumber1],
            [
                'branch_id'       => $branchId,
                'store_id'        => $storeId,
                'client_id'       => $customer1 ? $customer1->id : null,
                'date'            => now()->subDays(3)->toDateString(),
                'status'          => 'approved',
                'payment_type'    => 'cash',
                'subtotal'        => $subtotal1,
                'discount_type'   => 'fixed',
                'discount_value'  => 0.00,
                'discount_amount' => 0.00,
                'tax_id'          => $taxId,
                'tax_rate'        => $taxRate,
                'tax_amount'      => $taxAmount1,
                'shipping_cost'   => 0.00,
                'grand_total'     => $grandTotal1,
                'paid_amount'     => $grandTotal1,
                'remaining_amount'=> 0.00,
                'notes'           => 'فاتورة مبيعات نقدية مسددة بالكامل',
                'created_by'      => $userId,
            ]
        );

        InvoiceItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'invoice_id' => $sale1->id, 'item_id' => $item1->id],
            [
                'quantity'        => 2.00,
                'unit_price'      => $sellPrice1,
                'cost_price'      => $costPrice1,
                'subtotal'        => $sellPrice1 * 2,
                'total'           => $sellPrice1 * 2,
            ]
        );

        InvoiceItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'invoice_id' => $sale1->id, 'item_id' => $item2->id],
            [
                'quantity'        => 1.00,
                'unit_price'      => $sellPrice2,
                'cost_price'      => $costPrice2,
                'subtotal'        => $sellPrice2 * 1,
                'total'           => $sellPrice2 * 1,
            ]
        );

        InvoicePayment::updateOrCreate(
            ['tenant_id' => $tenantId, 'invoice_id' => $sale1->id, 'amount' => $grandTotal1],
            [
                'payment_method'  => 'cash',
                'payment_date'    => now()->subDays(3)->toDateString(),
                'reference_number'=> 'PAY-' . $salNumber1,
                'notes'           => 'سداد نقدي بالكامل',
                'created_by'      => $userId,
            ]
        );

        // 2. Sales Invoice 2 (Credit Sale with remaining amount)
        $salNumber2 = 'INV-SAL-01002';
        $subtotal2 = ($sellPrice1 * 3);
        $taxAmount2 = ($subtotal2 * $taxRate) / 100;
        $grandTotal2 = $subtotal2 + $taxAmount2;
        $paidAmount2 = 200.00;
        $remaining2 = $grandTotal2 - $paidAmount2;

        $sale2 = Invoice::updateOrCreate(
            ['tenant_id' => $tenantId, 'type' => 'sale', 'invoice_number' => $salNumber2],
            [
                'branch_id'       => $branchId,
                'store_id'        => $storeId,
                'client_id'       => $customer1 ? $customer1->id : null,
                'date'            => now()->subDays(1)->toDateString(),
                'due_date'        => now()->addDays(20)->toDateString(),
                'status'          => 'approved',
                'payment_type'    => 'credit',
                'subtotal'        => $subtotal2,
                'discount_type'   => 'fixed',
                'discount_value'  => 0.00,
                'discount_amount' => 0.00,
                'tax_id'          => $taxId,
                'tax_rate'        => $taxRate,
                'tax_amount'      => $taxAmount2,
                'shipping_cost'   => 0.00,
                'grand_total'     => $grandTotal2,
                'paid_amount'     => $paidAmount2,
                'remaining_amount'=> $remaining2,
                'notes'           => 'فاتورة مبيعات آجلة مسدد منها دفعة 200 والباقي على الحساب',
                'created_by'      => $userId,
            ]
        );

        InvoiceItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'invoice_id' => $sale2->id, 'item_id' => $item1->id],
            [
                'quantity'        => 3.00,
                'unit_price'      => $sellPrice1,
                'cost_price'      => $costPrice1,
                'subtotal'        => $subtotal2,
                'total'           => $subtotal2,
            ]
        );

        // 3. Purchase Invoice (فاتورة مشتريات من المورد)
        $purNumber = 'INV-PUR-01001';
        $purSubtotal = ($costPrice1 * 10) + ($costPrice2 * 5);
        $purTax = ($purSubtotal * $taxRate) / 100;
        $purGrandTotal = $purSubtotal + $purTax;

        $purchase1 = Invoice::updateOrCreate(
            ['tenant_id' => $tenantId, 'type' => 'purchase', 'invoice_number' => $purNumber],
            [
                'branch_id'       => $branchId,
                'store_id'        => $storeId,
                'client_id'       => $supplier1 ? $supplier1->id : null,
                'date'            => now()->subDays(5)->toDateString(),
                'status'          => 'approved',
                'payment_type'    => 'credit',
                'subtotal'        => $purSubtotal,
                'tax_id'          => $taxId,
                'tax_rate'        => $taxRate,
                'tax_amount'      => $purTax,
                'grand_total'     => $purGrandTotal,
                'paid_amount'     => 0.00,
                'remaining_amount'=> $purGrandTotal,
                'notes'           => 'توريد بضاعة جديدة للمستودع الرئيسي',
                'created_by'      => $userId,
            ]
        );

        InvoiceItem::updateOrCreate(
            ['tenant_id' => $tenantId, 'invoice_id' => $purchase1->id, 'item_id' => $item1->id],
            [
                'quantity'        => 10.00,
                'unit_price'      => $costPrice1,
                'cost_price'      => $costPrice1,
                'subtotal'        => $costPrice1 * 10,
                'total'           => $costPrice1 * 10,
            ]
        );

        // 4. Quotation (عرض سعر للعميل)
        $quoNumber = 'QUO-01001';
        Invoice::updateOrCreate(
            ['tenant_id' => $tenantId, 'type' => 'quotation', 'invoice_number' => $quoNumber],
            [
                'branch_id'       => $branchId,
                'client_id'       => $customer1 ? $customer1->id : null,
                'date'            => now()->toDateString(),
                'due_date'        => now()->addDays(15)->toDateString(),
                'status'          => 'approved',
                'payment_type'    => 'cash',
                'subtotal'        => 5000.00,
                'grand_total'     => 5750.00,
                'notes'           => 'عرض سعر توريد أجهزة ومعدات لشركة الأهرام',
                'created_by'      => $userId,
            ]
        );

        // 5. Purchase Order (أمر شراء للمورد)
        $poNumber = 'PO-01001';
        Invoice::updateOrCreate(
            ['tenant_id' => $tenantId, 'type' => 'purchase_order', 'invoice_number' => $poNumber],
            [
                'branch_id'       => $branchId,
                'client_id'       => $supplier1 ? $supplier1->id : null,
                'date'            => now()->toDateString(),
                'due_date'        => now()->addDays(7)->toDateString(),
                'status'          => 'approved',
                'payment_type'    => 'credit',
                'subtotal'        => 12000.00,
                'grand_total'     => 12000.00,
                'notes'           => 'طلب شحنة قطع غيار ومستلزمات جديدة',
                'created_by'      => $userId,
            ]
        );
    }
}
