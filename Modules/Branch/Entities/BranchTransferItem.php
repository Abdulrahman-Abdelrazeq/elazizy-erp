<?php

namespace Modules\Branch\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Inventory\Entities\Item;

class BranchTransferItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'branch_transfer_items';

    protected $fillable = [
        'tenant_id',
        'transfer_id',
        'item_id',
        'sent_quantity',
        'received_quantity',
        'unit_cost',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'sent_quantity'     => 'float',
        'received_quantity' => 'float',
        'unit_cost'         => 'float',
        'total_cost'        => 'float',
    ];

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(BranchTransfer::class, 'transfer_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
