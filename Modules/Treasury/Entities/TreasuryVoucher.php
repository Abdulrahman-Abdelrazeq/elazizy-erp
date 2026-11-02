<?php

namespace Modules\Treasury\Entities;

use App\Models\Branch;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CRM\Entities\Client;
use Modules\Invoice\Entities\Invoice;

class TreasuryVoucher extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'treasury_id',
        'voucher_number',
        'type',
        'category_id',
        'client_id',
        'invoice_id',
        'amount',
        'date',
        'payment_method',
        'reference_number',
        'received_from_or_paid_to',
        'notes',
        'attachment',
        'status',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'float',
        'date'   => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeReceipts($query)
    {
        return $query->where('type', 'receipt');
    }

    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'receipt' ? 'سند قبض / تحصيل' : 'سند صرف / دفع';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'cash'          => 'نقدي (كاش)',
            'bank_transfer' => 'تحويل بنكي',
            'check'         => 'شيك بنكي',
            'online'        => 'دفع إلكتروني / محفظة',
            'card'          => 'بطاقة بنكية',
            default         => 'نقدي',
        };
    }

    /**
     * Generate sequential unique voucher number per type
     */
    public static function generateNumber(string $type, ?int $tenantId = null): string
    {
        if (!$tenantId) {
            $tenantId = (auth()->check() && auth()->user()->tenant_id)
                ? auth()->user()->tenant_id
                : (session('tenant_id') ?: (Tenant::first()?->id ?? 1));
        }

        $prefix = $type === 'receipt' ? 'REC-' : 'PAY-';

        $last = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('type', $type)
            ->latest('id')
            ->first();

        $nextSeq = $last ? ($last->id + 1) : 1001;
        $candidate = $prefix . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('type', $type)
            ->where('voucher_number', $candidate)
            ->exists()) {
            $nextSeq++;
            $candidate = $prefix . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
        }

        return $candidate;
    }
}
