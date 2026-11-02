<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Entities\Client;
use Modules\CRM\Entities\DebtSchedule;

class DebtScheduleController extends Controller
{
    /**
     * List debt schedules and installments
     */
    public function index(Request $request)
    {
        $query = DebtSchedule::with('client');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        $schedules = $query->orderBy('due_date')->paginate(15);
        $clients = Client::where('is_active', true)->get();

        // Update overdue status
        DebtSchedule::where('status', 'pending')
            ->where('due_date', '<', now()->toDateString())
            ->update(['status' => 'overdue']);

        $stats = [
            'total_pending' => DebtSchedule::where('status', 'pending')->sum('amount'),
            'total_overdue' => DebtSchedule::where('status', 'overdue')->sum('amount'),
            'total_paid'    => DebtSchedule::where('status', 'paid')->sum('paid_amount'),
        ];

        return view('crm::debts.index', compact('schedules', 'clients', 'stats'));
    }

    /**
     * Store new installment/schedule
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount'    => 'required|numeric|min:0.01',
            'due_date'  => 'required|date',
            'notes'     => 'nullable|string',
        ]);

        DebtSchedule::create([
            'client_id' => $validated['client_id'],
            'amount'    => $validated['amount'],
            'due_date'  => $validated['due_date'],
            'status'    => $validated['due_date'] < now()->toDateString() ? 'overdue' : 'pending',
            'notes'     => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تمت جدولة القسط / الدين بنجاح!');
    }

    /**
     * Mark debt installment as paid
     */
    public function markPaid(DebtSchedule $schedule)
    {
        $schedule->update([
            'paid_amount' => $schedule->amount,
            'paid_at'     => now(),
            'status'      => 'paid',
        ]);

        // Reduce client debt balance
        if ($schedule->client) {
            $schedule->client->decrement('current_balance', $schedule->amount);
        }

        return back()->with('success', 'تم تسجيل سداد القسط وتحديث رصيد العميل بنجاح!');
    }
}

