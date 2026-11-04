<?php

namespace Modules\Asset\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetDepreciation;

class AssetDepreciationController extends Controller
{
    /**
     * List Asset Depreciation Entries
     */
    public function index(Request $request)
    {
        $query = AssetDepreciation::with(['asset.category', 'asset.branch', 'journalEntry', 'creator'])->latest();

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        if ($request->filled('fiscal_period')) {
            $query->where('fiscal_period', $request->fiscal_period);
        }

        if ($request->filled('is_posted')) {
            $query->where('is_posted', $request->boolean('is_posted'));
        }

        $depreciations = $query->paginate(20)->withQueryString();
        $assets = Asset::orderBy('name')->get();
        $branches = Branch::where('is_active', true)->get();

        $stats = [
            'total_runs'         => AssetDepreciation::count(),
            'total_amount'       => (float) AssetDepreciation::sum('depreciation_amount'),
            'posted_count'       => AssetDepreciation::where('is_posted', true)->count(),
            'unposted_count'     => AssetDepreciation::where('is_posted', false)->count(),
        ];

        return view('asset::depreciations.index', compact('depreciations', 'assets', 'branches', 'stats'));
    }

    /**
     * Show form to calculate and run depreciation
     */
    public function create()
    {
        $assets = Asset::where('status', 'active')
            ->where('depreciation_method', '!=', 'none')
            ->whereColumn('current_book_value', '>', 'salvage_value')
            ->with(['branch', 'category'])
            ->orderBy('name')
            ->get();

        $branches = Branch::where('is_active', true)->get();

        return view('asset::depreciations.create', compact('assets', 'branches'));
    }

    /**
     * Store single asset depreciation
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'asset_id'            => 'required|exists:assets,id',
            'depreciation_date'   => 'required|date',
            'fiscal_year'         => 'required|integer|min:2020|max:2099',
            'fiscal_period'       => 'required|string|max:20',
            'depreciation_amount' => 'required|numeric|min:0.01',
            'notes'               => 'nullable|string',
            'post_to_gl'          => 'nullable|boolean',
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);

        if ($asset->current_book_value <= $asset->salvage_value) {
            return back()->with('error', 'هذا الأصل مكتمل الإهلاك دفترياً ولا يمكن إضافة إهلاك إضافي له.');
        }

        DB::beginTransaction();
        try {
            $depAmount = min((float) $validated['depreciation_amount'], (float) $asset->current_book_value - (float) $asset->salvage_value);
            $accumAfter = (float) $asset->total_depreciated_amount + $depAmount;
            $bookValueAfter = max((float) $asset->salvage_value, (float) $asset->purchase_cost - $accumAfter);

            $depreciation = AssetDepreciation::create([
                'tenant_id'                      => $tenantId,
                'asset_id'                       => $asset->id,
                'depreciation_date'              => $validated['depreciation_date'],
                'fiscal_year'                    => $validated['fiscal_year'],
                'fiscal_period'                  => $validated['fiscal_period'],
                'depreciation_amount'            => $depAmount,
                'accumulated_depreciation_after' => $accumAfter,
                'book_value_after'               => $bookValueAfter,
                'is_posted'                      => false,
                'notes'                          => $validated['notes'] ?? null,
                'created_by'                     => Auth::id(),
            ]);

            $asset->update([
                'total_depreciated_amount' => $accumAfter,
                'current_book_value'       => $bookValueAfter,
                'status'                   => $bookValueAfter <= $asset->salvage_value ? 'fully_depreciated' : $asset->status,
            ]);

            if ($request->boolean('post_to_gl')) {
                $depreciation->postToGeneralLedger(Auth::id());
            }

            DB::commit();

            return redirect()->route('assets.depreciations.index')
                ->with('success', "تم تسجيل قيد إهلاك الأصل {$asset->name} بنجاح بمبلغ " . number_format($depAmount, 2) . " ر.س");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تسجيل الإهلاك: ' . $e->getMessage());
        }
    }

    /**
     * Run Batch Periodic Depreciation for all active assets
     */
    public function runBatch(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'fiscal_year'   => 'required|integer|min:2020|max:2099',
            'fiscal_period' => 'required|string|max:20',
            'period_type'   => 'required|in:monthly,annual',
            'branch_id'     => 'nullable|exists:branches,id',
            'post_to_gl'    => 'nullable|boolean',
        ]);

        $query = Asset::where('status', 'active')
            ->where('depreciation_method', '!=', 'none')
            ->whereColumn('current_book_value', '>', 'salvage_value');

        if (!empty($validated['branch_id'])) {
            $query->where('branch_id', $validated['branch_id']);
        }

        $assets = $query->get();

        if ($assets->isEmpty()) {
            return back()->with('error', 'لا توجد أصول نشطة قابلة للإهلاك تنطبق عليها معايير التحديد.');
        }

        $postToGl = $request->boolean('post_to_gl', false);
        $processedCount = 0;
        $totalDepreciationAmount = 0.00;

        DB::beginTransaction();
        try {
            foreach ($assets as $asset) {
                $amount = $validated['period_type'] === 'annual'
                    ? $asset->calculateAnnualDepreciation()
                    : $asset->calculateMonthlyDepreciation();

                if ($amount <= 0) {
                    continue;
                }

                $maxPossible = max(0.00, (float) $asset->current_book_value - (float) $asset->salvage_value);
                $finalAmount = min($amount, $maxPossible);

                if ($finalAmount <= 0) {
                    continue;
                }

                $accumAfter = (float) $asset->total_depreciated_amount + $finalAmount;
                $bookValueAfter = max((float) $asset->salvage_value, (float) $asset->purchase_cost - $accumAfter);

                $dep = AssetDepreciation::create([
                    'tenant_id'                      => $tenantId,
                    'asset_id'                       => $asset->id,
                    'depreciation_date'              => now()->toDateString(),
                    'fiscal_year'                    => $validated['fiscal_year'],
                    'fiscal_period'                  => $validated['fiscal_period'],
                    'depreciation_amount'            => $finalAmount,
                    'accumulated_depreciation_after' => $accumAfter,
                    'book_value_after'               => $bookValueAfter,
                    'is_posted'                      => false,
                    'notes'                          => "تشغيل إهلاك دوري مجمع ({$validated['period_type']}) للفترة {$validated['fiscal_period']}",
                    'created_by'                     => Auth::id(),
                ]);

                $asset->update([
                    'total_depreciated_amount' => $accumAfter,
                    'current_book_value'       => $bookValueAfter,
                    'status'                   => $bookValueAfter <= $asset->salvage_value ? 'fully_depreciated' : $asset->status,
                ]);

                if ($postToGl) {
                    $dep->postToGeneralLedger(Auth::id());
                }

                $processedCount++;
                $totalDepreciationAmount += $finalAmount;
            }

            DB::commit();

            return redirect()->route('assets.depreciations.index')->with(
                'success',
                "تم بنجاح تشغيل الإهلاك الدوري لعدد ({$processedCount}) أصل بإجمالي مبالغ إهلاك: " . number_format($totalDepreciationAmount, 2) . " ر.س" . ($postToGl ? " وتم ترحيل القيود لدفتر اليومية العامة آلياً." : "")
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'فشل في تشغيل الإهلاك الدوري المجمع: ' . $e->getMessage());
        }
    }

    /**
     * Show Depreciation Record
     */
    public function show(AssetDepreciation $depreciation)
    {
        $depreciation->load(['asset.category', 'asset.branch', 'journalEntry.lines.account', 'creator']);
        return view('asset::depreciations.show', compact('depreciation'));
    }

    /**
     * Post single depreciation to General Ledger
     */
    public function postToGl(AssetDepreciation $depreciation)
    {
        if ($depreciation->is_posted) {
            return back()->with('error', 'هذا الإهلاك مرحل ومؤثر في دفتر الأستاذ بالفعل.');
        }

        $entry = $depreciation->postToGeneralLedger(Auth::id());

        if (!$entry) {
            return back()->with('error', 'تعذر ترحيل القيد لعدم اكتمال ربط حسابات مصروف الإهلاك ومجمع الإهلاك في تصنيف الأصل.');
        }

        return back()->with('success', "تم ترحيل قيد الإهلاك إلى اليومية العامة بنجاح برقم: {$entry->entry_number}");
    }
}
