<?php

namespace Modules\Asset\Entities;

use App\Models\User;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\JournalEntryLine;

class AssetDepreciation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'asset_id',
        'journal_entry_id',
        'depreciation_date',
        'fiscal_year',
        'fiscal_period',
        'depreciation_amount',
        'accumulated_depreciation_after',
        'book_value_after',
        'is_posted',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'depreciation_date'              => 'date',
        'fiscal_year'                    => 'integer',
        'depreciation_amount'            => 'float',
        'accumulated_depreciation_after' => 'float',
        'book_value_after'               => 'float',
        'is_posted'                      => 'boolean',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Post this depreciation entry to General Ledger
     */
    public function postToGeneralLedger(?int $userId = null): ?JournalEntry
    {
        if ($this->is_posted && $this->journal_entry_id) {
            return $this->journalEntry;
        }

        $asset = $this->asset()->with(['category', 'branch'])->first();
        if (!$asset || !$asset->category) {
            return null;
        }

        $tenantId = $this->tenant_id;
        $branchId = $asset->branch_id;
        $amount = (float) $this->depreciation_amount;

        if ($amount <= 0) {
            return null;
        }

        // Debit Account: Depreciation Expense
        $expenseAccountId = $asset->category->depreciation_expense_account_id;
        if (!$expenseAccountId) {
            $fallbackExpense = Account::where('tenant_id', $tenantId)
                ->where(function ($q) {
                    $q->where('code', '5105')
                      ->orWhere('name', 'like', '%إهلاك%')
                      ->orWhere('name', 'like', '%عمومية%');
                })
                ->where('type', 'expense')
                ->first();
            $expenseAccountId = $fallbackExpense?->id;
        }

        // Credit Account: Accumulated Depreciation
        $accumAccountId = $asset->category->depreciation_account_id;
        if (!$accumAccountId) {
            $fallbackAccum = Account::where('tenant_id', $tenantId)
                ->where(function ($q) {
                    $q->where('code', '1209')
                      ->orWhere('code', '1290')
                      ->orWhere('name', 'like', '%مجمع إهلاك%')
                      ->orWhere('name', 'like', '%الأصول الثابتة%');
                })
                ->first();
            $accumAccountId = $fallbackAccum?->id;
        }

        if (!$expenseAccountId || !$accumAccountId) {
            // Cannot post without valid debit/credit accounts
            return null;
        }

        return DB::transaction(function () use ($tenantId, $branchId, $amount, $asset, $expenseAccountId, $accumAccountId, $userId) {
            $entryNumber = JournalEntry::generateNumber($tenantId);

            $entry = JournalEntry::create([
                'tenant_id'    => $tenantId,
                'branch_id'    => $branchId,
                'entry_number' => $entryNumber,
                'date'         => $this->depreciation_date,
                'reference'    => "DEP-{$asset->asset_number}-{$this->fiscal_period}",
                'description'  => "قيد إهلاك أصل ثابت: {$asset->name} ({$asset->asset_number}) للفترة {$this->fiscal_period}",
                'total_debit'  => $amount,
                'total_credit' => $amount,
                'status'       => 'posted',
                'source_type'  => 'manual',
                'source_id'    => $this->id,
                'created_by'   => $userId ?? $this->created_by,
                'posted_by'    => $userId ?? $this->created_by,
                'posted_at'    => now(),
            ]);

            // Line 1: Debit Depreciation Expense
            JournalEntryLine::create([
                'tenant_id'        => $tenantId,
                'journal_entry_id' => $entry->id,
                'account_id'       => $expenseAccountId,
                'cost_center_id'   => $asset->cost_center_id,
                'debit'            => $amount,
                'credit'           => 0.00,
                'description'      => "مصروف إهلاك أصل: {$asset->name}",
            ]);

            // Line 2: Credit Accumulated Depreciation
            JournalEntryLine::create([
                'tenant_id'        => $tenantId,
                'journal_entry_id' => $entry->id,
                'account_id'       => $accumAccountId,
                'cost_center_id'   => $asset->cost_center_id,
                'debit'            => 0.00,
                'credit'           => $amount,
                'description'      => "مجمع إهلاك أصل: {$asset->name}",
            ]);

            $this->update([
                'journal_entry_id' => $entry->id,
                'is_posted'        => true,
            ]);

            return $entry;
        });
    }
}
