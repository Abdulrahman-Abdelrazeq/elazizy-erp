<?php

namespace Modules\Project\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Project\Entities\Project;
use Modules\Project\Entities\ProjectChangeOrder;
use Modules\Project\Entities\ProjectExtract;

class ProjectAnalyticsController extends Controller
{
    /**
     * Projects Executive Analytics & Performance Dashboard
     */
    public function index(Request $request)
    {
        $projects = Project::with(['client', 'branch', 'projectManager'])
            ->withCount(['milestones', 'boqItems', 'extracts'])
            ->latest()
            ->get();

        $totalOriginalContract = (float) $projects->sum('contract_value');
        $totalApprovedCO = (float) ProjectChangeOrder::where('status', 'approved')->sum('amount_change');
        $totalRevisedContract = (float) $projects->sum('revised_value');
        $totalBilled = (float) ProjectExtract::whereIn('status', ['approved', 'paid'])->sum('net_amount');
        $totalPaid = (float) ProjectExtract::sum('paid_amount');
        $totalRetentionDeducted = (float) ProjectExtract::whereIn('status', ['approved', 'paid'])->sum('retention_deduction');
        $totalAdvanceDeducted = (float) ProjectExtract::whereIn('status', ['approved', 'paid'])->sum('advance_payment_deduction');
        $outstandingCollection = max(0.00, $totalBilled - $totalPaid);

        $statusCounts = [
            'planning'          => $projects->where('status', 'planning')->count(),
            'in_progress'       => $projects->where('status', 'in_progress')->count(),
            'delivered_primary' => $projects->where('status', 'delivered_primary')->count(),
            'completed'         => $projects->where('status', 'completed')->count(),
            'suspended'         => $projects->where('status', 'suspended')->count(),
        ];

        $avgCompletion = $projects->count() > 0 ? round($projects->avg('completion_percentage'), 1) : 0.0;

        return view('project::analytics.index', compact(
            'projects',
            'totalOriginalContract',
            'totalApprovedCO',
            'totalRevisedContract',
            'totalBilled',
            'totalPaid',
            'totalRetentionDeducted',
            'totalAdvanceDeducted',
            'outstandingCollection',
            'statusCounts',
            'avgCompletion'
        ));
    }
}
