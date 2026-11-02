<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Entities\Unit;

class UnitController extends Controller
{
    /**
     * List measurement units
     */
    public function index()
    {
        $units = Unit::withCount('items')->latest()->get();
        return view('inventory::units.index', compact('units'));
    }

    /**
     * Store new unit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string|max:20',
        ]);

        Unit::create([
            'name'      => $validated['name'],
            'code'      => $validated['code'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'تمت إضافة وحدة القياس بنجاح!');
    }

    /**
     * Delete unit
     */
    public function destroy(Unit $unit)
    {
        if ($unit->items()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذه الوحدة لأنها مرتبطة بأصناف في النظام!');
        }

        $unit->delete();
        return back()->with('success', 'تم حذف وحدة القياس بنجاح.');
    }
}

