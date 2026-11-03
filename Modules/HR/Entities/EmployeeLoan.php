<?php

namespace Modules\HR\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Treasury\Entities\Treasury;

class EmployeeLoan extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_loans';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'treasury_id',
        'loan_number',
        'date',
        'amount',
        'monthly_installment',
        'total_installments',
        'paid_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'date'                => 'date',
        'amount'              => 'float',
        'monthly_installment' => 'float',
        'paid_amount'         => 'float',
        'total_installments'  => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0.00, $this->amount - $this->paid_amount);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function generateNumber(): string
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;
        $count = static::withoutGlobalScopes()->where('tenant_id', $tenantId)->count() + 1;
        $number = 'LOAN-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('loan_number', $number)->exists()) {
            $count++;
            $number = 'LOAN-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        return $number;
    }
}
