<?php

namespace Modules\Inventory\Entities;

use App\Models\Branch;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'stores';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'code',
        'keeper_name',
        'phone',
        'address',
        'is_main',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_main' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ItemStock::class);
    }

    public function totalItemsCount(): int
    {
        return $this->stocks()->where('quantity', '>', 0)->count();
    }
}

