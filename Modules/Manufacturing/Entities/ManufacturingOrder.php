<?php

namespace Modules\Manufacturing\Entities;

use App\Models\Branch;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Inventory\Entities\Item;
use Modules\Inventory\Entities\Store;

class ManufacturingOrder extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'mfg_orders';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'order_number',
        'bom_id',
        'product_id',
        'from_store_id',
        'to_store_id',
        'planned_quantity',
        'produced_quantity',
        'rejected_quantity',
        'status',
        'order_date',
        'expected_completion_date',
        'actual_completion_date',
        'estimated_material_cost',
        'estimated_labor_cost',
        'estimated_overhead_cost',
        'estimated_total_cost',
        'actual_material_cost',
        'actual_labor_cost',
        'actual_overhead_cost',
        'actual_total_cost',
        'unit_cost',
        'notes',
        'created_by',
        'completed_by',
    ];

    protected $casts = [
        'planned_quantity'        => 'float',
        'produced_quantity'       => 'float',
        'rejected_quantity'       => 'float',
        'order_date'               => 'date',
        'expected_completion_date' => 'date',
        'actual_completion_date'   => 'date',
        'estimated_material_cost'  => 'float',
        'estimated_labor_cost'     => 'float',
        'estimated_overhead_cost'  => 'float',
        'estimated_total_cost'     => 'float',
        'actual_material_cost'     => 'float',
        'actual_labor_cost'        => 'float',
        'actual_overhead_cost'     => 'float',
        'actual_total_cost'        => 'float',
        'unit_cost'                => 'float',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function bom(): BelongsTo
    {
        return $this->belongsTo(Bom::class, 'bom_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'product_id');
    }

    public function fromStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ManufacturingOrderItem::class, 'order_id');
    }

    public function recalculateCosts(): void
    {
        $materialCost = (float) $this->items()->sum('total_cost');
        $laborCost = (float) $this->actual_labor_cost ?: (float) $this->estimated_labor_cost;
        $overheadCost = (float) $this->actual_overhead_cost ?: (float) $this->estimated_overhead_cost;

        $totalCost = $materialCost + $laborCost + $overheadCost;
        $produced = max(1.0, (float) $this->produced_quantity ?: (float) $this->planned_quantity);
        $unitCost = $totalCost / $produced;

        $this->update([
            'actual_material_cost' => $materialCost,
            'actual_labor_cost'    => $laborCost,
            'actual_overhead_cost' => $overheadCost,
            'actual_total_cost'    => $totalCost,
            'unit_cost'            => $unitCost,
        ]);
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $latest = static::whereYear('created_at', $year)->latest('id')->first();
        $next = $latest ? ((int) substr($latest->order_number, -4)) + 1 : 1;
        return sprintf("MO-%s-%04d", $year, $next);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'       => 'مسودة تخطيطية',
            'confirmed'   => 'مؤكد ومعتمد',
            'in_progress' => 'قيد التشغيل',
            'completed'   => 'مكتمل ومُنتج',
            'cancelled'   => 'ملغي',
            default       => $this->status,
        };
    }
}
