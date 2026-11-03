<?php

namespace Modules\Manufacturing\Entities;

use App\Models\Branch;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workstation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'mfg_workstations';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'code',
        'hourly_cost',
        'capacity_per_day',
        'description',
        'is_active',
    ];

    protected $casts = [
        'hourly_cost'      => 'float',
        'capacity_per_day' => 'float',
        'is_active'        => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(BomOperation::class, 'workstation_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
