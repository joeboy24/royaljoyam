<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Collection;

class InventoryCsvService
{
    /**
     * Active inventory is empty when no non-deleted items exist.
     */
    public function isActiveInventoryEmpty(): bool
    {
        return ! Item::where('del', 'no')->exists();
    }

    /**
     * Headers for an upload-ready CSV template (aligned with add-item fields).
     *
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @return list<string>
     */
    public function templateHeaders(Collection $branches): array
    {
        $headers = [
            'Name',
            'Description',
            'Category',
            'Brand',
            'Barcode',
            'General Qty',
            'Base Price (Gh)',
        ];

        foreach ($branches as $branch) {
            $headers[] = $branch->name.' Qty';
        }

        return $headers;
    }

    /**
     * Headers for the current inventory data export.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @return list<string>
     */
    public function dataExportHeaders(Collection $branches): array
    {
        $headers = [
            'Item No',
            'Name',
            'Category',
            'Brand',
            'Barcode',
            'General Qty',
            'Stock Status',
            'Base Price (Gh)',
            'Date',
        ];

        foreach ($branches as $branch) {
            $headers[] = $branch->name.' Qty';
        }

        return $headers;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $branches
     * @return list<string|int|float>
     */
    public function dataExportRow(Item $item, Collection $branches, int $threshold): array
    {
        $row = [
            $item->item_no,
            $item->name,
            $item->cat,
            $item->brand,
            $item->barcode,
            $item->qty,
            $item->stockBadgeLabel($threshold),
            number_format((float) $item->price, 2, '.', ''),
            $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '',
        ];

        for ($i = 0; $i < $branches->count(); $i++) {
            $field = 'q'.($i + 1);
            $row[] = $item->$field ?? 0;
        }

        return $row;
    }
}
