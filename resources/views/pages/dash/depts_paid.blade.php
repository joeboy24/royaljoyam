@extends('layouts.dashlay')

@section('content')

  <div class="content dash-paid-debts-content">
    <div class="container-fluid dash-paid-debts-body">

      @include('inc.messages')

      <div class="card dash-paid-debts-card">
        <x-dash-page-header
          title="Paid Debts"
          subtitle="{{ $isAdmin ? 'Debt payments collected for ' . $periodLabel : 'Debt payments for ' . $periodLabel }}"
          icon="fa fa-dollar"
        >
          <x-slot:actions>
            <a href="/sales" class="dash-page-header-btn inventory-action-btn dash-tip" data-tip="Back to sales">
              <i class="fa fa-shopping-basket"></i>
              <span>Sales</span>
            </a>
          </x-slot:actions>
        </x-dash-page-header>

        <div class="card-body dash-form-body dash-paid-debts-panel">
          <div class="dash-paid-debts-summary">
            <span class="dash-paid-debts-stat">
              <i class="fa fa-list-ol"></i>
              <span><strong>{{ $sales_pay->total() }}</strong> payments</span>
            </span>
            <span class="dash-paid-debts-stat">
              <i class="fa fa-money"></i>
              <span>Total collected: <strong>Gh₵ {{ number_format($totalPaid, 2) }}</strong></span>
            </span>
          </div>

          @if ($sales_pay->count() > 0)
            <div class="dash-paid-debts-table-wrap table-responsive">
              <table class="table dash-paid-debts-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Buyer</th>
                    <th>Amount paid</th>
                    <th>Balance</th>
                    <th>Date</th>
                    <th class="ryt actsize">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sales_pay as $payment)
                    @if ($payment->sale)
                      <tr @class(['rowColour' => $loop->even, 'dash-paid-debts-error-row' => $payment->del !== 'no'])>
                        <td>{{ $loop->iteration + ($sales_pay->currentPage() - 1) * $sales_pay->perPage() }}</td>
                        <td>
                          <strong>{{ $payment->sale->order_no }}</strong>
                          <p class="dash-paid-debts-meta">User: {{ $payment->sale->user->name ?? '—' }}</p>
                        </td>
                        <td>
                          {{ $payment->sale->buy_name }}
                          <p class="dash-paid-debts-meta">{{ $payment->sale->buy_contact }}</p>
                        </td>
                        <td>
                          <span class="dash-paid-debts-amount">Gh₵ {{ number_format($payment->amt_paid, 2) }}</span>
                          <p class="dash-paid-debts-meta">Order total: Gh₵ {{ number_format($payment->sale->tot, 2) }}</p>
                        </td>
                        <td>
                          @if ((float) $payment->bal > 0)
                            <span class="dash-paid-debts-balance dash-paid-debts-balance--due">Gh₵ {{ number_format($payment->bal, 2) }}</span>
                          @else
                            <span class="dash-paid-debts-balance">Cleared</span>
                          @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</td>
                        <td class="ryt">
                          @if ($payment->del === 'no')
                            <form action="{{ url('/sales/payments/' . $payment->id) }}" method="POST" class="dash-paid-debts-delete-form">
                              @csrf
                              @method('DELETE')
                              <button
                                type="submit"
                                class="inventory-action-btn inventory-action-btn-icon dash-tip"
                                data-tip="Delete payment"
                                title="Delete payment"
                                onclick="return confirm('Are you sure you want to permanently delete this payment record?');"
                              >
                                <i class="fa fa-trash"></i>
                              </button>
                            </form>
                          @endif
                        </td>
                      </tr>
                    @else
                      <tr class="dash-paid-debts-error-row">
                        <td>{{ $loop->iteration + ($sales_pay->currentPage() - 1) * $sales_pay->perPage() }}</td>
                        <td colspan="4">
                          Record display error
                          <p class="dash-paid-debts-meta">User: {{ $payment->user->name ?? '—' }} · Amount: Gh₵ {{ number_format($payment->amt_paid, 2) }}</p>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</td>
                        <td></td>
                      </tr>
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>

            {{ $sales_pay->links() }}
          @else
            <p class="dash-paid-debts-empty">No paid debt records found for {{ $periodLabel }}.</p>
          @endif
        </div>
      </div>

    </div>
  </div>

  <link rel="stylesheet" href="/maindir/css/dash-paid-debts.css?v=1">

@endsection
