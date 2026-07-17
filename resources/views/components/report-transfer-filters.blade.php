@props([
    'action',
    'clearUrl',
    'branches' => collect(),
    'filterLabel' => 'Transfer filters',
])

@php
  $salesDate = session('date_today') ?: now()->format('Y-m-d');
  $defaultFrom = date('Y-m-01', strtotime($salesDate));
  $defaultTo = date('Y-m-t', strtotime($salesDate));

  $dateFrom = request()->query('date_from', $defaultFrom);
  $dateTo = request()->query('date_to', $defaultTo);
  $fromBranch = request()->query('from_branch', 'All Branches');
  $toBranch = request()->query('to_branch', 'All Branches');
  $search = trim((string) request()->query('transfersearch', ''));

  $activeFilterCount = 0;

  if ($search !== '') {
      $activeFilterCount++;
  }

  if ($dateFrom !== $defaultFrom) {
      $activeFilterCount++;
  }

  if ($dateTo !== $defaultTo) {
      $activeFilterCount++;
  }

  if ($fromBranch !== 'All Branches') {
      $activeFilterCount++;
  }

  if ($toBranch !== 'All Branches') {
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
        <label class="inventory-search-field dash-reports-search-field">
          <span class="inventory-search-field-icon"><i class="fa fa-search"></i></span>
          <input
            type="search"
            class="inventory-search-input"
            name="transfersearch"
            value="{{ $search }}"
            placeholder="Item name, item no., notes, user..."
            aria-label="Search transfers"
          />
        </label>

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

        <label class="inventory-filter-field">
          <span class="inventory-filter-field-icon"><i class="fa fa-sign-out"></i></span>
          <select name="from_branch" class="inventory-filter-select" title="Filter by source branch">
            <option value="All Branches" @selected($fromBranch === 'All Branches')>From any branch</option>
            @foreach ($branches as $branchOption)
              <option value="{{ $branchOption->tag }}" @selected((string) $fromBranch === (string) $branchOption->tag)>From {{ $branchOption->name }}</option>
            @endforeach
          </select>
        </label>

        <label class="inventory-filter-field">
          <span class="inventory-filter-field-icon"><i class="fa fa-sign-in"></i></span>
          <select name="to_branch" class="inventory-filter-select" title="Filter by destination branch">
            <option value="All Branches" @selected($toBranch === 'All Branches')>To any branch</option>
            @foreach ($branches as $branchOption)
              <option value="{{ $branchOption->tag }}" @selected((string) $toBranch === (string) $branchOption->tag)>To {{ $branchOption->name }}</option>
            @endforeach
          </select>
        </label>

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
        </div>
      </div>
    </div>
  </div>
</form>
