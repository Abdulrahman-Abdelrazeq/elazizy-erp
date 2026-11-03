<?php

namespace Modules\Manufacturing\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Entities\Item;

class BomItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'mfg_bom_items';

    protected $fillable = [
        'tenant_id',
        'bom_id',
        'item_id',
        'quantity',
        'unit_cost',
        'scrap_percentage',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'quantity'         => 'float',
        'unit_cost'        => 'float',
        'scrap_percentage' => 'float',
        'total_cost'       => 'float',
    ];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class, 'bom_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
