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
                          <div class="menu_box active_menu">
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
                        <form class="salesForm" action="{{action('DashController@debts')}}" method="GET">
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
                            <a href="/debts"><button type="button" class="btn btn-success" name="store_action" value="empty_cart"><i class="fa fa-refresh"></i></button></a>
                            {{-- <a href="/expensereportprinting"><button type="button" class="btn black" name="store_action" value="empty_cart"><i class="fa fa-print"></i></button></a> --}}
                            <a href="/debtsreportprinting"><button type="button" class="btn black" name="store_action" value="empty_cart"><i class="fa fa-print"></i></button></a>
                            
                          </div>

                        </form>
                    </div>

                </div>

                <div class="card">
                  <div id="printarea1" class="card-body">
              
                    @if (count($sales) > 0)
                        <table class="table mt">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Order No.</th>
                            <th>User</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Pay Mode</th>
                            <th>Buyer's Name</th>
                            <th>Status</th>
                            <th>Date/Time Created</th>
                            <th class="ryt">Actions</th>
                          </thead>
                          <tbody id="tb">

                            @foreach ($sales as $sale)

                              @if ($sale->del == 'no')
                                
                                @if ($c%2==0)
                                  @if ($sale->del_status == 'Not Delivered')
                                    @if ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid')
                                      <tr class="debt_alert">
                                    @else
                                    <tr class="not_delivered">
                                    @endif
                                  @else
                                    @if ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid')
                                      <tr class="debt_alert">
                                    @else
                                      <tr class="rowColour">
                                    @endif
                                  @endif
                                @else
                                  @if ($sale->del_status == 'Not Delivered')
                                    @if ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid')
                                      <tr class="debt_alert">
                                    @else
                                    <tr class="not_delivered">
                                    @endif
                                  @else
                                    @if ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid')
                                      <tr class="debt_alert">
                                    @else
                                      <tr>
                                    @endif
                                  @endif
                                @endif
                                  <td>{{$c++}}</td>
                                  <td>{{$sale->order_no}}</td>
                                  <td>{{$sale->user->name}}<br>{{$sale->user->status}}</td>
                                  <td>{{$sale->qty}}</td>
                                  <td>Gh₵ {{number_format($sale->tot, 2)}}</td>
                                  <td>{{$sale->pay_mode}}<br>
                                    @if($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid == 'Paid')
                                      <b>{{$sale->paid}}</b>
                                      &nbsp; <i class="fa fa-check" style="color: rgb(0, 163, 0)"></i>
                                    @endif
                                  </td>  
                                  <td>{{$sale->buy_name}}<br>{{$sale->buy_contact}}</td>
                                  <td>{{$sale->del_status}}</td>
                                  <td>{{$sale->created_at}}<br><p style="color: #0071ce; margin: 0">{{$sale->updated_at}}</p></td>  

                                    <td>
                                      <a href="/reporting/{{$sale->id}}"><button type="button" title="Print Order" class="print_black"><i class="fa fa-print"></i></button></a>
                                      
                                      @if ($sale->paid != 'Paid' && $sale->pay_mode == 'Post Payment(Debt)')
                                        {{-- @if ($sale->tot - ())
                                            
                                        @endif --}}
                                        <button type="submit" data-toggle="modal" data-target="#pay_debt{{$sale->id}}" title="Pay Debt" class="print_black"><i class="fa fa-money"></i></button>
                                        
                                        <div class="modal fade" id="pay_debt{{$sale->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                          <div class="modal-dialog modtop" role="document">
                                            <div id="printarea" class="modal-content">
                                                <div class="modal-header">
                                                <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-save"></i>&nbsp;&nbsp; Make Payment for {{$sale->buy_name}}</h6>
                                              </div>
                                                <div class="card card-profile">
                                                  <div class="card-avatar">
                                                    <a href="#">
                                                    {{-- <img class="img" src="/storage/members_imgs/{{$fee->student->photo}}" /> --}}
                                                    </a>
                                                  </div>
                                                  <div class="card-body">
                                                    <h6 class="card-category text-gray"></h6>
                                                    <div style="height: 30px">
                                                    </div>
                                      
                                                    <form action="{{ action('ItemsController@store') }}" method="POST">
                                                      @csrf
                      
                                                      <div class="cartIncrease">
                                                        <input type="hidden" name="send_id" value="{{$sale->id}}">
                                                        <input type="hidden" name="send_tot" value="{{$sale->tot}}">
                                                        <input type="number" min="1" step="any" name="amt_paid" placeholder="Amount" value="{{$sale->tot - $sale->paid_debt}}" max="{{$sale->tot - $sale->paid_debt}}">
                                                        <button class="black_btn" type="submit" name="store_action" value="pay_debt" onclick="return confirm('Are you sure you want to proceed payment?');"><i class="fa fa-money"></i> &nbsp; Pay</button>
                                                      </div>
                                                        
                                                    </form>
                                      
                                                  </div>
                                                </div>
                                            </div>
                                      
                                          </div>
                                        </div>
                                      @endif
                                      
                                      <button type="submit" data-toggle="modal" data-target="#edit_order{{ $sale->id }}" title="Edit Order" class="print_black">&nbsp;<i class="fa fa-pencil"></i>&nbsp;</button>
                                      <div class="modal fade" id="edit_order{{ $sale->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        
                                        <div class="modal-dialog modtop" role="document">
                                          <div id="printarea" class="modal-content">
                                            <div class="modal-header">
                                              <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-save"></i>&nbsp;&nbsp; Edit {{ $sale->buy_name }}'s order details</h6>
                                            </div>
                                              <div class="card card-profile">
                                                <div class="card-avatar">
                                                  <a href="#">
                                                  {{-- <img class="img" src="/storage/members_imgs/{{$fee->student->photo}}" /> --}}
                                                  </a>
                                                </div>
                                                <div class="card-body">
                                                  <h6 class="card-category text-gray"></h6>
                                                  <div style="height: 30px">
                                                  </div>
                                    
                                                  <form action="{{ action('ItemsController@update', $sale->id) }}" method="POST">
                                                    <input type="hidden" name="_method" value="PUT">
                                                    @csrf
                    
                                                    <div class="my_panel">

                                                      <div class="input_div">
                                                          <p>Buyer's Name: </p>
                                                          <input type="text" class="sref2" name="buy_name" placeholder="Buyer's Name" value="{{ $sale->buy_name }}" required/>
                                                      </div>
                          
                                                      <div class="input_div">
                                                          <p>Contact: </p>
                                                          <input type="number" class="sref2" name="buy_contact" placeholder="Contact" min="0" value="{{ $sale->buy_contact }}" required/>
                                                      </div>
                          
                                                      <div class="input_div">
                                                          <select name="pay_mode">
                                                            <option selected>{{ $sale->pay_mode }}</option>
                                                            <option>Cash</option>
                                                            <option>Cheque</option>
                                                            <option>Mobile Money</option>
                                                            <option>Post Payment(Debt)</option>
                                                          </select> 
                                                      </div>

                                                      <div class="input_div">
                                                        <button type="submit" class="btn btn-info pull-left" name="store_action" value="update_sales"><i class="fa fa-save"></i> &nbsp; Update</button>
                                                      </div>

                                                    </div>

                                                  </form>
                                    
                                                </div>
                                              </div>
                                          </div>
                                    
                                        </div>
                                      </div>
                                    </td> 

                                </tr>
                              
                              @endif

                            @endforeach

                          </tbody>
                        </table>
                        <p>No. of Records : <b style="color: #000000">{{$sales->total()}}</b> &nbsp;&nbsp;&nbsp; Total Amount : <b style="color: #000000">Gh₵ {{ number_format(session('debts')->sum('tot'), 2) }}</b></p>
                        {{-- {{ $sales->links() }} --}}
                        {{ $sales->appends(['date_from' => request()->query('date_from'), 'date_to' => request()->query('date_to'), 'branch' => request()->query('branch')])->links() }}

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