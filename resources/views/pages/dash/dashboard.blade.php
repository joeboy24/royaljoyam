
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
      <li class="nav-item ">
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
    
  <div class="content">
        <div class="container-fluid">

          @include('inc.messages')
          <div class="cols_cont">

            <div class="col_60">

              <a href="/config" class="myA">
                <div class="card card-stats">
                  
                  <i class="fa fa-cogs myIcon"></i>

                  <h3 class='config'>Configuration</h3>
                  
                  <div class="card-footer">
                    <div class="stats">Administrator Setup
                    </div>
                  </div>
                </div>
              </a>
            </div>

            <div class="col_60">

              <a href="/dashuser" class="myA">
                <div class="card card-stats">
                  
                  <i class="fa fa-edit myIcon"></i>

                  <h3 class='config'>Registry</h3>
                  
                  <div class="card-footer">
                    <div class="stats">Register User, Items/Categories...
                    </div>
                  </div>
                </div>
              </a>
            </div>

            <div class="col_60">

              <a href="/waybill" class="myA">
                <div class="card card-stats">
                  
                  <i class="fa fa-truck myIcon"></i>

                  <h3 class='config'>Waybill</h3>
                  
                  <div class="card-footer">
                    <div class="stats">Waybill info. Management
                    </div>
                  </div>
                </div>
              </a>
            </div>

            <div class="col_60">

              <a href="/sales" class="myA">
                <div class="card card-stats">
                
                  <i class="fa fa-shopping-basket myIcon"></i>
  
                  <h3 class='config'>Sales</h3>
                  
                  <div class="card-footer">
                    <div class="stats">Manage Sales & Records
                    </div>
                  </div>
                </div>
              </a>
            </div>

            <div class="col_60">
              <a href="/reporting" class="myA">
                <div class="card card-stats">
                  
                  <i class="fa fa-file-text myIcon"></i>

                  <h3 class='config'>Reports</h3>
                  
                  <div class="card-footer">
                    <div class="stats">Manage Report System
                    </div>
                  </div>
                </div>
              </a>
            </div>

            <div class="col_60">

              <a href="/expenses" class="myA">
                <div class="card card-stats">
                  
                  <i class="fa fa-money myIcon"></i>

                  <h3 class='config'>Expenditure</h3>
                  
                  <div class="card-footer">
                    <div class="stats">Manage Expenses here.
                    </div>
                  </div>
                </div>
              </a>
            </div>

            <div class="col_60">

              <a href="/closure_page" class="myA">
                <div class="card card-stats">
                  
                  <i class="fa fa-calendar myIcon"></i>

                  <h3 class='config'>Closure</h3>
                  
                  <div class="card-footer">
                    <div class="stats">Manage Closure here.
                    </div>
                  </div>
                </div>
              </a>
            </div>


          </div>

        </div>
  </div>

@endsection