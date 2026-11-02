<?php

namespace Modules\Treasury\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreasuryTransaction extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'treasury_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'source_type',
        'source_id',
        'transaction_date',
        'description',
        'created_by',
    ];

    protected $casts = [
        'amount'           => 'float',
        'balance_before'   => 'float',
        'balance_after'    => 'float',
        'transaction_date' => 'datetime',
    ];

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'in' ? 'إيداع / وارد (+)' : 'صرف / صادر (-)';
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source_type) {
            'voucher'   => 'سند مالي',
            'invoice'   => 'فاتورة بيع/شراء',
            'pos_shift' => 'جرد كاشير/وردية',
            'transfer'  => 'تحويل بين الخزائن',
            'opening'   => 'رصيد افتتاحي',
            default     => 'حركة مالية',
        };
    }
}
