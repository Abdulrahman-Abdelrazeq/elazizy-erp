<?php

namespace Modules\POS\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CRM\Entities\Client;

class PosHeldOrder extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'shift_id',
        'terminal_id',
        'reference_code',
        'client_id',
        'cart_data',
        'subtotal',
        'notes',
        'held_at',
    ];

    protected $casts = [
        'cart_data' => 'array',
        'subtotal'  => 'float',
        'held_at'   => 'datetime',
    ];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'shift_id');
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'terminal_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
