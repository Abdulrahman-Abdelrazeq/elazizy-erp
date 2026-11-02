<?php

namespace Modules\Invoice\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\Unit;

class InvoiceItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'item_id',
        'unit_id',
        'unit_multiplier',
        'quantity',
        'unit_price',
        'cost_price',
        'discount_percent',
        'discount_amount',
        'subtotal',
        'tax_percent',
        'tax_amount',
        'total',
        'batch_number',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'unit_multiplier'  => 'float',
        'quantity'         => 'float',
        'unit_price'       => 'float',
        'cost_price'       => 'float',
        'discount_percent' => 'float',
        'discount_amount'  => 'float',
        'subtotal'         => 'float',
        'tax_percent'      => 'float',
        'tax_amount'       => 'float',
        'total'            => 'float',
        'expiry_date'      => 'date',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
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
