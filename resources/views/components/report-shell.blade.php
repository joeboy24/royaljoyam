@props([
    'active' => null,
    'title' => null,
    'subtitle' => null,
    'icon' => 'fa fa-file-text',
    'panelClass' => '',
])

<div class="content dash-reports-content">
  <div class="container-fluid dash-reports-body">

    @include('inc.messages')

    <x-report-nav :active="$active" />

    @isset($filters)
      <div class="dash-reports-toolbar">
        {{ $filters }}
      </div>
    @endisset

    @if ($title)
      <div class="card dash-reports-card">
        <x-dash-page-header :title="$title" :subtitle="$subtitle" :icon="$icon">
          @isset($actions)
            <x-slot:actions>{{ $actions }}</x-slot:actions>
          @endisset
        </x-dash-page-header>

        <div @class([
          'card-body',
          'dash-form-body',
          'dash-reports-panel',
          $panelClass,
        ])>
          {{ $slot }}
        </div>
      </div>
    @else
      {{ $slot }}
    @endif

    @isset($footer)
      <div class="card dash-reports-kpi-card">
        <div class="card-body dash-reports-kpi-body">
          {{ $footer }}
        </div>
      </div>
    @endisset

  </div>
</div>

<script src="/maindir/js/dash-reports.js?v=3" defer></script>
