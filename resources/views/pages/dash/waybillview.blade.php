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
      <li class="nav-item active2">
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
      <li class="nav-item ">
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

                    <form style="width: 400px" method="GET" action="{{ url('/waybillview') }}">
                      <div class="input-group no-border">
                        {{-- <input type="text" value="" class="form-control search_field" id="search" name="search" placeholder="Search Records...">
                        <button type="submit" class="btn btn-white btn-round my_bt">
                          <i class="material-icons">search</i>
                          <div class="ripple-container"></div>
                        </button> --}}

                          <input type="search" value="" class="form-control search_field" id="waybillsearch" name="waybillsearch" placeholder="Search Waybill...">
                          
                          <button type="submit" class="btn btn-white btn-round my_bt">
                            <i class="material-icons">search</i>
                            <div class="ripple-container"></div>
                          </button>

                          <a href="/waybillview" class="refresh_a"><button type="submit" class="btn btn-success btn-round" id="mb">
                            <i class="fa fa-refresh"></i>
                            <div class="ripple-container"></div>
                          </button></a>
                          
                      </div>
                    </form>
                      
                  </div>
                  <div class="col-md-7 offset-md-0 myTrim">
                    <a href="#"><button type="submit" class="btn btn-white pull-right" title="Recycle Bin"><i class="fa fa-trash"></i></button></a>
                    <a href="/waybill"><button type="submit" class="btn btn-white pull-right" ><i class="fa fa-arrow-left"></i></button></a>
                    {{-- <a href="/students"><button type="submit" class="btn btn-white pull-right" ><i class="fa fa-refresh"></i></button></a> --}}
                  </div>

                </div>

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Waybill History</h4>
                  {{-- <p class="card-category">Complete your profile here..</p> --}}
                </div>
                <div id="printarea1" class="card-body">
            
                    @if (count($waybills) > 0)
                        <table class="table mt">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Stock No.</th>
                            <th>Company</th>
                            {{-- <th>Address / Contact</th> --}}
                            <th>Driver</th>
                            <th>Bill No.</th>
                            <th>Weight</th>
                            <th>Pieces</th>
                            <th>Qty.</th>
                            <th>Status</th>
                            <th>Delivery Date</th>
                            <th class="ryt actsize">Actions</th>
                          </thead>
                          <tbody id="tb">

                            @foreach ($waybills as $waybill)

                              @if ($waybill->del == 'no')
                                
                                @if ($c%2==0)
                                  <tr class="rowColour">
                                @else
                                  <tr>
                                @endif
                                  <td>{{$c++}}</td>
                                  <td>{{$waybill->stock_no}}<br><p class="small_p">User: {{$waybill->user->name}}</p></td>
                                  {{-- <td>{{$waybill->comp_name}}</td> --}}
                                  <td>{{$waybill->comp_name.', '.$waybill->comp_add}}<br><p class="small_p">{{$waybill->comp_contact}}</p></td>
                                  <td>{{$waybill->drv_name}}<br>{{$waybill->drv_contact}}<br><p class="small_p">{{$waybill->vno}}</p></td>
                                  {{-- <td>{{number_format($waybill->)}}</td> --}}
                                  <td>{{$waybill->bill_no}}</td>
                                  <td>{{$waybill->weight}}</td>
                                  <td>{{$waybill->nop}}</td>
                                  <td>{{$waybill->tot_qty}}</td>
                                  <td>
                                    @if ($waybill->status == 'Delivered')
                                      <p class="delivered"><i class="fa fa-check"></i>&nbsp;&nbsp;Deliv...</p>
                                    @else
                                      <p class="pending"><i class="fa fa-warning"></i>&nbsp;&nbsp;Pending</p> 
                                    @endif
                                  </td>
                                  <td>{{date('M. d, Y', strtotime($waybill->del_date))}}</td>

                                  <td class="ryt">
                                    
                                    <form action="{{ action('ItemsController@update', $waybill->id) }}" method="POST">
                                      <input type="hidden" name="_method" value="PUT">
                                      @csrf

                                      <a href="" class="my_trash green color10" data-toggle="modal" rel="tooltip" title="Edit Record" data-target="#edit_{{$waybill->id}}"><i class="fa fa-pencil"></i></a>
                                      {{-- <button type="button" class="view2" rel="tooltip" title="View Record" data-toggle="modal" data-target="#{{$waybill->id}}"><i class="fa fa-folder-open"></i></button>
                                       --}}
                                      <a href="/distribution/{{$waybill->id}}" class="my_trash bg3 color10" rel="tooltip" title="Distribute"><i class="fa fa-share-alt"></i></a>
                                      <button name="store_action" value="del_waybil" rel="tooltip" title="Delete Waybil" class="icon_btn color6" title="Distribute" onclick="return confirm('Are you sure you want to delete record?');"><i class="fa fa-trash"></i></button>
                                      {{-- <a type="submit" name="store_action" value="del_item" rel="tooltip" title="Delete Item" class="my_trash bg6 color8" onclick="return confirm('Are you sure you want to delete record?');"><i class="fa fa-trash"></i></a> --}}
                                    

                                      <div class="modal fade" id="edit_{{$waybill->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modtop" role="document">
                                          <div class="modal-content">
                                              
                                              <div class="card card-profile">
                                                <div class="card-body">
                                                  
                                                  <div class="row justify-content-center">

                                                      <div class="col-md-6 cl">
                                                        <div style="height:30px"></div>
                                                        
                                                        <p>Sender Info. / From:</p>
                                                        <div class="my_panel">
                                                          <div class="input_div">
                                                              <p>Company Name: </p>
                                                              <input type="text" name="comp_name" value="{{$waybill->comp_name}}" required/>
                                                          </div>
                              
                                                          <div class="input_div">
                                                              <p>Address: </p>
                                                              <textarea name="comp_add" rows="4" required>{{$waybill->comp_add}}</textarea>
                                                          </div>
                              
                                                          <div class="input_div">
                                                              <p>Contact: </p>
                                                              <input type="text" name="comp_contact" value="{{$waybill->comp_contact}}" required/>
                                                          </div>
                                                        </div>
                              
                                                        <p>Dispatch Driver</p>
                                                        <div class="my_panel">
                                                          <div class="input_div">
                                                              <p>Driver's Name: </p>
                                                              <input type="text" name="drv_name" value="{{$waybill->drv_name}}" required/>
                                                          </div>
                              
                                                          <div class="input_div">
                                                              <p>Contact: </p>
                                                              <input type="text" name="drv_contact" value="{{$waybill->drv_contact}}" required/>
                                                          </div>
                              
                                                          <div class="input_div">
                                                              <p>Vehicle Reg. No: </p>
                                                              <input type="text" name="vno" value="{{$waybill->vno}}" required/>
                                                          </div>
                                                        </div>   
                              
                                                      </div>
                                                
                                                      <div class="col-md-6">
                                                        <div style="height:60px"></div>
                              
                                                        <div class="input_div">
                                                            <p>Waybill No.: </p>
                                                            <input type="text" min="0" name="bill_no" value="{{$waybill->bill_no}}" required/>
                                                        </div>
                              
                                                        <div class="input_div">
                                                            <p>Weight of Package: </p>
                                                            <input type="text" name="weight" value="{{$waybill->weight}}"/>
                                                        </div>
                              
                                                        <div class="input_div">
                                                            <p>No. of Pieces: </p>
                                                            <input type="text" name="nop" value="{{$waybill->nop}}"/>
                                                        </div>
                              
                                                        <div class="input_div">
                                                            <p>Total Quantity: </p>
                                                            <input type="text" name="tot_qty" value="{{$waybill->tot_qty}}"/>
                                                        </div>
                              
                                                        <div class="input_div">
                                                            <p>Delivery Date: </p>
                                                            <input type="date" placeholder="DD/MM/YYY" name="del_date" value="{{$waybill->del_date}}"/>
                                                        </div>
                              
                                                        <div class="input_div">
                                                          <p>Status: </p>
                                                          <select name="status">
                                                            <option selected>{{$waybill->status}}</option>
                                                            <option>Pending</option>
                                                            <option>Delivered</option>
                                                          </select>
                                                        </div>
                                                      
                                                      </div>
                                              
                              
                                                    </div>                 

                                                </div>
                                              </div>
                                              
                                              <div class="modal-footer">
                                                <button type="submit" class="btn btn-info" name="store_action" value="update_waybill"><i class="fa fa-save"></i> &nbsp; Update Record</button>
                                              </div>

                                          </div>
                                    
                                        </div>
                                      </div>

                                    </form>                  
                                    
                                  </td>
                                </tr>

                                {{-- <div id="disnone">
                                  @if (($c-1)%2==0)
                                    <tr class="rowColour">
                                  @else
                                    <tr>
                                  @endif
                                    <td></td>
                                    <td><p class="gray_p">1</p></td>
                                    <td><p class="gray_p">Safe Sack</p></td>
                                    <td><p class="gray_p">200</p></td>
                                  </tr>
                                </div> --}}
                              
                              @endif

                            @endforeach

                          </tbody>
                        </table>
                        <p>Total: <b style="color: #000000">{{count($waybills)}}</b></p>

                        {{-- {{ Auth::user()->name }}
                        {{ auth()->user()->email }}

                        @foreach ($ITM as $IT)
                          <p>{{$IT->item_id}} - {{$IT->item->name}}</p>
                        @endforeach

                        @foreach ($waybills as $waybill)
                          <p>{{$waybill->name}} - {{$waybill->itemimage->item_id}}</p>
                        @endforeach --}}

                         {{ $waybills->appends(['waybillsearch' => request()->query('waybillsearch')])->links() }}

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


@endsection