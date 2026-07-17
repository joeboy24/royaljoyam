@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="sales"
  title="Sales Report"
  subtitle="Filter sales by date, branch, and delivery status."
  icon="fa fa-shopping-basket"
  panel-class="dash-reports-sales-panel"
>
  <x-slot:filters>
    <x-report-inventory-filters
      :action="url('/reporting')"
      clear-url="/reporting"
      print-url="/reportprinting"
      filter-label="Sales filters"
      :show-delivery="true"
      :branches="$branches"
    />
  </x-slot:filters>

  <x-slot:actions>
    <x-report-sales-date-bar compact />
  </x-slot:actions>

  <div id="printarea1">
    <x-report-sales-table
      :sales="$sales"
      :cash="$cash"
      :cheque="$cheque"
      :momo="$momo"
      :sum-dbt="$sum_dbt"
    />
  </div>

  <x-slot:footer>
    <x-report-branch-kpis
      :b1="$b1"
      :b2="$b2"
      :b3="$b3"
      :b4="$b4"
      :b5="$b5"
      :b1_profits="$b1_profits"
      :b2_profits="$b2_profits"
      :b3_profits="$b3_profits"
      :b4_profits="$b4_profits"
      :b5_profits="$b5_profits"
      :exp_b1="$exp_b1"
      :exp_b2="$exp_b2"
      :exp_b3="$exp_b3"
      :exp_b4="$exp_b4"
      :exp_b5="$exp_b5"
      :expenses="$expenses"
    />
  </x-slot:footer>
</x-report-shell>

<x-report-breakdown-modal :breakdown="$breakdown" :branches="$branches" />
<x-report-sales-modals />

@endsection

@section('footer')
<script type="text/javascript">
  $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>
@endsection
