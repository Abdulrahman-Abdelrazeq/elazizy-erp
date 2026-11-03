<?php

namespace Modules\HR\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_departments';

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'manager_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function designations(): HasMany
    {
        return $this->hasMany(Designation::class, 'department_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
