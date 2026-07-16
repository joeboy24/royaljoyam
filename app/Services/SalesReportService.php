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

        $salesQuery = Sale::query()->where($saleFilters)->orderByDesc('id');
        $this->applyDateFilter($salesQuery, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, false);

        $sales = (clone $salesQuery)->paginate(10);
        $salesSend = (clone $salesQuery)->get();

        $cash = $this->sumPayMode($saleFilters, Sale::PAY_MODE_CASH, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, false);
        $cheque = $this->sumPayMode($saleFilters, Sale::PAY_MODE_CHEQUE, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, false);
        $momo = $this->sumPayMode($saleFilters, Sale::PAY_MODE_MOBILE_MONEY, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, false);
        $sumDebt = $this->sumPayMode($saleFilters, Sale::PAY_MODE_DEBT, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, false);

        $branchMetrics = $this->buildBranchMetrics(
            $branch,
            $dateMode,
            $dateFrom,
            $dateTo,
            $sessionSalesDate
        );

        $expenses = Expense::query()->where($expenseFilters);
        $this->applyDateFilter($expenses, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, false);
        $expenses = $expenses->get();

        $profitsQuery = SalesHistory::query()->where(['del' => 'no']);
        $this->applyDateFilter(
            $profitsQuery,
            $dateMode,
            $dateFrom,
            $dateTo,
            $sessionSalesDate,
            $dateMode === 'today_default'
        );
        $genProfits = (float) $profitsQuery->sum('profits');

        $gross = $cash + $cheque + $momo + $sumDebt;
        $net = $gross - (float) $expenses->sum('expense_cost');

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
            'paid_debts' => $this->paidDebtsByBranch($salesSend),
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
            $saleFilters = $delvr === 'Del. / Not Delivered'
                ? ['del' => 'no']
                : ['del' => 'no', 'del_status' => $delvr];

            return [$saleFilters, ['del' => 'no'], 'All'];
        }

        $saleFilters = $delvr === 'Del. / Not Delivered'
            ? ['del' => 'no', 'user_bv' => $branch]
            : ['del' => 'no', 'del_status' => $delvr, 'user_bv' => $branch];

        return [
            $saleFilters,
            ['del' => 'no', 'companybranch_id' => $branch],
            $branch,
        ];
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

            $saleMatch = ['del' => 'no', 'user_bv' => $tag];
            $expenseMatch = ['del' => 'no', 'companybranch_id' => $tag];

            $salesQuery = Sale::query()->where($saleMatch);
            $this->applyDateFilter($salesQuery, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $dateMode === 'today_default');

            $profitsQuery = SalesHistory::query()->where($saleMatch);
            $this->applyDateFilter($profitsQuery, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, $dateMode === 'today_default');

            $expenseQuery = Expense::query()->where($expenseMatch);
            $this->applyDateFilter($expenseQuery, $dateMode, $dateFrom, $dateTo, $sessionSalesDate, false);

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
     * @param  Collection<int, Sale>  $sales
     * @return array<int, float>
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
