@props([
    'action',
    'clearUrl',
    'printUrl' => null,
    'exportUrl' => null,
    'showBranch' => true,
    'showDelivery' => false,
    'showSearch' => false,
    'showDebtStatus' => false,
    'searchName' => 'search',
    'searchPlaceholder' => 'Search records...',
    'branches' => collect(),
    'filterLabel' => 'Report filters',
])

@php
  $dateFrom = request()->query('date_from', '');
  $dateTo = request()->query('date_to', '');
  $branch = request()->query('branch', 'All Branches');
  $delvr = request()->query('delvr', 'Del. / Not Delivered');
  $debtStatus = request()->query('debt_status', 'outstanding');
  $search = $showSearch ? trim((string) request()->query($searchName, '')) : '';

  $activeFilterCount = 0;

  if ($showSearch && $search !== '') {
      $activeFilterCount++;
  }

  if ($dateFrom !== '') {
      $activeFilterCount++;
  }

  if ($dateTo !== '') {
      $activeFilterCount++;
  }

  if ($showBranch && $branch !== 'All Branches') {
      $activeFilterCount++;
  }

  if ($showDelivery && $delvr !== 'Del. / Not Delivered') {
      $activeFilterCount++;
  }

  if ($showDebtStatus && $debtStatus !== 'outstanding') {
      $activeFilterCount++;
  }
@endphp

<form class="inventory-filter-form dash-reports-filter-form" action="{{ $action }}" method="GET" data-report-filter-form novalidate>
  <div class="inventory-filter-row">
    <div class="inventory-filters-panel">
      <div class="inventory-filters-heading">
        <span class="inventory-filters-label">
          <i class="fa fa-filter"></i>
          {{ $filterLabel }}
          @if ($activeFilterCount > 0)
            <span class="inventory-search-active-dot" title="Filters active"></span>
            <span class="inventory-filters-count">{{ $activeFilterCount }}</span>
          @endif
        </span>
      </div>

      <p class="dash-reports-filter-error" data-report-filter-error role="alert" hidden>
        Provide <strong>Date from</strong> when using <strong>Date to</strong>.
      </p>

      <div class="inventory-filters-controls">
        @if ($showSearch)
          <label class="inventory-search-field dash-reports-search-field">
            <span class="inventory-search-field-icon"><i class="fa fa-search"></i></span>
            <input
              type="search"
              class="inventory-search-input"
              name="{{ $searchName }}"
              value="{{ $search }}"
              placeholder="{{ $searchPlaceholder }}"
              aria-label="Search report"
            />
          </label>
        @endif

        <div class="dash-reports-date-range">
          <label class="inventory-filter-field inventory-filter-field-compact">
            <span class="inventory-filter-field-icon"><i class="fa fa-calendar"></i></span>
            <input
              type="date"
              class="inventory-date-input"
              name="date_from"
              value="{{ $dateFrom }}"
              aria-label="Date from"
              data-report-date-from
            />
          </label>

          <span class="inventory-date-separator dash-reports-date-separator" aria-hidden="true"><i class="fa fa-long-arrow-right"></i></span>

          <label class="inventory-filter-field inventory-filter-field-compact">
            <span class="inventory-filter-field-icon"><i class="fa fa-calendar"></i></span>
            <input
              type="date"
              class="inventory-date-input"
              name="date_to"
              value="{{ $dateTo }}"
              aria-label="Date to"
              data-report-date-to
            />
          </label>
        </div>

        @if ($showBranch)
          <label class="inventory-filter-field">
            <span class="inventory-filter-field-icon"><i class="fa fa-building"></i></span>
            <select name="branch" class="inventory-filter-select" title="Filter by branch" required>
              <option value="All Branches" @selected($branch === 'All Branches')>All Branches</option>
              @foreach ($branches as $branchOption)
                <option value="{{ $branchOption->tag }}" @selected((string) $branch === (string) $branchOption->tag)>{{ $branchOption->name }}</option>
              @endforeach
            </select>
          </label>
        @endif

        @if ($showDelivery)
          <label class="inventory-filter-field">
            <span class="inventory-filter-field-icon"><i class="fa fa-truck"></i></span>
            <select name="delvr" class="inventory-filter-select" title="Filter by delivery status" required>
              <option value="Del. / Not Delivered" @selected($delvr === 'Del. / Not Delivered')>Del. / Not Delivered</option>
              <option value="Delivered" @selected($delvr === 'Delivered')>Delivered</option>
              <option value="Not Delivered" @selected($delvr === 'Not Delivered')>Not Delivered</option>
            </select>
          </label>
        @endif

        @if ($showDebtStatus)
          <label class="inventory-filter-field">
            <span class="inventory-filter-field-icon"><i class="fa fa-filter"></i></span>
            <select name="debt_status" class="inventory-filter-select" title="Filter by debt status">
              <option value="outstanding" @selected($debtStatus === 'outstanding')>Outstanding debts</option>
              <option value="cleared" @selected($debtStatus === 'cleared')>Cleared debts</option>
              <option value="all" @selected($debtStatus === 'all')>All debt orders</option>
            </select>
          </label>
        @endif

        <div class="dash-reports-filter-actions">
          <button type="submit" class="inventory-search-btn inventory-search-btn-primary dash-tip" data-tip="Apply filters">
            <i class="fa fa-filter"></i>
            <span>Load data</span>
          </button>

          @if ($activeFilterCount > 0)
            <a href="{{ $clearUrl }}" class="inventory-search-btn inventory-search-btn-clear inventory-search-btn-icon dash-tip" data-tip="Clear filters" aria-label="Clear filters">
              <i class="fa fa-refresh"></i>
            </a>
          @endif

          @if ($printUrl)
            <a href="{{ \App\Support\ReportPrintQuery::url($printUrl) }}" class="inventory-search-btn inventory-search-btn-muted inventory-search-btn-icon dash-tip" data-tip="Print report" aria-label="Print report" target="_blank" rel="noopener">
              <i class="fa fa-print"></i>
            </a>
          @endif

          @if ($exportUrl)
            <a href="{{ \App\Support\ReportPrintQuery::url($exportUrl) }}" class="inventory-search-btn inventory-search-btn-muted dash-tip" data-tip="Export CSV">
              <i class="fa fa-download"></i>
              <span class="dash-reports-filter-export-label">Export</span>
            </a>
          @endif
        </div>

        {{ $slot }}
      </div>
    </div>
  </div>
</form>
