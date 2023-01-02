<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Category;
use App\Models\User;
use App\Models\Cart;
use App\Models\Item;
use App\Models\Sale;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Waybill;
use App\Models\ItemAudit;
use App\Models\SalesPayment;
use App\Models\SalesHistory;
use App\Models\CompanyBranch;
use App\Models\OrderReturn;
use App\Models\Wbdistribution;
use Exception;
use Session;
use DateTime;

class DashController extends Controller
{
    public function __construct(){
        $this->middleware(['auth', 'load_auth']);
    } 

    //
    public function say(){
        return 775757;
    }
    
    public function dashboard(){

        if(session('date_today') == ''){
            Session::put('date_today', date('Y-m-d'));
        }
        return view('pages.dash.dashboard');
    }

    public function configurations(){

        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }

        $items = Item::all();
        $company = Company::all();
        $branches = CompanyBranch::all();
        $pass = [
            'items' => $items,
            'company' => $company,
            'branches' => $branches
        ];
        return view('pages.dash.configuration')->with($pass);
    }

    public function dashuser(){

        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }

        $i = 1;
        $r = 1;
        $items = Item::all();
        $users = User::all();
        $cat = Category::all();
        $branches = CompanyBranch::all();

        $pass = [
            'i' => $i,
            'r' => $r,
            'items' => $items,
            'users' => $users,
            'branches' => $branches,
            'category' => $cat
        ];

        return view('pages.dash.dashuser')->with($pass);
    }

    public function sales(){

        if(session('date_today') == ''){
            Session::put('date_today', date('Y-m-d'));
        }

        if(session('sales_permit') == 0){
            return redirect('/dashboard')->with('error', 'Oops..! Contact administrator to initialize '.date('F, Y').' opening');
        }


        if(auth()->user()->status == 'Administrator'){
            $uid_hold = 'no';
            $field = "del";
        }else{
            $uid_hold = auth()->user()->id;
            $field = "user_id";
        }

        $items = Item::all();

        $uidMatch = [
            $field => $uid_hold
        ];
        $sales = Sale::where($uidMatch)->where('created_at', 'LIKE', '%'.session('date_today').'%')->orderBy('id', 'desc')->paginate(10);
        $sales2 = Sale::where($uidMatch)->where('created_at', 'LIKE', '%'.session('date_today').'%')->get();
        // return $sales;
        $debts = SalesPayment::where($uidMatch)->where('updated_at', 'LIKE', '%'.session('date_today').'%')->get();
        // 2021-05-12 18:50:28
        $cashMatch = [
            'pay_mode' => 'Cash',
            $field => $uid_hold
        ];
        $cash = Sale::where($cashMatch)->where('created_at', 'LIKE', '%'.session('date_today').'%')->sum('tot');
        $chequeMatch = [
            'pay_mode' => 'Cheque',
            $field => $uid_hold
        ];
        $cheque = Sale::where($chequeMatch)->where('created_at', 'LIKE', '%'.session('date_today').'%')->sum('tot');
        $momoMatch = [
            'pay_mode' => 'Mobile Money',
            $field => $uid_hold
        ];
        $momo = Sale::where($momoMatch)->where('created_at', 'LIKE', '%'.session('date_today').'%')->sum('tot');
        $debtMatch = [
            'pay_mode' => 'Post Payment(Debt)',
            $field => $uid_hold
        ];
        $sum_dbt = Sale::where($debtMatch)->where('created_at', 'LIKE', '%'.session('date_today').'%')->sum('tot');
        // $profits = SalesHistory::where($field, $uid_hold)->where('created_at', 'LIKE', '%'.session('date_today').'%')->get();

        // Select for both where and %like%
        $expenses = Expense::where('user_id', auth()->user()->id)->where('created_at', 'LIKE', '%'.date('Y-m-d').'%');

        // br

        //$sales2 = Sale::where($match);
        $sum_ex_dbt = $sales2->sum('tot') - $sum_dbt;
        $sum_inc_dbt = $sum_ex_dbt + $debts->sum('amt_paid');
        $debts_paid = $debts->sum('amt_paid');
        // $sum_ex_dbt = $sales->sum('tot') - ($sum_dbt + $cheque);
        // End select both
        $uidMatch = [
            $field => $uid_hold
        ];
        $carts = Cart::where($uidMatch)->where('created_at', 'LIKE', '%'.session('date_today').'%')->get();

        $pass = [
            'i' => 1,
            'c' => 1,
            'j' => 1,
            'items' => $items,
            'sales' => $sales,
            'expenses' => $expenses,
            'cash' => $cash,
            'cheque' => $cheque,
            'momo' => $momo,
            'sum_dbt' => $sum_dbt,
            'sum_dbt' => $sum_dbt,
            'debts_paid' => $debts_paid,
            'sum_inc_dbt' => $sum_inc_dbt,
            'carts' => $carts
        ];
        return view('pages.dash.sales')->with($pass);
    }

    public function waybill(){
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        return view('pages.dash.waybill');
    }

    public function stockview(){
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        return view('pages.dash.stockview');
    }

    public function waybillview(Request $request){
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        $c = 1;
        $match = ['del' => 'no'];
        $waybillsearch = $request->query('waybillsearch');
        if(!empty($waybillsearch)){
            $waybills = Waybill::where($match)->where('comp_name', 'like', '%'.$waybillsearch.'%')->orderBy('id', 'desc')->paginate(10);
            if(count($waybills) < 1){
                $waybills = Waybill::where($match)->where('drv_name', 'like', '%'.$waybillsearch.'%')->orderBy('id', 'desc')->paginate(10);
        
            }
        }else{
            $waybills = Waybill::where($match)->orderBy('id', 'desc')->paginate(10);
        }
        $pass = [
            'c' => $c,
            'waybills' => $waybills
        ];
        return view('pages.dash.waybillview')->with($pass);
    }

    public function empty_cart(){
        // try {
            //code...
            
            $uid = auth()->user()->id;
            $ubv = auth()->user()->bv;
            $carts = Cart::where('user_id', $uid)->get();
            $q = 'q'.$ubv;
            if(count($carts) > 0){
                foreach ($carts as $cart) {
                    # code...

                    $item = Item::find($cart->item_id);
                    $oq = $item->qty;
                    $obq = $item->$q;
                    // return $obq;
                    $item->qty = $oq + $cart->qty;
                    $item->$q = $obq + $cart->qty;
                    $item->save();

                    // Empty specific user/branch cart
                    $cart_del = Cart::find($cart->id);
                    $cart_del->delete();
                }
            }
            return redirect('/sales')->with('success', 'Cart Emptied..');

        // } catch (\Throwable $th) {
        //     //throw $th;
        //     return redirect('/sales')->with('error', 'Oops..! Unhandled Error.. ');
        // }
    }

    public function reporting2(){
        $c = 1;

        // Get sum Branch 1
        $b1_match = ['del' => 'no', 'user_bv' => 1 ];
        $b1 = Sale::where($b1_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
        // Get sum Branch 2
        $b2_match = ['del' => 'no', 'user_bv' => 2 ];
        $b2 = Sale::where($b2_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
        // Get sum Branch 3
        $b3_match = ['del' => 'no', 'user_bv' => 3 ];
        $b3 = Sale::where($b3_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');

        // ->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')

        $expenses = Expense::where('del', 'no')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->get();

        $pass = [
            'b1' => $b1, 
            'b2' => $b2, 
            'b3' => $b3, 
            'expenses' => $expenses
        ];
        return view('pages.dash.reportsview')->with($pass);
    }

    public function reportprinting(){
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        // return url()->previous();
        $company = Company::find(1);
        $pass = [
            'count' => 1,
            'company' => $company,
            'sales' => session('sales')
        ];
        // return session('sales');
        return view('pages.dash.invoice')->with($pass);
    }

    public function stockreportprinting(){
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        // // return url()->previous();
        // $items = Item::all();
        $company = Company::find(1);
        $pass = [
            'count' => 1,
            'company' => $company,
            'items' => session('items')
        ];
        // return session('sales');
        return view('pages.dash.stockinvoice')->with($pass);
    }

    public function stockfillprint(){
        // return 1234;
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }

        $pass = [
            'count' => 1
        ];

        $pass = [
            'c' => 1,
            'x' => 1,
            'y' => 1,
            't' => 1,
            'qtr' => 0,
            'qts' => 0,
            'tamt' => 0,
            'tprof' => 0,
            'exists' => 0,
            'qtr_tot' => 0,
        ];
        // return session('sales');
        return view('pages.dash.stockfillinvoice')->with($pass);
    }

    public function waybillprint(){
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        $pass = [
            'count' => 1
        ];
        return view('pages.invoice.waybillprint')->with($pass);
    }

    public function distreportprint(){
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        $pass = [
            'count' => 1,
            'sum' => 0,
        ];
        return view('pages.invoice.distreportprint')->with($pass);
    }

    public function returnprint(){
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        // // return url()->previous();
        // $items = Item::all();
        $company = Company::find(1);
        $pass = [
            'count' => 1,
            'company' => $company,
            'returns' => session('returnsrep')
        ];
        // return session('sales');
        return view('pages.invoice.returninvoice')->with($pass);
    }

    public function expensereportprinting(){
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        // return url()->previous();
        $company = Company::find(1);
        $pass = [
            'count' => 1,
            'company' => $company,
            'expenses' => session('expenses')
        ];
        // return session('sales');
        return view('pages.dash.expenseinvoice')->with($pass);
    }

    public function genstockbal(){
        // return session('genstockbal');
        
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        // return url()->previous();
        $company = Company::find(1);
        $pass = [
            'count' => 1,
            'company' => $company,
            'genstockbal' => session('genstockbal')
        ];
        // return session('sales');
        return view('pages.dash.genstockbal')->with($pass);
    }

    public function changedate(Request $request){
        $date_today = $request->query('date_today');
        if(empty($date_today)){
            return redirect(url()->previous())->with('error', 'Select date to change to..!');
        }
        Session::put('date_today', $date_today);
        return redirect(url()->previous())->with('success', 'Date changed to '.$date_today);
    }

    public function deliverer(Request $request){
        $sale_id = $request->query('deliverer');
        $delTxt = $request->query('deliverer_text');
        // return url()->previous();
        $sale = Sale::find($sale_id);
        $sale->del_status = $delTxt;
        $sale->save();
        return redirect('/sales')->with('success', 'Delivery status changed to *'.$delTxt.'*');
    }

    public function stockbal(Request $request){

        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        //
        Session::put('stockfill', 1);
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        // $date_to = date('Y-m-d', strtotime('+1 day', $date_to));
        // $date_to = new DateTime($date_to);
        // return $date_to;
        // $date_to->modify('+1 day');
        // new DateTime('2013-01-29'))->add(new DateInterval('P1D')
        // echo (new DateTime('2013-01-29'))->add(new DateInterval('P1D'))->format('Y-m-d H:i:s');
        // date('Y-m-d H:i:s', strtotime('+1 day', $startDate))         where('created_at', '>=', $date_from)->where('created_at', '<=', strtotime('+1 day', $date_to))
        
        if (!empty($date_from) && empty($date_to)) {
            // return 12345;
            # code...
            $nonedit = 'true';
            $items = ItemAudit::where('del', 'no')->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->paginate(10);
            $items_send = ItemAudit::where('del', 'no')->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->get();
            $saleshistory_send = SalesHistory::where('del', 'no')->where('created_at', 'LIKE', '%'.$date_from.'%')->get();
            $stock = SalesHistory::where('del', 'no')->select('item_id')->where('created_at', 'LIKE', '%'.$date_from.'%')->distinct('item_id')->get();
            // $sh_dist = SalesHistory::where('del', 'no')->select('user_bv')->where('created_at', 'LIKE', '%'.$date_from.'%')->distinct('item_id')->get();
        }elseif (empty($date_from) && !empty($date_to)) {
            $nonedit = 'true';
            return redirect(url()->previous())->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }elseif (!empty($date_from) && !empty($date_to)) {
            $nonedit = 'true';
            $items = ItemAudit::where('del', 'no')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->paginate(10);
            $items_send = ItemAudit::where('del', 'no')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->get();
            $saleshistory_send = SalesHistory::where('del', 'no')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->get();
            $stock = SalesHistory::where('del', 'no')->select('item_id')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->distinct('item_id')->get();
        }else{
            Session::put('stockfill', 0);
            $nonedit = 'false';
            $items = Item::where('del', 'no')->paginate(10);
            $items_send = Item::where('del', 'no')->get();
            $saleshistory_send = SalesHistory::where('del', 'no')->where('created_at', 'LIKE', '%'.session('date_today').'%')->get();
            $stock = SalesHistory::where('del', 'no')->select('item_id')->where('created_at', 'LIKE', '%'.session('date_today').'%')->distinct('item_id')->get();
            // $items = ItemAudit::where('del', 'no')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->orderBy('id', 'desc')->paginate(10);
        }

        // return $sh_dist;

        Session::put('stock', $stock);
        Session::put('items', $items_send);
        Session::put('genstockbal', $saleshistory_send);
        Session::put('sales_history', $saleshistory_send);

        Session::put('date_from', $date_from);
        Session::put('date_to', $date_to);

        $cats = Category::All();
        $company = Company::find(1);
        $pass = [
            'c' => 1,
            'x' => 1,
            'y' => 1,
            't' => 1,
            'qtr' => 0,
            'qts' => 0,
            'tamt' => 0,
            'tprof' => 0,
            'exists' => 0,
            'qtr_tot' => 0,
            'stock' => $stock,
            'sales_history' => $saleshistory_send
        ];
        // if (session('stockfill') == 0) {
            return view('pages.dash.stockbalances')->with($pass);
        // } else {
        //     return view('pages.dash.stockfillinvoice')->with($pass);
        // }
        
    }

    public function saleshistory(Request $request){
        // return view('pages.dash.saleshistory'); 

        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        //
        $c = 1;
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        
        if (!empty($date_from) && empty($date_to)) {
            // return 12345;
            # code...
            $nonedit = 'true';
            $items = ItemAudit::where('del', 'no')->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->paginate(10);
            $items_send = ItemAudit::where('del', 'no')->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->get();
            $saleshistory_send = SalesHistory::where('del', 'no')->where('created_at', 'LIKE', '%'.$date_from.'%')->get();
        }elseif (empty($date_from) && !empty($date_to)) {
            $nonedit = 'true';
            return redirect(url()->previous())->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }elseif (!empty($date_from) && !empty($date_to)) {
            $nonedit = 'true';
            $items = ItemAudit::where('del', 'no')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->paginate(10);
            $items_send = ItemAudit::where('del', 'no')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->get();
            $saleshistory_send = SalesHistory::select('item_no', 'name', 'qty', 'cost_price', 'unit_price', 'tot', 'profits')->where('del', 'no')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->distinct('name')->get();
        }else{
            $nonedit = 'false';
            $items = Item::where('del', 'no')->paginate(10);
            $items_send = Item::where('del', 'no')->get();
            $saleshistory_send = SalesHistory::where('del', 'no')->where('created_at', 'LIKE', '%'.session('date_today').'%')->get();
            // $items = ItemAudit::where('del', 'no')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->orderBy('id', 'desc')->paginate(10);
        }

        Session::put('items', $items_send);
        Session::put('genstockbal', $saleshistory_send);

        Session::put('date_from', $date_from);
        Session::put('date_to', $date_to);

        $cats = Category::All();
        $company = Company::find(1);
        $pass = [
            'c' => 1,
            'y' => 1,
            'cats' => $cats,
            'items' => $items,
            'company' => $company,
            'nonedit' => $nonedit,
            'sales' => 3
        ];
        // $dist_category = Item::select('cat')->where($match)->where('img_count', '>', 1)->distinct()->get();
        return view('pages.dash.saleshistory')->with($pass);
    }

    public function expensereport(Request $request){

        // return 1234567;

        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        $exp_b1 = 0;
        $exp_b2 = 0;
        $exp_b3 = 0;
        $exp_b4 = 0;
        $exp_b5 = 0.0;
        // $exp_b6 = 0;
        // $exp_b7 = 0;

        $branch = $request->input('branch');
        if($branch == 'All Branches'){
            $match = ['del' => 'no'];
        }else{
            $match = ['del' => 'no', 'companybranch_id' => $branch];
        }

        $exp_b1_match = ['del' => 'no', 'companybranch_id' => 1 ];
        $exp_b2_match = ['del' => 'no', 'companybranch_id' => 2 ];
        $exp_b3_match = ['del' => 'no', 'companybranch_id' => 3 ];
        $exp_b4_match = ['del' => 'no', 'companybranch_id' => 4 ];
        $exp_b5_match = ['del' => 'no', 'companybranch_id' => 5 ];
        // $exp_b6_match = ['del' => 'no', 'companybranch_id' => 6 ];
        // $exp_b7_match = ['del' => 'no', 'companybranch_id' => 7 ];
        
        $c = 1;
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        
        if (!empty($date_from) && empty($date_to)) {
            // return 12345;
            # code...
            $expenses = Expense::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->paginate(10);
            $expenses_send = Expense::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->get();

            $exp_b1 = Expense::where($exp_b1_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            $exp_b2 = Expense::where($exp_b2_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            $exp_b3 = Expense::where($exp_b3_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            $exp_b4 = Expense::where($exp_b4_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            $exp_b5 = Expense::where($exp_b5_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            // $exp_b6 = Expense::where($exp_b6_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            // $exp_b7 = Expense::where($exp_b7_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
        }elseif (empty($date_from) && !empty($date_to)) {
            return redirect(url()->previous())->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }elseif (!empty($date_from) && !empty($date_to)) {
            // $expenses = Expense::where('user_id', auth()->user()->bv)->where('created_at', 'LIKE', '%'.session('date_today').'%');
            $expenses = Expense::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->paginate(10);
            $expenses_send = Expense::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->get();

            $exp_b1 = Expense::where($exp_b1_match)->where('companybranch_id', 1)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            $exp_b2 = Expense::where($exp_b2_match)->where('companybranch_id', 2)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            $exp_b3 = Expense::where($exp_b3_match)->where('companybranch_id', 3)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            $exp_b4 = Expense::where($exp_b4_match)->where('companybranch_id', 4)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            $exp_b5 = Expense::where($exp_b5_match)->where('companybranch_id', 5)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            // $exp_b6 = Expense::where($exp_b6_match)->where('companybranch_id', 6)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            // $exp_b7 = Expense::where($exp_b7_match)->where('companybranch_id', 7)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            // return $exp_b5;
        }else{
            $expenses = Expense::where('del', 'no')->orderBy('id', 'desc')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->paginate(10);
            $expenses_send = Expense::where('del', 'no')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->orderBy('id', 'desc')->get();
            // $items = ItemAudit::where('del', 'no')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->orderBy('id', 'desc')->paginate(10);

            $exp_b1 = Expense::where($exp_b1_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            $exp_b2 = Expense::where($exp_b2_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            $exp_b3 = Expense::where($exp_b3_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            $exp_b4 = Expense::where($exp_b4_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            $exp_b5 = Expense::where($exp_b5_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            // $exp_b6 = Expense::where($exp_b6_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            // $exp_b7 = Expense::where($exp_b7_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
        }

        Session::put('expenses', $expenses_send);
        $branches = CompanyBranch::all();

        Session::put('date_from', $date_from);
        Session::put('date_to', $date_to);
        Session::put('exp_b1', $exp_b1);
        Session::put('exp_b2', $exp_b2);
        Session::put('exp_b3', $exp_b3);
        Session::put('exp_b4', $exp_b4);
        Session::put('exp_b5', $exp_b5);
        // Session::put('exp_b6', $exp_b6);
        // Session::put('exp_b7', $exp_b7);

        $cats = Category::All();
        $company = Company::find(1);
        $pass = [
            'i' => 1,
            'y' => 1,
            'cats' => $cats,
            'expenses' => $expenses,
            'branches' => $branches,
            'company' => $company,
            'sales' => 3
        ];
        return view('pages.dash.expensereport')->with($pass);
    }

    public function debts(Request $request){

        // return 1234567;

        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }

        $branch = $request->input('branch');
        if($branch == 'All Branches'){
            $match = ['del' => 'no', 'pay_mode' => 'Post Payment(Debt)', 'paid' => 'no'];
        }else{
            $match = ['del' => 'no', 'user_bv' => $branch, 'pay_mode' => 'Post Payment(Debt)', 'paid' => 'no'];
        }
        
        $c = 1;
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        
        if (!empty($date_from) && empty($date_to)) {
            // return 12345;
            # code...
            $sales = Sale::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->paginate(10);
            $sales_send = Sale::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->get();
        }elseif (empty($date_from) && !empty($date_to)) {
            return redirect(url()->previous())->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }elseif (!empty($date_from) && !empty($date_to)) {
            // $expenses = Expense::where('user_id', auth()->user()->bv)->where('created_at', 'LIKE', '%'.session('date_today').'%');
            $sales = Sale::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->paginate(10);
            $sales_send = Sale::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->get();
        }else{
            $match = ['del' => 'no', 'pay_mode' => 'Post Payment(Debt)', 'paid' => 'no'];
            $sales = Sale::where($match)->orderBy('id', 'desc')->paginate(10);
            $sales_send = Sale::where($match)->orderBy('id', 'desc')->get();
            // $items = ItemAudit::where('del', 'no')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->orderBy('id', 'desc')->paginate(10);
        }

        Session::put('debts', $sales_send);
        $branches = CompanyBranch::all();

        Session::put('date_from', $date_from);
        Session::put('date_to', $date_to);

        $cats = Category::All();
        $company = Company::find(1);
        $pass = [
            'i' => 1,
            'c' => 1,
            'cats' => $cats,
            'sales' => $sales,
            'branches' => $branches,
            'company' => $company
        ];
        return view('pages.dash.debts')->with($pass);
    }

    public function waybillreport(Request $request){

        // return 1234567;

        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        $match = ['del' => 'no'];
        
        if (!empty($date_from) && empty($date_to)) {
            $waybills = Waybill::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->paginate(10);
            $waybills_send = Waybill::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->get();
        }elseif (empty($date_from) && !empty($date_to)) {
            return redirect(url()->previous())->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }elseif (!empty($date_from) && !empty($date_to)) {
            $waybills = Waybill::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->paginate(10);
            $waybills_send = Waybill::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->get();
        }else{
            // $match = ['del' => 'no'];
            $waybills = Waybill::where($match)->orderBy('id', 'desc')->paginate(10);
            $waybills_send = Waybill::where($match)->orderBy('id', 'desc')->get();
        }

        Session::put('waybillreps', $waybills_send);
        Session::put('date_from', $date_from);
        Session::put('date_to', $date_to);

        $cats = Category::All();
        $company = Company::find(1);
        $pass = [
            'i' => 1,
            'c' => 1,
            'cats' => $cats,
            'waybills' => $waybills,
            'company' => $company
        ];
        return view('pages.dash.waybillreport')->with($pass);
    }

    public function returnsreport(Request $request){

        // return 1234567;

        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        $branch = $request->query('branch');
        if($branch == 'All Branches'){
            $match = ['del' => 'no'];
        }else{
            $match = ['del' => 'no', 'user_bv' => $branch,];
        }
        
        if (!empty($date_from) && empty($date_to)) {
            $returns = OrderReturn::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->paginate(10);
            $returns_send = OrderReturn::where($match)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->get();
        }elseif (empty($date_from) && !empty($date_to)) {
            return redirect(url()->previous())->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }elseif (!empty($date_from) && !empty($date_to)) {
            $returns = OrderReturn::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->paginate(10);
            $returns_send = OrderReturn::where($match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->get();
        }else{
            // $match = ['del' => 'no'];
            $returns = OrderReturn::where($match)->orderBy('id', 'desc')->paginate(10);
            $returns_send = OrderReturn::where($match)->orderBy('id', 'desc')->get();
        }

        Session::put('returnsrep', $returns_send);
        Session::put('date_from', $date_from);
        Session::put('date_to', $date_to);

        $cats = Category::All();
        $company = Company::find(1);
        $pass = [
            'i' => 1,
            'c' => 1,
            'cats' => $cats,
            'returns' => $returns,
            'company' => $company,
            'branches' => CompanyBranch::all(),
        ];
        return view('pages.dash.returns')->with($pass);
    }

    public function distreport(Request $request){

        // return 1234567;

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
        // return $wbds;

        $cats = Category::All();
        $company = Company::find(1);
        $pass = [
            'i' => 1,
            'c' => 1,
            'sum' => 0,
            'cats' => $cats,
            'wbdreports' => $wbds,
        ];
        return view('pages.dash.distreport')->with($pass);
        
    }

    public function closure(Request $request){

        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        return view('pages.dash.closure');
    }

}
