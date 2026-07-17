<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Category;
use App\Models\CompanyBranch;
use App\Services\ClosureService;
use Session;

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

        $closureService = app(ClosureService::class);
        $cl = $closureService->currentMonthClosure();

        Session::put('cl', $cl);
        Session::put('sales_permit', $closureService->salesPermitFor(auth()->user()));

        $branchLabels = ['A', 'B', 'C', 'D', 'E'];
        for ($tag = 1; $tag <= 5; $tag++) {
            $branch = CompanyBranch::where('tag', $tag)->first();
            $name = $branch ? $branch->name : '';
            Session::put('branch_'.$tag, $name);
            Session::put('branch_'.$branchLabels[$tag - 1], $name);
        }

        Session::put('cats', Category::all());
        Session::put('company', Company::find(1));
        Session::put('compbranch', CompanyBranch::all());

        return $next($request);
    }
}
