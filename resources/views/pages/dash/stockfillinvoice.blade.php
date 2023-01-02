
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
                {{-- <table class="invBottomTbl">
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
                </table> --}}

                @if (count(session('stock')) > 0)
                    <table class="invBottomTbl mt">
                        <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Item Details.</th>
                            @foreach (session('compbranch') as $br)
                            <th></th>
                            <th class="ryt">Branch</th>
                            <th>{{$br->tag}}</th>
                            <th></th>
                            @endforeach
                        </thead>

                        <tbody id="tb">

                            <tr>
                            <td></td>
                            <td></td>
                            @foreach (session('compbranch') as $br)
                                <td class="ryt avl2">QTY<br><p class="small_gr">SOLD</p></td>
                                <td class="ryt avl2">TOTAL<br><p class="small_gr">AMT. Gh₵</p></td>
                                <td class="added">QTY<br><p class="small_gr">REM</p></td>
                                <td class="avl2">PROFIT<br><p class="small_gr">Gh₵</p></td>
                            @endforeach
                            </tr>

                            @foreach (session('stock') as $stk)
                                
                            <tr>
                            <td>{{$x++}}</td>
                            <td>{{$stk->item->name}}<br><p class="small_p">{{$stk->item->item_no.' - '.$stk->item->desc}}</p></td>
                            @for ($i = 0; $i < count(session('compbranch')); $i++)
                                <input type="hidden" value="{{$qval = 'q'.$i+1}}">
                                <input type="hidden" name="tvalue" value="{{$qtr = $stk->item->$qval}}">
                                <input type="hidden" name="tvalue" value="{{$qtr_tot = $qtr_tot + $stk->item->$qval}}">
                                @foreach (session('sales_history') as $sh)
                                    @if ($stk->item_id == $sh->item_id && $sh->user_bv == $i+1)
                                        <input type="hidden" value="{{$qts = $qts + $sh->qty}}">
                                        <input type="hidden" value="{{$tamt = $tamt + $sh->tot}}">
                                        <input type="hidden" value="{{$tprof = $tprof + $sh->profits}}">
                                    @endif
                                @endforeach
                                <td class="ryt avl2 c1">@if($qts!=0){{number_format($qts)}}@else-@endif</td>
                                <td class="ryt avl2 c2">@if($tamt!=0){{number_format($tamt)}}@else-@endif</td>
                                <td class="added c3">{{number_format($qtr)}}</td>
                                <td class="avl c4">@if($tprof!=0){{number_format($tprof, 2)}}@else-@endif</td>
                            
                                <input type="hidden" value="{{$qts=0}}">
                                <input type="hidden" value="{{$tamt=0}}">
                                <input type="hidden" value="{{$tprof=0}}">
                            @endfor
                            <input type="hidden" value="{{$qtr=0}}">
                            </tr>

                            @endforeach

                            <tr>
                                <td></td>
                                <td><h6>Total</h6></td>
                                @for ($i = 0; $i < count(session('compbranch')); $i++)
                                    
                                    <input type="hidden" name="tvalue" value="{{$qtr_tot = $qtr_tot + $stk->item->$qval}}">
                                    @foreach (session('sales_history') as $sh)
                                        @if ($sh->user_bv == $i+1)
                                        <input type="hidden" value="{{$qts = $qts + $sh->qty}}">
                                        <input type="hidden" value="{{$tamt = $tamt + $sh->tot}}">
                                        <input type="hidden" value="{{$tprof = $tprof + $sh->profits}}">
                                        @endif
                                    @endforeach

                                    <input type="hidden" value="{{$qval = 'q'.$i+1}}">
                                    <td class="ryt avl2 c1"><h6>@if($qts!=0){{number_format($qts)}}@endif</h6></td>
                                    <td class="ryt avl2 c2"><h6>@if($tamt!=0){{number_format($tamt)}}@endif</h6></td>
                                    <td class="added c3">
                                        <!--h6>number_format($qtr)</h6-->
                                    </td>
                                    <td class="avl c4"><h6>@if($tprof!=0){{number_format($tprof, 2)}}@endif</h6></td>
                                    
                                    <input type="hidden" value="{{$qts=0}}">
                                    <input type="hidden" value="{{$tamt=0}}">
                                    <input type="hidden" value="{{$tprof=0}}">
                                @endfor
                            </tr>

                        </tbody>
                    </table>

                @else
                    <p>No Records Found</p>
                @endif
            </div>

        </div>
    </section>

</body>

</html>