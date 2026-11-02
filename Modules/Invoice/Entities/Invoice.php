<?php

namespace Modules\Invoice\Entities;

use App\Models\Branch;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\CRM\Entities\Client;
use Modules\Inventory\Entities\Store;
use Modules\Setting\Entities\Tax;

class Invoice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'store_id',
        'client_id',
        'invoice_number',
        'type',
        'date',
        'due_date',
        'status',
        'payment_type',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_id',
        'tax_rate',
        'tax_amount',
        'shipping_cost',
        'grand_total',
        'paid_amount',
        'remaining_amount',
        'notes',
        'terms_conditions',
        'parent_id',
        'created_by',
    ];

    protected $casts = [
        'date'             => 'date',
        'due_date'         => 'date',
        'subtotal'         => 'float',
        'discount_value'   => 'float',
        'discount_amount'  => 'float',
        'tax_rate'         => 'float',
        'tax_amount'       => 'float',
        'shipping_cost'    => 'float',
        'grand_total'      => 'float',
        'paid_amount'      => 'float',
        'remaining_amount' => 'float',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Invoice::class, 'parent_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeSales($query)
    {
        return $query->where('type', 'sale');
    }

    public function scopePurchases($query)
    {
        return $query->where('type', 'purchase');
    }

    public function scopeSaleReturns($query)
    {
        return $query->where('type', 'sale_return');
    }

    public function scopePurchaseReturns($query)
    {
        return $query->where('type', 'purchase_return');
    }

    public function scopeQuotations($query)
    {
        return $query->where('type', 'quotation');
    }

    public function scopePurchaseOrders($query)
    {
        return $query->where('type', 'purchase_order');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'sale'            => 'فاتورة مبيعات',
            'purchase'        => 'فاتورة مشتريات',
            'sale_return'     => 'مرتجع مبيعات',
            'purchase_return' => 'مرتجع مشتريات',
            'quotation'       => 'عرض أسعار',
            'purchase_order'  => 'أمر شراء',
            default           => 'فاتورة',
        };
    }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match($this->payment_type) {
            'cash'          => 'نقدي (كاش)',
            'credit'        => 'آجل (ذمم)',
            'partial'       => 'سداد جزئي',
            'card'          => 'بطاقة بنكية / مدى',
            'bank_transfer' => 'تحويل بنكي',
            default         => 'نقدي',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved'  => 'معتمدة',
            'draft'     => 'مسودة',
            'closed'    => 'مغلقة ومسددة',
            'cancelled' => 'ملغاة',
            default     => 'معتمدة',
        };
    }

    /**
     * Generate next sequential invoice number per tenant and type
     */
    public static function generateNumber(string $type, ?int $tenantId = null): string
    {
        $tenantId = $tenantId ?: (auth()->check() ? auth()->user()->tenant_id : session('tenant_id', 1));
        $prefix = match($type) {
            'sale'            => 'INV-SAL-',
            'purchase'        => 'INV-PUR-',
            'sale_return'     => 'RET-SAL-',
            'purchase_return' => 'RET-PUR-',
            'quotation'       => 'QUO-',
            'purchase_order'  => 'PO-',
            default           => 'INV-',
        };

        $last = static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('type', $type)
            ->latest('id')
            ->first();

        $nextSeq = $last ? ($last->id + 1) : 1001;
        return $prefix . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
    }
}
