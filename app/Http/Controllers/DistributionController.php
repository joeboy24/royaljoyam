<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Waybill;
use App\Models\Wbcontent;
use App\Models\Wbdistribution;
use App\Support\BranchQuantities;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'load_auth']);
        $this->middleware(function ($request, $next) {
            if (auth()->user()->status != 'Administrator') {
                return redirect('/dashboard');
            }

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Waybill::class);

        $query = $request->getQueryString();

        return redirect('/distreport'.($query ? '?'.$query : ''));
    }

    public function show($id)
    {
        $waybill = Waybill::active()->findOrFail($id);
        $this->authorize('distribute', $waybill);

        $wbcs = Wbcontent::where('waybill_id', $id)->where('del', 'no')->with(['item', 'waybill'])->get();
        $cur_qtys = $wbcs->isNotEmpty()
            ? $wbcs->map(fn ($wbc) => Item::find($wbc->item_id))->values()
            : collect();

        $dist_qtys = Wbdistribution::where('waybill_id', $id)->where('del', 'no')->get();
        $dist_sent = [];
        foreach ($dist_qtys as $dist) {
            BranchQuantities::accumulateSent($dist_sent, $dist);
        }

        $branches = BranchQuantities::activeBranches();

        return view('pages.dash.waybill_dist', [
            'c' => 1,
            'x' => 1,
            't' => 1,
            'wb_id' => $id,
            'waybill' => $waybill,
            'items' => Item::where('del', 'no')->orderBy('name')->get(),
            'wbcontents' => $wbcs,
            'cur_qtys' => $cur_qtys,
            'branches' => $branches,
            'dist_qtys' => $dist_qtys,
            'dist_sent' => $dist_sent,
        ]);
    }
}
