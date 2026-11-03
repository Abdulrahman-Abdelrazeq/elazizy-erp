<?php

namespace Modules\Treasury\Entities;

use App\Models\Branch;
use App\Models\User;
use App\Traits\BelongsToTenant;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Treasury extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'type',
        'account_number',
        'bank_name',
        'bank_branch',
        'iban',
        'swift',
        'opening_balance',
        'current_balance',
        'currency',
        'is_active',
        'is_default',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'opening_balance' => 'float',
        'current_balance' => 'float',
        'is_active'       => 'boolean',
        'is_default'      => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(TreasuryTransaction::class, 'treasury_id')->latest('transaction_date');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(TreasuryVoucher::class, 'treasury_id');
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(TreasuryTransfer::class, 'from_treasury_id');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(TreasuryTransfer::class, 'to_treasury_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'cash'   => 'خزينة نقدية (كاش)',
            'bank'   => 'حساب بنكي',
            'wallet' => 'محفظة إلكترونية',
            default  => 'خزينة مالية',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getBalanceAttribute(): float
    {
        return (float) $this->current_balance;
    }

    /**
     * Credit treasury (deposit / in) and log transaction
     */
    public function credit(float $amount, string $sourceType, ?int $sourceId, string $description, ?DateTimeInterface $date = null): TreasuryTransaction
    {
        $balanceBefore = (float) $this->current_balance;
        $balanceAfter = $balanceBefore + $amount;

        $this->update(['current_balance' => $balanceAfter]);

        return TreasuryTransaction::create([
            'tenant_id'        => $this->tenant_id,
            'treasury_id'      => $this->id,
            'type'             => 'in',
            'amount'           => $amount,
            'balance_before'   => $balanceBefore,
            'balance_after'    => $balanceAfter,
            'source_type'      => $sourceType,
            'source_id'        => $sourceId,
            'transaction_date' => $date ?: now(),
            'description'      => $description,
            'created_by'       => auth()->id(),
        ]);
    }

    /**
     * Debit treasury (withdrawal / out) and log transaction
     */
    public function debit(float $amount, string $sourceType, ?int $sourceId, string $description, ?DateTimeInterface $date = null): TreasuryTransaction
    {
        $balanceBefore = (float) $this->current_balance;
        $balanceAfter = $balanceBefore - $amount;

        $this->update(['current_balance' => $balanceAfter]);

        return TreasuryTransaction::create([
            'tenant_id'        => $this->tenant_id,
            'treasury_id'      => $this->id,
            'type'             => 'out',
            'amount'           => $amount,
            'balance_before'   => $balanceBefore,
            'balance_after'    => $balanceAfter,
            'source_type'      => $sourceType,
            'source_id'        => $sourceId,
            'transaction_date' => $date ?: now(),
            'description'      => $description,
            'created_by'       => auth()->id(),
        ]);
    }

    /**
     * Recalculate balance from ledger history
     */
    public function recalculateBalance(): float
    {
        $totalIn = (float) $this->transactions()->where('type', 'in')->sum('amount');
        $totalOut = (float) $this->transactions()->where('type', 'out')->sum('amount');
        $newBalance = $this->opening_balance + $totalIn - $totalOut;

        $this->update(['current_balance' => $newBalance]);
        return $newBalance;
    }
}

