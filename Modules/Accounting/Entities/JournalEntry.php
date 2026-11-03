<?php

namespace Modules\Accounting\Entities;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'entry_number',
        'date',
        'reference',
        'description',
        'total_debit',
        'total_credit',
        'status',
        'source_type',
        'source_id',
        'created_by',
        'posted_by',
        'posted_at',
    ];

    protected $casts = [
        'date'         => 'date',
        'total_debit'  => 'float',
        'total_credit' => 'float',
        'posted_at'    => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    public function isBalanced(): bool
    {
        return abs((float) $this->total_debit - (float) $this->total_credit) < 0.001;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'posted' => 'معتمد ومؤثر (Posted)',
            'draft'  => 'مسودة قيد (Draft)',
            default  => 'معتمد',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source_type) {
            'manual'    => 'قيد يدوي عام',
            'invoice'   => 'فاتورة بيع/شراء',
            'voucher'   => 'سند قبض/صرف',
            'pos_shift' => 'جرد كاشير',
            'opening'   => 'قيد افتتاحي',
            default     => 'حركة نظام',
        };
    }

    /**
     * Generate sequential unique journal entry number per tenant
     */
    public static function generateNumber(?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = (auth()->check() && auth()->user()->tenant_id)
                ? auth()->user()->tenant_id
                : (session('tenant_id') ?: (Tenant::first()?->id ?? 1));
        }

        $last = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->latest('id')
            ->first();

        $nextSeq = $last ? ($last->id + 1) : 1001;
        $candidate = 'ENT-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('entry_number', $candidate)
            ->exists()) {
            $nextSeq++;
            $candidate = 'ENT-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}

