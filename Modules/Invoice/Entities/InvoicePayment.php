<?php

namespace Modules\Invoice\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'invoice_id',
        'payment_method',
        'amount',
        'payment_date',
        'reference_number',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount'       => 'float',
        'payment_date' => 'date',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash'          => 'نقدي (كاش)',
            'card'          => 'شبكة / بطاقة بنكية',
            'bank_transfer' => 'تحويل بنكي',
            'cheque'        => 'شيك بنكي',
            default         => 'نقدي',
        };
    }
}

