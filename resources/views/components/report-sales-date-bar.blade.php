@props([
    'compact' => false,
])

@php
  $salesDate = session('date_today') ?: now()->format('Y-m-d');
@endphp

@if ($compact)
  <form action="{{ url('/changedate') }}" method="GET" class="dash-reports-header-date-form">
    <label class="dash-reports-header-date-picker" for="report_sales_date_picker">
      <span class="sr-only">Set sales date</span>
      <input
        id="report_sales_date_picker"
        class="dash-reports-header-date-input"
        name="date_today"
        type="date"
        value="{{ $salesDate }}"
        aria-label="Set sales date"
      />
    </label>
    <button type="submit" class="dash-reports-header-date-submit">
      <i class="fa fa-calendar"></i>
      <span>Change date</span>
    </button>
  </form>
@else
  <div class="dash-reports-sales-date-bar">
    <div class="dash-reports-sales-date-info">
      <span class="dash-reports-sales-date-icon" aria-hidden="true"><i class="fa fa-calendar-check-o"></i></span>
      <div class="dash-reports-sales-date-copy">
        <p class="dash-reports-sales-date-label">Session sales date</p>
        <p class="dash-reports-sales-date-value">{{ \Carbon\Carbon::parse($salesDate)->format('l, d M Y') }}</p>
      </div>
    </div>

    <form action="{{ url('/changedate') }}" method="GET" class="dash-reports-sales-date-form">
      <label class="dash-reports-sales-date-picker" for="report_sales_date_picker_full">
        <span class="sr-only">Set sales date</span>
        <input
          id="report_sales_date_picker_full"
          class="dash-reports-sales-date-input"
          name="date_today"
          type="date"
          value="{{ $salesDate }}"
          aria-label="Set sales date"
        />
      </label>
      <button type="submit" class="inventory-search-btn inventory-search-btn-primary dash-reports-sales-date-submit">
        <i class="fa fa-calendar"></i>
        <span>Change date</span>
      </button>
    </form>
  </div>
@endif
