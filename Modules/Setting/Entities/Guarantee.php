<?php

namespace Modules\Setting\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guarantee extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'guarantees';

    protected $fillable = [
        'tenant_id',
        'title',
        'duration_days',
        'policy',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'duration_days' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}

