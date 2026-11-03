<?php

namespace Modules\Branch\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchUser;

class BranchUserController extends Controller
{
    /**
     * List user branch assignments
     */
    public function index(Request $request)
    {
        $query = BranchUser::with(['branch', 'user'])->latest();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $assignments = $query->paginate(20)->withQueryString();
        $branches = Branch::where('is_active', true)->get();
        $users = User::all();

        return view('branch::users.index', compact('assignments', 'branches', 'users'));
    }

    /**
     * Store new branch user assignment
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'branch_id'  => 'required|exists:branches,id',
            'user_id'    => 'required|exists:users,id',
            'role'       => 'required|string|max:50',
            'is_default' => 'nullable|boolean',
            'can_switch' => 'nullable|boolean',
        ]);

        BranchUser::updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'branch_id' => $validated['branch_id'],
                'user_id'   => $validated['user_id'],
            ],
            [
                'role'       => $validated['role'],
                'is_default' => $request->boolean('is_default', false),
                'can_switch' => $request->boolean('can_switch', true),
            ]
        );

        return back()->with('success', 'تم تعيين المستخدم وتحديد صلاحياته بالفرع بنجاح!');
    }

    /**
     * Delete assignment
     */
    public function destroy(BranchUser $branchUser)
    {
        $branchUser->delete();
        return back()->with('success', 'تم إلغاء تعيين المستخدم من الفرع بنجاح.');
    }
}
