<?php

namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Entities\Client;
use Modules\Invoice\Entities\Invoice;
use Modules\Treasury\Entities\ExpenseCategory;
use Modules\Treasury\Entities\Treasury;
use Modules\Treasury\Entities\TreasuryVoucher;

class VoucherController extends Controller
{
    /**
     * Vouchers Listing (Receipts & Payments)
     */
    public function index(Request $request)
    {
        $query = TreasuryVoucher::with(['treasury', 'category', 'client', 'creator'])->latest('date')->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('treasury_id')) {
            $query->where('treasury_id', $request->treasury_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('voucher_number', 'like', "%{$s}%")
                  ->orWhere('received_from_or_paid_to', 'like', "%{$s}%")
                  ->orWhere('notes', 'like', "%{$s}%")
                  ->orWhere('reference_number', 'like', "%{$s}%");
            });
        }

        $vouchers = $query->paginate(15)->withQueryString();

        $stats = [
            'total_receipts' => (float) TreasuryVoucher::where('type', 'receipt')->where('status', 'approved')->sum('amount'),
            'total_payments' => (float) TreasuryVoucher::where('type', 'payment')->where('status', 'approved')->sum('amount'),
            'receipts_count' => TreasuryVoucher::where('type', 'receipt')->count(),
            'payments_count' => TreasuryVoucher::where('type', 'payment')->count(),
        ];
        $stats['net_flow'] = $stats['total_receipts'] - $stats['total_payments'];

        $treasuries = Treasury::where('is_active', true)->get();
        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('treasury::vouchers.index', compact('vouchers', 'stats', 'treasuries', 'categories'));
    }

    /**
     * Show Create Voucher Form
     */
    public function create(Request $request)
    {
        $type = $request->input('type', 'receipt');
        if (!in_array($type, ['receipt', 'payment'])) {
            $type = 'receipt';
        }

        $treasuries = Treasury::where('is_active', true)->get();
        $categories = ExpenseCategory::where('type', $type === 'receipt' ? 'revenue' : 'expense')
            ->where('is_active', true)
            ->get();

        $customers = Client::customers()->where('is_active', true)->get();
        $suppliers = Client::suppliers()->where('is_active', true)->get();

        // Pending invoices for optional direct settlement
        $unpaidSales = Invoice::where('type', 'sale')->whereIn('status', ['approved', 'partial'])->get();
        $unpaidPurchases = Invoice::where('type', 'purchase')->whereIn('status', ['approved', 'partial'])->get();

        $voucherNumber = TreasuryVoucher::generateNumber($type);

        return view('treasury::vouchers.create', compact(
            'type',
            'treasuries',
            'categories',
            'customers',
            'suppliers',
            'unpaidSales',
            'unpaidPurchases',
            'voucherNumber'
        ));
    }

    /**
     * Store Voucher & Execute Financial Movement
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'                     => 'required|in:receipt,payment',
            'treasury_id'              => 'required|exists:treasuries,id',
            'category_id'              => 'nullable|exists:expense_categories,id',
            'client_id'                => 'nullable|exists:clients,id',
            'invoice_id'               => 'nullable|exists:invoices,id',
            'amount'                   => 'required|numeric|min:0.01',
            'date'                     => 'required|date',
            'payment_method'           => 'required|in:cash,bank_transfer,check,online,card',
            'reference_number'         => 'nullable|string|max:100',
            'received_from_or_paid_to' => 'nullable|string|max:200',
            'notes'                    => 'nullable|string',
        ]);

        $treasury = Treasury::findOrFail($validated['treasury_id']);
        $amount = (float) $validated['amount'];

        // If payment, ensure treasury has sufficient funds
        if ($validated['type'] === 'payment' && $treasury->current_balance < $amount) {
            return back()->withInput()->with('error', 'عفواً، رصيد الخزينة الحالي (' . formatMoney($treasury->current_balance) . ') لا يكفي لإتمام هذا الصرف!');
        }

        DB::beginTransaction();
        try {
            $voucherNumber = TreasuryVoucher::generateNumber($validated['type']);

            $voucher = TreasuryVoucher::create([
                'branch_id'                => activeBranch() ? activeBranch()->id : $treasury->branch_id,
                'treasury_id'              => $treasury->id,
                'voucher_number'           => $voucherNumber,
                'type'                     => $validated['type'],
                'category_id'              => $validated['category_id'] ?? null,
                'client_id'                => $validated['client_id'] ?? null,
                'invoice_id'               => $validated['invoice_id'] ?? null,
                'amount'                   => $amount,
                'date'                     => $validated['date'],
                'payment_method'           => $validated['payment_method'],
                'reference_number'         => $validated['reference_number'] ?? null,
                'received_from_or_paid_to' => $validated['received_from_or_paid_to'] ?? null,
                'notes'                    => $validated['notes'] ?? null,
                'status'                   => 'approved',
                'created_by'               => Auth::id(),
            ]);

            // Execute Cash/Bank Impact
            if ($validated['type'] === 'receipt') {
                $desc = 'سند قبض تحصيل رقم: ' . $voucherNumber . ' (' . ($validated['received_from_or_paid_to'] ?? 'إيداع') . ')';
                $treasury->credit($amount, 'voucher', $voucher->id, $desc, \Carbon\Carbon::parse($validated['date']));

                // If customer debt collection
                if (!empty($validated['client_id'])) {
                    $client = Client::find($validated['client_id']);
                    if ($client) {
                        $client->decrement('balance', $amount);
                    }
                }

                // If linked invoice
                if (!empty($validated['invoice_id'])) {
                    $invoice = Invoice::find($validated['invoice_id']);
                    if ($invoice) {
                        $invoice->increment('paid_amount', $amount);
                        $invoice->remaining_amount = max(0, $invoice->grand_total - $invoice->paid_amount);
                        if ($invoice->remaining_amount <= 0) {
                            $invoice->status = 'paid';
                        } else {
                            $invoice->status = 'partial';
                        }
                        $invoice->save();
                    }
                }
            } else {
                $desc = 'سند صرف دفع رقم: ' . $voucherNumber . ' (' . ($validated['received_from_or_paid_to'] ?? 'صرف') . ')';
                $treasury->debit($amount, 'voucher', $voucher->id, $desc, \Carbon\Carbon::parse($validated['date']));

                // If supplier debt payment
                if (!empty($validated['client_id'])) {
                    $client = Client::find($validated['client_id']);
                    if ($client) {
                        $client->decrement('balance', $amount);
                    }
                }

                // If linked purchase invoice
                if (!empty($validated['invoice_id'])) {
                    $invoice = Invoice::find($validated['invoice_id']);
                    if ($invoice) {
                        $invoice->increment('paid_amount', $amount);
                        $invoice->remaining_amount = max(0, $invoice->grand_total - $invoice->paid_amount);
                        if ($invoice->remaining_amount <= 0) {
                            $invoice->status = 'paid';
                        } else {
                            $invoice->status = 'partial';
                        }
                        $invoice->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('treasury.vouchers.show', $voucher->id)
                ->with('success', 'تم تسجيل واعتِماد السند المالي وتحديث رصيد الخزينة بنجاح!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء معالجة السند: ' . $e->getMessage());
        }
    }

    /**
     * Show Voucher Details
     */
    public function show(TreasuryVoucher $voucher)
    {
        $voucher->load(['treasury', 'category', 'client', 'invoice', 'creator']);
        return view('treasury::vouchers.show', compact('voucher'));
    }

    /**
     * Printable Voucher Slip
     */
    public function print(TreasuryVoucher $voucher)
    {
        $voucher->load(['treasury', 'category', 'client', 'creator']);
        return view('treasury::vouchers.print', compact('voucher'));
    }

    /**
     * Cancel / Void Voucher
     */
    public function destroy(TreasuryVoucher $voucher)
    {
        if ($voucher->status === 'cancelled') {
            return back()->with('warning', 'هذا السند ملغي بالفعل مسبقاً.');
        }

        DB::beginTransaction();
        try {
            $treasury = $voucher->treasury;

            // Reverse the financial effect
            if ($voucher->type === 'receipt') {
                $treasury->debit($voucher->amount, 'voucher', $voucher->id, 'إلغاء سند قبض رقم: ' . $voucher->voucher_number);
                if ($voucher->client_id) {
                    Client::where('id', $voucher->client_id)->increment('balance', $voucher->amount);
                }
            } else {
                $treasury->credit($voucher->amount, 'voucher', $voucher->id, 'إلغاء سند صرف رقم: ' . $voucher->voucher_number);
                if ($voucher->client_id) {
                    Client::where('id', $voucher->client_id)->increment('balance', $voucher->amount);
                }
            }

            $voucher->update(['status' => 'cancelled']);

            DB::commit();
            return back()->with('success', 'تم إلغاء السند المالي وعكس الأثر المالي في الخزينة بنجاح.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'فشل إلغاء السند: ' . $e->getMessage());
        }
    }
}
