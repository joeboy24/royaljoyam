@extends('layouts.dashlay')

@section('content')

  <div class="content dash-closure-content">
    <div class="container-fluid dash-closure-body">

      @include('inc.messages')

      <div class="card dash-closure-card">
        <x-dash-page-header
          title="{{ $month_label }}"
          subtitle="Month-end summary across all branches."
          icon="fa fa-calendar-check-o"
        >
          <x-slot:actions>
            <a href="/closure_page" class="dash-page-header-btn inventory-action-btn dash-tip" data-tip="Back to month list">
              <i class="fa fa-arrow-left"></i>
              <span>All months</span>
            </a>
          </x-slot:actions>
        </x-dash-page-header>

        <div class="card-body dash-form-body dash-closure-panel">

          <section class="dash-closure-status-bar">
            <div class="dash-closure-status-bar-main">
              <span @class([
                'dash-closure-status',
                'dash-closure-status--lg',
                'dash-closure-status--open' => $closure_status === 'open',
                'dash-closure-status--closed' => $closure_status === 'closed',
                'dash-closure-status--pending' => $closure_status === 'not_opened',
              ])>
                @if ($closure_status === 'open')
                  Open
                @elseif ($closure_status === 'closed')
                  Closed
                @else
                  Not opened
                @endif
              </span>

              <div class="dash-closure-status-copy">
                @if ($closure_status === 'open')
                  <p class="dash-closure-status-title">This month is open for sales and expenses.</p>
                  <p class="dash-closure-status-text">Close the month when period totals should be locked.</p>
                @elseif ($closure_status === 'closed')
                  <p class="dash-closure-status-title">This month has been closed.</p>
                  <p class="dash-closure-status-text">
                    @if ($closure && $closure->updated_at)
                      Closed on {{ $closure->updated_at->format('d M Y, H:i') }}.
                    @else
                      Snapshot totals are stored for this period.
                    @endif
                  </p>
                @else
                  <p class="dash-closure-status-title">This month has not been opened yet.</p>
                  <p class="dash-closure-status-text">Open the month to allow staff sales and expenses for this period.</p>
                @endif
              </div>
            </div>

            <div class="dash-closure-status-actions">
              @if ($closure_status === 'not_opened')
                <form action="{{ action('ItemsController@store') }}" method="POST" class="dash-closure-action-form">
                  @csrf
                  <button
                    type="submit"
                    name="store_action"
                    value="set_closure"
                    class="inventory-action-btn inventory-action-btn-primary"
                  >
                    <i class="fa fa-unlock-alt"></i>
                    <span>Open month</span>
                  </button>
                </form>
              @elseif ($closure_status === 'open')
                <form
                  action="{{ action('ItemsController@store') }}"
                  method="POST"
                  class="dash-closure-action-form"
                  onsubmit="return confirm('Close {{ $month_label }}? Period totals will be snapshotted and the month marked closed.');"
                >
                  @csrf
                  <button
                    type="submit"
                    name="store_action"
                    value="closure"
                    class="inventory-action-btn dash-closure-btn-close"
                  >
                    <i class="fa fa-lock"></i>
                    <span>Close month</span>
                  </button>
                </form>
              @else
                <span class="dash-closure-action-disabled">
                  <i class="fa fa-check-circle"></i>
                  No further actions
                </span>
              @endif
            </div>
          </section>

          <section class="dash-closure-summary-section">
            <h6 class="inventory-edit-section-title"><i class="fa fa-pie-chart"></i> Items summary</h6>
            <div class="dash-closure-kpi-grid">
              <div class="dash-closure-kpi">
                <span class="dash-closure-kpi-label">Qty sold</span>
                <span class="dash-closure-kpi-value">{{ number_format($summary['qty_sold']) }}</span>
              </div>
              <div class="dash-closure-kpi">
                <span class="dash-closure-kpi-label">Total Gh₵</span>
                <span class="dash-closure-kpi-value">{{ number_format($summary['amt_sold'], 2) }}</span>
              </div>
              <div class="dash-closure-kpi">
                <span class="dash-closure-kpi-label">Profit Gh₵</span>
                <span class="dash-closure-kpi-value">{{ number_format($summary['profit'], 2) }}</span>
              </div>
              <div class="dash-closure-kpi">
                <span class="dash-closure-kpi-label">Qty available</span>
                <span class="dash-closure-kpi-value">{{ number_format($summary['qty_available']) }}</span>
              </div>
            </div>

            @if (count($branch_summaries) > 1)
              <div class="dash-closure-branch-chips">
                @foreach ($branch_summaries as $branchSummary)
                  <div class="dash-closure-branch-chip">
                    <span class="dash-closure-branch-chip-name">{{ $branchSummary['name'] }}</span>
                    <span class="dash-closure-branch-chip-meta">
                      Qty {{ number_format($branchSummary['qty_sold']) }}
                      · Gh₵ {{ number_format($branchSummary['amt_sold'], 2) }}
                      · Profit {{ number_format($branchSummary['profit'], 2) }}
                    </span>
                  </div>
                @endforeach
              </div>
            @endif
          </section>

          <section class="dash-closure-table-section">
            <h6 class="inventory-edit-section-title"><i class="fa fa-truck"></i> Distribution summary</h6>

            @if (count($distribution_rows) > 0)
              <div class="table-responsive dash-closure-table-wrap">
                <table class="table dash-closure-table dash-closure-multi-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Item</th>
                      @foreach ($branches as $branch)
                        <th class="text-right">{{ $branch->name }}</th>
                      @endforeach
                      <th class="text-right">Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($distribution_rows as $index => $row)
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                          <span class="dash-closure-item-name">{{ $row['name'] }}</span>
                          @if ($row['meta'] !== '')
                            <span class="dash-closure-item-meta">{{ $row['meta'] }}</span>
                          @endif
                        </td>
                        @foreach ($branches as $branch)
                          @php $column = $column_keys[(string) $branch->tag] ?? null; @endphp
                          <td class="text-right">
                            {{ $column ? number_format($row['quantities'][$column] ?? 0) : '—' }}
                          </td>
                        @endforeach
                        <td class="text-right">{{ number_format($row['total']) }}</td>
                      </tr>
                    @endforeach
                    <tr class="dash-closure-total-row">
                      <td></td>
                      <td>Total distribution</td>
                      @foreach ($branches as $branch)
                        @php $column = $column_keys[(string) $branch->tag] ?? null; @endphp
                        <td class="text-right">
                          {{ $column ? number_format($distribution_totals['quantities'][$column] ?? 0) : '—' }}
                        </td>
                      @endforeach
                      <td class="text-right">{{ number_format($distribution_totals['total']) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            @else
              <div class="dash-empty-state dash-closure-empty">
                <span class="dash-empty-state-icon" aria-hidden="true"><i class="fa fa-truck"></i></span>
                <p class="dash-empty-state-title">No distribution records</p>
                <p class="dash-empty-state-text">No waybill distributions were recorded for {{ $month_label }}.</p>
              </div>
            @endif
          </section>

          <section class="dash-closure-table-section">
            <h6 class="inventory-edit-section-title"><i class="fa fa-shopping-basket"></i> Sales summary</h6>

            @if (count($sales_rows) > 0)
              <div class="table-responsive dash-closure-table-wrap">
                <table class="table dash-closure-table dash-closure-multi-table dash-closure-sales-table">
                  <thead>
                    <tr>
                      <th rowspan="2">#</th>
                      <th rowspan="2">Item</th>
                      @foreach ($branches as $branch)
                        <th colspan="4" class="text-center dash-closure-branch-head">{{ $branch->name }}</th>
                      @endforeach
                    </tr>
                    <tr>
                      @foreach ($branches as $branch)
                        <th class="text-right">Qty sold</th>
                        <th class="text-right">Total Gh₵</th>
                        <th class="text-right">Qty rem</th>
                        <th class="text-right">Profit</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($sales_rows as $index => $row)
                      <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                          <span class="dash-closure-item-name">{{ $row['name'] }}</span>
                          @if ($row['meta'] !== '')
                            <span class="dash-closure-item-meta">{{ $row['meta'] }}</span>
                          @endif
                        </td>
                        @foreach ($branches as $branch)
                          @php $cell = $row['branches'][(string) $branch->tag] ?? ['qty_sold' => 0, 'amt_sold' => 0, 'qty_rem' => 0, 'profit' => 0]; @endphp
                          <td class="text-right">{{ $cell['qty_sold'] != 0 ? number_format($cell['qty_sold']) : '—' }}</td>
                          <td class="text-right">{{ $cell['amt_sold'] != 0 ? number_format($cell['amt_sold'], 2) : '—' }}</td>
                          <td class="text-right">{{ number_format($cell['qty_rem']) }}</td>
                          <td class="text-right">{{ $cell['profit'] != 0 ? number_format($cell['profit'], 2) : '—' }}</td>
                        @endforeach
                      </tr>
                    @endforeach
                    <tr class="dash-closure-total-row">
                      <td></td>
                      <td>Total</td>
                      @foreach ($branches as $branch)
                        @php $total = $sales_totals[(string) $branch->tag] ?? ['qty_sold' => 0, 'amt_sold' => 0, 'profit' => 0]; @endphp
                        <td class="text-right">{{ $total['qty_sold'] != 0 ? number_format($total['qty_sold']) : '' }}</td>
                        <td class="text-right">{{ $total['amt_sold'] != 0 ? number_format($total['amt_sold'], 2) : '' }}</td>
                        <td></td>
                        <td class="text-right">{{ $total['profit'] != 0 ? number_format($total['profit'], 2) : '' }}</td>
                      @endforeach
                    </tr>
                  </tbody>
                </table>
              </div>
            @else
              <div class="dash-empty-state dash-closure-empty">
                <span class="dash-empty-state-icon" aria-hidden="true"><i class="fa fa-shopping-basket"></i></span>
                <p class="dash-empty-state-title">No sales records</p>
                <p class="dash-empty-state-text">No sales were recorded for {{ $month_label }}.</p>
              </div>
            @endif
          </section>

        </div>
      </div>

    </div>
  </div>

@endsection

@section('footer')
  <link rel="stylesheet" href="/maindir/css/dash-closure.css?v=2">
@endsection
