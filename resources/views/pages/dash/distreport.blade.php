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
                    <div class="menu_box">
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
                    <div class="menu_box active_menu">
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
              <form class="inventory-filter-form" action="{{ action('DashController@distreport') }}" method="GET">
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

                      <a href="/distreport" class="inventory-search-btn inventory-search-btn-clear" title="Clear filters">
                        <i class="fa fa-refresh"></i>
                        <span>Clear</span>
                      </a>

                      <a href="/distreportprint" class="inventory-search-btn inventory-search-btn-muted" title="Print report">
                        <i class="fa fa-print"></i>
                        <span>Print</span>
                      </a>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <div class="card">
            <x-dash-page-header
              title="Distribution Report"
              subtitle="Review branch distribution history by date."
              icon="fa fa-share-alt"
            />

            <div id="printarea1" class="card-body">
              @if ($wbdreports->total() > 0)
                @php
                  $branchKeys = collect(session('compbranch', []))->keys()->map(fn ($index) => 'q'.($index + 1));
                @endphp
                <div class="table-responsive">
                  <table class="table mt">
                    <thead class="text-secondary hideMe">
                      <tr>
                        <th>#</th>
                        <th>Item</th>
                        @foreach (session('compbranch', []) as $br)
                          <th>Br {{ $br->tag }}</th>
                        @endforeach
                        <th class="ryt">Date Distributed</th>
                      </tr>
                    </thead>
                    <tbody id="tb">
                      @foreach ($wbdreports as $wbd)
                        @if ($wbd->del == 'no')
                          <tr @class(['rowColour' => $c % 2 === 0])>
                            <td>{{ $c++ }}</td>
                            <td>
                              @if ($wbd->item)
                                {{ $wbd->item->item_no.' - '.$wbd->item->name }}
                                @if ($wbd->waybill)
                                  <p class="waybill-table-meta">{{ $wbd->waybill->comp_name }}</p>
                                @endif
                              @else
                                <span class="text-muted">Item unavailable</span>
                              @endif
                            </td>
                            @foreach (session('compbranch', []) as $branchIndex => $br)
                              @php $qtyKey = 'q'.($branchIndex + 1); @endphp
                              <td>{{ $wbd->{$qtyKey} ?? 0 }}</td>
                            @endforeach
                            <td class="ryt">{{ date('M. d, Y', strtotime($wbd->created_at)) }}</td>
                          </tr>
                        @endif
                      @endforeach

                      <tr>
                        <td></td>
                        <td><b>Total Qty.:</b> {{ $branchKeys->sum(fn ($key) => $wbdreports->sum($key)) }}</td>
                        @foreach ($branchKeys as $key)
                          <td><b>{{ number_format($wbdreports->sum($key)) }}</b></td>
                        @endforeach
                        <td></td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div class="waybill-list-footer">
                  <p class="mb-0">
                    No. of records: <strong>{{ $wbdreports->total() }}</strong>
                  </p>
                  {{ $wbdreports->appends(['date_from' => request()->query('date_from'), 'date_to' => request()->query('date_to')])->links() }}
                </div>
              @else
                <div class="dash-empty-state">
                  <span class="dash-empty-state-icon"><i class="fa fa-share-alt"></i></span>
                  <h3 class="dash-empty-state-title">No distribution records found</h3>
                  <p class="dash-empty-state-text">
                    @if (! empty($date_from) || ! empty($date_to))
                      No distributions match the selected date range. Try different dates or clear the filter.
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
