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
use Modules\Setting\Entities\Tax;

class QuotationController extends Controller
{
    /**
     * Quotations Listing
     */
    public function index(Request $request)
    {
        $query = Invoice::quotations()->with(['client', 'creator']);

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

        $quotations = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_quotes' => Invoice::quotations()->count(),
            'total_value'  => Invoice::quotations()->sum('grand_total'),
            'converted'    => Invoice::quotations()->where('status', 'closed')->count(),
            'pending'      => Invoice::quotations()->where('status', 'draft')->orWhere('status', 'approved')->count(),
        ];

        return view('invoice::quotations.index', compact('quotations', 'stats'));
    }

    /**
     * Show create quotation form
     */
    public function create()
    {
        $customers = Client::customers()->where('is_active', true)->with('group')->get();
        $taxes = Tax::where('is_active', true)->get();
        $items = Item::where('is_active', true)->with(['category', 'itemUnits.unit'])->get();
        $nextNumber = Invoice::generateNumber('quotation');

        return view('invoice::quotations.create', compact('customers', 'taxes', 'items', 'nextNumber'));
    }

    /**
     * Store new quotation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'date'            => 'required|date',
            'due_date'        => 'nullable|date',
            'discount_type'   => 'required|in:fixed,percentage',
            'discount_value'  => 'nullable|numeric|min:0',
            'tax_id'          => 'nullable|exists:taxes,id',
            'shipping_cost'   => 'nullable|numeric|min:0',
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

            $discountValue = (float) ($validated['discount_value'] ?? 0);
            $discountAmount = $validated['discount_type'] === 'percentage' 
                ? ($subtotal * $discountValue) / 100 
                : min($discountValue, $subtotal);
            $afterDiscount = max(0, $subtotal - $discountAmount);

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

            $invoiceNumber = Invoice::generateNumber('quotation');
            $quotation = Invoice::create([
                'branch_id'       => activeBranch() ? activeBranch()->id : null,
                'client_id'       => $validated['client_id'],
                'invoice_number'  => $invoiceNumber,
                'type'            => 'quotation',
                'date'            => $validated['date'],
                'due_date'        => $validated['due_date'] ?? null,
                'status'          => 'approved',
                'payment_type'    => 'cash',
                'subtotal'        => $subtotal,
                'discount_type'   => $validated['discount_type'],
                'discount_value'  => $discountValue,
                'discount_amount' => $discountAmount,
                'tax_id'          => $validated['tax_id'] ?? null,
                'tax_rate'        => $taxRate,
                'tax_amount'      => $taxAmount,
                'shipping_cost'   => $shippingCost,
                'grand_total'     => $grandTotal,
                'notes'           => $validated['notes'] ?? null,
                'terms_conditions'=> $validated['terms_conditions'] ?? null,
                'created_by'      => Auth::id(),
            ]);

            foreach ($itemsData as $row) {
                InvoiceItem::create([
                    'invoice_id'      => $quotation->id,
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
            }

            DB::commit();
            return redirect()->route('invoices.quotations.index')->with('success', 'تم إنشاء عرض السعر بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Convert quotation to actual sales invoice
     */
    public function convertToSale(Invoice $quotation, Request $request)
    {
        $storeId = $request->store_id ?: Store::where('is_active', true)->value('id');
        if (!$storeId) {
            return back()->with('error', 'يرجى تحديد مستودع الصرف أولاً لتنفيذ الفاتورة.');
        }

        DB::beginTransaction();
        try {
            $saleNumber = Invoice::generateNumber('sale');
            $sale = Invoice::create([
                'branch_id'       => $quotation->branch_id,
                'store_id'        => $storeId,
                'client_id'       => $quotation->client_id,
                'invoice_number'  => $saleNumber,
                'type'            => 'sale',
                'date'            => now()->toDateString(),
                'status'          => 'approved',
                'payment_type'    => $request->payment_type ?: 'cash',
                'subtotal'        => $quotation->subtotal,
                'discount_type'   => $quotation->discount_type,
                'discount_value'  => $quotation->discount_value,
                'discount_amount' => $quotation->discount_amount,
                'tax_id'          => $quotation->tax_id,
                'tax_rate'        => $quotation->tax_rate,
                'tax_amount'      => $quotation->tax_amount,
                'shipping_cost'   => $quotation->shipping_cost,
                'grand_total'     => $quotation->grand_total,
                'paid_amount'     => $request->payment_type === 'cash' ? $quotation->grand_total : 0.00,
                'remaining_amount'=> $request->payment_type === 'cash' ? 0.00 : $quotation->grand_total,
                'notes'           => 'محولة تلقائياً من عرض السعر رقم: ' . $quotation->invoice_number,
                'parent_id'       => $quotation->id,
                'created_by'      => Auth::id(),
            ]);

            foreach ($quotation->items as $item) {
                InvoiceItem::create([
                    'invoice_id'      => $sale->id,
                    'item_id'         => $item->item_id,
                    'unit_id'         => $item->unit_id,
                    'quantity'        => $item->quantity,
                    'unit_price'      => $item->unit_price,
                    'cost_price'      => $item->cost_price,
                    'discount_percent'=> $item->discount_percent,
                    'discount_amount' => $item->discount_amount,
                    'subtotal'        => $item->subtotal,
                    'total'           => $item->total,
                ]);

                // Decrement stock
                $stock = ItemStock::firstOrCreate(
                    ['tenant_id' => $sale->tenant_id, 'item_id' => $item->item_id, 'store_id' => $storeId],
                    ['quantity' => 0.00]
                );
                $stock->decrement('quantity', $item->quantity);
            }

            // Mark quotation closed
            $quotation->update(['status' => 'closed']);

            // If credit, update client balance
            if ($sale->remaining_amount > 0 && $sale->client) {
                $sale->client->increment('current_balance', $sale->remaining_amount);
            }

            DB::commit();
            return redirect()->route('invoices.sales.show', $sale->id)->with('success', 'تم تحويل عرض السعر إلى فاتورة مبيعات معتمدة وصرف البضاعة بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'فشل التحويل: ' . $e->getMessage());
        }
    }
}
