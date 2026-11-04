<?php

namespace Modules\Quality\Entities;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CRM\Entities\Client;
use Modules\HR\Entities\Employee;
use Modules\Inventory\Entities\Item;
use Modules\Manufacturing\Entities\ManufacturingOrder;
use Modules\Project\Entities\Project;

class QualityInspection extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'inspection_number',
        'type',
        'template_id',
        'item_id',
        'supplier_id',
        'manufacturing_order_id',
        'project_id',
        'batch_number',
        'total_quantity',
        'sample_size',
        'passed_quantity',
        'failed_quantity',
        'status',
        'inspector_id',
        'inspection_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'total_quantity'  => 'float',
        'sample_size'     => 'float',
        'passed_quantity' => 'float',
        'failed_quantity' => 'float',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(QualityTemplate::class, 'template_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'supplier_id');
    }

    public function manufacturingOrder(): BelongsTo
    {
        return $this->belongsTo(ManufacturingOrder::class, 'manufacturing_order_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'inspector_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function results(): HasMany
    {
        return $this->hasMany(QualityInspectionResult::class, 'inspection_id');
    }

    public function ncrs(): HasMany
    {
        return $this->hasMany(QualityNcr::class, 'inspection_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'incoming'      => 'فحص استلام مواد خام (Incoming QC)',
            'in_process'    => 'فحص أثناء التشغيل والتصنيع (In-Process QC)',
            'final_product' => 'فحص المنتج النهائي التام (Final Product QC)',
            'project_site'  => 'فحص واختبار موقع المشروع (Site Quality Test)',
            default         => 'فحص جودة',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'              => 'قيد الفحص والمراجعة (Pending)',
            'passed'               => 'مطابق ومعتمد (Passed)',
            'rejected'             => 'مرفوض وغير مطابق (Rejected)',
            'conditionally_passed' => 'مقبول بتجاوز/استثناء (Conditional Pass)',
            default                => 'قيد الفحص',
        };
    }

    public function getPassRateAttribute(): float
    {
        if ($this->total_quantity > 0) {
            return round(($this->passed_quantity / $this->total_quantity) * 100, 1);
        }

        $totalCheckpoints = $this->results()->count();
        if ($totalCheckpoints > 0) {
            $passedCheckpoints = $this->results()->where('is_passed', true)->count();
            return round(($passedCheckpoints / $totalCheckpoints) * 100, 1);
        }

        return 0.0;
    }

    /**
     * Generate unique sequential number: QC-YYYY-XXXX
     */
    public static function generateNumber(?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = currentTenant() ? currentTenant()->id : (Tenant::first()?->id ?? 1);
        }

        $year = date('Y');
        $prefix = "QC-{$year}-";

        $last = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('inspection_number', 'like', "{$prefix}%")
            ->latest('id')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->inspection_number);
            $seq = intval(end($parts)) + 1;
        }

        $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('inspection_number', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
