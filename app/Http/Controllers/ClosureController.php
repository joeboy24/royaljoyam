<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
// use App\Models\Company;
// use App\Models\User;
use App\Models\Item;
// use App\Models\Cart;
use App\Models\Sale;
// use App\Models\Order;
// use App\Models\Expense;
// use App\Models\Waybill;
use App\Models\ItemAudit;
use App\Models\SalesHistory;
// use App\Models\SalesPayment;
// use App\Models\CompanyBranch;
// use App\Models\ItemImage;
// use App\Models\Category;
// use App\Models\Wbcontent;
use App\Models\Wbdistribution;
use App\Models\Closure;
use Exception;
use Session;

class ClosureController extends Controller
{
    public function __construct(){
        $this->middleware(['auth', 'load_auth']);
    } 
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
    public function show($full_date)
    {
        //
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        // return $id;
        // Session::put('stockfill', 1);
        // $full_date = $id;
        $mth = date('m', strtotime($full_date));
        $yr = date('Y', strtotime($full_date));
        $om = $mth - 1;
        if ($om < 10) {
            $om = '0'.$om;
        }
        $old_month = date($yr.'-'.$om.'-01');
        
        $date_from = date($yr.'-'.$mth.'-01');
        $date_to = date($yr.'-'.$mth.'-t');
        $closure_state = '';

        $openning_check = Closure::where('month', $date_from)->latest()->first();
        if ($openning_check != '') {
            $closure_state = $openning_check->status;
            Session::put('mth_openning', 1);
        } else {
            Session::put('mth_openning', 0);
        }

        // $closure_check = Closure::where('month', $old_month)->latest()->first();
        // if ($closure_check != '') {
        //     Session::put('mth_closure', 1);
        // } else {
        //     // return redirect(url()->previous())->with('error', 'Set closure for '.date('F, Y', strtotime($old_month)));
        //     Session::put('mth_closure', 0);
        // }
        
        $items = ItemAudit::where('del', 'no')->whereBetween('created_at', [$date_from, $date_to])->orderBy('id', 'desc')->paginate(10);
        $items_send = ItemAudit::where('del', 'no')->whereBetween('created_at', [$date_from, $date_to])->orderBy('id', 'desc')->get();
        $saleshistory_send = SalesHistory::where('del', 'no')->whereBetween('created_at', [$date_from, $date_to])->get();
        $dist_send = Wbdistribution::where('del', 'no')->whereBetween('created_at', [$date_from, $date_to])->get();
        $dist_dist = Wbdistribution::where('del', 'no')->select('item_id')->whereBetween('created_at', [$date_from, $date_to])->distinct('item_id')->get();
        $stock = SalesHistory::where('del', 'no')->select('item_id')->whereBetween('created_at', [$date_from, $date_to])->distinct('item_id')->get();
        
        // return $stock;
        // return count($saleshistory_send);

        Session::put('stock', $stock);
        Session::put('items', Item::all());
        Session::put('sales_history', $saleshistory_send);
        Session::put('cldate', $date_from);

        $pass = [
            'yr' => $yr,
            'p' => 1,
            'c' => 1,
            'x' => 1,
            'y' => 1,
            't' => 1,
            'br1' => 0,
            'br2' => 0,
            'br3' => 0,
            'br4' => 0,
            'br5' => 0,
            'br6' => 0,
            'br7' => 0,
            'xh' => 0,
            'qtr' => 0,
            'qts' => 0,
            'tamt' => 0,
            'tprof' => 0,
            'exists' => 0,
            'qtr_tot' => 0,
            'stock' => $stock,
            'dist_dist' => $dist_dist,
            'distribution' => $dist_send,
            'closure_state' => $closure_state,
            // 'closure_id' => $closure_check->id,
            'sales_history' => $saleshistory_send
        ];
        
        return view('pages.invoice.closureinvoice')->with($pass);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
