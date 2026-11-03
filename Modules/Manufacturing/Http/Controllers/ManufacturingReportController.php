<?php

namespace Modules\Manufacturing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\Store;
use Modules\Manufacturing\Entities\ManufacturingOrder;

class ManufacturingReportController extends Controller
{
    /**
     * Manufacturing Analytics & Comprehensive Report
     */
    public function index(Request $request)
    {
        $query = ManufacturingOrder::with(['product.unit', 'fromStore', 'toStore', 'creator', 'completedBy'])
            ->where('status', 'completed')
            ->latest('actual_completion_date');

        if ($request->filled('from_date')) {
            $query->whereDate('actual_completion_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('actual_completion_date', '<=', $request->to_date);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('store_id')) {
            $query->where('to_store_id', $request->store_id);
        }

        $completedOrders = $query->paginate(20)->withQueryString();

        // Financial & Production Aggregate Stats
        $stats = [
            'operations_count' => (int) $query->count(),
            'total_produced'   => (float) $query->sum('produced_quantity'),
            'total_rejected'   => (float) $query->sum('rejected_quantity'),
            'total_cost'       => (float) $query->sum('actual_total_cost'),
            'material_cost'    => (float) $query->sum('actual_material_cost'),
            'labor_cost'       => (float) $query->sum('actual_labor_cost'),
            'overhead_cost'    => (float) $query->sum('actual_overhead_cost'),
            'avg_unit_cost'    => $query->count() > 0 ? (float) $query->avg('unit_cost') : 0.00,
        ];

        // Top produced products summary
        $topProducts = ManufacturingOrder::where('status', 'completed')
            ->select('product_id', DB::raw('SUM(produced_quantity) as total_qty'), DB::raw('SUM(actual_total_cost) as total_spent'), DB::raw('COUNT(*) as runs_count'))
            ->groupBy('product_id')
            ->with('product.unit')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $products = Item::where('is_service', false)->orderBy('name')->get();
        $stores = Store::where('is_active', true)->get();

        return view('manufacturing::reports.index', compact('completedOrders', 'stats', 'topProducts', 'products', 'stores'));
    }
}
