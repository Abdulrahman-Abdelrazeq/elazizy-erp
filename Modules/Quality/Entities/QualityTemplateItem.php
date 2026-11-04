<?php

namespace Modules\Quality\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QualityTemplateItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'template_id',
        'checkpoint_name',
        'standard_type',
        'expected_boolean',
        'min_value',
        'max_value',
        'target_value',
        'unit_of_measure',
        'inspection_method',
        'is_mandatory',
        'sort_order',
    ];

    protected $casts = [
        'expected_boolean' => 'boolean',
        'is_mandatory'     => 'boolean',
        'min_value'        => 'float',
        'max_value'        => 'float',
        'target_value'     => 'float',
        'sort_order'       => 'integer',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(QualityTemplate::class, 'template_id');
    }

    public function getFormattedCriteriaAttribute(): string
    {
        if ($this->standard_type === 'boolean') {
            return $this->expected_boolean ? 'مطابق / سليم (Pass / True)' : 'غير مطابق (Fail / False)';
        }

        if ($this->standard_type === 'numeric_range') {
            $unit = $this->unit_of_measure ? " {$this->unit_of_measure}" : '';
            if (!is_null($this->min_value) && !is_null($this->max_value)) {
                return "من {$this->min_value} إلى {$this->max_value}{$unit}" . (!is_null($this->target_value) ? " (المستهدف: {$this->target_value})" : '');
            } elseif (!is_null($this->min_value)) {
                return "حد أدنى: {$this->min_value}{$unit}";
            } elseif (!is_null($this->max_value)) {
                return "حد أقصى: {$this->max_value}{$unit}";
            }
            return "مستهدف: {$this->target_value}{$unit}";
        }

        return 'مطابقة المواصفة القياسية المعتمدة';
    }

    /**
     * Check if an observed value satisfies this criteria
     */
    public function isSatisfied(mixed $observedValue): bool
    {
        if ($this->standard_type === 'boolean') {
            $boolVal = filter_var($observedValue, FILTER_VALIDATE_BOOLEAN);
            return $boolVal === $this->expected_boolean;
        }

        if ($this->standard_type === 'numeric_range') {
            if (!is_numeric($observedValue)) {
                return false;
            }
            $num = (float) $observedValue;
            if (!is_null($this->min_value) && $num < $this->min_value) {
                return false;
            }
            if (!is_null($this->max_value) && $num > $this->max_value) {
                return false;
            }
            return true;
        }

        // Text observation
        return !empty(trim((string)$observedValue));
    }
}
