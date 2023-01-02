
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

            @include('inc.messages')
            <form action="{{ action('ItemsController@store') }}" method="POST">
                @csrf
                @if (date('Y') == $yr)
                    {{-- <p>{{date('Y').' == '.$yr}}</p> --}}
                    @if (session('mth_openning') == 1)
                        @if ($closure_state == 'open')
                            <button name="store_action" value="closure" class="closure_btn">Close current month</button>
                        @endif
                    @else
                        <button name="store_action" value="set_closure" class="closure_btn2">Open current month</button>
                    @endif
                @endif
            </form>
            
            <div class="invCenter">
                <table class="invCenterTbl">
                    <tbody>
                        <tr>
                            <td class="col-sm-2 pr">Month :</td>
                            @if (session('cldate') != '')
                                <td class="col-sm-3">{{ date('F, Y', strtotime(session('cldate'))) }}</td>
                            @else
                                <td class="col-sm-3">Today</td>
                            @endif
                            <td class="col-sm-2"></td>
                            <td class="col-sm-3"></td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 pr"><h5>Items Summary</h5></td>
                            <td class="col-sm-3">{{ session('date_to') }}</td>
                            <td class="col-sm-2"><b>Total Gh₵</b></td>
                            <td class="col-sm-3"><b>Profit Gh₵</b></td>
                        </tr>
                        <tr>
                            <td class="col-sm-2 pr">Qty. Sold :</td>
                            <td class="col-sm-3">{{number_format(session('sales_history')->sum('qty'))}}</td>
                            <td class="col-sm-2">{{number_format(session('sales_history')->sum('tot'), 2)}}</td>
                            <td class="col-sm-2">{{number_format(session('sales_history')->sum('profits'), 2)}}</td>
                            {{-- <td class="col-sm-3">Royal Joham V... {{session('company')->contact}}</td> --}}
                        </tr>
                        <tr>
                            <td class="col-sm-2 pr">Qty. Available :</td>
                            <td class="col-sm-3">{{number_format(session('items')->sum('qty'))}}</td>
                            {{-- <td class="col-sm-2">172,120.30</td> --}}
                            {{-- <td class="col-sm-3">Royal Joham V... {{session('company')->contact}}</td> --}}
                        </tr>
                        <!--tr>
                            <td class="col-sm-3">Payment Methods :</td>
                            <td class="col-sm-3">Cash/Cheque/Momo..</td>
                            <td class="col-sm-2">Report Date :</td>
                            {{-- <td class="col-sm-3">{{date('d-m-Y')}}</td> --}}
                        </tr-->
                    </tbody>
                </table>
            </div>

            <div class="invBottom">

                <p class="closure_top">Distribution Summary</p>

                @if(count($distribution) > 0)
                    <table class="invBottomTbl">

                        <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Items Distributed</th>
                            <th class="pr">All</th>
                            <th>Branches</th>
                            @for ($i = 0; $i < count(session('compbranch'))-2; $i++)
                                <th>&nbsp;</th>
                            @endfor
                            <th class="pr">Total&nbsp;Gh₵</th>
                        </thead>
                        
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                @foreach (session('compbranch') as $br)
                                    <td class="ryt pr">&nbsp;&nbsp;&nbsp;Branch&nbsp;{{$br->tag}} <p class="small_p">{{substr($br->name, 0,8)}}..</p></td>
                                @endforeach
                            </tr>
                            @foreach ($dist_dist as $dist)
                                <tr>
                                    <td class="col-sm-1">{{$p++}}</td>
                                    <td>{{$dist->item->name}}<br><p style="color: rgb(0, 139, 226); font-size: 0.9em">{{$dist->item->cat.' - '.$dist->item->desc}}</p></td>
                                    
                                        @foreach ($distribution as $distr)

                                            @if ($distr->item_id == $dist->item_id)
                                                {{-- <input type="hidden" value="{{$m = 'q'.$i+1}}"> --}}

                                                <input type="hidden" value="{{$br1 = $br1 + $distr->q1}}">
                                                <input type="hidden" value="{{$br2 = $br2 + $distr->q2}}">
                                                <input type="hidden" value="{{$br3 = $br3 + $distr->q3}}">
                                                <input type="hidden" value="{{$br4 = $br4 + $distr->q4}}">
                                                <input type="hidden" value="{{$br5 = $br5 + $distr->q5}}">
                                                {{-- <input type="hidden" value="{{$br6 = $br6 + $distr->q6}}">
                                                <input type="hidden" value="{{$br7 = $br7 + $distr->q7}}"> --}}
                                            @endif
                                                
                                        @endforeach

                                        {{-- @for ($z = 1; $z <= count(session('compbranch')); $z++)
                                            <input type="hidden" value="{{$vl = '$br'.$z}}">
                                            <td class="col-sm-1 pr">{{ $vl }}</td>
                                        @endfor --}}
                                        <td class="col-sm-1 pr">{{ number_format($br1) }}</td>
                                        <td class="col-sm-1 pr">{{ number_format($br2) }}</td>
                                        <td class="col-sm-1 pr">{{ number_format($br3) }}</td>
                                        <td class="col-sm-1 pr">{{ number_format($br4) }}</td>
                                        <td class="col-sm-1 pr">{{ number_format($br5) }}</td>
                                        {{-- <td class="col-sm-1 pr">{{ number_format($br6) }}</td>
                                        <td class="col-sm-1 pr">{{ number_format($br7) }}</td> --}}
                                        <td class="col-sm-1 pr">{{ number_format($br1+$br2+$br3+$br4+$br5+$br6+$br7) }}</td>
                                        @php
                                            $br1 = 0; $br2 = 0; $br3 = 0; $br4 = 0; $br5 = 0; $br6 = 0; $br7 = 0; 
                                        @endphp
                                        
                                </tr>
                            @endforeach
                            
                            {{-- <tr>
                                <td class="col-sm-1">{{$count++}}</td>
                                <td class="col-sm-6"><h4>Stock Count: {{ count($items) }}</h4></td>
                                <td class="col-sm-1 pr">{{ $items->sum('q1') }}</td>
                                <td class="col-sm-1 pr">{{ $items->sum('q2') }}</td>
                                <td class="col-sm-1 pr">{{ $items->sum('q3') }}</td>
                                <td class="col-sm-1 pr">{{ $items->sum('q4') }}</td>
                                <td class="col-sm-1 pr">{{ $items->sum('q5') }}</td>
                                <td class="col-sm-2 pr"> - </td>
                            </tr> --}}
                            <tr class="invTot">
                                <td class="col-sm-1"><h4>Total</h4></td>
                                <td class="col-sm-6">Distribution</td>
                                @foreach (session('compbranch') as $br)
                                    <input type="hidden" value="{{$m = 'q'.$br->tag}}">
                                    <td class="col-sm-1 pr">{{$distribution->sum($m)}}</td>
                                @endforeach
                                <td class="col-sm-1 pr"><h4>{{ number_format($distribution->sum('q1')+$distribution->sum('q2')+$distribution->sum('q3')+$distribution->sum('q4')+$distribution->sum('q5')+$distribution->sum('q6')+$distribution->sum('q7'), 2) }}</h4></td>
                            </tr>
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger">
                        No Distribution Records Found 
                    </div>
                @endif

                <p>&nbsp;</p>
                <p class="closure_top">Sales Summary</p>

                @if (count(session('stock')) > 0)

                    <div id="table_inner">
                        <table class="invBottomTbl mt">
                            <thead class=" text-secondary hideMe">
                                <th>#</th>
                                <th>Items</th>
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
                    </div>

                @else
                    <p>No Records Found</p>
                @endif
            </div>

        </div>
    </section>

</body>

</html>