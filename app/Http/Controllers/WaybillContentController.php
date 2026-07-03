<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Waybill;
use App\Models\Wbcontent;
use App\Services\WaybillDistributionService;
use App\Support\BranchQuantities;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaybillContentController extends Controller
{
    public function __construct(
        protected WaybillDistributionService $distributionService
    ) {
        $this->middleware(['auth', 'load_auth']);
    }

    public function store(Request $request, Waybill $waybill)
    {
        $this->authorize('update', $waybill);
        $this->authorize('create', Wbcontent::class);

        $request->validate([
            'item' => 'required',
            'qty' => 'required|integer|min:1',
        ]);

        $itemId = $request->input('item');
        $qty = $request->input('qty');
        $item = Item::find($itemId);

        if (! $item) {
            return redirect(url()->previous())->with('error', 'Item not found.');
        }

        $exists = Wbcontent::where('waybill_id', $waybill->id)->where('item_id', $itemId)->exists();
        if ($exists) {
            return redirect(url()->previous())->with('error', 'Oops..! Item `'.$item->item_no.' - '.$item->name.'` already added.');
        }

        Wbcontent::create([
            'user_id' => (string) auth()->id(),
            'waybill_id' => (string) $waybill->id,
            'item_id' => (string) $itemId,
            'qty' => (string) $qty,
        ]);

        Waybill::syncTotQtyFor($waybill->id);

        return redirect(url()->previous())->with('success', 'Item `'.$item->item_no.' - '.$item->name.'` successfully added.');
    }

    public function update(Request $request, Wbcontent $wbcontent)
    {
        $this->authorize('update', $wbcontent);

        $request->validate([
            'qty' => 'required|integer|min:0',
        ]);

        $newQty = max(0, (int) $request->input('qty'));
        if ($newQty < (int) $wbcontent->qty_dist) {
            return redirect(url()->previous())->with('error', 'Cannot set quantity below already distributed amount ('.$wbcontent->qty_dist.').');
        }

        $wbcontent->qty = (string) $newQty;
        $wbcontent->save();
        Waybill::syncTotQtyFor($wbcontent->waybill_id);

        return redirect(url()->previous())->with('success', 'Waybill quantity update successful');
    }

    public function destroy(Wbcontent $wbcontent)
    {
        $this->authorize('delete', $wbcontent);

        if ((int) $wbcontent->qty_dist > 0) {
            return redirect(url()->previous())->with('error', 'Cannot remove item — '.$wbcontent->qty_dist.' already distributed to branches.');
        }

        $waybillId = $wbcontent->waybill_id;
        $wbcontent->delete();
        Waybill::syncTotQtyFor($waybillId);

        return redirect(url()->previous())->with('success', 'Item removed from waybill');
    }

    public function distribute(Request $request, Wbcontent $wbcontent)
    {
        $this->authorize('distribute', $wbcontent);

        $branchQtys = $this->distributionService->branchQtysFromRequest($request, (int) $wbcontent->item_id);
        if (BranchQuantities::distributedTotal($branchQtys) <= 0) {
            return redirect(url()->previous())->with('error', 'Enter at least one branch quantity to distribute.');
        }

        try {
            DB::beginTransaction();
            $error = $this->distributionService->apply($wbcontent, $branchQtys);
            if ($error) {
                DB::rollBack();

                return redirect(url()->previous())->with('error', $error);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return redirect(url()->previous())->with('error', 'Oops..! Something went wrong while distributing to branches.');
        }

        return redirect(url()->previous())->with('success', 'Waybill branch quantities update successful');
    }

    public function distributeAll(Request $request, Waybill $waybill)
    {
        $this->authorize('distribute', $waybill);

        $result = $this->distributionService->distributeAll($waybill, $request);

        if ($result['error']) {
            return redirect(url()->previous())->with('error', $result['error']);
        }

        $updated = $result['updated'];
        $label = $updated === 1 ? '1 item' : $updated.' items';

        return redirect(url()->previous())->with('success', 'Branch distribution saved for '.$label.'.');
    }
}
