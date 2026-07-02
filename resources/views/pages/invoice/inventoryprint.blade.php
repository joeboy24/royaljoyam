<html>

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $showRecycle ? 'Recycle Bin Report' : 'Inventory Report' }} - {{ config('app.name', 'Laravel') }}</title>
    <link href="/maindir/css/font-awesome.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #eceff1;
            color: #333;
        }

        .inventory-print-wrap {
            width: min(1100px, 96%);
            margin: 24px auto 40px;
        }

        .inventory-print-sheet {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
            padding: 30px 40px 32px;
        }

        .inventory-print-header {
            position: relative;
            margin-bottom: 8px;
        }

        .inventory-print-header-top {
            max-width: 520px;
            margin: 0 auto;
            padding: 10px 10px 6px;
            text-align: center;
        }

        .inventory-print-header-top h1 {
            margin: 0;
            font-size: 2.4em;
            font-weight: 700;
            letter-spacing: -1px;
            text-transform: uppercase;
            color: #b11b00;
        }

        .inventory-print-header-top h4 {
            margin: 8px 0 0;
            font-size: 1.05em;
            font-weight: 500;
            letter-spacing: 2px;
            color: #1e262e;
        }

        .inventory-print-loc {
            margin: 8px 0 0;
            font-size: 0.9em;
            font-weight: 300;
            color: #363432;
        }

        .inventory-print-contact {
            margin: 4px 0 0;
            font-size: 0.82em;
            font-weight: 500;
            color: #1e262e;
        }

        .inventory-print-actions {
            position: absolute;
            top: 0;
            right: 0;
        }

        .inventory-print-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1px solid #d0d0d0;
            border-radius: 999px;
            background: #fff;
            color: #455a64;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .inventory-print-btn:hover {
            background: #f5f5f5;
        }

        .inventory-print-subtitle {
            margin: 18px 0 6px;
            font-weight: 300;
            font-size: 0.8em;
            letter-spacing: 0.4px;
            color: #999;
        }

        .inventory-print-meta-line {
            margin: 0 0 16px;
            font-weight: 300;
            font-size: 0.8em;
            color: #666;
            line-height: 1.4;
        }

        .inventory-print-body {
            padding: 0;
        }

        .inventory-print-table-wrap {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .inventory-print-table {
            width: 100%;
            border-collapse: collapse;
        }

        .inventory-print-table thead th {
            padding: 11px 12px;
            background: #f5f5f5;
            border-bottom: 2px solid #dde4e6;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #607d8b;
            text-align: left;
            white-space: nowrap;
        }

        .inventory-print-table tbody td {
            padding: 9px 12px;
            border-bottom: 1px solid #eef2f3;
            font-size: 12px;
            color: #455a64;
            vertical-align: middle;
        }

        .inventory-print-table tbody tr:nth-child(even) {
            background: #fafcfd;
        }

        .inventory-print-table tbody tr:last-child td {
            border-bottom: none;
        }

        .inventory-print-table .col-num,
        .inventory-print-table .col-qty,
        .inventory-print-table .col-price {
            text-align: right;
            white-space: nowrap;
        }

        .inventory-print-table .col-name {
            font-weight: 600;
            color: #263238;
        }

        .inventory-print-stock {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            line-height: 1.2;
            white-space: nowrap;
        }

        .inventory-print-stock-ok {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .inventory-print-stock-low {
            background: #fff8e1;
            color: #f57f17;
        }

        .inventory-print-stock-out {
            background: #ffebee;
            color: #c62828;
        }

        .inventory-print-empty {
            padding: 28px 12px;
            text-align: center;
            color: #90a4ae;
            font-size: 13px;
        }

        .inventory-print-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px solid #eceff1;
            font-size: 11px;
            color: #90a4ae;
        }

        @media (max-width: 768px) {
            .inventory-print-sheet {
                padding: 18px;
            }

            .inventory-print-actions {
                position: static;
                margin-bottom: 10px;
                text-align: right;
            }
        }

        @media print {
            body {
                background: #fff;
            }

            .inventory-print-wrap {
                width: 100%;
                margin: 0;
            }

            .inventory-print-sheet {
                border-radius: 0;
                box-shadow: none;
                padding: 0;
            }

            .inventory-print-actions {
                display: none !important;
            }

            .inventory-print-header-top h1 {
                color: #b11b00 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .inventory-print-stock,
            .inventory-print-table thead th,
            .inventory-print-table tbody tr:nth-child(even) {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .inventory-print-table {
                page-break-inside: auto;
            }

            .inventory-print-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .inventory-print-table thead {
                display: table-header-group;
            }
        }
    </style>
</head>

<body>

    <div class="inventory-print-wrap">
        <div class="inventory-print-sheet">

            <div class="inventory-print-header">
                <div class="inventory-print-actions">
                    <button type="button" class="inventory-print-btn" onclick="window.print()">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>

                <div class="inventory-print-header-top">
                    <h1>Royal Joyam</h1>
                    <h4>Ventures</h4>
                    @if ($company)
                        <p class="inventory-print-loc">{{ $company->address }}</p>
                        <p class="inventory-print-contact">{{ $company->contact }}</p>
                    @endif
                </div>
            </div>

            {{-- <p class="inventory-print-subtitle">
                {{ $showRecycle ? 'Inventory Recycle Bin Report' : 'Inventory Report' }}
            </p> --}}

            <p class="inventory-print-meta-line text-center">
                {{ $showRecycle ? 'Inventory Recycle Bin Report' : 'Inventory Report' }}
                <strong>Generated on:</strong> {{ now()->format('d M Y, H:i') }}
                &nbsp;&nbsp;·&nbsp;&nbsp;
                <strong>Records:</strong> {{ number_format($filteredItemCount) }} {{ $filteredItemCount === 1 ? 'item' : 'items' }}
                &nbsp;&nbsp;·&nbsp;&nbsp;
            </p>

            <div class="inventory-print-body">
                <div class="inventory-print-table-wrap">
                    <table class="inventory-print-table">
                        <thead>
                            <tr>
                                <th class="col-num">#</th>
                                <th>Item No.</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th class="col-qty">Qty</th>
                                <th>Stock</th>
                                <th class="col-price">Price (Gh₵)</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                @php $stockLevel = $item->stockLevel($lowStockThreshold); @endphp
                                <tr>
                                    <td class="col-num">{{ $loop->iteration }}</td>
                                    <td>{{ $item->item_no }}</td>
                                    <td class="col-name">{{ $item->name }}</td>
                                    <td>{{ $item->cat }}</td>
                                    <td class="col-qty">{{ $item->qty }}</td>
                                    <td>
                                        <span class="inventory-print-stock inventory-print-stock-{{ $stockLevel }}">
                                            {{ $item->stockBadgeLabel($lowStockThreshold) }}
                                        </span>
                                    </td>
                                    <td class="col-price">{{ number_format((float) $item->price, 2) }}</td>
                                    <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="inventory-print-empty">No records to print.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="inventory-print-footer">
                    <span>Royal Joyam Ventures — {{ $showRecycle ? 'Recycle bin' : 'Inventory' }}</span>
                    <span>Printed {{ now()->format('d M Y, H:i') }}</span>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
