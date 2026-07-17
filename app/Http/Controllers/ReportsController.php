<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBranch;
use App\Models\Sale;
use App\Models\User;
use App\Services\OrderReturnService;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use RuntimeException;

class ReportsController extends Controller
{
    public function __construct(
        protected SalesReportService $salesReportService,
        protected OrderReturnService $orderReturnService
    ) {
        $this->middleware(['auth', 'load_auth']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        if (Session::get('date_today') == '') {
            Session::put('date_today', date('Y-m-d'));
        }

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if (empty($dateFrom) && ! empty($dateTo)) {
            return redirect('/reporting')->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }

        $branch = $request->query('branch', 'All Branches');
        $delvr = $request->query('delvr', 'Del. / Not Delivered');

        $report = $this->salesReportService->build([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'branch' => $branch,
            'delvr' => $delvr,
            'session_sales_date' => Session::get('date_today'),
        ]);

        Session::put('branch', $report['session_branch']);

        $legacy = $this->salesReportService->toLegacyViewData($report);

        Session::put('b1_profits', $legacy['b1_profits']);
        Session::put('b2_profits', $legacy['b2_profits']);
        Session::put('b3_profits', $legacy['b3_profits']);
        Session::put('b4_profits', $legacy['b4_profits']);
        Session::put('b5_profits', $legacy['b5_profits']);
        Session::put('gen_profits', $report['gen_profits']);
        Session::put('b1', $legacy['b1']);
        Session::put('b2', $legacy['b2']);
        Session::put('b3', $legacy['b3']);
        Session::put('b4', $legacy['b4']);
        Session::put('b5', $legacy['b5']);
        Session::put('exp_b1', $legacy['exp_b1']);
        Session::put('exp_b2', $legacy['exp_b2']);
        Session::put('exp_b3', $legacy['exp_b3']);
        Session::put('exp_b4', $legacy['exp_b4']);
        Session::put('exp_b5', $legacy['exp_b5']);
        Session::put('gross', $report['gross']);
        Session::put('net', $report['net']);
        Session::put('sales', $report['sales_send']);
        Session::put('cash', $report['cash']);
        Session::put('cheque', $report['cheque']);
        Session::put('momo', $report['momo']);
        Session::put('sum_dbt', $report['sum_dbt']);
        Session::put('expenses', $report['expenses']);
        Session::put('date_from', $dateFrom);
        Session::put('date_to', $dateTo);

        return view('pages.dash.reportsview')->with(array_merge(
            [
                'c' => 1,
                'sales' => $report['sales'],
                'branches' => CompanyBranch::all(),
                'breakdown' => $this->salesReportService->buildBreakdownTable($report),
            ],
            $legacy
        ));
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
            'sales' => $sales,
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
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        try {
            $this->orderReturnService->returnSale((int) $id);
        } catch (RuntimeException $exception) {
            return redirect(url()->previous())->with('error', $exception->getMessage());
        }

        return redirect(url()->previous())->with('success', 'Order return successfull');
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
