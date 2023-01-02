
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
                        <th>Item Details</th>
                        @foreach (session('compbranch') as $br)
                            <th class="pr">Br.{{$br->tag}}</th>
                        @endforeach
                        <th class="pr">Date/Time Updated</th>
                    </thead>
                    <tbody>
                        @if(count($items) > 0)
                            @foreach ($items as $item)
                                @if ($item->del != 'yes')
                                <tr>
                                    <td class="col-sm-1">{{$count++}}</td>
                                    <td>{{$item->item_no.' - '.$item->name}}<br><p style="color: #aaa; font-size: 0.9em">{{$item->cat.' - '.$item->desc}}</p></td>
                                    @foreach (session('compbranch') as $br)
                                        <input type="hidden" value="{{$x = 'q'.$br->tag}}">
                                        <td class="col-sm-1 pr">{{$item->$x}}</td>
                                    @endforeach
                                    <td class="col-sm-2 pr">{{$item->updated_at}}</td>
                                    {{-- <td class="col-sm-6"><h4>{{$sale->order_no}}</h4><p>Paid by {{$sale->buy_name}} &nbsp;&nbsp;&nbsp; Tel: {{$sale->buy_contact}}<br>
                                        Pay Mode: {{$sale->pay_mode}} &nbsp;&nbsp; Qty.: {{$sale->qty}}</p></td>
                                    <td class="col-sm-1 pr">{{number_format($sale->tot)}}.00</td>
                                    <td class="col-sm-2 pr">{{$sale->del_status}}</td>
                                    <td class="col-sm-2 pr">{{$sale->created_at}}</td> --}}
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <p>No records to print out</p>
                        @endif
                        
                        <tr>
                            <td class="col-sm-1">{{$count++}}</td>
                            <td class="col-sm-6"><h4>Stock Count: {{ count($items) }}</h4></td>
                            <td class="col-sm-1 pr">{{ $items->sum('q1') }}</td>
                            <td class="col-sm-1 pr">{{ $items->sum('q2') }}</td>
                            <td class="col-sm-1 pr">{{ $items->sum('q3') }}</td>
                            <td class="col-sm-1 pr">{{ $items->sum('q4') }}</td>
                            <td class="col-sm-1 pr">{{ $items->sum('q5') }}</td>
                            <td class="col-sm-2 pr"> - </td>
                        </tr>
                        <tr class="invTot">
                            <td class="col-sm-1">Stock <h4>Total</h4><br></td>
                            <td class="col-sm-6"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"></td>
                            <td class="col-sm-1 pr"><h4>{{ $items->sum('q1')+$items->sum('q2')+$items->sum('q3')+$items->sum('q4')+$items->sum('q5') }}</h4></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>