@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="returns"
  title="Returns Report"
  subtitle="Review returned order line items by date and branch."
  icon="fa fa-warning"
>
  <x-slot:filters>
    <x-report-inventory-filters
      :action="url('/returnsreport')"
      clear-url="/returnsreport"
      print-url="/returnprint"
      filter-label="Returns filters"
      :show-search="true"
      search-name="returnsearch"
      search-placeholder="Item no., name, cashier..."
      :branches="$branches"
    />
  </x-slot:filters>

  <div id="printarea1">
    <x-report-returns-table :returns="$returns" />
  </div>
</x-report-shell>

@endsection

@section('footer')

<script type="text/javascript">
  $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

@endsection
