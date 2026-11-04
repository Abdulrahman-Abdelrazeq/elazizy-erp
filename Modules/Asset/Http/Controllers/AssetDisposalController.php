<?php

namespace Modules\Asset\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\JournalEntryLine;
use Modules\Asset\Entities\Asset;
use Modules\Asset\Entities\AssetDisposal;
use Modules\Treasury\Entities\Treasury;
use Modules\Treasury\Entities\TreasuryTransaction;

class AssetDisposalController extends Controller
{
    /**
     * List Asset Disposals & Sales
     */
    public function index(Request $request)
    {
        $query = AssetDisposal::with(['asset.category', 'asset.branch', 'treasury', 'journalEntry', 'creator'])->latest();

        if ($request->filled('disposal_type')) {
            $query->where('disposal_type', $request->disposal_type);
        }

        $disposals = $query->paginate(15)->withQueryString();

        $stats = [
            'total_disposals' => AssetDisposal::count(),
            'total_sales_val' => (float) AssetDisposal::where('disposal_type', 'sale')->sum('sale_price'),
            'total_gains'     => (float) AssetDisposal::where('gain_or_loss', '>', 0)->sum('gain_or_loss'),
            'total_losses'    => (float) abs(AssetDisposal::where('gain_or_loss', '<', 0)->sum('gain_or_loss')),
        ];

        return view('asset::disposals.index', compact('disposals', 'stats'));
    }

    /**
     * Show form to dispose / sell Asset
     */
    public function create(Request $request)
    {
        $assets = Asset::whereIn('status', ['active', 'in_maintenance', 'fully_depreciated'])
            ->with(['branch', 'category'])
            ->orderBy('name')
            ->get();

        $treasuries = Treasury::where('is_active', true)->get();
        $selectedAssetId = $request->query('asset_id');

        return view('asset::disposals.create', compact('assets', 'treasuries', 'selectedAssetId'));
    }

    /**
     * Store Asset Disposal / Sale
     */
    public function store(Request $request)
    {
        $tenantId = currentTenant() ? currentTenant()->id : 1;

        $validated = $request->validate([
            'asset_id'      => 'required|exists:assets,id',
            'disposal_type' => 'required|in:sale,scrap,loss',
            'disposal_date' => 'required|date',
            'sale_price'    => 'nullable|numeric|min:0',
            'treasury_id'   => 'nullable|required_if:disposal_type,sale|exists:treasuries,id',
            'buyer_name'    => 'nullable|string|max:150',
            'notes'         => 'nullable|string',
            'post_to_gl'    => 'nullable|boolean',
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);

        if (in_array($asset->status, ['disposed', 'sold'])) {
            return back()->with('error', 'هذا الأصل مستبعد أو مباع مسبقاً.');
        }

        DB::beginTransaction();
        try {
            $salePrice = $validated['disposal_type'] === 'sale' ? (float) ($validated['sale_price'] ?? 0.00) : 0.00;
            $bookValueAtDisposal = (float) $asset->current_book_value;
            $gainOrLoss = $salePrice - $bookValueAtDisposal;

            $disposal = AssetDisposal::create([
                'tenant_id'              => $tenantId,
                'asset_id'               => $asset->id,
                'disposal_type'          => $validated['disposal_type'],
                'disposal_date'          => $validated['disposal_date'],
                'sale_price'             => $salePrice,
                'book_value_at_disposal' => $bookValueAtDisposal,
                'gain_or_loss'           => $gainOrLoss,
                'treasury_id'            => $validated['treasury_id'] ?? null,
                'buyer_name'             => $validated['buyer_name'] ?? null,
                'notes'                  => $validated['notes'] ?? null,
                'created_by'             => Auth::id(),
            ]);

            // Update asset status
            $newStatus = $validated['disposal_type'] === 'sale' ? 'sold' : 'disposed';
            $asset->update([
                'status'             => $newStatus,
                'current_book_value' => 0.00,
            ]);

            // If sale and treasury provided, deposit funds in treasury
            if ($salePrice > 0 && !empty($validated['treasury_id'])) {
                $treasury = Treasury::find($validated['treasury_id']);
                if ($treasury) {
                    $balanceBefore = (float) $treasury->current_balance;
                    $treasury->current_balance = $balanceBefore + $salePrice;
                    $treasury->save();

                    TreasuryTransaction::create([
                        'tenant_id'        => $tenantId,
                        'treasury_id'      => $treasury->id,
                        'type'             => 'in',
                        'amount'           => $salePrice,
                        'balance_before'   => $balanceBefore,
                        'balance_after'    => $balanceBefore + $salePrice,
                        'source_type'      => 'voucher',
                        'source_id'        => $disposal->id,
                        'transaction_date' => $validated['disposal_date'],
                        'description'      => "ثمن بيع أصل ثابت ({$asset->name} - {$asset->asset_number})",
                        'created_by'       => Auth::id(),
                    ]);
                }
            }

            // Accounting Entry if requested
            if ($request->boolean('post_to_gl')) {
                $assetAccount = $asset->category?->asset_account_id;
                $accumAccount = $asset->category?->depreciation_account_id;

                if ($assetAccount && $accumAccount) {
                    $entryNumber = JournalEntry::generateNumber($tenantId);
                    $entry = JournalEntry::create([
                        'tenant_id'    => $tenantId,
                        'branch_id'    => $asset->branch_id,
                        'entry_number' => $entryNumber,
                        'date'         => $validated['disposal_date'],
                        'reference'    => "DISP-{$asset->asset_number}",
                        'description'  => "قيد استبعاد/بيع أصل ثابت: {$asset->name} ({$asset->asset_number})",
                        'total_debit'  => (float) $asset->purchase_cost,
                        'total_credit' => (float) $asset->purchase_cost,
                        'status'       => 'posted',
                        'source_type'  => 'manual',
                        'source_id'    => $disposal->id,
                        'created_by'   => Auth::id(),
                        'posted_by'    => Auth::id(),
                        'posted_at'    => now(),
                    ]);

                    $totalDep = (float) $asset->total_depreciated_amount;
                    if ($totalDep > 0) {
                        JournalEntryLine::create([
                            'tenant_id'        => $tenantId,
                            'journal_entry_id' => $entry->id,
                            'account_id'       => $accumAccount,
                            'debit'            => $totalDep,
                            'credit'           => 0.00,
                            'description'      => "إقفال مجمع إهلاك: {$asset->name}",
                        ]);
                    }

                    JournalEntryLine::create([
                        'tenant_id'        => $tenantId,
                        'journal_entry_id' => $entry->id,
                        'account_id'       => $assetAccount,
                        'debit'            => 0.00,
                        'credit'           => (float) $asset->purchase_cost,
                        'description'      => "إقفال التكلفة التاريخية لأصل مستبعد: {$asset->name}",
                    ]);

                    $disposal->update(['journal_entry_id' => $entry->id]);
                }
            }

            DB::commit();

            return redirect()->route('assets.disposals.index')->with(
                'success',
                "تم تسجيل عملية الاستبعاد/البيع بنجاح للأصل: {$asset->name}. " .
                ($gainOrLoss > 0 ? "أرباح رأسمالية: " . number_format($gainOrLoss, 2) . " ر.س" : ($gainOrLoss < 0 ? "خسائر استبعاد: " . number_format(abs($gainOrLoss), 2) . " ر.س" : "دون أرباح أو خسائر"))
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تسجيل استبعاد الأصل: ' . $e->getMessage());
        }
    }
}
