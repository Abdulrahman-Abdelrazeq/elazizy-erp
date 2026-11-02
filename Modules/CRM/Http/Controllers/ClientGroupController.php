<?php

namespace Modules\CRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Entities\ClientGroup;

class ClientGroupController extends Controller
{
    /**
     * List customer groups and pricing tiers
     */
    public function index()
    {
        $groups = ClientGroup::withCount('clients')->latest()->get();
        return view('crm::groups.index', compact('groups'));
    }

    /**
     * Store new group
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:100',
            'price_tier'          => 'required|in:retail,wholesale,half_wholesale',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        ClientGroup::create([
            'name'                => $validated['name'],
            'price_tier'          => $validated['price_tier'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0.00,
            'is_active'           => true,
        ]);

        return back()->with('success', 'تمت إضافة شريحة تصنيف العملاء بنجاح!');
    }

    /**
     * Update group
     */
    public function update(Request $request, ClientGroup $group)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:100',
            'price_tier'          => 'required|in:retail,wholesale,half_wholesale',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'is_active'           => 'nullable|boolean',
        ]);

        $group->update([
            'name'                => $validated['name'],
            'price_tier'          => $validated['price_tier'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0.00,
            'is_active'           => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'تم تعديل الشريحة بنجاح!');
    }

    /**
     * Delete group
     */
    public function destroy(ClientGroup $group)
    {
        if ($group->clients()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذه الشريحة لأنها مرتبطة بعملاء مسجلين!');
        }

        $group->delete();
        return back()->with('success', 'تم حذف الشريحة بنجاح.');
    }
}

