<html>
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} — Daily close {{ $dailyClose->close_date }}</title>
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

            <x-report-print-meta
              title="Daily close"
              :date-from="$printMeta['date_from'] ?? $dailyClose->close_date"
              :date-to="$printMeta['date_to'] ?? $dailyClose->close_date"
              :branch="$printMeta['branch'] ?? $dailyClose->branch_label"
            />

            <div class="invCenter">
                <table class="invCenterTbl">
                    <tbody>
                        <tr>
                            <td class="col-sm-3">Cash :</td>
                            <td class="col-sm-3">{{ number_format((float) $dailyClose->cash, 2) }}</td>
                            <td class="col-sm-3">Cheque :</td>
                            <td class="col-sm-3">{{ number_format((float) $dailyClose->cheque, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Mobile Money :</td>
                            <td class="col-sm-3">{{ number_format((float) $dailyClose->momo, 2) }}</td>
                            <td class="col-sm-3">Debt sold :</td>
                            <td class="col-sm-3">{{ number_format((float) $dailyClose->debt_sold, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Collected debt :</td>
                            <td class="col-sm-3">{{ number_format((float) $dailyClose->collected_debt, 2) }}</td>
                            <td class="col-sm-3">Expenses :</td>
                            <td class="col-sm-3">{{ number_format((float) $dailyClose->expenses, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Gross collected :</td>
                            <td class="col-sm-3"><b>{{ number_format((float) $dailyClose->gross_collected, 2) }}</b></td>
                            <td class="col-sm-3">Net total :</td>
                            <td class="col-sm-3"><b>{{ number_format((float) $dailyClose->net_total, 2) }}</b></td>
                        </tr>
                        <tr>
                            <td class="col-sm-3">Counted cash :</td>
                            <td class="col-sm-3">{{ $dailyClose->counted_cash !== null ? number_format((float) $dailyClose->counted_cash, 2) : '—' }}</td>
                            <td class="col-sm-3">Variance :</td>
                            <td class="col-sm-3">{{ $dailyClose->variance !== null ? number_format((float) $dailyClose->variance, 2) : '—' }}</td>
                        </tr>
                        @if ($dailyClose->notes)
                            <tr>
                                <td class="col-sm-3">Notes :</td>
                                <td class="col-sm-9" colspan="3">{{ $dailyClose->notes }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <script>window.print();</script>
</body>
</html>
