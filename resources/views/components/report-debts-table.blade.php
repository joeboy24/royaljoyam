@props([
    'sales',
    'debtStatus' => 'outstanding',
])

@php
  $allDebts = session('debts', collect());
  $grandTotal = (float) $allDebts->sum('tot');

  $summaryLabel = match ($debtStatus) {
      'cleared' => 'Cleared total',
      'all' => 'Debt orders total',
      default => 'Outstanding total',
  };

  $emptyMessage = match ($debtStatus) {
      'cleared' => 'No cleared debt orders found for the selected filters.',
      'all' => 'No debt orders found for the selected filters.',
      default => 'No outstanding debts found for the selected filters.',
  };
@endphp

@if ($sales->count() > 0)
  <div class="dash-reports-table-wrap table-responsive">
    <table class="table mt dash-reports-data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Order</th>
          <th>Qty</th>
          <th>Buyer</th>
          <th>Status</th>
          <th class="ryt">Balance</th>
          <th class="ryt">Total</th>
          <th>Date</th>
          <th class="ryt actsize">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($sales as $sale)
          @if ($sale->del !== 'no')
            @continue
          @endif

          @php
            $rowNumber = $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage();
            $paymentBadge = $sale->paymentStatusBadge();
          @endphp

          <tr @class([
            'rowColour' => $loop->even,
            'dash-reports-sales-row--debt' => $sale->hasOutstandingDebt(),
            'dash-reports-sales-row--undelivered' => $sale->del_status === 'Not Delivered' && ! $sale->hasOutstandingDebt(),
          ])>
            <td>{{ $rowNumber }}</td>
            <td>
              <strong>{{ $sale->order_no }}</strong>
              <p class="dash-reports-sales-meta">{{ $sale->user->name ?? '—' }}</p>
              <span class="dash-sales-badge {{ $sale->payModeBadgeClass() }}">{{ $sale->payModeShortLabel() }}</span>
            </td>
            <td>{{ $sale->qty }}</td>
            <td>
              {{ $sale->buy_name }}
              <p class="dash-reports-sales-meta">{{ $sale->buy_contact }}</p>
            </td>
            <td>
              <div class="dash-sales-status-stack">
                @if ($sale->del_status === 'Delivered')
                  <span class="dash-sales-status-pill dash-sales-status-pill--delivered is-readonly">Delivered</span>
                @else
                  <span class="dash-sales-status-pill dash-sales-status-pill--undelivered is-readonly">Undelivered</span>
                @endif

                @if ($paymentBadge)
                  <span class="dash-sales-payment-badge {{ $paymentBadge['class'] }}">{{ $paymentBadge['label'] }}</span>
                @endif
              </div>
            </td>
            <td class="ryt">
              <strong>Gh₵ {{ number_format($sale->debtBalance(), 2) }}</strong>
            </td>
            <td class="ryt">
              <strong>Gh₵ {{ number_format((float) $sale->tot, 2) }}</strong>
            </td>
            <td>
              {{ $sale->created_at }}
              <p class="dash-reports-sales-meta">{{ $sale->updated_at }}</p>
            </td>
            <td class="ryt">
              <div class="dash-reports-sales-actions">
                <a href="/reporting/{{ $sale->id }}" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Print" title="Print order">
                  <i class="fa fa-print"></i>
                </a>

                @if ($sale->paid !== 'Paid' && $sale->pay_mode === 'Post Payment(Debt)')
                  <button
                    type="button"
                    class="inventory-action-btn inventory-action-btn-icon dash-tip report-pay-debt-trigger"
                    data-tip="Pay debt"
                    title="Pay debt"
                    data-toggle="modal"
                    data-target="#reportPayDebtModal"
                    data-sale-id="{{ $sale->id }}"
                    data-sale-tot="{{ number_format((float) $sale->tot, 2, '.', '') }}"
                    data-balance="{{ number_format($sale->debtBalance(), 2, '.', '') }}"
                    data-buyer="{{ e($sale->buy_name) }}"
                  >
                    <i class="fa fa-money"></i>
                  </button>
                @endif

                <button
                  type="button"
                  class="inventory-action-btn inventory-action-btn-icon dash-tip report-edit-trigger"
                  data-tip="Edit"
                  title="Edit order"
                  data-toggle="modal"
                  data-target="#reportEditOrderModal"
                  data-action="{{ url('/sales/' . $sale->id) }}"
                  data-buy-name="{{ e($sale->buy_name) }}"
                  data-buy-contact="{{ e($sale->buy_contact) }}"
                  data-pay-mode="{{ e($sale->pay_mode) }}"
                  data-notes="{{ e($sale->notes ?? '') }}"
                  data-order="{{ $sale->order_no }}"
                >
                  <i class="fa fa-pencil"></i>
                </button>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="dash-reports-table-summary">
    <span><strong>{{ number_format($sales->total()) }}</strong> records</span>
    <span>{{ $summaryLabel }}: <strong>Gh₵ {{ number_format($grandTotal, 2) }}</strong></span>
  </div>

  {{ $sales->appends([
    'date_from' => request()->query('date_from'),
    'date_to' => request()->query('date_to'),
    'branch' => request()->query('branch'),
    'debtsearch' => request()->query('debtsearch'),
    'debt_status' => request()->query('debt_status'),
  ])->links() }}
@else
  <x-report-empty-state icon="fa fa-folder-open" :message="$emptyMessage" />
@endif
