<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Wbcontent;
use App\Models\CompanyBranch;
use App\Models\Wbdistribution;
use App\Models\Waybill;
use DateTime;

class DistributionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware(['auth', 'load_auth']);
    } 

    public function index(Request $request)
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        $query = $request->getQueryString();

        return redirect('/distreport'.($query ? '?'.$query : ''));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $wbcs = Wbcontent::where('waybill_id', $id)->where('del', 'no')->with(['item', 'waybill'])->get();
        $cur_qtys = $wbcs->isNotEmpty()
            ? $wbcs->map(fn ($wbc) => Item::find($wbc->item_id))->values()
            : collect();

        $dist_qtys = Wbdistribution::where('waybill_id', $id)->where('del', 'no')->get();
        $dist_sent = [];
        foreach ($dist_qtys as $dist) {
            $itemId = $dist->item_id;
            if (! isset($dist_sent[$itemId])) {
                $dist_sent[$itemId] = array_fill_keys(array_map(fn ($i) => 'q'.$i, range(1, 7)), 0);
            }
            for ($i = 1; $i <= 7; $i++) {
                $key = 'q'.$i;
                $dist_sent[$itemId][$key] += (int) ($dist->{$key} ?? 0);
            }
        }

        $items = Item::where('del', 'no')->orderBy('name')->get();
        $waybill = Waybill::active()->findOrFail($id);
        $send = [
            'c' => 1,
            'x' => 1,
            't' => 1,
            'wb_id' => $id,
            'waybill' => $waybill,
            'items' => $items,
            'wbcontents' => $wbcs,
            'cur_qtys' => $cur_qtys,
            'branches' => CompanyBranch::where('del', 'no')->orderBy('tag')->get(),
            'dist_qtys' => $dist_qtys,
            'dist_sent' => $dist_sent,
        ];
        return view('pages.dash.waybill_dist')->with($send);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        //
        return $request->input('tvalue');
        return $id;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
