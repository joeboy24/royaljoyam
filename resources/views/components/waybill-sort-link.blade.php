@props([
    'column',
    'label',
    'sort' => '',
    'dir' => 'desc',
    'listQuery' => [],
])

@php
  $nextDir = ($sort === $column && $dir === 'asc') ? 'desc' : 'asc';
  $href = url('/waybillview?' . http_build_query(array_merge($listQuery, [
      'sort' => $column,
      'dir' => $nextDir,
  ])));
@endphp

<a href="{{ $href }}" class="waybill-sort-link @if($sort === $column) is-active @endif">
  {{ $label }}
  @if ($sort === $column)
    <i class="fa fa-sort-{{ $dir === 'asc' ? 'asc' : 'desc' }}"></i>
  @else
    <i class="fa fa-sort"></i>
  @endif
</a>
