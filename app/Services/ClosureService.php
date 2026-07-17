<?php

namespace App\Services;

use App\Models\Closure;
use App\Models\Item;
use App\Models\SalesHistory;
use App\Models\User;
use App\Models\Wbdistribution;
use App\Support\BranchQuantities;
use DateTime;
use InvalidArgumentException;

class ClosureService
{
    public function __construct(
        private readonly ClosureSummaryService $summaryService
    ) {
    }

    /** Parse a month URL slug (`d-m-Y` or `Y-m-d`) into `Y-m-01`. */
    public function resolveMonthKey(string $monthParam): string
    {
        $parsed = DateTime::createFromFormat('d-m-Y', $monthParam)
            ?: DateTime::createFromFormat('Y-m-d', $monthParam)
            ?: DateTime::createFromFormat('Y-m-01', $monthParam);

        if (! $parsed) {
            throw new InvalidArgumentException('Invalid month selected.');
        }

        return $parsed->format('Y-m-01');
    }

    public function previousMonthKey(string $monthKey): string
    {
        return (new DateTime($monthKey))->modify('-1 month')->format('Y-m-01');
    }

    public function monthLabel(string $monthKey): string
    {
        return date('F, Y', strtotime($monthKey));
    }

    public function currentMonthKey(): string
    {
        return date('Y-m-01');
    }

    public function currentMonthClosure(): ?Closure
    {
        return Closure::where('month', $this->currentMonthKey())->latest()->first();
    }

    public function isMonthOpen(?Closure $closure = null): bool
    {
        $closure ??= $this->currentMonthClosure();

        return $closure !== null && $closure->status === 'open';
    }

    /**
     * Staff may sell/expense only while the current month is open.
     * Administrators always keep access so they can open/close periods.
     */
    public function salesPermitFor(User $user): int
    {
        if ($user->status === 'Administrator') {
            return 1;
        }

        return $this->isMonthOpen() ? 1 : 0;
    }

    public function salesPermitDeniedMessage(): string
    {
        $closure = $this->currentMonthClosure();
        $label = $this->monthLabel($this->currentMonthKey());

        if ($closure && $closure->status === 'closed') {
            return 'Oops..! '.$label.' has been closed. Contact the administrator if you need access.';
        }

        return 'Oops..! Contact the administrator to open '.$label.'.';
    }

    /**
     * Why this month cannot be opened, or null when opening is allowed.
     * Previous month must be closed before a new month can open
     * (except the very first bootstrap when no closures exist yet).
     */
    public function openBlockedReason(string $monthKey): ?string
    {
        $currentMonthKey = date('Y-m-01');

        if ($monthKey < $currentMonthKey) {
            return 'Openings cannot be made for a past month.';
        }

        $existing = Closure::where('month', $monthKey)->latest()->first();

        if ($existing) {
            if ($existing->status === 'open') {
                return $this->monthLabel($monthKey).' is already open.';
            }

            if ($existing->status === 'closed') {
                return $this->monthLabel($monthKey).' is already closed.';
            }
        }

        $previousMonthKey = $this->previousMonthKey($monthKey);
        $previous = Closure::where('month', $previousMonthKey)->latest()->first();
        $previousClosed = $previous !== null && $previous->status === 'closed';

        if (! $previousClosed) {
            // Allow only the first-ever open when there is no closure history.
            if ($previous === null && ! Closure::query()->exists()) {
                return null;
            }

            return 'Close '.$this->monthLabel($previousMonthKey)
                .' before opening '.$this->monthLabel($monthKey).'.';
        }

        return null;
    }

    public function canOpenMonth(string $monthKey): bool
    {
        return $this->openBlockedReason($monthKey) === null;
    }

    public function openMonth(string $monthKey, User $user): Closure
    {
        $blocked = $this->openBlockedReason($monthKey);
        if ($blocked !== null) {
            throw new InvalidArgumentException($blocked);
        }

        $existing = Closure::where('month', $monthKey)->latest()->first();

        if ($existing) {
            $existing->user_id = (string) $user->id;
            $existing->status = 'open';
            $existing->save();

            return $existing->fresh();
        }

        return Closure::create([
            'user_id' => (string) $user->id,
            'month' => $monthKey,
            'status' => 'open',
        ]);
    }

    public function closeMonth(string $monthKey, User $user): Closure
    {
        $closure = Closure::where('month', $monthKey)->latest()->first();

        if (! $closure || $closure->status !== 'open') {
            throw new InvalidArgumentException(
                'Open '.$this->monthLabel($monthKey).' before closing it.'
            );
        }

        $dateTo = date('Y-m-t', strtotime($monthKey));
        $rangeEnd = $dateTo.' 23:59:59';

        $items = Item::where('del', 'no')->get();
        $salesHistory = SalesHistory::with('item')
            ->where('del', 'no')
            ->whereBetween('created_at', [$monthKey, $rangeEnd])
            ->get();
        $distributions = Wbdistribution::with('item')
            ->where('del', 'no')
            ->whereBetween('created_at', [$monthKey, $rangeEnd])
            ->get();

        $summary = $this->summaryService->build($salesHistory, $distributions, $items);

        $closure->user_id = (string) $user->id;
        $closure->month = $monthKey;
        $closure->tot_qty = (string) $summary['summary']['qty_sold'];
        $closure->avl_qty = (string) $summary['summary']['qty_available'];
        $closure->amt_sold = (string) $summary['summary']['amt_sold'];
        $closure->profits = (string) $summary['summary']['profit'];
        $closure->status = 'closed';

        foreach (BranchQuantities::allColumnKeys() as $column) {
            $closure->{$column} = '0';
        }

        foreach ($summary['branch_summaries'] as $branchSummary) {
            $column = BranchQuantities::columnForBranchTag($branchSummary['tag']);
            if ($column !== null) {
                $closure->{$column} = (string) $branchSummary['qty_sold'];
            }
        }

        $closure->save();

        return $closure->fresh();
    }
}
