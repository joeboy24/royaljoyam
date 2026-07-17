@extends('layouts.dashlay')

@section('content')

  @php
    $activeFilterCount = 0;

    if (($paidDebtSearch ?? '') !== '') {
        $activeFilterCount++;
    }

    if (($dateFrom ?? '') !== '') {
        $activeFilterCount++;
    }

    if (($dateTo ?? '') !== '') {
        $activeFilterCount++;
    }

    if ($isAdmin && ($branch ?? 'All Branches') !== 'All Branches') {
        $activeFilterCount++;
    }
  @endphp

  <div class="content dash-paid-debts-content">
    <div class="container-fluid dash-paid-debts-body">

      @include('inc.messages')

      <div class="card dash-paid-debts-card">
        <x-dash-page-header
          title="Paid Debts"
          subtitle="Debt payments collected for {{ $periodLabel }}."
          icon="fa fa-dollar"
        >
          <x-slot:actions>
            @if ($isAdmin)
              <a href="{{ url('/debts') }}" class="dash-page-header-btn inventory-action-btn dash-tip" data-tip="Outstanding debts report">
                <i class="fa fa-arrow-left"></i>
                <span>Debts</span>
              </a>
            @endif
            <a href="{{ url('/sales') }}" class="dash-page-header-btn inventory-action-btn dash-tip" data-tip="Back to sales">
              <i class="fa fa-shopping-basket"></i>
              <span>Sales</span>
            </a>
          </x-slot:actions>
        </x-dash-page-header>

        <div class="card-body dash-form-body dash-paid-debts-panel">
          <form
            method="GET"
            action="{{ url('/paid_debts') }}"
            class="inventory-filter-form inventory-list-toolbar dash-paid-debts-filter-form"
            data-report-filter-form
            novalidate
          >
            <div class="inventory-filter-row">
              <div class="inventory-search-panel">
                <div class="inventory-search-heading">
                  <span class="inventory-search-label">
                    <i class="fa fa-search"></i>
                    Search
                    @if (($paidDebtSearch ?? '') !== '')
                      <span class="inventory-search-active-dot" title="Search active"></span>
                    @endif
                  </span>
                </div>

                <div class="inventory-search-controls">
                  <label class="inventory-search-field">
                    <span class="inventory-search-field-icon"><i class="fa fa-search"></i></span>
                    <input
                      type="search"
                      name="paiddebtsearch"
                      value="{{ $paidDebtSearch ?? '' }}"
                      class="inventory-search-input"
                      placeholder="Order, buyer, contact, cashier..."
                      aria-label="Search paid debts"
                    />
                  </label>

                  <button type="submit" class="inventory-search-btn inventory-search-btn-primary" title="Search">
                    <i class="fa fa-search"></i>
                    <span>Search</span>
                  </button>

                  <div class="inventory-filters-panel is-collapsed" data-collapsible-filters>
                    <button
                      type="button"
                      class="inventory-filters-toggle inventory-search-btn inventory-search-btn-muted dash-tip"
                      aria-expanded="false"
                      aria-controls="paidDebtsFilters"
                      data-tip="Filters"
                    >
                      <i class="fa fa-filter"></i>
                      @if ($activeFilterCount > 0)
                        <span class="inventory-filters-count">{{ $activeFilterCount }}</span>
                      @endif
                    </button>

                    <div class="inventory-filters-body" id="paidDebtsFilters">
                      <p class="dash-reports-filter-error" data-report-filter-error role="alert" hidden>
                        Provide <strong>Date from</strong> when using <strong>Date to</strong>.
                      </p>

                      <div class="inventory-filters-controls">
                        <div class="dash-reports-date-range">
                          <label class="inventory-filter-field inventory-filter-field-compact">
                            <span class="inventory-filter-field-icon"><i class="fa fa-calendar"></i></span>
                            <input
                              type="date"
                              class="inventory-date-input"
                              name="date_from"
                              value="{{ $dateFrom ?? '' }}"
                              aria-label="Date from"
                              data-report-date-from
                            />
                          </label>

                          <span class="inventory-date-separator dash-reports-date-separator" aria-hidden="true">
                            <i class="fa fa-long-arrow-right"></i>
                          </span>

                          <label class="inventory-filter-field inventory-filter-field-compact">
                            <span class="inventory-filter-field-icon"><i class="fa fa-calendar"></i></span>
                            <input
                              type="date"
                              class="inventory-date-input"
                              name="date_to"
                              value="{{ $dateTo ?? '' }}"
                              aria-label="Date to"
                              data-report-date-to
                            />
                          </label>
                        </div>

                        @if ($isAdmin)
                          <label class="inventory-filter-field">
                            <span class="inventory-filter-field-icon"><i class="fa fa-building"></i></span>
                            <select name="branch" class="inventory-filter-select" title="Filter by branch">
                              <option value="All Branches" @selected(($branch ?? 'All Branches') === 'All Branches')>All Branches</option>
                              @foreach ($branches as $branchOption)
                                <option value="{{ $branchOption->tag }}" @selected((string) ($branch ?? '') === (string) $branchOption->tag)>
                                  {{ $branchOption->name }}
                                </option>
                              @endforeach
                            </select>
                          </label>
                        @else
                          <div class="dash-paid-debts-branch-readonly inventory-filter-field" title="Branch locked to your account">
                            <span class="inventory-filter-field-icon"><i class="fa fa-building"></i></span>
                            <span>{{ $branchName ?? auth()->user()->status }}</span>
                          </div>
                        @endif

                        <button type="submit" class="inventory-search-btn inventory-search-btn-primary inventory-filters-apply">
                          <i class="fa fa-filter"></i>
                          <span>Apply</span>
                        </button>
                      </div>
                    </div>
                  </div>

                  <a href="{{ url('/paid_debts') }}" class="inventory-search-btn inventory-search-btn-clear inventory-search-btn-icon dash-tip" data-tip="Clear filters">
                    <i class="fa fa-refresh"></i>
                  </a>
                </div>
              </div>
            </div>
          </form>

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
            <p class="dash-paid-debts-empty">
              @if (($paidDebtSearch ?? '') !== '')
                No paid debt records match “{{ $paidDebtSearch }}” for {{ $periodLabel }}.
              @else
                No paid debt records found for {{ $periodLabel }}.
              @endif
            </p>
          @endif
        </div>
      </div>

    </div>
  </div>

  <link rel="stylesheet" href="/maindir/css/dash-paid-debts.css?v=5">
  <link rel="stylesheet" href="/maindir/css/dash-reports.css?v=34">

@endsection

@section('footer')
  <script src="/maindir/js/inventory-collapsible-filters.js?v=2"></script>
  <script src="/maindir/js/dash-reports.js?v=3"></script>
@endsection
