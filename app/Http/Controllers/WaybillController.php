<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWaybillRequest;
use App\Http\Requests\UpdateWaybillRequest;
use App\Models\Waybill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WaybillController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'load_auth']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Waybill::class);

        $showRecycle = $request->query('recycle') === '1';
        $waybillsearch = trim((string) $request->query('waybillsearch', ''));
        $filterStatus = trim((string) $request->query('status', ''));
        $filterDistribution = trim((string) $request->query('distribution', ''));
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $sort = (string) $request->query('sort', '');
        $dir = (string) $request->query('dir', 'desc');
        $perPage = in_array((int) $request->query('per_page', 10), [10, 25, 50], true)
            ? (int) $request->query('per_page', 10)
            : 10;

        $listQuery = $this->listQueryParams(
            $showRecycle,
            $waybillsearch,
            $filterStatus,
            $filterDistribution,
            $dateFrom,
            $dateTo,
            $sort,
            $dir,
            $perPage
        );

        $waybillsQuery = Waybill::query()
            ->with('user')
            ->withSum(['wbcontent as qty_total' => fn ($q) => $q->where('del', 'no')], 'qty')
            ->withSum(['wbcontent as qty_distributed' => fn ($q) => $q->where('del', 'no')], 'qty_dist')
            ->withCount(['wbcontent as item_count' => fn ($q) => $q->where('del', 'no')])
            ->search($waybillsearch)
            ->statusFilter($filterStatus)
            ->deliveryBetween($dateFrom, $dateTo)
            ->ordered($sort, $dir);

        if (! $showRecycle) {
            $waybillsQuery->distributionFilter($filterDistribution);
        }

        if ($showRecycle) {
            $waybillsQuery->deleted();
        } else {
            $waybillsQuery->active();
        }

        $waybills = $waybillsQuery
            ->paginate($perPage)
            ->appends($listQuery);

        return view('pages.dash.waybillview', [
            'c' => 1,
            'waybills' => $waybills,
            'waybillsearch' => $waybillsearch,
            'showRecycle' => $showRecycle,
            'filterStatus' => $filterStatus,
            'filterDistribution' => $filterDistribution,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'sort' => $sort,
            'dir' => $dir,
            'perPage' => $perPage,
            'listQuery' => $listQuery,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Waybill::class);

        return view('pages.dash.waybill', [
            'suggestedBillNo' => Waybill::suggestBillNo(),
        ]);
    }

    public function store(StoreWaybillRequest $request)
    {
        try {
            $validated = $request->preparedAttributes();

            $xter = substr(str_shuffle(str_repeat('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', 4)), 0, 4);
            $stockNo = 'ST'.$xter.date('is');

            $waybill = DB::transaction(function () use ($validated, $stockNo) {
                return Waybill::create(array_merge($validated, [
                    'user_id' => (string) auth()->id(),
                    'stock_no' => $stockNo,
                    'del' => 'no',
                ]));
            });

            return redirect('/distribution/'.$waybill->id)->with('success', 'Waybill saved. Add items below to distribute.');
        } catch (\Illuminate\Database\QueryException $e) {
            report($e);

            return redirect()->back()->withInput()->with('error', $this->queryExceptionMessage($e));
        } catch (\Throwable $e) {
            report($e);

            return redirect()->back()->withInput()->with('error', 'Oops..! Something went wrong while saving the waybill.');
        }
    }

    public function update(UpdateWaybillRequest $request, Waybill $waybill)
    {
        $this->authorize('update', $waybill);

        if ($waybill->del === 'yes') {
            return redirect('/waybillview')->with('error', 'Waybill not found');
        }

        $validated = $request->preparedAttributes();

        DB::transaction(function () use ($waybill, $validated) {
            $waybill->fill($validated);
            $waybill->user_id = (string) auth()->id();
            $waybill->save();
        });

        return redirect('/waybillview')->with('success', 'Bill Successfully Updated');
    }

    public function destroy(Waybill $waybill)
    {
        $this->authorize('delete', $waybill);

        if ($waybill->del === 'yes') {
            return redirect('/waybillview')->with('error', 'Waybill not found');
        }

        $waybill->del = 'yes';
        $waybill->save();

        return redirect('/waybillview')->with('success', 'Waybill moved to recycle bin');
    }

    public function restore(Waybill $waybill)
    {
        $this->authorize('restore', $waybill);

        if ($waybill->del !== 'yes') {
            return redirect('/waybillview?recycle=1')->with('error', 'Waybill not found in recycle bin');
        }

        $waybill->del = 'no';
        $waybill->save();

        return redirect('/waybillview?recycle=1')->with('success', 'Waybill restored successfully');
    }

    private function listQueryParams(
        bool $showRecycle,
        string $waybillsearch,
        string $filterStatus,
        string $filterDistribution,
        ?string $dateFrom,
        ?string $dateTo,
        string $sort,
        string $dir,
        int $perPage
    ): array {
        return array_filter([
            'recycle' => $showRecycle ? '1' : null,
            'waybillsearch' => $waybillsearch !== '' ? $waybillsearch : null,
            'status' => $filterStatus !== '' ? $filterStatus : null,
            'distribution' => $filterDistribution !== '' ? $filterDistribution : null,
            'date_from' => $dateFrom ?: null,
            'date_to' => $dateTo ?: null,
            'sort' => $sort !== '' ? $sort : null,
            'dir' => $dir !== 'desc' ? $dir : null,
            'per_page' => $perPage !== 10 ? $perPage : null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function queryExceptionMessage(\Illuminate\Database\QueryException $exception): string
    {
        if ($this->isDuplicateBillNo($exception)) {
            return 'That waybill number already exists. Please use a different one.';
        }

        $message = strtolower($exception->getMessage());
        $column = $this->queryExceptionColumn($exception);

        if (str_contains($message, 'data too long')) {
            if ($column) {
                return 'Could not save waybill. The '.str_replace('_', ' ', $column).' is too long. Error: ';
            }

            return 'Could not save waybill. One or more fields may be too long. Error: ';
        }

        if (str_contains($message, 'cannot be null')
            || str_contains($message, "doesn't have a default value")
            || str_contains($message, 'not null constraint failed')) {
            if ($column) {
                return 'Could not save waybill. Missing or invalid value for '.str_replace('_', ' ', $column).'. Error: ';
            }

            return 'Could not save waybill. A required field was missing. Error: ';
        }

        if (str_contains($message, 'incorrect date')
            || str_contains($message, 'invalid datetime')
            || str_contains($message, '1292')) {
            return 'Could not save waybill. Please enter a valid delivery date or leave it blank. Error: ';
        }

        return 'Could not save waybill. Error: ';
    }

    private function queryExceptionColumn(\Illuminate\Database\QueryException $exception): ?string
    {
        if (preg_match("/column '([^']+)'/i", $exception->getMessage(), $matches)) {
            return $matches[1];
        }

        if (preg_match('/not null constraint failed: (?:[\w.]+\.)?(\w+)/i', $exception->getMessage(), $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function isDuplicateBillNo(\Illuminate\Database\QueryException $exception): bool
    {
        $message = strtolower($exception->getMessage());
        $sqlState = $exception->errorInfo[0] ?? '';
        $driverCode = (int) ($exception->errorInfo[1] ?? 0);

        if ($sqlState === '23000' && in_array($driverCode, [1062, 19], true)) {
            return str_contains($message, 'bill_no');
        }

        return str_contains($message, 'unique constraint failed')
            && str_contains($message, 'bill_no');
    }
}
