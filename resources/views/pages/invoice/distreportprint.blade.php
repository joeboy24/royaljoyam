
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
            
            <p style="color: #aaa; font-size: 0.9em; letter-spacing: 0.5px">Stock Update / Distribution Report</p>
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
                        <th>Item</th>
                        @foreach (session('compbranch') as $br)
                            <th class="col-sm-1 pr">Br {{$br->tag}}</th>
                        @endforeach
                        <th class="pr">Date Distributed</th>
                    </thead>
                    <tbody>
                        @if(count(session('wbdreports')) > 0)
                            @foreach (session('wbdreports') as $wbd)
                                @if ($wbd->del != 'yes')
                                <tr>
                                    <td>{{$count++}}</td>
                                    <td>{{$wbd->item->item_no.' - '.$wbd->item->name}}<br><p class="small_p">{{$wbd->waybill->comp_name}}</p></td>
                                    @foreach (session('compbranch') as $br)
                                      <input type="hidden" value="{{$x = 'q'.$br->tag}}">
                                      <td class="col-sm-1 pr">{{$wbd->$x}}</td>
                                    @endforeach
                                    <td class="pr">{{date('M. d, Y', strtotime($wbd->created_at))}}</td>
                                </tr>
                                @endif
                            @endforeach
                        @else
                            <p>No records to print out</p>
                        @endif
                       
                        <tr class="invTot">
                            <td class="col-sm-1"></td>
                            <td class="col-sm-1"><h4>Total</h4>Distribution<br></td>
                            @foreach (session('compbranch') as $br)
                                <input type="hidden" value="{{$x = 'q'.$br->tag}}">
                                <input type="hidden" value="{{$sum = $sum + session('wbdreports')->sum($x)}}">
                                <td class="col-sm-1 pr"><h4>{{ number_format(session('wbdreports')->sum($x))}}</h4></td>
                            @endforeach
                            <td class="col-sm-1 pr"><h4>
                                {{session('wbdreports')->sum(['q1'])+session('wbdreports')->sum(['q2'])+
                                session('wbdreports')->sum(['q3'])+session('wbdreports')->sum(['q4'])+
                                session('wbdreports')->sum(['q5'])+session('wbdreports')->sum(['q6'])+
                                session('wbdreports')->sum(['q7'])}}</h4>
                            </td>

                        </tr>
                        
                    </tbody>
                </table>
            </div>

        </div>
    </section>

</body>

</html>