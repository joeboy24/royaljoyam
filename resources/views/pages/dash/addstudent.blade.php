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
      <li class="nav-item ">
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
            <div class="col-md-7">

              @include('inc.messages')

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Add Student</h4>
                  <p class="card-category">Complete student profile here..</p>
                </div>
                <div class="card-body">
            
                  <div class="container">
                      <div class="row justify-content-center">
                          <div class="col-md-8">
                            <div class="card-header">Register</div>
                  
                              <div class="card-body">

                                <form action="{{action('StudentController@store')}}" method="POST" enctype="multipart/form-data">
                                  @csrf
                                  <div class="form-group">
                                    <!--label for="cat-title" class="col-form-label">Title:</label-->
                                    <input type="text" class="form-control" name="fname" placeholder="Firstname" required/>
                                  </div>
                                  <div class="form-group">
                                    <input type="text" class="form-control" name="sname" placeholder="Other names" required/>
                                  </div>
                                  
                                  <label for="" class="col-form-label smalllable">Date of Birth: </label>
                                  <div class="form-group">
                                  <input type="date" class="form-control" name="dob" placeholder="dd-mm-YYYY"/>
                                  </div>
                                  <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Gender:</label>
                                    <select name="sex" class="form-control" id="sex">
                                      <option>Male</option>
                                      <option>Female</option>
                                    </select>
                    
                                  </div>
                      
                                  <div class="form-group">
                                    <label for="recipient-name" class="col-form-label">Class:</label>
                                    <select name="std_cls" class="form-control" id="std_cls">
                                      @if(count($stages) > 0)
                                        @foreach ($stages as $stage)
                                          @if($stage->del != 'yes')
                                            <option>{{$stage->cls_name}}</option>
                                          @endif
                                        @endforeach
                                      @endif
                                    </select>
                                  </div>

                                  <div class="form-group">
                                    <input type="text" class="form-control" name="guardian" placeholder="Guardian's Full Name" required/>
                                  </div>
                      
                                  <div class="form-group">
                                    <input type="text" class="form-control" name="contact" placeholder="Contact No." required/>
                                  </div>
                                  <div class="form-group">
                                    <input type="email" class="form-control" name="email" placeholder="Email" />
                                  </div>
                                  
                                  <div class="form-group">
                                    <input type="text" class="form-control" name="residence" placeholder="Place of Recidence"/>
                                  </div>
                      
                                  <div class="">
                                    <label class="upfiles">Upload Photo: &nbsp; </label>
                                    <input type="file" name="std_img">
                                  </div>

                                  <div class="form-group">
                                    <input id="bill_total" type="text" class="form-control selected_courses myReadonly" name="bill_total" placeholder="Bill Total: Gh₵ 0" readonly required/>
                                  </div>
                                  
                                  <div class="modal-footer">
                                    <button type="submit" class="btn btn-info" name="store_action" value="admi_create_std"><i class="fa fa-save"></i> &nbsp; Save</button>
                                  </div>
                                </form>

                                {{-- <a href="/dashboard"><button type="submit" class="btn btn-info" name="store_action" value="admi_create_std"><i class="fa fa-save"></i> &nbsp; Un Save</button></a>
                                   --}}

                              </div>
                                 
                            </div>
                              
                          </div>
                      </div>
                  </div>
                </div>
            </div>

            
            <div class="col-md-5">
              <div class="card card-profile">
                <div class="card-body">
                  <h4 class="card-title">Add Students From Excel File</h4>

                  <form action="{{action('ImportsController@store')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="col-md-10 offset-md-1">
      
                      <div class="">
                        <label class="upfiles">Upload File Here: &nbsp; </label>
                        <input type="file" name="import_file" required>
                      </div>
                      
                      <p>Click <a href="/download">Here</a> to download excel file format</p>

                      <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="store_action" value="import_std"><i class="fa fa-save"></i> &nbsp; Upload File</button>
                      </div>

                    </div>

                  </form>
                   
                </div>
              </div>

                  <div style="height: 30px">
                  </div>

              <div class="card card-profile">
                <div class="card-body">
                  <h4 class="card-title">Add Items To Bill</h4>

                          @if (count($payables) > 0)
                            <table class="table">
                              <thead class="text-secondary">
                                <th></th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Cost</th>
                                <th class="ryt">
                                  Action
                                </th>
                              </thead>
                              <tbody>
 
                                <script>var hold = ''; var hold2 = parseInt(0);</script>

                              @foreach ($payables as $payable)
                                  <tr><td>{{$i++}}</td>
                                    <td>{{$payable->name}}</td>
                                    <td>{{$payable->desc}}</td>
                                    <td>{{$payable->cost}}</td>
                                    <td class="ryt">
                                      <form action="{{ action('StudentController@destroy', $payable->id) }}" method="POST">

                                        <input type="checkbox" class="checkbox1" id="myCheck{{$i}}" name="" value="{{$payable->cost}}" onclick="myFunction{{$i}}()">
                                        <input type="text" style="display: none" id="myCheck2{{$i}}" value="{{$payable->name}}">

                                        <input type="hidden" name="_method" value="DELETE">
                                        @csrf
                                        <button type="submit" name="sub_action" value="crs_del" rel="tooltip" title="Delete Course" class="close2" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-close"></i></button>
                                      
                                        <p id="text{{$i}}" style="display:none">Checked{{$i}}</p>
                                        <p id="demo{{$i}}"></p>

                                        <script>
                                          function myFunction{{$i}}(){
                                            var checkBox = document.getElementById('myCheck{{$i}}')
                                            var BoxValue = document.getElementById('myCheck{{$i}}').value;
                                            var BoxValue2 = document.getElementById('myCheck2{{$i}}').value;
                                            var text = document.getElementById('text{{$i}}')
                                            var txt_input = document.getElementById('txt_input')

                                            var bill_total = document.getElementById('bill_total')


                                            if (checkBox.checked == true){

                                              hold2 = hold2 + parseInt(BoxValue);
                                              bill_total.value = hold2;


                                              hold = hold + ',' + BoxValue2;
                                              var getR = txt_input.value;
                                              if (getR == ''){
                                                txt_input.value = BoxValue2 + ',';
                                              }else{
                                                txt_input.value = getR + BoxValue2 + ',';
                                              }

                                            }else{

                                              hold2 = hold2 - BoxValue;
                                              bill_total.value = hold2;

                                              //text.style.display = 'none';

                                              BoxValue2 = BoxValue2 + ',';
                                              var getR =  document.getElementById('txt_input').value;
                                              getR = getR.replace(BoxValue2, '');
                                              txt_input.value = getR;

                                            }

                                          }
                                        </script>
                                      
                                      </form>
                                    </td>
                                  </tr>
                              @endforeach
                              </tbody>
                            </table>

                            <p id="receiver" style="display:none">A</p>
                            <input id="txt_input" type="text" class="form-control selected_courses myReadonly" name="name" placeholder="Selected items will be added to bill" readonly required/>
                            
                          @else
                            <p>No Items Registered</p>
                          @endif

                </div>
              </div>

                  <div style="height: 30px">
                  </div>

                  <div class="modal-footer">
                    <a href="/students"><button type="submit" class="btn btn-info" name="store_action" value="admi_upload_std"><i class="fa fa-folder-open"></i> &nbsp; All Students</button></a>
                  </div>

              <div class="form-group row mb-0">
                <div class="col-md-3 offset-md-0">
                </div>

                {{-- <div class="col-md-9 offset-md-0">
                    <button type="submit" class="btn btn-info pull-right" name="store_action" value="create_class"  data-toggle="modal" data-target="#clsModal" onclick="getFunction()"><i class="fa fa-plus-circle"></i> &nbsp; Assign Subjects to Class</button>
                </div> --}}

                <script>
                  function getFunction(){
                      var textarea = document.getElementById('txt_area')
                      var getData =  document.getElementById('txt_input').value;
                      textarea.value = getData;
                    }
                </script>

              </div>

            </div>

          </div>
        </div>
  </div>


  <div class="modal fade" id="clsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp; Confirm Class & Course Registeration</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div class='col-md-10 offset-md-1'>
          <form action="{{action('StudentController@store')}}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- <div class="form-group">
              <!--label for="cat-title" class="col-form-label">Title:</label-->
              <label class="smalllable">Type Class </label>
              <input type="text" class="form-control" name="name" placeholder="eg. 1, 2 or JHS 3" required/>
            </div> --}}

            <div class="form-group">
             
              <label class="col-form-label">Choose Class</label>
              <select name="cls_name" class="form-control" id="cls_name">
                @if(count($stages) > 0)
                  @foreach ($stages as $stage)
                    @if($stage->del != 'yes')
                      <option>{{$stage->cls_name}}</option>
                    @endif
                  @endforeach
                @endif
              </select>
              
              <label class="col-form-label">Selected Subjects</label>
              <div class="form-group">
                <textarea name="sel_sub" id="txt_area" class="form-control myReadonly" placeholder="Course Description" rows="3" readonly></textarea>
              </div>

              <label class="col-form-label">Class Teacher/Lecturer</label>
              <select name="cls_tch" class="form-control" id="assign_tch">
                <option selected>Not Assigned</option>
                <option>Teacher 1</option>
                <option>Teacher 2</option>
                <option>Teacher 3</option>
              </select>

              {{-- <label for="dept" class="col-form-label">Department:</label>
              <select name="dept" class="form-control" id="dept" required>
                @if(count($deptms) > 0)
                  @foreach ($deptms as $dept)
                    @if($dept->del != 'yes')
                      <option>{{$dept->name}}</option>
                    @endif
                  @endforeach
                @endif
              </select> --}}

            </div>

            {{-- @if (count($courses) > 0)
            <table class="table">
              <thead class="text-secondary">
                <th>ID</th>
                <th>Course Name</th>
                <th class="ryt">
                  Action
                </th>
              </thead>
              <tbody>
              @foreach ($courses as $course)
                  <tr><td>{{$o++}}</td>
                    <td>{{$course->name}}</td>
                    <td class="ryt">
                      <form action="{{ action('StudentController@destroy', $course->id) }}" method="POST">

                        <input type="checkbox" class="checkbox1" id="myCheck" name="" value="{{$course->name}}" >

                        <input type="hidden" name="_method" value="DELETE">
                        @csrf
                        <button type="submit" name="sub_action" value="crs_del" rel="tooltip" title="Delete Course" class="close2" onclick="return confirm('Are you sure you want to delete this Subject?');"><i class="fa fa-close"></i></button>
                      </form>
                    </td>
                  </tr>
              @endforeach
              </tbody>
            </table>
            @else
              <p>No Subjects Registered</p>
            @endif --}}
      
            <div class="modal-footer">
              <button type="submit" class="btn btn-info" name="store_action" value="admi_create_cls"><i class="fa fa-save"></i> &nbsp; Save</button>
            </div>
          </form>
          </div>

        </div>
        <!--div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Send message</button>
        </div-->
      </div>
    </div>
  </div>

@endsection