<?php

namespace Modules\Treasury\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Treasury\Entities\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    /**
     * Categories Listing (Expenses & Revenues)
     */
    public function index(Request $request)
    {
        $query = ExpenseCategory::with('parent')->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%");
        }

        $categories = $query->paginate(15)->withQueryString();
        $parents = ExpenseCategory::whereNull('parent_id')->where('is_active', true)->get();

        $stats = [
            'total_expenses' => ExpenseCategory::where('type', 'expense')->count(),
            'total_revenues' => ExpenseCategory::where('type', 'revenue')->count(),
        ];

        return view('treasury::categories.index', compact('categories', 'parents', 'stats'));
    }

    /**
     * Store Category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'code'        => 'nullable|string|max:50',
            'type'        => 'required|in:expense,revenue',
            'parent_id'   => 'nullable|exists:expense_categories,id',
            'description' => 'nullable|string',
        ]);

        ExpenseCategory::create([
            'name'        => $validated['name'],
            'code'        => $validated['code'] ?? null,
            'type'        => $validated['type'],
            'parent_id'   => $validated['parent_id'] ?? null,
            'is_active'   => true,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'تمت إضافة بند المصروفات / الإيرادات بنجاح!');
    }

    /**
     * Update Category
     */
    public function update(Request $request, ExpenseCategory $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'code'        => 'nullable|string|max:50',
            'type'        => 'required|in:expense,revenue',
            'parent_id'   => 'nullable|exists:expense_categories,id',
            'is_active'   => 'nullable|boolean',
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name'        => $validated['name'],
            'code'        => $validated['code'] ?? null,
            'type'        => $validated['type'],
            'parent_id'   => $validated['parent_id'] ?? null,
            'is_active'   => $request->boolean('is_active', true),
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'تم تحديث بيانات البند بنجاح!');
    }

    /**
     * Delete Category
     */
    public function destroy(ExpenseCategory $category)
    {
        if ($category->vouchers()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذا البند لوجود سندات مالية مسجلة عليه!');
        }

        $category->delete();
        return back()->with('success', 'تم حذف البند بنجاح.');
    }
}

