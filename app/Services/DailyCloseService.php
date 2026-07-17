<?php

namespace App\Services;

use App\Models\DailyClosure;
use App\Models\Expense;
use App\Models\Sale;
use App\Models\SalesPayment;
use App\Models\User;
use App\Support\BranchQuantities;
use InvalidArgumentException;

class DailyCloseService
{
    public const AUTO_CLOSE_NOTE = 'System auto-closed — assumed cash from recorded sales (no till count submitted).';

    public function scopeKeyFor(User $user): string
    {
        return $user->status === 'Administrator'
            ? 'admin'
            : 'bv:'.(string) $user->bv;
    }

    public function branchLabelFor(User $user): string
    {
        if ($user->status === 'Administrator') {
            return 'All branches';
        }

        return (string) (session('branch_'.$user->bv) ?: ('Branch '.$user->bv));
    }

    public function findForUser(User $user, string $closeDate): ?DailyClosure
    {
        return DailyClosure::query()
            ->where('del', 'no')
            ->where('close_date', $closeDate)
            ->where('scope_key', $this->scopeKeyFor($user))
            ->latest('id')
            ->first();
    }

    /**
     * @return array{
     *   cash: float,
     *   cheque: float,
     *   momo: float,
     *   debt_sold: float,
     *   collected_debt: float,
     *   expenses: float,
     *   gross_collected: float,
     *   net_total: float,
     *   sales_ex_debt: float
     * }
     */
    public function summarizeForUser(User $user, string $closeDate): array
    {
        if ($user->status === 'Administrator') {
            $field = 'del';
            $uidHold = 'no';
            $debts = SalesPayment::where('del', 'no')
                ->where('created_at', 'LIKE', '%'.$closeDate.'%')
                ->get();
        } else {
            $field = 'user_id';
            $uidHold = $user->id;
            $debts = SalesPayment::where('user_id', $uidHold)
                ->where('del', 'no')
                ->where('created_at', 'LIKE', '%'.$closeDate.'%')
                ->get();
        }

        // Match sales-page KPI scope: expenses for the acting user only.
        $expensesQuery = Expense::where('user_id', $user->id)
            ->where('created_at', 'LIKE', '%'.$closeDate.'%');

        $uidMatch = [$field => $uidHold];
        $sales = Sale::where($uidMatch)->where('created_at', 'LIKE', '%'.$closeDate.'%')->get();

        $cash = (float) Sale::where($uidMatch + ['pay_mode' => Sale::PAY_MODE_CASH])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->sum('tot');
        $cheque = (float) Sale::where($uidMatch + ['pay_mode' => Sale::PAY_MODE_CHEQUE])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->sum('tot');
        $momo = (float) Sale::where($uidMatch + ['pay_mode' => Sale::PAY_MODE_MOBILE_MONEY])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->sum('tot');
        $debtSold = (float) Sale::where($uidMatch + ['pay_mode' => Sale::PAY_MODE_DEBT])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->sum('tot');

        $debtsPaid = (float) $debts->sum('amt_paid');
        $debtCheckoutWithoutPaymentRow = (float) Sale::where($uidMatch + ['pay_mode' => Sale::PAY_MODE_DEBT])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->whereDoesntHave('salespayment', function ($query) {
                $query->where('del', 'no');
            })
            ->sum('payment');
        $collectedDebt = $debtsPaid + $debtCheckoutWithoutPaymentRow;
        $expenses = (float) $expensesQuery->sum('expense_cost');
        $salesExDebt = (float) $sales->sum('tot') - $debtSold;
        $grossCollected = $cash + $cheque + $momo + $collectedDebt;
        $netTotal = $grossCollected - $expenses;

        return [
            'cash' => $cash,
            'cheque' => $cheque,
            'momo' => $momo,
            'debt_sold' => $debtSold,
            'collected_debt' => $collectedDebt,
            'expenses' => $expenses,
            'gross_collected' => $grossCollected,
            'net_total' => $netTotal,
            'sales_ex_debt' => $salesExDebt,
        ];
    }

    /**
     * Per-branch EOD figures for a sales date (live from sales/expenses).
     *
     * @return list<array{
     *   tag: string,
     *   name: string,
     *   cash: float,
     *   cheque: float,
     *   momo: float,
     *   debt_sold: float,
     *   collected_debt: float,
     *   expenses: float,
     *   gross_collected: float,
     *   net_total: float,
     *   variance: float|null,
     *   closed: bool
     * }>
     */
    public function summarizeBranchesForDate(string $closeDate): array
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $closeDate)) {
            return [];
        }

        $storedByScope = DailyClosure::query()
            ->where('del', 'no')
            ->where('close_date', $closeDate)
            ->where('scope_key', 'like', 'bv:%')
            ->orderByDesc('id')
            ->get()
            ->unique('scope_key')
            ->keyBy('scope_key');

        $rows = [];

        foreach (BranchQuantities::activeBranches() as $branch) {
            $tag = (string) $branch->tag;
            $summary = $this->summarizeForBranchTag($tag, $closeDate);
            $stored = $storedByScope->get('bv:'.$tag);

            $rows[] = [
                'tag' => $tag,
                'name' => (string) $branch->name,
                'cash' => $summary['cash'],
                'cheque' => $summary['cheque'],
                'momo' => $summary['momo'],
                'debt_sold' => $summary['debt_sold'],
                'collected_debt' => $summary['collected_debt'],
                'expenses' => $summary['expenses'],
                'gross_collected' => $summary['gross_collected'],
                'net_total' => $summary['net_total'],
                'variance' => $stored?->variance !== null ? (float) $stored->variance : null,
                'closed' => $stored !== null,
            ];
        }

        return $rows;
    }

    /**
     * @return array{
     *   cash: float,
     *   cheque: float,
     *   momo: float,
     *   debt_sold: float,
     *   collected_debt: float,
     *   expenses: float,
     *   gross_collected: float,
     *   net_total: float
     * }
     */
    public function summarizeForBranchTag(string $branchTag, string $closeDate): array
    {
        $saleMatch = ['del' => 'no', 'user_bv' => $branchTag];

        $cash = (float) Sale::where($saleMatch + ['pay_mode' => Sale::PAY_MODE_CASH])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->sum('tot');
        $cheque = (float) Sale::where($saleMatch + ['pay_mode' => Sale::PAY_MODE_CHEQUE])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->sum('tot');
        $momo = (float) Sale::where($saleMatch + ['pay_mode' => Sale::PAY_MODE_MOBILE_MONEY])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->sum('tot');
        $debtSold = (float) Sale::where($saleMatch + ['pay_mode' => Sale::PAY_MODE_DEBT])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->sum('tot');

        $debtsPaid = (float) SalesPayment::query()
            ->where('del', 'no')
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->whereHas('sale', function ($query) use ($branchTag) {
                $query->where('del', 'no')->where('user_bv', $branchTag);
            })
            ->sum('amt_paid');

        $debtCheckoutWithoutPaymentRow = (float) Sale::where($saleMatch + ['pay_mode' => Sale::PAY_MODE_DEBT])
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->whereDoesntHave('salespayment', function ($query) {
                $query->where('del', 'no');
            })
            ->sum('payment');

        $collectedDebt = $debtsPaid + $debtCheckoutWithoutPaymentRow;
        $expenses = (float) Expense::query()
            ->where('del', 'no')
            ->where('companybranch_id', $branchTag)
            ->where('created_at', 'LIKE', '%'.$closeDate.'%')
            ->sum('expense_cost');
        $grossCollected = $cash + $cheque + $momo + $collectedDebt;
        $netTotal = $grossCollected - $expenses;

        return [
            'cash' => $cash,
            'cheque' => $cheque,
            'momo' => $momo,
            'debt_sold' => $debtSold,
            'collected_debt' => $collectedDebt,
            'expenses' => $expenses,
            'gross_collected' => $grossCollected,
            'net_total' => $netTotal,
        ];
    }

    public function closeDay(
        User $user,
        string $closeDate,
        ?float $countedCash = null,
        ?string $notes = null
    ): DailyClosure {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $closeDate)) {
            throw new InvalidArgumentException('Invalid close date.');
        }

        if ($this->findForUser($user, $closeDate)) {
            throw new InvalidArgumentException(
                'Day '.$closeDate.' is already closed for '.$this->branchLabelFor($user).'.'
            );
        }

        $summary = $this->summarizeForUser($user, $closeDate);
        $variance = $countedCash === null
            ? null
            : round($countedCash - $summary['cash'], 2);

        return DailyClosure::create([
            'user_id' => (string) $user->id,
            'scope_key' => $this->scopeKeyFor($user),
            'branch_label' => $this->branchLabelFor($user),
            'close_date' => $closeDate,
            'cash' => (string) $summary['cash'],
            'cheque' => (string) $summary['cheque'],
            'momo' => (string) $summary['momo'],
            'debt_sold' => (string) $summary['debt_sold'],
            'collected_debt' => (string) $summary['collected_debt'],
            'expenses' => (string) $summary['expenses'],
            'gross_collected' => (string) $summary['gross_collected'],
            'net_total' => (string) $summary['net_total'],
            'counted_cash' => $countedCash === null ? null : (string) $countedCash,
            'variance' => $variance === null ? null : (string) $variance,
            'notes' => $notes ? trim($notes) : null,
            'status' => 'closed',
            'del' => 'no',
        ]);
    }

    /**
     * Close a past day that staff never closed manually.
     * Uses DB cash as assumed till count (variance 0) and a system note.
     */
    public function autoCloseDay(User $user, string $closeDate): ?DailyClosure
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $closeDate)) {
            return null;
        }

        if ($closeDate >= date('Y-m-d')) {
            return null;
        }

        if ($this->findForUser($user, $closeDate)) {
            return null;
        }

        $summary = $this->summarizeForUser($user, $closeDate);
        if (! $this->dayHasActivity($summary)) {
            return null;
        }

        return $this->closeDay(
            $user,
            $closeDate,
            $summary['cash'],
            self::AUTO_CLOSE_NOTE
        );
    }

    /**
     * Auto-close recent past days still open for this user's scope.
     *
     * @return list<DailyClosure>
     */
    public function autoClosePastDays(User $user, int $lookbackDays = 14): array
    {
        $lookbackDays = max(1, min(60, $lookbackDays));
        $closed = [];
        $today = strtotime(date('Y-m-d'));

        for ($i = 1; $i <= $lookbackDays; $i++) {
            $date = date('Y-m-d', strtotime('-'.$i.' days', $today));
            $result = $this->autoCloseDay($user, $date);
            if ($result) {
                $closed[] = $result;
            }
        }

        return $closed;
    }

    /**
     * @param  array{
     *   cash: float,
     *   cheque: float,
     *   momo: float,
     *   debt_sold: float,
     *   collected_debt: float,
     *   expenses: float,
     *   gross_collected: float,
     *   net_total: float,
     *   sales_ex_debt: float
     * }  $summary
     */
    private function dayHasActivity(array $summary): bool
    {
        return $summary['gross_collected'] > 0
            || $summary['debt_sold'] > 0
            || $summary['expenses'] > 0;
    }
}
