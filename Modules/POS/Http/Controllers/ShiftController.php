<?php

namespace Modules\POS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\POS\Entities\CashierShift;
use Modules\POS\Entities\PosTerminal;

class ShiftController extends Controller
{
    /**
     * Shifts Listing & Cash Drawer Log
     */
    public function index(Request $request)
    {
        $query = CashierShift::with(['user', 'terminal', 'closedBy'])->latest('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('terminal_id')) {
            $query->where('terminal_id', $request->terminal_id);
        }

        $shifts = $query->paginate(15)->withQueryString();

        $stats = [
            'active_shifts' => CashierShift::where('status', 'open')->count(),
            'today_sales'   => CashierShift::whereDate('started_at', now()->toDateString())->sum('cash_sales'),
            'total_shifts'  => CashierShift::count(),
        ];

        $terminals = PosTerminal::where('is_active', true)->get();

        return view('pos::shifts.index', compact('shifts', 'stats', 'terminals'));
    }

    /**
     * Open a new shift
     */
    public function open(Request $request)
    {
        $validated = $request->validate([
            'terminal_id'     => 'required|exists:pos_terminals,id',
            'opening_balance' => 'required|numeric|min:0',
            'start_notes'     => 'nullable|string',
        ]);

        $user = Auth::user();

        // Check if user already has an active open shift
        $existing = CashierShift::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return redirect()->route('pos.screen')->with('warning', 'لديك بالفعل وردية عمل مفتوحة حالياً.');
        }

        $terminal = PosTerminal::findOrFail($validated['terminal_id']);
        $shiftNumber = CashierShift::generateNumber();

        $shift = CashierShift::create([
            'branch_id'       => $terminal->branch_id,
            'terminal_id'     => $terminal->id,
            'user_id'         => $user->id,
            'shift_number'    => $shiftNumber,
            'started_at'      => now(),
            'status'          => 'open',
            'opening_balance' => (float) $validated['opening_balance'],
            'expected_cash'   => (float) $validated['opening_balance'],
            'start_notes'     => $validated['start_notes'] ?? null,
        ]);

        return redirect()->route('pos.screen')->with('success', 'تم فتح الوردية واستلام العهدة النقدية بنجاح! مرحباً بك.');
    }

    /**
     * Close active shift and calculate cash variance
     */
    public function close(Request $request, CashierShift $shift)
    {
        $validated = $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'end_notes'   => 'nullable|string',
        ]);

        // Recalculate totals first
        $shift->recalculateTotals();

        $actualCash = (float) $validated['actual_cash'];
        $expectedCash = (float) $shift->expected_cash;
        $variance = $actualCash - $expectedCash;

        $shift->update([
            'status'          => 'closed',
            'ended_at'        => now(),
            'actual_cash'     => $actualCash,
            'cash_difference' => $variance,
            'end_notes'       => $validated['end_notes'] ?? null,
            'closed_by'       => Auth::id(),
        ]);

        return redirect()->route('pos.shifts.show', $shift->id)->with('success', 'تم إغلاق الوردية والدرج بنجاح وتم توليد تقرير Z النهائي.');
    }

    /**
     * Show detailed Shift Z-Report
     */
    public function show(CashierShift $shift)
    {
        $shift->recalculateTotals();
        $shift->load(['user', 'terminal', 'closedBy', 'invoices.client']);

        return view('pos::shifts.show', compact('shift'));
    }

    /**
     * Print 80mm Z-Report Slip
     */
    public function printZReport(CashierShift $shift)
    {
        $shift->recalculateTotals();
        $shift->load(['user', 'terminal', 'closedBy']);

        return view('pos::shifts.print_z', compact('shift'));
    }
}

