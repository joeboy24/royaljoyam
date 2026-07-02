<div class="sidebar-wrapper dash-sidebar">
  <ul class="nav">
    @foreach ($dashNavItems as $item)
      <li class="nav-item{{ $item['active'] ? ' active2' : '' }}">
        <a class="nav-link" href="{{ $item['url'] }}">
          @if (($item['icon'] ?? '') === 'material-icons')
            <i class="material-icons">{{ $item['icon_name'] ?? '' }}</i>
          @else
            <i class="{{ $item['icon'] }}"></i>
          @endif
          <p>{{ $item['label'] }}</p>
        </a>
      </li>
    @endforeach
    <li class="nav-item active-pro ">
      <a class="nav-link" href="#">
        <i class=""></i>
        <p>&nbsp;</p>
      </a>
    </li>
  </ul>
</div>
