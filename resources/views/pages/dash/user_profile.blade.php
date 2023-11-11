@extends('layouts.dashlay')

@section('sidebar-wrapper')
  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item active2">
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
            <div class="col-md-8">

              @include('inc.messages')

              <div class="card">
                
                <div class="card-body">
            
                  <div class="container">
                      <div class="row justify-content-center">
                          <div class="col-md-10">
                            <img class="profile_img" src="/maindir/image/user3.png" width="200" alt="">
                            <p class="profile_header">Update Login Credentials</p>
                  
                              <div class="card-body">

                                <form action="{{action('ItemsController@update', auth()->user()->id)}}" method="POST" enctype="multipart/form-data">
                                  <input type="hidden" name="_method" value="PUT">
                                  @csrf

                                  <div class="form-group">
                                      <input id="name" placeholder="Name" type="text" class="form-control" name="name" value="{{auth()->user()->name}}" required autofocus>
                                  </div>
                  
                                  <div class="form-group">
                                      <input id="email" placeholder="Email" type="email" class="form-control" name="email" value="{{auth()->user()->email}}" required>
                                  </div>
                  
                                  <div class="form-group">
                                      <input id="password" placeholder="New Password" type="password" class="form-control" name="password" required>
                                  </div>

                                  <div class="form-group">
                                      <input id="password-confirm" placeholder="Confirm New Password" type="password" class="form-control" name="password_confirmation" required>
                                  </div>
                                
                                  <div class="modal-footer">
                                    <button type="submit" class="btn bg5" name="store_action" value="update_user"><i class="fa fa-save"></i> &nbsp; Update</button>
                                  </div>

                                </form>

                              </div>
                                 
                            </div>
                              
                          </div>
                      </div>
                  </div>
              </div>

            </div>

          </div>
        </div>
  </div>



  <div class="modal fade" id="comp_branch" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modtop" role="document">
      <div id="printarea" class="modal-content">
          <div class="card card-profile">
            <div class="card-body">
              <h6 class="card-category text-gray"></h6>
              <div style="height: 30px">
              </div>

              <h3 class="card-title">All Branches</h3>

              @if(count($branches) != 0)

              <table id="config_tbl">
                <thead>
                  <th><h5 class="card-title">Name</h5></th>
                  <th><h5 class="card-title">Location</h5></th>
                  <th><h5 class="card-title">Contact</h5></th>
                </thead>
                <tbody>
                  @foreach ($branches as $branch)

                    <form action="{{ action('ItemsController@destroy', $branch->id) }}" method="POST">
                      <input type="hidden" name="_method" value="DELETE">
                      @csrf

                      <tr>
                        <td>{{$branch->name}}</td>
                        <td>{{$branch->loc}}</td>
                        <td>{{$branch->contact}}
                        <button type="submit" name="del_action" value="branch_del" rel="tooltip" title="Delete Branch" class="close2" onclick="return confirm('Are you sure you want to delete branch?');"><i class="fa fa-close"></i></button>
                        </td>
                      </tr>

                    </form>

                  @endforeach

                </tbody>
              </table>

              @else
              <p>Oops..! No branch registered yet!</p>
              @endif

            </div>
          </div>
      </div>

    </div>
  </div>


@endsection