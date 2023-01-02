
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
                            <td class="col-sm-3">Net Total :<br><br></td>
                            <td class="col-sm-3"><b>Gh₵ {{number_format(session('net'), 2)}}</b><br><br></td>
                            
                        </tr>
                        <tr>
                            <td class="col-sm-3">Date From :</td>
                            <td class="col-sm-3">{{date("d-m-Y", strtotime(session('date_from')))}}</td>
                            <td class="col-sm-2"><b>Tot. Quantity :</b></td>
                            <td class="col-sm-4">{{$sales->sum('qty')}}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Date To :</td>
                            @if(session('date_to') == '')
                                <td class="col-sm-3"> - </td>
                            @else
                                <td class="col-sm-3">{{date("d-m-Y", strtotime(session('date_to')))}}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="col-sm-3"></td>
                            <td class="col-sm-3"></td>
                            <td class="col-sm-2">Sales Person :</td>
                            <td class="col-sm-4">Royal Joham V... {{session('company')->contact}}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Payment Methods :</td>
                            <td class="col-sm-3">Cash/Cheque/Momo..</td>
                            <td class="col-sm-2">Report Date :</td>
                            <td class="col-sm-4">{{date('d-m-Y')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="invBottom">
                <table class="invBottomTbl">
                    <thead>
                        <th>#</th>
                        <th>Order No. / Description</th>
                        <th class="pr">Total(Gh₵)</th>
                        <th class="pr">Status</th>
                        <th class="pr">Date</th>
                    </thead>
                    <tbody>
                        @if(count($sales) > 0)
                            @foreach ($sales as $sale)
                              <tr>
                                <td class="col-sm-1">{{$count++}}</td>
                                <td class="col-sm-6"><h4>{{$sale->order_no}}</h4><p>Paid by {{$sale->buy_name}} &nbsp;&nbsp;&nbsp; Tel: {{$sale->buy_contact}}<br>
                                    Pay Mode: {{$sale->pay_mode}} &nbsp;&nbsp; Qty.: {{$sale->qty}}</p></td>
                                <td class="col-sm-1 pr">{{number_format($sale->tot, 2)}}
                                    @if ($sale->discount != 0)
                                      <p class="gray_p"><del>{{number_format($sale->discount, 2)}}</del> off</p>
                                    @endif
                                </td>
                                <td class="col-sm-2 pr">{{$sale->del_status}}</td>
                                <td class="col-sm-2 pr">{{$sale->created_at}}</td>
                              </tr>
                            @endforeach
                        @else
                            <p>No records to print out</p>
                        @endif
                        
                        @if (session('branch') == 'All')
                            <tr>
                                <td class="col-sm-1"></td>
                                <td class="col-sm-6"><h4>Sales</h4>
                                    @foreach (session('compbranch') as $cbr)
                                        <br>Branch {{$cbr->id}}: {{substr($cbr->name, 0,17)}}...
                                    @endforeach
                                    <br>Total:
                                </td>
                                <td class="col-sm-1 pr"><br>
                                    @foreach (session('compbranch') as $cbr)
                                        <input type="hidden" value="{{$b = 'b'.$cbr->tag}}">
                                        <br>{{number_format(session($b), 2)}}
                                    @endforeach
                                    <br>{{number_format(session('gross'), 2)}}
                                </td>
                                <td class="col-sm-2 pr"> - </td>
                                <td class="col-sm-2 pr"> - </td>
                            </tr>
                            
                            <tr>
                                <td class="col-sm-1"></td>
                                <td class="col-sm-6"><h4>Expenditure</h4>
                                    @foreach (session('compbranch') as $cbr)
                                        <br>Branch {{$cbr->id}}: {{substr($cbr->name, 0,17)}}...
                                    @endforeach
                                    <br>Total:
                                </td>
                                <td class="col-sm-1 pr"><br><h4>&nbsp;</h4>
                                    @foreach (session('compbranch') as $cbr)
                                        <input type="hidden" value="{{$b = 'exp_b'.$cbr->tag}}">
                                        <br>{{number_format(session($b), 2)}}
                                    @endforeach
                                    <br>{{number_format(session('expenses')->sum('expense_cost'), 2)}}</td>
                                <td class="col-sm-2 pr"> - </td>
                                <td class="col-sm-2 pr"> - </td>
                            </tr>

                            <tr>
                                <td class="col-sm-1"></td>
                                <td class="col-sm-6"><h4>Profits</h4>
                                    @foreach (session('compbranch') as $cbr)
                                        <br>Branch {{$cbr->id}}: {{substr($cbr->name, 0,17)}}...
                                    @endforeach
                                    <br>Total:
                                </td>
                                <td class="col-sm-1 pr"><h4>&nbsp;</h4>
                                    @foreach (session('compbranch') as $cbr)
                                        <input type="hidden" value="{{$b = 'b'.$cbr->tag.'_profits'}}">
                                        <br>{{number_format(session($b), 2)}}
                                    @endforeach
                                    <br>{{number_format(session('gen_profits'), 2)}}</td>
                                <td class="col-sm-2 pr"> - </td>
                                <td class="col-sm-2 pr"> - </td>
                            </tr>

                            <tr>
                                <td class="col-sm-1"></td>
                                <td class="col-sm-6"></td>
                                <td class="col-sm-1 pr"></td>
                                <td class="col-sm-2 pr">VAT</td>
                                <td class="col-sm-2 pr">0.00</td>
                            </tr>
                            <tr class="invTot">
                                <td class="col-sm-1">Gross <h4>Total</h4><br></td>
                                <td class="col-sm-6"></td>
                                <td class="col-sm-1 pr"><h4>Gh₵&nbsp;{{number_format(session('gross'), 2)}}</h4></td>
                                <td class="col-sm-2 pr"><h4></h4></td>
                                <td class="col-sm-2 pr"></td>
                            </tr>
                            <tr class="invTot">
                                <td class="col-sm-1">Net Total</td>
                                <td class="col-sm-6"></td>
                                <td class="col-sm-1 pr">Gh₵&nbsp;{{number_format(session('net'), 2)}}</td>
                                <td class="col-sm-2 pr"><h4></h4></td>
                                <td class="col-sm-2 pr"></td>
                            </tr>

                        @else

                            <tr>
                                <td class="col-sm-1"></td>
                                <td class="col-sm-6"><h4>Branch {{session('branch')}}: {{session('branch_'.session('branch'))}}</h4> </td>
                            </tr>

                            <tr>
                                <td class="col-sm-1"></td>
                                <td class="col-sm-6">Sales</td>
                                <td class="col-sm-1 pr">{{number_format(session('b'.session('branch')), 2)}}</td>
                                <td class="col-sm-2 pr"> - </td>
                                <td class="col-sm-2 pr"> - </td>
                            </tr>

                            <tr>
                                <td class="col-sm-1"></td>
                                <td class="col-sm-6">Expenditure</td>
                                <td class="col-sm-1 pr">{{number_format(session('exp_b'.session('branch')), 2)}}</td>
                                <td class="col-sm-2 pr"> - </td>
                                <td class="col-sm-2 pr"> - </td>
                            </tr>

                            <tr>
                                <td class="col-sm-1"></td>
                                <td class="col-sm-6">Profit</td>
                                <td class="col-sm-1 pr">{{number_format(session('b'.session('branch').'_profits'), 2)}}</td>
                                <td class="col-sm-2 pr"> - </td>
                                <td class="col-sm-2 pr"> - </td>
                            </tr>

                            <tr>
                                <td class="col-sm-1"></td>
                                <td class="col-sm-6"></td>
                                <td class="col-sm-1 pr"></td>
                                <td class="col-sm-2 pr">VAT</td>
                                <td class="col-sm-2 pr">0.00</td>
                            </tr>
                            <tr class="invTot">
                                <td class="col-sm-1">Gross <h4>Total</h4><br></td>
                                <td class="col-sm-6"></td>
                                <td class="col-sm-1 pr"><h4>Gh₵&nbsp;{{number_format(session('b'.session('branch')), 2)}}</h4></td>
                                <td class="col-sm-2 pr"><h4></h4></td>
                                <td class="col-sm-2 pr"></td>
                            </tr>
                            <tr class="invTot">
                                <td class="col-sm-1">Net Total</td>
                                <td class="col-sm-6"></td>
                                <td class="col-sm-1 pr">Gh₵&nbsp;{{number_format(session('b'.session('branch'))-session('exp_b'.session('branch')), 2)}}</td>
                                <td class="col-sm-2 pr"><h4></h4></td>
                                <td class="col-sm-2 pr"></td>
                            </tr>

                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>