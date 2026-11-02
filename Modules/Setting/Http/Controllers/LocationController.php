<?php

namespace Modules\Setting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Setting\Entities\Location;

class LocationController extends Controller
{
    /**
     * List hierarchical locations
     */
    public function index()
    {
        $countries = Location::where('type', 'country')->with('children.children')->get();
        $allCities = Location::where('type', 'city')->get();

        return view('setting::locations.index', compact('countries', 'allCities'));
    }

    /**
     * Store new location (Country, City, or Delivery Zone)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:100',
            'type'          => 'required|in:country,city,zone',
            'parent_id'     => 'nullable|exists:locations,id',
            'delivery_cost' => 'nullable|numeric|min:0',
        ]);

        Location::create([
            'name'          => $validated['name'],
            'type'          => $validated['type'],
            'parent_id'     => $validated['parent_id'] ?? null,
            'delivery_cost' => $validated['delivery_cost'] ?? 0.00,
            'is_active'     => true,
        ]);

        return back()->with('success', 'تمت إضافة المنطقة الجغرافية بنجاح!');
    }

    /**
     * Delete location
     */
    public function destroy(Location $location)
    {
        $location->children()->delete();
        $location->delete();

        return back()->with('success', 'تم حذف المنطقة وجميع المناطق التابعة لها بنجاح.');
    }
}

