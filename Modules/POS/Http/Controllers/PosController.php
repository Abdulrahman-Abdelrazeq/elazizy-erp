<?php

namespace Modules\POS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Entities\Client;
use Modules\Inventory\Entities\Category;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;
use Modules\Invoice\Entities\Invoice;
use Modules\Invoice\Entities\InvoiceItem;
use Modules\Invoice\Entities\InvoicePayment;
use Modules\POS\Entities\CashierShift;
use Modules\POS\Entities\PosHeldOrder;
use Modules\POS\Entities\PosTerminal;
use Modules\Setting\Entities\Tax;

class PosController extends Controller
{
    /**
     * Main High-Speed POS Cashier Interface
     */
    public function screen(Request $request)
    {
        $user = Auth::user();

        // 1. Check if user has an open shift
        $activeShift = CashierShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->latest('id')
            ->first();

        $terminals = PosTerminal::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();

        // If no active shift, pass activeShift as null so Blade shows shift open dialog
        if (!$activeShift) {
            return view('pos::screen.index', [
                'activeShift' => null,
                'terminals'   => $terminals,
                'stores'      => $stores,
                'categories'  => [],
                'items'       => [],
                'customers'   => [],
                'taxes'       => [],
            ]);
        }

        // Active shift exists: load products, categories, customers
        $categories = Category::where('is_active', true)->get();
        $items = Item::where('is_active', true)
            ->with(['category', 'stocks'])
            ->get();

        $customers = Client::customers()->where('is_active', true)->with('group')->get();
        $taxes = Tax::where('is_active', true)->get();
        $defaultTax = Tax::where('is_default', true)->first();

        // Recalculate shift totals
        $activeShift->recalculateTotals();

        return view('pos::screen.index', compact(
            'activeShift',
            'terminals',
            'stores',
            'categories',
            'items',
            'customers',
            'taxes',
            'defaultTax'
        ));
    }

    /**
     * High-speed POS checkout
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'shift_id'        => 'required|exists:cashier_shifts,id',
            'client_id'       => 'nullable|exists:clients,id',
            'payment_type'    => 'required|in:cash,card,credit,partial',
            'discount_type'   => 'required|in:fixed,percentage',
            'discount_value'  => 'nullable|numeric|min:0',
            'tax_id'          => 'nullable|exists:taxes,id',
            'paid_amount'     => 'nullable|numeric|min:0',
            'items'           => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity'=> 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $shift = CashierShift::findOrFail($validated['shift_id']);
        if ($shift->isClosed()) {
            return response()->json(['success' => false, 'message' => 'عفواً، هذه الوردية مغلقة حالياً.'], 422);
        }

        $storeId = $shift->terminal->store_id ?: Store::where('is_active', true)->value('id');

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $line) {
                $item = Item::findOrFail($line['item_id']);
                $qty = (float) $line['quantity'];
                $price = (float) $line['unit_price'];
                $lineTotal = $qty * $price;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'item'       => $item,
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'cost_price' => (float) ($item->buy_price ?? 0),
                    'total'      => $lineTotal,
                ];
            }

            // Discount
            $discountValue = (float) ($validated['discount_value'] ?? 0);
            $discountAmount = $validated['discount_type'] === 'percentage'
                ? ($subtotal * $discountValue) / 100
                : min($discountValue, $subtotal);
            $afterDiscount = max(0, $subtotal - $discountAmount);

            // Tax
            $taxRate = 0;
            $taxAmount = 0;
            if (!empty($validated['tax_id'])) {
                $taxObj = Tax::find($validated['tax_id']);
                if ($taxObj) {
                    $taxRate = (float) $taxObj->rate;
                    $taxAmount = ($afterDiscount * $taxRate) / 100;
                }
            }

            $grandTotal = $afterDiscount + $taxAmount;

            $paymentType = $validated['payment_type'];
            $paidAmount = (float) ($validated['paid_amount'] ?? 0);
            if ($paymentType === 'cash' || $paymentType === 'card') {
                $paidAmount = max($paidAmount, $grandTotal);
            } elseif ($paymentType === 'credit') {
                $paidAmount = 0.00;
            }
            $remainingAmount = max(0, $grandTotal - $paidAmount);
            $changeAmount = max(0, $paidAmount - $grandTotal);

            // Create Invoice
            $invoiceNumber = Invoice::generateNumber('sale');
            $invoice = Invoice::create([
                'branch_id'       => $shift->branch_id,
                'shift_id'        => $shift->id,
                'store_id'        => $storeId,
                'client_id'       => $validated['client_id'] ?? null,
                'invoice_number'  => $invoiceNumber,
                'type'            => 'sale',
                'date'            => now()->toDateString(),
                'status'          => 'approved',
                'payment_type'    => $paymentType,
                'subtotal'        => $subtotal,
                'discount_type'   => $validated['discount_type'],
                'discount_value'  => $discountValue,
                'discount_amount' => $discountAmount,
                'tax_id'          => $validated['tax_id'] ?? null,
                'tax_rate'        => $taxRate,
                'tax_amount'      => $taxAmount,
                'grand_total'     => $grandTotal,
                'paid_amount'     => min($paidAmount, $grandTotal),
                'remaining_amount'=> $remainingAmount,
                'notes'           => 'نقطة بيع - كاشير (' . ($shift->terminal->name ?? 'POS') . ')',
                'created_by'      => Auth::id(),
            ]);

            // Save items & decrement stock
            foreach ($itemsData as $row) {
                InvoiceItem::create([
                    'invoice_id'      => $invoice->id,
                    'item_id'         => $row['item']->id,
                    'unit_multiplier' => 1.00,
                    'quantity'        => $row['quantity'],
                    'unit_price'      => $row['unit_price'],
                    'cost_price'      => $row['cost_price'],
                    'subtotal'        => $row['total'],
                    'total'           => $row['total'],
                ]);

                // Decrement inventory
                $stock = ItemStock::firstOrCreate(
                    ['tenant_id' => $invoice->tenant_id, 'item_id' => $row['item']->id, 'store_id' => $storeId],
                    ['quantity' => 0.00]
                );
                $stock->decrement('quantity', $row['quantity']);
            }

            // Payment record
            if ($paidAmount > 0) {
                InvoicePayment::create([
                    'invoice_id'       => $invoice->id,
                    'payment_method'   => $paymentType === 'card' ? 'card' : 'cash',
                    'amount'           => min($paidAmount, $grandTotal),
                    'payment_date'     => now()->toDateString(),
                    'reference_number' => 'POS-PAY-' . $invoiceNumber,
                    'notes'            => 'سداد كاشير فوري',
                    'created_by'       => Auth::id(),
                ]);
            }

            // If credit, increment client balance
            if ($invoice->client && $remainingAmount > 0) {
                $invoice->client->increment('current_balance', $remainingAmount);
            }

            // Refresh shift totals
            $shift->recalculateTotals();

            DB::commit();

            return response()->json([
                'success'        => true,
                'message'        => 'تمت معالجة الفاتورة بنجاح!',
                'invoice_id'     => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'grand_total'    => $grandTotal,
                'paid_amount'    => $paidAmount,
                'change_amount'  => $changeAmount,
                'print_url'      => route('pos.receipt', $invoice->id),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'خطأ أثناء المعالجة: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Park/Hold current cart in POS
     */
    public function holdOrder(Request $request)
    {
        $validated = $request->validate([
            'shift_id'  => 'required|exists:cashier_shifts,id',
            'client_id' => 'nullable|exists:clients,id',
            'cart_data' => 'required',
            'subtotal'  => 'required|numeric',
        ]);

        $shift = CashierShift::findOrFail($validated['shift_id']);
        $count = PosHeldOrder::where('shift_id', $shift->id)->count() + 1;
        $code = 'معلقة #' . $count;

        $order = PosHeldOrder::create([
            'tenant_id'      => $shift->tenant_id,
            'shift_id'       => $shift->id,
            'terminal_id'    => $shift->terminal_id,
            'reference_code' => $code,
            'client_id'      => $validated['client_id'] ?? null,
            'cart_data'      => is_array($validated['cart_data']) ? $validated['cart_data'] : json_decode($validated['cart_data'], true),
            'subtotal'       => $validated['subtotal'],
            'held_at'        => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'تم تعليق الفاتورة بنجاح برقم: ' . $code, 'held_id' => $order->id]);
    }

    /**
     * Get held/parked orders
     */
    public function getHeldOrders(CashierShift $shift)
    {
        $orders = $shift->heldOrders()->with('client')->latest('id')->get();
        return response()->json(['success' => true, 'orders' => $orders]);
    }

    /**
     * Recall & delete held order
     */
    public function recallHeldOrder(PosHeldOrder $order)
    {
        $cartData = $order->cart_data;
        $clientId = $order->client_id;
        $order->delete();

        return response()->json([
            'success'   => true,
            'cart_data' => $cartData,
            'client_id' => $clientId,
        ]);
    }

    /**
     * 80mm Thermal Receipt Slip
     */
    public function receipt(Invoice $invoice)
    {
        $invoice->load(['branch', 'client', 'items.item', 'creator', 'payments']);
        return view('pos::screen.receipt', compact('invoice'));
    }
}
