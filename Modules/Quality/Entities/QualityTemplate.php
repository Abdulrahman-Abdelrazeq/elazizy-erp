<?php

namespace Modules\Quality\Entities;

use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Inventory\Entities\Category;
use Modules\Inventory\Entities\Item;

class QualityTemplate extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'type',
        'item_id',
        'category_id',
        'is_active',
        'description',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QualityTemplateItem::class, 'template_id')->orderBy('sort_order');
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(QualityInspection::class, 'template_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'incoming'      => 'فحص استلام مواد خام (Incoming QC)',
            'in_process'    => 'فحص أثناء التشغيل والتصنيع (In-Process QC)',
            'final_product' => 'فحص المنتج النهائي التام (Final Product QC)',
            'project_site'  => 'فحص واختبار موقع المشروع (Site Quality Test)',
            default         => 'فحص جودة',
        };
    }

    /**
     * Generate sequential code: QT-YYYY-XXXX
     */
    public static function generateCode(?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = currentTenant() ? currentTenant()->id : (Tenant::first()?->id ?? 1);
        }

        $year = date('Y');
        $prefix = "QT-{$year}-";

        $last = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('code', 'like', "{$prefix}%")
            ->latest('id')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->code);
            $seq = intval(end($parts)) + 1;
        }

        $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('code', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
