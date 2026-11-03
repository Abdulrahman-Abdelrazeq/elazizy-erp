<?php

namespace Modules\Branch\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Branch\Entities\Branch;
use Modules\Inventory\Entities\Store;
use Modules\Treasury\Entities\Treasury;

class BranchController extends Controller
{
    /**
     * List all Branches
     */
    public function index(Request $request)
    {
        $query = Branch::with(['parentBranch', 'stores', 'treasuries', 'assignedUsers'])->latest();

        if ($request->filled('branch_type')) {
            $query->where('branch_type', $request->branch_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('manager_name', 'like', "%{$s}%");
            });
        }

        $branches = $query->paginate(15)->withQueryString();

        $stats = [
            'total_branches' => Branch::count(),
            'active_count'   => Branch::where('is_active', true)->count(),
            'total_stores'   => Store::count(),
            'total_treasury' => Treasury::count(),
        ];

        return view('branch::branches.index', compact('branches', 'stats'));
    }

    /**
     * Show form to create new branch
     */
    public function create()
    {
        $parentBranches = Branch::where('is_active', true)->get();
        return view('branch::branches.create', compact('parentBranches'));
    }

    /**
     * Store new branch
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'name'                    => 'required|string|max:150',
            'code'                    => 'nullable|string|max:50',
            'branch_type'             => 'required|in:hq,retail,wholesale,distribution_center,warehouse_only',
            'parent_id'               => 'nullable|exists:branches,id',
            'phone'                   => 'nullable|string|max:50',
            'email'                   => 'nullable|email|max:150',
            'manager_name'            => 'nullable|string|max:150',
            'city'                    => 'nullable|string|max:100',
            'district'                => 'nullable|string|max:100',
            'address'                 => 'nullable|string|max:255',
            'commercial_registration' => 'nullable|string|max:100',
            'tax_number'              => 'nullable|string|max:100',
            'monthly_budget'          => 'nullable|numeric|min:0',
            'is_active'               => 'nullable|boolean',
            'is_main'                 => 'nullable|boolean',
        ]);

        $code = $validated['code'] ?: 'BR-' . str_pad((string)(Branch::count() + 1), 3, '0', STR_PAD_LEFT);

        $branch = Branch::create([
            'tenant_id'               => $tenantId,
            'parent_id'               => $validated['parent_id'] ?? null,
            'name'                    => $validated['name'],
            'code'                    => $code,
            'branch_type'             => $validated['branch_type'],
            'phone'                   => $validated['phone'] ?? null,
            'email'                   => $validated['email'] ?? null,
            'manager_name'            => $validated['manager_name'] ?? null,
            'city'                    => $validated['city'] ?? null,
            'district'                => $validated['district'] ?? null,
            'address'                 => $validated['address'] ?? null,
            'commercial_registration' => $validated['commercial_registration'] ?? null,
            'tax_number'              => $validated['tax_number'] ?? null,
            'monthly_budget'          => $validated['monthly_budget'] ?? 0.00,
            'is_main'                 => $request->boolean('is_main', false),
            'is_active'               => $request->boolean('is_active', true),
        ]);

        return redirect()->route('branches.branches.show', $branch->id)
            ->with('success', 'تم تسجيل الفرع / مركز التوزيع بنجاح! كود الفرع: ' . $code);
    }

    /**
     * Show 360-degree Branch Profile
     */
    public function show(Branch $branch)
    {
        $branch->load([
            'parentBranch',
            'subBranches',
            'stores.stocks.item',
            'treasuries',
            'assignedUsers',
            'employees',
            'outboundTransfers' => fn($q) => $q->latest()->take(5),
            'inboundTransfers'  => fn($q) => $q->latest()->take(5),
        ]);

        $inventoryValue = $branch->getInventoryValue();
        $monthlySales = $branch->getMonthlySales();

        return view('branch::branches.show', compact('branch', 'inventoryValue', 'monthlySales'));
    }

    /**
     * Show form to edit branch
     */
    public function edit(Branch $branch)
    {
        $parentBranches = Branch::where('id', '!=', $branch->id)->where('is_active', true)->get();
        return view('branch::branches.edit', compact('branch', 'parentBranches'));
    }

    /**
     * Update branch
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:150',
            'code'                    => 'nullable|string|max:50',
            'branch_type'             => 'required|in:hq,retail,wholesale,distribution_center,warehouse_only',
            'parent_id'               => 'nullable|exists:branches,id',
            'phone'                   => 'nullable|string|max:50',
            'email'                   => 'nullable|email|max:150',
            'manager_name'            => 'nullable|string|max:150',
            'city'                    => 'nullable|string|max:100',
            'district'                => 'nullable|string|max:100',
            'address'                 => 'nullable|string|max:255',
            'commercial_registration' => 'nullable|string|max:100',
            'tax_number'              => 'nullable|string|max:100',
            'monthly_budget'          => 'nullable|numeric|min:0',
            'is_active'               => 'required|boolean',
            'is_main'                 => 'required|boolean',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.branches.show', $branch->id)
            ->with('success', 'تم تحديث بيانات الفرع بنجاح.');
    }

    /**
     * Delete branch
     */
    public function destroy(Branch $branch)
    {
        if ($branch->is_main) {
            return back()->with('error', 'لا يمكن حذف الفرع الرئيسي للشركة!');
        }

        if ($branch->stores()->exists() || $branch->treasuries()->exists()) {
            return back()->with('error', 'لا يمكن حذف الفرع لارتباطه بمستودعات أو خزائن مالية نشطة!');
        }

        $branch->delete();
        return redirect()->route('branches.branches.index')->with('success', 'تم حذف الفرع بنجاح.');
    }
}
