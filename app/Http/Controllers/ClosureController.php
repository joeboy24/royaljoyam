<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\SalesHistory;
use App\Models\Wbdistribution;
use App\Models\Closure;
use App\Services\ClosureService;
use App\Services\ClosureSummaryService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class ClosureController extends Controller
{
    public function __construct(
        private readonly ClosureService $closureService,
        private readonly ClosureSummaryService $summaryService
    ) {
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

        try {
            $date_from = $this->closureService->resolveMonthKey($full_date);
        } catch (InvalidArgumentException $e) {
            return redirect('/closure_page')->with('error', $e->getMessage());
        }

        $date_to = date('Y-m-t', strtotime($date_from));
        $rangeEnd = $date_to.' 23:59:59';

        $closure = Closure::where('month', $date_from)->latest()->first();
        $closure_state = $closure?->status ?? '';

        $items = Item::where('del', 'no')->get();
        $salesHistory = SalesHistory::with('item')
            ->where('del', 'no')
            ->whereBetween('created_at', [$date_from, $rangeEnd])
            ->get();
        $distributions = Wbdistribution::with('item')
            ->where('del', 'no')
            ->whereBetween('created_at', [$date_from, $rangeEnd])
            ->get();

        $summary = $this->summaryService->build($salesHistory, $distributions, $items);

        $status = match ($closure_state) {
            'open' => 'open',
            'closed' => 'closed',
            default => 'not_opened',
        };

        return view('pages.dash.closuredetail', [
            'yr' => (int) date('Y', strtotime($date_from)),
            'month_label' => $this->closureService->monthLabel($date_from),
            'month_slug' => date('d-m-Y', strtotime($date_from)),
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

    public function open(Request $request, string $month)
    {
        if ($request->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        try {
            $monthKey = $this->closureService->resolveMonthKey($month);
            $this->closureService->openMonth($monthKey, $request->user());
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()
            ->back()
            ->with('success', 'Opening for '.$this->closureService->monthLabel($monthKey).' successfully set.');
    }

    public function close(Request $request, string $month)
    {
        if ($request->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        try {
            $monthKey = $this->closureService->resolveMonthKey($month);
            $this->closureService->closeMonth($monthKey, $request->user());
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()
            ->back()
            ->with('success', 'Closure set for '.$this->closureService->monthLabel($monthKey).'.');
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
