
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
                        <th>Title</th>
                        <th>Description</th>
                        <th class="pr">Cost(Gh₵)</th>
                        <th class="pr">Date/Time Updated</th>
                    </thead>
                    <tbody>
                        @if(count($expenses) > 0)
                            @foreach ($expenses as $expense)
                                <tr><td>{{$count++}}</td>
                                <td>{{$expense->title}}<br>{{session('branch_'.$expense->companybranch_id)}}</td>
                                <td>{{$expense->desc}}</td>
                                <td class="col-sm-1 pr">{{$expense->expense_cost}}</td>
                                <td class="col-sm-2 pr">{{$expense->created_at}}</td>
                                </tr>
                            @endforeach
                        @else
                            <p>No records to print out</p>
                        @endif
                        
                        <tr>
                            <td class="col-sm-1"></td>
                            <td class="col-sm-6"><h4>Expenditure</h4>
                                @foreach (session('compbranch') as $cbr)
                                  <br>Branch {{$cbr->id}}: {{substr($cbr->name, 0,17)}}...
                                @endforeach
                                <br>Total:
                            </td>
                            <td class="col-sm-1 pr"><br>
                                @foreach (session('compbranch') as $cbr)
                                  <input type="hidden" value="{{$b = 'exp_b'.$cbr->tag}}">
                                  <br>{{number_format(session($b), 2)}}
                                @endforeach
                                <br>{{number_format(session('expenses')->sum('expense_cost'), 2)}}
                            </td>
                            <td class="col-sm-2 pr"> - </td>
                            <td class="col-sm-2 pr"> - </td>
                        </tr>
                        
                        <tr>
                            <td class="col-sm-1"></td>
                            <td class="col-sm-6"><h4>No. of Records : {{ count($expenses) }}</h4></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"> - </td>
                            <td class="col-sm-1 pr"> - </td>
                        </tr>
                        <tr class="invTot">
                            <td class="col-sm-1"><h4>Total</h4>Expenditure</td>
                            <td class="col-sm-6"></td>
                            <td class="col-sm-1 pr"><h4></h4></td>
                            <td class="col-sm-1 pr"><h4>{{ number_format($expenses->sum('expense_cost'), 2) }}</h4></td>
                            <td class="col-sm-1 pr"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>