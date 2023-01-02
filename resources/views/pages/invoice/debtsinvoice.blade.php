
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
                            <td class="col-sm-3">Total Amt. : Gh₵</td>
                            <td class="col-sm-3"><b>{{ number_format(session('debts')->sum('tot'), 2) }}</b></td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Tot. Count :</td>
                            <td class="col-sm-3"><b>{{ count(session('debts')) }}</b></td>
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
                        <th>Branch Det.</th>
                        <th>Quantity</th>
                        <th class="pr">Total&nbsp;Gh₵</th>
                        <th>Pay Mode</th>
                        <th>Buyer's Det.</th>
                        <th>Status</th>
                        <th>Date/Time Created</th>
                        {{-- <th class="ryt">Actions</th> --}}
                    </thead>
                    <tbody>
                        <input type="hidden" value="{{$c = 1}}">
                        @if(count(session('debts')) > 0)
                            @foreach (session('debts') as $sale)

                              @if ($sale->del == 'no')
                                
                                @if ($c%2==0)
                                  @if ($sale->del_status == 'Not Delivered')
                                    @if ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid')
                                      <tr class="debt_alert">
                                    @else
                                    <tr class="not_delivered">
                                    @endif
                                  @else
                                    @if ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid')
                                      <tr class="debt_alert">
                                    @else
                                      <tr class="rowColour">
                                    @endif
                                  @endif
                                @else
                                  @if ($sale->del_status == 'Not Delivered')
                                    @if ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid')
                                      <tr class="debt_alert">
                                    @else
                                    <tr class="not_delivered">
                                    @endif
                                  @else
                                    @if ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid')
                                      <tr class="debt_alert">
                                    @else
                                      <tr>
                                    @endif
                                  @endif
                                @endif
                                  <td>{{$c++}}</td>
                                  <td>{{$sale->order_no}}<br>{{$sale->user->status}}<br>User: {{$sale->user->name}}</td>
                                  <td class="pr">{{$sale->qty}}</td>
                                  <td class="pr">{{number_format($sale->tot, 2)}}<br><p class="small_p">- {{number_format($sale->paid_debt, 2)}}</p></td>
                                  <td>{{$sale->pay_mode}}<br>
                                    @if($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid == 'Paid')
                                      <b>{{$sale->paid}}</b>
                                      &nbsp; <i class="fa fa-check" style="color: rgb(0, 163, 0)"></i>
                                    @endif
                                  </td>  
                                  <td>{{$sale->buy_name}}<br>{{$sale->buy_contact}}</td>
                                  <td>{{$sale->del_status}}</td>
                                  <td>{{$sale->created_at}}<br><p style="color: #0071ce; margin: 0">{{$sale->updated_at}}</p></td>  

                                </tr>
                              
                              @endif

                            @endforeach

                        @else
                            <p>No records to print out</p>
                        @endif
                        
                        {{-- <tr>
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
                        </tr> --}}

                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>