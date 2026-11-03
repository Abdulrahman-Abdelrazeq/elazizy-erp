<?php

namespace Modules\HR\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryClause extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_salary_clauses';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'is_fixed',
        'default_amount',
        'is_active',
    ];

    protected $casts = [
        'is_fixed'       => 'boolean',
        'is_active'      => 'boolean',
        'default_amount' => 'float',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEarnings($query)
    {
        return $query->where('type', 'earning');
    }

    public function scopeDeductions($query)
    {
        return $query->where('type', 'deduction');
    }
}
