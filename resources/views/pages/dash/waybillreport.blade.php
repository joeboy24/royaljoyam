@extends('layouts.dashlay')

@section('content')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-11">

          @include('inc.messages')

          <div class="row">
            <div class="col-md-12">
              <div class="menu_box_cont">
                <div class="inner">
                  <a href="/reporting" class="myA">
                    <div class="menu_box">
                      <h4><i class="fa fa-shopping-basket"></i>&nbsp;&nbsp; Sales</h4>
                      <p>General sales report</p>
                    </div>
                  </a>
                  <a href="/stockbal" class="myA">
                    <div class="menu_box">
                      <h4><i class="fa fa-bar-chart"></i>&nbsp;&nbsp; Stock</h4>
                      <p>General stock balances</p>
                    </div>
                  </a>
                  <a href="/expensereport" class="myA">
                    <div class="menu_box">
                      <h4><i class="fa fa-suitcase"></i>&nbsp;&nbsp; Expenses</h4>
                      <p>General expenses report</p>
                    </div>
                  </a>
                  <a href="/debts" class="myA">
                    <div class="menu_box">
                      <h4><i class="fa fa-folder-open"></i>&nbsp;&nbsp; Debts</h4>
                      <p>Debts (Post Payments)</p>
                    </div>
                  </a>
                  <a href="/waybillreport" class="myA">
                    <div class="menu_box active_menu">
                      <h4><i class="fa fa-truck"></i>&nbsp;&nbsp; Waybill</h4>
                      <p>Waybill Report</p>
                    </div>
                  </a>
                  <a href="/returnsreport" class="myA">
                    <div class="menu_box">
                      <h4><i class="fa fa-warning"></i>&nbsp;&nbsp; Returns</h4>
                      <p>Returns report</p>
                    </div>
                  </a>
                  <a href="/distreport" class="myA">
                    <div class="menu_box">
                      <h4><i class="fa fa-share-alt"></i>&nbsp;&nbsp; Distribution</h4>
                      <p>Distribution report</p>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group row mb-0 hideMe">
            <div class="col-md-12 myTrim">
              <form class="inventory-filter-form" action="{{ url('/waybillreport') }}" method="GET">
                <div class="inventory-filter-row">
                  <div class="inventory-filters-panel">
                    <div class="inventory-filters-heading">
                      <span class="inventory-filters-label">
                        <i class="fa fa-calendar"></i>
                        Date range
                        @if (! empty($date_from) || ! empty($date_to))
                          <span class="inventory-search-active-dot" title="Filter active"></span>
                        @endif
                      </span>
                    </div>

                    <div class="inventory-filters-controls">
                      <label class="inventory-filter-field inventory-filter-field-compact">
                        <span class="inventory-filter-field-icon"><i class="fa fa-calendar"></i></span>
                        <input type="date" class="inventory-date-input" name="date_from" value="{{ $date_from ?? '' }}"/>
                      </label>

                      <span class="inventory-date-separator">to</span>

                      <label class="inventory-filter-field inventory-filter-field-compact">
                        <span class="inventory-filter-field-icon"><i class="fa fa-calendar"></i></span>
                        <input type="date" class="inventory-date-input" name="date_to" value="{{ $date_to ?? '' }}"/>
                      </label>

                      <button type="submit" class="inventory-search-btn inventory-search-btn-primary">
                        <i class="fa fa-filter"></i>
                        <span>Load data</span>
                      </button>

                      <a href="/waybillreport" class="inventory-search-btn inventory-search-btn-clear" title="Clear filters">
                        <i class="fa fa-refresh"></i>
                        <span>Clear</span>
                      </a>

                      <a href="{{ url('/waybillprint?' . http_build_query(array_filter(['date_from' => $date_from ?? null, 'date_to' => $date_to ?? null]))) }}" class="inventory-search-btn inventory-search-btn-muted" title="Print report" target="_blank" rel="noopener">
                        <i class="fa fa-print"></i>
                        <span>Print</span>
                      </a>

                      <a href="{{ url('/waybillreport/export?' . http_build_query(array_filter(['date_from' => $date_from ?? null, 'date_to' => $date_to ?? null]))) }}" class="inventory-search-btn inventory-search-btn-muted dash-tip" data-tip="Export CSV">
                        <i class="fa fa-download"></i>
                        <span>Export</span>
                      </a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="card">
            <x-dash-page-header
              title="Waybill Report"
              subtitle="Filter waybills by created date and review totals."
              icon="fa fa-truck"
            />

            <div id="printarea1" class="card-body">
              @if ($waybills->total() > 0)
                <div class="table-responsive">
                  <table class="table mt">
                    <thead class="text-secondary hideMe">
                      <tr>
                        <th>#</th>
                        <th>Stock No.</th>
                        <th>Company</th>
                        <th>Driver</th>
                        <th>Bill No.</th>
                        <th>Weight</th>
                        <th>Pieces</th>
                        <th>Qty.</th>
                        <th class="waybill-table-status-col">Status</th>
                        <th class="waybill-table-date-col">Delivery Date</th>
                      </tr>
                    </thead>
                    <tbody id="tb">
                      @foreach ($waybills as $wb)
                        <tr @class(['rowColour' => $c % 2 === 0])>
                          <td>{{ $c++ }}</td>
                          <td>
                            {{ $wb->stock_no }}
                            <p class="waybill-table-meta">User: {{ $wb->user->name }}</p>
                          </td>
                          <td>
                            {{ $wb->comp_name }}, {{ $wb->comp_add }}
                            <p class="waybill-table-meta">{{ $wb->comp_contact }}</p>
                          </td>
                          <td>
                            {{ $wb->drv_name }}<br>{{ $wb->drv_contact }}
                            <p class="waybill-table-meta">{{ $wb->vno }}</p>
                          </td>
                          <td>{{ $wb->bill_no }}</td>
                          <td>{{ $wb->weight ?: '—' }}</td>
                          <td>{{ $wb->nop ?: '—' }}</td>
                          <td>{{ $wb->tot_qty ?: '—' }}</td>
                          <td class="waybill-table-status-col">
                            <x-waybill-status-badge :status="$wb->status" />
                          </td>
                          <td class="waybill-table-date-col">{{ $wb->formattedDeliveryDate() }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <div class="waybill-list-footer">
                  <p class="mb-0">
                    No. of records: <strong>{{ $waybills->total() }}</strong>
                    &nbsp;&nbsp; Total qty.: <strong>{{ number_format($reportSummary['total_qty'] ?? 0) }}</strong>
                  </p>
                  @if (! empty($reportSummary['by_status']))
                    <div class="waybill-report-status-summary">
                      @foreach ($reportSummary['by_status'] as $status => $statusCount)
                        @if ($statusCount > 0)
                          <span class="waybill-report-status-chip">{{ $status }}: <strong>{{ $statusCount }}</strong></span>
                        @endif
                      @endforeach
                    </div>
                  @endif
                  {{ $waybills->appends(['date_from' => request()->query('date_from'), 'date_to' => request()->query('date_to')])->links() }}
                </div>
              @else
                <div class="dash-empty-state">
                  <span class="dash-empty-state-icon"><i class="fa fa-bar-chart"></i></span>
                  <h3 class="dash-empty-state-title">No waybill records found</h3>
                  <p class="dash-empty-state-text">
                    @if (! empty($date_from) || ! empty($date_to))
                      No waybills match the selected date range. Try different dates or clear the filter.
                    @else
                      Choose a date range above and click Load data to generate the report.
                    @endif
                  </p>
                </div>
              @endif
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

@endsection
