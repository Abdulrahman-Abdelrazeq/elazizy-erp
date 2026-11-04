<?php

namespace Modules\Quality\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityInspectionResult extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'inspection_id',
        'template_item_id',
        'checkpoint_name',
        'standard_criteria',
        'observed_value',
        'is_passed',
        'defect_notes',
    ];

    protected $casts = [
        'is_passed' => 'boolean',
    ];

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(QualityInspection::class, 'inspection_id');
    }

    public function templateItem(): BelongsTo
    {
        return $this->belongsTo(QualityTemplateItem::class, 'template_item_id');
    }
}
