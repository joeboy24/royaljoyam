@extends('layouts.dashlay')

@section('content')

  <div class="content dash-closure-content">
    <div class="container-fluid dash-closure-body">

      @include('inc.messages')

      <div class="card dash-closure-card">
        <x-dash-page-header
          title="Month-end closure"
          subtitle="Open and close monthly periods. Status shown for {{ $priorYear }}–{{ $currentYear }}."
          icon="fa fa-calendar-check-o"
        />

        <div class="card-body dash-form-body dash-closure-panel">

          @if (count($priorYearCards) > 0)
            <section class="dash-closure-year-section">
              <h6 class="inventory-edit-section-title">
                <i class="fa fa-calendar"></i> {{ $priorYear }}
                <span class="dash-closure-year-note">Sep–Dec</span>
              </h6>
              <div class="dash-closure-grid">
                @foreach ($priorYearCards as $card)
                  @include('pages.dash.partials.closure-month-card', ['card' => $card])
                @endforeach
              </div>
            </section>
          @endif

          <section class="dash-closure-year-section">
            <h6 class="inventory-edit-section-title">
              <i class="fa fa-calendar"></i> {{ $currentYear }}
            </h6>
            <div class="dash-closure-grid">
              @foreach ($currentYearCards as $card)
                @include('pages.dash.partials.closure-month-card', ['card' => $card])
              @endforeach
            </div>
          </section>

        </div>
      </div>

    </div>
  </div>

@endsection

@section('footer')
  <link rel="stylesheet" href="/maindir/css/dash-closure.css?v=2">
@endsection
