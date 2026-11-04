<?php

namespace Modules\Asset\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Accounting\Entities\Account;
use Modules\Asset\Entities\AssetCategory;

class AssetCategoryController extends Controller
{
    /**
     * List Asset Categories
     */
    public function index(Request $request)
    {
        $categories = AssetCategory::with(['assetAccount', 'depreciationAccount', 'depreciationExpenseAccount'])
            ->withCount('assets')
            ->latest()
            ->paginate(15);

        $stats = [
            'total_categories' => AssetCategory::count(),
            'active_categories'=> AssetCategory::where('is_active', true)->count(),
            'total_assets'     => AssetCategory::withCount('assets')->get()->sum('assets_count'),
            'total_gross_val'  => AssetCategory::all()->sum('total_gross_cost'),
        ];

        return view('asset::categories.index', compact('categories', 'stats'));
    }

    /**
     * Show form to create new category
     */
    public function create()
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;
        $assetAccounts = Account::where('type', 'asset')->orderBy('code')->get();
        $expenseAccounts = Account::where('type', 'expense')->orderBy('code')->get();

        return view('asset::categories.create', compact('assetAccounts', 'expenseAccounts'));
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'name'                            => 'required|string|max:150',
            'code'                            => 'nullable|string|max:50',
            'depreciation_method'             => 'required|in:straight_line,declining_balance,none',
            'depreciation_rate'               => 'required|numeric|min:0|max:100',
            'useful_life_years'               => 'required|numeric|min:0.1|max:100',
            'asset_account_id'                => 'nullable|exists:chart_of_accounts,id',
            'depreciation_account_id'         => 'nullable|exists:chart_of_accounts,id',
            'depreciation_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
            'description'                     => 'nullable|string',
            'is_active'                       => 'nullable|boolean',
        ]);

        $validated['tenant_id'] = $tenantId;
        $validated['is_active'] = $request->boolean('is_active', true);

        AssetCategory::create($validated);

        return redirect()->route('assets.categories.index')->with('success', 'تم إنشاء تصنيف الأصول الثابتة بنجاح.');
    }

    /**
     * Show form to edit category
     */
    public function edit(AssetCategory $category)
    {
        $assetAccounts = Account::where('type', 'asset')->orderBy('code')->get();
        $expenseAccounts = Account::where('type', 'expense')->orderBy('code')->get();

        return view('asset::categories.edit', compact('category', 'assetAccounts', 'expenseAccounts'));
    }

    /**
     * Update category
     */
    public function update(Request $request, AssetCategory $category)
    {
        $validated = $request->validate([
            'name'                            => 'required|string|max:150',
            'code'                            => 'nullable|string|max:50',
            'depreciation_method'             => 'required|in:straight_line,declining_balance,none',
            'depreciation_rate'               => 'required|numeric|min:0|max:100',
            'useful_life_years'               => 'required|numeric|min:0.1|max:100',
            'asset_account_id'                => 'nullable|exists:chart_of_accounts,id',
            'depreciation_account_id'         => 'nullable|exists:chart_of_accounts,id',
            'depreciation_expense_account_id' => 'nullable|exists:chart_of_accounts,id',
            'description'                     => 'nullable|string',
            'is_active'                       => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $category->update($validated);

        return redirect()->route('assets.categories.index')->with('success', 'تم تحديث تصنيف الأصول بنجاح.');
    }

    /**
     * Delete category
     */
    public function destroy(AssetCategory $category)
    {
        if ($category->assets()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذا التصنيف لوجود أصول مسجلة عليه بالفعل.');
        }

        $category->delete();
        return redirect()->route('assets.categories.index')->with('success', 'تم حذف تصنيف الأصول بنجاح.');
    }
}
