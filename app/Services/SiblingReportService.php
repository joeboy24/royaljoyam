<?php

namespace App\Services;

use App\Models\BranchTransfer;
use App\Models\CompanyBranch;
use App\Models\Expense;
use App\Models\OrderReturn;
use App\Models\Sale;
use App\Models\SalesPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class SiblingReportService
{
    public function expensesQuery(Request $request): Builder
    {
        $branch = $request->input('branch');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if ($branch === null || $branch === '' || $branch == 'All Branches') {
            $match = ['del' => 'no'];
        } else {
            $match = ['del' => 'no', 'companybranch_id' => $branch];
        }

        $query = Expense::where($match);

        if (! empty($dateFrom) && empty($dateTo)) {
            $query->where('created_at', 'LIKE', '%'.$dateFrom.'%');
        } elseif (! empty($dateFrom) && ! empty($dateTo)) {
            $query->whereBetween('created_at', [$dateFrom, new \DateTime($dateTo.'+1 day')]);
        } else {
            $today = session('date_today') ?: date('Y-m-d');
            $query->where('created_at', 'LIKE', '%'.$today.'%');
        }

        return $query->orderBy('id', 'desc');
    }

    public function debtsQuery(Request $request): Builder
    {
        $branch = $request->input('branch');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $debtsearch = trim((string) $request->query('debtsearch', ''));
        $debtStatus = $request->query('debt_status', 'outstanding');

        $match = ['del' => 'no', 'pay_mode' => Sale::PAY_MODE_DEBT];

        if ($branch !== null && $branch !== '' && $branch !== 'All Branches') {
            $match['user_bv'] = $branch;
        }

        if ($debtStatus === 'cleared') {
            $match['paid'] = 'Paid';
        } elseif ($debtStatus === 'outstanding') {
            $match['paid'] = 'no';
        }

        $query = Sale::with('user')->where($match)->reportSearch($debtsearch);

        if (! empty($dateFrom) && empty($dateTo)) {
            $query->where('created_at', 'LIKE', '%'.$dateFrom.'%');
        } elseif (! empty($dateFrom) && ! empty($dateTo)) {
            $query->whereBetween('created_at', [$dateFrom, new \DateTime($dateTo.'+1 day')]);
        }

        return $query->orderBy('id', 'desc');
    }

    public function returnsQuery(Request $request): Builder
    {
        $branch = $request->query('branch');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $returnsearch = trim((string) $request->query('returnsearch', ''));

        if ($branch === null || $branch === '' || $branch == 'All Branches') {
            $match = ['del' => 'no'];
        } else {
            $match = ['del' => 'no', 'user_bv' => $branch];
        }

        $query = OrderReturn::with('user')->where($match)->reportSearch($returnsearch);

        if (! empty($dateFrom) && empty($dateTo)) {
            $query->where('created_at', 'LIKE', '%'.$dateFrom.'%');
        } elseif (! empty($dateFrom) && ! empty($dateTo)) {
            $query->whereBetween('created_at', [$dateFrom, new \DateTime($dateTo.'+1 day')]);
        }

        return $query->orderBy('id', 'desc');
    }

    public function paidDebtsContext(Request $request, User $user): array
    {
        $isAdmin = $user->status === 'Administrator';
        $salesDate = session('date_today') ?: now()->format('Y-m-d');
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));
        $search = trim((string) $request->query('paiddebtsearch', ''));

        if ($isAdmin) {
            $branch = $request->query('branch', 'All Branches');
        } else {
            $branch = (string) $user->bv;
        }

        if ($dateFrom === '' && $dateTo === '') {
            if ($isAdmin) {
                $dateFrom = date('Y-m-01', strtotime($salesDate));
                $dateTo = date('Y-m-t', strtotime($salesDate));
            } else {
                $dateFrom = $salesDate;
                $dateTo = $salesDate;
            }
        }

        $query = SalesPayment::with(['sale.user', 'user'])->where('del', 'no');

        if ($branch !== null && $branch !== '' && $branch !== 'All Branches') {
            $query->whereHas('sale', function ($saleQuery) use ($branch) {
                $saleQuery->where('user_bv', $branch);
            });
        }

        if ($dateFrom !== '' && $dateTo === '') {
            $query->where('created_at', 'LIKE', '%'.$dateFrom.'%');
        } elseif ($dateFrom !== '' && $dateTo !== '') {
            $query->whereBetween('created_at', [$dateFrom, new \DateTime($dateTo.'+1 day')]);
        }

        $query->paidDebtSearch($search);

        return [
            'query' => $query->orderByDesc('id'),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'branch' => $branch,
            'branchName' => $this->paidDebtsBranchName($branch, $isAdmin, $user),
            'search' => $search,
            'periodLabel' => $this->paidDebtsPeriodLabel($dateFrom, $dateTo),
            'isAdmin' => $isAdmin,
        ];
    }

    protected function paidDebtsBranchName(string $branch, bool $isAdmin, User $user): string
    {
        if ($isAdmin && ($branch === '' || $branch === 'All Branches')) {
            return 'All Branches';
        }

        $record = CompanyBranch::query()
            ->where('tag', $branch)
            ->first();

        return $record?->name ?? ($isAdmin ? 'Branch '.$branch : $user->status);
    }

    protected function paidDebtsPeriodLabel(string $dateFrom, string $dateTo): string
    {
        if ($dateFrom === $dateTo) {
            return \Carbon\Carbon::parse($dateFrom)->format('D, d M Y');
        }

        $monthStart = date('Y-m-01', strtotime($dateFrom));
        $monthEnd = date('Y-m-t', strtotime($dateFrom));

        if ($dateFrom === $monthStart && $dateTo === $monthEnd) {
            return \Carbon\Carbon::parse($dateFrom)->format('F Y');
        }

        return \Carbon\Carbon::parse($dateFrom)->format('M d, Y')
            .' – '
            .\Carbon\Carbon::parse($dateTo)->format('M d, Y');
    }

    public function branchTransfersQuery(Request $request): Builder
    {
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));
        $fromBranch = $request->query('from_branch', 'All Branches');
        $toBranch = $request->query('to_branch', 'All Branches');
        $search = trim((string) $request->query('transfersearch', ''));

        if ($dateFrom === '' && $dateTo === '') {
            $salesDate = session('date_today') ?: now()->format('Y-m-d');
            $dateFrom = date('Y-m-01', strtotime($salesDate));
            $dateTo = date('Y-m-t', strtotime($salesDate));
        }

        $query = BranchTransfer::with(['item', 'user'])
            ->where('del', 'no')
            ->reportSearch($search);

        if ($fromBranch !== null && $fromBranch !== '' && $fromBranch !== 'All Branches') {
            $query->where('from_branch', (string) $fromBranch);
        }

        if ($toBranch !== null && $toBranch !== '' && $toBranch !== 'All Branches') {
            $query->where('to_branch', (string) $toBranch);
        }

        if ($dateFrom !== '' && $dateTo === '') {
            $query->where('created_at', 'LIKE', '%'.$dateFrom.'%');
        } elseif ($dateFrom !== '' && $dateTo !== '') {
            $query->whereBetween('created_at', [$dateFrom, new \DateTime($dateTo.'+1 day')]);
        }

        return $query->orderByDesc('id');
    }

    public function branchLabel(string $branchTag): string
    {
        $branch = CompanyBranch::query()->where('tag', (string) $branchTag)->first();

        return $branch?->name ?? 'Branch '.$branchTag;
    }
}
