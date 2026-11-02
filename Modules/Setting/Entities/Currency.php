<?php

namespace Modules\Setting\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'currencies';

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'subunit',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:4',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}

