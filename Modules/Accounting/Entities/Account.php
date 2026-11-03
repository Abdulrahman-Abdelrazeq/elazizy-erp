<?php

namespace Modules\Accounting\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'chart_of_accounts';

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'code',
        'name',
        'name_en',
        'type',
        'nature',
        'level',
        'is_parent',
        'opening_balance',
        'current_balance',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'float',
        'current_balance' => 'float',
        'is_parent'       => 'boolean',
        'is_active'       => 'boolean',
        'level'           => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id')->orderBy('code');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function journalLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParents($query)
    {
        return $query->where('is_parent', true);
    }

    public function scopeLeafs($query)
    {
        return $query->where('is_parent', false);
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'asset'     => 'الأصول (Assets)',
            'liability' => 'الالتزامات (Liabilities)',
            'equity'    => 'حقوق الملكية (Equity)',
            'revenue'   => 'الإيرادات (Revenues)',
            'expense'   => 'المصروفات (Expenses)',
            default     => 'أخرى',
        };
    }

    public function getNatureLabelAttribute(): string
    {
        return $this->nature === 'debit' ? 'مدين (Debit)' : 'دائن (Credit)';
    }

    /**
     * Recalculate balance from journal lines
     */
    public function recalculateBalance(): float
    {
        if ($this->is_parent) {
            $sum = 0;
            foreach ($this->children as $child) {
                $sum += $child->recalculateBalance();
            }
            $this->update(['current_balance' => $sum]);
            return $sum;
        }

        $totalDebit = (float) $this->journalLines()
            ->whereHas('journalEntry', fn($q) => $q->where('status', 'posted'))
            ->sum('debit');

        $totalCredit = (float) $this->journalLines()
            ->whereHas('journalEntry', fn($q) => $q->where('status', 'posted'))
            ->sum('credit');

        if ($this->nature === 'debit') {
            $newBalance = $this->opening_balance + $totalDebit - $totalCredit;
        } else {
            $newBalance = $this->opening_balance + $totalCredit - $totalDebit;
        }

        $this->update(['current_balance' => $newBalance]);
        return $newBalance;
    }
}

