<?php

namespace Modules\HR\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Designation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_designations';

    protected $fillable = [
        'tenant_id',
        'department_id',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

