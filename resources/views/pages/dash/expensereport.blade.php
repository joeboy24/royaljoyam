@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="expenses"
  title="Expenses Report"
  subtitle="Review branch expenditure records by date."
  icon="fa fa-suitcase"
>
  <x-slot:filters>
    <x-report-inventory-filters
      :action="url('/expensereport')"
      clear-url="/expensereport"
      print-url="/expensereportprinting"
      filter-label="Expense filters"
      :branches="$branches"
    />
  </x-slot:filters>

  <div id="printarea1">
    <x-report-expenses-table :expenses="$expenses" />
  </div>
</x-report-shell>

@endsection

@section('footer')

<script type="text/javascript">
  $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

@endsection
