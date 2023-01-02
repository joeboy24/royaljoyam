<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Wbcontent;
use App\Models\CompanyBranch;
use App\Models\Wbdistribution;
use Session;
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
        //
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        $match = ['del' => 'no'];
        
        if (!empty($date_from) && empty($date_to)) {
            $wbds = Wbdistribution::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->paginate(10);
            $wbds_send = Wbdistribution::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->get();
        }elseif (empty($date_from) && !empty($date_to)) {
            return redirect(url()->previous())->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }elseif (!empty($date_from) && !empty($date_to)) {
            $wbds = Wbdistribution::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->paginate(10);
            $wbds_send = Wbdistribution::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->get();
        }else{
            // $match = ['del' => 'no'];
            $wbds = Wbdistribution::where($match)->orderBy('id', 'desc')->paginate(10);
            $wbds_send = Wbdistribution::where($match)->orderBy('id', 'desc')->get();
        }

        Session::put('wbdreports', $wbds_send);
        Session::put('date_from', $date_from);
        Session::put('date_to', $date_to);

        $pass = [
            'i' => 1,
            'c' => 1,
            'wbds' => $wbds,
        ];
        return view('pages.dash.distreport')->with($pass);
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
        //
        // $cur_qtys[] = '';
        $wbcs = Wbcontent::where('waybill_id', $id)->where('del', 'no')->get();
        if (count($wbcs) > 0) {
            foreach ($wbcs as $wbc) {
                $cur_qtys[] = Item::find($wbc->item_id);
            }
        }else {
            $cur_qtys = '';
        }
        // return $cur_qtys[1]->name;
        $items = Item::where('del', 'no')->get();
        $send = [
            'c' => 1,
            'x' => 1,
            't' => 1,
            'wb_id' => $id,
            'items' => $items,
            'wbcontents' => $wbcs,
            'cur_qtys' => $cur_qtys,
            'branches' => CompanyBranch::all(),
            'dist_qtys' => Wbdistribution::where('waybill_id', $id)->where('del', 'no')->get(),
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
