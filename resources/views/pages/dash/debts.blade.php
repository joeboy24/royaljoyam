@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="debts"
  title="Debts Report"
  subtitle="Review outstanding post-payment sales by branch."
  icon="fa fa-folder-open"
>
  <x-slot:filters>
    <x-report-inventory-filters
      :action="url('/debts')"
      clear-url="/debts"
      print-url="/debtsreportprinting"
      filter-label="Debts filters"
      :show-search="true"
      search-name="debtsearch"
      search-placeholder="Order no., buyer, contact, notes..."
      :branches="$branches"
    />
  </x-slot:filters>

  <div id="printarea1">
    <x-report-debts-table :sales="$sales" />
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
