@props([
    'title',
    'subtitle' => null,
    'icon' => 'fa fa-folder-open',
])

<div {{ $attributes->merge(['class' => 'card-header card-header-primary dash-page-header']) }}>
    <div class="dash-page-header-main">
        <div class="dash-page-header-title-row">
            <span class="dash-page-header-icon" aria-hidden="true">
                <i class="{{ $icon }}"></i>
            </span>
            <div>
                <h4 class="card-title dash-page-header-title">{{ $title }}</h4>
                @if ($subtitle)
                    <p class="dash-page-header-subtitle">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
    </div>

    @isset($actions)
        <div class="dash-page-header-actions">
            {{ $actions }}
        </div>
    @endisset
</div>
