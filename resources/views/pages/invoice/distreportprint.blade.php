
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
      $wbdreports = collect($wbdreports ?? []);
      $date_from = $date_from ?? '';
      $date_to = $date_to ?? '';
      $branches = collect($branches ?? []);
      $branchKeys = $branches->keys()->map(fn ($index) => 'q'.($index + 1));
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
            
            <p style="color: #aaa; font-size: 0.9em; letter-spacing: 0.5px">Stock Update / Distribution Report</p>
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
                        <th>Item</th>
                        @foreach ($branches as $br)
                            <th class="col-sm-1 pr">Br {{ $br->tag }}</th>
                        @endforeach
                        <th class="pr">Date Distributed</th>
                    </thead>
                    <tbody>
                        @if ($wbdreports->count() > 0)
                            @foreach ($wbdreports as $wbd)
                                @if ($wbd->del != 'yes')
                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>
                                      @if ($wbd->item)
                                        {{ $wbd->item->item_no.' - '.$wbd->item->name }}
                                        @if ($wbd->waybill)
                                          <br><p class="small_p">{{ $wbd->waybill->comp_name }}</p>
                                        @endif
                                      @else
                                        Item unavailable
                                      @endif
                                    </td>
                                    @foreach ($branches as $branchIndex => $br)
                                      @php $qtyKey = 'q'.($branchIndex + 1); @endphp
                                      <td class="col-sm-1 pr">{{ $wbd->{$qtyKey} ?? 0 }}</td>
                                    @endforeach
                                    <td class="pr">{{ date('M. d, Y', strtotime($wbd->created_at)) }}</td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                              <td colspan="{{ 3 + $branches->count() }}">No records to print out</td>
                            </tr>
                        @endif
                       
                        @if ($wbdreports->count() > 0)
                        <tr class="invTot">
                            <td class="col-sm-1"></td>
                            <td class="col-sm-1"><h4>Total</h4>Distribution<br></td>
                            @foreach ($branchKeys as $key)
                                <td class="col-sm-1 pr"><h4>{{ number_format($wbdreports->sum($key)) }}</h4></td>
                            @endforeach
                            <td class="col-sm-1 pr"><h4>{{ $branchKeys->sum(fn ($key) => $wbdreports->sum($key)) }}</h4></td>
                        </tr>
                        @endif
                        
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>