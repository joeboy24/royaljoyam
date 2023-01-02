
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
            
            <div class="invHeaderMid">
                <div class="row">
                    <div class="col-sm-4 delAdd">
                        <p>{{$order->buy_name}}</p>
                        <p>{{$order->buy_contact}}</p>
                        <p>Ghana</p>
                    </div>
                    <div class="col-sm-4 delAdd2">
                        <h4>Delivery Address</h4>
                        <p>{{$order->buy_name}}</p>
                        <p>{{$order->buy_contact}}</p>
                        <p>Ghana</p>
                        <h2>RECEIPT</h2>
                    </div>
                    <div class="col-sm-4">
                    
                    </div>
                </div>
            </div>

            <div class="invCenter">
                <table class="invCenterTbl">
                    <tbody>
                        <tr>
                            <td class="col-sm-3">Paid by :<br><br></td>
                            <td class="col-sm-3"><b>{{$order->buy_name}}<br><br></td>
                            
                        </tr>
                        <tr>
                            <td class="col-sm-3">Contact :</td>
                            <td class="col-sm-3">{{$order->buy_contact}}</td>
                            <td class="col-sm-2"><b>No. of Items :</b></td>
                            <td class="col-sm-4">{{$order->qty}}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Date Paid :</td>
                            <td class="col-sm-3">{{date("d-m-Y", strtotime($order->created_at))}}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Sales Person :</td>
                            <td class="col-sm-3">{{$user->name}}</td>
                            <td class="col-sm-2">Received By :</td>
                            <td class="col-sm-4">Royal Joham V... {{session('company')->contact}}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Payment Method :</td>
                            <td class="col-sm-3">{{$order->pay_mode}}</td>
                            <td class="col-sm-2">Printed On :</td>
                            <td class="col-sm-4">{{date('d-m-Y')}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="invBottom">
                <table class="invBottomTbl">
                    <thead>
                        <th># &nbsp; Item No. / Description</th>
                        <th>Unit Price</th>
                        <th class="pr">Total(Gh₵)</th>
                        <th class="pr">Status</th>
                        <th class="pr">Date</th>
                    </thead>
                    <tbody>
                        @if(count($sales) > 0)
                            @foreach ($sales as $sale)
                              <tr>
                                <td class="col-sm-6"><h4> {{$count++}}. &nbsp;&nbsp;{{$order->order_no}}</h4><p>{{$sale->name}}&nbsp;&nbsp;Qty.: {{$sale->qty}}</p></td>
                                <td class="col-sm-1">{{$sale->unit_price}}&nbsp;</td>
                                <td class="col-sm-1 pr">{{number_format($sale->tot, 2)}}</td>
                                <td class="col-sm-2 pr">{{$order->del_status}}</td>
                                <td class="col-sm-2 pr">{{$sale->created_at}}</td>
                              </tr>
                            @endforeach
                        @else
                            <p>No records to print out</p>
                        @endif
                        <!--tr>
                            <td class="col-sm-1">2</td>
                            <td class="col-sm-6"><h4>U231755321-0732    Paypal</h4><br><p>Visa card transaction</td>
                            <td class="col-sm-1 pr">3</td>
                            <td class="col-sm-2 pr">72.00</td>
                            <td class="col-sm-2 pr">216.00</td>
                        </tr>
                        <tr>
                            <td class="col-sm-1">3</td>
                            <td class="col-sm-6"><h4>M921765327-0137    Alesis</h4><br><p>V61 USB-MIDI Keyboard Controller (Black)</p></td>
                            <td class="col-sm-1 pr">1</td>
                            <td class="col-sm-2 pr">108.40</td>
                            <td class="col-sm-2 pr">108.40</td>
                        </tr-->
                        <tr>
                            <td class="col-sm-6">Discount</td>
                            <td class="col-sm-1"></td>
                            <td class="col-sm-1 pr">{{$order->discount}}</td>
                            <td class="col-sm-2 pr"> - </td>
                            <td class="col-sm-2 pr"> - </td>
                        </tr>
                        <tr>
                            <td class="col-sm-6"><h4>Delivery</h4></td>
                            <td class="col-sm-1"></td>
                            <td class="col-sm-1 pr">0.00</td>
                            <td class="col-sm-2 pr"> - </td>
                            <td class="col-sm-2 pr"> - </td>
                        </tr>
                        <tr>
                            <td class="col-sm-1"></td>
                            <td class="col-sm-6"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-2 pr">VAT&nbsp;:</td>
                            <td class="col-sm-2 pr">0.00</td>
                        </tr>
                        <tr>
                            <td class="col-sm-1"></td>
                            <td class="col-sm-6"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-2 pr">Payment&nbsp;:</td>
                            <td class="col-sm-2 pr">{{number_format($order->payment, 2)}}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-1"></td>
                            <td class="col-sm-6"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-2 pr">Change&nbsp;:</td>
                            <td class="col-sm-2 pr">{{number_format($order->change, 2)}}</td>
                        </tr>
                        <tr class="invTot">
                            <td class="col-sm-1"><h4>Total Gh₵</h4><br></td>
                            <td class="col-sm-6"></td>
                            <td class="col-sm-1 pr"><h4>{{number_format($order->tot, 2)}}</h4></td>
                            <td class="col-sm-2 pr"><h4></h4></td>
                            <td class="col-sm-2 pr"></td>
                        </tr>
                        
                        @if (($order->pay_mode == 'Post Payment(Debt)' || $order->pay_mode == 'Post Pay/Debt(Paid)') && count($order->salespayment) > 0)
                            <thead>
                                <th>Payments Made</th>
                                <th>Amount</th>
                                <th class="pr">Bal.</th>
                                <th class="pr"></th>
                                <th class="pr">Date</th>
                            </thead>
                            @foreach ($order->salespayment as $sp)
                                <tr>
                                    <td class="col-sm-6">{{$count2++}}<br></td>
                                    <td class="col-sm-1">{{$sp->amt_paid}}</td>
                                    <td class="col-sm-1 pr">{{$sp->bal}}</td>
                                    <td class="col-sm-2 pr"></td>
                                    <td class="col-sm-2 pr">{{$sp->created_at}}</td>
                                </tr>
                            @endforeach
                            <tr class="invTot">
                                <td class="col-sm-1"><h4>Total Gh₵</h4></td>
                                <td class="col-sm-6"><h4>{{number_format($order->salespayment->sum('amt_paid'), 2)}}</h4></td>
                                <td class="col-sm-1 pr"><h4>{{number_format($sp->bal, 2)}}</h4></td>
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