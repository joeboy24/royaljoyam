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
            <div class="col-md-10">

              @include('inc.messages')

              {{-- <form action="{{action('PostsController@store')}}" method="POST">
                @csrf --}}

                <div class="form-group row mb-0">
                  <div class="col-md-3 offset-md-0">
                    <a href="/dashuser"><button type="submit" class="btn btn-primary"><i class="fa fa-shopping-basket"></i> &nbsp; Stock / Inventory</button></a>
                  </div>

                  <div class="col-md-9 offset-md-0">
                      <a href="/waybillview"><button type="submit" class="btn btn-info pull-right"><i class="fa fa-table"></i> &nbsp; View Waybills</button></a>
                  </div>
                </div>

              {{-- </form> --}}

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Waybill</h4>
                  <p class="card-category">Complete waybill info. here..</p>
                </div>
                <div class="card-body">
                  <form action="{{action('ItemsController@store')}}" method="POST">
                            @csrf
                    <div class="container">
                      <div class="row justify-content-center">

                          

                      {{-- <div class="card-body"> --}}

                        <div class="col-md-5 cl">
                          <div style="height:30px"></div>
                          
                          <p>Sender Info. / From:</p>
                          <div class="my_panel">
                            <div class="input_div">
                                <p>Company Name: </p>
                                <input type="text" class="" name="comp_name" required/>
                            </div>

                            <div class="input_div">
                                <p>Address: </p>
                                <textarea class="" name="comp_add" rows="4" required></textarea>
                            </div>

                            <div class="input_div">
                                <p>Contact: </p>
                                <input type="text" class="" name="comp_contact" required/>
                            </div>
                          </div>

                          <p>Dispatch Driver</p>
                          <div class="my_panel">
                            <div class="input_div">
                                <p>Driver's Name: </p>
                                <input type="text" class="" name="drv_name" required/>
                            </div>

                            <div class="input_div">
                                <p>Contact: </p>
                                <input type="text" class="" name="drv_contact" required/>
                            </div>

                            <div class="input_div">
                                <p>Vehicle Reg. No: </p>
                                <input type="text" class="" name="vno" required/>
                            </div>
                          </div>   

                        </div>
                  
                        <div class="col-md-5">
                          <div style="height:60px"></div>

                          <div class="input_div">
                              <p>Waybill No.: </p>
                              <input type="text" min="0" class="" name="bill_no" required/>
                          </div>

                          <div class="input_div">
                              <p>Weight of Package: </p>
                              <input type="text" class="" name="weight"/>
                          </div>

                          <div class="input_div">
                              <p>No. of Pieces: </p>
                              <input type="text" class="" name="nop"/>
                          </div>

                          <div class="input_div">
                              <p>Total Quantity: </p>
                              <input type="text" class="" name="tot_qty"/>
                          </div>

                          <div class="input_div">
                              <p>Delivery Date: </p>
                              <input type="date" class="" placeholder="DD/MM/YYY" name="del_date"/>
                          </div>

                          <div class="input_div">
                            <p>Status: </p>
                            <select name="status">
                                <option>Pending</option>
                                <option>Delivered</option>
                            </select>
                          </div>
                        
                          <div class="modal-footer">
                            <button type="submit" class="btn btn-info" name="store_action" value="add_waybill"><i class="fa fa-save"></i> &nbsp; Save Bill</button>
                          </div>

                        </div>
                
                      {{-- </div> --}}

                      </div>
                    </div>
                  </form>
                  <div style="height:30px"></div>
                </div>
              </div>

            </div>


                          
            {{-- <div class="col-md-5">
              <div class="card card-profile">
                <div class="card-body myScroll">
                  <h4 class="card-title">All Registered Categories</h4>

                          @if (count($category) > 0)
                            <table class="newtable1">
                              <thead class="text-secondary">
                                <th class="text_left">Category / Description</th>
                                <th class="ryt">
                                  Action
                                </th>
                              </thead>
                              <tbody>


                              @foreach ($category as $cat)
                                  <tr>
                                    <td>{{$i++}} &nbsp; <b class="myb">{{$cat->name}}</b><br>{{$cat->desc}}</td>
                                    <td class="ryt">
                                      <form action="{{ action('ItemsController@destroy', $cat->id) }}" method="POST">

                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                        <button type="submit" name="del_action" value="cat_del" rel="tooltip" title="Delete Category" class="close2" onclick="return confirm('Are you sure you want to delete this Category?');"><i class="fa fa-close"></i></button>
                                    
                                      </form>
                                    </td>
                                  </tr>
                              @endforeach

                              </tbody>
                            </table>

                            @else
                              <p>No Category Added Yet</p>
                            @endif

                </div>
              </div>

                  <div style="height: 30px">
                  </div>

              <div class="card card-profile">
                <div class="card-body">
                  
                  <div class="form-group inputHold">
                    <a href="/items"><button type="submit" class="btn btn-secondary" data-toggle="modal" data-target="#view_items"><i class="fa fa-folder-open"></i> &nbsp; View All Items</button></a>
                    <button type="submit" class="btn btn-secondary" data-toggle="modal" data-target="#view_users"><i class="fa fa-folder-open"></i> &nbsp; View All Users</button>
                  </div>
                  
                </div>
              </div>

            </div> --}}


          </div>
        </div>
  </div>



  <div class="modal fade" id="usrModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp; Register User Here</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          
          <form action="{{action('ItemsController@store')}}" method="POST">
              @csrf

              <div class="form-group row">
                  <div class="col-md-12">
                      <input id="name" placeholder="Name" type="text" class="form-control" name="name" required autofocus>
                  </div>
              </div>

              <div class="form-group row">
                  <div class="col-md-12">
                      <input id="email" placeholder="Email" type="email" class="form-control" name="email" required>
                  </div>
              </div>

              <div class="form-group row">
                <div class="col-md-12">
                  <select name="status" class="form-control" id="assign_tch" required>
                    <option selected>User</option>
                    <option>Administrator</option>
                  </select>
                </div>
              </div>

              <div class="form-group row">
                  <div class="col-md-12">
                      <input id="password" placeholder="Password" type="password" class="form-control" name="password" required>
                  </div>
              </div>

              <div class="form-group row">
                  <div class="col-md-12">
                      <input id="password-confirm" placeholder="Confirm Password" type="password" class="form-control" name="password_confirmation" required>
                  </div>
              </div>

              <div class="form-group row mb-0">
                  <div class="col-md-6 offset-md-4">
                      <button type="submit" class="btn btn-info" name="store_action" value="create_user"><i class="fa fa-save"></i> &nbsp; Add User</button>
                  </div>
              </div>
          </form>

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="catModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp; Add Category Here</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          
          <form action="{{action('ItemsController@store')}}" method="POST">
              @csrf

              <div class="form-group row">
                  <div class="col-md-12">
                      <input id="name" placeholder="Name" type="text" class="form-control" name="name" required autofocus>
                  </div>
              </div>

              <div class="form-group row">
                <div class="col-md-12">
                  <textarea name="desc" class="form-control" rows="2" placeholder="Category Description"></textarea>
                </div>
              </div>

              <div class="form-group row mb-0">
                  <div class="col-md-6 offset-md-4">
                      <button type="submit" class="btn btn-info" name="store_action" value="add_cat"><i class="fa fa-save"></i> &nbsp; Add Category</button>
                  </div>
              </div>
          </form>

        </div>
      </div>
    </div>
  </div>

  {{-- <div class="modal fade" id="view_users" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modtop" role="document">
      <div id="printarea" class="modal-content">
          <div class="card card-profile">
            <div class="card-avatar">
              <a href="#">
              <!--img class="img" src="/storage/members_imgs/{{$fee->student->photo}}" /-->
              </a>
            </div>
            <div class="card-body">
              <h6 class="card-category text-gray"></h6>
              <div style="height: 30px">
              </div>

              <h3 class="card-title">All Users</h3>

              @if(count($users) != 0)

              <table id="config_tbl">
                <thead>
                  <th><h5 class="card-title">Username</h5></th>
                  <th><h5 class="card-title">Email</h5></th>
                  <th><h5 class="card-title">Status</h5></th>
                </thead>
                <tbody>
                  @foreach ($users as $user)

                    <form action="{{ action('ItemsController@destroy', $user->id) }}" method="POST">

                      <input type="hidden" name="_method" value="DELETE">
                      @csrf

                      <tr>
                        <td>{{$user->name}}</td>
                        <td>{{$user->email}}</td>
                        <td>{{$user->status}}
                        <button type="submit" name="del_action" value="usr_del" rel="tooltip" title="Delete User" class="close2" onclick="return confirm('Are you sure you want to delete user?');"><i class="fa fa-close"></i></button>
                        </td>
                      </tr>

                    </form>

                  @endforeach

                </tbody>
              </table>

              @else
              <p>Oops..! No user registered yet!</p>
              @endif

            </div>
          </div>
      </div>

    </div>
  </div> --}}

@endsection