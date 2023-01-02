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
use App\Models\OrderReturn;
use App\Models\SalesHistory;
use App\Models\CompanyBranch;
use Exception;
use Session;
use DateTime;

class ReportsController extends Controller
{
    public function __construct(){
        $this->middleware(['auth', 'load_auth']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $soh = $request->query('soh');

        if ($soh == '1') {
            # Sales History Report...
            return 123424;

            $date_from = $request->query('date_from');
            $date_to = $request->query('date_to');
            $branch = $request->query('branch');
            $delvr = $request->query('delvr');

            if ($branch == 'All Branches') {
                if ($delvr == 'Del. / Not Delivered') {
                    $del = ['del' => 'no'];
                    // return $delvr;
                } else {
                    $del = ['del' => 'no', 'del_status' => $delvr ];
                }
                $exp_del = ['del' => 'no'];
                Session::put('branch', 'All');
            } else {
                Session::put('branch', $branch);
                if ($delvr == 'Del. / Not Delivered') {
                    $del = ['del' => 'no', 'user_bv' => $branch ];
                } else {
                    $del = ['del' => 'no', 'del_status' => $delvr, 'user_bv' => $branch ];
                }
            }
        }
        

        # Sales Report...
        if(auth()->user()->status != 'Administrator'){
            return redirect('/dashboard'); 
        }
        if(Session::get('date_today') == ''){
            Session::put('date_today', date('Y-m-d'));
        }
        //
        $c = 1;
        $date_from = $request->query('date_from');
        $date_to = $request->query('date_to');
        $branch = $request->query('branch');
        $delvr = $request->query('delvr');
        Session::put('branch', 'All Branches');

        if ($branch == 'All Branches') {
            if ($delvr == 'Del. / Not Delivered') {
                $del = ['del' => 'no'];
                // return $delvr;
            } else {
                $del = ['del' => 'no', 'del_status' => $delvr ];
            }
            $exp_del = ['del' => 'no'];
            Session::put('branch', 'All');
        } else {
            Session::put('branch', $branch);
            if ($delvr == 'Del. / Not Delivered') {
                $del = ['del' => 'no', 'user_bv' => $branch ];
            } else {
                $del = ['del' => 'no', 'del_status' => $delvr, 'user_bv' => $branch ];
            }
            $exp_del = ['del' => 'no', 'companybranch_id' => $branch ];
            $cash_b1 = 0;
            $cash_b2 = 0;
            $cash_b3 = 0;
            $cash_b4 = 0;
            $cash_b5 = 0;
            // $cash_b6 = 0;
            // $cash_b7 = 0;

            $cheque_b1 = 0;
            $cheque_b2 = 0;
            $cheque_b3 = 0;
            $cheque_b4 = 0;
            $cheque_b5 = 0;
            // $cheque_b6 = 0;
            // $cheque_b7 = 0;

            $momo_b1 = 0;
            $momo_b2 = 0;
            $momo_b3 = 0;
            $momo_b4 = 0;
            $momo_b5 = 0;
            // $momo_b6 = 0;
            // $momo_b7 = 0;

            $debt_b1 = 0;
            $debt_b2 = 0;
            $debt_b3 = 0;
            $debt_b4 = 0;
            $debt_b5 = 0;
            // $debt_b6 = 0;
            // $debt_b7 = 0;
        }
        
        $b1_match = ['del' => 'no', 'user_bv' => 1 ];
        $b2_match = ['del' => 'no', 'user_bv' => 2 ];
        $b3_match = ['del' => 'no', 'user_bv' => 3 ];
        $b4_match = ['del' => 'no', 'user_bv' => 4 ];
        $b5_match = ['del' => 'no', 'user_bv' => 5 ];
        // $b6_match = ['del' => 'no', 'user_bv' => 6 ];
        // $b7_match = ['del' => 'no', 'user_bv' => 7 ];
        $exp_b1_match = ['del' => 'no', 'companybranch_id' => 1 ];
        $exp_b2_match = ['del' => 'no', 'companybranch_id' => 2 ];
        $exp_b3_match = ['del' => 'no', 'companybranch_id' => 3 ];
        $exp_b4_match = ['del' => 'no', 'companybranch_id' => 4 ];
        $exp_b5_match = ['del' => 'no', 'companybranch_id' => 5 ];
        // $exp_b6_match = ['del' => 'no', 'companybranch_id' => 6 ];
        // $exp_b7_match = ['del' => 'no', 'companybranch_id' => 7 ];

        // if ($request->filled('date_from')) {
        if (!empty($date_from) && empty($date_to)) {
            # code...
            $sales = Sale::where($del)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->paginate(10);
            $sales_send = Sale::where($del)->where('created_at', 'LIKE', '%'.$date_from.'%')->orderBy('id', 'desc')->get();
            // Get Money Sums
            $cash = Sale::where($del)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            $cheque = Sale::where($del)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            $momo = Sale::where($del)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            $sum_dbt = Sale::where($del)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');

            if ($branch == 1 || $branch == 'All Branches') {
                $cash_b1 = Sale::where($b1_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $cheque_b1 = Sale::where($b1_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $momo_b1 = Sale::where($b1_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $debt_b1 = Sale::where($b1_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            }
            if ($branch == 2 || $branch == 'All Branches') {
                $cash_b2 = Sale::where($b2_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $cheque_b2 = Sale::where($b2_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $momo_b2 = Sale::where($b2_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $debt_b2 = Sale::where($b2_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            }
            if ($branch == 3 || $branch == 'All Branches') {
                $cash_b3 = Sale::where($b3_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $cheque_b3 = Sale::where($b3_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $momo_b3 = Sale::where($b3_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $debt_b3 = Sale::where($b3_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            }
            if ($branch == 4 || $branch == 'All Branches') {
                $cash_b4 = Sale::where($b4_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $cheque_b4 = Sale::where($b4_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $momo_b4 = Sale::where($b4_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $debt_b4 = Sale::where($b4_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            }
            if ($branch == 5 || $branch == 'All Branches') {
                $cash_b5 = Sale::where($b5_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $cheque_b5 = Sale::where($b5_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $momo_b5 = Sale::where($b5_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
                $debt_b5 = Sale::where($b5_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            }
            // if ($branch == 6 || $branch == 'All Branches') {
            //     $cash_b6 = Sale::where($b6_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            //     $cheque_b6 = Sale::where($b6_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            //     $momo_b6 = Sale::where($b6_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            //     $debt_b6 = Sale::where($b6_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            // }
            // if ($branch == 7 || $branch == 'All Branches') {
            //     $cash_b7 = Sale::where($b7_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            //     $cheque_b7 = Sale::where($b7_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            //     $momo_b7 = Sale::where($b7_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            //     $debt_b7 = Sale::where($b7_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            // }

            // $br_det = [];
            // for ($i = 1; $i < 8; $i++) { 
            //     # code...
            //     // if ($branch == 5 || $branch == 'All Branches') {
            //     $match = ['del' => 'no', 'user_bv' => $i ]; 
            //     $c1 = Sale::where($match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            //     $c2 = Sale::where($match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            //     $c3 = Sale::where($match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            //     $c4 = Sale::where($match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            
            //     array_push($br_det, [
            //         'cash' => $c1,
            //         'cheque' => $c2,
            //         'momo' => $c3,
            //         'debt' => $c4,
            //     ]);
            // }
            // Session::put('br_det', $br_det);

            // Get Sum Branch 1 - 3
            $b1 = Sale::where($b1_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            $b2 = Sale::where($b2_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            $b3 = Sale::where($b3_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            $b4 = Sale::where($b4_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            $b5 = Sale::where($b5_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            // $b6 = Sale::where($b6_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');
            // $b7 = Sale::where($b7_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('tot');

            $expenses = Expense::where('del', 'no')->where('created_at', 'LIKE', '%'.$date_from.'%')->get();

            // Get Exp Branch 1 - 3
            $exp_b1 = Expense::where($exp_b1_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            $exp_b2 = Expense::where($exp_b2_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            $exp_b3 = Expense::where($exp_b3_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            $exp_b4 = Expense::where($exp_b4_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            $exp_b5 = Expense::where($exp_b5_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            // $exp_b6 = Expense::where($exp_b6_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');
            // $exp_b7 = Expense::where($exp_b7_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('expense_cost');

            // Get Gen profits
            $gp_match = ['del' => 'no'];
            $gen_profits = SalesHistory::where($gp_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('profits');
            // Get Profits Branch 1 - 3
            $b1_profits = SalesHistory::where($b1_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('profits');
            $b2_profits = SalesHistory::where($b2_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('profits');
            $b3_profits = SalesHistory::where($b3_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('profits');
            $b4_profits = SalesHistory::where($b4_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('profits');
            $b5_profits = SalesHistory::where($b5_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('profits');
            // $b6_profits = SalesHistory::where($b6_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('profits');
            // $b7_profits = SalesHistory::where($b7_match)->where('created_at', 'LIKE', '%'.$date_from.'%')->sum('profits');

        }elseif (empty($date_from) && !empty($date_to)) {
            return redirect('/reporting')->with('error', 'Oops..! Provide *Date From* in order to proceed');

        }elseif (!empty($date_from) && !empty($date_to)) {

            $sales = Sale::where($del)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->paginate(10);
            $sales_send = Sale::where($del)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->orderBy('id', 'desc')->get();
            // Get Money Sums
            $cash = Sale::where($del)->where('pay_mode', 'cash')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            $cheque = Sale::where($del)->where('pay_mode', 'cheque')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            $momo = Sale::where($del)->where('pay_mode', 'Mobile Money')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            $sum_dbt = Sale::where($del)->where('pay_mode', 'Post Payment(Debt)')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');

            if ($branch == 1 || $branch == 'All Branches') {
                $cash_b1 = Sale::where($b1_match)->where('pay_mode', 'cash')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $cheque_b1 = Sale::where($b1_match)->where('pay_mode', 'cheque')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $momo_b1 = Sale::where($b1_match)->where('pay_mode', 'Mobile Money')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $debt_b1 = Sale::where($b1_match)->where('pay_mode', 'Post Payment(Debt)')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            }
            if ($branch == 2 || $branch == 'All Branches') {
                $cash_b2 = Sale::where($b2_match)->where('pay_mode', 'cash')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $cheque_b2 = Sale::where($b2_match)->where('pay_mode', 'cheque')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $momo_b2 = Sale::where($b2_match)->where('pay_mode', 'Mobile Money')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $debt_b2 = Sale::where($b2_match)->where('pay_mode', 'Post Payment(Debt)')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            }
            if ($branch == 3 || $branch == 'All Branches') {
                $cash_b3 = Sale::where($b3_match)->where('pay_mode', 'cash')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $cheque_b3 = Sale::where($b3_match)->where('pay_mode', 'cheque')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $momo_b3 = Sale::where($b3_match)->where('pay_mode', 'Mobile Money')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $debt_b3 = Sale::where($b3_match)->where('pay_mode', 'Post Payment(Debt)')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            }
            if ($branch == 4 || $branch == 'All Branches') {
                $cash_b4 = Sale::where($b4_match)->where('pay_mode', 'cash')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $cheque_b4 = Sale::where($b4_match)->where('pay_mode', 'cheque')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $momo_b4 = Sale::where($b4_match)->where('pay_mode', 'Mobile Money')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $debt_b4 = Sale::where($b4_match)->where('pay_mode', 'Post Payment(Debt)')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            }
            if ($branch == 5 || $branch == 'All Branches') {
                $cash_b5 = Sale::where($b5_match)->where('pay_mode', 'cash')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $cheque_b5 = Sale::where($b5_match)->where('pay_mode', 'cheque')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $momo_b5 = Sale::where($b5_match)->where('pay_mode', 'Mobile Money')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
                $debt_b5 = Sale::where($b5_match)->where('pay_mode', 'Post Payment(Debt)')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            }
            // if ($branch == 6 || $branch == 'All Branches') {
            //     $cash_b6 = Sale::where($b6_match)->where('pay_mode', 'cash')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            //     $cheque_b6 = Sale::where($b6_match)->where('pay_mode', 'cheque')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            //     $momo_b6 = Sale::where($b6_match)->where('pay_mode', 'Mobile Money')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            //     $debt_b6 = Sale::where($b6_match)->where('pay_mode', 'Post Payment(Debt)')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            // }
            // if ($branch == 7 || $branch == 'All Branches') {
            //     $cash_b7 = Sale::where($b7_match)->where('pay_mode', 'cash')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            //     $cheque_b7 = Sale::where($b7_match)->where('pay_mode', 'cheque')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            //     $momo_b7 = Sale::where($b7_match)->where('pay_mode', 'Mobile Money')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            //     $debt_b7 = Sale::where($b7_match)->where('pay_mode', 'Post Payment(Debt)')->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            // }

            // Get sum Branch 1
            $b1 = Sale::where($b1_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            // Get sum Branch 2
            $b2 = Sale::where($b2_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            // Get sum Branch 3
            $b3 = Sale::where($b3_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            $b4 = Sale::where($b4_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            $b5 = Sale::where($b5_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            // $b6 = Sale::where($b6_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');
            // $b7 = Sale::where($b7_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('tot');

            $expenses = Expense::where($exp_del)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->get();
            
            $exp_b1 = Expense::where($exp_b1_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            $exp_b2 = Expense::where($exp_b2_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            $exp_b3 = Expense::where($exp_b3_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            $exp_b4 = Expense::where($exp_b4_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            $exp_b5 = Expense::where($exp_b5_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            // $exp_b6 = Expense::where($exp_b6_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');
            // $exp_b7 = Expense::where($exp_b7_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('expense_cost');

            // Get general profits
            $gp_match = ['del' => 'no'];
            $gen_profits = SalesHistory::where($gp_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('profits');

            $b1_profits = SalesHistory::where($b1_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('profits');
            $b2_profits = SalesHistory::where($b2_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('profits');
            $b3_profits = SalesHistory::where($b3_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('profits');
            $b4_profits = SalesHistory::where($b4_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('profits');
            $b5_profits = SalesHistory::where($b5_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('profits');
            // $b6_profits = SalesHistory::where($b6_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('profits');
            // $b7_profits = SalesHistory::where($b7_match)->whereBetween('created_at', [$date_from, new \DateTime($date_to.'+1 day')])->sum('profits');

        }else{
            
            $sales = Sale::where('del', 'no')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->orderBy('id', 'desc')->paginate(10);
            $sales_send = Sale::where('del', 'no')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->orderBy('id', 'desc')->get();
            // Get Money Sums
            $cash = Sale::where('del', 'no')->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $cheque = Sale::where('del', 'no')->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $momo = Sale::where('del', 'no')->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $sum_dbt = Sale::where('del', 'no')->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');

            if ($branch == 1 || $branch == 'All Branches') {
            }
            if ($branch == 2 || $branch == 'All Branches') {
            }
            if ($branch == 3 || $branch == 'All Branches') {
            }
            $cash_b1 = Sale::where($b1_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $cash_b2 = Sale::where($b2_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $cash_b3 = Sale::where($b3_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $cash_b4 = Sale::where($b4_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $cash_b5 = Sale::where($b5_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            // $cash_b6 = Sale::where($b6_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            // $cash_b7 = Sale::where($b7_match)->where('pay_mode', 'cash')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');

            $cheque_b1 = Sale::where($b1_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $cheque_b2 = Sale::where($b2_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $cheque_b3 = Sale::where($b3_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $cheque_b4 = Sale::where($b4_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $cheque_b5 = Sale::where($b5_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            // $cheque_b6 = Sale::where($b6_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            // $cheque_b7 = Sale::where($b7_match)->where('pay_mode', 'cheque')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');

            $momo_b1 = Sale::where($b1_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $momo_b2 = Sale::where($b2_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $momo_b3 = Sale::where($b3_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $momo_b4 = Sale::where($b4_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $momo_b5 = Sale::where($b5_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            // $momo_b6 = Sale::where($b6_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            // $momo_b7 = Sale::where($b7_match)->where('pay_mode', 'Mobile Money')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');

            $debt_b1 = Sale::where($b1_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $debt_b2 = Sale::where($b2_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $debt_b3 = Sale::where($b3_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $debt_b4 = Sale::where($b4_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            $debt_b5 = Sale::where($b5_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            // $debt_b6 = Sale::where($b6_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');
            // $debt_b7 = Sale::where($b7_match)->where('pay_mode', 'Post Payment(Debt)')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('tot');

            // Get sum Branch 1
            $b1 = Sale::where($b1_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('tot');
            $b2 = Sale::where($b2_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('tot');
            $b3 = Sale::where($b3_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('tot');
            $b4 = Sale::where($b4_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('tot');
            $b5 = Sale::where($b5_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('tot');
            // $b6 = Sale::where($b6_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('tot');
            // $b7 = Sale::where($b7_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('tot');
            $expenses = Expense::where('del', 'no')->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->get();

            $exp_b1 = Expense::where($exp_b1_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            $exp_b2 = Expense::where($exp_b2_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            $exp_b3 = Expense::where($exp_b3_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            $exp_b4 = Expense::where($exp_b4_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            $exp_b5 = Expense::where($exp_b5_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            // $exp_b6 = Expense::where($exp_b6_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');
            // $exp_b7 = Expense::where($exp_b7_match)->where('created_at', 'LIKE', '%'.date("Y-m-d").'%')->sum('expense_cost');


            // Get general profits
            $gp_match = ['del' => 'no'];
            $gen_profits = SalesHistory::where($gp_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('profits');

            $b1_profits = SalesHistory::where($b1_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('profits');
            $b2_profits = SalesHistory::where($b2_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('profits');
            $b3_profits = SalesHistory::where($b3_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('profits');
            $b4_profits = SalesHistory::where($b4_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('profits');
            $b5_profits = SalesHistory::where($b5_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('profits');
            // $b6_profits = SalesHistory::where($b6_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('profits');
            // $b7_profits = SalesHistory::where($b7_match)->where('created_at', 'LIKE', '%'.Session::get('date_today').'%')->sum('profits');

        }

        // return session('br_det');

        $gross = $cash + $cheque + $momo + $sum_dbt;
        $net = $gross - $expenses->sum('expense_cost');

            // Set session variables
            Session::put('b1_profits', $b1_profits);
            Session::put('b2_profits', $b2_profits);
            Session::put('b3_profits', $b3_profits);
            Session::put('b4_profits', $b4_profits);
            Session::put('b5_profits', $b5_profits);
            // Session::put('b6_profits', $b6_profits);
            // Session::put('b7_profits', $b7_profits);

            Session::put('gen_profits', $gen_profits);
            Session::put('b1', $b1);
            Session::put('b2', $b2);
            Session::put('b3', $b3);
            Session::put('b4', $b4);
            Session::put('b5', $b5);
            // Session::put('b6', $b6);
            // Session::put('b7', $b7);

            Session::put('exp_b1', $exp_b1);
            Session::put('exp_b2', $exp_b2);
            Session::put('exp_b3', $exp_b3);
            Session::put('exp_b4', $exp_b4);
            Session::put('exp_b5', $exp_b5);
            // Session::put('exp_b6', $exp_b6);
            // Session::put('exp_b7', $exp_b7);

            Session::put('gross', $gross);
            Session::put('net', $net);
            Session::put('sales', $sales_send);
            Session::put('cash', $cash);
            Session::put('cheque', $cheque);
            Session::put('momo', $momo);
            Session::put('sum_dbt', $sum_dbt);
            // Session::put('cash_b1', $cash);
            // Session::put('cash_b2', $cash);
            // Session::put('cash_b3', $cash);
            // Session::put('cheque_b1', $cheque);
            // Session::put('cheque_b2', $cheque);
            // Session::put('cheque_b3', $cheque);
            Session::put('expenses', $expenses);
            Session::put('date_from', $date_from);
            Session::put('date_to', $date_to);

            $company_branch = CompanyBranch::all();

        $pass = [
            'c' => $c, 
            'b1' => $b1, 
            'b2' => $b2, 
            'b3' => $b3, 
            'b4' => $b4, 
            'b5' => $b5, 
            // 'b6' => $b6, 
            // 'b7' => $b7, 

            'exp_b1' => $exp_b1, 
            'exp_b2' => $exp_b2, 
            'exp_b3' => $exp_b3, 
            'exp_b4' => $exp_b4, 
            'exp_b5' => $exp_b5, 
            // 'exp_b6' => $exp_b6, 
            // 'exp_b7' => $exp_b7, 

            'cash' => $cash,
            'cheque' => $cheque,
            'momo' => $momo,
            'sum_dbt' => $sum_dbt,
            'cash_b1' => $cash_b1,
            'cash_b2' => $cash_b2,
            'cash_b3' => $cash_b3,
            'cash_b4' => $cash_b4,
            'cash_b5' => $cash_b5,
            // 'cash_b6' => $cash_b6,
            // 'cash_b7' => $cash_b7,

            'cheque_b1' => $cheque_b1,
            'cheque_b2' => $cheque_b2,
            'cheque_b3' => $cheque_b3,
            'cheque_b4' => $cheque_b4,
            'cheque_b5' => $cheque_b5,
            // 'cheque_b6' => $cheque_b6,
            // 'cheque_b7' => $cheque_b7,

            'momo_b1' => $momo_b1,
            'momo_b2' => $momo_b2,
            'momo_b3' => $momo_b3,
            'momo_b4' => $momo_b4,
            'momo_b5' => $momo_b5,
            // 'momo_b6' => $momo_b6,
            // 'momo_b7' => $momo_b7,

            'debt_b1' => $debt_b1,
            'debt_b2' => $debt_b2,
            'debt_b3' => $debt_b3,
            'debt_b4' => $debt_b4,
            'debt_b5' => $debt_b5,
            // 'debt_b6' => $debt_b6,
            // 'debt_b7' => $debt_b7,

            'sales' => $sales, 
            'branches' => $company_branch, 
            'b1_profits' => $b1_profits,
            'b2_profits' => $b2_profits,
            'b3_profits' => $b3_profits, 
            'b4_profits' => $b4_profits, 
            'b5_profits' => $b5_profits, 
            // 'b6_profits' => $b6_profits, 
            // 'b7_profits' => $b7_profits, 

            'gen_profits' => $gen_profits,
            'expenses' => $expenses
        ];
        return view('pages.dash.reportsview')->with($pass);
    
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
        $order = Sale::find($id);
        $sales = $order->saleshistory->all();
        $user = User::find($order->user_id);

        $company = Company::find(1);
        $pass = [
            'count' => 1,  
            'count2' => 1,
            'user' => $user,
            'order' => $order,
            'company' => $company,
            'sales' => $sales
        ];

        return view('pages.dash.single_invoice')->with($pass);

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
        // $sh = SalesHistory::find($id);
        // return $id;

        $SalesHistory = SalesHistory::where('sale_id', $id)->get();
        foreach ($SalesHistory as $sh) {
            # code...
            $OrderReturn = OrderReturn::firstOrCreate([
                'user_id' => $sh->user_id,
                'sale_id' => $sh->sale_id,
                'item_id' => $sh->item_id,
                'user_bv' => $sh->user_bv,
                'item_no' => $sh->item_no,
                'name' => $sh->name,
                'qty' => $sh->qty,
                'cost_price' => $sh->cost_price,
                'unit_price' => $sh->unit_price,
                'profits' => $sh->profits,
                'tot' => $sh->tot,
                'del_status' => $sh->del,
                'order_date' => $sh->created_at,
            ]);

            $new_qty = 'q'.$sh->user_bv;

            // Save to OrderReturn
            $item = Item::find($sh->item_id);
            $item->$new_qty = $item->$new_qty + $sh->qty;
            $item->save();

            // Delete from SalesHistory
            $sh->delete();
            
        }

            // Delete from Sales
            $sales = Sale::find($id);
            $sales->delete();

        // return $SalesHistory;
        return redirect(url()->previous());
        // return $id;
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
