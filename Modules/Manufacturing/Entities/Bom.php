<?php

namespace Modules\Manufacturing\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\Unit;

class Bom extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'mfg_boms';

    protected $fillable = [
        'tenant_id',
        'bom_number',
        'name',
        'product_id',
        'output_quantity',
        'unit_id',
        'raw_material_cost',
        'labor_cost',
        'overhead_cost',
        'total_cost',
        'cost_per_unit',
        'is_active',
        'is_default',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'output_quantity'   => 'float',
        'raw_material_cost' => 'float',
        'labor_cost'        => 'float',
        'overhead_cost'     => 'float',
        'total_cost'        => 'float',
        'cost_per_unit'     => 'float',
        'is_active'         => 'boolean',
        'is_default'        => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'product_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BomItem::class, 'bom_id');
    }

    public function operations(): HasMany
    {
        return $this->hasMany(BomOperation::class, 'bom_id')->orderBy('sequence_order');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ManufacturingOrder::class, 'bom_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function recalculateCosts(): void
    {
        $materialCost = (float) $this->items()->sum('total_cost');
        $laborCost = (float) $this->operations()->sum('operation_cost');
        $overheadCost = (float) $this->overhead_cost;

        $totalCost = $materialCost + $laborCost + $overheadCost;
        $outputQty = max(0.001, (float) $this->output_quantity);
        $costPerUnit = $totalCost / $outputQty;

        $this->update([
            'raw_material_cost' => $materialCost,
            'labor_cost'        => $laborCost,
            'total_cost'        => $totalCost,
            'cost_per_unit'     => $costPerUnit,
        ]);
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $latest = static::whereYear('created_at', $year)->latest('id')->first();
        $next = $latest ? ((int) substr($latest->bom_number, -4)) + 1 : 1;
        return sprintf("BOM-%s-%04d", $year, $next);
    }
}
