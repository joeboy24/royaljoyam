<?php

namespace App\Console\Commands;

use App\Models\Item;
use Illuminate\Console\Command;

class RepairInventoryQty extends Command
{
    protected $signature = 'inventory:repair-qty
                            {--dry-run : Show affected items without saving changes}';

    protected $description = 'Repair item general qty values that are negative or lower than branch totals';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $items = Item::where('del', 'no')->orderBy('id')->get();
        $repaired = 0;

        foreach ($items as $item) {
            if (!$item->needsGeneralQtyRepair()) {
                continue;
            }

            $oldQty = (int) $item->qty;
            $branchTotal = $item->branchQtyTotal();
            $newQty = max($branchTotal, 0);

            if ($dryRun) {
                $this->line(sprintf(
                    '[dry-run] #%d %s: qty %d -> %d (branch total %d)',
                    $item->id,
                    $item->name,
                    $oldQty,
                    $newQty,
                    $branchTotal
                ));
            } else {
                $item->repairGeneralQty();
                $this->line(sprintf(
                    'Repaired #%d %s: qty %d -> %d',
                    $item->id,
                    $item->name,
                    $oldQty,
                    $newQty
                ));
            }

            $repaired++;
        }

        if ($repaired === 0) {
            $this->info('No item quantities needed repair.');
            return self::SUCCESS;
        }

        $this->info($dryRun
            ? "{$repaired} item(s) would be repaired. Re-run without --dry-run to apply."
            : "Repaired {$repaired} item(s).");

        return self::SUCCESS;
    }
}
