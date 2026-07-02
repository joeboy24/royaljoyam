<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\CompanyBranch;
use Session;

class PagesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'load_auth'], ['except' => ['index', 'try']]);
    }

    public function index()
    {
        if (! Session::has('sales')) {
            Session::put('b1', '');
            Session::put('b2', '');
            Session::put('b3', '');
            Session::put('gross', '');
            Session::put('net', '');
            Session::put('sales', '');
            Session::put('cash', '');
            Session::put('cheque', '');
            Session::put('momo', '');
            Session::put('sum_dbt', '');
            Session::put('expenses', '');
            Session::put('date_today', date('Y-m-d'));
        }

        return redirect('/dashboard');
    }

    public function expenses()
    {
        if (session('sales_permit') == 0) {
            return redirect('/dashboard')->with('error', 'Oops..! Contact administrator to initialize '.date('F, Y').' opening');
        }

        $match = [
            'del' => 'no',
            'companybranch_id' => auth()->user()->bv,
        ];

        $pass = [
            'i' => 1,
            'branches' => CompanyBranch::all(),
            'genexp' => Expense::where($match)->where('created_at', 'LIKE', '%'.date('Y-m').'%')->get(),
            'expenses' => Expense::where($match)->where('created_at', 'LIKE', '%'.date('Y-m').'%')->orderBy('id', 'DESC')->paginate(20),
        ];

        return view('pages.dash.expenses')->with($pass);
    }

    public function try()
    {
        return 1234;
    }
}
