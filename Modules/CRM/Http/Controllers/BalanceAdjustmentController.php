<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\CRM\Entities\BalanceAdjustment;
use Modules\CRM\Entities\Client;

class BalanceAdjustmentController extends Controller
{
    /**
     * List balance settlements and write-offs
     */
    public function index()
    {
        $adjustments = BalanceAdjustment::with(['client', 'creator'])->latest()->paginate(15);
        $clients = Client::where('is_active', true)->get();

        return view('crm::adjustments.index', compact('adjustments', 'clients'));
    }

    /**
     * Store balance write-off or adjustment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'type'      => 'required|in:write_off,discount,opening_diff',
            'amount'    => 'required|numeric|min:0.01',
            'date'      => 'required|date',
            'notes'     => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $client = Client::findOrFail($validated['client_id']);

            BalanceAdjustment::create([
                'client_id'  => $client->id,
                'type'       => $validated['type'],
                'amount'     => $validated['amount'],
                'date'       => $validated['date'],
                'notes'      => $validated['notes'],
                'created_by' => Auth::id(),
            ]);

            // Deduct from client balance (write-off decreases debt)
            $client->decrement('current_balance', $validated['amount']);

            DB::commit();
            return back()->with('success', 'تم شطب / تسوية الرصيد وتحديث كشف الحساب بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تنفيذ التسوية: ' . $e->getMessage());
        }
    }
}

