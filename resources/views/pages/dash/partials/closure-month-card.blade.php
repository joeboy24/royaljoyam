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
  <div class="dash-closure-month-top">
    <span class="dash-closure-month-label">{{ $card['label'] }}</span>
    <span @class([
      'dash-closure-status',
      'dash-closure-status--open' => $card['status'] === 'open',
      'dash-closure-status--closed' => $card['status'] === 'closed',
      'dash-closure-status--pending' => $card['status'] === 'not_opened',
    ])>
      {{ $card['status_label'] }}
    </span>
  </div>

  @if ($card['is_current'])
    <p class="dash-closure-month-current">Current month</p>
  @endif

  @if ($card['status'] === 'closed' && ($card['amt_sold'] !== null || $card['profits'] !== null))
    <div class="dash-closure-month-meta">
      <span>Sold Gh₵ {{ number_format((float) $card['amt_sold'], 2) }}</span>
      <span>Profit Gh₵ {{ number_format((float) $card['profits'], 2) }}</span>
    </div>
  @elseif ($card['status'] === 'open')
    <p class="dash-closure-month-hint">Period is open for sales</p>
  @else
    <p class="dash-closure-month-hint">Not opened yet</p>
  @endif
</a>
