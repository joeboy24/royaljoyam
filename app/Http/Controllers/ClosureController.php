<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\SalesHistory;
use App\Models\Wbdistribution;
use App\Models\Closure;
use App\Services\ClosureSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use DateTime;

class ClosureController extends Controller
{
    public function __construct()
    {
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
     * @param  string  $full_date
     * @return \Illuminate\Http\Response
     */
    public function show($full_date)
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        $parsed = DateTime::createFromFormat('d-m-Y', $full_date)
            ?: DateTime::createFromFormat('Y-m-d', $full_date);

        if (!$parsed) {
            return redirect('/closure_page')->with('error', 'Invalid month selected.');
        }

        $date_from = $parsed->format('Y-m-01');
        $date_to = $parsed->format('Y-m-t');
        $rangeEnd = $date_to.' 23:59:59';

        $closure = Closure::where('month', $date_from)->latest()->first();
        $closure_state = $closure?->status ?? '';

        Session::put('mth_openning', $closure ? 1 : 0);

        $items = Item::where('del', 'no')->get();
        $salesHistory = SalesHistory::with('item')
            ->where('del', 'no')
            ->whereBetween('created_at', [$date_from, $rangeEnd])
            ->get();
        $distributions = Wbdistribution::with('item')
            ->where('del', 'no')
            ->whereBetween('created_at', [$date_from, $rangeEnd])
            ->get();

        // Kept for ItemsController open/close until that logic moves to a dedicated service.
        Session::put('stock', $salesHistory->unique('item_id')->values());
        Session::put('items', $items);
        Session::put('sales_history', $salesHistory);
        Session::put('cldate', $date_from);

        $summary = app(ClosureSummaryService::class)->build($salesHistory, $distributions, $items);

        $status = match ($closure_state) {
            'open' => 'open',
            'closed' => 'closed',
            default => 'not_opened',
        };

        return view('pages.dash.closuredetail', [
            'yr' => (int) $parsed->format('Y'),
            'month_label' => $parsed->format('F, Y'),
            'date_from' => $date_from,
            'date_to' => $date_to,
            'closure' => $closure,
            'closure_status' => $status,
            'closure_state' => $closure_state,
            'is_opened' => (bool) $closure,
            'branches' => $summary['branches'],
            'column_keys' => $summary['column_keys'],
            'summary' => $summary['summary'],
            'branch_summaries' => $summary['branch_summaries'],
            'distribution_rows' => $summary['distribution_rows'],
            'distribution_totals' => $summary['distribution_totals'],
            'sales_rows' => $summary['sales_rows'],
            'sales_totals' => $summary['sales_totals'],
        ]);
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
