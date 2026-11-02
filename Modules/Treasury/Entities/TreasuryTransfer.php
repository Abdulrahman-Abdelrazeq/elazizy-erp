<?php

namespace Modules\Treasury\Entities;

use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreasuryTransfer extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'transfer_number',
        'from_treasury_id',
        'to_treasury_id',
        'amount',
        'transfer_fee',
        'transfer_date',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount'        => 'float',
        'transfer_fee'  => 'float',
        'transfer_date' => 'date',
    ];

    public function fromTreasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class, 'from_treasury_id');
    }

    public function toTreasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class, 'to_treasury_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate unique sequential transfer number
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
        $candidate = 'TRF-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('transfer_number', $candidate)
            ->exists()) {
            $nextSeq++;
            $candidate = 'TRF-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
