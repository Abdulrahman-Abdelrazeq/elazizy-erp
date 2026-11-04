<?php

namespace Modules\Project\Entities;

use App\Models\Tenant;
use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectExtract extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'extract_number',
        'extract_type',
        'period_from',
        'period_to',
        'issue_date',
        'gross_amount',
        'previous_gross_amount',
        'current_gross_amount',
        'advance_payment_deduction',
        'retention_deduction',
        'other_deductions',
        'net_subtotal',
        'tax_rate',
        'tax_amount',
        'net_amount',
        'paid_amount',
        'status',
        'consultant_engineer_name',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'period_from'               => 'date',
        'period_to'                 => 'date',
        'issue_date'                => 'date',
        'gross_amount'              => 'float',
        'previous_gross_amount'     => 'float',
        'current_gross_amount'      => 'float',
        'advance_payment_deduction' => 'float',
        'retention_deduction'       => 'float',
        'other_deductions'          => 'float',
        'net_subtotal'              => 'float',
        'tax_rate'                  => 'float',
        'tax_amount'                => 'float',
        'net_amount'                => 'float',
        'paid_amount'               => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectExtractItem::class, 'extract_id');
    }

    /**
     * Recalculate totals and deductions
     */
    public function recalculateTotals(): void
    {
        $gross = (float) $this->items()->sum('total_amount');
        $prevGross = (float) $this->previous_gross_amount;
        $currentGross = max(0.00, $gross - $prevGross);

        $project = $this->project;
        $advanceRate = (float) ($project?->advance_payment_percentage ?? 10.0);
        $retentionRate = (float) ($project?->retention_percentage ?? 5.0);

        $advanceDeduction = round($currentGross * ($advanceRate / 100), 2);
        $retentionDeduction = round($currentGross * ($retentionRate / 100), 2);
        $otherDeductions = (float) $this->other_deductions;

        $netSubtotal = max(0.00, $currentGross - $advanceDeduction - $retentionDeduction - $otherDeductions);
        $taxRate = (float) ($this->tax_rate ?: 15.00);
        $taxAmount = round($netSubtotal * ($taxRate / 100), 2);
        $netAmount = $netSubtotal + $taxAmount;

        $this->update([
            'gross_amount'              => $gross,
            'current_gross_amount'      => $currentGross,
            'advance_payment_deduction' => $advanceDeduction,
            'retention_deduction'       => $retentionDeduction,
            'net_subtotal'              => $netSubtotal,
            'tax_amount'                => $taxAmount,
            'net_amount'                => $netAmount,
        ]);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'مسودة مستخلص (Draft)',
            'submitted' => 'مرفوع للاستشاري (Submitted)',
            'approved'  => 'معتمد للصرف (Approved)',
            'paid'      => 'مسدد بالكامل (Paid)',
            'rejected'  => 'مرفوض ومُعاد (Rejected)',
            default     => 'مسودة',
        };
    }

    public function getExtractTypeLabelAttribute(): string
    {
        return match ($this->extract_type) {
            'advance'  => 'دفعة مقدمة (Advance Payment)',
            'progress' => 'مستخلص جاري (Progress Extract)',
            'final'    => 'مستخلص ختامي نهائي (Final Extract)',
            default    => 'مستخلص جاري',
        };
    }

    /**
     * Generate sequential extract number for project
     */
    public static function generateNumber(Project $project): string
    {
        $count = static::where('project_id', $project->id)->count();
        $seq = $count + 1;
        return 'EXT-' . $project->code . '-' . str_pad($seq, 2, '0', STR_PAD_LEFT);
    }
}
