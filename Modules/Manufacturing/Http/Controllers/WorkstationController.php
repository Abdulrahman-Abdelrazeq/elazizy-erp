<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Modules\Manufacturing\Entities\Workstation;

class WorkstationController extends Controller
{
    /**
     * List all workstations & production lines
     */
    public function index(Request $request)
    {
        $query = Workstation::with('branch')->latest();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $workstations = $query->paginate(15)->withQueryString();
        $branches = Branch::all();

        $stats = [
            'total'       => Workstation::count(),
            'active'      => Workstation::where('is_active', true)->count(),
            'total_hours' => (float) Workstation::where('is_active', true)->sum('capacity_per_day'),
            'avg_cost'    => (float) Workstation::where('is_active', true)->avg('hourly_cost'),
        ];

        return view('manufacturing::workstations.index', compact('workstations', 'branches', 'stats'));
    }

    /**
     * Store new workstation
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'code'             => 'nullable|string|max:50',
            'branch_id'        => 'nullable|exists:branches,id',
            'hourly_cost'      => 'required|numeric|min:0',
            'capacity_per_day' => 'required|numeric|min:0.5',
            'description'      => 'nullable|string',
        ]);

        $code = $validated['code'] ?: 'WS-' . str_pad((string)(Workstation::count() + 1), 3, '0', STR_PAD_LEFT);

        Workstation::create([
            'tenant_id'        => $tenantId,
            'name'             => $validated['name'],
            'code'             => $code,
            'branch_id'        => $validated['branch_id'] ?? (activeBranch() ? activeBranch()->id : null),
            'hourly_cost'      => $validated['hourly_cost'],
            'capacity_per_day' => $validated['capacity_per_day'],
            'description'      => $validated['description'] ?? null,
            'is_active'        => true,
        ]);

        return back()->with('success', 'تم إضافة محطة العمل / خط الإنتاج بنجاح!');
    }

    /**
     * Update workstation
     */
    public function update(Request $request, Workstation $workstation)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'code'             => 'nullable|string|max:50',
            'branch_id'        => 'nullable|exists:branches,id',
            'hourly_cost'      => 'required|numeric|min:0',
            'capacity_per_day' => 'required|numeric|min:0.5',
            'description'      => 'nullable|string',
            'is_active'        => 'required|boolean',
        ]);

        $workstation->update($validated);

        return back()->with('success', 'تم تحديث بيانات محطة العمل بنجاح.');
    }

    /**
     * Delete workstation
     */
    public function destroy(Workstation $workstation)
    {
        if ($workstation->operations()->exists()) {
            return back()->with('error', 'لا يمكن حذف محطة العمل لارتباطها بعمليات ومراحل إنتاج مسجلة في قوائم المواد!');
        }

        $workstation->delete();
        return back()->with('success', 'تم حذف محطة العمل بنجاح.');
    }
}
