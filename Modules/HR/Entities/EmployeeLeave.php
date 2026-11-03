<?php

namespace Modules\HR\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeave extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_leaves';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_count',
        'reason',
        'status',
        'approved_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'days_count' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getLeaveTypeLabelAttribute(): string
    {
        return match ($this->leave_type) {
            'annual'    => 'إجازة سنوية اعتيادية',
            'sick'      => 'إجازة مرضية',
            'emergency' => 'إجازة عارضة',
            'unpaid'    => 'إجازة بدون راتب',
            default     => $this->leave_type,
        };
    }
}
