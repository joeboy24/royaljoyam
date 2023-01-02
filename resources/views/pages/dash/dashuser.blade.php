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
      <li class="nav-item active2">
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

              {{-- <form action="{{action('PostsController@store')}}" method="POST">
                @csrf --}}

                <div class="form-group row mb-0">
                  <div class="col-md-3 offset-md-0">
                      <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#usrModal"><i class="fa fa-users"></i> &nbsp; Register User</button>
                  </div>

                  <div class="col-md-9 offset-md-0">
                      <button type="submit" class="btn btn-info pull-right" data-toggle="modal" data-target="#catModal"><i class="fa fa-users"></i> &nbsp; Add Category</button>
                  </div>
                </div>

              {{-- </form> --}}

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Add Stock Item</h4>
                  <p class="card-category">Complete item profile here..</p>
                </div>
                <div class="card-body">
            
                  <div class="container">
                      <div class="row justify-content-center">
                          <div class="col-md-8">
                              
                                  <div class="card-header">Input Details</div>
                  
                                  <div class="card-body">

                                    <form action="{{action('ItemsController@store')}}" method="POST" enctype="multipart/form-data">
                                      @csrf
                                      <div class="form-group">
                                        <input type="text" class="form-control" name="name" placeholder="Item Name" required/>
                                      </div>
    
                                      <div class="form-group">
                                        <textarea name="desc" class="form-control" rows="3" placeholder="Item Description" required></textarea>
                                      </div>

                                      <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Choose Category</label>
                                        <select name="cat" class="form-control" id="assign_tch" required>
                                          @if(count($category) > 0)
                                            @foreach ($category as $cat)
                                              <option>{{$cat->name}}</option>
                                            @endforeach
                                          @endif
                                        </select>
                                      </div>

                                      <div class="form-group">
                                        <input type="text" class="form-control" name="brand" placeholder="Brand / Manufacturer"/>
                                      </div>
    
                                      <div class="form-group">
                                        <input type="text" class="form-control" name="barcode" placeholder="Barcode"/>
                                      </div>
                          
                                      <div class="form-group">
                                        <input type="number" class="form-control" min="0" name="qty" placeholder="Quantity" required/>
                                      </div>

                                      <!--div class="form-group">
                                        <input type="text" class="form-control" name="cost_price" placeholder="Cost Price" required/>
                                      </div-->
    
                                      <div class="form-group">
                                        <input type="number" class="form-control" min="0c" name="price" placeholder="Cost Price" required/>
                                      </div>
                                      
                                      <div class="">
                                        <label class="upfiles">Upload Image(s): &nbsp; </label>
                                        <input type="file" name="items[]" multiple>
                                      </div>
                                      
                                      <div class="modal-footer">
                                        <button type="submit" class="btn btn-info" name="store_action" value="add_item"><i class="fa fa-save"></i> &nbsp; Save</button>
                                      </div>
                                    </form>

                                  </div>
                              
                          </div>
                      </div>
                  </div>
                </div>
              </div>


            </div>


                          
            <div class="col-md-5">
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

                                  {{-- <input type="checkbox" class="checkbox1" id="myCheck{{$i}}" name="" value="{{$course->name}}" onclick="myFunction{{$i}}()"> --}}

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
                  <h4 class="card-title">Branch Pricing / Quantity</h4>
                  <p class="card-category">Select to set up branch prices & quantity for items here</p>

                    <ul class="nav nav_radio">
                        <li>
                            <label><input id="radioRegister" onclick="showPrice()" name="choice" type="radio"> Pricing</label>
                        </li>
                        <li>
                            <label><input id="radioGuest" onclick="showQuantity()" name="choice" type="radio"> Quantity</label>
                        </li>
                    </ul>

                  <div id="price_div" class="col-md-12">
                    <form action="{{action('ItemsController@store')}}" method="POST">
                      @csrf
        
                        <div class="my_panel">

                          <div class="dropdown">
                            <div class="input_div">
                              <h4>Manage Pricing</h4>
                              <p>Item Name: </p>
                              <input type="text" name="comp_name" placeholder="Search Item..." id="myInput" onkeyup="filterFunction()" required/>
                            </div>
                              @if (count($items) > 0)

                              {{-- <input id="b1" type="hidden" value="ererwe"/> --}}
                                <div id="myDropdown" class="dropdown_content" onselect="myFunction()">
                                  @foreach ($items as $item)
                                    
                                    <a id="selItem{{$item->id}}" onclick="selFunction{{$item->id}}()">{{$item->name}}</a>
                                    <input id="b{{$item->id}}" type="hidden" value="{{$item->b1}}"/>
                                    <input id="c{{$item->id}}" type="hidden" value="{{$item->b2}}"/>
                                    <input id="d{{$item->id}}" type="hidden" value="{{$item->b3}}"/>

                                    <script>
                                      
                                      function myFunction() {
                                        var drp = document.getElementById("myDropdown");
                                        drp.style.display = "none";
                                      }
                                      
                                      function selFunction{{$item->id}}() {
                                        var selItem = document.getElementById("selItem{{$item->id}}");
                                        document.getElementById("myInput").value = selItem.innerHTML;
                                        document.getElementById("myDropdown").style.display = "none";

                                        document.getElementById("bb1").value = document.getElementById("b{{$item->id}}").value
                                        document.getElementById("bb2").value = document.getElementById("c{{$item->id}}").value
                                        document.getElementById("bb3").value = document.getElementById("d{{$item->id}}").value
                                        document.getElementById("id").value = "{{$item->id}}";
                                      }
                                      
                                      function filterFunction() {
                                      
                                        document.getElementById("myDropdown").style.display = "block";
                                        
                                        var input, filter, ul, li, a, i;
                                        input = document.getElementById("myInput");
                                        filter = input.value.toUpperCase();
                                        div = document.getElementById("myDropdown");
                                        a = div.getElementsByTagName("a");
                                        for (i = 0; i < a.length; i++) {
                                          txtValue = a[i].textContent || a[i].innerText;
                                          if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                            a[i].style.display = "";
                                          } else {
                                            a[i].style.display = "none";
                                          }
                                        }
                                      }

                                    </script>

                                  @endforeach
                                </div>
                                  {{-- {{$item->name}} --}}
                              @endif
                              
                          </div>
                          

                          <div class="input_div">
                              <p>Branch 1: Price</p>
                              <input id="bb1" min="0" type="number" name="b1"/>
                              <input id="id" type="hidden" name="id"/>
                          </div>

                          <div class="input_div">
                              <p>Branch 2: Price</p>
                              <input id="bb2" min="0" type="number" name="b2"/>
                          </div>

                          <div class="input_div">
                              <p>Branch 3: Price</p>
                              <input id="bb3" min="0" type="number" name="b3"/>
                          </div>

                        </div>
                            
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-info" name="store_action" value="update_branch"><i class="fa fa-save"></i> &nbsp; Update</button>
                        </div>
                    </form>
                  </div>


                  <div id="quantity_div" class="col-md-12">
                    <form action="{{action('ItemsController@store')}}" method="POST">
                      @csrf
        
                        <div class="my_panel">

                          <div class="dropdown">
                            <div class="input_div2">
                              <h4>Manage Quantity</h4>
                              <p>Item Name: </p>
                              <input type="text" name="comp_name2" placeholder="Search Item..." id="myInput2" onkeyup="filterFunction2()" required/>
                            </div>
                              @if (count($items) > 0)

                              {{-- <input id="b1" type="hidden" value="ererwe"/> --}}
                                <div id="myDropdown2" class="dropdown_content2" onselect="myFunction2()">
                                  @foreach ($items as $item)
                                    
                                    <a id="selItem2{{$item->id}}" onclick="selFunction2{{$item->id}}()">{{$item->name}}</a>
                                    <input id="x{{$item->id}}" type="hidden" value="{{$item->q1}}"/>
                                    <input id="y{{$item->id}}" type="hidden" value="{{$item->q2}}"/>
                                    <input id="z{{$item->id}}" type="hidden" value="{{$item->q3}}"/>

                                    <script>
                                      
                                      function myFunction2() {
                                        var drp = document.getElementById("myDropdown2");
                                        drp.style.display = "none";
                                      }
                                      
                                      function selFunction2{{$item->id}}() {
                                        var selItem = document.getElementById("selItem2{{$item->id}}");
                                        document.getElementById("myInput2").value = selItem.innerHTML;
                                        document.getElementById("myDropdown2").style.display = "none";

                                        document.getElementById("xx1").value = document.getElementById("x{{$item->id}}").value
                                        document.getElementById("yy2").value = document.getElementById("y{{$item->id}}").value
                                        document.getElementById("zz3").value = document.getElementById("z{{$item->id}}").value
                                        document.getElementById("id2").value = "{{$item->id}}";
                                      }
                                      
                                      function filterFunction2() {
                                      
                                        document.getElementById("myDropdown2").style.display = "block";
                                        
                                        var input, filter, ul, li, a, i;
                                        input = document.getElementById("myInput2");
                                        filter = input.value.toUpperCase();
                                        div = document.getElementById("myDropdown2");
                                        a = div.getElementsByTagName("a");
                                        for (i = 0; i < a.length; i++) {
                                          txtValue = a[i].textContent || a[i].innerText;
                                          if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                            a[i].style.display = "";
                                          } else {
                                            a[i].style.display = "none";
                                          }
                                        }
                                      }

                                    </script>

                                  @endforeach
                                </div>
                                  {{-- {{$item->name}} --}}
                              @endif
                              
                          </div>
                          

                          <div class="input_div">
                              <p>Branch 1: Qty.</p>
                              <input id="xx1" min="0" type="number" name="x1"/>
                              <input id="id2" type="hidden" name="id2" value="27"/>
                          </div>

                          <div class="input_div">
                              <p>Branch 2: Qty.</p>
                              <input id="yy2" min="0" type="number" name="y2"/>
                          </div>

                          <div class="input_div">
                              <p>Branch 3: Qty.</p>
                              <input id="zz3" min="0" type="number" name="z3"/>
                          </div>

                        </div>
                            
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary" name="store_action" value="update_branch_qty"><i class="fa fa-save"></i> &nbsp; Update</button>
                        </div>
                    </form>
                  </div>
                        
                </div>
              </div>

              <div style="height: 30px">
              </div>

              <div class="card card-profile">
                <div class="card-body">
                  
                  <div class="form-group inputHold">
                    <a href="/items"><button type="submit" class="btn btn-secondary" data-toggle="modal" data-target="#view_items"><i class="fa fa-folder-open"></i> &nbsp; Stock View</button></a>
                    <button type="submit" class="btn btn-secondary" data-toggle="modal" data-target="#view_users"><i class="fa fa-folder-open"></i> &nbsp; View All Users</button>
                  </div>
                  
                </div>
              </div>

            </div>

            
            {{-- 
            <div class="card-body col-md-0">
              <h4 class="card-title">All Registered Teachers</h4>

              @if (count($teachers) > 0)
                        <table class="table">
                          <thead class=" text-secondary">
                            <th></th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Role</th>
                            <th>Contact</th>
                            <th class="ryt">
                              Action
                            </th>
                          </thead>
                          <tbody>
                          @foreach ($teachers as $teacher)
                              <tr><td>{{$t++}}</td>
                                <td>{{$teacher->tch_id}}</td>
                                <td>{{$teacher->fname.' '.$teacher->sname}}</td>
                                <td>{{$teacher->dob}}</td>
                                <td>{{$teacher->sex}}</td>
                                <td>{{$teacher->role.' ('.$teacher->role_desc.')'}}</td>
                                <td>{{$teacher->contact}}</td>
                                <td class="ryt">
                                  <form action="{{ action('StudentController@destroy', $teacher->id) }}" method="POST">
                                    <input type="hidden" name="_method" value="DELETE">
                                    @csrf
                                    <button type="submit" name="sub_action" value="trs_del" rel="tooltip" title="Delete User" class="close2" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fa fa-close"></i></button>
                                  </form>
                                </td>
                              </tr>
                          @endforeach
                          </tbody>
                        </table>
               @else
                  <p>No Staff Registered Yet</p>
               @endif

            
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
              <label class="col-form-label myLabel" style="margin-left:20px">User Type / Branch Name</label>
                <div class="col-md-12">
                  <select name="status" class="form-control" id="assign_tch" required>
                    <option>Administrator</option>
                    @if (count($branches) > 0)
                      @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option> 
                      @endforeach
                    @endif
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

  <div class="modal fade" id="view_users" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modtop" role="document">
      <div id="printarea" class="modal-content">
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

                    @if ($user->created_at != '')
                      <form action="{{ action('ItemsController@destroy', $user->id) }}" method="POST">

                        <input type="hidden" name="_method" value="DELETE">
                        @csrf

                        @if ($user->del != 'yes')
                          <tr>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->status}}
                            <button type="submit" name="del_action" value="usr_del" rel="tooltip" title="Delete User" class="close2" onclick="return confirm('Are you sure you want to delete user?');"><i class="fa fa-close"></i></button>
                            </td>
                          </tr>
                        @endif

                      </form>
                    @endif

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
  </div>

  <script>

    function showPrice(){
        var x = document.getElementById('price_div');
        x.style.display = "block";
        var y = document.getElementById('quantity_div');
        y.style.display = "none";

    }

    function showQuantity(){
        var x = document.getElementById('quantity_div');
        x.style.display = "block";
        var y = document.getElementById('price_div');
        y.style.display = "none";

    }
    

</script>

@endsection