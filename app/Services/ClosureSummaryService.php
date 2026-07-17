<?php

namespace App\Services;

use App\Support\BranchQuantities;
use Illuminate\Support\Collection;

class ClosureSummaryService
{
    /**
     * Build multi-branch month-end summary tables from sales and distribution rows.
     *
     * @return array{
     *   branches: Collection,
     *   summary: array{qty_sold: float, amt_sold: float, profit: float, qty_available: float},
     *   branch_summaries: list<array{tag: string, name: string, qty_sold: float, amt_sold: float, profit: float}>,
     *   distribution_rows: list<array{item_id: string, name: string, meta: string, quantities: array<string, float>, total: float}>,
     *   distribution_totals: array{quantities: array<string, float>, total: float},
     *   sales_rows: list<array{item_id: string, name: string, meta: string, branches: array<string, array{qty_sold: float, amt_sold: float, qty_rem: float, profit: float}>}>,
     *   sales_totals: array<string, array{qty_sold: float, amt_sold: float, profit: float}>
     * }
     */
    public function build(Collection $salesHistory, Collection $distributions, Collection $items): array
    {
        $branches = BranchQuantities::activeBranches();
        $columnKeys = [];

        foreach ($branches as $branch) {
            $column = BranchQuantities::columnForBranchTag($branch->tag);
            if ($column !== null) {
                $columnKeys[(string) $branch->tag] = $column;
            }
        }

        $branchSummaries = [];
        $salesTotals = [];

        foreach ($branches as $branch) {
            $tag = (string) $branch->tag;
            $branchSales = $salesHistory->filter(fn ($sale) => (string) $sale->user_bv === $tag);

            $branchSummaries[] = [
                'tag' => $tag,
                'name' => $branch->name,
                'qty_sold' => (float) $branchSales->sum('qty'),
                'amt_sold' => (float) $branchSales->sum('tot'),
                'profit' => (float) $branchSales->sum('profits'),
            ];

            $salesTotals[$tag] = [
                'qty_sold' => 0.0,
                'amt_sold' => 0.0,
                'profit' => 0.0,
            ];
        }

        $itemsById = $items->keyBy(fn ($item) => (string) $item->id);

        $distributionRows = $this->buildDistributionRows($distributions, $columnKeys);
        $distributionTotals = [
            'quantities' => array_fill_keys(array_values($columnKeys), 0.0),
            'total' => 0.0,
        ];

        foreach ($distributionRows as $row) {
            foreach ($row['quantities'] as $column => $qty) {
                $distributionTotals['quantities'][$column] = ($distributionTotals['quantities'][$column] ?? 0) + $qty;
            }
            $distributionTotals['total'] += $row['total'];
        }

        $salesRows = [];
        $itemIds = $salesHistory->pluck('item_id')->unique()->values();

        foreach ($itemIds as $itemId) {
            $itemKey = (string) $itemId;
            $item = $itemsById->get($itemKey) ?? $salesHistory->firstWhere('item_id', $itemId)?->item;
            $branchCells = [];

            foreach ($branches as $branch) {
                $tag = (string) $branch->tag;
                $column = $columnKeys[$tag] ?? null;
                $branchSales = $salesHistory->filter(
                    fn ($sale) => (string) $sale->item_id === $itemKey && (string) $sale->user_bv === $tag
                );

                $qtySold = (float) $branchSales->sum('qty');
                $amtSold = (float) $branchSales->sum('tot');
                $profit = (float) $branchSales->sum('profits');
                $qtyRem = $item && $column ? (float) ($item->{$column} ?? 0) : 0.0;

                $branchCells[$tag] = [
                    'qty_sold' => $qtySold,
                    'amt_sold' => $amtSold,
                    'qty_rem' => $qtyRem,
                    'profit' => $profit,
                ];

                $salesTotals[$tag]['qty_sold'] += $qtySold;
                $salesTotals[$tag]['amt_sold'] += $amtSold;
                $salesTotals[$tag]['profit'] += $profit;
            }

            $salesRows[] = [
                'item_id' => $itemKey,
                'name' => $item->name ?? ($salesHistory->firstWhere('item_id', $itemId)->name ?? '—'),
                'meta' => $item
                    ? trim(($item->item_no ?? '').' - '.($item->desc ?? ''), ' -')
                    : '',
                'branches' => $branchCells,
            ];
        }

        return [
            'branches' => $branches,
            'summary' => [
                'qty_sold' => (float) $salesHistory->sum('qty'),
                'amt_sold' => (float) $salesHistory->sum('tot'),
                'profit' => (float) $salesHistory->sum('profits'),
                'qty_available' => (float) $items->sum('qty'),
            ],
            'branch_summaries' => $branchSummaries,
            'distribution_rows' => $distributionRows,
            'distribution_totals' => $distributionTotals,
            'sales_rows' => $salesRows,
            'sales_totals' => $salesTotals,
            'column_keys' => $columnKeys,
        ];
    }

    /**
     * @param  array<string, string>  $columnKeys  tag => qN
     * @return list<array{item_id: string, name: string, meta: string, quantities: array<string, float>, total: float}>
     */
    private function buildDistributionRows(Collection $distributions, array $columnKeys): array
    {
        $grouped = [];

        foreach ($distributions as $distribution) {
            $itemId = (string) $distribution->item_id;

            if (! isset($grouped[$itemId])) {
                $item = $distribution->item;
                $grouped[$itemId] = [
                    'item_id' => $itemId,
                    'name' => $item->name ?? '—',
                    'meta' => $item
                        ? trim(($item->cat ?? '').' - '.($item->desc ?? ''), ' -')
                        : '',
                    'quantities' => array_fill_keys(array_values($columnKeys), 0.0),
                    'total' => 0.0,
                ];
            }

            foreach ($columnKeys as $column) {
                $qty = (float) ($distribution->{$column} ?? 0);
                $grouped[$itemId]['quantities'][$column] += $qty;
                $grouped[$itemId]['total'] += $qty;
            }
        }

        return array_values($grouped);
    }
}
