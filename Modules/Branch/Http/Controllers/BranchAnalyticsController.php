<?php

namespace Modules\Branch\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Branch\Entities\Branch;
use Modules\Branch\Entities\BranchTransfer;

class BranchAnalyticsController extends Controller
{
    /**
     * Comparative Performance & Analytics across all Branches
     */
    public function index(Request $request)
    {
        $branches = Branch::with(['stores', 'treasuries', 'employees', 'assignedUsers'])->get();

        $branchStats = [];
        $consolidated = [
            'total_branches'       => $branches->count(),
            'total_stores'         => 0,
            'total_inventory_val'  => 0.00,
            'total_sales_month'    => 0.00,
            'total_treasury_bal'   => 0.00,
            'total_employees'      => 0,
            'in_transit_transfers' => BranchTransfer::where('status', 'dispatched')->count(),
            'in_transit_value'     => (float) BranchTransfer::where('status', 'dispatched')->sum('total_cost'),
        ];

        foreach ($branches as $b) {
            $invVal = $b->getInventoryValue();
            $sales = $b->getMonthlySales();
            $treasuryBal = (float) $b->treasuries()->sum('current_balance');
            $empCount = $b->employees()->count();
            $storeCount = $b->stores()->count();

            $branchStats[] = [
                'branch'          => $b,
                'inventory_value' => $invVal,
                'monthly_sales'   => $sales,
                'treasury_bal'    => $treasuryBal,
                'employees_count' => $empCount,
                'stores_count'    => $storeCount,
            ];

            $consolidated['total_stores'] += $storeCount;
            $consolidated['total_inventory_val'] += $invVal;
            $consolidated['total_sales_month'] += $sales;
            $consolidated['total_treasury_bal'] += $treasuryBal;
            $consolidated['total_employees'] += $empCount;
        }

        return view('branch::analytics.index', compact('branchStats', 'consolidated'));
    }
}
