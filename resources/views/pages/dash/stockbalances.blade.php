@extends('layouts.dashlay')

@section('content')

<x-report-shell
  active="stock"
  title="Stock Balances"
  subtitle="Review sold, remaining, and profit quantities by branch."
  icon="fa fa-bar-chart"
>
  <x-slot:filters>
    <x-report-inventory-filters
      :action="url('/stockbal')"
      clear-url="/stockbal"
      :show-branch="false"
    >
      @if (session('stockfill') == 0)
        <a href="/stockreportprinting" class="inventory-search-btn inventory-search-btn-muted inventory-search-btn-icon dash-tip" data-tip="Print report" aria-label="Print report">
          <i class="fa fa-print"></i>
        </a>
      @else
        <a href="/stockfillprint" class="inventory-search-btn inventory-search-btn-muted inventory-search-btn-icon dash-tip" data-tip="Print report" aria-label="Print report">
          <i class="fa fa-print"></i>
        </a>
      @endif
      <a href="/genstockbal" class="inventory-search-btn inventory-search-btn-muted" title="General stock balances">
        <i class="fa fa-bar-chart"></i>
        <span>General</span>
      </a>
    </x-report-inventory-filters>
  </x-slot:filters>

  <div id="printarea1">
              
                    @if (count(session('stock')) > 0)
                      <table class="table mt">
                        <thead class=" text-secondary hideMe">
                          <th>#</th>
                          <th>Item Details.</th>
                          @foreach (session('compbranch') as $br)
                            <th></th>
                            <th class="ryt">Branch</th>
                            <th>{{$br->tag}}</th>
                            <th></th>
                          @endforeach
                        </thead>
                        <tbody id="tb">

                          <tr>
                            <td></td>
                            <td></td>
                            @foreach (session('compbranch') as $br)
                              <td class="ryt avl2">QTY<br><p class="small_gr">SOLD</p></td>
                              <td class="ryt avl2">TOTAL<br><p class="small_gr">AMT. Gh₵</p></td>
                              <td class="added">QTY<br><p class="small_gr">REM</p></td>
                              <td class="avl2">PROFIT<br><p class="small_gr">Gh₵</p></td>
                            @endforeach
                          </tr>

                          @foreach (session('stock') as $stk)
                              
                              <tr>
                                <td>{{$x++}}</td>
                                <td>{{$stk->item->name}}<br><p class="small_p">{{$stk->item->item_no.' - '.$stk->item->desc}}</p></td>
                                @for ($i = 0; $i < count(session('compbranch')); $i++)
                                  <input type="hidden" value="{{$qval = 'q'.$i+1}}">
                                  <input type="hidden" name="tvalue" value="{{$qtr = $stk->item->$qval}}">
                                  <input type="hidden" name="tvalue" value="{{$qtr_tot = $qtr_tot + $stk->item->$qval}}">
                                  @foreach (session('sales_history') as $sh)
                                    @if ($stk->item_id == $sh->item_id && $sh->user_bv == $i+1)
                                      <input type="hidden" value="{{$qts = $qts + $sh->qty}}">
                                      <input type="hidden" value="{{$tamt = $tamt + $sh->tot}}">
                                      <input type="hidden" value="{{$tprof = $tprof + $sh->profits}}">
                                    @endif
                                  @endforeach
                                  <td class="ryt avl2 c1">@if($qts!=0){{number_format($qts)}}@else-@endif</td>
                                  <td class="ryt avl2 c2">@if($tamt!=0){{number_format($tamt)}}@else-@endif</td>
                                  <td class="added c3">{{number_format($qtr)}}</td>
                                  <td class="avl c4">@if($tprof!=0){{number_format($tprof, 2)}}@else-@endif</td>
                                
                                  <input type="hidden" value="{{$qts=0}}">
                                  <input type="hidden" value="{{$tamt=0}}">
                                  <input type="hidden" value="{{$tprof=0}}">
                                @endfor
                                <input type="hidden" value="{{$qtr=0}}">
                              </tr>

                          @endforeach

                          <tr>
                            <td></td>
                            <td><h6>Total</h6></td>
                            @for ($i = 0; $i < count(session('compbranch')); $i++)
                              
                              <input type="hidden" name="tvalue" value="{{$qtr_tot = $qtr_tot + $stk->item->$qval}}">
                                @foreach (session('sales_history') as $sh)
                                  @if ($sh->user_bv == $i+1)
                                    <input type="hidden" value="{{$qts = $qts + $sh->qty}}">
                                    <input type="hidden" value="{{$tamt = $tamt + $sh->tot}}">
                                    <input type="hidden" value="{{$tprof = $tprof + $sh->profits}}">
                                  @endif
                                @endforeach

                                <input type="hidden" value="{{$qval = 'q'.$i+1}}">
                                <td class="ryt avl2 c1"><h6>@if($qts!=0){{number_format($qts)}}@endif</h6></td>
                                <td class="ryt avl2 c2"><h6>@if($tamt!=0){{number_format($tamt)}}@endif</h6></td>
                                <td class="added c3">
                                  <h6>{{-- {{number_format($qtr)}} --}}</h6>
                                </td>
                                <td class="avl c4"><h6>@if($tprof!=0){{number_format($tprof, 2)}}@endif</h6></td>
                              
                                <input type="hidden" value="{{$qts=0}}">
                                <input type="hidden" value="{{$tamt=0}}">
                                <input type="hidden" value="{{$tprof=0}}">
                            @endfor
                          </tr>

                        </tbody>
                      </table>

    @else
      <x-report-empty-state
        icon="fa fa-bar-chart"
        message="No stock data for the selected filters. Load data from the toolbar first."
      />
    @endif
  </div>
</x-report-shell>

@endsection

@section('footer')


<script type="text/javascript">
  $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

@endsection