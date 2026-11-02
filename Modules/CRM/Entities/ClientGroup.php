<?php

namespace Modules\CRM\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientGroup extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'client_groups';

    protected $fillable = [
        'tenant_id',
        'name',
        'price_tier',
        'discount_percentage',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_percentage' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'group_id');
    }
}

