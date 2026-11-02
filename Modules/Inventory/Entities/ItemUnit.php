<?php

namespace Modules\Inventory\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemUnit extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'item_units';

    protected $fillable = [
        'tenant_id',
        'item_id',
        'unit_id',
        'multiplier',
        'buy_price',
        'sell_price',
        'barcode',
    ];

    protected function casts(): array
    {
        return [
            'multiplier' => 'decimal:3',
            'buy_price' => 'decimal:2',
            'sell_price' => 'decimal:2',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}

