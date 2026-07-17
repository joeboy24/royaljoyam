<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Waybill;
use App\Models\Wbcontent;
use App\Models\Wbdistribution;
use App\Support\BranchQuantities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaybillDistributionService
{
    public function branchQtysFromRequest(Request $request, int $itemId): array
    {
        return BranchQuantities::fromRequest($request, $itemId);
    }

    public function apply(Wbcontent $wbc, array $branchQtys): ?string
    {
        $waybill = Waybill::find($wbc->waybill_id);
        if (! $waybill || ! $waybill->canDistribute()) {
            return 'Waybill must be marked Delivered before distributing to branches.';
        }

        $itup = Item::find($wbc->item_id);
        if (! $itup) {
            return 'Item not found.';
        }

        $totQs = BranchQuantities::distributedTotal($branchQtys);
        $remaining = (int) $wbc->qty - (int) $wbc->qty_dist;

        if ($remaining <= 0) {
            return 'Oops..! Restock item `'.$itup->name.'` in order to distribute. 0 left';
        }
        if ($totQs > $remaining) {
            return 'Oops..! Only '.$remaining.' available for distribution to branches';
        }

        $wbc->qty_dist = (int) $wbc->qty_dist + $totQs;
        $wbc->save();

        Wbdistribution::create(array_merge([
            'user_id' => auth()->user()->id,
            'waybill_id' => $wbc->waybill_id,
            'item_id' => $wbc->item_id,
        ], BranchQuantities::normalizeForStorage($branchQtys)));

        BranchQuantities::applyToItem($itup, $branchQtys);

        return null;
    }

    public function distributeAll(Waybill $waybill, Request $request): array
    {
        if (! $waybill->canDistribute()) {
            return [
                'error' => 'Waybill must be marked Delivered before distributing to branches.',
                'updated' => 0,
            ];
        }

        $wbcontents = Wbcontent::where('waybill_id', $waybill->id)->where('del', 'no')->get();
        $updated = 0;

        DB::beginTransaction();
        try {
            foreach ($wbcontents as $wbc) {
                $branchQtys = $this->branchQtysFromRequest($request, (int) $wbc->item_id);
                if (BranchQuantities::distributedTotal($branchQtys) <= 0) {
                    continue;
                }

                $error = $this->apply($wbc, $branchQtys);
                if ($error) {
                    DB::rollBack();

                    return ['error' => $error, 'updated' => 0];
                }

                $updated++;
            }

            if ($updated === 0) {
                DB::rollBack();

                return [
                    'error' => 'Enter at least one branch quantity to distribute.',
                    'updated' => 0,
                ];
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return [
                'error' => 'Oops..! Something went wrong while distributing to branches.',
                'updated' => 0,
            ];
        }

        return ['error' => null, 'updated' => $updated];
    }
}
