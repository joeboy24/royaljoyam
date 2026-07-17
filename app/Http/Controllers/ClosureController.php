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
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClosureController extends Controller
{
    public function __construct(
        private readonly ClosureService $closureService,
        private readonly ClosureSummaryService $summaryService
    ) {
        $this->middleware(['auth', 'load_auth']);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($full_date)
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        try {
            $pass = $this->monthDetailPayload($full_date);
        } catch (InvalidArgumentException $e) {
            return redirect('/closure_page')->with('error', $e->getMessage());
        }

        return view('pages.dash.closuredetail', $pass);
    }

    public function print(string $month)
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        try {
            $pass = $this->monthDetailPayload($month);
        } catch (InvalidArgumentException $e) {
            return redirect('/closure_page')->with('error', $e->getMessage());
        }

        $pass['printMeta'] = [
            'date_from' => $pass['date_from'],
            'date_to' => $pass['date_to'],
        ];

        return view('pages.invoice.closureprint', $pass);
    }

    public function export(string $month): StreamedResponse|\Illuminate\Http\RedirectResponse
    {
        if (auth()->user()->status != 'Administrator') {
            return redirect('/dashboard');
        }

        try {
            $pass = $this->monthDetailPayload($month);
        } catch (InvalidArgumentException $e) {
            return redirect('/closure_page')->with('error', $e->getMessage());
        }

        $filename = 'closure-'.$pass['date_from'].'.csv';

        return response()->streamDownload(function () use ($pass) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Month', $pass['month_label']]);
            fputcsv($out, ['Status', $pass['closure_status']]);
            fputcsv($out, []);
            fputcsv($out, ['Summary', 'Value']);
            fputcsv($out, ['Qty sold', $pass['summary']['qty_sold']]);
            fputcsv($out, ['Amount sold', $pass['summary']['amt_sold']]);
            fputcsv($out, ['Profit', $pass['summary']['profit']]);
            fputcsv($out, ['Qty available', $pass['summary']['qty_available']]);
            fputcsv($out, []);
            fputcsv($out, ['Branch', 'Qty sold', 'Amount sold', 'Profit']);
            foreach ($pass['branch_summaries'] as $branch) {
                fputcsv($out, [
                    $branch['name'],
                    $branch['qty_sold'],
                    $branch['amt_sold'],
                    $branch['profit'],
                ]);
            }
            fputcsv($out, []);
            fputcsv($out, ['Sales item', 'Branch', 'Qty sold', 'Amount', 'Qty rem', 'Profit']);
            foreach ($pass['sales_rows'] as $row) {
                foreach ($pass['branches'] as $branch) {
                    $cell = $row['branches'][(string) $branch->tag] ?? null;
                    if (! $cell) {
                        continue;
                    }
                    fputcsv($out, [
                        $row['name'],
                        $branch->name,
                        $cell['qty_sold'],
                        $cell['amt_sold'],
                        $cell['qty_rem'],
                        $cell['profit'],
                    ]);
                }
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
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

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }

    /**
     * @return array<string, mixed>
     */
    private function monthDetailPayload(string $fullDate): array
    {
        $date_from = $this->closureService->resolveMonthKey($fullDate);
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

        $openBlockedReason = $status === 'not_opened'
            ? $this->closureService->openBlockedReason($date_from)
            : null;
        $previousMonthKey = $this->closureService->previousMonthKey($date_from);

        return [
            'yr' => (int) date('Y', strtotime($date_from)),
            'month_label' => $this->closureService->monthLabel($date_from),
            'month_slug' => date('d-m-Y', strtotime($date_from)),
            'date_from' => $date_from,
            'date_to' => $date_to,
            'closure' => $closure,
            'closure_status' => $status,
            'closure_state' => $closure_state,
            'is_opened' => (bool) $closure,
            'can_open' => $status === 'not_opened' && $openBlockedReason === null,
            'open_blocked_reason' => $openBlockedReason,
            'previous_month_label' => $this->closureService->monthLabel($previousMonthKey),
            'previous_month_slug' => date('d-m-Y', strtotime($previousMonthKey)),
            'branches' => $summary['branches'],
            'column_keys' => $summary['column_keys'],
            'summary' => $summary['summary'],
            'branch_summaries' => $summary['branch_summaries'],
            'distribution_rows' => $summary['distribution_rows'],
            'distribution_totals' => $summary['distribution_totals'],
            'sales_rows' => $summary['sales_rows'],
            'sales_totals' => $summary['sales_totals'],
        ];
    }
}
