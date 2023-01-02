
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

    <section id="invoice">
        <div class="invoiceContent">

            <div class="invHeaderTop">
                <h1>ROYAL JOYAM</h1>
                <h4>Ventures</h4>
                <P class="locInfo">{{session('company')->address}}</P>
                <P class="contactInfo">{{session('company')->contact}}, {{session('company')->email}}</P>
            </div>

            <div style="height: 50px">
            </div>
            
            <p style="color: #aaa; font-size: 0.9em; letter-spacing: 0.5px">Order Returns Report</p>
            <div class="invCenter">
                <table class="invCenterTbl">
                    <tbody>
                        <tr>
                            <td class="col-sm-3">Date From :</td>
                            @if (session('date_from') != '')
                                <td class="col-sm-3">{{ session('date_from') }}</td>
                            @else
                                <td class="col-sm-3">Today</td>
                            @endif
                            <td class="col-sm-4"></td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Date To :</td>
                            <td class="col-sm-3">{{ session('date_to') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="invBottom">
                <table class="invBottomTbl">
                    <thead>
                        <th>#</th>
                        <th class="">Item</th>
                        <th class="pr">Qty</th>
                        <th class="pr">Unit Price (Gh₵)</th>
                        <th class="pr">Total (Gh₵)</th>
                        <th class="pr">Order Date</th>
                    </thead>
                    <tbody>
                        @if(count($returns) > 0)
                            @foreach ($returns as $rtn)
                                @if ($rtn->del != 'yes')
                                <tr>
                                    <td>{{$count++}}</td>
                                    <td class="col-sm-1">{{$rtn->name}}<br><p class="gray_p">Item No.: {{$rtn->item_no}}</p></td>
                                    <td class="col-sm-1 pr">{{$rtn->qty}}</td>
                                    <td class="col-sm-1 pr">{{$rtn->unit_price}}</td>
                                    <td class="col-sm-1 pr">{{number_format($rtn->tot)}}</td>
                                    <td class="col-sm-2 pr">{{date('M. d, Y', strtotime($rtn->order_date))}}<br><p class="small_p">By: {{$rtn->user->name}}</p></td>
                                  
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <p>No records to print out</p>
                        @endif
{{--                         
                        <tr>
                            <td></td>
                            <td><b>Total</b></td>
                            <td><b>{{ number_format(session('returnsrep')->sum('qty')) }}</b></td>
                            <td></td>
                            <td><b>{{ number_format(session('returnsrep')->sum('tot')) }}</b></td>
                            <td></td>
                          </tr>
                        <tr>
                            <td class="col-sm-1"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr">{{ number_format(session('returnsrep')->sum('qty')) }}</td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr">{{ number_format(session('returnsrep')->sum('tot')) }}</td>
                            <td class="col-sm-2 pr"> - </td>
                        </tr> --}}
                        <tr class="invTot">
                            <td class="col-sm-1"></td>
                            <td class="col-sm-1">Returns <h4>Total</h4><br></td>
                            <td class="col-sm-1 pr">{{ number_format(session('returnsrep')->sum('qty')) }}</td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"><h4>{{ number_format(session('returnsrep')->sum('tot')) }}</td>
                            <td class="col-sm-1 pr"><h4></h4></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>