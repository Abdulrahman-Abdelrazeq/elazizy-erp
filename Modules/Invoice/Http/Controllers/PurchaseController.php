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

class PurchaseController extends Controller
{
    /**
     * Purchase Invoices Listing
     */
    public function index(Request $request)
    {
        $query = Invoice::purchases()->with(['client', 'store', 'creator']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('client', function ($cq) use ($s) {
                      $cq->where('name', 'like', "%{$s}%")
                         ->orWhere('company_name', 'like', "%{$s}%");
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

        $invoices = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_purchases' => Invoice::purchases()->where('status', '!=', 'cancelled')->sum('grand_total'),
            'cash_purchases'  => Invoice::purchases()->where('status', '!=', 'cancelled')->where('payment_type', 'cash')->sum('grand_total'),
            'credit_purchases'=> Invoice::purchases()->where('status', '!=', 'cancelled')->where('payment_type', 'credit')->sum('grand_total'),
            'total_invoices'  => Invoice::purchases()->count(),
        ];

        $stores = Store::where('is_active', true)->get();
        $suppliers = Client::suppliers()->where('is_active', true)->get();

        return view('invoice::purchases.index', compact('invoices', 'stats', 'stores', 'suppliers'));
    }

    /**
     * Show create purchase invoice form
     */
    public function create()
    {
        $suppliers = Client::suppliers()->where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();
        $taxes = Tax::where('is_active', true)->get();
        $items = Item::where('is_active', true)->with(['category', 'itemUnits.unit'])->get();
        $nextNumber = Invoice::generateNumber('purchase');

        return view('invoice::purchases.create', compact('suppliers', 'stores', 'taxes', 'items', 'nextNumber'));
    }

    /**
     * Store newly created purchase invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'store_id'        => 'required|exists:stores,id',
            'date'            => 'required|date',
            'due_date'        => 'nullable|date',
            'payment_type'    => 'required|in:cash,credit,partial,bank_transfer',
            'discount_type'   => 'required|in:fixed,percentage',
            'discount_value'  => 'nullable|numeric|min:0',
            'tax_id'          => 'nullable|exists:taxes,id',
            'shipping_cost'   => 'nullable|numeric|min:0',
            'paid_amount'     => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
            'items'           => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.quantity'=> 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
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
                    'cost_price'      => $unitPrice, // update cost price on purchase
                    'discount_percent'=> $discPercent,
                    'discount_amount' => $lineDiscAmount,
                    'subtotal'        => $lineNet,
                    'total'           => $lineNet,
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

            $shippingCost = (float) ($validated['shipping_cost'] ?? 0);
            $grandTotal = $afterDiscount + $taxAmount + $shippingCost;

            $paymentType = $validated['payment_type'];
            $paidAmount = (float) ($validated['paid_amount'] ?? 0);
            if ($paymentType === 'cash' || $paymentType === 'bank_transfer') {
                $paidAmount = $grandTotal;
            } elseif ($paymentType === 'credit') {
                $paidAmount = 0.00;
            }
            $remainingAmount = max(0, $grandTotal - $paidAmount);

            $invoiceNumber = Invoice::generateNumber('purchase');
            $invoice = Invoice::create([
                'branch_id'       => activeBranch() ? activeBranch()->id : null,
                'store_id'        => $validated['store_id'],
                'client_id'       => $validated['client_id'],
                'invoice_number'  => $invoiceNumber,
                'type'            => 'purchase',
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
                'created_by'      => Auth::id(),
            ]);

            // Save items and INCREMENT stock
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

                // Increment warehouse stock
                $stock = ItemStock::firstOrCreate(
                    [
                        'tenant_id' => $invoice->tenant_id,
                        'item_id'   => $row['item']->id,
                        'store_id'  => $validated['store_id'],
                    ],
                    ['quantity' => 0.00]
                );
                $stock->increment('quantity', $row['quantity']);

                // Update item's latest cost price
                $row['item']->update(['buy_price' => $row['unit_price']]);
            }

            // Payment record
            if ($paidAmount > 0) {
                InvoicePayment::create([
                    'invoice_id'     => $invoice->id,
                    'payment_method' => $paymentType === 'bank_transfer' ? 'bank_transfer' : 'cash',
                    'amount'         => $paidAmount,
                    'payment_date'   => $validated['date'],
                    'reference_number' => 'PUR-PAY-' . $invoiceNumber,
                    'notes'          => 'دفعة فاتورة مشتريات للمورد',
                    'created_by'     => Auth::id(),
                ]);
            }

            // Update supplier balance (creditor - debt owed to supplier)
            if ($invoice->client && $remainingAmount > 0) {
                $invoice->client->decrement('current_balance', $remainingAmount);
            }

            DB::commit();
            return redirect()->route('invoices.purchases.show', $invoice->id)->with('success', 'تم تسجيل واعتماد فاتورة المشتريات وإضافة البضاعة للمخزن بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ فاتورة المشتريات: ' . $e->getMessage());
        }
    }

    /**
     * Show purchase details
     */
    public function show(Invoice $purchase)
    {
        $purchase->load(['branch', 'store', 'client', 'items.item.category', 'items.unit', 'tax', 'payments.creator', 'creator']);
        return view('invoice::purchases.show', compact('purchase'));
    }
}
