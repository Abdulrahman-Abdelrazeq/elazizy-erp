<?php

namespace Modules\POS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Entities\Client;
use Modules\Inventory\Entities\Store;
use Modules\POS\Entities\PosTerminal;

class TerminalController extends Controller
{
    /**
     * Terminals Listing
     */
    public function index()
    {
        $terminals = PosTerminal::with(['store', 'defaultClient', 'shifts' => function ($q) {
            $q->where('status', 'open');
        }])->latest('id')->get();

        $stores = Store::where('is_active', true)->get();
        $customers = Client::customers()->where('is_active', true)->get();

        return view('pos::terminals.index', compact('terminals', 'stores', 'customers'));
    }

    /**
     * Store new POS terminal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'code'              => 'nullable|string|max:50',
            'store_id'          => 'required|exists:stores,id',
            'default_client_id' => 'nullable|exists:clients,id',
            'printer_type'      => 'required|in:thermal_80mm,thermal_57mm,a4',
        ]);

        $code = $validated['code'] ?: 'POS-' . (PosTerminal::count() + 1);

        PosTerminal::create([
            'branch_id'         => activeBranch() ? activeBranch()->id : null,
            'store_id'          => $validated['store_id'],
            'default_client_id' => $validated['default_client_id'] ?? null,
            'name'              => $validated['name'],
            'code'              => $code,
            'printer_type'      => $validated['printer_type'],
            'is_active'         => true,
        ]);

        return back()->with('success', 'تم تعريف نقطة البيع / الكاشير بنجاح!');
    }

    /**
     * Update terminal
     */
    public function update(Request $request, PosTerminal $terminal)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'store_id'          => 'required|exists:stores,id',
            'default_client_id' => 'nullable|exists:clients,id',
            'printer_type'      => 'required|in:thermal_80mm,thermal_57mm,a4',
            'is_active'         => 'nullable|boolean',
        ]);

        $terminal->update([
            'name'              => $validated['name'],
            'store_id'          => $validated['store_id'],
            'default_client_id' => $validated['default_client_id'] ?? null,
            'printer_type'      => $validated['printer_type'],
            'is_active'         => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم تحديث بيانات نقطة البيع بنجاح!');
    }

    /**
     * Delete terminal
     */
    public function destroy(PosTerminal $terminal)
    {
        if ($terminal->shifts()->where('status', 'open')->exists()) {
            return back()->with('error', 'لا يمكن حذف نقطة البيع لوجود وردية عمل مفتوحة عليها حالياً!');
        }

        $terminal->delete();
        return back()->with('success', 'تم حذف نقطة البيع بنجاح.');
    }
}

