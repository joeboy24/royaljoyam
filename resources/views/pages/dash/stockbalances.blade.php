@extends('layouts.dashlay')

@section('sidebar-wrapper')
  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link" href="/dashboard">
          <i class="material-icons">dashboard</i> 
          <p>Dashboard</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/config">
          <i class="fa fa-cogs"></i>
          <p>Configuration</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/dashuser">
          <i class="fa fa-edit"></i>
          <p>Registry</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/waybill">
          <i class="fa fa-truck"></i>
          <p>Waybill</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/sales">
          <i class="fa fa-shopping-basket"></i>
          <p>Sales</p>
        </a>
      </li>
      <li class="nav-item active2">
        <a class="nav-link" href="/reporting">
          <i class="fa fa-file-text"></i>
          <p>Report</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="/closure_page">
          <i class="fa fa-calendar"></i>
          <p>Closure</p>
        </a>
      </li>
      <!--li class="nav-item ">
        <a class="nav-link" href="#">
          <i class="fa fa-table"></i>
          <p>Null</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="#">
          <i class="material-icons">library_books</i>
          <p>Null</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="#">
          <i class="fa fa-envelope"></i>
          <p>Messaging</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="#">
          <i class="material-icons">bubble_chart</i>
          <p>Help</p>
        </a>
      </li-->
      <li class="nav-item active-pro ">
        <a class="nav-link" href="#">
          <i class=""></i>
          <p>&nbsp;</p>
        </a>
      </li>
    </ul>
  </div>  
@endsection

@section('content')

  <!-- End Navbar -->
  <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-11">

              @include('inc.messages')

                <div class="row">
                  <div class="col-md-12">
                    <div class="menu_box_cont">
                      <div class="inner">
                        <a href="/reporting" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-shopping-basket"></i>&nbsp;&nbsp; Sales</h4>
                            <p>General sales report</p>
                          </div>
                        </a>
                        <a href="/stockbal" class="myA">
                          <div class="menu_box active_menu">
                            <h4><i class="fa fa-bar-chart"></i>&nbsp;&nbsp; Stock</h4>
                            <p>General stock balances</p>
                          </div>
                        </a>
                        <a href="/expensereport" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-suitcase"></i>&nbsp;&nbsp; Expenses</h4>
                            <p>General expenses report</p>
                          </div>
                        </a>
                        <a href="/debts" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-folder-open"></i>&nbsp;&nbsp; Debts</h4>
                            <p>Debts (Post Payments)</p>
                          </div>
                        </a>
                        <a href="/waybillreport" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-truck"></i>&nbsp;&nbsp; Waybill</h4>
                            <p>Waybill Report</p>
                          </div>
                        </a>
                        <a href="/returnsreport" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-warning"></i>&nbsp;&nbsp; Returns</h4>
                            <p>Returns report</p>
                          </div>
                        </a>
                        <a href="/distreport" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-share-alt"></i>&nbsp;&nbsp; Distribution</h4>
                            <p>Distribution report</p>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-md-12 offset-md-0">

                    <div class="form-group row mb-0 searchRef">
                        <form class="salesForm" action="{{action('DashController@stockbal')}}" method="GET">
                          @csrf
                          <div class="dropdown">

                            <input type="date" class="sref" name="date_from" placeholder="yyyy-mm-dd"/>
                            <input type="text" class="sref" name="" placeholder=" From - To " style="width:70px; border:none; padding:0" readonly/>
                            <input type="date" class="sref" name="date_to" placeholder="yyyy-mm-dd"/>
                            
                            <button type="submit" class="btn btn-info">&nbsp; Load Data</button>
                            <a href="/stockbal"><button type="button" class="btn btn-success" name="store_action" value="empty_cart"><i class="fa fa-refresh"></i></button></a>
                            @if (session('stockfill') == 0)
                              <a href="/stockreportprinting"><button type="button" class="btn black" name="store_action" value="empty_cart"><i class="fa fa-print"></i></button></a>
                            @else
                              <a href="/stockfillprint"><button type="button" class="btn black" name="store_action" value="empty_cart"><i class="fa fa-print"></i></button></a>
                            @endif
                            {{-- <a href="/stock"><button type="button" class="btn black" name="store_action" value="empty_cart"><i class="fa fa-print"></i></button></a> --}}
                            <a href="/genstockbal"><button type="button" class="btn black"><i class="fa fa-bar-chart"></i></button></a>
                            
                          </div>

                        </form>
                    </div>

                </div>

                <div class="card">
                  <div id="printarea1" class="card-body">
              
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
                      <p>No Records Found</p>
                    @endif
                  </div>
                </div>
            </div>

          </div>
        </div>
  </div>


@endsection

@section('footer')

<script type="text/javascript">
  $('#search').on('keyup',function(){
      $value=$(this).val();
      $.ajax({
          type : 'get',
          url : '{{URL::to('/searchfee')}}',
          data:{'search':$value},
          success:function(data){
          $('#tb').html(data);
          }
      });
  })
</script>
<script type="text/javascript">
  $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

@endsection