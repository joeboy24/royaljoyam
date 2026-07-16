<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Company;
use App\Models\Waybill;
use App\Models\Wbdistribution;
use App\Support\BranchQuantities;
use Illuminate\Http\Request;

class WaybillReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'load_auth']);
        $this->middleware(function ($request, $next) {
            if (auth()->user()->status != 'Administrator') {
                return redirect('/dashboard');
            }

            return $next($request);
        });
    }

    public function waybillReport(Request $request)
    {
        $this->authorize('viewAny', Waybill::class);

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if (empty($dateFrom) && ! empty($dateTo)) {
            return redirect(url()->previous())->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }

        $reportQuery = $this->waybillReportQuery($dateFrom, $dateTo);
        $waybills = (clone $reportQuery)->paginate(10);
        $waybillsAll = (clone $reportQuery)->get();

        return view('pages.dash.waybillreport', [
            'i' => 1,
            'c' => 1,
            'cats' => Category::all(),
            'waybills' => $waybills,
            'company' => Company::find(1),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'reportSummary' => $this->waybillReportSummary($waybillsAll),
        ]);
    }

    public function waybillPrint(Request $request)
    {
        $this->authorize('viewAny', Waybill::class);

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if (empty($dateFrom) && ! empty($dateTo)) {
            return redirect('/waybillreport')->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }

        $waybills = $this->waybillReportQuery($dateFrom, $dateTo)->get();

        return view('pages.invoice.waybillprint', $this->waybillPrintViewData($waybills, $dateFrom, $dateTo));
    }

    public function waybillPrintSingle($id)
    {
        $this->authorize('viewAny', Waybill::class);

        $waybill = Waybill::with('user')->active()->findOrFail($id);

        return view('pages.invoice.waybillprint', $this->waybillPrintViewData(
            collect([$waybill]),
            $waybill->del_date ?: '',
            ''
        ));
    }

    public function exportWaybillReport(Request $request)
    {
        $this->authorize('viewAny', Waybill::class);

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if (empty($dateFrom) && ! empty($dateTo)) {
            return redirect('/waybillreport')->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }

        $waybills = $this->waybillReportQuery($dateFrom, $dateTo)->get();
        $filename = 'waybill-report-'.date('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($waybills) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Stock No',
                'Bill No',
                'Company',
                'Company Address',
                'Company Contact',
                'Driver',
                'Driver Contact',
                'Vehicle No',
                'Weight',
                'Pieces',
                'Total Qty',
                'Status',
                'Delivery Date',
                'Created By',
                'Created At',
            ]);

            foreach ($waybills as $waybill) {
                fputcsv($handle, [
                    $waybill->stock_no,
                    $waybill->bill_no,
                    $waybill->comp_name,
                    $waybill->comp_add,
                    $waybill->comp_contact,
                    $waybill->drv_name,
                    $waybill->drv_contact,
                    $waybill->vno,
                    $waybill->weight,
                    $waybill->nop,
                    $waybill->tot_qty,
                    $waybill->status,
                    $waybill->formattedDeliveryDate(),
                    $waybill->user->name ?? '',
                    $waybill->created_at ? \Carbon\Carbon::parse($waybill->created_at)->format('Y-m-d H:i') : '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function distReport(Request $request)
    {
        $this->authorize('viewAny', Waybill::class);

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if (empty($dateFrom) && ! empty($dateTo)) {
            return redirect(url()->previous())->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }

        $reportQuery = $this->distributionReportQuery($dateFrom, $dateTo);
        $branches = BranchQuantities::activeBranches();

        return view('pages.dash.distreport', [
            'i' => 1,
            'c' => 1,
            'sum' => 0,
            'cats' => Category::all(),
            'wbdreports' => (clone $reportQuery)->paginate(10),
            'branches' => $branches,
            'branchKeys' => collect(BranchQuantities::activeColumnKeys()),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
        ]);
    }

    public function distReportPrint(Request $request)
    {
        $this->authorize('viewAny', Waybill::class);

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if (empty($dateFrom) && ! empty($dateTo)) {
            return redirect('/distreport')->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }

        $wbdreports = $this->distributionReportQuery($dateFrom, $dateTo)->get();
        $branches = BranchQuantities::activeBranches();

        return view('pages.invoice.distreportprint', [
            'count' => 1,
            'sum' => 0,
            'wbdreports' => $wbdreports,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'company' => Company::find(1),
            'branches' => $branches,
            'branchKeys' => collect(BranchQuantities::activeColumnKeys()),
        ]);
    }

    public function exportDistReport(Request $request)
    {
        $this->authorize('viewAny', Waybill::class);

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        if (empty($dateFrom) && ! empty($dateTo)) {
            return redirect('/distreport')->with('error', 'Oops..! Provide *Date From* in order to proceed');
        }

        $wbdreports = $this->distributionReportQuery($dateFrom, $dateTo)->get();
        $branches = BranchQuantities::activeBranches();
        $filename = 'distribution-report-'.date('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($wbdreports, $branches) {
            $handle = fopen('php://output', 'w');
            $headers = [
                'Item No',
                'Item Name',
                'Waybill Bill No',
                'Company',
            ];

            foreach ($branches as $branch) {
                $headers[] = 'Branch '.$branch->tag;
            }

            $headers[] = 'Date Distributed';
            fputcsv($handle, $headers);

            foreach ($wbdreports as $wbd) {
                $row = [
                    $wbd->item->item_no ?? '',
                    $wbd->item->name ?? '',
                    $wbd->waybill->bill_no ?? '',
                    $wbd->waybill->comp_name ?? '',
                ];

                foreach (BranchQuantities::activeColumnKeys() as $qtyKey) {
                    $row[] = $wbd->{$qtyKey} ?? 0;
                }

                $row[] = $wbd->created_at ? \Carbon\Carbon::parse($wbd->created_at)->format('Y-m-d H:i') : '';
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function waybillReportQuery(?string $dateFrom, ?string $dateTo)
    {
        return Waybill::active()
            ->with('user')
            ->when(! empty($dateFrom) && empty($dateTo), function ($query) use ($dateFrom) {
                $query->where('created_at', 'LIKE', '%'.$dateFrom.'%');
            })
            ->when(! empty($dateFrom) && ! empty($dateTo), function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, new \DateTime($dateTo.'+1 day')]);
            })
            ->orderBy('id', 'desc');
    }

    private function distributionReportQuery(?string $dateFrom, ?string $dateTo)
    {
        return Wbdistribution::with(['item', 'waybill'])
            ->where('del', 'no')
            ->when(! empty($dateFrom) && empty($dateTo), function ($query) use ($dateFrom) {
                $query->where('created_at', 'LIKE', '%'.$dateFrom.'%');
            })
            ->when(! empty($dateFrom) && ! empty($dateTo), function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, new \DateTime($dateTo.'+1 day')]);
            })
            ->orderBy('id', 'desc');
    }

    private function waybillReportSummary($waybills): array
    {
        return [
            'total_qty' => $waybills->sum(fn ($waybill) => (int) $waybill->tot_qty),
            'by_status' => collect(Waybill::statusOptions())->mapWithKeys(function ($status) use ($waybills) {
                return [$status => $waybills->where('status', $status)->count()];
            })->all(),
        ];
    }

    private function waybillPrintViewData($waybills, ?string $dateFrom, ?string $dateTo): array
    {
        return [
            'count' => 1,
            'waybills' => $waybills,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'company' => Company::find(1),
        ];
    }
}
