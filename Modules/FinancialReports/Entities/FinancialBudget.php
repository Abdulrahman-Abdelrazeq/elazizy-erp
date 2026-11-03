<?php

namespace Modules\FinancialReports\Entities;

use App\Models\Branch;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\JournalEntryLine;

class FinancialBudget extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'financial_budgets';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'fiscal_year',
        'account_id',
        'period_type',
        'target_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'fiscal_year'   => 'integer',
        'target_amount' => 'float',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForYear($query, int $year)
    {
        return $query->where('fiscal_year', $year);
    }

    /**
     * Compute actual amount spent/earned on this account in the fiscal year
     */
    public function getActualAmountAttribute(): float
    {
        if (!$this->account) {
            return 0.00;
        }

        $startDate = "{$this->fiscal_year}-01-01";
        $endDate = "{$this->fiscal_year}-12-31";

        $accountIds = [$this->account_id];
        if ($this->account->is_parent) {
            $accountIds = array_merge($accountIds, $this->account->children()->pluck('id')->toArray());
        }

        $debit = (float) JournalEntryLine::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                  ->whereDate('date', '>=', $startDate)
                  ->whereDate('date', '<=', $endDate);
                if ($this->branch_id) {
                    $q->where('branch_id', $this->branch_id);
                }
            })->sum('debit');

        $credit = (float) JournalEntryLine::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                $q->where('status', 'posted')
                  ->whereDate('date', '>=', $startDate)
                  ->whereDate('date', '<=', $endDate);
                if ($this->branch_id) {
                    $q->where('branch_id', $this->branch_id);
                }
            })->sum('credit');

        // If revenue: credit - debit; If expense/asset: debit - credit
        if ($this->account->type === 'revenue') {
            return max(0, $credit - $debit);
        }

        return max(0, $debit - $credit);
    }

    /**
     * Compute variance (Actual - Budget)
     */
    public function getVarianceAttribute(): float
    {
        return $this->actual_amount - $this->target_amount;
    }

    /**
     * Compute achievement rate percentage
     */
    public function getAchievementPercentAttribute(): float
    {
        if ($this->target_amount <= 0) {
            return 0.00;
        }

        return round(($this->actual_amount / $this->target_amount) * 100, 1);
    }
}
