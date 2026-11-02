<?php

namespace Modules\Inventory\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Setting\Entities\Tax;

class Item extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'items';

    protected $fillable = [
        'tenant_id',
        'name',
        'sku',
        'barcode',
        'category_id',
        'brand_id',
        'unit_id',
        'tax_id',
        'buy_price',
        'avg_cost',
        'sell_price',
        'wholesale_price',
        'half_wholesale_price',
        'minimum_sell_price',
        'min_quantity_alert',
        'is_service',
        'has_expiration',
        'expire_date',
        'image',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'buy_price' => 'decimal:2',
            'avg_cost' => 'decimal:2',
            'sell_price' => 'decimal:2',
            'wholesale_price' => 'decimal:2',
            'half_wholesale_price' => 'decimal:2',
            'minimum_sell_price' => 'decimal:2',
            'min_quantity_alert' => 'decimal:2',
            'is_service' => 'boolean',
            'has_expiration' => 'boolean',
            'expire_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ItemStock::class);
    }

    public function itemUnits(): HasMany
    {
        return $this->hasMany(ItemUnit::class);
    }

    /**
     * Get total quantity across all stores
     */
    public function totalQuantity(): float
    {
        if ($this->is_service) {
            return 999.00;
        }

        return (float) $this->stocks()->sum('quantity');
    }

    /**
     * Get stock status badge data
     */
    public function stockStatus(): array
    {
        if ($this->is_service) {
            return ['status' => 'service', 'label' => 'صنف خدمي', 'class' => 'badge-service'];
        }

        $qty = $this->totalQuantity();

        if ($qty <= 0) {
            return ['status' => 'out_of_stock', 'label' => 'نفذ من المخزون', 'class' => 'badge-danger'];
        }

        if ($qty <= $this->min_quantity_alert) {
            return ['status' => 'low_stock', 'label' => 'منخفض (حد الطلب)', 'class' => 'badge-warning'];
        }

        return ['status' => 'in_stock', 'label' => 'متوفر', 'class' => 'badge-success'];
    }

    protected $appends = [
        'selling_price',
        'cost_price',
        'code',
    ];

    public function getSellingPriceAttribute(): float
    {
        return (float) ($this->sell_price ?? 0);
    }

    public function getCostPriceAttribute(): float
    {
        return (float) ($this->buy_price ?? 0);
    }

    public function getCodeAttribute(): string
    {
        return (string) ($this->sku ?: ($this->barcode ?: 'ITM-' . $this->id));
    }
}

