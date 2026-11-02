<?php

namespace Modules\Invoice\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Entities\Client;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;
use Modules\Invoice\Entities\Invoice;
use Modules\Invoice\Entities\InvoiceItem;
use Modules\Invoice\Entities\InvoicePayment;
use Modules\Setting\Entities\Tax;

class SaleController extends Controller
{
    /**
     * Sales Invoices Listing
     */
    public function index(Request $request)
    {
        $query = Invoice::sales()->with(['client', 'store', 'creator']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('client', function ($cq) use ($s) {
                      $cq->where('name', 'like', "%{$s}%")
                         ->orWhere('company_name', 'like', "%{$s}%")
                         ->orWhere('phone', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $invoices = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_sales'    => Invoice::sales()->where('status', '!=', 'cancelled')->sum('grand_total'),
            'cash_sales'     => Invoice::sales()->where('status', '!=', 'cancelled')->where('payment_type', 'cash')->sum('grand_total'),
            'credit_sales'   => Invoice::sales()->where('status', '!=', 'cancelled')->where('payment_type', 'credit')->sum('grand_total'),
            'total_invoices' => Invoice::sales()->count(),
        ];

        $stores = Store::where('is_active', true)->get();
        $customers = Client::customers()->where('is_active', true)->get();

        return view('invoice::sales.index', compact('invoices', 'stats', 'stores', 'customers'));
    }

    /**
     * Show create sale invoice form
     */
    public function create()
    {
        $customers = Client::customers()->where('is_active', true)->with('group')->get();
        $stores = Store::where('is_active', true)->get();
        $taxes = Tax::where('is_active', true)->get();
        $items = Item::where('is_active', true)->with(['category', 'itemUnits.unit', 'stocks'])->get();
        $nextNumber = Invoice::generateNumber('sale');

        return view('invoice::sales.create', compact('customers', 'stores', 'taxes', 'items', 'nextNumber'));
    }

    /**
     * Store newly created sale invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'       => 'nullable|exists:clients,id',
            'store_id'        => 'required|exists:stores,id',
            'date'            => 'required|date',
            'due_date'        => 'nullable|date',
            'payment_type'    => 'required|in:cash,credit,partial,card,bank_transfer',
            'discount_type'   => 'required|in:fixed,percentage',
            'discount_value'  => 'nullable|numeric|min:0',
            'tax_id'          => 'nullable|exists:taxes,id',
            'shipping_cost'   => 'nullable|numeric|min:0',
            'paid_amount'     => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
            'terms_conditions'=> 'nullable|string',
            'items'           => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.quantity'=> 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            // 1. Calculate line items totals
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $line) {
                $item = Item::findOrFail($line['item_id']);
                $qty = (float) $line['quantity'];
                $unitPrice = (float) $line['unit_price'];
                $discPercent = (float) ($line['discount_percent'] ?? 0);
                
                $lineSubtotal = $qty * $unitPrice;
                $lineDiscAmount = ($lineSubtotal * $discPercent) / 100;
                $lineNet = $lineSubtotal - $lineDiscAmount;
                $subtotal += $lineNet;

                $itemsData[] = [
                    'item'            => $item,
                    'unit_id'         => $line['unit_id'] ?? null,
                    'quantity'        => $qty,
                    'unit_price'      => $unitPrice,
                    'cost_price'      => (float) $item->cost_price,
                    'discount_percent'=> $discPercent,
                    'discount_amount' => $lineDiscAmount,
                    'subtotal'        => $lineNet,
                    'total'           => $lineNet,
                ];
            }

            // 2. Invoice level discount
            $discountValue = (float) ($validated['discount_value'] ?? 0);
            $discountAmount = 0;
            if ($validated['discount_type'] === 'percentage') {
                $discountAmount = ($subtotal * $discountValue) / 100;
            } else {
                $discountAmount = min($discountValue, $subtotal);
            }
            $afterDiscount = max(0, $subtotal - $discountAmount);

            // 3. Tax Calculation
            $taxRate = 0;
            $taxAmount = 0;
            if (!empty($validated['tax_id'])) {
                $taxObj = Tax::find($validated['tax_id']);
                if ($taxObj) {
                    $taxRate = (float) $taxObj->rate;
                    $taxAmount = ($afterDiscount * $taxRate) / 100;
                }
            }

            $shippingCost = (float) ($validated['shipping_cost'] ?? 0);
            $grandTotal = $afterDiscount + $taxAmount + $shippingCost;

            // 4. Payment calculations
            $paymentType = $validated['payment_type'];
            $paidAmount = (float) ($validated['paid_amount'] ?? 0);
            if ($paymentType === 'cash' || $paymentType === 'card' || $paymentType === 'bank_transfer') {
                $paidAmount = $grandTotal;
            } elseif ($paymentType === 'credit') {
                $paidAmount = 0.00;
            }
            $remainingAmount = max(0, $grandTotal - $paidAmount);

            // 5. Create Invoice record
            $invoiceNumber = Invoice::generateNumber('sale');
            $invoice = Invoice::create([
                'branch_id'       => activeBranch() ? activeBranch()->id : null,
                'store_id'        => $validated['store_id'],
                'client_id'       => $validated['client_id'] ?? null,
                'invoice_number'  => $invoiceNumber,
                'type'            => 'sale',
                'date'            => $validated['date'],
                'due_date'        => $validated['due_date'] ?? null,
                'status'          => 'approved',
                'payment_type'    => $paymentType,
                'subtotal'        => $subtotal,
                'discount_type'   => $validated['discount_type'],
                'discount_value'  => $discountValue,
                'discount_amount' => $discountAmount,
                'tax_id'          => $validated['tax_id'] ?? null,
                'tax_rate'        => $taxRate,
                'tax_amount'      => $taxAmount,
                'shipping_cost'   => $shippingCost,
                'grand_total'     => $grandTotal,
                'paid_amount'     => $paidAmount,
                'remaining_amount'=> $remainingAmount,
                'notes'           => $validated['notes'] ?? null,
                'terms_conditions'=> $validated['terms_conditions'] ?? null,
                'created_by'      => Auth::id(),
            ]);

            // 6. Save items and decrement stock
            foreach ($itemsData as $row) {
                InvoiceItem::create([
                    'invoice_id'      => $invoice->id,
                    'item_id'         => $row['item']->id,
                    'unit_id'         => $row['unit_id'],
                    'unit_multiplier' => 1.00,
                    'quantity'        => $row['quantity'],
                    'unit_price'      => $row['unit_price'],
                    'cost_price'      => $row['cost_price'],
                    'discount_percent'=> $row['discount_percent'],
                    'discount_amount' => $row['discount_amount'],
                    'subtotal'        => $row['subtotal'],
                    'total'           => $row['total'],
                ]);

                // Decrement inventory stock
                $stock = ItemStock::firstOrCreate(
                    [
                        'tenant_id' => $invoice->tenant_id,
                        'item_id'   => $row['item']->id,
                        'store_id'  => $validated['store_id'],
                    ],
                    ['quantity' => 0.00]
                );
                $stock->decrement('quantity', $row['quantity']);
            }

            // 7. Record payment if paid > 0
            if ($paidAmount > 0) {
                InvoicePayment::create([
                    'invoice_id'     => $invoice->id,
                    'payment_method' => in_array($paymentType, ['card', 'bank_transfer']) ? $paymentType : 'cash',
                    'amount'         => $paidAmount,
                    'payment_date'   => $validated['date'],
                    'reference_number' => 'PAY-' . $invoiceNumber,
                    'notes'          => 'دفعة فاتورة مبيعات',
                    'created_by'     => Auth::id(),
                ]);
            }

            // 8. Update customer balance if there is credit/remaining
            if ($invoice->client && $remainingAmount > 0) {
                $invoice->client->increment('current_balance', $remainingAmount);
            }

            DB::commit();
            return redirect()->route('invoices.sales.show', $invoice->id)->with('success', 'تم إنشاء واعتماد فاتورة المبيعات بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ الفاتورة: ' . $e->getMessage());
        }
    }

    /**
     * Show invoice details
     */
    public function show(Invoice $sale)
    {
        $sale->load(['branch', 'store', 'client.group', 'items.item.category', 'items.unit', 'tax', 'payments.creator', 'creator']);
        return view('invoice::sales.show', compact('sale'));
    }

    /**
     * Print invoice (A4 / Thermal receipt)
     */
    public function print(Invoice $sale)
    {
        $sale->load(['branch', 'store', 'client.group', 'items.item', 'items.unit', 'tax', 'creator']);
        return view('invoice::sales.print', compact('sale'));
    }

    /**
     * Cancel/Delete invoice
     */
    public function destroy(Invoice $sale)
    {
        DB::beginTransaction();
        try {
            // Revert inventory stock
            foreach ($sale->items as $item) {
                $stock = ItemStock::where('item_id', $item->item_id)->where('store_id', $sale->store_id)->first();
                if ($stock) {
                    $stock->increment('quantity', $item->quantity);
                }
            }

            // Revert client balance
            if ($sale->client && $sale->remaining_amount > 0) {
                $sale->client->decrement('current_balance', $sale->remaining_amount);
            }

            $sale->update(['status' => 'cancelled']);
            DB::commit();

            return redirect()->route('invoices.sales.index')->with('success', 'تم إلغاء فاتورة المبيعات وإرجاع الكميات للمستودع بنجاح.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'تعذر إلغاء الفاتورة: ' . $e->getMessage());
        }
    }
}
