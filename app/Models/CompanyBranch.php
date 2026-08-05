<?php

namespace App\Models;

use App\Support\BranchQuantities;
use Illuminate\Database\Eloquent\Model;

class CompanyBranch extends Model
{
    public function expense(){
        return $this->HasMany('App\Models\Expense');
    }

    public function canBeDeleted(): bool
    {
        return empty($this->deletionBlockers());
    }

    /**
     * Human-readable reasons this branch cannot be deleted.
     *
     * @return list<string>
     */
    public function deletionBlockers(): array
    {
        if ($this->del === 'yes') {
            return ['Branch is already deleted.'];
        }

        $activeCount = static::query()->where('del', 'no')->count();
        if ($activeCount <= 1) {
            return ['At least one branch is required.'];
        }

        $tag = (string) $this->tag;
        $id = (string) $this->id;
        $reasons = [];

        $userCount = User::query()
            ->where('del', 'no')
            ->where(function ($q) use ($id, $tag) {
                $q->where('company_branch_id', $id)
                    ->orWhere('bv', $tag);
            })
            ->count();
        if ($userCount > 0) {
            $reasons[] = $userCount.' user'.($userCount === 1 ? '' : 's').' assigned to this branch';
        }

        $saleCount = Sale::query()->where('user_bv', $tag)->count();
        if ($saleCount > 0) {
            $reasons[] = $saleCount.' sale'.($saleCount === 1 ? '' : 's').' linked to this branch';
        }

        $historyCount = SalesHistory::query()->where('user_bv', $tag)->count();
        if ($historyCount > 0) {
            $reasons[] = $historyCount.' sales history record'.($historyCount === 1 ? '' : 's').' linked to this branch';
        }

        $returnCount = OrderReturn::query()->where('user_bv', $tag)->count();
        if ($returnCount > 0) {
            $reasons[] = $returnCount.' order return'.($returnCount === 1 ? '' : 's').' linked to this branch';
        }

        $expenseCount = Expense::query()
            ->where(function ($q) use ($id, $tag) {
                $q->where('companybranch_id', $id)
                    ->orWhere('companybranch_id', $tag);
            })
            ->count();
        if ($expenseCount > 0) {
            $reasons[] = $expenseCount.' expense'.($expenseCount === 1 ? '' : 's').' linked to this branch';
        }

        $transferCount = BranchTransfer::query()
            ->where('from_branch', $tag)
            ->orWhere('to_branch', $tag)
            ->count();
        if ($transferCount > 0) {
            $reasons[] = $transferCount.' branch transfer'.($transferCount === 1 ? '' : 's').' linked to this branch';
        }

        $closureCount = DailyClosure::query()
            ->where('scope_key', 'bv:'.$tag)
            ->count();
        if ($closureCount > 0) {
            $reasons[] = $closureCount.' daily closure'.($closureCount === 1 ? '' : 's').' linked to this branch';
        }

        $qtyColumn = BranchQuantities::columnForBranchTag($tag);
        if ($qtyColumn) {
            $stockedItems = Item::query()
                ->where('del', 'no')
                ->where($qtyColumn, '>', 0)
                ->count();
            if ($stockedItems > 0) {
                $reasons[] = $stockedItems.' inventory item'.($stockedItems === 1 ? '' : 's').' still have stock in this branch';
            }
        }

        return $reasons;
    }
}
