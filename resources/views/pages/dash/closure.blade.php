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
      <li class="nav-item">
        <a class="nav-link" href="/reporting">
          <i class="fa fa-file-text"></i>
          <p>Report</p>
        </a>
      </li>
      <li class="nav-item active2">
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
            <div class="col-md-12">

              @include('inc.messages')

                {{-- <div class="row">
                  <div class="col-md-12">
                    <div class="menu_box_cont">
                      <div class="inner">
                        <a href="/reporting" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-shopping-basket"></i>&nbsp;&nbsp; Sales</h4>
                            <p>General sales report</p>
                          </div>
                        </a>
                        <a href="/stockbal" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-bar-chart"></i>&nbsp;&nbsp; Stock</h4>
                            <p>General stock balances</p>
                          </div>
                        </a>
                        <a href="/expensereport" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-suitcase"></i>&nbsp;&nbsp; Expenses</h4>
                            <p>General expenses report</p>
                          </div>
                        </a>
                        <a href="/debts" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-folder-open"></i>&nbsp;&nbsp; Debts</h4>
                            <p>Debts (Post Payments)</p>
                          </div>
                        </a>
                        <a href="/waybillreport" class="myA">
                          <div class="menu_box">
                            <h4><i class="fa fa-truck"></i>&nbsp;&nbsp; Waybill</h4>
                            <p>Waybill Report</p>
                          </div>
                        </a>
                        <a href="/returnsreport" class="myA">
                          <div class="menu_box active_menu">
                            <h4><i class="fa fa-warning"></i>&nbsp;&nbsp; Returns</h4>
                            <p>General sales report</p>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                </div> --}}

                <div class="row">
                  <div class="col-md-12">
                    @for ($i = 9; $i <= 12; $i++)
                      @if ($i < 10) <input type="hidden" value="{{$i = '0'.$i}}"> @endif
                      <input type="hidden" value="{{$dt='01-'.$i.'-'.date('Y')-1}}">
                      <a href="/closure/{{$dt}}" class="myA">
                        <div class="closure_box2">
                          <h4><i class="fa fa-calendar"></i>&nbsp;&nbsp; {{date('F, Y', strtotime($dt))}}</h4>
                          <p>{{$i}} Details Here...</p>
                        </div>
                      </a>
                    @endfor
                  </div>

                  <div class="col-md-12">
                    @for ($i = 1; $i <= 12; $i++)
                      @if ($i < 10) <input type="hidden" value="{{$i = '0'.$i}}"> @endif
                      <input type="hidden" value="{{$dt='01-'.$i.'-'.date('Y')}}">
                      <a href="/closure/{{$dt}}" class="myA">
                        <div class="closure_box">
                          <h4><i class="fa fa-calendar"></i>&nbsp;&nbsp; {{date('F, Y', strtotime($dt))}}</h4>
                          <p>{{$i}} Details Here...</p>
                        </div>
                      </a>
                    @endfor
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