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
      <li class="nav-item">
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
                        <input type="text" value="" class="form-control search_field" id="search" name="search" placeholder="Search Records...">
                        <button type="submit" class="btn btn-white btn-round my_bt">
                          <i class="material-icons">search</i>
                          <div class="ripple-container"></div>
                        </button>

                          <a href="/studentsrecycler" class="refresh_a"><button type="submit" name="store_action" value="refresh_val" class="btn btn-success btn-round" id="mb">
                            <i class="fa fa-refresh"></i>
                            <div class="ripple-container"></div>
                          </button></a>

                      </div>
                      
                  </div>
                  <div class="col-md-7 offset-md-0 myTrim">
                    <a href="/students"><button type="submit" class="btn btn-white pull-right" ><i class="fa fa-arrow-left"></i></button></a>
                    <form action="{{ action('StudentController@store') }}" method="POST">
                      @csrf
                      <button type="submit" class="btn btn-white pull-right" name="store_action" value="std_perm_del_all" rel="tooltip" title="Delete All" onclick="return confirm('Are you sure you want to permanently delete all trash records?');"><i class="fa fa-trash"></i></button>
                    </form>
                  </div>

                </div>

              <div class="card recycle">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Showing Deleted Students Records(Trash)</h4>
                  {{-- <p class="card-category">Complete your profile here..</p> --}}
                </div>
                <div id="printarea1" class="card-body">
            
                    @if (count($students) > 0)
                        <table class="table">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Id</th>
                            <th>Fullame</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Class</th>
                            <th>Guardian</th>
                            <th>G.Contact</th>
                            <th>Email</th>
                            <th>Residence</th>
                            <th>Bill (Gh₵)</th>
                            <th>User</th>
                            <th>Date & Time</th>
                            <th class="ryt">Actions</th>
                          </thead>
                          <tbody id="tb">
                            @foreach ($students as $student)
                              @if ($c%2==0)
                                <tr class="rowColour"><td>{{$c++}}</td>
                              @else
                                <tr><td>{{$c++}}</td>
                              @endif
                                  <td>{{$student->std_id}}</td>
                                  <td>{{$student->fname.' '.$student->sname}}</td>
                                  <td>{{$student->sex}}</td>
                                  <td>{{$student->dob}}</td>
                                  <td>{{$student->class}}</td>
                                  <td>{{$student->guardian}}</td>
                                  <td>{{$student->contact}}</td>
                                  <td>{{$student->email}}</td>
                                  <td>{{$student->residence}}</td>
                                  <td>{{$student->bill}}</td>
                                  <td>{{$student->user->name}}</td>
                                  <td>{{$student->created_at}}</td>
                                  <td class="ryt">
                                    
                                    <form action="{{ action('StudentController@update', $student->id) }}" method="POST" enctype="multipart/form-data">
                                      <input type="hidden" name="_method" value="PUT">
                                      @csrf

                                      <button type="submit" name="sub_action" value="std_perm_del" rel="tooltip" title="Delete Student" class="close2" onclick="return confirm('Are you sure you want to delete permanently? Fees records will be deleted as well..');"><i class="fa fa-close"></i></button>
                                      <button type="submit" name="sub_action" value="std_restore" rel="tooltip" title="Restore Student" class="view3" onclick="return confirm('Are you sure you want to restore {{$student->fname}}`s record? Fees records will be restored as well..');"><i class="fa fa-check"></i></button>
                                  
                                    </form>                  
                                    
                                  </td>
                                </tr>
                            @endforeach
                          </tbody>
                        </table>
                        <p>Deleted Records : <b style="color: #000000">{{$std_pop}}</b></p>
                        {{ $students->links() }}

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
          url : '{{URL::to('searchstudenttrash')}}',
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