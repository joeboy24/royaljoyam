<?php

namespace App\Http\Controllers;

use App\Services\DailyCloseService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class DailyCloseController extends Controller
{
    public function __construct(
        private readonly DailyCloseService $dailyCloseService
    ) {
        $this->middleware(['auth', 'load_auth']);
    }

    public function store(Request $request)
    {
        if (session('sales_permit') == 0) {
            return redirect('/dashboard')->with(
                'error',
                app(\App\Services\ClosureService::class)->salesPermitDeniedMessage()
            );
        }

        $closeDate = session('date_today') ?: date('Y-m-d');
        $countedCash = $request->filled('counted_cash')
            ? (float) $request->input('counted_cash')
            : null;
        $notes = $request->input('notes');

        try {
            $this->dailyCloseService->closeDay(
                $request->user(),
                $closeDate,
                $countedCash,
                is_string($notes) ? $notes : null
            );
        } catch (InvalidArgumentException $e) {
            return redirect('/sales')->with('error', $e->getMessage());
        }

        return redirect('/sales')->with('success', 'Day closed for '.$closeDate.'.');
    }

    public function print(Request $request, string $date)
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return redirect('/sales')->with('error', 'Invalid daily close date.');
        }

        $dailyClose = $this->dailyCloseService->findForUser($request->user(), $date);

        if (! $dailyClose && $request->user()->status === 'Administrator' && $request->filled('id')) {
            $dailyClose = \App\Models\DailyClosure::query()
                ->where('del', 'no')
                ->whereKey($request->query('id'))
                ->first();
        }

        if (! $dailyClose) {
            return redirect('/sales')->with('error', 'No daily close found for '.$date.'.');
        }

        return view('pages.invoice.dailycloseprint', [
            'dailyClose' => $dailyClose,
            'printMeta' => [
                'date_from' => $dailyClose->close_date,
                'date_to' => $dailyClose->close_date,
                'branch' => $dailyClose->branch_label,
            ],
        ]);
    }
}
