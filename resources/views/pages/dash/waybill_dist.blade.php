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

                {{-- <div class="form-group row mb-0 hideMe">

                  <div class="col-md-7 offset-md-5 myTrim">
                    <a href="#"><button type="submit" class="btn btn-white pull-right" title="Recycle Bin"><i class="fa fa-trash"></i></button></a>
                    <a href="/waybillview"><button type="submit" class="btn btn-white pull-right" ><i class="fa fa-arrow-left"></i></button></a>
                  </div>

                </div> --}}

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Distribution</h4>
                  <p class="card-category">Add items to waybill and distribute to branches</p>
                </div>
                <div id="printarea1" class="card-body">

                  <div class="row">
                    <div class="col-md-10 gen_form_cont">
                      <form action="{{ action('ItemsController@store') }}" method="POST">
                        @csrf
                        <select class="gen_form" name="item">
                          @foreach ($items as $item)
                            <option value="{{$item->id}}">{{$item->item_no.' - '.$item->name}}</option>
                          @endforeach
                        </select>
                        <input type="hidden" name="wb_id" value="{{$wb_id}}">
                        <input class="gen_form" type="number" name="qty" placeholder="Qty." min="0" required>
                        <button class="subbtn" type="submit" name="store_action" value="add_wbcontent"><i class="fa fa-plus-circle"></i>&nbsp; Add</button>
                      </form>
                    </div>
                  </div>
            
                    @if (count($wbcontents) > 0)
                      <table class="table mt">
                        <thead class=" text-secondary hideMe">
                          <th>#</th>
                          <th>Waybill</th>
                          <th>Item</th>
                          <th>Qty.</th>
                          <th>Rem.</th>
                          <th>Date Added</th>
                          <th class="ryt actsize">Actions</th>
                        </thead>
                        <tbody id="tb">

                          @foreach ($wbcontents as $wbc)

                            @if ($wbc->del == 'no')
                              
                              @if ($c%2==0)
                                <tr class="rowColour">
                              @else
                                <tr>
                              @endif
                                <td>{{$c++}}</td>
                                <td>{{$wbc->waybill->bill_no}}<br><p class="gray_p">{{$wbc->waybill->comp_name}}</p></td>
                                <td>{{$wbc->item->item_no.' - '.$wbc->item->name}}<br><p class="small_p">{{$wbc->item->brand}}</p></td>
                                <td>{{$wbc->qty}}</td>
                                <td>{{$wbc->qty - $wbc->qty_dist}}</td>
                                <td>{{date('M. d, Y', strtotime($wbc->created_at))}}</td>

                                <td class="ryt">
                                  
                                  <form action="{{ action('ItemsController@update', $wbc->id) }}" method="POST">
                                    <input type="hidden" name="_method" value="PUT">
                                    @csrf

                                    <a href="" class="my_trash green color10" data-toggle="modal" rel="tooltip" title="Edit Record" data-target="#edit_{{$wbc->id}}"><i class="fa fa-pencil"></i></a>
                                    <button name="store_action" value="del_wbcontent" rel="tooltip" title="Delete Waybil" class="icon_btn color6" title="Distribute" onclick="return confirm('Are you sure you want to delete record?');"><i class="fa fa-trash"></i></button>
                                    

                                    <div class="modal fade" id="edit_{{$wbc->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                      <div class="modal-dialog modtop" role="document">
                                        <div class="modal-content">
                                            
                                            <div class="card card-profile">
                                              <div class="card-body">
                                                
                                                <div class="row justify-content-center">
                                                    <div class="col-md-11 cl">
                                                      <div style="height:30px"></div>
                                                      
                                                      <p>Alter Distributed Qty. Here</p>
                                                      <div class="my_panel">
                                                        <div class="input_div">
                                                            <p>Quantity </p>
                                                            <input type="number" name="qty" min="0" value="{{$wbc->qty}}" required/>
                                                        </div>
                                                    </div>
                                                  </div>                 

                                              </div>
                                            </div>
                                            
                                            <div class="modal-footer">
                                              <button type="submit" class="btn btn-info" name="store_action" value="up_wbcontent"><i class="fa fa-save"></i> &nbsp; Update Record</button>
                                            </div>

                                        </div>
                                  
                                      </div>
                                    </div>

                                  </form>                  
                                  
                                </td>
                              </tr>
                            
                            @endif

                          @endforeach

                        </tbody>
                      </table>
                      {{-- <p>Total: <b style="color: #000000">{{count($wbcontents)}}</b></p> --}}

                      <div style="height: 30px">
                      </div>

                      {{-- <div class="col-md-10 gen_form_cont">
                        <p class="small_p">Distribute to branches here</p>
                        <form action="{{ action('ItemsController@store') }}" method="POST">
                          @csrf
                          <select class="gen_form" name="item">
                            @foreach ($branches as $br)
                              <option value="{{$br->id}}">{{$br->name}}</option>
                            @endforeach
                          </select>
                          <input type="hidden" name="wb_id" value="{{$wb_id}}">
                          <input class="gen_form" type="number" name="qty" placeholder="Qty." min="0" ondblclick="return this.readOnly='';">
                          <button class="subbtn" type="submit" name="store_action" value="add_wbcontent"><i class="fa fa-plus-circle"></i>&nbsp; Add</button>
                        </form>
                      </div> --}}

                      <a href="/distreport" class="my_trash_btn2 green color10" rel="tooltip"><i class="fa fa-share-alt"></i>&nbsp;View Distribution History</a>
                      <p class="small_p">&nbsp;</p>  
                      <p class="small_p"><i class="fa fa-warning"></i> DEFINE QUANTITIES TO DISTRIBUTE TO BRANCHES HERE</p>

                      {{-- <form action="{{ action('ItemsController@store') }}" method="POST"> --}}
                        @csrf
                        @if (count($wbcontents) > 0)
                          <table class="table mt">
                            <thead class=" text-secondary hideMe">
                              <th>#</th>
                              <th>Items</th>
                              @foreach ($branches as $br)
                                <th class="ryt">BR.</th>
                                <th>{{$br->tag}}</th>
                              @endforeach
                              <th class="ryt actsize">Actions
                                <!--button type="submit" name="store_action" value="update_allwbc" 
                                  class="my_trash_btn green color10" rel="tooltip" 
                                  onclick="return confirm('Are you sure you want to update record?');">
                                  <i class="fa fa-check-square"></i>&nbsp;Update All
                                </button-->
                              </th>
                            </thead>
                            <tbody id="tb">

                                  <tr>
                                    <td></td>
                                    <td></td>
                                    @foreach ($branches as $br)
                                      <td class="ryt avl">AVL</td>
                                      <td class="added">ADD</td>
                                    @endforeach
                                    <td></td>
                                  </tr>

                              @foreach ($wbcontents as $wbc)
                          
                                @if ($wbc->del == 'no')
                                  
                                <form action="{{ action('ItemsController@update', $wbc->id) }}" method="POST">
                                  <input type="hidden" name="_method" value="PUT">
                                  @csrf
                                  <tr>
                                    <td>{{$x++}}</td>
                                    <td>{{$wbc->item->item_no.' - '.$wbc->item->name}}</td>
                                    <input type="hidden" name="tvalue" value="{{$t++}}">
                                    @for ($i = 0; $i < count($branches); $i++)
                                      <input type="hidden" value="{{$val = 'q'.$i+1}}">
                                      <td class="ryt avl">{{$cur_qtys[$x-2]->$val}}</td>
                                      {{-- @if (count($dist_qtys) > 0)
                                        @foreach ($dist_qtys as $dist)
                                            @if ($wbc->item_id == $dist->item_id)      
                                              <input type="hidden" value="{{$wbd = $dist->$val}}">
                                            @endif
                                        @endforeach
                                      @endif --}}
                                      <td><input class="fig_txtbox" type="number" min="0" name="{{$val.$wbc->item_id}}" placeholder="0" required></td>
                                    @endfor
                                    <td class="ryt">
                                      <button type="submit" name="store_action" value="up_wbdist" class="my_trash_btn bg3 color10" onclick="return confirm('Are you sure you want to update record?');"><i class="fa fa-check-square"></i>&nbsp;Update</button>
                                    </td>
                                  </tr>
                                </form>

                                @endif

                              @endforeach

                            </tbody>
                          </table>
                        @endif
                      {{-- </form> --}}                    
                    @else
                      <div class="alert alert-danger">
                        No item found on this waybill
                      </div>
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