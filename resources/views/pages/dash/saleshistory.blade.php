@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="sales"
  title="Sales History"
  subtitle="Review historical sales line items by date range."
  icon="fa fa-th-large"
>
  <x-slot:filters>
    <x-report-inventory-filters
      :action="url('/saleshistory')"
      clear-url="/saleshistory"
      print-url="/reportprinting"
      :show-branch="false"
    >
      <a href="/reporting" class="inventory-search-btn inventory-search-btn-muted" title="Open sales report">
        <i class="fa fa-shopping-basket"></i>
        <span>Sales report</span>
      </a>
    </x-report-inventory-filters>
  </x-slot:filters>

  <div id="printarea1">
    <p class="text-muted mb-0">Sales history table is being consolidated into the main sales report.</p>
  </div>
</x-report-shell>

@endsection
