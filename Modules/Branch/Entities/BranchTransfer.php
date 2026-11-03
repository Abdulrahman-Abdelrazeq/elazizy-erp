<?php

namespace Modules\Branch\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Inventory\Entities\Store;

class BranchTransfer extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'branch_transfers';

    protected $fillable = [
        'tenant_id',
        'transfer_number',
        'from_branch_id',
        'to_branch_id',
        'from_store_id',
        'to_store_id',
        'status',
        'transfer_date',
        'dispatch_date',
        'received_date',
        'total_items_count',
        'total_cost',
        'shipping_company',
        'driver_name',
        'vehicle_plate',
        'tracking_number',
        'notes',
        'created_by',
        'dispatched_by',
        'received_by',
    ];

    protected $casts = [
        'transfer_date'     => 'date',
        'dispatch_date'     => 'datetime',
        'received_date'     => 'datetime',
        'total_items_count' => 'integer',
        'total_cost'        => 'float',
    ];

    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function fromStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function toStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BranchTransferItem::class, 'transfer_id');
    }

    public function recalculateTotals(): void
    {
        $this->update([
            'total_items_count' => $this->items()->count(),
            'total_cost'        => (float) $this->items()->sum('total_cost'),
        ]);
    }

    public static function generateNumber(): string
    {
        $year = date('Y');
        $latest = static::whereYear('created_at', $year)->latest('id')->first();
        $next = $latest ? ((int) substr($latest->transfer_number, -4)) + 1 : 1;
        return sprintf("TR-%s-%04d", $year, $next);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'      => 'مسودة تحويل',
            'dispatched' => 'في الطريق (مشحونة)',
            'received'   => 'تم الاستلام والتوريد',
            'cancelled'  => 'ملغي ومسترجع',
            default      => $this->status,
        };
    }
}
