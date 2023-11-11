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
      <li class="nav-item active2">
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
                  <h4 class="card-title">Paid Debts</h4>
                  <p class="card-category">An overview of debt paid on {{date('D. d-m-Y', strtotime(session('date_today')))}}</p>
                </div>
                <div id="printarea1" class="card-body">
            
                    @if (count($sales_pay) > 0)
                      <table class="table mt">
                        <thead class=" text-secondary hideMe">
                          <th>#</th>
                          <th>Order No.</th>
                          <th>Buyer</th>
                          <th>Amt.&nbsp;Paid&nbsp;(Gh₵)</th>
                          <th>Bal. Rem.</th>
                          <th>Date Added</th>
                          <th class="ryt actsize">Actions</th>
                        </thead>
                        <tbody id="tb">

                          @foreach ($sales_pay as $sl)

                            @if ($sl->sale)
                                @if ($sl->del == 'no')
                                  @if ($c%2==0)
                                    <tr class="rowColour">
                                  @else
                                    <tr>
                                  @endif
                                @else
                                    <tr class="alert-danger">
                                @endif
                                    <td>{{$c++}}</td>
                                    <td>{{$sl->sale->order_no}}<br><p class="gray_p">User: {{$sl->sale->user->name}}</p></td>
                                    <td>{{$sl->sale->buy_name}}<br><p class="small_p">{{$sl->sale->buy_contact}}</p></td>
                                    <td>{{number_format($sl->amt_paid, 2)}}<br><p class="small_p">Tot : {{number_format($sl->sale->tot, 2)}}</p></td>
                                    <td>{{$sl->bal}}</td>
                                    <td>{{date('M. d, Y', strtotime($sl->created_at))}}</td>

                                    <td class="ryt">
                                  
                                      <form action="{{ action('ItemsController@update', $sl->id) }}" method="POST">
                                        <input type="hidden" name="_method" value="PUT">
                                        @csrf

                                        @if ($sl->del == 'no')
                                          <button name="store_action" value="del_paid_debt" rel="tooltip" title="Delete Record" class="icon_btn color6" title="Distribute" onclick="return confirm('Are you sure you want to permanently delete record?');"><i class="fa fa-trash"></i></button>
                                        @endif
                                      </form>                  
                                  
                                    </td>
                                  </tr>
                            
                            @else
                                <tr style="background: #ffe172">
                                    <td>{{$c++}}</td>
                                    <td>Record display error..!<br><p class="small_p">User: {{$sl->user->name}}</p><p class="small_p">Amount: {{$sl->amt_paid}}</p></td>
                                    <td></td><td></td><td></td>
                                    <td>{{date('l M. d, Y', strtotime($sl->created_at))}}</td>
                                    <td></td>
                                </tr>
                            @endif 
                            
                            {{-- @endif --}}

                          @endforeach

                        </tbody>
                      </table>
                      {{$sales_pay->links()}}
                      {{-- <p>Total: <b style="color: #000000">{{count($sales_pay)}}</b></p> --}}
                 
                    @else
                      <div class="alert alert-danger">
                        No records found
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