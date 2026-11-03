<?php

namespace Modules\HR\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_attendances';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'late_minutes',
        'overtime_hours',
        'notes',
    ];

    protected $casts = [
        'date'           => 'date',
        'late_minutes'   => 'integer',
        'overtime_hours' => 'float',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'present'  => 'حاضر',
            'absent'   => 'غائب',
            'late'     => 'متأخر',
            'on_leave' => 'إجازة رسمية',
            default    => $this->status,
        };
    }
}

