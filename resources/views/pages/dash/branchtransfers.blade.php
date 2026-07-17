@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="stock"
  title="Branch Transfers"
  subtitle="Review stock moved between branches for customer pickup or rebalancing."
  icon="fa fa-exchange"
>
  <x-slot:actions>
    <a href="{{ \App\Support\ReportPrintQuery::url('/stockbal') }}" class="dash-page-header-btn inventory-action-btn dash-tip" data-tip="Back to stock balances">
      <i class="fa fa-arrow-left"></i>
      <span>Stock balances</span>
    </a>
  </x-slot:actions>

  <x-slot:filters>
    <x-report-transfer-filters
      :action="url('/branchtransfers')"
      clear-url="/branchtransfers"
      :branches="$branches"
    />
  </x-slot:filters>

  <div id="printarea1">
    <x-report-transfers-table :transfers="$transfers" />
  </div>
</x-report-shell>

@endsection

@section('footer')

<script type="text/javascript">
  $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

@endsection
