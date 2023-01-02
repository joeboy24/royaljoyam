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

                  <div class="col-md-7 offset-md-0">

                    <form action="{{action('FeesController@store')}}" method="POST" enctype="multipart/form-data">
                      @csrf
                        {{-- <label for="recipient-name" class="col-form-label">Asign Teacher/Lecturer</label> --}}
                        <select name="sel_class" class="form-control sort" id="stages" onchange="SelFunction()">
                          <option>Select Class</option>
                          @if(count($stages) > 0)
                            @foreach ($stages as $stage)
                              @if($stage->del != 'yes')
                                <option>{{$stage->cls_name}}</option>
                              @endif
                            @endforeach
                          @endif
                          <option>None</option>
                        </select>

                        <select name="sel_term" class="form-control sort">
                          <option>Select Term</option>
                          <option>1</option>
                          <option>2</option>
                          <option>None</option>
                        </select>

                        
                        <script>

                          function MyPrint(){

                            alert('Print alert!');

                            var prntContent = document.getElementById('printarea');
                            var WinPrint = Window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
                            WinPrint.document.write(prntContent.innerHTML);
                            WinPrint.document.close();
                            WinPrint.focus();
                            WinPrint.print();
                            WinPrint.close;
                            
                          }

                          function SelFunction(){

                            var SelectValue = document.getElementById('stages').value;
                            var txt_input = document.getElementById('txt_input')

                            txt_input.value = SelectValue;
                            //alert('Changed');

                          }
                        </script>

  
                        <input type="text" style="display: none" class="" id="txt_input" name="id_hold">
                        <button type="submit" class="btn btn-primary" name="load_action" value="load_class_fee"><i class="fa fa-refresh"></i> &nbsp; Load Results</button>
                    </form>

                  </div>

                  <div class="col-md-5 offset-md-0 myTrim">
                      <div class="input-group no-border">
                        <form method="GET" action="{{ url('/searchtry') }}">
                          <input type="text" value="" class="form-control search_field" id="searchtry" name="searchquery" placeholder="Search Records...">
                          <button type="submit" class="btn btn-white btn-round my_bt">
                            <i class="material-icons">search</i>
                            <div class="ripple-container"></div>
                          </button>
                        </form>

                        <form action="{{action('FeesController@store')}}" method="POST">
                          @csrf
                        <a class="refresh_a"><button type="submit" name="store_action" value="refresh_val" class="btn btn-success btn-round" id="mb">
                          <i class="fa fa-refresh"></i>
                          <div class="ripple-container"></div>
                        </button></a>
                        </form>

                      </div>
                    </div>
                </div>


              <div class="card">
                <div class="card-header card-header-primary hideMe">
                  <h4 class="card-title">All Records On Fees</h4>
                  {{-- <p class="card-category">Complete your profile here..</p> --}}
                </div>
                <div id="printarea1" class="card-body">
            
                    @if (count($fees) > 0)
                        <table class="table">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>ID</th>
                            <th>Fullame</th>
                            <th>Class</th>
                            <th>Term</th>
                            <th>Year</th>
                            <th>Inst1</th>
                            <th>Inst2</th>
                            <th>Inst3</th>
                            <th>Inst4</th>
                            <th class="totAmt">Total (Gh₵)</th>
                            <th class="balAmt">Bal.</th>
                            <th class="feeStatus">Status</th>
                            <th class="ryt">Actions</th>
                          </thead>
                          <tbody id="tb">
                            @foreach ($fees as $fee)
                              <tr></tr>
                            @if ($c%2==0)
                                <tr class="rowColour"><td class="hideMe">{{$c++}}</td>
                            @else
                                <tr><td class="hideMe">{{$c++}}</td>
                            @endif
                                  <td class="hideMe">{{$fee->student->std_id}}</td>
                                  <td class="hideMe">{{$fee->fullname}}</td>
                                  <td class="hideMe">{{$fee->class}}</td>
                                  <td class="hideMe">{{$fee->term}}</td>
                                  <td class="hideMe">{{$fee->year}}</td>
                                  <td class="hideMe">{{$fee->inst1}}</td>
                                  <td class="hideMe">{{$fee->inst2}}</td>
                                  <td class="hideMe">{{$fee->inst3}}</td>
                                  <td class="hideMe">{{$fee->inst4}}</td>
                                  <td class="totAmt hideMe">{{$sum = $fee->inst1 + $fee->inst2 + $fee->inst3 + $fee->inst4}}</td>
                                  <td class="balAmt hideMe">{{$bal = $fee->student->bill - $sum}}</td>
                                  
                                  @if($fee->student->bill == 0 and $fee->inst1 == 0 and $fee->inst2 == 0 and $fee->inst3 == 0 and $fee->inst4 == 0)
                                    <td class="feeStatus hideMe"><i class="fa fa-warning myWarning"></i></td>
                                  @else
                                    @if($sum >= $fee->student->bill)
                                      <td class="feeStatus hideMe"><i class="fa fa-check mySuccess"></i></td>

                                    @else
                                      <td class="feeStatus hideMe"><i class="fa fa-warning myWarning"></i></td>
                                    @endif
                                  @endif

                                  <td class="ryt">
                                    <form action="{{ action('FeesController@update', $fee->id) }}" method="POST">
                                      <input type="hidden" name="_method" value="PUT">
                                      @csrf

                                      {{-- <button type="submit" name="sub_action" value="del" rel="tooltip" title="Delete Record" class="close2" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fa fa-close"></i></button>
                                      --}}
                                      <a href="" class="edit hideMe" data-toggle="modal" rel="tooltip" title="Edit Record" data-target="#edit_{{$fee->id}}"><i class="fa fa-pencil"></i></a>
                                      <button type="button" class="view2 hideMe" rel="tooltip" title="View Record" data-toggle="modal" data-target="#{{$fee->id}}"><i class="fa fa-folder-open"></i></button>
                                      <!--textarea class="form-control" id="article-ckeditor" name="body" placeholder="Body/Text" rows="5"></textarea-->
                                      <input type="hidden" class="form-control" name="mid" value="{{$fee->id}}"/> 
                                                      

                                      <div class="modal fade" id="{{$fee->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                                  <h3 class="card-title">{{$company->name}}</h3>
                                                  <p class="card-category text-gray pp">{{session('company')->address}}</p>
                                                  <p class="card-category text-gray pp">Contact: {{session('company')->contact.' / Email: '.session('company')->email.session('company')->contact.' / Email: '.session('company')->email}}</p>
                                                  <h4 class="card-title">Payment Receipt</h4>

                                                  <a class="btn btn-info hideMe" onclick="window.print()"><p  id="print_btn" style="margin-top: 5px; color:#fff"><i class="fa fa-print"></i>&nbsp; Print</p></a>

                                                  <table id="reports_tbl">
                                                    <tbody>
                                                      <tr><td>Name: </td><td>{{$fee->student->fname.' '.$fee->student->sname}}</td></tr>

                                                      @if($sum >= $fee->student->bill and $fee->student->bill != 0 and $sum != 0)
                                                      <tr><td>Being: </td><td>Full payment for {{$company->ac_year}} academic year school fees.</td></tr>
                                                      
                                                      @else

                                                        @if($sum == 0)
                                                        @elseif($sum == 0 and $sum < $fee->student->bill)
                                                        @elseif($sum == 0 and $fee->student->bill == 0)
                                                        @elseif($sum != 0 and $sum < $fee->student->bill)
                                                          <tr><td>Being: </td><td>Part payment for {{$company->ac_year}} academic year school fees.</td></tr>
                                                        @endif

                                                      @endif

                                                      <tr><td>Payments Made: </td><td></td></tr>
                                                      <tr><td><h5 class="card-title">Date</h5></td><td class="feeTot">Amount</td></tr>

                                                      @if ($fee->inst4 > 0)
                                                        <tr><td>{{$fee->updated_at}}</td><td>Gh₵ {{$fee->inst4}}.00</td></tr>
                                                      @endif
                                                      @if ($fee->inst3 > 0)
                                                        <tr><td>{{$fee->updated_at}}</td><td>Gh₵ {{$fee->inst3}}.00</td></tr>
                                                      @endif
                                                      @if ($fee->inst2 > 0)
                                                        <tr><td>{{$fee->updated_at}}</td><td>Gh₵ {{$fee->inst2}}.00</td></tr>
                                                      @endif
                                                      @if ($fee->inst1 > 0)
                                                        <tr><td>{{$fee->created_at}}</td><td>Gh₵ {{$fee->inst1}}.00</td></tr>
                                                      @endif

                                                      <tr><td><h5 class="card-title">Total: </h5></td><td class="feeTot">Gh₵ {{$sum}}.00</td></tr>

                                                      <tr><td></td><td></td></tr>

                                                      @if ($bal < 0)
                                                        <tr><td><h5 class="card-title">Balance:</h5> </td><td class="feeTot">0.00</td></tr>
                                                        <tr><td> </td><td class="tr">{{$bal}}.00 will be credited to next Year/Term's fees.</td></tr>
                                                      @else
                                                        <tr><td><h5 class="card-title">Balance:</h5> </td><td class="feeTot">Gh₵ {{$bal}}.00</td></tr>
                                                      @endif

                                                      <tr><td></td><td>Accountant's Signature</td></tr>
                                                      <tr><td></td><td>______________________</td></tr>
                                                    </tbody>
                                                  </table>

                                                  <!--p class="card-description">
                                                    Don't be scared of the truth because we need to restart the human foundation in truth And I love you like Kanye loves Kanye I love Rick Owens’ bed design but the back is...
                                                  </p-->
                                                </div>
                                              </div>
                                          </div>
                                    
                                        </div>
                                      </div>


                                      <div class="modal fade" id="edit_{{$fee->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modtop" role="document">
                                          <div class="modal-content">
                                              
                                              <div class="card card-profile">
                                                <div class="card-avatar">
                                                  <a href="#pablo">
                                                  <img class="img" src="/storage/std_imgs/{{$fee->student->photo}}" />
                                                  </a>
                                                </div>
                                                <div class="card-body">
                                                  <h6 class="card-category text-gray">Class: {{$fee->class.'  /  Term: '.$fee->term}}</h6>
                                                  <h4 class="card-title">{{$fee->student->fname.' '.$fee->student->sname}}</h4>

                                                  <table class="user_view_tbl">
                                                    <tbody>

                                                      <tr class="tbl_tr"><td class="tl">Student ID</td><td class="tr">{{$fee->student->std_id}}
                                                        {{-- <input type="text" class="form-control" name="fname" placeholder="Firstname" value="{{$fee->fname}}" required/> 
                                                        <input type="hidden" class="form-control" name="mid" value="{{$fee->id}}"/>  --}}
                                                      </td></tr>
                                                      
                                                      {{-- <label for="cat-title" class="col-form-label myLabel">Select Installment:</label> --}}
                                                      <tr><td class="tl">Select Installment no.</td>
                                                        <td class="tr">
                                                          <select id="sel_inst" name="sel_inst" class="form-control">
                                                            <option value="None Selected">None Selected</option>
                                                            <option value="inst1">Installment 1</option>
                                                            <option value="inst2">Installment 2</option>
                                                            <option value="inst3">Installment 3</option>
                                                            <option value="inst4">Installment 4</option>
                                                          </select>
                                                        </td>
                                                      </tr>

                                                      <tr class="tbl_tr">
                                                        <td class="tl">Type Value</td>
                                                        
                                                        <td class="tr">
                                                          <div class="form-group">
                                                            <input id="instValue" type='text' name="instValue" class="form-control"/>
                                                          </div>
                                                        </td>
                                                      </tr>

                                                    </tbody>
                                                  </table>

                                                </div>
                                              </div>
                                              
                                              <div class="modal-footer">
                                                <button type="submit" class="btn btn-info" name="sub_action" value="update_fee"><i class="fa fa-save"></i> &nbsp; Update Record</button>
                                              </div>

                                          </div>
                                    
                                        </div>
                                      </div>
                                      
                                    </form>
                                  </td>


                                </tr>
                            @endforeach
                                <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td class="hideMe"><h6>Total: </h6></td><td class="totAmt hideMe"><h6>{{number_format($sum5)}}.00</h6></td></tr>

                          </tbody>
                        </table>
                        {{ $fees->links() }}
                    @else
                      <p>No Records Found</p>
                    @endif
                </div>
              </div>
  

              <div class="container-fluid hideMe">

                <div class="row">
      
                  <div class="col-lg-3 col-md-6 col-sm-6">
      
                    <a href="/feereports" class="myA">
                      <div class="card card-stats">
                    
                        <h4 class='config2'><i class="fa fa-print myIcon2"></i>&nbsp;&nbsp; Payment History</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Search & Print Reports
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>
      
                  <div class="col-lg-3 col-md-6 col-sm-6">
      
                    <a href="/expenses" class="myA">
                      <div class="card card-stats">
                    
                        <h4 class='config2'><i class="fa fa-money myIcon2"></i>&nbsp;&nbsp; Expenses</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Add/View Expenses
                          </div>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-3 col-md-6 col-sm-6">
      
                      <div class="card card-stats">
                    
                        <h4 class='config2'><i class="fa fa-circle myIcon3"></i>&nbsp;&nbsp;Gh₵ {{$sumAll}}.00</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Accont Balance
                          </div>
                        </div>
                      </div>
                  </div>

                  <div class="col-lg-3 col-md-6 col-sm-6">
      
                      <div class="card card-stats">
                    
                        <h4 class='config2'><i class="fa fa-circle myIcon4"></i>&nbsp;&nbsp;Gh₵ {{number_format($sumEx)}}.00</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Total Expenses
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