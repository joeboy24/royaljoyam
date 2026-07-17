@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="debts"
  title="Debts Report"
  subtitle="Review post-payment sales by branch and settlement status."
  icon="fa fa-folder-open"
>
  <x-slot:actions>
    <a href="{{ url('/paid_debts') }}" class="dash-page-header-btn inventory-action-btn dash-tip" data-tip="View collected debt payments">
      <i class="fa fa-check-circle"></i>
      <span>Paid debts</span>
    </a>
  </x-slot:actions>

  <x-slot:filters>
    <x-report-inventory-filters
      :action="url('/debts')"
      clear-url="/debts"
      print-url="/debtsreportprinting"
      filter-label="Debts filters"
      :show-search="true"
      :show-debt-status="true"
      search-name="debtsearch"
      search-placeholder="Order no., buyer, contact, notes..."
      :branches="$branches"
    />
  </x-slot:filters>

  <div id="printarea1">
    <x-report-debts-table :sales="$sales" :debt-status="request()->query('debt_status', 'outstanding')" />
  </div>

  <x-report-sales-modals />
  <x-report-pay-debt-modal />
</x-report-shell>

@endsection

@section('footer')

<script type="text/javascript">
  $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

@endsection
