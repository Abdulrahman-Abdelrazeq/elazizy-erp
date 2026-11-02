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

class PurchaseOrderController extends Controller
{
    /**
     * Purchase Orders Listing
     */
    public function index(Request $request)
    {
        $query = Invoice::purchaseOrders()->with(['client', 'creator']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('client', function ($cq) use ($s) {
                      $cq->where('name', 'like', "%{$s}%");
                  });
            });
        }

        $orders = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_orders' => Invoice::purchaseOrders()->count(),
            'total_value'  => Invoice::purchaseOrders()->sum('grand_total'),
            'received'     => Invoice::purchaseOrders()->where('status', 'closed')->count(),
            'pending'      => Invoice::purchaseOrders()->where('status', 'approved')->count(),
        ];

        return view('invoice::orders.index', compact('orders', 'stats'));
    }

    /**
     * Show create purchase order form
     */
    public function create()
    {
        $suppliers = Client::suppliers()->where('is_active', true)->get();
        $items = Item::where('is_active', true)->with(['category', 'itemUnits.unit'])->get();
        $nextNumber = Invoice::generateNumber('purchase_order');

        return view('invoice::orders.create', compact('suppliers', 'items', 'nextNumber'));
    }

    /**
     * Store new purchase order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'date'            => 'required|date',
            'due_date'        => 'nullable|date',
            'notes'           => 'nullable|string',
            'terms_conditions'=> 'nullable|string',
            'items'           => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity'=> 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $line) {
                $qty = (float) $line['quantity'];
                $unitPrice = (float) $line['unit_price'];
                $lineTotal = $qty * $unitPrice;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'item_id'    => $line['item_id'],
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'total'      => $lineTotal,
                ];
            }

            $orderNumber = Invoice::generateNumber('purchase_order');
            $order = Invoice::create([
                'branch_id'       => activeBranch() ? activeBranch()->id : null,
                'client_id'       => $validated['client_id'],
                'invoice_number'  => $orderNumber,
                'type'            => 'purchase_order',
                'date'            => $validated['date'],
                'due_date'        => $validated['due_date'] ?? null,
                'status'          => 'approved',
                'payment_type'    => 'cash',
                'subtotal'        => $subtotal,
                'grand_total'     => $subtotal,
                'notes'           => $validated['notes'] ?? null,
                'terms_conditions'=> $validated['terms_conditions'] ?? null,
                'created_by'      => Auth::id(),
            ]);

            foreach ($itemsData as $row) {
                InvoiceItem::create([
                    'invoice_id'      => $order->id,
                    'item_id'         => $row['item_id'],
                    'unit_multiplier' => 1.00,
                    'quantity'        => $row['quantity'],
                    'unit_price'      => $row['unit_price'],
                    'subtotal'        => $row['total'],
                    'total'           => $row['total'],
                ]);
            }

            DB::commit();
            return redirect()->route('invoices.orders.index')->with('success', 'تم إصدار أمر الشراء للمورد بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Convert Purchase Order to actual Purchase invoice upon delivery
     */
    public function convertToPurchase(Invoice $order, Request $request)
    {
        $storeId = $request->store_id ?: Store::where('is_active', true)->value('id');
        if (!$storeId) {
            return back()->with('error', 'يرجى تحديد مستودع استلام البضاعة أولاً.');
        }

        DB::beginTransaction();
        try {
            $purNumber = Invoice::generateNumber('purchase');
            $purchase = Invoice::create([
                'branch_id'       => $order->branch_id,
                'store_id'        => $storeId,
                'client_id'       => $order->client_id,
                'invoice_number'  => $purNumber,
                'type'            => 'purchase',
                'date'            => now()->toDateString(),
                'status'          => 'approved',
                'payment_type'    => $request->payment_type ?: 'credit',
                'subtotal'        => $order->subtotal,
                'grand_total'     => $order->grand_total,
                'paid_amount'     => $request->payment_type === 'cash' ? $order->grand_total : 0.00,
                'remaining_amount'=> $request->payment_type === 'cash' ? 0.00 : $order->grand_total,
                'notes'           => 'مستلمة ومحولة من أمر الشراء رقم: ' . $order->invoice_number,
                'parent_id'       => $order->id,
                'created_by'      => Auth::id(),
            ]);

            foreach ($order->items as $item) {
                InvoiceItem::create([
                    'invoice_id'      => $purchase->id,
                    'item_id'         => $item->item_id,
                    'unit_id'         => $item->unit_id,
                    'quantity'        => $item->quantity,
                    'unit_price'      => $item->unit_price,
                    'cost_price'      => $item->unit_price,
                    'subtotal'        => $item->subtotal,
                    'total'           => $item->total,
                ]);

                // Increment warehouse stock
                $stock = ItemStock::firstOrCreate(
                    ['tenant_id' => $purchase->tenant_id, 'item_id' => $item->item_id, 'store_id' => $storeId],
                    ['quantity' => 0.00]
                );
                $stock->increment('quantity', $item->quantity);
            }

            // Close order
            $order->update(['status' => 'closed']);

            // Update supplier balance if credit
            if ($purchase->remaining_amount > 0 && $purchase->client) {
                $purchase->client->decrement('current_balance', $purchase->remaining_amount);
            }

            DB::commit();
            return redirect()->route('invoices.purchases.show', $purchase->id)->with('success', 'تم استلام البضاعة في المخزن وتحويل أمر الشراء إلى فاتورة مشتريات معتمدة!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'فشل التحويل: ' . $e->getMessage());
        }
    }
}

