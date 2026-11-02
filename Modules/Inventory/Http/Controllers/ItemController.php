<?php

namespace Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\Brand;
use Modules\Inventory\Entities\Category;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\ItemUnit;
use Modules\Inventory\Entities\Store;
use Modules\Inventory\Entities\Unit;
use Modules\Setting\Entities\Tax;

class ItemController extends Controller
{
    /**
     * Items Catalog Listing
     */
    public function index(Request $request)
    {
        $query = Item::with(['category', 'brand', 'unit', 'tax', 'stocks.store']);

        // Search by name, barcode, sku
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $items = $query->latest()->paginate(15)->withQueryString();

        // Catalog statistics
        $stats = [
            'total_items' => Item::count(),
            'low_stock'   => Item::where('is_service', false)->whereHas('stocks', function($q) {
                $q->havingRaw('SUM(quantity) <= items.min_quantity_alert AND SUM(quantity) > 0');
            })->count(),
            'out_of_stock' => Item::where('is_service', false)->whereDoesntHave('stocks', function($q) {
                $q->where('quantity', '>', 0);
            })->count(),
            'total_valuation' => ItemStock::sum(DB::raw('quantity * avg_cost')),
        ];

        $categories = Category::all();
        $stores = Store::where('is_active', true)->get();

        return view('inventory::items.index', compact('items', 'categories', 'stores', 'stats'));
    }

    /**
     * Show create item form
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();
        $taxes = Tax::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();

        return view('inventory::items.create', compact('categories', 'brands', 'units', 'taxes', 'stores'));
    }

    /**
     * Store newly created item
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:200',
            'sku'                  => 'nullable|string|max:100',
            'barcode'              => 'nullable|string|max:100',
            'category_id'          => 'nullable|exists:categories,id',
            'brand_id'             => 'nullable|exists:brands,id',
            'unit_id'              => 'nullable|exists:units,id',
            'tax_id'               => 'nullable|exists:taxes,id',
            'buy_price'            => 'required|numeric|min:0',
            'sell_price'           => 'required|numeric|min:0',
            'wholesale_price'      => 'nullable|numeric|min:0',
            'half_wholesale_price' => 'nullable|numeric|min:0',
            'minimum_sell_price'   => 'nullable|numeric|min:0',
            'min_quantity_alert'   => 'nullable|numeric|min:0',
            'is_service'           => 'nullable|boolean',
            'has_expiration'       => 'nullable|boolean',
            'expire_date'          => 'nullable|date',
            'description'          => 'nullable|string',
            // Initial stock
            'initial_store_id'     => 'nullable|exists:stores,id',
            'initial_quantity'     => 'nullable|numeric|min:0',
        ]);

        // Auto-generate barcode if omitted
        $barcode = $validated['barcode'] ?: (string) rand(100000000000, 999999999999);
        $sku = $validated['sku'] ?: 'SKU-' . strtoupper(substr(uniqid(), -6));

        DB::beginTransaction();
        try {
            $item = Item::create([
                'name'                 => $validated['name'],
                'sku'                  => $sku,
                'barcode'              => $barcode,
                'category_id'          => $validated['category_id'] ?? null,
                'brand_id'             => $validated['brand_id'] ?? null,
                'unit_id'              => $validated['unit_id'] ?? null,
                'tax_id'               => $validated['tax_id'] ?? null,
                'buy_price'            => $validated['buy_price'],
                'avg_cost'             => $validated['buy_price'],
                'sell_price'           => $validated['sell_price'],
                'wholesale_price'      => $validated['wholesale_price'] ?? $validated['sell_price'],
                'half_wholesale_price' => $validated['half_wholesale_price'] ?? $validated['sell_price'],
                'minimum_sell_price'   => $validated['minimum_sell_price'] ?? $validated['buy_price'],
                'min_quantity_alert'   => $validated['min_quantity_alert'] ?? 5.00,
                'is_service'           => $request->boolean('is_service'),
                'has_expiration'       => $request->boolean('has_expiration'),
                'expire_date'          => $validated['expire_date'] ?? null,
                'description'          => $validated['description'] ?? null,
                'is_active'            => true,
            ]);

            // If initial stock was provided
            if (!$item->is_service && !empty($validated['initial_store_id']) && !empty($validated['initial_quantity'])) {
                ItemStock::create([
                    'tenant_id' => $item->tenant_id,
                    'item_id'   => $item->id,
                    'store_id'  => $validated['initial_store_id'],
                    'quantity'  => $validated['initial_quantity'],
                    'avg_cost'  => $item->buy_price,
                ]);
            }

            DB::commit();
            return redirect()->route('inventory.items.index')->with('success', 'تمت إضافة الصنف بنجاح!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ الصنف: ' . $e->getMessage());
        }
    }

    /**
     * Edit item form
     */
    public function edit(Item $item)
    {
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();
        $taxes = Tax::where('is_active', true)->get();
        $stores = Store::where('is_active', true)->get();

        return view('inventory::items.edit', compact('item', 'categories', 'brands', 'units', 'taxes', 'stores'));
    }

    /**
     * Update item
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:200',
            'sku'                  => 'nullable|string|max:100',
            'barcode'              => 'required|string|max:100',
            'category_id'          => 'nullable|exists:categories,id',
            'brand_id'             => 'nullable|exists:brands,id',
            'unit_id'              => 'nullable|exists:units,id',
            'tax_id'               => 'nullable|exists:taxes,id',
            'buy_price'            => 'required|numeric|min:0',
            'sell_price'           => 'required|numeric|min:0',
            'wholesale_price'      => 'nullable|numeric|min:0',
            'half_wholesale_price' => 'nullable|numeric|min:0',
            'minimum_sell_price'   => 'nullable|numeric|min:0',
            'min_quantity_alert'   => 'nullable|numeric|min:0',
            'is_service'           => 'nullable|boolean',
            'has_expiration'       => 'nullable|boolean',
            'expire_date'          => 'nullable|date',
            'description'          => 'nullable|string',
            'is_active'            => 'nullable|boolean',
        ]);

        $item->update([
            'name'                 => $validated['name'],
            'sku'                  => $validated['sku'] ?? $item->sku,
            'barcode'              => $validated['barcode'],
            'category_id'          => $validated['category_id'] ?? null,
            'brand_id'             => $validated['brand_id'] ?? null,
            'unit_id'              => $validated['unit_id'] ?? null,
            'tax_id'               => $validated['tax_id'] ?? null,
            'buy_price'            => $validated['buy_price'],
            'sell_price'           => $validated['sell_price'],
            'wholesale_price'      => $validated['wholesale_price'] ?? $validated['sell_price'],
            'half_wholesale_price' => $validated['half_wholesale_price'] ?? $validated['sell_price'],
            'minimum_sell_price'   => $validated['minimum_sell_price'] ?? $validated['buy_price'],
            'min_quantity_alert'   => $validated['min_quantity_alert'] ?? 5.00,
            'is_service'           => $request->boolean('is_service'),
            'has_expiration'       => $request->boolean('has_expiration'),
            'expire_date'          => $validated['expire_date'] ?? null,
            'description'          => $validated['description'] ?? null,
            'is_active'            => $request->boolean('is_active', true),
        ]);

        return redirect()->route('inventory.items.index')->with('success', 'تم تعديل بيانات الصنف بنجاح!');
    }

    /**
     * Delete item
     */
    public function destroy(Item $item)
    {
        $item->stocks()->delete();
        $item->itemUnits()->delete();
        $item->delete();

        return back()->with('success', 'تم حذف الصنف وسجلاته بنجاح.');
    }

    /**
     * Print item barcode sticker
     */
    public function barcode(Item $item)
    {
        return view('inventory::items.barcode', compact('item'));
    }
}

