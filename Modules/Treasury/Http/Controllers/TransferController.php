<?php

namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Treasury\Entities\Treasury;
use Modules\Treasury\Entities\TreasuryTransfer;

class TransferController extends Controller
{
    /**
     * Internal Transfers Listing
     */
    public function index(Request $request)
    {
        $query = TreasuryTransfer::with(['fromTreasury', 'toTreasury', 'creator'])->latest('transfer_date')->latest('id');

        if ($request->filled('from_treasury_id')) {
            $query->where('from_treasury_id', $request->from_treasury_id);
        }

        if ($request->filled('to_treasury_id')) {
            $query->where('to_treasury_id', $request->to_treasury_id);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('transfer_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('transfer_date', '<=', $request->to_date);
        }

        $transfers = $query->paginate(15)->withQueryString();
        $treasuries = Treasury::where('is_active', true)->get();

        $stats = [
            'total_transfers' => (float) TreasuryTransfer::where('status', 'completed')->sum('amount'),
            'total_fees'      => (float) TreasuryTransfer::where('status', 'completed')->sum('transfer_fee'),
            'transfers_count' => TreasuryTransfer::count(),
        ];

        return view('treasury::transfers.index', compact('transfers', 'treasuries', 'stats'));
    }

    /**
     * Show Create Transfer Form
     */
    public function create()
    {
        $treasuries = Treasury::where('is_active', true)->get();
        $transferNumber = TreasuryTransfer::generateNumber();

        return view('treasury::transfers.create', compact('treasuries', 'transferNumber'));
    }

    /**
     * Store and Execute Transfer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_treasury_id' => 'required|exists:treasuries,id|different:to_treasury_id',
            'to_treasury_id'   => 'required|exists:treasuries,id',
            'amount'           => 'required|numeric|min:0.01',
            'transfer_fee'     => 'nullable|numeric|min:0',
            'transfer_date'    => 'required|date',
            'notes'            => 'nullable|string',
        ]);

        $from = Treasury::findOrFail($validated['from_treasury_id']);
        $to = Treasury::findOrFail($validated['to_treasury_id']);
        $amount = (float) $validated['amount'];
        $fee = (float) ($validated['transfer_fee'] ?? 0);
        $totalDeduction = $amount + $fee;

        if ($from->current_balance < $totalDeduction) {
            return back()->withInput()->with('error', 'عفواً، رصيد الخزينة المصدرة (' . formatMoney($from->current_balance) . ') لا يكفي لتغطية مبلغ التحويل مع المصاريف (' . formatMoney($totalDeduction) . ')!');
        }

        DB::beginTransaction();
        try {
            $transferNumber = TreasuryTransfer::generateNumber();

            $transfer = TreasuryTransfer::create([
                'transfer_number'  => $transferNumber,
                'from_treasury_id' => $from->id,
                'to_treasury_id'   => $to->id,
                'amount'           => $amount,
                'transfer_fee'     => $fee,
                'transfer_date'    => $validated['transfer_date'],
                'notes'            => $validated['notes'] ?? null,
                'status'           => 'completed',
                'created_by'       => Auth::id(),
            ]);

            $date = \Carbon\Carbon::parse($validated['transfer_date']);

            // 1. Debit Source Treasury
            $fromDesc = 'تحويل صادر إلى (' . $to->name . ') رقم: ' . $transferNumber . ($fee > 0 ? " (شامل عمولة: " . formatMoney($fee) . ")" : "");
            $from->debit($totalDeduction, 'transfer', $transfer->id, $fromDesc, $date);

            // 2. Credit Destination Treasury
            $toDesc = 'تحويل وارد من (' . $from->name . ') رقم: ' . $transferNumber;
            $to->credit($amount, 'transfer', $transfer->id, $toDesc, $date);

            DB::commit();

            return redirect()->route('treasury.transfers.index')
                ->with('success', 'تم تنفيذ التحويل المالي وتحديث أرصدة الخزائن بنجاح! رقم التحويل: ' . $transferNumber);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'فشل تنفيذ التحويل: ' . $e->getMessage());
        }
    }
}
