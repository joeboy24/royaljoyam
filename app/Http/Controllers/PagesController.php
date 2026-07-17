<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

    public function try()
    {
        return 1234;
    }
}
