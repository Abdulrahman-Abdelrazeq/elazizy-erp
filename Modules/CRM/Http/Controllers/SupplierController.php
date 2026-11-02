<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Entities\Client;
use Modules\Setting\Entities\Location;

class SupplierController extends Controller
{
    /**
     * Suppliers Listing
     */
    public function index(Request $request)
    {
        $query = Client::suppliers()->with(['location']);

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('tax_number', 'like', "%{$s}%");
            });
        }

        $suppliers = $query->latest()->paginate(15)->withQueryString();

        // Supplier Stats
        $stats = [
            'total_suppliers' => Client::suppliers()->count(),
            'total_payable'   => abs(Client::suppliers()->where('current_balance', '<', 0)->sum('current_balance')),
            'total_receivable'=> Client::suppliers()->where('current_balance', '>', 0)->sum('current_balance'),
        ];

        return view('crm::suppliers.index', compact('suppliers', 'stats'));
    }

    /**
     * Show create supplier form
     */
    public function create()
    {
        $cities = Location::where('type', 'city')->get();
        return view('crm::suppliers.create', compact('cities'));
    }

    /**
     * Store newly created supplier
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:150',
            'company_name'         => 'nullable|string|max:150',
            'code'                 => 'nullable|string|max:50',
            'phone'                => 'nullable|string|max:50',
            'mobile'               => 'nullable|string|max:50',
            'email'                => 'nullable|email|max:150',
            'tax_number'           => 'nullable|string|max:50',
            'commercial_register'  => 'nullable|string|max:50',
            'location_id'          => 'nullable|exists:locations,id',
            'address'              => 'nullable|string|max:255',
            'opening_balance'      => 'nullable|numeric|min:0',
            'opening_balance_type' => 'required|in:debit,credit',
            'notes'                => 'nullable|string',
        ]);

        $openingBal = (float) ($validated['opening_balance'] ?? 0);
        $currentBal = $validated['opening_balance_type'] === 'credit' ? -$openingBal : $openingBal;
        $code = $validated['code'] ?: 'SUP-' . rand(1000, 9999);

        Client::create([
            'type'                 => 'supplier',
            'name'                 => $validated['name'],
            'company_name'         => $validated['company_name'] ?? null,
            'code'                 => $code,
            'phone'                => $validated['phone'] ?? null,
            'mobile'               => $validated['mobile'] ?? null,
            'email'                => $validated['email'] ?? null,
            'tax_number'           => $validated['tax_number'] ?? null,
            'commercial_register'  => $validated['commercial_register'] ?? null,
            'location_id'          => $validated['location_id'] ?? null,
            'address'              => $validated['address'] ?? null,
            'opening_balance'      => $openingBal,
            'opening_balance_type' => $validated['opening_balance_type'],
            'current_balance'      => $currentBal,
            'notes'                => $validated['notes'] ?? null,
            'is_active'            => true,
        ]);

        return redirect()->route('crm.suppliers.index')->with('success', 'تمت إضافة المورد بنجاح!');
    }

    /**
     * Show edit supplier form
     */
    public function edit(Client $supplier)
    {
        $cities = Location::where('type', 'city')->get();
        return view('crm::suppliers.edit', compact('supplier', 'cities'));
    }

    /**
     * Update supplier
     */
    public function update(Request $request, Client $supplier)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:150',
            'company_name'        => 'nullable|string|max:150',
            'code'                => 'nullable|string|max:50',
            'phone'               => 'nullable|string|max:50',
            'mobile'              => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:150',
            'tax_number'          => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'location_id'         => 'nullable|exists:locations,id',
            'address'             => 'nullable|string|max:255',
            'notes'               => 'nullable|string',
            'is_active'           => 'nullable|boolean',
        ]);

        $supplier->update([
            'name'                => $validated['name'],
            'company_name'        => $validated['company_name'] ?? null,
            'code'                => $validated['code'] ?? $supplier->code,
            'phone'               => $validated['phone'] ?? null,
            'mobile'              => $validated['mobile'] ?? null,
            'email'               => $validated['email'] ?? null,
            'tax_number'          => $validated['tax_number'] ?? null,
            'commercial_register' => $validated['commercial_register'] ?? null,
            'location_id'         => $validated['location_id'] ?? null,
            'address'             => $validated['address'] ?? null,
            'notes'               => $validated['notes'] ?? null,
            'is_active'           => $request->boolean('is_active', true),
        ]);

        return redirect()->route('crm.suppliers.index')->with('success', 'تم تعديل بيانات المورد بنجاح!');
    }

    /**
     * Delete supplier
     */
    public function destroy(Client $supplier)
    {
        $supplier->delete();
        return back()->with('success', 'تم حذف المورد بنجاح.');
    }

    /**
     * Statement of Account (كشف حساب مورد)
     */
    public function statement(Client $supplier)
    {
        $supplier->load(['debtSchedules', 'balanceAdjustments', 'notes.creator']);
        return view('crm::suppliers.statement', compact('supplier'));
    }
}

