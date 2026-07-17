@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="waybill"
  title="Waybill Report"
  subtitle="Filter waybills by created date and review totals."
  icon="fa fa-truck"
>
  <x-slot:filters>
    <x-report-inventory-filters
      :action="url('/waybillreport')"
      clear-url="/waybillreport"
      print-url="/waybillprint"
      export-url="/waybillreport/export"
      filter-label="Waybill filters"
      :show-branch="false"
      :show-search="true"
      search-name="waybillsearch"
      search-placeholder="Company, bill no., stock no., driver..."
    />
  </x-slot:filters>

  <div id="printarea1">
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
        {{ $waybills->appends([
          'date_from' => request()->query('date_from'),
          'date_to' => request()->query('date_to'),
          'waybillsearch' => request()->query('waybillsearch'),
        ])->links() }}
      </div>
    @else
      <div class="dash-empty-state">
        <span class="dash-empty-state-icon"><i class="fa fa-bar-chart"></i></span>
        <h3 class="dash-empty-state-title">No waybill records found</h3>
        <p class="dash-empty-state-text">
          @if (! empty($date_from) || ! empty($date_to) || ! empty($waybillsearch))
            No waybills match the selected filters. Try different dates or search terms.
          @else
            Choose a date range above and click Load data to generate the report.
          @endif
        </p>
      </div>
    @endif
  </div>
</x-report-shell>

@endsection
