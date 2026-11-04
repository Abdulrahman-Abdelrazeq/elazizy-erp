<?php

namespace Modules\Asset\Entities;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Accounting\Entities\CostCenter;
use Modules\CRM\Entities\Client;
use Modules\HR\Entities\Employee;
use Modules\Setting\Entities\Location;

class Asset extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'category_id',
        'location_id',
        'cost_center_id',
        'custodian_employee_id',
        'supplier_id',
        'asset_number',
        'name',
        'barcode',
        'serial_number',
        'model_number',
        'purchase_date',
        'in_service_date',
        'purchase_cost',
        'salvage_value',
        'depreciable_cost',
        'depreciation_method',
        'depreciation_rate',
        'useful_life_years',
        'total_depreciated_amount',
        'current_book_value',
        'status',
        'warranty_expiry',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'purchase_date'            => 'date',
        'in_service_date'          => 'date',
        'warranty_expiry'          => 'date',
        'purchase_cost'            => 'float',
        'salvage_value'            => 'float',
        'depreciable_cost'         => 'float',
        'depreciation_rate'        => 'float',
        'useful_life_years'        => 'float',
        'total_depreciated_amount' => 'float',
        'current_book_value'       => 'float',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function costCenter(): BelongsTo
    {
        return $this->belongsTo(CostCenter::class, 'cost_center_id');
    }

    public function custodian(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'custodian_employee_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'supplier_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function depreciations(): HasMany
    {
        return $this->hasMany(AssetDepreciation::class, 'asset_id');
    }

    public function disposal(): HasOne
    {
        return $this->hasOne(AssetDisposal::class, 'asset_id');
    }

    /**
     * Calculate annual depreciation amount based on configured method
     */
    public function calculateAnnualDepreciation(): float
    {
        if ($this->depreciation_method === 'none' || in_array($this->status, ['disposed', 'sold', 'fully_depreciated'])) {
            return 0.00;
        }

        $depreciable = max(0.00, (float) $this->purchase_cost - (float) $this->salvage_value);

        if ($this->depreciation_method === 'straight_line') {
            if ($this->useful_life_years > 0) {
                return round($depreciable / $this->useful_life_years, 2);
            }
            if ($this->depreciation_rate > 0) {
                return round($depreciable * ($this->depreciation_rate / 100), 2);
            }
        } elseif ($this->depreciation_method === 'declining_balance') {
            $rate = $this->depreciation_rate > 0 ? $this->depreciation_rate : 20.00;
            $annual = (float) $this->current_book_value * ($rate / 100);
            $maxPossible = max(0.00, (float) $this->current_book_value - (float) $this->salvage_value);
            return round(min($annual, $maxPossible), 2);
        }

        return 0.00;
    }

    /**
     * Calculate monthly depreciation amount
     */
    public function calculateMonthlyDepreciation(): float
    {
        return round($this->calculateAnnualDepreciation() / 12, 2);
    }

    /**
     * Recalculate book value and accumulated depreciation from records
     */
    public function recalculateBookValue(): void
    {
        $totalDepreciated = (float) $this->depreciations()->sum('depreciation_amount');
        $newBookValue = max((float) $this->salvage_value, (float) $this->purchase_cost - $totalDepreciated);

        $updates = [
            'total_depreciated_amount' => $totalDepreciated,
            'current_book_value'       => $newBookValue,
        ];

        if ($newBookValue <= (float) $this->salvage_value && $this->status === 'active') {
            $updates['status'] = 'fully_depreciated';
        }

        $this->update($updates);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active'            => 'نشط بالخدمة (Active)',
            'in_maintenance'    => 'تحت الصيانة (In Maintenance)',
            'fully_depreciated' => 'مكتمل الإهلاك دفترياً (Fully Depreciated)',
            'disposed'          => 'مستبعد / مكهّن (Disposed/Scrapped)',
            'sold'              => 'مباع (Sold)',
            default             => 'نشط',
        };
    }

    public function getDepreciationMethodLabelAttribute(): string
    {
        return match ($this->depreciation_method) {
            'straight_line'     => 'القسط الثابت',
            'declining_balance' => 'القسط المتناقص',
            'none'              => 'غير خاضع للإهلاك',
            default             => 'القسط الثابت',
        };
    }

    /**
     * Generate sequential unique asset number per tenant: AST-YYYY-XXXX
     */
    public static function generateNumber(?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = currentTenant() ? currentTenant()->id : (Tenant::first()?->id ?? 1);
        }

        $year = date('Y');
        $prefix = "AST-{$year}-";

        $last = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('asset_number', 'like', "{$prefix}%")
            ->latest('id')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last->asset_number);
            $seq = intval(end($parts)) + 1;
        }

        $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('asset_number', $candidate)
            ->exists()) {
            $seq++;
            $candidate = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
