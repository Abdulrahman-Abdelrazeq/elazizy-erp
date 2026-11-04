<?php

namespace Modules\Project\Entities;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Accounting\Entities\CostCenter;
use Modules\CRM\Entities\Client;
use Modules\HR\Entities\Employee;

class Project extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'client_id',
        'cost_center_id',
        'project_manager_id',
        'code',
        'name',
        'contract_number',
        'contract_date',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'contract_value',
        'revised_value',
        'advance_payment_percentage',
        'advance_payment_amount',
        'retention_percentage',
        'retention_amount',
        'completion_percentage',
        'status',
        'location',
        'description',
        'created_by',
    ];

    protected $casts = [
        'contract_date'              => 'date',
        'start_date'                 => 'date',
        'expected_end_date'          => 'date',
        'actual_end_date'            => 'date',
        'contract_value'             => 'float',
        'revised_value'              => 'float',
        'advance_payment_percentage' => 'float',
        'advance_payment_amount'     => 'float',
        'retention_percentage'       => 'float',
        'retention_amount'           => 'float',
        'completion_percentage'      => 'float',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function projectManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'project_manager_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class, 'project_id');
    }

    public function boqItems(): HasMany
    {
        return $this->hasMany(ProjectBoqItem::class, 'project_id');
    }

    public function changeOrders(): HasMany
    {
        return $this->hasMany(ProjectChangeOrder::class, 'project_id');
    }

    public function extracts(): HasMany
    {
        return $this->hasMany(ProjectExtract::class, 'project_id');
    }

    /**
     * Recalculate revised value and overall progress
     */
    public function recalculateFinancials(): void
    {
        $approvedCoSum = (float) $this->changeOrders()->where('status', 'approved')->sum('amount_change');
        $revised = (float) $this->contract_value + $approvedCoSum;

        // Advance & retention amounts
        $advanceAmt = $revised * ((float) $this->advance_payment_percentage / 100);
        $retentionAmt = $revised * ((float) $this->retention_percentage / 100);

        // Overall progress percentage based on BOQ or Milestones
        $totalBoqVal = (float) $this->boqItems()->sum('total_price');
        $executedBoqVal = (float) $this->boqItems()->selectRaw('SUM(executed_quantity * unit_price) as exec_val')->value('exec_val');

        $completion = 0.00;
        if ($totalBoqVal > 0) {
            $completion = min(100.00, round(($executedBoqVal / $totalBoqVal) * 100, 2));
        } else {
            // Milestone-based fallback
            $milestones = $this->milestones;
            if ($milestones->isNotEmpty()) {
                $weightedSum = 0;
                $totalWeight = 0;
                foreach ($milestones as $m) {
                    $weight = (float) $m->weight_percentage > 0 ? (float) $m->weight_percentage : (100 / $milestones->count());
                    $weightedSum += ((float) $m->completion_percentage * $weight);
                    $totalWeight += $weight;
                }
                $completion = $totalWeight > 0 ? min(100.00, round($weightedSum / $totalWeight, 2)) : 0.00;
            }
        }

        $this->update([
            'revised_value'          => $revised,
            'advance_payment_amount' => $advanceAmt,
            'retention_amount'       => $retentionAmt,
            'completion_percentage'  => $completion,
        ]);
    }

    public function getTotalBilledAmountAttribute(): float
    {
        return (float) $this->extracts()->whereIn('status', ['approved', 'paid'])->sum('net_amount');
    }

    public function getTotalPaidAmountAttribute(): float
    {
        return (float) $this->extracts()->sum('paid_amount');
    }

    public function getRemainingDueAttribute(): float
    {
        return max(0.00, $this->total_billed_amount - $this->total_paid_amount);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planning'          => 'تخطيط وتجهيز (Planning)',
            'in_progress'       => 'قيد التنفيذ (In-Progress)',
            'suspended'         => 'معلق وموقف مؤقتاً (Suspended)',
            'delivered_primary' => 'تسليم ابتدائي (Primary Delivery)',
            'completed'         => 'منتهي ومسلّم نهائياً (Completed)',
            'cancelled'         => 'ملغي (Cancelled)',
            default             => 'قيد التنفيذ',
        };
    }

    /**
     * Generate unique sequential code: PRJ-YYYY-XXXX
     */
    public static function generateCode(?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = currentTenant() ? currentTenant()->id : (Tenant::first()?->id ?? 1);
        }

        $year = date('Y');
        $prefix = "PRJ-{$year}-";

        $last = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('code', 'like', "{$prefix}%")
            ->latest('id')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->code);
            $seq = intval(end($parts)) + 1;
        }

        $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('code', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
