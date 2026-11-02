<?php

namespace Modules\CRM\Entities;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtSchedule extends Model
{
    use HasFactory, BelongsToTenant;

    protected $table = 'debt_schedules';

    protected $fillable = [
        'tenant_id',
        'client_id',
        'amount',
        'paid_amount',
        'due_date',
        'paid_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Remaining unpaid amount
     */
    public function remainingAmount(): float
    {
        return max(0.00, (float) ($this->amount - $this->paid_amount));
    }
}

