<?php

namespace Modules\Project\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectChangeOrder extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'order_number',
        'title',
        'request_date',
        'approval_date',
        'amount_change',
        'time_extension_days',
        'status',
        'justification',
        'approved_by',
    ];

    protected $casts = [
        'request_date'        => 'date',
        'approval_date'       => 'date',
        'amount_change'       => 'float',
        'time_extension_days' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'    => 'مسودة قيد المراجعة',
            'approved' => 'معتمد ومضاف للعقد',
            'rejected' => 'مرفوض',
            default    => 'مسودة',
        };
    }

    public static function generateOrderNumber(int $projectId): string
    {
        $count = static::where('project_id', $projectId)->count();
        return 'CO-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
}
