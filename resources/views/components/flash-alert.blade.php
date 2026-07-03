@props(['type' => 'info', 'message'])

@php
  $normalizedType = $type === 'danger' ? 'error' : $type;
  $variants = [
    'success' => ['title' => 'Success', 'icon' => 'fa-check'],
    'error' => ['title' => 'Error', 'icon' => 'fa-times-circle'],
    'warning' => ['title' => 'Warning', 'icon' => 'fa-exclamation-triangle'],
    'info' => ['title' => 'Info', 'icon' => 'fa-info-circle'],
  ];
  $variant = $variants[$normalizedType] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => 'dash-flash dash-flash-'.$normalizedType]) }} role="alert" data-dash-flash>
  <span class="dash-flash-icon" aria-hidden="true">
    <i class="fa {{ $variant['icon'] }}"></i>
  </span>
  <div class="dash-flash-body">
    <span class="dash-flash-title">{{ $variant['title'] }}</span>
    <p class="dash-flash-text">{{ $message }}</p>
  </div>
  <button type="button" class="dash-flash-close" data-dash-flash-close aria-label="Dismiss message">
    <i class="material-icons">close</i>
  </button>
</div>
