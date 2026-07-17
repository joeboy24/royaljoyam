<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} — {{ $month_label }} closure</title>
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
                <P class="locInfo">{{ session('company')->address }}</P>
                <P class="contactInfo">{{ session('company')->contact }}, {{ session('company')->email }}</P>
            </div>

            <div style="height: 50px"></div>

            <p style="color: #aaa; font-size: 0.9em; letter-spacing: 0.5px">
              Month-end closure — {{ $month_label }} ({{ ucfirst(str_replace('_', ' ', $closure_status)) }})
            </p>

            <div class="invCenter">
                <table class="invCenterTbl">
                    <tbody>
                        <tr>
                            <td class="col-sm-2">Date From :</td>
                            <td class="col-sm-2">{{ $printMeta['date_from'] ?? $date_from }}</td>
                            <td class="col-sm-2">Qty sold :</td>
                            <td class="col-sm-2">{{ number_format($summary['qty_sold']) }}</td>
                            <td class="col-sm-2">Total Gh₵ :</td>
                            <td class="col-sm-2">{{ number_format($summary['amt_sold'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-2">Date To :</td>
                            <td class="col-sm-2">{{ $printMeta['date_to'] ?? $date_to }}</td>
                            <td class="col-sm-2">Profit Gh₵ :</td>
                            <td class="col-sm-2">{{ number_format($summary['profit'], 2) }}</td>
                            <td class="col-sm-2">Qty available :</td>
                            <td class="col-sm-2">{{ number_format($summary['qty_available']) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="invBottom">
                <p class="closure_top">Branch summary</p>
                <table class="invBottomTbl">
                    <thead>
                        <th>#</th>
                        <th>Branch</th>
                        <th class="pr">Qty sold</th>
                        <th class="pr">Amount Gh₵</th>
                        <th class="pr">Profit Gh₵</th>
                    </thead>
                    <tbody>
                        @foreach ($branch_summaries as $index => $branch)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $branch['name'] }}</td>
                                <td class="pr">{{ number_format($branch['qty_sold']) }}</td>
                                <td class="pr">{{ number_format($branch['amt_sold'], 2) }}</td>
                                <td class="pr">{{ number_format($branch['profit'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p>&nbsp;</p>
                <p class="closure_top">Distribution summary</p>
                @if (count($distribution_rows) > 0)
                    <table class="invBottomTbl">
                        <thead>
                            <th>#</th>
                            <th>Item</th>
                            @foreach ($branches as $branch)
                                <th class="pr">{{ $branch->name }}</th>
                            @endforeach
                            <th class="pr">Total</th>
                        </thead>
                        <tbody>
                            @foreach ($distribution_rows as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row['name'] }}</td>
                                    @foreach ($branches as $branch)
                                        @php $column = $column_keys[(string) $branch->tag] ?? null; @endphp
                                        <td class="pr">{{ $column ? number_format($row['quantities'][$column] ?? 0) : '—' }}</td>
                                    @endforeach
                                    <td class="pr">{{ number_format($row['total']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No distribution records</p>
                @endif

                <p>&nbsp;</p>
                <p class="closure_top">Sales summary</p>
                @if (count($sales_rows) > 0)
                    <table class="invBottomTbl">
                        <thead>
                            <th>#</th>
                            <th>Item</th>
                            @foreach ($branches as $branch)
                                <th class="pr">{{ $branch->name }} qty</th>
                                <th class="pr">{{ $branch->name }} amt</th>
                            @endforeach
                        </thead>
                        <tbody>
                            @foreach ($sales_rows as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row['name'] }}</td>
                                    @foreach ($branches as $branch)
                                        @php $cell = $row['branches'][(string) $branch->tag] ?? ['qty_sold' => 0, 'amt_sold' => 0]; @endphp
                                        <td class="pr">{{ $cell['qty_sold'] != 0 ? number_format($cell['qty_sold']) : '—' }}</td>
                                        <td class="pr">{{ $cell['amt_sold'] != 0 ? number_format($cell['amt_sold'], 2) : '—' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No sales records</p>
                @endif
            </div>
        </div>
    </section>
    <script>window.print();</script>
</body>
</html>
