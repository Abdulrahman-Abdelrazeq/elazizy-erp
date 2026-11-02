<?php

namespace Modules\CRM\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Setting\Entities\Location;

class Client extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'clients';

    protected $fillable = [
        'tenant_id',
        'group_id',
        'type',
        'name',
        'code',
        'company_name',
        'phone',
        'mobile',
        'email',
        'tax_number',
        'commercial_register',
        'national_id',
        'location_id',
        'address',
        'credit_limit',
        'credit_days',
        'opening_balance',
        'opening_balance_type',
        'current_balance',
        'loyalty_points',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'opening_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'credit_days' => 'integer',
            'loyalty_points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ClientGroup::class, 'group_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ClientNote::class)->latest();
    }

    public function debtSchedules(): HasMany
    {
        return $this->hasMany(DebtSchedule::class)->orderBy('due_date');
    }

    public function balanceAdjustments(): HasMany
    {
        return $this->hasMany(BalanceAdjustment::class)->latest();
    }

    // Scopes
    public function scopeCustomers(Builder $query): Builder
    {
        return $query->whereIn('type', ['customer', 'both']);
    }

    public function scopeSuppliers(Builder $query): Builder
    {
        return $query->whereIn('type', ['supplier', 'both']);
    }

    public function scopeDebtors(Builder $query): Builder
    {
        return $query->where('current_balance', '>', 0);
    }

    public function scopeCreditors(Builder $query): Builder
    {
        return $query->where('current_balance', '<', 0);
    }

    /**
     * Check if client exceeded credit limit
     */
    public function isCreditLimitExceeded(): bool
    {
        if ($this->credit_limit <= 0) {
            return false;
        }

        return $this->current_balance > $this->credit_limit;
    }

    /**
     * Balance Status and styling badge
     */
    public function balanceStatus(): array
    {
        if ($this->current_balance > 0) {
            return [
                'type' => 'debit',
                'label' => 'مدين (مستحق عليه)',
                'class' => 'text-danger font-bold',
                'bg' => '#FEE2E2',
                'color' => '#DC2626',
            ];
        }

        if ($this->current_balance < 0) {
            return [
                'type' => 'credit',
                'label' => 'دائن (رصيد له)',
                'class' => 'text-success font-bold',
                'bg' => '#D1E7DD',
                'color' => '#0F5132',
            ];
        }

        return [
            'type' => 'balanced',
            'label' => 'متزن (0.00)',
            'class' => 'text-muted',
            'bg' => '#F1F5F9',
            'color' => '#64748B',
        ];
    }
}
