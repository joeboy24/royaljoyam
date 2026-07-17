@extends('layouts.dashlay')

@section('content')

  <div class="content dash-closure-content">
    <div class="container-fluid dash-closure-body">

      @include('inc.messages')

      <div class="card dash-closure-card">
        <x-dash-page-header
          title="Month-end closure"
          subtitle="Browse and manage monthly periods for {{ $selectedYear }}."
          icon="fa fa-calendar-check-o"
        />

        <div class="card-body dash-form-body dash-closure-panel">

          <div class="dash-closure-year-nav">
            @if ($prevYear)
              <a href="{{ url('/closure_page?year='.$prevYear) }}" class="dash-closure-year-btn dash-tip" data-tip="Previous year">
                <i class="fa fa-chevron-left"></i>
                <span>{{ $prevYear }}</span>
              </a>
            @else
              <span class="dash-closure-year-btn is-disabled" aria-disabled="true">
                <i class="fa fa-chevron-left"></i>
                <span>—</span>
              </span>
            @endif

            <form method="GET" action="{{ url('/closure_page') }}" class="dash-closure-year-form">
              <label class="dash-closure-year-field">
                <span class="dash-closure-year-label">Year</span>
                <select name="year" class="dash-closure-year-select" onchange="this.form.submit()">
                  @for ($year = $maxYear; $year >= $minYear; $year--)
                    <option value="{{ $year }}" @selected($year === $selectedYear)>{{ $year }}</option>
                  @endfor
                </select>
              </label>
            </form>

            @if ($nextYear)
              <a href="{{ url('/closure_page?year='.$nextYear) }}" class="dash-closure-year-btn dash-tip" data-tip="Next year">
                <span>{{ $nextYear }}</span>
                <i class="fa fa-chevron-right"></i>
              </a>
            @else
              <span class="dash-closure-year-btn is-disabled" aria-disabled="true">
                <span>—</span>
                <i class="fa fa-chevron-right"></i>
              </span>
            @endif
          </div>

          <section class="dash-closure-year-section">
            <div class="dash-closure-section-head">
              <h6 class="inventory-edit-section-title">
                <i class="fa fa-th-large"></i> {{ $selectedYear }} months
              </h6>
              <div class="dash-closure-legend" aria-hidden="true">
                <span class="dash-closure-legend-item is-open"><i class="fa fa-unlock-alt"></i> Open</span>
                <span class="dash-closure-legend-item is-closed"><i class="fa fa-lock"></i> Closed</span>
                <span class="dash-closure-legend-item is-pending"><i class="fa fa-circle-o"></i> Not opened</span>
              </div>
            </div>

            <div class="dash-closure-grid">
              @foreach ($monthCards as $card)
                @include('pages.dash.partials.closure-month-card', ['card' => $card])
              @endforeach
            </div>
          </section>

          <section class="dash-closure-table-section">
            <div class="dash-closure-section-head">
              <h6 class="inventory-edit-section-title">
                <i class="fa fa-moon-o"></i> Daily closes
                <span class="dash-closure-year-note">EOD snapshots for {{ $selectedYear }} · click a row for branch breakdown</span>
              </h6>
            </div>

            @if (count($dailyCloses) > 0)
              <div class="table-responsive dash-closure-table-wrap dash-closure-daily-wrap">
                <table class="table dash-closure-table dash-closure-daily-table">
                  <thead>
                    <tr>
                      <th class="dash-closure-daily-col-toggle"></th>
                      <th>Date</th>
                      <th>Scope</th>
                      <th class="text-right">Gross</th>
                      <th class="text-right">Net</th>
                      <th class="text-center">Cash variance</th>
                      <th class="text-center">Notes</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($dailyCloses as $daily)
                      @php
                        $panelId = 'daily-close-branches-'.$daily->id;
                        $branchesForDay = $dailyBranchBreakdowns[$daily->close_date] ?? [];
                        $hasAutoNote = is_string($daily->notes) && str_contains($daily->notes, 'System auto-closed');
                      @endphp
                      <tr
                        class="dash-closure-daily-row"
                        data-daily-close-toggle
                        data-daily-close-target="{{ $panelId }}"
                        tabindex="0"
                        role="button"
                        aria-expanded="false"
                        aria-controls="{{ $panelId }}"
                      >
                        <td class="dash-closure-daily-col-toggle">
                          <span class="dash-closure-daily-chevron" aria-hidden="true">
                            <i class="fa fa-chevron-right"></i>
                          </span>
                        </td>
                        <td>
                          <span class="dash-closure-daily-date">{{ $daily->close_date }}</span>
                          <span class="dash-closure-daily-date-sub">{{ \Carbon\Carbon::parse($daily->close_date)->format('D') }}</span>
                        </td>
                        <td>
                          <span class="dash-closure-daily-scope">{{ $daily->branch_label }}</span>
                        </td>
                        <td class="text-right dash-closure-daily-num">{{ number_format((float) $daily->gross_collected, 2) }}</td>
                        <td class="text-right dash-closure-daily-num">{{ number_format((float) $daily->net_total, 2) }}</td>
                        <td class="text-center">
                          @if ($daily->variance !== null)
                            <span @class([
                              'dash-closure-daily-variance',
                              'is-zero' => (float) $daily->variance == 0.0,
                              'is-short' => (float) $daily->variance < 0,
                              'is-over' => (float) $daily->variance > 0,
                            ])>
                              {{ number_format((float) $daily->variance, 2) }}
                            </span>
                          @else
                            <span class="dash-closure-daily-muted">—</span>
                          @endif
                        </td>
                        <td class="text-center">
                          @if ($hasAutoNote)
                            <span class="dash-closure-daily-badge">Auto closed</span>
                          @elseif ($daily->notes)
                            <span class="dash-closure-daily-muted dash-tip" data-tip="{{ $daily->notes }}">Note</span>
                          @else
                            <span class="dash-closure-daily-muted">—</span>
                          @endif
                        </td>
                        <td class="text-right" data-daily-close-stop>
                          <a
                            href="{{ route('dailyclose.print', ['date' => $daily->close_date, 'id' => $daily->id]) }}"
                            class="inventory-action-btn inventory-action-btn-icon dash-tip"
                            data-tip="Print daily close"
                            target="_blank"
                            rel="noopener"
                          >
                            <i class="fa fa-print"></i>
                          </a>
                        </td>
                      </tr>
                      <tr id="{{ $panelId }}" class="dash-closure-daily-detail" hidden>
                        <td colspan="8">
                          <div class="dash-closure-daily-detail-inner">
                            <div class="dash-closure-daily-detail-head">
                              <span>
                                <i class="fa fa-sitemap" aria-hidden="true"></i>
                                Branch breakdown · {{ $daily->close_date }}
                              </span>
                              <span class="dash-closure-year-note">Live totals for that sales date</span>
                            </div>

                            @if (count($branchesForDay) > 0)
                              <div class="dash-closure-daily-branch-grid">
                                @foreach ($branchesForDay as $branchDay)
                                  @php
                                    $branchActive = ($branchDay['gross_collected'] + $branchDay['expenses'] + $branchDay['debt_sold']) > 0;
                                  @endphp
                                  <article @class([
                                    'dash-closure-daily-branch',
                                    'is-active' => $branchActive,
                                    'is-empty' => ! $branchActive,
                                    'is-closed' => $branchDay['closed'],
                                  ])>
                                    <header class="dash-closure-daily-branch-head">
                                      <strong>{{ $branchDay['name'] }}</strong>
                                      @if ($branchDay['closed'])
                                        <span class="dash-closure-daily-badge is-closed">Closed</span>
                                      @endif
                                    </header>
                                    <div class="dash-closure-daily-branch-stats">
                                      <span><em>Cash</em>{{ number_format($branchDay['cash'], 2) }}</span>
                                      <span><em>Gross</em>{{ number_format($branchDay['gross_collected'], 2) }}</span>
                                      <span><em>Expenses</em>{{ number_format($branchDay['expenses'], 2) }}</span>
                                      <span><em>Net</em>{{ number_format($branchDay['net_total'], 2) }}</span>
                                      <span>
                                        <em>Variance</em>
                                        {{ $branchDay['variance'] !== null ? number_format($branchDay['variance'], 2) : '—' }}
                                      </span>
                                    </div>
                                  </article>
                                @endforeach
                              </div>
                            @else
                              <p class="dash-closure-daily-detail-empty">No branch activity found for this date.</p>
                            @endif
                          </div>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="dash-empty-state dash-closure-empty">
                <span class="dash-empty-state-icon" aria-hidden="true"><i class="fa fa-moon-o"></i></span>
                <p class="dash-empty-state-title">No daily closes yet</p>
                <p class="dash-empty-state-text">Close the day from the Sales page to store an end-of-day snapshot.</p>
              </div>
            @endif
          </section>

        </div>
      </div>

    </div>
  </div>

@endsection

@section('footer')
  <link rel="stylesheet" href="/maindir/css/dash-closure.css?v=10">
  <script src="/maindir/js/dash-closure.js?v=1"></script>
@endsection
