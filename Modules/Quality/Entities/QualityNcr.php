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

class QualityNcr extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'quality_ncrs';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'ncr_number',
        'inspection_id',
        'item_id',
        'supplier_id',
        'manufacturing_order_id',
        'project_id',
        'title',
        'description',
        'severity',
        'root_cause_category',
        'root_cause_analysis',
        'defect_quantity',
        'estimated_loss_amount',
        'disposition',
        'disposition_notes',
        'status',
        'reported_by',
        'closed_at',
        'created_by',
    ];

    protected $casts = [
        'defect_quantity'       => 'float',
        'estimated_loss_amount' => 'float',
        'closed_at'             => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(QualityInspection::class, 'inspection_id');
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

    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reported_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function capas(): HasMany
    {
        return $this->hasMany(QualityCapa::class, 'ncr_id');
    }

    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity) {
            'critical' => 'حرج وخطير (Critical)',
            'major'    => 'رئيسي وجوهري (Major)',
            'minor'    => 'ثانوي وبسيط (Minor)',
            default    => 'ثانوي',
        };
    }

    public function getRootCauseLabelAttribute(): string
    {
        return match ($this->root_cause_category) {
            'raw_material' => 'مواد خام وموردين (Raw Material)',
            'machinery'    => 'آلات ومعدات التشغيل (Machinery)',
            'workmanship'  => 'عمالة وتشغيل بشري (Workmanship)',
            'environment'  => 'بيئة وتخزين (Environment)',
            'design'       => 'مخططات وتصميم (Design)',
            default        => 'أسباب أخرى',
        };
    }

    public function getDispositionLabelAttribute(): string
    {
        return match ($this->disposition) {
            'pending'          => 'قيد تحديد القرار (Pending)',
            'scrap'            => 'إتلاف وتكهين (Scrap)',
            'rework'           => 'إعادة تشغيل وتصليح (Rework)',
            'return_to_vendor' => 'إرجاع للمورد المورد (Return to Vendor)',
            'use_as_is'        => 'قبول باستثناء فني (Use As-Is)',
            default            => 'قيد الدراسة',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open'                => 'مفتوح قيد المعالجة (Open)',
            'under_investigation' => 'قيد التحقيق والتحليل (Under Investigation)',
            'action_taken'        => 'تم اتخاذ الإجراء (Action Taken)',
            'closed'              => 'مغلق ومحقق (Closed)',
            default               => 'مفتوح',
        };
    }

    /**
     * Generate sequential number: NCR-YYYY-XXXX
     */
    public static function generateNumber(?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = currentTenant() ? currentTenant()->id : (Tenant::first()?->id ?? 1);
        }

        $year = date('Y');
        $prefix = "NCR-{$year}-";

        $last = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('ncr_number', 'like', "{$prefix}%")
            ->latest('id')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->ncr_number);
            $seq = intval(end($parts)) + 1;
        }

        $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('ncr_number', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
