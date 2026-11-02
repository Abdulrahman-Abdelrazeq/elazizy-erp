<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;

class StoreController extends Controller
{
    /**
     * List warehouses & stores
     */
    public function index()
    {
        $stores = Store::with(['branch', 'stocks.item'])->latest()->get();
        $branches = Branch::where('is_active', true)->get();

        return view('inventory::stores.index', compact('stores', 'branches'));
    }

    /**
     * Store new warehouse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'code'        => 'nullable|string|max:50',
            'branch_id'   => 'nullable|exists:branches,id',
            'keeper_name' => 'nullable|string|max:100',
            'phone'       => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:255',
            'is_main'     => 'nullable|boolean',
        ]);

        if (!empty($validated['is_main'])) {
            Store::where('is_main', true)->update(['is_main' => false]);
        }

        Store::create([
            'name'        => $validated['name'],
            'code'        => $validated['code'] ?? 'STR-' . rand(10, 99),
            'branch_id'   => $validated['branch_id'] ?? null,
            'keeper_name' => $validated['keeper_name'] ?? null,
            'phone'       => $validated['phone'] ?? null,
            'address'     => $validated['address'] ?? null,
            'is_main'     => $request->boolean('is_main'),
            'is_active'   => true,
        ]);

        return back()->with('success', 'تمت إضافة المستودع بنجاح!');
    }

    /**
     * View inventory balances of a specific warehouse
     */
    public function show(Store $store)
    {
        $stocks = ItemStock::where('store_id', $store->id)
            ->with(['item.category', 'item.unit'])
            ->latest()
            ->paginate(20);

        return view('inventory::stores.show', compact('store', 'stocks'));
    }
}

