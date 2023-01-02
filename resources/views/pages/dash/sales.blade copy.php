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
        <a class="nav-link" href="/addstudent">
          <i class="fa fa-group"></i>
          <p>Students</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="/dashuser">
          <i class="fa fa-edit"></i>
          <p>Registry</p>
        </a>
      <li class="nav-item active2">
        <a class="nav-link" href="/fees">
          <i class="material-icons">content_paste</i>
          <p>Fees Mgt.</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="#">
          <i class="fa fa-list"></i>
          <p>Attendance</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="#">
          <i class="fa fa-table"></i>
          <p>Timetable</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="#">
          <i class="material-icons">library_books</i>
          <p>Exam & Results</p>
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
      </li>
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

                <div class="col-md-12 offset-md-0">

                  

                    <div class="form-group row mb-0 searchRef">

                      <div>
                        <form class="salesForm">
                          <select>
                            <option selected>Cement Bag</option>
                            <option>Wood Nails Pack</option>
                            <option>Pipe Tube(Size 2)</option>
                          </select>
                          <input class="sref" type="text" placeholder="Reference" />
                          <input class="sqty" type="number" placeholder="Qty" value="1" min="1" />
                          <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> &nbsp; Add</button>
                          <button type="submit" class="btn btn-info"  data-toggle="modal" data-target="#orderModal"><i class="fa fa-users"></i> &nbsp; Add Order Details</button>
                        </form>
                      </div>

                        {{-- <a><button type="submit" name="store_action" value="refresh_val" class="myrefbtn" id="mb">
                          <i class="fa fa-refresh"></i>
                          <div class="ripple-container"></div>
                        </button></a>

                        <div class="col-md-9 offset-md-0">
                            <button type="submit" class="btn btn-primary pull-right" data-toggle="modal" data-target="#catModal"><i class="fa fa-users"></i> &nbsp; Add Category</button>
                        </div> --}}
                    </div>
                  </div>

              <div class="card">
                <div class="card-header card-header-primary hideMe">
                  <h4 class="card-title">All Records On Fees</h4>
                  {{-- <p class="card-category">Complete your profile here..</p> --}}
                </div>
                <div id="printarea1" class="card-body">
            
                    {{-- @if (count($orders) > 0) --}}
                        <table class="table mt">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Ref.</th>
                            <th>Item Id.</th>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Unit Price(Gh₵)</th>
                            <th>Total(Gh₵)</th>
                            <th>User</th>
                            <th>Date</th>
                            <th class="ryt">Actions</th>
                          </thead>
                          <tbody id="tb">

                            @for ($i = 1; $i < 5; $i++)
                                @if ($i%2==0)
                                  <tr class="rowColour"><td>{{$i}}</td>
                                @else
                                  <tr><td>{{$i}}</td>
                                @endif
                                  <td>Ref-0017213-21{{$i}}</td>
                                  <td>M219234{{$i}}</td>
                                  <td>Cement Bag</td>
                                  <td>3</td>
                                  <td>40.00</td>
                                  <td>120.00</td>
                                  <td>Code80</td>
                                  <td>{{date('Y.m.d is')}}</td>
                                  <td class="ryt">
                                    
                                    <form action="{{ action('ItemsController@update', '1') }}" method="POST" enctype="multipart/form-data">
                                      <input type="hidden" name="_method" value="PUT">
                                      @csrf

                                      <a href="" class="edit" data-toggle="modal" rel="tooltip" title="Edit Record" data-target="#edit_"><i class="fa fa-pencil"></i></a>
                                      {{-- <button type="button" class="view2" rel="tooltip" title="View Record" data-toggle="modal" data-target="#{{$order->id}}"><i class="fa fa-folder-open"></i></button>
                                       --}}
                                      <button type="submit" name="store_action" value="del_ord" rel="tooltip" title="Delete Item" class="close2" onclick="return confirm('Are you sure you want to delete selected item?');"><i class="fa fa-close"></i></button>
                                    

                                      {{-- <div class="modal fade" id="edit_{{$order->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modtop" role="document">
                                          <div class="modal-content">
                                              
                                              <div class="card card-profile">
                                                <div class="card-avatar">
                                                  <a href="#pablo">
                                                  <img class="img" src="/storage/rjv_receipts/{{$order->order_receipt}}" />
                                                  </a>
                                                </div>
                                                <div class="card-body">
                                                  <h4 class="card-category text-gray">Item N0: {{$order->item_no}}</h4>
                                                  <h6 class="card-title">Created by: {{$order->user->id}}</h6>




                                                  <table class="user_view_tbl">
                                                    <tbody>

                                                      <tr class="tbl_tr"><td class="tl">Reference No./Id.</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" name="ref" placeholder="Reference No./Id." value="{{$order->ref}}" required/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Company Name</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" name="company_name" placeholder="Reference No./Id." value="{{$order->company_name}}" required/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Company Name</td><td class="tr">
                                                        <div class="form-group">
                                                          <textarea type="text" class="form-control" rows="4" name="desd" placeholder="Company Name" required>{{$order->desc}}</textarea>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Contact</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" name="contact" placeholder="Contact" value="{{$order->contact}}" required/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Description</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type='text' class="form-control" placeholder="Description" name="desc" value="{{$order->desc}}"/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Total</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="number" class="form-control" name="tot" placeholder="Total" value="{{$order->tot}}" required/>
                                                        </div>
                                                      </td></tr>

                                                    </tbody>
                                                  </table>


                                                                   

                                                </div>
                                              </div>
                                              
                                              <div class="modal-footer">
                                                <button type="submit" class="btn btn-info" name="store_action" value="update_item"><i class="fa fa-save"></i> &nbsp; Update Record</button>
                                              </div>

                                          </div>
                                    
                                        </div>
                                      </div> --}}

                                    </form>                  
                                    
                                  </td>
                                </tr>
                            @endfor
                            <tr><td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>13</td>
                                <td></td>
                                <td>480.00</td>
                                <td>Code80</td>
                                <td>{{date('Y.m.d is')}}</td>
                                <td class="ryt">
                                  
                                  <form action="{{ action('ItemsController@update', '1') }}" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="_method" value="PUT">
                                    @csrf

                                    <a href="" class="edit" data-toggle="modal" rel="tooltip" title="Edit Record" data-target="#edit_"><i class="fa fa-pencil"></i></a>
                                    {{-- <button type="button" class="view2" rel="tooltip" title="View Record" data-toggle="modal" data-target="#{{$order->id}}"><i class="fa fa-folder-open"></i></button>
                                     --}}
                                    <button type="submit" name="store_action" value="del_ord" rel="tooltip" title="Delete Item" class="close2" onclick="return confirm('Are you sure you want to delete selected item?');"><i class="fa fa-close"></i></button>
                                  
                                  </form>                  
                                  
                                </td>
                              </tr>
                              

                          </tbody>
                        </table>
                        {{-- <p>Total Records : <b style="color: #000000">{{count($orders)}}</b></p> --}}

                        <div style="height: 30px">
                        </div>
      

                    {{-- @else
                      <p>No Records Found</p>
                    @endif --}}
                </div>
              </div>
  

              <div class="container-fluid hideMe">

                <div class="row">
      
                  <div class="col-lg-3 col-md-6 col-sm-6">
                    <a href="/feereports" class="myA">
                      <div class="card card-stats">
                        <button class="btn salesBtn purple"><i class="fa fa-shopping-basket salesI"></i></button>
                        <h4 class='config2'>Gh₵ 480.00</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Daily Sales Report</div>
                        </div>
                      </div>
                    </a>
                  </div>
      
                  <div class="col-lg-3 col-md-6 col-sm-6">
                    <a href="/feereports" class="myA">
                      <div class="card card-stats">
                        <button class="btn salesBtn pink"><i class="fa fa-money salesI"></i></button>
                        <h4 class='config2'>Gh₵ 170.00</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Daily Expenditure Report</div>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-3 col-md-6 col-sm-6">
                    <a href="/feereports" class="myA">
                      <div class="card card-stats">
                        <button class="btn salesBtn mygreen"><i class="fa fa-shopping-basket salesI"></i></button>
                        <h4 class='config2'>Gh₵ 480.00</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Daily Sales Report</div>
                        </div>
                      </div>
                    </a>
                  </div>
      
                  <div class="col-lg-3 col-md-6 col-sm-6">
                    <a href="/feereports" class="myA">
                      <div class="card card-stats">
                        <button class="btn salesBtn seablue"><i class="fa fa-shopping-basket salesI"></i></button>
                        <h4 class='config2'>Gh₵ 170.00</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Daily Expenditure Report</div>
                        </div>
                      </div>
                    </a>
                  </div>
      
                </div>

              </div>

            </div>



          </div>
        </div>
  </div>




  <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp; Record Order Details Here</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          
            <form action="{{action('ItemsController@store')}}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="ref" placeholder="Reference No/Id" type="text" class="form-control" name="ref" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="company_name" placeholder="From: Company Name" type="text" class="form-control" name="company_name" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="contact" placeholder="From: Company's Contact" type="text" class="form-control" name="contact" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                    <textarea name="desc" class="form-control" rows="3" placeholder="Description"></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="tot" placeholder="Total Amt. Gh₵" type="number" class="form-control" name="tot" required autofocus>
                    </div>
                </div>

                <div class="col-md-12">
                    <label class="upfiles">Upload Receipt: &nbsp; </label>
                    <input type="file" name="repfile" required>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-info" name="store_action" value="add_order"><i class="fa fa-save"></i> &nbsp; Submit</button>
                    </div>
                </div>
            </form>

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