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
      <li class="nav-item">
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


          <div class="col-md-12 myTrim">
            <a href="{{url()->previous()}}"><button type="submit" class="btn btn-white" ><i class="fa fa-arrow-left"></i></button></a>
          </div>

        <div class="card">
          <div class="card card-profile">
            <div class="card-body">
              <h4 class="card-title">Add Expenses Here</h4>

              <div class="col-md-12">
                <form action="{{action('ItemsController@store')}}" method="POST" enctype="multipart/form-data">
                  @csrf


                  <label for="cat-title" class="col-form-label myLabel">Expense Title:</label>
                  <div class="form-group">
                    <input type="text" class="form-control" name="title" placeholder="eg. Internet Payment." required/>
                  </div>

                  <label for="cat-title" class="col-form-label myLabel">Description:</label>
                  
                  <div class="form-group">
                    <textarea name="desc" class="form-control" rows="3" placeholder="Type description here"></textarea>
                  </div>

                  <label for="cat-title" class="col-form-label myLabel">Cost: (Gh₵)</label>
                  <div class="form-group">
                    <input type="number" step="any" min="0" class="form-control" name="expense_cost" maxlength="10" placeholder="eg. 1000"/>
                  </div>
                  {{-- @if (auth()->user()->status == 'Administrator') --}}
                    <label for="cat-title" class="col-form-label myLabel">Choose Branch</label>
                    <div class="form-group">
                      <select name="branch" class="form-control" required>
                        <option value="0">Select Branch Name</option>
                        @if (count($branches) > 0)
                          @foreach ($branches as $branch)
                            @if (auth()->user()->status == 'Administrator')
                              <option value="{{ $branch->id }}">{{ $branch->name }}</option> 
                            @elseif (auth()->user()->bv == $branch->id)
                              <option value="{{ $branch->id }}">{{ $branch->name }}</option> 
                            @endif
                          @endforeach
                        @endif
                      </select>
                    </div>
                  {{-- @endif --}}
                  
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-info" name="store_action" value="create_expense"><i class="fa fa-save"></i> &nbsp; Save</button>
                  </div>
                </form>
              </div>
                
            </div>
          </div>
        </div>

              <div style="height: 30px">
              </div>

          <div class="card card-profile">
            <div class="card-body">
              <h4 class="card-title">All Expenses Made</h4>

              @if (count($expenses) > 0)
                <table class="table">
                  <thead class="text-secondary">
                    <th></th>
                    <th>Branch</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Cost</th>
                    <th class="pr">Date/Time</th>
                    <th class="ryt">
                      Action
                    </th>
                  </thead>
                  <tbody>

                  
                  @foreach ($expenses as $expense)
                      <tr><td>{{$i++}}</td>
                        <td>{{$expense->companybranch->name}}</td>
                        <td>{{$expense->title}}</td>
                        <td>{{$expense->desc}}</td>
                        <td>{{number_format($expense->expense_cost)}}</td>
                        <td>{{date('D, M-d', strtotime($expense->created_at))}}</td>
                        <td class="ryt">
                          <form action="{{ action('ItemsController@destroy', $expense->id) }}" method="POST">

                            <input type="hidden" name="_method" value="DELETE">
                            @csrf
                            <button type="submit" name="del_action" value="expense_del" rel="tooltip" title="Delete Record" class="close2" onclick="return confirm('NOTE: Deleting this record will credit main account with Gh₵ {{$expense->expense_cost}}');"><i class="fa fa-close"></i></button>
                        
                          </form>
                        </td>
                      </tr>
                  @endforeach
                  <tr>
                    <td></td><td></td><td><b>No. of Records : {{count($expenses)}}</b></td><td><b>Total</b></td><td><b>{{ number_format($expenses->sum('expense_cost'), 2) }}</b></td><td></td><td></td>
                  </tr>
                  

                  </tbody>
                </table>
              @else
                <p>No expenses made yet</p>
              @endif                        

            </div>
          </div>

              <div style="height: 30px">
              </div>

      </div>
    </div>
  </div>


@endsection