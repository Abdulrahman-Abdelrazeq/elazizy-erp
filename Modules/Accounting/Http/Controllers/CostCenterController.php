<?php

namespace Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\CostCenter;

class CostCenterController extends Controller
{
    /**
     * Cost Centers Listing
     */
    public function index(Request $request)
    {
        $query = CostCenter::with('parent')->latest('id');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%");
            });
        }

        $costCenters = $query->paginate(15)->withQueryString();
        $parents = CostCenter::whereNull('parent_id')->where('is_active', true)->get();

        $stats = [
            'total'  => CostCenter::count(),
            'active' => CostCenter::where('is_active', true)->count(),
        ];

        return view('accounting::cost-centers.index', compact('costCenters', 'parents', 'stats'));
    }

    /**
     * Store Cost Center
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:cost_centers,id',
            'code'      => 'required|string|max:50',
            'name'      => 'required|string|max:150',
            'notes'     => 'nullable|string',
        ]);

        CostCenter::create([
            'parent_id' => $validated['parent_id'] ?? null,
            'code'      => $validated['code'],
            'name'      => $validated['name'],
            'is_active' => true,
            'notes'     => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم إنشاء مركز التكلفة بنجاح!');
    }

    /**
     * Update Cost Center
     */
    public function update(Request $request, CostCenter $costCenter)
    {
        $validated = $request->validate([
            'parent_id' => 'nullable|exists:cost_centers,id',
            'name'      => 'required|string|max:150',
            'is_active' => 'nullable|boolean',
            'notes'     => 'nullable|string',
        ]);

        $costCenter->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'name'      => $validated['name'],
            'is_active' => $request->boolean('is_active', true),
            'notes'     => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'تم تحديث مركز التكلفة بنجاح!');
    }

    /**
     * Delete Cost Center
     */
    public function destroy(CostCenter $costCenter)
    {
        if ($costCenter->journalLines()->exists()) {
            return back()->with('error', 'لا يمكن حذف مركز التكلفة لوجود قيود يومية مرتبطة به!');
        }

        $costCenter->delete();
        return back()->with('success', 'تم حذف مركز التكلفة بنجاح.');
    }
}

