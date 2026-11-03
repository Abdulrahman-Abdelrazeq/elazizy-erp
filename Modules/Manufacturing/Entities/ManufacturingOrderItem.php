<?php

namespace Modules\Manufacturing\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Entities\Item;

class ManufacturingOrderItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'mfg_order_items';

    protected $fillable = [
        'tenant_id',
        'order_id',
        'item_id',
        'planned_quantity',
        'actual_quantity',
        'unit_cost',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'planned_quantity' => 'float',
        'actual_quantity'  => 'float',
        'unit_cost'        => 'float',
        'total_cost'       => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(ManufacturingOrder::class, 'order_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
