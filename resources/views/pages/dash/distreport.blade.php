@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="distribution"
  title="Distribution Report"
  subtitle="Review branch distribution history by date."
  icon="fa fa-share-alt"
>
  <x-slot:filters>
    @php
      $distQuery = array_filter([
        'date_from' => $date_from ?? null,
        'date_to' => $date_to ?? null,
      ]);
    @endphp
    <x-report-inventory-filters
      :action="url('/distreport')"
      clear-url="/distreport"
      :print-url="url('/distreportprint?' . http_build_query($distQuery))"
      :export-url="url('/distreport/export?' . http_build_query($distQuery))"
      filter-label="Date range"
      :show-branch="false"
    />
  </x-slot:filters>

  <div id="printarea1">
    @if ($wbdreports->total() > 0)
      @php
        $reportBranches = $branches ?? collect();
        $reportBranchKeys = $branchKeys ?? collect();
      @endphp
      <div class="table-responsive">
        <table class="table mt">
          <thead class="text-secondary hideMe">
            <tr>
              <th>#</th>
              <th>Item</th>
              @foreach ($reportBranches as $br)
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
                  @foreach ($reportBranchKeys as $qtyKey)
                    <td>{{ $wbd->{$qtyKey} ?? 0 }}</td>
                  @endforeach
                  <td class="ryt">{{ date('M. d, Y', strtotime($wbd->created_at)) }}</td>
                </tr>
              @endif
            @endforeach

            <tr>
              <td></td>
              <td><b>Total Qty.:</b> {{ $reportBranchKeys->sum(fn ($key) => $wbdreports->sum($key)) }}</td>
              @foreach ($reportBranchKeys as $key)
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
</x-report-shell>

@endsection
