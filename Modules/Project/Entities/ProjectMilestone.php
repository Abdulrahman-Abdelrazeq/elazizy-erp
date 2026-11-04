<?php

namespace Modules\Project\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMilestone extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'project_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'weight_percentage',
        'completion_percentage',
        'status',
        'deliverables',
    ];

    protected $casts = [
        'start_date'            => 'date',
        'end_date'              => 'date',
        'weight_percentage'     => 'float',
        'completion_percentage' => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'قيد الانتظار (Pending)',
            'in_progress' => 'جاري العمل (In Progress)',
            'completed'   => 'مكتملة ومسلّمة (Completed)',
            'delayed'     => 'متأخرة عن الجدول (Delayed)',
            default       => 'قيد التنفيذ',
        };
    }
}
