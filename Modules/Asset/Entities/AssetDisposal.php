<?php

namespace Modules\Asset\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Treasury\Entities\Treasury;

class AssetDisposal extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'asset_id',
        'journal_entry_id',
        'disposal_type',
        'disposal_date',
        'sale_price',
        'book_value_at_disposal',
        'gain_or_loss',
        'treasury_id',
        'buyer_name',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'disposal_date'          => 'date',
        'sale_price'             => 'float',
        'book_value_at_disposal' => 'float',
        'gain_or_loss'           => 'float',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function treasury(): BelongsTo
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDisposalTypeLabelAttribute(): string
    {
        return match ($this->disposal_type) {
            'sale'  => 'بيع أصل (Asset Sale)',
            'scrap' => 'تكهين وتحويل لخردة (Scrapped)',
            'loss'  => 'إتلاف أو فقدان (Loss / Write-off)',
            default => 'استبعاد',
        };
    }
}
