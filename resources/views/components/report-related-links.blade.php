@props([
    'links' => [],
])

@if (count($links) > 0)
  <div class="dash-reports-related-links" aria-label="Related reports">
    @foreach ($links as $link)
      <a href="{{ $link['url'] }}" class="dash-reports-related-link">
        <i class="fa {{ $link['icon'] ?? 'fa-external-link' }}"></i>
        <span>{{ $link['label'] }}</span>
      </a>
    @endforeach
  </div>
@endif
