<?php

namespace Modules\HR\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_payroll_items';

    protected $fillable = [
        'tenant_id',
        'payroll_id',
        'employee_id',
        'basic_salary',
        'allowances_amount',
        'overtime_amount',
        'deductions_amount',
        'loan_deduction',
        'net_salary',
        'status',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'basic_salary'      => 'float',
        'allowances_amount' => 'float',
        'overtime_amount'   => 'float',
        'deductions_amount' => 'float',
        'loan_deduction'    => 'float',
        'net_salary'        => 'float',
    ];

    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getGrossSalaryAttribute(): float
    {
        return $this->basic_salary + $this->allowances_amount + $this->overtime_amount;
    }

    public function getTotalDeductionsAttribute(): float
    {
        return $this->deductions_amount + $this->loan_deduction;
    }
}

