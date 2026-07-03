@props(['status', 'remaining' => 0])

@php
  $badges = [
    'none' => ['class' => 'waybill-dist-none', 'icon' => 'fa-minus-circle', 'label' => 'No items'],
    'pending' => ['class' => 'waybill-dist-pending', 'icon' => 'fa-clock-o', 'label' => 'Pending'],
    'partial' => ['class' => 'waybill-dist-partial', 'icon' => 'fa-adjust', 'label' => 'Partial'],
    'complete' => ['class' => 'waybill-dist-complete', 'icon' => 'fa-check-circle', 'label' => 'Complete'],
  ];
  $badge = $badges[$status] ?? $badges['none'];
@endphp

<span {{ $attributes->merge(['class' => 'waybill-dist-badge '.$badge['class']]) }}>
  <i class="fa {{ $badge['icon'] }}"></i>
  <span>{{ $badge['label'] }}</span>
  @if ($remaining > 0 && in_array($status, ['pending', 'partial'], true))
    <span class="waybill-dist-badge-meta">{{ $remaining }} rem.</span>
  @endif
</span>
