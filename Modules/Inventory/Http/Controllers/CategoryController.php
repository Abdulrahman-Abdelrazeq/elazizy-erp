<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Inventory\Entities\Brand;
use Modules\Inventory\Entities\Category;

class CategoryController extends Controller
{
    /**
     * List categories and brands
     */
    public function index()
    {
        $categories = Category::withCount('items')->with('parent')->latest()->get();
        $brands = Brand::withCount('items')->latest()->get();

        return view('inventory::categories.index', compact('categories', 'brands'));
    }

    /**
     * Store new category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create([
            'name'      => $validated['name'],
            'code'      => $validated['code'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'تمت إضافة القسم بنجاح!');
    }

    /**
     * Delete category
     */
    public function destroy(Category $category)
    {
        if ($category->items()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذا القسم لأنه يحتوي على منتجات وأصناف!');
        }

        $category->delete();
        return back()->with('success', 'تم حذف القسم بنجاح.');
    }

    /**
     * Store new brand
     */
    public function storeBrand(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        Brand::create([
            'name'      => $validated['name'],
            'is_active' => true,
        ]);

        return back()->with('success', 'تمت إضافة الماركة التجارية بنجاح!');
    }

    /**
     * Delete brand
     */
    public function destroyBrand(Brand $brand)
    {
        if ($brand->items()->exists()) {
            return back()->with('error', 'لا يمكن حذف هذه الماركة لأنها مرتبطة بأصناف مسجلة!');
        }

        $brand->delete();
        return back()->with('success', 'تم حذف الماركة بنجاح.');
    }
}

