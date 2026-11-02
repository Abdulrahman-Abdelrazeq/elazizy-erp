<?php

namespace Modules\Setting\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'locations';

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'type',
        'delivery_cost',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'delivery_cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }
}

