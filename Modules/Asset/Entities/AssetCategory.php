<?php

namespace Modules\Asset\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Accounting\Entities\Account;

class AssetCategory extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'code',
        'depreciation_method',
        'depreciation_rate',
        'useful_life_years',
        'asset_account_id',
        'depreciation_account_id',
        'depreciation_expense_account_id',
        'description',
        'is_active',
    ];

    protected $casts = [
        'depreciation_rate' => 'float',
        'useful_life_years' => 'float',
        'is_active'         => 'boolean',
    ];

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    public function assetAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'asset_account_id');
    }

    public function depreciationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'depreciation_account_id');
    }

    public function depreciationExpenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'depreciation_expense_account_id');
    }

    public function getDepreciationMethodLabelAttribute(): string
    {
        return match ($this->depreciation_method) {
            'straight_line'     => 'القسط الثابت (Straight-Line)',
            'declining_balance' => 'القسط المتناقص (Declining Balance)',
            'none'              => 'أصل غير خاضع للإهلاك (Non-Depreciable)',
            default             => 'القسط الثابت',
        };
    }

    public function getTotalGrossCostAttribute(): float
    {
        return (float) $this->assets()->sum('purchase_cost');
    }

    public function getTotalBookValueAttribute(): float
    {
        return (float) $this->assets()->sum('current_book_value');
    }

    public function getTotalDepreciatedAttribute(): float
    {
        return (float) $this->assets()->sum('total_depreciated_amount');
    }
}
