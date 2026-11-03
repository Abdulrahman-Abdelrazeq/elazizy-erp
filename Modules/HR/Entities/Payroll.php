<?php

namespace Modules\HR\Entities;

use App\Models\Branch;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Treasury\Entities\Treasury;

class Payroll extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'hr_payrolls';

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'treasury_id',
        'payroll_number',
        'year',
        'month',
        'status',
        'payment_date',
        'total_basic',
        'total_allowances',
        'total_deductions',
        'total_loans_deducted',
        'total_net',
        'notes',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'payment_date'         => 'date',
        'total_basic'          => 'float',
        'total_allowances'     => 'float',
        'total_deductions'     => 'float',
        'total_loans_deducted' => 'float',
        'total_net'            => 'float',
        'year'                 => 'integer',
        'month'                => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'payroll_id');
    }

    public function recalculateTotals(): void
    {
        $this->total_basic = (float) $this->items()->sum('basic_salary');
        $this->total_allowances = (float) $this->items()->sum('allowances_amount') + (float) $this->items()->sum('overtime_amount');
        $this->total_deductions = (float) $this->items()->sum('deductions_amount');
        $this->total_loans_deducted = (float) $this->items()->sum('loan_deduction');
        $this->total_net = (float) $this->items()->sum('net_salary');
        $this->save();
    }

    public static function generateNumber(int $year, int $month): string
    {
        $monthStr = str_pad($month, 2, '0', STR_PAD_LEFT);
        return "PAY-{$year}-{$monthStr}";
    }
}
