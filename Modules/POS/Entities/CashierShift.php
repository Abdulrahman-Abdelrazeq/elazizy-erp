<?php

namespace Modules\POS\Entities;

use App\Models\Branch;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Invoice\Entities\Invoice;

class CashierShift extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'terminal_id',
        'user_id',
        'shift_number',
        'started_at',
        'ended_at',
        'status',
        'opening_balance',
        'cash_sales',
        'card_sales',
        'credit_sales',
        'total_returns',
        'expected_cash',
        'actual_cash',
        'cash_difference',
        'start_notes',
        'end_notes',
        'closed_by',
    ];

    protected $casts = [
        'started_at'       => 'datetime',
        'ended_at'         => 'datetime',
        'opening_balance'  => 'float',
        'cash_sales'       => 'float',
        'card_sales'       => 'float',
        'credit_sales'     => 'float',
        'total_returns'    => 'float',
        'expected_cash'    => 'float',
        'actual_cash'      => 'float',
        'cash_difference'  => 'float',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function terminal(): BelongsTo
    {
        return $this->belongsTo(PosTerminal::class, 'terminal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'shift_id');
    }

    public function heldOrders(): HasMany
    {
        return $this->hasMany(PosHeldOrder::class, 'shift_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Recalculate shift sales and expected drawer cash
     */
    public function recalculateTotals(): self
    {
        $cashSales = (float) $this->invoices()
            ->where('type', 'sale')
            ->where('status', '!=', 'cancelled')
            ->where('payment_type', 'cash')
            ->sum('paid_amount');

        $cardSales = (float) $this->invoices()
            ->where('type', 'sale')
            ->where('status', '!=', 'cancelled')
            ->where('payment_type', 'card')
            ->sum('paid_amount');

        $creditSales = (float) $this->invoices()
            ->where('type', 'sale')
            ->where('status', '!=', 'cancelled')
            ->where('payment_type', 'credit')
            ->sum('grand_total');

        $returns = (float) $this->invoices()
            ->where('type', 'sale_return')
            ->where('status', '!=', 'cancelled')
            ->sum('grand_total');

        $expectedCash = $this->opening_balance + $cashSales - $returns;

        $this->update([
            'cash_sales'    => $cashSales,
            'card_sales'    => $cardSales,
            'credit_sales'  => $creditSales,
            'total_returns' => $returns,
            'expected_cash' => $expectedCash,
        ]);

        return $this;
    }

    /**
     * Generate unique shift sequence number
     */
    public static function generateNumber(?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = (auth()->check() && auth()->user()->tenant_id)
                ? auth()->user()->tenant_id
                : (session('tenant_id') ?: (\App\Models\Tenant::first()?->id ?? 1));
        }

        $last = static::withoutGlobalScope('tenant')->where('tenant_id', $tenantId)->latest('id')->first();
        $next = $last ? ($last->id + 1) : 101;
        $candidate = 'SHF-' . str_pad($next, 5, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('shift_number', $candidate)
            ->exists()) {
            $next++;
            $candidate = 'SHF-' . str_pad($next, 5, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
