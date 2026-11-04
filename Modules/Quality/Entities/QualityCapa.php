<?php

namespace Modules\Quality\Entities;

use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Entities\Employee;

class QualityCapa extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'quality_capas';

    protected $fillable = [
        'tenant_id',
        'capa_number',
        'ncr_id',
        'type',
        'title',
        'action_plan',
        'assigned_to',
        'due_date',
        'completion_date',
        'effectiveness_criteria',
        'effectiveness_status',
        'status',
        'created_by',
    ];

    protected $casts = [
        'due_date'        => 'date',
        'completion_date' => 'date',
    ];

    public function ncr(): BelongsTo
    {
        return $this->belongsTo(QualityNcr::class, 'ncr_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'corrective' => 'إجراء تصحيحي (Corrective Action)',
            'preventive' => 'إجراء وقائي (Preventive Action)',
            default      => 'إجراء جودة',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'         => 'قيد التكليف (Pending)',
            'in_progress'     => 'جاري التنفيذ (In Progress)',
            'completed'       => 'تم التنفيذ (Completed)',
            'verified_closed' => 'محقق ومغلق رسمياً (Verified & Closed)',
            default           => 'قيد التنفيذ',
        };
    }

    public function getEffectivenessLabelAttribute(): string
    {
        return match ($this->effectiveness_status) {
            'effective'          => 'إجراء فعال ومثبت (Effective)',
            'ineffective'        => 'غير فعال ويحتاج مراجعة (Ineffective)',
            'pending_evaluation' => 'قيد تقييم الفاعلية (Pending Evaluation)',
            default              => 'قيد المراجعة',
        };
    }

    /**
     * Generate sequential number: CAPA-YYYY-XXXX
     */
    public static function generateNumber(?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = currentTenant() ? currentTenant()->id : (Tenant::first()?->id ?? 1);
        }

        $year = date('Y');
        $prefix = "CAPA-{$year}-";

        $last = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('capa_number', 'like', "{$prefix}%")
            ->latest('id')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->capa_number);
            $seq = intval(end($parts)) + 1;
        }

        $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('capa_number', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
