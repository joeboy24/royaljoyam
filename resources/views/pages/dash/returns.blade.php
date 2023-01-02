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

                <div class="form-group row mb-0 hideMe">
                    <div class="col-md-5 offset-md-0 myTrim">
                      <div class="input-group no-border">
                        

                        <form action="{{action('FeesController@store')}}" method="POST">
                          @csrf
                        </form>

                      </div>
                    </div>
                </div>

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
                          <div class="menu_box">
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
                          <div class="menu_box active_menu">
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
                        <form class="salesForm" action="{{action('DashController@returnsreport')}}" method="GET">
                          @csrf
                          <div class="dropdown">

                            <input type="date" class="sref" name="date_from" placeholder="yyyy-mm-dd"/>
                            <input type="text" class="sref" name="" placeholder=" From - To " style="width:70px; border:none; padding:0" readonly/>
                            <input type="date" class="sref" name="date_to" placeholder="yyyy-mm-dd"/>

                            <select name="branch" class="sref" required>
                              <option>All Branches</option>
                              @if (count($branches) > 0)
                                @foreach ($branches as $branch)
                                  <option value="{{ $branch->tag }}">{{ $branch->name }}</option> 
                                @endforeach
                              @endif
                            </select>

                            <button type="submit" class="btn btn-info"></i> &nbsp; Load Data</button>
                            <a href="/debts"><button type="button" class="btn btn-success"><i class="fa fa-refresh"></i></button></a>
                            <a href="/returnprint"><button type="button" class="btn black"><i class="fa fa-print"></i></button></a>
                            {{-- <a href="/expensereportprinting"><button type="button" class="btn black" name="store_action" value="empty_cart"><i class="fa fa-print"></i></button></a> --}}
                            
                          </div>

                        </form>
                    </div>

                </div>

                <div class="card">
                  <div id="printarea1" class="card-body">
              
                    @if (count($returns) > 0)
                        <table class="table mt">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit Price (Gh₵)</th>
                            <th>Total (Gh₵)</th>
                            <th>Order Date</th>
                          </thead>
                          <tbody id="tb">

                            @foreach ($returns as $rtn)

                              @if ($rtn->del == 'no')
                                
                                @if ($c%2==0)
                                  <tr class="rowColour">
                                @else
                                  <tr>
                                @endif
                                  <td>{{$c++}}</td>
                                  <td>{{$rtn->name}}<br><p class="gray_p">Item No.: {{$rtn->item_no}}</p></td>
                                  <td>{{$rtn->qty}}</td>
                                  <td>{{$rtn->unit_price}}</td>
                                  <td>{{number_format($rtn->tot)}}</td>
                                  {{-- <td>
                                    @if ($rtn->status == 'Delivered')
                                      <p class="delivered"><i class="fa fa-check"></i>&nbsp;&nbsp;Delivered</p>
                                    @else
                                      <p class="pending"><i class="fa fa-warning"></i>&nbsp;&nbsp;Pending</p> 
                                    @endif
                                  </td> --}}
                                  <td>{{date('M. d, Y', strtotime($rtn->order_date))}}<br><p class="small_p">By: {{$rtn->user->name}}</p></td>
                                  
                                </tr>
                              
                              @endif

                            @endforeach

                            <tr>
                              <td></td>
                              <td><b>Total</b></td>
                              <td><b>{{ number_format(session('returnsrep')->sum('qty')) }}</b></td>
                              <td></td>
                              <td><b>{{ number_format(session('returnsrep')->sum('tot')) }}</b></td>
                              <td></td>
                            </tr>

                          </tbody>
                        </table>
                        {{-- <p>No. of Records : <b style="color: #000000">{{$returns->total()}}</b> &nbsp;&nbsp;&nbsp; Total Qty. : <b style="color: #000000">Gh₵ {{ number_format(session('returnsrep')->sum('tot')) }}</b></p> --}}
                        {{ $returns->appends(['date_from' => request()->query('date_from'), 'date_to' => request()->query('date_to'), 'branch' => request()->query('branch')])->links() }}

                        <div style="height: 30px">
                        </div>
      

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