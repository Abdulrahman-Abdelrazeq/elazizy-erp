<?php

namespace Modules\Inventory\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemStock extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'item_stocks';

    protected $fillable = [
        'tenant_id',
        'item_id',
        'store_id',
        'quantity',
        'avg_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'avg_cost' => 'decimal:2',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }
}

