<?php

namespace App\Services;

use App\Models\BranchTransfer;
use App\Models\Item;
use App\Models\User;
use App\Support\BranchQuantities;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BranchTransferService
{
    public function transfer(
        Item $item,
        string $fromBranchTag,
        string $toBranchTag,
        int $qty,
        User $user,
        ?string $notes = null
    ): BranchTransfer {
        if ($qty <= 0) {
            throw new InvalidArgumentException('Enter a quantity greater than zero.');
        }

        if ($fromBranchTag === $toBranchTag) {
            throw new InvalidArgumentException('Choose two different branches.');
        }

        $fromColumn = BranchQuantities::columnForBranchTag($fromBranchTag);
        $toColumn = BranchQuantities::columnForBranchTag($toBranchTag);

        if ($fromColumn === null || $toColumn === null) {
            throw new InvalidArgumentException('Select valid branches.');
        }

        return DB::transaction(function () use ($item, $fromColumn, $toColumn, $fromBranchTag, $toBranchTag, $qty, $user, $notes) {
            $lockedItem = Item::query()->whereKey($item->id)->lockForUpdate()->firstOrFail();

            if ($lockedItem->del === 'yes') {
                throw new InvalidArgumentException('Item not found.');
            }

            $available = max(0, (int) ($lockedItem->{$fromColumn} ?? 0));

            if ($qty > $available) {
                throw new InvalidArgumentException('Not enough stock at the source branch (available: '.$available.').');
            }

            $lockedItem->{$fromColumn} = (string) ($available - $qty);
            $lockedItem->{$toColumn} = (string) (max(0, (int) ($lockedItem->{$toColumn} ?? 0)) + $qty);
            $lockedItem->save();

            return BranchTransfer::create([
                'user_id' => (string) $user->id,
                'item_id' => (string) $lockedItem->id,
                'from_branch' => $fromBranchTag,
                'to_branch' => $toBranchTag,
                'qty' => (string) $qty,
                'notes' => $notes ? trim($notes) : null,
                'del' => 'no',
            ]);
        });
    }

    public function branchName(string $branchTag): string
    {
        $branch = BranchQuantities::activeBranches()->firstWhere('tag', (string) $branchTag);

        return $branch ? $branch->name : 'Branch '.$branchTag;
    }
}
