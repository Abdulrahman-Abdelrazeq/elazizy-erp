<?php

namespace Modules\POS\Entities;

use App\Models\Branch;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CRM\Entities\Client;
use Modules\Inventory\Entities\Store;

class PosTerminal extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'store_id',
        'name',
        'code',
        'printer_type',
        'default_client_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function defaultClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'default_client_id');
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(CashierShift::class, 'terminal_id');
    }

    public function activeShift(): ?CashierShift
    {
        return $this->shifts()->where('status', 'open')->latest('id')->first();
    }
}

