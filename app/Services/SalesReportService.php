<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SalesHistory;
use App\Models\SalesPayment;
use App\Support\BranchQuantities;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SalesReportService
{
    public const LEGACY_BRANCH_SLOTS = 5;

    /**
     * @param  array{date_from?: ?string, date_to?: ?string, branch?: string, delvr?: string, session_sales_date?: string}  $filters
     * @return array<string, mixed>
     */
    public function build(array $filters): array
    {
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;
        $branch = $filters['branch'] ?? 'All Branches';
        $delvr = $filters['delvr'] ?? 'Del. / Not Delivered';
        $sessionSalesDate = $filters['session_sales_date'] ?? date('Y-m-d');

        [$saleFilters, $expenseFilters, $sessionBranch] = $this->resolveFilters($branch, $delvr);
        $dateMode = $this->resolveDateMode($dateFrom, $dateTo);
        $useSessionSalesDate = $dateMode === 'today_default';

        $salesQuery = Sale::query()->where($saleFilters)->with('user')->orderByDesc('id');
        $this->applyDateFilter($salesQuery, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $useSessionSalesDate);

        $sales = (clone $salesQuery)->paginate(10);
        $salesSend = (clone $salesQuery)->get();

        $cash = $this->sumPayMode($saleFilters, Sale::PAY_MODE_CASH, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $useSessionSalesDate);
        $cheque = $this->sumPayMode($saleFilters, Sale::PAY_MODE_CHEQUE, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $useSessionSalesDate);
        $momo = $this->sumPayMode($saleFilters, Sale::PAY_MODE_MOBILE_MONEY, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $useSessionSalesDate);
        $sumDebt = $this->sumPayMode($saleFilters, Sale::PAY_MODE_DEBT, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $useSessionSalesDate);

        $branchMetrics = $this->buildBranchMetrics(
            $branch,
            $delvr,
            $dateMode,
            $dateFrom,
            $dateTo,
            $sessionSalesDate
        );

        $expenses = Expense::query()->where($expenseFilters);
        $this->applyDateFilter($expenses, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $useSessionSalesDate);
        $expenses = $expenses->get();

        $genProfits = round(array_sum(array_column($branchMetrics, 'profits')), 2);

        $paidDebts = $this->collectedDebtByBranch(
            $branch,
            $dateMode,
            $dateFrom,
            $dateTo,
            $sessionSalesDate
        );
        $collectedDebt = array_sum($paidDebts);

        $gross = $cash + $cheque + $momo + $sumDebt;
        $net = $cash + $cheque + $momo + $collectedDebt - (float) $expenses->sum('expense_cost');

        return [
            'sales' => $sales,
            'sales_send' => $salesSend,
            'expenses' => $expenses,
            'cash' => $cash,
            'cheque' => $cheque,
            'momo' => $momo,
            'sum_dbt' => $sumDebt,
            'gross' => $gross,
            'net' => $net,
            'gen_profits' => $genProfits,
            'branch_metrics' => $branchMetrics,
            'paid_debts' => $paidDebts,
            'collected_debt' => $collectedDebt,
            'session_branch' => $sessionBranch,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ];
    }

    /**
     * Flatten branch metrics into legacy b1..b5 / cash_b1.. keys expected by reportsview.
     *
     * @param  array<string, mixed>  $report
     * @return array<string, mixed>
     */
    public function toLegacyViewData(array $report): array
    {
        $legacy = [
            'cash' => $report['cash'],
            'cheque' => $report['cheque'],
            'momo' => $report['momo'],
            'sum_dbt' => $report['sum_dbt'],
            'gen_profits' => $report['gen_profits'],
            'expenses' => $report['expenses'],
            'pds' => $this->normalizePaidDebts($report['paid_debts']),
        ];

        for ($slot = 1; $slot <= self::LEGACY_BRANCH_SLOTS; $slot++) {
            $metric = $this->metricForTag($report['branch_metrics'], (string) $slot);

            $legacy['b'.$slot] = $metric['sales_total'];
            $legacy['b'.$slot.'_profits'] = $metric['profits'];
            $legacy['exp_b'.$slot] = $metric['expenses'];
            $legacy['cash_b'.$slot] = $metric['cash'];
            $legacy['cheque_b'.$slot] = $metric['cheque'];
            $legacy['momo_b'.$slot] = $metric['momo'];
            $legacy['debt_b'.$slot] = $metric['debt'];
        }

        return $legacy;
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, mixed>, 2: string}
     */
    protected function resolveFilters(string $branch, string $delvr): array
    {
        if ($branch === 'All Branches') {
            $saleFilters = $this->applyDeliveryFilter(['del' => 'no'], $delvr);

            return [$saleFilters, ['del' => 'no'], 'All'];
        }

        $saleFilters = $this->applyDeliveryFilter(
            ['del' => 'no', 'user_bv' => $branch],
            $delvr
        );

        return [
            $saleFilters,
            ['del' => 'no', 'companybranch_id' => $branch],
            $branch,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    protected function applyDeliveryFilter(array $filters, string $delvr): array
    {
        if ($delvr !== 'Del. / Not Delivered') {
            $filters['del_status'] = $delvr;
        }

        return $filters;
    }

    /**
     * @return array<string, mixed>
     */
    protected function branchSaleMatch(string $tag, string $delvr): array
    {
        return $this->applyDeliveryFilter(['del' => 'no', 'user_bv' => $tag], $delvr);
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    protected function applySaleDeliveryFilter(Builder $query, string $delvr): void
    {
        if ($delvr === 'Del. / Not Delivered') {
            return;
        }

        $query->whereIn('sale_id', Sale::query()
            ->where('del', 'no')
            ->where('del_status', $delvr)
            ->select('id'));
    }

    protected function resolveDateMode(?string $dateFrom, ?string $dateTo): string
    {
        if (! empty($dateFrom) && empty($dateTo)) {
            return 'single_date';
        }

        if (! empty($dateFrom) && ! empty($dateTo)) {
            return 'date_range';
        }

        return 'today_default';
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    protected function applyDateFilter(
        Builder $query,
        string $dateMode,
        ?string $dateFrom,
        ?string $dateTo,
        string $sessionSalesDate,
        bool $useSessionSalesDate
    ): void {
        if ($dateMode === 'single_date') {
            $query->where('created_at', 'LIKE', '%'.$dateFrom.'%');

            return;
        }

        if ($dateMode === 'date_range') {
            $query->whereBetween('created_at', [$dateFrom, new DateTime($dateTo.'+1 day')]);

            return;
        }

        $date = $useSessionSalesDate ? $sessionSalesDate : date('Y-m-d');
        $query->where('created_at', 'LIKE', '%'.$date.'%');
    }

    /**
     * @param  array<string, mixed>  $saleFilters
     */
    protected function sumPayMode(
        array $saleFilters,
        string $payMode,
        string $dateMode,
        ?string $dateFrom,
        ?string $dateTo,
        string $sessionSalesDate,
        bool $useSessionSalesDate
    ): float {
        $query = Sale::query()->where($saleFilters)->matchingPayMode($payMode);
        $this->applyDateFilter($query, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $useSessionSalesDate);

        return (float) $query->sum('tot');
    }

    /**
     * @return list<array{tag: string, sales_total: float, cash: float, cheque: float, momo: float, debt: float, expenses: float, profits: float}>
     */
    protected function buildBranchMetrics(
        string $selectedBranch,
        string $delvr,
        string $dateMode,
        ?string $dateFrom,
        ?string $dateTo,
        string $sessionSalesDate
    ): array {
        $metrics = [];

        foreach (BranchQuantities::activeBranches() as $branch) {
            $tag = (string) $branch->tag;

            if (! $this->shouldIncludeBranch($selectedBranch, $tag)) {
                $metrics[] = $this->emptyBranchMetric($tag);

                continue;
            }

            $saleMatch = $this->branchSaleMatch($tag, $delvr);
            $expenseMatch = ['del' => 'no', 'companybranch_id' => $tag];

            $salesQuery = Sale::query()->where($saleMatch);
            $this->applyDateFilter($salesQuery, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $dateMode === 'today_default');

            $profitsQuery = SalesHistory::query()
                ->where('del', 'no')
                ->where('user_bv', $tag);
            $this->applySaleDeliveryFilter($profitsQuery, $delvr);
            $this->applyDateFilter($profitsQuery, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $dateMode === 'today_default');

            $expenseQuery = Expense::query()->where($expenseMatch);
            $this->applyDateFilter($expenseQuery, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $dateMode === 'today_default');

            $metrics[] = [
                'tag' => $tag,
                'sales_total' => (float) (clone $salesQuery)->sum('tot'),
                'cash' => $this->sumBranchPayMode($saleMatch, Sale::PAY_MODE_CASH, $dateMode, $dateFrom, $dateTo, $sessionSalesDate),
                'cheque' => $this->sumBranchPayMode($saleMatch, Sale::PAY_MODE_CHEQUE, $dateMode, $dateFrom, $dateTo, $sessionSalesDate),
                'momo' => $this->sumBranchPayMode($saleMatch, Sale::PAY_MODE_MOBILE_MONEY, $dateMode, $dateFrom, $dateTo, $sessionSalesDate),
                'debt' => $this->sumBranchPayMode($saleMatch, Sale::PAY_MODE_DEBT, $dateMode, $dateFrom, $dateTo, $sessionSalesDate),
                'expenses' => (float) (clone $expenseQuery)->sum('expense_cost'),
                'profits' => (float) (clone $profitsQuery)->sum('profits'),
            ];
        }

        return $metrics;
    }

    /**
     * @param  array<string, mixed>  $saleMatch
     */
    protected function sumBranchPayMode(
        array $saleMatch,
        string $payMode,
        string $dateMode,
        ?string $dateFrom,
        ?string $dateTo,
        string $sessionSalesDate
    ): float {
        $query = Sale::query()->where($saleMatch)->matchingPayMode($payMode);
        $this->applyDateFilter($query, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $dateMode === 'today_default');

        return (float) $query->sum('tot');
    }

    protected function shouldIncludeBranch(string $selectedBranch, string $tag): bool
    {
        if ($selectedBranch === 'All Branches') {
            return true;
        }

        return (string) $selectedBranch === $tag;
    }

    /**
     * @return array{tag: string, sales_total: float, cash: float, cheque: float, momo: float, debt: float, expenses: float, profits: float}
     */
    protected function emptyBranchMetric(string $tag): array
    {
        return [
            'tag' => $tag,
            'sales_total' => 0.0,
            'cash' => 0.0,
            'cheque' => 0.0,
            'momo' => 0.0,
            'debt' => 0.0,
            'expenses' => 0.0,
            'profits' => 0.0,
        ];
    }

    /**
     * @param  list<array{tag: string, sales_total: float, cash: float, cheque: float, momo: float, debt: float, expenses: float, profits: float}>  $metrics
     * @return array{tag: string, sales_total: float, cash: float, cheque: float, momo: float, debt: float, expenses: float, profits: float}
     */
    protected function metricForTag(array $metrics, string $tag): array
    {
        foreach ($metrics as $metric) {
            if ($metric['tag'] === $tag) {
                return $metric;
            }
        }

        return $this->emptyBranchMetric($tag);
    }

    /**
     * Debt cash collected per branch for the report period (matches daily sales page).
     * Uses payment date, not sale date, so collections on older orders still count.
     *
     * @return array<int|string, float>
     */
    public function collectedDebtByBranch(
        string $selectedBranch,
        string $dateMode,
        ?string $dateFrom,
        ?string $dateTo,
        string $sessionSalesDate
    ): array {
        $totals = [];
        $useSessionSalesDate = $dateMode === 'today_default';

        $paymentsQuery = SalesPayment::query()
            ->where('del', 'no')
            ->whereHas('sale', function (Builder $query) use ($selectedBranch) {
                $query->where('del', 'no');

                if ($selectedBranch !== 'All Branches') {
                    $query->where('user_bv', $selectedBranch);
                }
            });

        $this->applyDateFilter(
            $paymentsQuery,
            $dateMode,
            $dateFrom,
            $dateTo,
            $sessionSalesDate,
            $useSessionSalesDate
        );

        foreach ($paymentsQuery->with('sale')->get() as $payment) {
            $sale = $payment->sale;

            if (! $sale) {
                continue;
            }

            $tag = (string) $sale->user_bv;
            $totals[$tag] = ($totals[$tag] ?? 0.0) + (float) $payment->amt_paid;
        }

        $checkoutDebtQuery = Sale::query()
            ->where('del', 'no')
            ->matchingPayMode(Sale::PAY_MODE_DEBT)
            ->whereDoesntHave('salespayment', function (Builder $query) {
                $query->where('del', 'no');
            });

        if ($selectedBranch !== 'All Branches') {
            $checkoutDebtQuery->where('user_bv', $selectedBranch);
        }

        $this->applyDateFilter(
            $checkoutDebtQuery,
            $dateMode,
            $dateFrom,
            $dateTo,
            $sessionSalesDate,
            $useSessionSalesDate
        );

        foreach ($checkoutDebtQuery->get() as $sale) {
            $payment = (float) $sale->payment;

            if ($payment <= 0) {
                continue;
            }

            $tag = (string) $sale->user_bv;
            $totals[$tag] = ($totals[$tag] ?? 0.0) + $payment;
        }

        return $totals;
    }

    /**
     * @param  Collection<int, Sale>  $sales
     * @return array<int|string, float>
     *
     * @deprecated Prefer collectedDebtByBranch(); kept for legacy callers.
     */
    public function paidDebtsByBranch(Collection $sales): array
    {
        $totals = [];

        if ($sales->isEmpty()) {
            return $totals;
        }

        $salesById = $sales->keyBy('id');

        $payments = SalesPayment::where('del', 'no')
            ->whereIn('sale_id', $salesById->keys())
            ->get();

        foreach ($payments as $payment) {
            $sale = $salesById->get($payment->sale_id);

            if (! $sale) {
                continue;
            }

            $tag = (string) $sale->user_bv;
            $totals[$tag] = ($totals[$tag] ?? 0.0) + (float) $payment->amt_paid;
        }

        return $totals;
    }

    /**
     * Structured rows for the sales report breakdown modal.
     *
     * @param  array<string, mixed>  $report
     * @return array{columns: list<array{slot: int, metric: array<string, float|string>, paid_debt: float, net: float}>, rows: list<array<string, mixed>>, net_total: float}
     */
    public function buildBreakdownTable(array $report): array
    {
        $paidDebts = $this->normalizePaidDebts($report['paid_debts']);
        $expenseTotal = (float) $report['expenses']->sum('expense_cost');

        $columns = [];

        for ($slot = 1; $slot <= self::LEGACY_BRANCH_SLOTS; $slot++) {
            $metric = $this->metricForTag($report['branch_metrics'], (string) $slot);
            $paidDebt = $paidDebts[$slot - 1];

            $columns[] = [
                'slot' => $slot,
                'metric' => $metric,
                'paid_debt' => $paidDebt,
                'net' => $this->branchNetTotal($metric, $paidDebt),
                'cash_at_hand' => $this->branchCashAtHand($metric, $paidDebt),
            ];
        }

        $cashAtHandValues = array_column($columns, 'cash_at_hand');

        $columnValues = fn (string $field): array => array_map(
            fn (array $column) => (float) $column['metric'][$field],
            $columns
        );

        $rows = [
            [
                'key' => 'cash',
                'label' => 'Cash',
                'kind' => 'payment',
                'values' => $columnValues('cash'),
                'total' => (float) $report['cash'],
            ],
            [
                'key' => 'cheque',
                'label' => 'Cheque',
                'kind' => 'payment',
                'values' => $columnValues('cheque'),
                'total' => (float) $report['cheque'],
            ],
            [
                'key' => 'momo',
                'label' => 'Mobile money',
                'kind' => 'payment',
                'values' => $columnValues('momo'),
                'total' => (float) $report['momo'],
            ],
            [
                'key' => 'debt',
                'label' => 'Post payment (debt)',
                'kind' => 'payment',
                'values' => $columnValues('debt'),
                'total' => (float) $report['sum_dbt'],
                'subrow' => [
                    'label' => 'Paid debts collected',
                    'values' => $paidDebts,
                    'total' => array_sum($paidDebts),
                ],
            ],
            [
                'key' => 'expenses',
                'label' => 'Expenditure',
                'kind' => 'expense',
                'values' => $columnValues('expenses'),
                'total' => $expenseTotal,
            ],
            [
                'key' => 'profits',
                'label' => 'Profits (margin)',
                'kind' => 'profit',
                'values' => $columnValues('profits'),
                'total' => round(array_sum($columnValues('profits')), 2),
            ],
            [
                'key' => 'cash_at_hand',
                'label' => 'Cash in drawer (est.)',
                'kind' => 'cash-hand',
                'values' => $cashAtHandValues,
                'total' => round(array_sum($cashAtHandValues), 2),
            ],
        ];

        return [
            'columns' => $columns,
            'rows' => $rows,
            'net_total' => round(
                (float) $report['cash']
                + (float) $report['cheque']
                + (float) $report['momo']
                + (float) ($report['collected_debt'] ?? array_sum($paidDebts))
                - $expenseTotal,
                2
            ),
        ];
    }

    /**
     * Money collected minus expenditure (matches daily sales net total).
     *
     * @param  array<string, float|string>  $metric
     */
    public function branchNetTotal(array $metric, float $paidDebt): float
    {
        return round(
            (float) $metric['cash']
            + (float) $metric['cheque']
            + (float) $metric['momo']
            + $paidDebt
            - (float) $metric['expenses'],
            2
        );
    }

    /**
     * Estimated drawer cash: cash sales + debt collections − expenditure.
     * Assumes debt payments and expenses were paid in cash.
     *
     * @param  array<string, float|string>  $metric
     */
    public function branchCashAtHand(array $metric, float $paidDebt): float
    {
        return round(
            (float) $metric['cash']
            + $paidDebt
            - (float) $metric['expenses'],
            2
        );
    }

    /**
     * @param  array<int|string, float>  $paidDebts
     * @return array<int, float>
     */
    protected function normalizePaidDebts(array $paidDebts): array
    {
        $normalized = [];

        for ($slot = 1; $slot <= self::LEGACY_BRANCH_SLOTS; $slot++) {
            $normalized[] = (float) ($paidDebts[(string) $slot] ?? $paidDebts[$slot] ?? 0.0);
        }

        return $normalized;
    }
}
