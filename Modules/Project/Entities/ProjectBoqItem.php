<?php

namespace Modules\Project\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectBoqItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'item_code',
        'description',
        'unit',
        'contract_quantity',
        'revised_quantity',
        'unit_price',
        'total_price',
        'executed_quantity',
        'completion_percentage',
    ];

    protected $casts = [
        'contract_quantity'     => 'float',
        'revised_quantity'      => 'float',
        'unit_price'            => 'float',
        'total_price'           => 'float',
        'executed_quantity'     => 'float',
        'completion_percentage' => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getExecutedValueAttribute(): float
    {
        return round((float) $this->executed_quantity * (float) $this->unit_price, 2);
    }

    public function getRemainingQuantityAttribute(): float
    {
        return max(0.00, (float) $this->revised_quantity - (float) $this->executed_quantity);
    }
}
