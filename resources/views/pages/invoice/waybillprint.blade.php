
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
            
            <p style="color: #aaa; font-size: 0.9em; letter-spacing: 0.5px">Waybill Report</p>
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
                        {{-- <th>#</th>
                        <th class="">Item</th>
                        <th class="pr">Qty</th>
                        <th class="pr">Unit Price (Gh₵)</th>
                        <th class="pr">Total (Gh₵)</th>
                        <th class="pr">Order Date</th> --}}
                        <th>#</th>
                        <th>Stock No.</th>
                        <th class="col-sm-2">Company</th>
                        <th>Driver</th>
                        <th>Bill No.</th>
                        <th>Weight</th>
                        <th>Pieces</th> 
                        <th>Qty.</th>
                        <th>Status</th>
                        <th class="col-sm-1 pr">Delivery Date</th>
                    </thead>
                    <tbody>
                        @if(count(session('waybillreps')) > 0)
                            @foreach (session('waybillreps') as $wb)
                                @if ($wb->del != 'yes')
                                <tr>
                                    <td>{{$count++}}</td>
                                    {{-- <td class="col-sm-1">{{$wb->name}}<br><p class="gray_p">Item No.: {{$wb->item_no}}</p></td>
                                    <td class="col-sm-1 pr">{{$wb->qty}}</td>
                                    <td class="col-sm-1 pr">{{$wb->unit_price}}</td>
                                    <td class="col-sm-1 pr">{{number_format($wb->tot)}}</td>
                                    <td class="col-sm-2 pr">{{date('M. d, Y', strtotime($wb->order_date))}}<br><p class="small_p">By: {{$wb->user->name}}</p></td> --}}
                                  
                                    <td>{{$wb->stock_no}}<br><p class="small_p">User: {{$wb->user->name}}</p></td>
                                    <td class="col-sm-2">{{$wb->comp_name.', '.$wb->comp_add}}<br><p class="small_p">{{$wb->comp_contact}}</p></td>
                                    <td>{{$wb->drv_name}}<br>{{$wb->drv_contact}}<br><p class="small_p">{{$wb->vno}}</p></td>
                                    <td>{{$wb->bill_no}}</td>
                                    <td>{{$wb->weight}}</td>
                                    <td>{{$wb->nop}}</td>
                                    <td>{{$wb->tot_qty}}</td>
                                    <td class="col-sm-1 pr">
                                        @if ($wb->status == 'Delivered')
                                        <p class="delivered"><i class="fa fa-check"></i>&nbsp;Del..</p>
                                        @else
                                        <p class="pending"><i class="fa fa-warning"></i>&nbsp;Pen..</p> 
                                        @endif
                                    </td>
                                    <td class="col-sm-2 pr">{{date('M. d, Y', strtotime($wb->del_date))}}</td>
                                    
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <p>No records to print out</p>
                        @endif
                        
                        {{-- <tr>
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
                            <td class="col-sm-1">Waybill <h4>Total</h4><br></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1">Qty.: </td>
                            <td class="col-sm-1 pr"><h4>{{ number_format(session('waybillreps')->sum('tot_qty')) }}</td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>