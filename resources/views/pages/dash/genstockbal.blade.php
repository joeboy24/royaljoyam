
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
                            {{-- <td class="col-sm-2"><b>Tot. Quantity :</b></td> --}}
                            <td class="col-sm-4"></td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Date To :</td>
                            <td class="col-sm-3">{{ session('date_to') }}</td>
                        </tr>
                        <!--tr>
                            <td class="col-sm-3"></td>
                            <td class="col-sm-3"></td>
                            <td class="col-sm-2">Sales Person :</td>
                            {{-- <td class="col-sm-4">Royal Joham V... {{session('company')->contact}}</td> --}}
                        </tr>
                        <tr>
                            <td class="col-sm-3">Payment Methods :</td>
                            <td class="col-sm-3">Cash/Cheque/Momo..</td>
                            <td class="col-sm-2">Report Date :</td>
                            {{-- <td class="col-sm-4">{{date('d-m-Y')}}</td> --}}
                        </tr-->
                    </tbody>
                </table>
            </div>

            <div class="invBottom">
                <table class="invBottomTbl">
                    <thead>
                        <th>#</th>
                        <th>Stock</th>
                        <th>Qty</th>
                        <th class="pr">CP(Gh₵)</th>
                        <th class="pr">SP(Gh₵)</th>
                        <th class="pr">Tot(Gh₵)</th>
                        <th class="pr">Profit(Gh₵)</th>
                    </thead>
                    <tbody>
                        @if(count($genstockbal) > 0)
                            @foreach ($genstockbal as $stock)
                                {{-- {{ session('old_itn', $stock->name) }}
                                @if (session('old_itn') != $stock->name) --}}
                                    <tr><td>{{$count++}}</td>
                                    <td>{{$stock->item_no}}<br>{{$stock->name}}</td>
                                    <td>{{$stock->qty}}</td>
                                    <td class="col-sm-1 pr">{{$stock->cost_price}}</td>
                                    <td class="col-sm-1 pr">{{$stock->unit_price}}</td>
                                    <td class="col-sm-1 pr">{{$stock->tot}}</td>
                                    <td class="col-sm-2 pr">{{$stock->profits}}</td>
                                    </tr>
                                {{-- @endif --}}
                            @endforeach
                        @else
                            <p>No records to print out</p>
                        @endif
                        
                        <tr class="invTot">
                            <td class="col-sm-1"><h4>Total</h4></td>
                            <td class="col-sm-1"></td>
                            <td class="col-sm-1"><h4>{{ $genstockbal->sum('qty') }}</h4></td>
                            <td class="col-sm-1 pr"><h4>{{ $genstockbal->sum('cost_price') }}</h4></td>
                            <td class="col-sm-1 pr"><h4>{{ $genstockbal->sum('unit_price') }}</h4></td>
                            <td class="col-sm-1 pr"><h4>{{ $genstockbal->sum('tot') }}</h4></td>
                            <td class="col-sm-1 pr"><h4>{{ $genstockbal->sum('profits') }}</h4></td>
                        </tr>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>