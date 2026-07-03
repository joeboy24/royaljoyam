<?php

namespace App\Support;

use App\Models\CompanyBranch;
use App\Models\Item;
use App\Models\Wbdistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class BranchQuantities
{
    public const MAX_COLUMNS = 7;

    public static function activeBranches(): Collection
    {
        return CompanyBranch::where('del', 'no')->orderBy('tag')->get();
    }

    /** @return list<string> */
    public static function allColumnKeys(): array
    {
        return array_map(fn (int $i) => 'q'.$i, range(1, self::MAX_COLUMNS));
    }

    /** @return list<string> */
    public static function activeColumnKeys(): array
    {
        $count = min(self::activeBranches()->count(), self::MAX_COLUMNS);

        if ($count <= 0) {
            return [];
        }

        return array_map(fn (int $i) => 'q'.$i, range(1, $count));
    }

    /** @return array<string, int> */
    public static function emptyRow(): array
    {
        return array_fill_keys(self::allColumnKeys(), 0);
    }

    public static function fromRequest(Request $request, int $itemId): array
    {
        $quantities = self::emptyRow();

        foreach (self::activeColumnKeys() as $key) {
            $quantities[$key] = max(0, (int) $request->input($key.$itemId, 0));
        }

        return $quantities;
    }

    public static function distributedTotal(array $quantities): int
    {
        $total = 0;

        foreach (self::activeColumnKeys() as $key) {
            $total += (int) ($quantities[$key] ?? 0);
        }

        return $total;
    }

    /** @return array<string, string> */
    public static function normalizeForStorage(array $quantities): array
    {
        $row = [];

        foreach (self::allColumnKeys() as $key) {
            $row[$key] = (string) max(0, (int) ($quantities[$key] ?? 0));
        }

        return $row;
    }

    public static function applyToItem(Item $item, array $quantities): void
    {
        foreach (self::allColumnKeys() as $key) {
            $item->{$key} = $item->{$key} + (int) ($quantities[$key] ?? 0);
        }

        $item->qty = $item->qty + self::distributedTotal($quantities);
        $item->save();
    }

    /** @param array<string, int> $distSent */
    public static function accumulateSent(array &$distSent, Wbdistribution $distribution): void
    {
        $itemId = $distribution->item_id;

        if (! isset($distSent[$itemId])) {
            $distSent[$itemId] = self::emptyRow();
        }

        foreach (self::allColumnKeys() as $key) {
            $distSent[$itemId][$key] += (int) ($distribution->{$key} ?? 0);
        }
    }
}
