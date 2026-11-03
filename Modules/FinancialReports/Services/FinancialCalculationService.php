<?php

namespace Modules\FinancialReports\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Accounting\Entities\Account;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\JournalEntryLine;
use Modules\Invoice\Entities\Invoice;
use Modules\Invoice\Entities\InvoiceItem;
use Modules\Treasury\Entities\Treasury;

class FinancialCalculationService
{
    /**
     * Compute Income Statement (قائمة الدخل / الأرباح والخسائر)
     */
    public function getIncomeStatement(string $fromDate, string $toDate, ?int $costCenterId = null, ?int $branchId = null): array
    {
        // 1. Revenues (حسابات الإيرادات - كود 4 أو type = revenue)
        $revenueAccounts = Account::where('type', 'revenue')->orderBy('code')->get();
        $revenueLines = [];
        $totalRevenues = 0.00;

        foreach ($revenueAccounts as $acc) {
            $amount = $this->getAccountPeriodNet($acc, $fromDate, $toDate, $costCenterId, $branchId);
            if ($acc->is_parent == false && abs($amount) > 0.001) {
                $revenueLines[] = [
                    'account' => $acc,
                    'amount'  => $amount,
                ];
            }
            if (!$acc->is_parent) {
                $totalRevenues += $amount;
            }
        }

        // 2. Cost of Goods Sold (تكلفة البضاعة المباعة - كود 5101 أو COGS)
        $cogsAccounts = Account::where('type', 'expense')
            ->where(function ($q) {
                $q->where('code', 'like', '5101%')
                  ->orWhere('name', 'like', '%تكلفة البضاعة%')
                  ->orWhere('name', 'like', '%COGS%');
            })->get();

        $cogsLines = [];
        $totalCogs = 0.00;

        foreach ($cogsAccounts as $acc) {
            $amount = $this->getAccountPeriodNet($acc, $fromDate, $toDate, $costCenterId, $branchId);
            if (!$acc->is_parent && abs($amount) > 0.001) {
                $cogsLines[] = [
                    'account' => $acc,
                    'amount'  => $amount,
                ];
            }
            if (!$acc->is_parent) {
                $totalCogs += $amount;
            }
        }

        // Gross Profit (مجمل الربح)
        $grossProfit = $totalRevenues - $totalCogs;
        $grossMarginPercent = $totalRevenues > 0 ? round(($grossProfit / $totalRevenues) * 100, 2) : 0.0;

        // 3. Operating & Administrative Expenses (المصروفات التشغيلية والإدارية)
        $cogsIds = $cogsAccounts->pluck('id')->toArray();
        $expenseAccounts = Account::where('type', 'expense')
            ->whereNotIn('id', $cogsIds)
            ->orderBy('code')
            ->get();

        $operatingExpenseLines = [];
        $totalOperatingExpenses = 0.00;

        foreach ($expenseAccounts as $acc) {
            $amount = $this->getAccountPeriodNet($acc, $fromDate, $toDate, $costCenterId, $branchId);
            if (!$acc->is_parent && abs($amount) > 0.001) {
                $operatingExpenseLines[] = [
                    'account' => $acc,
                    'amount'  => $amount,
                ];
            }
            if (!$acc->is_parent) {
                $totalOperatingExpenses += $amount;
            }
        }

        // Operating Profit (الربح التشغيلي)
        $operatingProfit = $grossProfit - $totalOperatingExpenses;

        // 4. Net Profit / Loss (صافي الربح / الخسارة النهائي)
        $netProfit = $operatingProfit;
        $netMarginPercent = $totalRevenues > 0 ? round(($netProfit / $totalRevenues) * 100, 2) : 0.0;

        return [
            'from_date'                 => $fromDate,
            'to_date'                   => $toDate,
            'cost_center_id'            => $costCenterId,
            'branch_id'                 => $branchId,
            'revenue_lines'             => $revenueLines,
            'total_revenues'            => $totalRevenues,
            'cogs_lines'                => $cogsLines,
            'total_cogs'                => $totalCogs,
            'gross_profit'              => $grossProfit,
            'gross_margin_percent'      => $grossMarginPercent,
            'operating_expense_lines'   => $operatingExpenseLines,
            'total_operating_expenses'  => $totalOperatingExpenses,
            'operating_profit'          => $operatingProfit,
            'net_profit'                => $netProfit,
            'net_margin_percent'        => $netMarginPercent,
            'is_profitable'             => $netProfit >= 0,
        ];
    }

    /**
     * Compute Balance Sheet / Financial Position (قائمة المركز المالي والميزانية العمومية)
     */
    public function getBalanceSheet(string $asOfDate, ?int $branchId = null): array
    {
        // 1. Current Assets (الأصول المتداولة - كود 11)
        $currentAssets = Account::where('type', 'asset')
            ->where('code', 'like', '11%')
            ->where('is_parent', false)
            ->orderBy('code')
            ->get()
            ->map(function ($acc) use ($asOfDate, $branchId) {
                $balance = $this->getAccountCumulativeBalance($acc, $asOfDate, $branchId);
                return ['account' => $acc, 'balance' => $balance];
            })
            ->filter(fn($item) => abs($item['balance']) > 0.001)
            ->values();

        $totalCurrentAssets = (float) $currentAssets->sum('balance');

        // 2. Fixed / Non-Current Assets (الأصول الثابتة - كود 12 أو غيره من الأصول غير المتداولة)
        $fixedAssets = Account::where('type', 'asset')
            ->where('code', 'not like', '11%')
            ->where('is_parent', false)
            ->orderBy('code')
            ->get()
            ->map(function ($acc) use ($asOfDate, $branchId) {
                $balance = $this->getAccountCumulativeBalance($acc, $asOfDate, $branchId);
                return ['account' => $acc, 'balance' => $balance];
            })
            ->filter(fn($item) => abs($item['balance']) > 0.001)
            ->values();

        $totalFixedAssets = (float) $fixedAssets->sum('balance');
        $totalAssets = $totalCurrentAssets + $totalFixedAssets;

        // 3. Current Liabilities (الالتزامات المتداولة - كود 21)
        $currentLiabilities = Account::where('type', 'liability')
            ->where('code', 'like', '21%')
            ->where('is_parent', false)
            ->orderBy('code')
            ->get()
            ->map(function ($acc) use ($asOfDate, $branchId) {
                $balance = $this->getAccountCumulativeBalance($acc, $asOfDate, $branchId);
                return ['account' => $acc, 'balance' => $balance];
            })
            ->filter(fn($item) => abs($item['balance']) > 0.001)
            ->values();

        $totalCurrentLiabilities = (float) $currentLiabilities->sum('balance');

        // 4. Non-Current Liabilities (الالتزامات غير المتداولة طويلة الأجل)
        $nonCurrentLiabilities = Account::where('type', 'liability')
            ->where('code', 'not like', '21%')
            ->where('is_parent', false)
            ->orderBy('code')
            ->get()
            ->map(function ($acc) use ($asOfDate, $branchId) {
                $balance = $this->getAccountCumulativeBalance($acc, $asOfDate, $branchId);
                return ['account' => $acc, 'balance' => $balance];
            })
            ->filter(fn($item) => abs($item['balance']) > 0.001)
            ->values();

        $totalNonCurrentLiabilities = (float) $nonCurrentLiabilities->sum('balance');
        $totalLiabilities = $totalCurrentLiabilities + $totalNonCurrentLiabilities;

        // 5. Equity (حقوق الملكية - كود 3)
        $equityAccounts = Account::where('type', 'equity')
            ->where('is_parent', false)
            ->orderBy('code')
            ->get()
            ->map(function ($acc) use ($asOfDate, $branchId) {
                $balance = $this->getAccountCumulativeBalance($acc, $asOfDate, $branchId);
                return ['account' => $acc, 'balance' => $balance];
            })
            ->filter(fn($item) => abs($item['balance']) > 0.001)
            ->values();

        $baseEquity = (float) $equityAccounts->sum('balance');

        // Period Net Income up to asOfDate (صافي أرباح الفترة المنقولة لحقوق الملكية)
        $startOfYear = Carbon::parse($asOfDate)->startOfYear()->toDateString();
        $periodIncome = $this->getIncomeStatement($startOfYear, $asOfDate, null, $branchId);
        $periodNetProfit = $periodIncome['net_profit'];

        $totalEquity = $baseEquity + $periodNetProfit;
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

        // Balance Check
        $diff = abs($totalAssets - $totalLiabilitiesAndEquity);
        $isBalanced = $diff <= 0.05;

        return [
            'as_of_date'                    => $asOfDate,
            'branch_id'                     => $branchId,
            'current_assets'                => $currentAssets,
            'total_current_assets'          => $totalCurrentAssets,
            'fixed_assets'                  => $fixedAssets,
            'total_fixed_assets'            => $totalFixedAssets,
            'total_assets'                  => $totalAssets,
            'current_liabilities'           => $currentLiabilities,
            'total_current_liabilities'     => $totalCurrentLiabilities,
            'non_current_liabilities'       => $nonCurrentLiabilities,
            'total_non_current_liabilities' => $totalNonCurrentLiabilities,
            'total_liabilities'             => $totalLiabilities,
            'equity_accounts'               => $equityAccounts,
            'base_equity'                   => $baseEquity,
            'period_net_profit'             => $periodNetProfit,
            'total_equity'                  => $totalEquity,
            'total_liabilities_and_equity'  => $totalLiabilitiesAndEquity,
            'diff'                          => $diff,
            'is_balanced'                   => $isBalanced,
        ];
    }

    /**
     * Compute Cash Flow Statement (قائمة التدفقات النقدية)
     */
    public function getCashFlowStatement(string $fromDate, string $toDate, ?int $branchId = null): array
    {
        // 1. Opening Cash & Banks (رصيد النقدية قبل الفترة)
        $cashAccounts = Account::where('type', 'asset')
            ->where('code', 'like', '1101%')
            ->where('is_parent', false)
            ->get();

        $openingCash = 0.00;
        foreach ($cashAccounts as $acc) {
            $priorDate = Carbon::parse($fromDate)->subDay()->toDateString();
            $openingCash += $this->getAccountCumulativeBalance($acc, $priorDate, $branchId);
        }

        // 2. Operating Activities Cash Flow (التدفقات النقدية التشغيلية)
        // Cash Inflows: Collections from customers / cash sales
        $operatingInflow = (float) JournalEntryLine::whereIn('account_id', $cashAccounts->pluck('id'))
            ->where('debit', '>', 0)
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate, $branchId) {
                $q->where('status', 'posted')
                  ->whereDate('date', '>=', $fromDate)
                  ->whereDate('date', '<=', $toDate)
                  ->where('source_type', '!=', 'opening');
                if ($branchId) $q->where('branch_id', $branchId);
            })->sum('debit');

        // Cash Outflows: Operating expenses and supplier payments
        $operatingOutflow = (float) JournalEntryLine::whereIn('account_id', $cashAccounts->pluck('id'))
            ->where('credit', '>', 0)
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate, $branchId) {
                $q->where('status', 'posted')
                  ->whereDate('date', '>=', $fromDate)
                  ->whereDate('date', '<=', $toDate)
                  ->where('source_type', '!=', 'opening');
                if ($branchId) $q->where('branch_id', $branchId);
            })->sum('credit');

        $netOperatingCash = $operatingInflow - $operatingOutflow;

        // 3. Investing Activities Cash Flow (التدفقات النقدية الاستثمارية - شراء أصول ثابتة)
        $fixedAccounts = Account::where('type', 'asset')
            ->where('code', 'like', '12%')
            ->where('is_parent', false)
            ->get();

        $investingOutflow = (float) JournalEntryLine::whereIn('account_id', $fixedAccounts->pluck('id'))
            ->where('debit', '>', 0)
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate, $branchId) {
                $q->where('status', 'posted')
                  ->whereDate('date', '>=', $fromDate)
                  ->whereDate('date', '<=', $toDate)
                  ->where('source_type', '!=', 'opening');
                if ($branchId) $q->where('branch_id', $branchId);
            })->sum('debit');

        $netInvestingCash = -$investingOutflow;

        // 4. Financing Activities (التدفقات التمويلية - زيادة رأس المال، التوزيعات)
        $equityAccounts = Account::where('type', 'equity')->where('is_parent', false)->get();
        $financingInflow = (float) JournalEntryLine::whereIn('account_id', $equityAccounts->pluck('id'))
            ->where('credit', '>', 0)
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate, $branchId) {
                $q->where('status', 'posted')
                  ->whereDate('date', '>=', $fromDate)
                  ->whereDate('date', '<=', $toDate)
                  ->where('source_type', '!=', 'opening');
                if ($branchId) $q->where('branch_id', $branchId);
            })->sum('credit');

        $netFinancingCash = $financingInflow;

        // Net change in cash & bank
        $netChangeInCash = $netOperatingCash + $netInvestingCash + $netFinancingCash;
        $closingCash = $openingCash + $netChangeInCash;

        // Actual Treasury Balance verification
        $actualTreasuryBalance = (float) Treasury::sum('current_balance');

        return [
            'from_date'                 => $fromDate,
            'to_date'                   => $toDate,
            'opening_cash'              => $openingCash,
            'operating_inflow'          => $operatingInflow,
            'operating_outflow'         => $operatingOutflow,
            'net_operating_cash'        => $netOperatingCash,
            'investing_outflow'         => $investingOutflow,
            'net_investing_cash'        => $netInvestingCash,
            'financing_inflow'          => $financingInflow,
            'net_financing_cash'        => $netFinancingCash,
            'net_change_in_cash'        => $netChangeInCash,
            'closing_cash'              => $closingCash,
            'actual_treasury_balance'   => $actualTreasuryBalance,
        ];
    }

    /**
     * Compute Financial Ratios & Performance Indicators (النسب والمؤشرات المالية)
     */
    public function getFinancialRatios(string $fromDate, string $toDate, ?int $branchId = null): array
    {
        $bs = $this->getBalanceSheet($toDate, $branchId);
        $is = $this->getIncomeStatement($fromDate, $toDate, null, $branchId);

        $currentAssets = $bs['total_current_assets'];
        $currentLiabilities = $bs['total_current_liabilities'];
        $totalAssets = $bs['total_assets'];
        $totalLiabilities = $bs['total_liabilities'];
        $totalEquity = $bs['total_equity'];

        $revenues = $is['total_revenues'];
        $grossProfit = $is['gross_profit'];
        $netProfit = $is['net_profit'];

        // Liquidity Ratios
        $currentRatio = $currentLiabilities > 0 ? round($currentAssets / $currentLiabilities, 2) : 0.0;
        $workingCapital = $currentAssets - $currentLiabilities;

        // Inventory value from current assets (code 1103)
        $inventoryVal = 0.00;
        foreach ($bs['current_assets'] as $item) {
            if (str_starts_with($item['account']->code, '1103')) {
                $inventoryVal += $item['balance'];
            }
        }
        $quickRatio = $currentLiabilities > 0 ? round(max(0, $currentAssets - $inventoryVal) / $currentLiabilities, 2) : 0.0;

        // Profitability Ratios
        $grossProfitMargin = $revenues > 0 ? round(($grossProfit / $revenues) * 100, 2) : 0.0;
        $netProfitMargin = $revenues > 0 ? round(($netProfit / $revenues) * 100, 2) : 0.0;
        $returnOnAssets = $totalAssets > 0 ? round(($netProfit / $totalAssets) * 100, 2) : 0.0;
        $returnOnEquity = $totalEquity > 0 ? round(($netProfit / $totalEquity) * 100, 2) : 0.0;

        // Leverage Ratios
        $debtToEquity = $totalEquity > 0 ? round($totalLiabilities / $totalEquity, 2) : 0.0;
        $debtToAssets = $totalAssets > 0 ? round(($totalLiabilities / $totalAssets) * 100, 2) : 0.0;

        return [
            'from_date'          => $fromDate,
            'to_date'            => $toDate,
            'current_ratio'      => $currentRatio,
            'quick_ratio'        => $quickRatio,
            'working_capital'    => $workingCapital,
            'gross_margin'       => $grossProfitMargin,
            'net_margin'         => $netProfitMargin,
            'roa'                => $returnOnAssets,
            'roe'                => $returnOnEquity,
            'debt_to_equity'     => $debtToEquity,
            'debt_to_assets'     => $debtToAssets,
            'total_assets'       => $totalAssets,
            'total_liabilities'  => $totalLiabilities,
            'total_equity'       => $totalEquity,
            'revenues'           => $revenues,
            'net_profit'         => $netProfit,
        ];
    }

    /**
     * Compute Tax & VAT Report (تقرير القيمة المضافة والإقرار الضريبي)
     */
    public function getTaxReport(string $fromDate, string $toDate, ?int $branchId = null): array
    {
        // 1. Output VAT (ضريبة مبيعات)
        $salesQuery = Invoice::where('type', 'sale')
            ->where('status', 'paid')
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate);
        if ($branchId) $salesQuery->where('branch_id', $branchId);

        $salesTaxable = (float) $salesQuery->sum('subtotal');
        $outputVat = (float) $salesQuery->sum('tax_amount');
        $salesTotal = (float) $salesQuery->sum('grand_total');

        // Sales Returns VAT
        $salesReturnsQuery = Invoice::where('type', 'sale_return')
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate);
        if ($branchId) $salesReturnsQuery->where('branch_id', $branchId);

        $salesReturnTaxable = (float) $salesReturnsQuery->sum('subtotal');
        $salesReturnVat = (float) $salesReturnsQuery->sum('tax_amount');

        $netOutputVat = $outputVat - $salesReturnVat;

        // 2. Input VAT (ضريبة مشتريات)
        $purchasesQuery = Invoice::where('type', 'purchase')
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate);
        if ($branchId) $purchasesQuery->where('branch_id', $branchId);

        $purchasesTaxable = (float) $purchasesQuery->sum('subtotal');
        $inputVat = (float) $purchasesQuery->sum('tax_amount');
        $purchasesTotal = (float) $purchasesQuery->sum('grand_total');

        // Purchase Returns VAT
        $purchaseReturnsQuery = Invoice::where('type', 'purchase_return')
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate);
        if ($branchId) $purchaseReturnsQuery->where('branch_id', $branchId);

        $purchaseReturnTaxable = (float) $purchaseReturnsQuery->sum('subtotal');
        $purchaseReturnVat = (float) $purchaseReturnsQuery->sum('tax_amount');

        $netInputVat = $inputVat - $purchaseReturnVat;

        // Net VAT Payable to Tax Authority (صافي الضريبة المستحقة للدولة)
        $netVatPayable = $netOutputVat - $netInputVat;

        return [
            'from_date'             => $fromDate,
            'to_date'               => $toDate,
            'sales_taxable'         => $salesTaxable,
            'output_vat'            => $outputVat,
            'sales_total'           => $salesTotal,
            'sales_return_taxable'  => $salesReturnTaxable,
            'sales_return_vat'      => $salesReturnVat,
            'net_output_vat'        => $netOutputVat,
            'purchases_taxable'     => $purchasesTaxable,
            'input_vat'             => $inputVat,
            'purchases_total'       => $purchasesTotal,
            'purchase_return_taxable'=> $purchaseReturnTaxable,
            'purchase_return_vat'   => $purchaseReturnVat,
            'net_input_vat'         => $netInputVat,
            'net_vat_payable'       => $netVatPayable,
            'is_payable'            => $netVatPayable >= 0,
        ];
    }

    /**
     * Compute Commercial Sales & Profitability Analysis (تحليل المبيعات والأرباح)
     */
    public function getSalesProfitability(string $fromDate, string $toDate, ?int $branchId = null): array
    {
        // Net Sales Invoices
        $invoices = Invoice::with(['items.item', 'client'])
            ->where('type', 'sale')
            ->where('status', 'paid')
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $totalSales = (float) $invoices->sum('subtotal');
        $totalCost = 0.00;

        $productProfits = [];
        $clientProfits = [];

        foreach ($invoices as $inv) {
            $clientId = $inv->client_id ?: 0;
            $clientName = $inv->client?->name ?? 'عميل نقدي / عام';

            if (!isset($clientProfits[$clientId])) {
                $clientProfits[$clientId] = [
                    'name'         => $clientName,
                    'sales'        => 0.00,
                    'cost'         => 0.00,
                    'profit'       => 0.00,
                    'orders_count' => 0,
                ];
            }
            $clientProfits[$clientId]['sales'] += $inv->subtotal;
            $clientProfits[$clientId]['orders_count'] += 1;

            foreach ($inv->items as $item) {
                $costPrice = $item->item?->purchase_price ?? ($item->unit_price * 0.7); // Fallback to 70% if unset
                $itemCost = $costPrice * $item->quantity;
                $itemSales = $item->total;
                $itemProfit = $itemSales - $itemCost;

                $totalCost += $itemCost;
                $clientProfits[$clientId]['cost'] += $itemCost;
                $clientProfits[$clientId]['profit'] += $itemProfit;

                $itemId = $item->item_id;
                $itemName = $item->item?->name ?? 'صنف غير محدد';

                if (!isset($productProfits[$itemId])) {
                    $productProfits[$itemId] = [
                        'name'   => $itemName,
                        'qty'    => 0,
                        'sales'  => 0.00,
                        'cost'   => 0.00,
                        'profit' => 0.00,
                    ];
                }
                $productProfits[$itemId]['qty'] += $item->quantity;
                $productProfits[$itemId]['sales'] += $itemSales;
                $productProfits[$itemId]['cost'] += $itemCost;
                $productProfits[$itemId]['profit'] += $itemProfit;
            }
        }

        $grossProfit = $totalSales - $totalCost;
        $profitMargin = $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 2) : 0.0;

        // Top 5 Profitable Products
        usort($productProfits, fn($a, $b) => $b['profit'] <=> $a['profit']);
        $topProducts = array_slice($productProfits, 0, 5);

        // Top 5 Profitable Clients
        usort($clientProfits, fn($a, $b) => $b['profit'] <=> $a['profit']);
        $topClients = array_slice($clientProfits, 0, 5);

        return [
            'from_date'     => $fromDate,
            'to_date'       => $toDate,
            'total_sales'   => $totalSales,
            'total_cost'    => $totalCost,
            'gross_profit'  => $grossProfit,
            'profit_margin' => $profitMargin,
            'top_products'  => $topProducts,
            'top_clients'   => $topClients,
        ];
    }

    /**
     * Helper: Get net amount of account for a specific period
     */
    protected function getAccountPeriodNet(Account $account, string $fromDate, string $toDate, ?int $costCenterId = null, ?int $branchId = null): float
    {
        $accountIds = [$account->id];
        if ($account->is_parent) {
            $accountIds = array_merge($accountIds, $account->children()->pluck('id')->toArray());
        }

        $linesQuery = JournalEntryLine::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', function ($q) use ($fromDate, $toDate, $branchId) {
                $q->where('status', 'posted')
                  ->whereDate('date', '>=', $fromDate)
                  ->whereDate('date', '<=', $toDate);
                if ($branchId) $q->where('branch_id', $branchId);
            });

        if ($costCenterId) {
            $linesQuery->where('cost_center_id', $costCenterId);
        }

        $debit = (float) (clone $linesQuery)->sum('debit');
        $credit = (float) (clone $linesQuery)->sum('credit');

        if ($account->nature === 'debit') {
            return $debit - $credit;
        }

        return $credit - $debit;
    }

    /**
     * Helper: Get cumulative balance of account up to asOfDate
     */
    protected function getAccountCumulativeBalance(Account $account, string $asOfDate, ?int $branchId = null): float
    {
        $accountIds = [$account->id];
        if ($account->is_parent) {
            $accountIds = array_merge($accountIds, $account->children()->pluck('id')->toArray());
        }

        $debit = (float) JournalEntryLine::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', function ($q) use ($asOfDate, $branchId) {
                $q->where('status', 'posted')
                  ->whereDate('date', '<=', $asOfDate);
                if ($branchId) $q->where('branch_id', $branchId);
            })->sum('debit');

        $credit = (float) JournalEntryLine::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', function ($q) use ($asOfDate, $branchId) {
                $q->where('status', 'posted')
                  ->whereDate('date', '<=', $asOfDate);
                if ($branchId) $q->where('branch_id', $branchId);
            })->sum('credit');

        if ($account->nature === 'debit') {
            return $account->opening_balance + $debit - $credit;
        }

        return $account->opening_balance + $credit - $debit;
    }
}
