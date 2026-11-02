<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Entities\Client;
use Modules\CRM\Entities\ClientGroup;
use Modules\Setting\Entities\Location;

class CustomerController extends Controller
{
    /**
     * Customers Listing
     */
    public function index(Request $request)
    {
        $query = Client::customers()->with(['group', 'location']);

        // Search
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('mobile', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('tax_number', 'like', "%{$s}%");
            });
        }

        // Group filter
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Debt status filter
        if ($request->debt_status === 'debtors') {
            $query->debtors();
        } elseif ($request->debt_status === 'creditors') {
            $query->creditors();
        }

        $customers = $query->latest()->paginate(15)->withQueryString();

        // Customer Stats
        $stats = [
            'total_customers' => Client::customers()->count(),
            'total_debit'     => Client::customers()->where('current_balance', '>', 0)->sum('current_balance'),
            'total_credit'    => abs(Client::customers()->where('current_balance', '<', 0)->sum('current_balance')),
            'over_limit'      => Client::customers()->whereRaw('credit_limit > 0 AND current_balance > credit_limit')->count(),
        ];

        $groups = ClientGroup::where('is_active', true)->get();

        return view('crm::customers.index', compact('customers', 'groups', 'stats'));
    }

    /**
     * Show create customer form
     */
    public function create()
    {
        $groups = ClientGroup::where('is_active', true)->get();
        $cities = Location::where('type', 'city')->get();

        return view('crm::customers.create', compact('groups', 'cities'));
    }

    /**
     * Store newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:150',
            'company_name'         => 'nullable|string|max:150',
            'code'                 => 'nullable|string|max:50',
            'group_id'             => 'nullable|exists:client_groups,id',
            'phone'                => 'nullable|string|max:50',
            'mobile'               => 'nullable|string|max:50',
            'email'                => 'nullable|email|max:150',
            'tax_number'           => 'nullable|string|max:50',
            'commercial_register'  => 'nullable|string|max:50',
            'national_id'          => 'nullable|string|max:50',
            'location_id'          => 'nullable|exists:locations,id',
            'address'              => 'nullable|string|max:255',
            'credit_limit'         => 'nullable|numeric|min:0',
            'credit_days'          => 'nullable|integer|min:0',
            'opening_balance'      => 'nullable|numeric|min:0',
            'opening_balance_type' => 'required|in:debit,credit',
            'notes'                => 'nullable|string',
        ]);

        $openingBal = (float) ($validated['opening_balance'] ?? 0);
        $currentBal = $validated['opening_balance_type'] === 'credit' ? -$openingBal : $openingBal;
        $code = $validated['code'] ?: 'CST-' . rand(1000, 9999);

        Client::create([
            'type'                 => 'customer',
            'name'                 => $validated['name'],
            'company_name'         => $validated['company_name'] ?? null,
            'code'                 => $code,
            'group_id'             => $validated['group_id'] ?? null,
            'phone'                => $validated['phone'] ?? null,
            'mobile'               => $validated['mobile'] ?? null,
            'email'                => $validated['email'] ?? null,
            'tax_number'           => $validated['tax_number'] ?? null,
            'commercial_register'  => $validated['commercial_register'] ?? null,
            'national_id'          => $validated['national_id'] ?? null,
            'location_id'          => $validated['location_id'] ?? null,
            'address'              => $validated['address'] ?? null,
            'credit_limit'         => $validated['credit_limit'] ?? 0.00,
            'credit_days'          => $validated['credit_days'] ?? 30,
            'opening_balance'      => $openingBal,
            'opening_balance_type' => $validated['opening_balance_type'],
            'current_balance'      => $currentBal,
            'notes'                => $validated['notes'] ?? null,
            'is_active'            => true,
        ]);

        return redirect()->route('crm.customers.index')->with('success', 'تمت إضافة العميل بنجاح!');
    }

    /**
     * Show edit customer form
     */
    public function edit(Client $customer)
    {
        $groups = ClientGroup::where('is_active', true)->get();
        $cities = Location::where('type', 'city')->get();

        return view('crm::customers.edit', compact('customer', 'groups', 'cities'));
    }

    /**
     * Update customer
     */
    public function update(Request $request, Client $customer)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:150',
            'company_name'        => 'nullable|string|max:150',
            'code'                => 'nullable|string|max:50',
            'group_id'            => 'nullable|exists:client_groups,id',
            'phone'               => 'nullable|string|max:50',
            'mobile'              => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:150',
            'tax_number'          => 'nullable|string|max:50',
            'commercial_register' => 'nullable|string|max:50',
            'national_id'         => 'nullable|string|max:50',
            'location_id'         => 'nullable|exists:locations,id',
            'address'             => 'nullable|string|max:255',
            'credit_limit'        => 'nullable|numeric|min:0',
            'credit_days'         => 'nullable|integer|min:0',
            'notes'               => 'nullable|string',
            'is_active'           => 'nullable|boolean',
        ]);

        $customer->update([
            'name'                => $validated['name'],
            'company_name'        => $validated['company_name'] ?? null,
            'code'                => $validated['code'] ?? $customer->code,
            'group_id'            => $validated['group_id'] ?? null,
            'phone'               => $validated['phone'] ?? null,
            'mobile'              => $validated['mobile'] ?? null,
            'email'               => $validated['email'] ?? null,
            'tax_number'          => $validated['tax_number'] ?? null,
            'commercial_register' => $validated['commercial_register'] ?? null,
            'national_id'         => $validated['national_id'] ?? null,
            'location_id'         => $validated['location_id'] ?? null,
            'address'             => $validated['address'] ?? null,
            'credit_limit'        => $validated['credit_limit'] ?? 0.00,
            'credit_days'         => $validated['credit_days'] ?? 30,
            'notes'               => $validated['notes'] ?? null,
            'is_active'           => $request->boolean('is_active', true),
        ]);

        return redirect()->route('crm.customers.index')->with('success', 'تم تعديل بيانات العميل بنجاح!');
    }

    /**
     * Delete customer
     */
    public function destroy(Client $customer)
    {
        $customer->delete();
        return back()->with('success', 'تم حذف العميل بنجاح.');
    }

    /**
     * Statement of Account (كشف حساب عميل)
     */
    public function statement(Client $customer)
    {
        $customer->load(['group', 'debtSchedules', 'balanceAdjustments', 'notes.creator']);

        return view('crm::customers.statement', compact('customer'));
    }
}
