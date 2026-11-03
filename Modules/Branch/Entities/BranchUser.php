<?php

namespace Modules\Branch\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchUser extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'branch_users';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'user_id',
        'role',
        'is_default',
        'can_switch',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'can_switch' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'manager'     => 'مدير الفرع',
            'accountant'  => 'محاسب الفرع',
            'cashier'     => 'كاشير / نقطة بيع',
            'storekeeper' => 'أمين مستودع الفرع',
            'sales_rep'   => 'مسؤول مبيعات',
            default       => 'موظف تشغيل',
        };
    }
}
