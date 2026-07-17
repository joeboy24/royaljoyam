@props(['transfers'])

@php
  $reportService = app(\App\Services\SiblingReportService::class);
  $allTransfers = session('transferrep', collect());
  $totalQty = (int) $allTransfers->sum(fn ($transfer) => (int) $transfer->qty);
@endphp

@if ($transfers->count() > 0)
  <div class="dash-reports-table-wrap table-responsive">
    <table class="table mt dash-reports-data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Item</th>
          <th>From</th>
          <th>To</th>
          <th class="ryt">Qty</th>
          <th>Notes</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($transfers as $transfer)
          @php
            $rowNumber = $loop->iteration + ($transfers->currentPage() - 1) * $transfers->perPage();
            $item = $transfer->item;
          @endphp

          <tr @class(['rowColour' => $loop->even])>
            <td>{{ $rowNumber }}</td>
            <td>
              @if ($item)
                <strong>{{ $item->name }}</strong>
                <p class="dash-reports-sales-meta">{{ $item->item_no }}</p>
              @else
                <strong>Item #{{ $transfer->item_id }}</strong>
              @endif
            </td>
            <td>
              <span class="dash-reports-transfer-branch">{{ $reportService->branchLabel($transfer->from_branch) }}</span>
            </td>
            <td>
              <span class="dash-reports-transfer-branch is-destination">{{ $reportService->branchLabel($transfer->to_branch) }}</span>
            </td>
            <td class="ryt"><strong>{{ number_format((int) $transfer->qty) }}</strong></td>
            <td>{{ $transfer->notes ?: '—' }}</td>
            <td>
              {{ $transfer->created_at ? \Carbon\Carbon::parse($transfer->created_at)->format('M. d, Y g:i A') : '—' }}
              <p class="dash-reports-sales-meta">By: {{ $transfer->user->name ?? '—' }}</p>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="dash-reports-table-summary">
    <span><strong>{{ number_format($transfers->total()) }}</strong> transfers</span>
    <span>Total qty moved: <strong>{{ number_format($totalQty) }}</strong></span>
  </div>

  {{ $transfers->appends([
    'date_from' => request()->query('date_from'),
    'date_to' => request()->query('date_to'),
    'from_branch' => request()->query('from_branch'),
    'to_branch' => request()->query('to_branch'),
    'transfersearch' => request()->query('transfersearch'),
  ])->links() }}
@else
  <x-report-empty-state icon="fa fa-exchange" message="No branch transfers found for the selected filters." />
@endif
