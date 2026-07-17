@props(['status'])

@if ($status === 'Delivered')
  <span {{ $attributes->merge(['class' => 'waybill-status-badge waybill-status-delivered']) }}>
    <i class="fa fa-check"></i> Delivered
  </span>
@elseif ($status === 'In Transit')
  <span {{ $attributes->merge(['class' => 'waybill-status-badge waybill-status-transit']) }}>
    <i class="fa fa-truck"></i> In Transit
  </span>
@else
  <span {{ $attributes->merge(['class' => 'waybill-status-badge waybill-status-pending']) }}>
    <i class="fa fa-clock-o"></i> Pending
  </span>
@endif
