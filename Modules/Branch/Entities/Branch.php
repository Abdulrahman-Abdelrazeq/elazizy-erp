<?php

namespace Modules\Branch\Entities;

use App\Models\Branch as BaseBranch;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\HR\Entities\Employee;
use Modules\Inventory\Entities\ItemStock;
use Modules\Inventory\Entities\Store;
use Modules\Invoice\Entities\Invoice;
use Modules\POS\Entities\PosShift;
use Modules\Treasury\Entities\Treasury;

class Branch extends BaseBranch
{
    use BelongsToTenant;

    protected $table = 'branches';

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'branch_type',
        'code',
        'phone',
        'email',
        'manager_name',
        'commercial_registration',
        'tax_number',
        'monthly_budget',
        'address',
        'city',
        'district',
        'is_main',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_main'        => 'boolean',
            'is_active'      => 'boolean',
            'monthly_budget' => 'float',
        ];
    }

    public function parentBranch(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function subBranches(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'branch_id');
    }

    public function treasuries(): HasMany
    {
        return $this->hasMany(Treasury::class, 'branch_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }

    public function posShifts(): HasMany
    {
        return $this->hasMany(PosShift::class, 'branch_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'branch_id');
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'branch_users', 'branch_id', 'user_id')
            ->withPivot(['role', 'is_default', 'can_switch'])
            ->withTimestamps();
    }

    public function outboundTransfers(): HasMany
    {
        return $this->hasMany(BranchTransfer::class, 'from_branch_id');
    }

    public function inboundTransfers(): HasMany
    {
        return $this->hasMany(BranchTransfer::class, 'to_branch_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->branch_type) {
            'hq'                  => 'المقر الرئيسي (HQ)',
            'retail'              => 'فرع مبيعات وتجزئة',
            'wholesale'           => 'فرع جملة وتوزيع',
            'distribution_center' => 'مركز توزيع إقليمي',
            'warehouse_only'      => 'مستودع لوجستي فقط',
            default               => 'فرع تشغيلي',
        };
    }

    public function getInventoryValue(): float
    {
        $storeIds = $this->stores()->pluck('id');
        if ($storeIds->isEmpty()) {
            return 0.00;
        }

        return (float) ItemStock::whereIn('store_id', $storeIds)
            ->selectRaw('SUM(quantity * avg_cost) as total_val')
            ->value('total_val') ?: 0.00;
    }

    public function getMonthlySales(): float
    {
        return (float) $this->invoices()
            ->where('type', 'sale')
            ->where('status', '!=', 'cancelled')
            ->whereMonth('date', date('m'))
            ->whereYear('date', date('Y'))
            ->sum('grand_total');
    }
}
