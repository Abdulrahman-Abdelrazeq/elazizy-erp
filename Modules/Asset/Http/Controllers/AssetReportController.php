<?php

namespace Modules\Asset\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetCategory;
use Modules\Asset\Entities\AssetDepreciation;

class AssetReportController extends Controller
{
    /**
     * Asset Portfolio, Depreciation & Net Book Value Reports
     */
    public function index(Request $request)
    {
        $query = Asset::with(['branch', 'category', 'custodian', 'location'])->latest();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->get();
        $branches = Branch::where('is_active', true)->get();
        $categories = AssetCategory::where('is_active', true)->get();

        $currentYear = date('Y');
        $ytdDepreciation = (float) AssetDepreciation::where('fiscal_year', $currentYear)->sum('depreciation_amount');

        $consolidated = [
            'total_assets_count' => $assets->count(),
            'total_gross_cost'   => (float) $assets->sum('purchase_cost'),
            'total_salvage'      => (float) $assets->sum('salvage_value'),
            'total_accum_dep'    => (float) $assets->sum('total_depreciated_amount'),
            'total_net_book_val' => (float) $assets->sum('current_book_value'),
            'ytd_depreciation'   => $ytdDepreciation,
        ];

        // Category breakdown
        $categoryBreakdown = [];
        foreach ($categories as $cat) {
            $catAssets = $assets->where('category_id', $cat->id);
            if ($catAssets->isNotEmpty()) {
                $categoryBreakdown[] = [
                    'category'   => $cat,
                    'count'      => $catAssets->count(),
                    'gross_cost' => (float) $catAssets->sum('purchase_cost'),
                    'accum_dep'  => (float) $catAssets->sum('total_depreciated_amount'),
                    'book_value' => (float) $catAssets->sum('current_book_value'),
                ];
            }
        }

        // Branch breakdown
        $branchBreakdown = [];
        foreach ($branches as $br) {
            $brAssets = $assets->where('branch_id', $br->id);
            if ($brAssets->isNotEmpty()) {
                $branchBreakdown[] = [
                    'branch'     => $br,
                    'count'      => $brAssets->count(),
                    'gross_cost' => (float) $brAssets->sum('purchase_cost'),
                    'accum_dep'  => (float) $brAssets->sum('total_depreciated_amount'),
                    'book_value' => (float) $brAssets->sum('current_book_value'),
                ];
            }
        }

        return view('asset::reports.index', compact(
            'assets', 'branches', 'categories', 'consolidated',
            'categoryBreakdown', 'branchBreakdown'
        ));
    }

    /**
     * Printable Comprehensive Asset Register
     */
    public function print(Request $request)
    {
        $query = Asset::with(['branch', 'category', 'custodian', 'location'])->latest();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->get();

        $consolidated = [
            'total_assets_count' => $assets->count(),
            'total_gross_cost'   => (float) $assets->sum('purchase_cost'),
            'total_accum_dep'    => (float) $assets->sum('total_depreciated_amount'),
            'total_net_book_val' => (float) $assets->sum('current_book_value'),
        ];

        return view('asset::reports.print', compact('assets', 'consolidated'));
    }
}
