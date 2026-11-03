<?php

namespace Modules\Manufacturing\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BomOperation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'mfg_bom_operations';

    protected $fillable = [
        'tenant_id',
        'bom_id',
        'workstation_id',
        'operation_name',
        'sequence_order',
        'standard_time_minutes',
        'operation_cost',
        'notes',
    ];

    protected $casts = [
        'sequence_order'        => 'integer',
        'standard_time_minutes' => 'float',
        'operation_cost'        => 'float',
    ];

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class, 'bom_id');
    }

    public function workstation(): BelongsTo
    {
        return $this->belongsTo(Workstation::class, 'workstation_id');
    }
}
