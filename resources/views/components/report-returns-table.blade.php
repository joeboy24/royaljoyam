@props(['returns'])

@php
  $allReturns = session('returnsrep', collect());
  $totalQty = (float) $allReturns->sum('qty');
  $totalAmount = (float) $allReturns->sum('tot');
@endphp

@if ($returns->count() > 0)
  <div class="dash-reports-table-wrap table-responsive">
    <table class="table mt dash-reports-data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Item</th>
          <th>Qty</th>
          <th class="ryt">Unit price</th>
          <th class="ryt">Total</th>
          <th class="ryt">Order date</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($returns as $rtn)
          @if ($rtn->del !== 'no')
            @continue
          @endif

          @php
            $rowNumber = $loop->iteration + ($returns->currentPage() - 1) * $returns->perPage();
          @endphp

          <tr @class(['rowColour' => $loop->even])>
            <td>{{ $rowNumber }}</td>
            <td>
              <strong>{{ $rtn->name }}</strong>
              <p class="dash-reports-sales-meta">Item no.: {{ $rtn->item_no }}</p>
            </td>
            <td>{{ $rtn->qty }}</td>
            <td class="ryt">Gh₵ {{ number_format((float) $rtn->unit_price, 2) }}</td>
            <td class="ryt"><strong>Gh₵ {{ number_format((float) $rtn->tot, 2) }}</strong></td>
            <td class="ryt">
              {{ date('M. d, Y', strtotime($rtn->order_date)) }}
              <p class="dash-reports-sales-meta">By: {{ $rtn->user->name ?? '—' }}</p>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="dash-reports-table-summary">
    <span><strong>{{ number_format($returns->total()) }}</strong> records</span>
    <span>Total qty: <strong>{{ number_format($totalQty) }}</strong></span>
    <span>Total amount: <strong>Gh₵ {{ number_format($totalAmount, 2) }}</strong></span>
  </div>

  {{ $returns->appends([
    'date_from' => request()->query('date_from'),
    'date_to' => request()->query('date_to'),
    'branch' => request()->query('branch'),
    'returnsearch' => request()->query('returnsearch'),
  ])->links() }}
@else
  <x-report-empty-state icon="fa fa-warning" message="No returns found for the selected filters." />
@endif
