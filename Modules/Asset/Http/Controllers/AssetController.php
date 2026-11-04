<?php

namespace Modules\Asset\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Accounting\Entities\CostCenter;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetCategory;
use Modules\CRM\Entities\Client;
use Modules\HR\Entities\Employee;
use Modules\Setting\Entities\Location;

class AssetController extends Controller
{
    /**
     * List Assets Registry
     */
    public function index(Request $request)
    {
        $query = Asset::with(['branch', 'category', 'location', 'custodian'])->latest();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('asset_number', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhere('barcode', 'like', "%{$s}%");
            });
        }

        $assets = $query->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();
        $categories = AssetCategory::where('is_active', true)->get();

        $stats = [
            'total_count'        => Asset::count(),
            'active_count'       => Asset::where('status', 'active')->count(),
            'total_purchase_cost'=> (float) Asset::sum('purchase_cost'),
            'total_depreciated'  => (float) Asset::sum('total_depreciated_amount'),
            'total_book_value'   => (float) Asset::sum('current_book_value'),
        ];

        return view('asset::assets.index', compact('assets', 'branches', 'categories', 'stats'));
    }

    /**
     * Show form to create new Asset
     */
    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $categories = AssetCategory::where('is_active', true)->get();
        $locations = Location::all();
        $costCenters = CostCenter::all();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $suppliers = Client::whereIn('type', ['supplier', 'both'])->orderBy('name')->get();

        $suggestedNumber = Asset::generateNumber();

        return view('asset::assets.create', compact(
            'branches', 'categories', 'locations', 'costCenters',
            'employees', 'suppliers', 'suggestedNumber'
        ));
    }

    /**
     * Store new Asset
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'branch_id'             => 'required|exists:branches,id',
            'category_id'           => 'required|exists:asset_categories,id',
            'location_id'           => 'nullable|exists:locations,id',
            'cost_center_id'        => 'nullable|exists:cost_centers,id',
            'custodian_employee_id' => 'nullable|exists:hr_employees,id',
            'supplier_id'           => 'nullable|exists:clients,id',
            'asset_number'          => 'nullable|string|max:50',
            'name'                  => 'required|string|max:190',
            'barcode'               => 'nullable|string|max:100',
            'serial_number'         => 'nullable|string|max:100',
            'model_number'          => 'nullable|string|max:100',
            'purchase_date'         => 'required|date',
            'in_service_date'       => 'required|date',
            'purchase_cost'         => 'required|numeric|min:0.01',
            'salvage_value'         => 'nullable|numeric|min:0',
            'depreciation_method'   => 'required|in:straight_line,declining_balance,none',
            'depreciation_rate'     => 'nullable|numeric|min:0|max:100',
            'useful_life_years'     => 'nullable|numeric|min:0.1|max:100',
            'warranty_expiry'       => 'nullable|date',
            'notes'                 => 'nullable|string',
        ]);

        $purchaseCost = (float) $validated['purchase_cost'];
        $salvageValue = (float) ($validated['salvage_value'] ?? 0.00);
        $depreciableCost = max(0.00, $purchaseCost - $salvageValue);

        $category = AssetCategory::find($validated['category_id']);
        $depMethod = $validated['depreciation_method'] ?? ($category?->depreciation_method ?? 'straight_line');
        $depRate = (float) ($validated['depreciation_rate'] ?? ($category?->depreciation_rate ?? 20.00));
        $usefulLife = (float) ($validated['useful_life_years'] ?? ($category?->useful_life_years ?? 5.00));

        $assetNumber = !empty($validated['asset_number'])
            ? $validated['asset_number']
            : Asset::generateNumber($tenantId);

        $asset = Asset::create([
            'tenant_id'                => $tenantId,
            'branch_id'                => $validated['branch_id'],
            'category_id'              => $validated['category_id'],
            'location_id'              => $validated['location_id'] ?? null,
            'cost_center_id'           => $validated['cost_center_id'] ?? null,
            'custodian_employee_id'    => $validated['custodian_employee_id'] ?? null,
            'supplier_id'              => $validated['supplier_id'] ?? null,
            'asset_number'             => $assetNumber,
            'name'                     => $validated['name'],
            'barcode'                  => $validated['barcode'] ?: $assetNumber,
            'serial_number'            => $validated['serial_number'] ?? null,
            'model_number'             => $validated['model_number'] ?? null,
            'purchase_date'            => $validated['purchase_date'],
            'in_service_date'          => $validated['in_service_date'],
            'purchase_cost'            => $purchaseCost,
            'salvage_value'            => $salvageValue,
            'depreciable_cost'         => $depreciableCost,
            'depreciation_method'      => $depMethod,
            'depreciation_rate'        => $depRate,
            'useful_life_years'        => $usefulLife,
            'total_depreciated_amount' => 0.00,
            'current_book_value'       => $purchaseCost,
            'status'                   => 'active',
            'warranty_expiry'          => $validated['warranty_expiry'] ?? null,
            'notes'                    => $validated['notes'] ?? null,
            'created_by'               => Auth::id(),
        ]);

        return redirect()->route('assets.assets.show', $asset->id)
            ->with('success', 'تم تسجيل الأصل الثابت بنجاح برقم: ' . $asset->asset_number);
    }

    /**
     * Show 360-degree Asset Profile
     */
    public function show(Asset $asset)
    {
        $asset->load([
            'branch', 'category.assetAccount', 'category.depreciationAccount', 'category.depreciationExpenseAccount',
            'location', 'costCenter', 'custodian', 'supplier', 'creator',
            'depreciations.journalEntry', 'disposal'
        ]);

        $annualDepreciation = $asset->calculateAnnualDepreciation();
        $monthlyDepreciation = $asset->calculateMonthlyDepreciation();

        return view('asset::assets.show', compact('asset', 'annualDepreciation', 'monthlyDepreciation'));
    }

    /**
     * Show form to edit Asset
     */
    public function edit(Asset $asset)
    {
        $branches = Branch::where('is_active', true)->get();
        $categories = AssetCategory::where('is_active', true)->get();
        $locations = Location::all();
        $costCenters = CostCenter::all();
        $employees = Employee::where('status', 'active')->orderBy('name')->get();
        $suppliers = Client::whereIn('type', ['supplier', 'both'])->orderBy('name')->get();

        return view('asset::assets.edit', compact(
            'asset', 'branches', 'categories', 'locations', 'costCenters',
            'employees', 'suppliers'
        ));
    }

    /**
     * Update Asset
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'branch_id'             => 'required|exists:branches,id',
            'category_id'           => 'required|exists:asset_categories,id',
            'location_id'           => 'nullable|exists:locations,id',
            'cost_center_id'        => 'nullable|exists:cost_centers,id',
            'custodian_employee_id' => 'nullable|exists:hr_employees,id',
            'supplier_id'           => 'nullable|exists:clients,id',
            'name'                  => 'required|string|max:190',
            'barcode'               => 'nullable|string|max:100',
            'serial_number'         => 'nullable|string|max:100',
            'model_number'          => 'nullable|string|max:100',
            'purchase_date'         => 'required|date',
            'in_service_date'       => 'required|date',
            'purchase_cost'         => 'required|numeric|min:0.01',
            'salvage_value'         => 'nullable|numeric|min:0',
            'depreciation_method'   => 'required|in:straight_line,declining_balance,none',
            'depreciation_rate'     => 'nullable|numeric|min:0|max:100',
            'useful_life_years'     => 'nullable|numeric|min:0.1|max:100',
            'status'                => 'required|in:active,in_maintenance,fully_depreciated,disposed,sold',
            'warranty_expiry'       => 'nullable|date',
            'notes'                 => 'nullable|string',
        ]);

        $purchaseCost = (float) $validated['purchase_cost'];
        $salvageValue = (float) ($validated['salvage_value'] ?? 0.00);
        $depreciableCost = max(0.00, $purchaseCost - $salvageValue);

        $validated['purchase_cost'] = $purchaseCost;
        $validated['salvage_value'] = $salvageValue;
        $validated['depreciable_cost'] = $depreciableCost;

        $asset->update($validated);
        $asset->recalculateBookValue();

        return redirect()->route('assets.assets.show', $asset->id)
            ->with('success', 'تم تحديث بيانات الأصل الثابت بنجاح.');
    }

    /**
     * Delete Asset
     */
    public function destroy(Asset $asset)
    {
        if ($asset->depreciations()->exists() || $asset->disposal) {
            return back()->with('error', 'لا يمكن حذف أصل ثابت مسجل عليه قيود إهلاك أو مستبعد محاسبياً.');
        }

        $asset->delete();
        return redirect()->route('assets.assets.index')->with('success', 'تم حذف بطاقة الأصل بنجاح.');
    }

    /**
     * Print Barcode Tag / Sticker
     */
    public function printBarcode(Asset $asset)
    {
        $asset->load(['branch', 'category', 'location', 'custodian']);
        return view('asset::assets.print_barcode', compact('asset'));
    }
}
