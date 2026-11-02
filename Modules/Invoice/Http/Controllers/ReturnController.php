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

class ReturnController extends Controller
{
    /**
     * Returns Listing (Sales returns & purchase returns)
     */
    public function index(Request $request)
    {
        $query = Invoice::whereIn('type', ['sale_return', 'purchase_return'])->with(['client', 'store', 'parent']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('client', function ($cq) use ($s) {
                      $cq->where('name', 'like', "%{$s}%");
                  });
            });
        }

        $returns = $query->latest('id')->paginate(15)->withQueryString();

        $stats = [
            'total_sales_returns'    => Invoice::saleReturns()->sum('grand_total'),
            'total_purchase_returns' => Invoice::purchaseReturns()->sum('grand_total'),
            'count_sales_returns'    => Invoice::saleReturns()->count(),
            'count_purchase_returns' => Invoice::purchaseReturns()->count(),
        ];

        return view('invoice::returns.index', compact('returns', 'stats'));
    }

    /**
     * Show create return form
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'sale_return'); // sale_return or purchase_return
        $stores = Store::where('is_active', true)->get();
        $clients = $type === 'sale_return' 
            ? Client::customers()->where('is_active', true)->get()
            : Client::suppliers()->where('is_active', true)->get();
        
        $items = Item::where('is_active', true)->get();
        $nextNumber = Invoice::generateNumber($type);

        // If referencing an original invoice
        $originalInvoice = null;
        if ($request->filled('invoice_id')) {
            $originalInvoice = Invoice::with('items.item')->find($request->invoice_id);
        }

        return view('invoice::returns.create', compact('type', 'stores', 'clients', 'items', 'nextNumber', 'originalInvoice'));
    }

    /**
     * Store return invoice
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'            => 'required|in:sale_return,purchase_return',
            'client_id'       => 'nullable|exists:clients,id',
            'store_id'        => 'required|exists:stores,id',
            'date'            => 'required|date',
            'parent_id'       => 'nullable|exists:invoices,id',
            'notes'           => 'nullable|string',
            'items'           => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity'=> 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $type = $validated['type'];
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $line) {
                $qty = (float) $line['quantity'];
                $price = (float) $line['unit_price'];
                $lineTotal = $qty * $price;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'item_id'    => $line['item_id'],
                    'quantity'   => $qty,
                    'unit_price' => $price,
                    'total'      => $lineTotal,
                ];
            }

            $invoiceNumber = Invoice::generateNumber($type);
            $returnInvoice = Invoice::create([
                'branch_id'       => activeBranch() ? activeBranch()->id : null,
                'store_id'        => $validated['store_id'],
                'client_id'       => $validated['client_id'] ?? null,
                'parent_id'       => $validated['parent_id'] ?? null,
                'invoice_number'  => $invoiceNumber,
                'type'            => $type,
                'date'            => $validated['date'],
                'status'          => 'approved',
                'payment_type'    => 'cash',
                'subtotal'        => $subtotal,
                'grand_total'     => $subtotal,
                'paid_amount'     => $subtotal,
                'remaining_amount'=> 0.00,
                'notes'           => $validated['notes'] ?? null,
                'created_by'      => Auth::id(),
            ]);

            foreach ($itemsData as $row) {
                InvoiceItem::create([
                    'invoice_id'      => $returnInvoice->id,
                    'item_id'         => $row['item_id'],
                    'unit_multiplier' => 1.00,
                    'quantity'        => $row['quantity'],
                    'unit_price'      => $row['unit_price'],
                    'subtotal'        => $row['total'],
                    'total'           => $row['total'],
                ]);

                $stock = ItemStock::firstOrCreate(
                    ['tenant_id' => $returnInvoice->tenant_id, 'item_id' => $row['item_id'], 'store_id' => $validated['store_id']],
                    ['quantity' => 0.00]
                );

                if ($type === 'sale_return') {
                    // Item returned from customer BACK into warehouse stock
                    $stock->increment('quantity', $row['quantity']);
                } else {
                    // Item returned back to supplier OUT OF warehouse stock
                    $stock->decrement('quantity', $row['quantity']);
                }
            }

            // Adjust client financial balance
            if ($returnInvoice->client) {
                if ($type === 'sale_return') {
                    // Reduce customer debt
                    $returnInvoice->client->decrement('current_balance', $subtotal);
                } else {
                    // Reduce supplier debt owed to them
                    $returnInvoice->client->increment('current_balance', $subtotal);
                }
            }

            DB::commit();
            return redirect()->route('invoices.returns.index')->with('success', 'تم اعتماد إشعار المرتجع وتحديث المخزون والحساب المالي بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء معالجة المرتجع: ' . $e->getMessage());
        }
    }
}

