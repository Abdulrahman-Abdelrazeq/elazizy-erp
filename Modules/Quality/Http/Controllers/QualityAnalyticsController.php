<?php

namespace Modules\Quality\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CRM\Entities\Client;
use Modules\Quality\Entities\QualityCapa;
use Modules\Quality\Entities\QualityInspection;
use Modules\Quality\Entities\QualityNcr;

class QualityAnalyticsController extends Controller
{
    /**
     * Quality Executive Dashboard & COPQ Analytics
     */
    public function index(Request $request)
    {
        $totalInspections = QualityInspection::count();
        $passedCount = QualityInspection::where('status', 'passed')->count();
        $rejectedCount = QualityInspection::where('status', 'rejected')->count();
        $conditionalCount = QualityInspection::where('status', 'conditionally_passed')->count();
        $pendingCount = QualityInspection::where('status', 'pending')->count();

        // First Pass Yield (FPY) %
        $completedInspections = $passedCount + $rejectedCount + $conditionalCount;
        $firstPassYield = $completedInspections > 0
            ? round(($passedCount / $completedInspections) * 100, 1)
            : 100.0;

        // Cost of Poor Quality (COPQ)
        $totalCopq = (float) QualityNcr::sum('estimated_loss_amount');

        // Root Cause Distribution (Pareto)
        $rootCauses = [
            'raw_material' => QualityNcr::where('root_cause_category', 'raw_material')->count(),
            'machinery'    => QualityNcr::where('root_cause_category', 'machinery')->count(),
            'workmanship'  => QualityNcr::where('root_cause_category', 'workmanship')->count(),
            'environment'  => QualityNcr::where('root_cause_category', 'environment')->count(),
            'design'       => QualityNcr::where('root_cause_category', 'design')->count(),
            'other'        => QualityNcr::where('root_cause_category', 'other')->count(),
        ];

        // Severity Distribution
        $severities = [
            'critical' => QualityNcr::where('severity', 'critical')->count(),
            'major'    => QualityNcr::where('severity', 'major')->count(),
            'minor'    => QualityNcr::where('severity', 'minor')->count(),
        ];

        // Supplier QC Scorecard
        $suppliers = QualityInspection::whereNotNull('supplier_id')
            ->with('supplier')
            ->selectRaw('supplier_id, count(*) as total_inspections, sum(case when status = "passed" then 1 else 0 end) as passed_inspections, sum(case when status = "rejected" then 1 else 0 end) as rejected_inspections')
            ->groupBy('supplier_id')
            ->get()
            ->map(function ($s) {
                $passRate = $s->total_inspections > 0
                    ? round(($s->passed_inspections / $s->total_inspections) * 100, 1)
                    : 0;
                $s->pass_rate = $passRate;
                return $s;
            })
            ->sortByDesc('total_inspections');

        // CAPA Summary
        $totalCapas = QualityCapa::count();
        $verifiedCapas = QualityCapa::where('status', 'verified_closed')->count();
        $effectiveCapas = QualityCapa::where('effectiveness_status', 'effective')->count();
        $capaEffectivenessRate = $totalCapas > 0 ? round(($effectiveCapas / $totalCapas) * 100, 1) : 0;

        return view('quality::analytics.index', compact(
            'totalInspections',
            'passedCount',
            'rejectedCount',
            'conditionalCount',
            'pendingCount',
            'firstPassYield',
            'totalCopq',
            'rootCauses',
            'severities',
            'suppliers',
            'totalCapas',
            'verifiedCapas',
            'effectiveCapas',
            'capaEffectivenessRate'
        ));
    }
}
