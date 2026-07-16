@props([
    'active' => null,
])

@php
  $items = \App\Support\ReportNavigation::items(request(), $active);
@endphp

<nav class="dash-reports-nav" aria-label="Report sections">
  <div class="dash-reports-nav-track">
    @foreach ($items as $item)
      <a
        href="{{ url($item['url']) }}"
        @class([
          'dash-reports-nav-tab',
          'is-active' => $item['active'],
        ])
        @if ($item['active']) aria-current="page" @endif
      >
        <span class="dash-reports-nav-icon" aria-hidden="true">
          <i class="{{ $item['icon'] }}"></i>
        </span>
        <span class="dash-reports-nav-copy">
          <strong class="dash-reports-nav-label">{{ $item['label'] }}</strong>
          <small class="dash-reports-nav-desc">{{ $item['description'] }}</small>
        </span>
      </a>
    @endforeach
  </div>
</nav>
