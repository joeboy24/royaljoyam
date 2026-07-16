@props([
    'sales',
    'cash' => 0,
    'cheque' => 0,
    'momo' => 0,
    'sumDbt' => 0,
])

@php
  $grossTotal = (float) $cash + (float) $cheque + (float) $momo + (float) $sumDbt;
@endphp

@if ($sales->count() > 0)
  <div class="dash-reports-table-wrap table-responsive">
    <table class="table mt dash-reports-sales-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Order</th>
          <th>Qty</th>
          <th>Pay mode</th>
          <th>Buyer</th>
          <th>Notes</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Total</th>
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
            $paymentBadge = $sale->paymentStatusBadge();
            $notesPreview = filled($sale->notes) ? \Illuminate\Support\Str::limit($sale->notes, 40) : null;
            $notesTruncated = filled($sale->notes) && strlen($sale->notes) > 40;
            $rowNumber = $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage();
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
            </td>
            <td>{{ $sale->qty }}</td>
            <td>
              <span class="dash-sales-badge {{ $sale->payModeBadgeClass() }}">{{ $sale->payModeShortLabel() }}</span>
            </td>
            <td>
              {{ $sale->buy_name }}
              <p class="dash-reports-sales-meta">{{ $sale->buy_contact }}</p>
            </td>
            <td class="dash-reports-sales-notes">
              @if (filled($sale->notes))
                @if ($notesTruncated)
                  <button
                    type="button"
                    class="dash-reports-notes-link"
                    data-toggle="modal"
                    data-target="#reportNotesModal"
                    data-notes="{{ e($sale->notes) }}"
                    data-order="{{ $sale->order_no }}"
                  >
                    {{ $notesPreview }}
                  </button>
                @else
                  <span title="{{ $sale->notes }}">{{ $notesPreview }}</span>
                @endif
              @else
                <span class="dash-reports-notes-empty">—</span>
              @endif
            </td>
            <td>
              Gh₵ {{ number_format((float) $sale->payment, 2) }}
              <p class="dash-reports-sales-meta">{{ $sale->changeOrBalanceLabel() }}: {{ number_format($sale->changeOrBalanceAmount(), 2) }}</p>
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
            <td>
              <strong>Gh₵ {{ number_format((float) $sale->tot, 2) }}</strong>
              @if ($sale->hasOutstandingDebt())
                <p class="dash-reports-sales-meta">Bal.: Gh₵ {{ number_format($sale->debtBalance(), 2) }}</p>
              @endif
              @if ((float) $sale->discount != 0)
                <p class="dash-reports-sales-meta">Dis.: Gh₵ {{ number_format((float) $sale->discount, 2) }}</p>
              @endif
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
                <a
                  href="/reporting/{{ $sale->id }}/edit"
                  class="inventory-action-btn inventory-action-btn-icon dash-tip"
                  data-tip="Return"
                  title="Return order"
                  onclick="return confirm('Returning order will permanently delete record. Continue?');"
                >
                  <i class="fa fa-mail-reply"></i>
                </a>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="dash-reports-table-summary">
    <span><strong>{{ number_format($sales->total()) }}</strong> records</span>
    <span>Total amount: <strong>Gh₵ {{ number_format($grossTotal, 2) }}</strong></span>
  </div>

  {{ $sales->appends([
    'date_from' => request()->query('date_from'),
    'date_to' => request()->query('date_to'),
    'branch' => request()->query('branch'),
    'delvr' => request()->query('delvr'),
  ])->links() }}
@else
  <x-report-empty-state />
@endif
