
<html>

<head>
    <meta charset="utf-8">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

	<link href="/maindir/css/inv_style.css" rel="stylesheet">
	<link href="/maindir/css/responsive.css" rel="stylesheet">
    <link href="/maindir/css/bootstrap2.min.css" rel="stylesheet">
    <link href="/maindir/css/font-awesome.min.css" rel="stylesheet">
</head>

<body style="background: #eee">

    @php
      $waybills = $waybills ?? collect(session('waybillreps', []));
      $company = $company ?? session('company');
      $date_from = $date_from ?? session('date_from');
      $date_to = $date_to ?? session('date_to');
    @endphp

    <section id="invoice">
        <div class="invoiceContent">

            <div class="invHeaderTop">
                <h1>ROYAL JOYAM</h1>
                <h4>Ventures</h4>
                @if ($company)
                  <P class="locInfo">{{ $company->address }}</P>
                  <P class="contactInfo">{{ $company->contact }}, {{ $company->email }}</P>
                @endif
            </div>

            <div style="height: 50px">
            </div>
            
            <p style="color: #aaa; font-size: 0.9em; letter-spacing: 0.5px">Waybill Report</p>
            <div class="invCenter">
                <table class="invCenterTbl">
                    <tbody>
                        <tr>
                            <td class="col-sm-3">Date From :</td>
                            @if (! empty($date_from))
                                <td class="col-sm-3">{{ $date_from }}</td>
                            @else
                                <td class="col-sm-3">All</td>
                            @endif
                            <td class="col-sm-4"></td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Date To :</td>
                            <td class="col-sm-3">{{ $date_to ?: '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="invBottom">
                <table class="invBottomTbl">
                    <thead>
                        <th>#</th>
                        <th>Stock No.</th>
                        <th class="col-sm-2">Company</th>
                        <th>Driver</th>
                        <th>Bill No.</th>
                        <th>Weight</th>
                        <th>Pieces</th> 
                        <th>Qty.</th>
                        <th class="waybill-print-status-col">Status</th>
                        <th class="waybill-print-date-col pr">Delivery Date</th>
                    </thead>
                    <tbody>
                        @if ($waybills->count() > 0)
                            @foreach ($waybills as $wb)
                                @if ($wb->del != 'yes')
                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>{{ $wb->stock_no }}<br><p class="small_p">User: {{ $wb->user->name ?? '—' }}</p></td>
                                    <td class="col-sm-2">{{ $wb->comp_name.', '.$wb->comp_add }}<br><p class="small_p">{{ $wb->comp_contact }}</p></td>
                                    <td>{{ $wb->drv_name }}<br>{{ $wb->drv_contact }}<br><p class="small_p">{{ $wb->vno }}</p></td>
                                    <td>{{ $wb->bill_no }}</td>
                                    <td>{{ $wb->weight }}</td>
                                    <td>{{ $wb->nop }}</td>
                                    <td>{{ $wb->tot_qty }}</td>
                                    <td class="waybill-print-status-col">
                                        @if ($wb->status === 'Delivered')
                                          <span class="waybill-print-status waybill-print-status-delivered"><i class="fa fa-check"></i> Delivered</span>
                                        @elseif ($wb->status === 'In Transit')
                                          <span class="waybill-print-status waybill-print-status-transit"><i class="fa fa-truck"></i> In Transit</span>
                                        @else
                                          <span class="waybill-print-status waybill-print-status-pending"><i class="fa fa-clock-o"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td class="waybill-print-date-col pr">{{ $wb->formattedDeliveryDate() }}</td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                              <td colspan="10">No records to print out</td>
                            </tr>
                        @endif

                        @if ($waybills->count() > 0)
                        <tr class="invTot">
                            <td class="col-sm-1"></td>
                            <td class="col-sm-1">Waybill <h4>Total</h4><br></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1">Qty.: </td>
                            <td class="col-sm-1 pr"><h4>{{ number_format($waybills->sum(fn ($wb) => (int) $wb->tot_qty)) }}</h4></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>
