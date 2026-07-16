@props([
    'icon' => 'fa fa-inbox',
    'message' => 'No records found for the selected filters.',
])

<div class="dash-reports-empty">
  <span class="dash-reports-empty-icon"><i class="{{ $icon }}"></i></span>
  <p>{{ $message }}</p>
</div>
