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
      <li class="nav-item active2">
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
            <div class="col-md-7">

              @include('inc.messages')

              <div class="card">
                
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Configuration</h4>
                  <p class="card-category">Set up information about company here..</p>
                </div>
                <div class="card-body">
            
                  <div class="container">
                      <div class="row justify-content-center">
                          <div class="col-md-8">
                            <div class="card-header">Register</div>
                  
                              <div class="card-body">

                                <form action="{{action('ItemsController@store')}}" method="POST" enctype="multipart/form-data">
                                  @csrf

                                  @if (count($company) < 1)

                                    <div class="form-group">
                                      <input type="text" class="form-control" name="name" placeholder="Name of Company" required/>
                                    </div>

                                    <div class="form-group">
                                      <textarea name="company_add" class="form-control" rows="3" placeholder="Address" required></textarea>
                                    </div>

                                    <div class="form-group">
                                      <input type="text" class="form-control" name="loc" placeholder="Location"/>
                                    </div>
                                    
                        
                                    <div class="form-group">
                                      <input type="text" class="form-control" name="contact" placeholder="Contact No." required/>
                                    </div>

                                    <div class="form-group">
                                      <input type="text" class="form-control" name="email" placeholder="Email" />
                                    </div>

                                    <div class="form-group">
                                      <input type="text" class="form-control" name="company_web" placeholder="Website" />
                                    </div>
                                    
                                    <div class="">
                                      <label class="upfiles">Upload Logo: &nbsp; </label>
                                      <input type="file" name="company_logo" required>
                                    </div>
                                  
                                    <div class="modal-footer">
                                      <button type="submit" class="btn btn-info" name="store_action" value="admi_config"><i class="fa fa-save"></i> &nbsp; Save</button>
                                    </div>

                                  @else

                                    @foreach ($company as $comp)
                                        
                                      <div class="form-group">
                                        <input type="text" class="form-control" name="name" placeholder="Name of Company" value="{{ $comp->name }}" required/>
                                      </div>

                                      <div class="form-group">
                                        <textarea name="company_add" class="form-control" rows="3" placeholder="Address" required>{{ $comp->address }}</textarea>
                                      </div>

                                      <div class="form-group">
                                        <input type="text" class="form-control" name="loc" placeholder="Location" value="{{ $comp->location }}"/>
                                      </div>
                                      
                          
                                      <div class="form-group">
                                        <input type="text" class="form-control" name="contact" placeholder="Contact No." value="{{ $comp->contact }}" required/>
                                      </div>

                                      <div class="form-group">
                                        <input type="text" class="form-control" name="email" placeholder="Email" value="{{ $comp->email }}" />
                                      </div>

                                      <div class="form-group">
                                        <input type="text" class="form-control" name="company_web" placeholder="Website" value="{{ $comp->website }}" />
                                      </div>
                                      
                                      <div class="">
                                        <!--label class="upfiles">Upload Logo: &nbsp; </label>
                                        <input type="file" name="company_logo"-->
                                        <input type="hidden" name="company_logo" value="{{$comp->logo}}" />
                                      </div>
                                      
                                      <div class="modal-footer">
                                        <p class="grants">Access Granted: Company Details Already Set</p>
                                        <button type="submit" class="btn btn-primary" name="store_action" value="admi_config"><i class="fa fa-save"></i> &nbsp; Update</button>
                                      </div>

                                    @endforeach

                                  @endif
                                </form>

                              </div>
                                 
                            </div>
                              
                          </div>
                      </div>
                  </div>
              </div>

            </div>

            <div class="col-md-5">
            
                    <div style="height: 30px">
                    </div>

              <div class="card card-profile">
                <div class="card-body">
                  <h4 class="card-title">Add Company Branches</h4>

                  <div class="col-md-10 offset-md-1">
                  <form action="{{action('ItemsController@store')}}" method="POST">
                    @csrf

                    <label for="cat-title" class="col-form-label myLabel">Branch Name:</label>
                    <div class="form-group">
                      <input type="text" class="form-control" name="name" placeholder="eg. RJV Adum Branch." required/>
                    </div>

                    <label for="cat-title" class="col-form-label myLabel">Location</label>
                    <div class="form-group">
                      <input type="text" class="form-control" name="loc" placeholder="eg. Adum" required/>
                    </div>

                    <label for="cat-title" class="col-form-label myLabel">Contact:</label>
                    <div class="form-group">
                      <input type="text" class="form-control" name="contact" required/>
                    </div>
                    @if (count($branches) < 5)
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-info" name="store_action" value="create_branch"><i class="fa fa-save"></i> &nbsp; Save</button>
                      </div>
                    @endif
                  </form>
                  </div>
                   
                </div>
              </div>
      
                    <div style="height: 30px">
                    </div>
              
              <div class="card card-profile">
                <div class="card-body">
                        
                  <div class="form-group inputHold">
                    <button type="submit" class="btn btn-secondary" data-toggle="modal" data-target="#comp_branch"><i class="fa fa-folder-open"></i> &nbsp; View Branches</button>
                    {{-- <button type="submit" class="btn btn-secondary" data-toggle="modal" data-target="#add_items"><i class="fa fa-folder-open"></i> &nbsp; View Additional Items</button> --}}
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