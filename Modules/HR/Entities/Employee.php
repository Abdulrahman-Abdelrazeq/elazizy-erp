<?php

namespace Modules\HR\Entities;

use App\Models\Branch;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_employees';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'department_id',
        'designation_id',
        'user_id',
        'emp_code',
        'name',
        'national_id',
        'phone',
        'email',
        'hire_date',
        'birth_date',
        'contract_type',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'other_allowances',
        'bank_name',
        'bank_account',
        'iban',
        'status',
        'notes',
    ];

    protected $casts = [
        'hire_date'           => 'date',
        'birth_date'          => 'date',
        'basic_salary'        => 'float',
        'housing_allowance'   => 'float',
        'transport_allowance' => 'float',
        'other_allowances'    => 'float',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(EmployeeLoan::class, 'employee_id');
    }

    public function activeLoans(): HasMany
    {
        return $this->loans()->where('status', 'active');
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(EmployeeLeave::class, 'employee_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }

    public function payrollItems(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'employee_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getTotalSalaryAttribute(): float
    {
        return (float) ($this->basic_salary + $this->housing_allowance + $this->transport_allowance + $this->other_allowances);
    }

    public function getActiveLoanBalanceAttribute(): float
    {
        return (float) $this->activeLoans->sum(fn($loan) => max(0, $loan->amount - $loan->paid_amount));
    }

    public static function generateCode(): string
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;
        $count = static::withoutGlobalScopes()->where('tenant_id', $tenantId)->count() + 1;
        $code = 'EMP-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        while (static::withoutGlobalScopes()->where('tenant_id', $tenantId)->where('emp_code', $code)->exists()) {
            $count++;
            $code = 'EMP-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
