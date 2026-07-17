@php
  $monthNum = date('m', strtotime($card['month_key']));
  $monthShort = date('M', strtotime($card['month_key']));
  $statusIcon = match ($card['status']) {
    'open' => 'fa-unlock-alt',
    'closed' => 'fa-lock',
    default => 'fa-circle-o',
  };
@endphp

<a
  href="{{ $card['url'] }}"
  @class([
    'dash-closure-month',
    'is-current' => $card['is_current'],
    'is-open' => $card['status'] === 'open',
    'is-closed' => $card['status'] === 'closed',
    'is-pending' => $card['status'] === 'not_opened',
  ])
>
  <span class="dash-closure-month-accent" aria-hidden="true"></span>
  <span class="dash-closure-month-index" aria-hidden="true">{{ $monthNum }}</span>

  <div class="dash-closure-month-top">
    <div class="dash-closure-month-heading">
      <span class="dash-closure-month-label">{{ $monthShort }}</span>
      <span class="dash-closure-month-year">{{ $card['year'] }}</span>
    </div>
    <span @class([
      'dash-closure-status',
      'dash-closure-status--open' => $card['status'] === 'open',
      'dash-closure-status--closed' => $card['status'] === 'closed',
      'dash-closure-status--pending' => $card['status'] === 'not_opened',
    ])>
      <i class="fa {{ $statusIcon }}" aria-hidden="true"></i>
      {{ $card['status_label'] }}
    </span>
  </div>

  @if ($card['is_current'])
    <p class="dash-closure-month-current">
      <i class="fa fa-circle" aria-hidden="true"></i>
      Current month
    </p>
  @endif

  @if ($card['status'] === 'closed' && ($card['amt_sold'] !== null || $card['profits'] !== null))
    <div class="dash-closure-month-meta">
      <span>
        <em>Sold</em>
        Gh₵ {{ number_format((float) $card['amt_sold'], 2) }}
      </span>
      <span>
        <em>Profit</em>
        Gh₵ {{ number_format((float) $card['profits'], 2) }}
      </span>
    </div>
  @elseif ($card['status'] === 'open')
    <p class="dash-closure-month-hint">Open for sales this period</p>
  @else
    <p class="dash-closure-month-hint">Tap to open or review</p>
  @endif

  <span class="dash-closure-month-go" aria-hidden="true">
    <i class="fa fa-arrow-right"></i>
  </span>
</a>
