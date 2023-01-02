<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Closure as Closures;
use App\Models\Company;
use App\Models\Category;
use App\Models\CompanyBranch;
use Session;
use Auth;

class load_auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->del == 'yes') {
            auth()->logout();
            return redirect('/')->with('error', 'Oops..! Access Denied');
        }
        // if (Auth::check()) {
        // }else {

            $cl = Closures::where('month', date('Y-m-01'))->latest()->first();
            Session::put('cl', $cl);
            if ($cl == '' && auth()->user()->status != 'Administrator') {
                return redirect('/')->with('error', 'Oops..! Contact administrator to initialize '.date('F, Y').' opening');
            }
            

            $b1 = CompanyBranch::where('tag', 1)->first();
            $b2 = CompanyBranch::where('tag', 2)->first();
            $b3 = CompanyBranch::where('tag', 3)->first();
            $b4 = CompanyBranch::where('tag', 4)->first();
            $b5 = CompanyBranch::where('tag', 5)->first();

            Session::put('branch_A', $b1->name);
            Session::put('branch_B', $b2->name);
            Session::put('branch_C', $b3->name);
            Session::put('branch_D', $b3->name);
            Session::put('branch_E', $b3->name);

            Session::put('branch_1', $b1->name);
            Session::put('branch_2', $b2->name);
            Session::put('branch_3', $b3->name);
            Session::put('branch_4', $b3->name);
            Session::put('branch_5', $b3->name);
            
            $cats = Category::All();
            $company = Company::find(1);
            $branch = CompanyBranch::all();

            Session::put('cats', $cats);
            Session::put('company', $company);
            Session::put('compbranch', $branch);
            return $next($request);
        // }
    }
}
