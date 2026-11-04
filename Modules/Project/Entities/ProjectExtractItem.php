<?php

namespace Modules\Project\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectExtractItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'extract_id',
        'boq_item_id',
        'item_description',
        'unit',
        'unit_price',
        'previous_quantity',
        'current_quantity',
        'total_quantity',
        'current_amount',
        'total_amount',
        'completion_percentage',
    ];

    protected $casts = [
        'unit_price'            => 'float',
        'previous_quantity'     => 'float',
        'current_quantity'      => 'float',
        'total_quantity'        => 'float',
        'current_amount'        => 'float',
        'total_amount'          => 'float',
        'completion_percentage' => 'float',
    ];

    public function extract(): BelongsTo
    {
        return $this->belongsTo(ProjectExtract::class, 'extract_id');
    }

    public function boqItem(): BelongsTo
    {
        return $this->belongsTo(ProjectBoqItem::class, 'boq_item_id');
    }
}
