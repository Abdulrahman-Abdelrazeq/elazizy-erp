<?php

namespace Modules\Setting\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'taxes';

    protected $fillable = [
        'tenant_id',
        'name',
        'rate',
        'code',
        'type',
        'applies_to',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}

