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

                <div class="form-group row mb-0 hideMe">
                    <div class="col-md-5 offset-md-0 myTrim">
                      <div class="input-group no-border">
                        

                        <form action="{{action('FeesController@store')}}" method="POST">
                          @csrf
                        
                        </form>

                      </div>
                    </div>
                </div>

                <div class="col-md-12 offset-md-0">

                    <div class="form-group row mb-0 searchRef">
                        <form class="salesForm" action="{{action('ItemsController@store')}}" method="POST">
                          @csrf
                          <div class="dropdown">

                              <input type="text" class="sref" name="item_name" placeholder="Search Item...." id="mySearch" onkeyup="filterFunction()" required/>
                            
                              @if (count($items) > 0)

                                <div id="myDropdown" class="dropdown_content" onselect="myFunction()">
                                  <button type="button" onclick="closeDropdown()" class="btn print_black"><i class="fa fa-times"></i></button>

                                  @foreach ($items as $item)
                                    <a id="selItem{{$item->id}}" onclick="selFunction{{$item->id}}()">
                                      <table class="itemlist_tbl">
                                        <tbody>
                                          <tr>
                                            <td><img class="img" style="width: 30px" src="/storage/rjv_items/{{$item->thumb_img}}" /></td>
                                            <td><b>{{$item->name}}</b><br>{{$item->desc}}</td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </a>

                                    <input id="b1{{$item->id}}" type="hidden" value="{{$item->b1}}"/>
                                    <input id="b2{{$item->id}}" type="hidden" value="{{$item->b2}}"/>
                                    <input id="b3{{$item->id}}" type="hidden" value="{{$item->b3}}"/>
                                    <input id="b4{{$item->id}}" type="hidden" value="{{$item->b4}}"/>
                                    <input id="b5{{$item->id}}" type="hidden" value="{{$item->b5}}"/>
                                    <input id="b6{{$item->id}}" type="hidden" value="{{$item->b6}}"/>
                                    <input id="b7{{$item->id}}" type="hidden" value="{{$item->b7}}"/>

                                    <input id="q1{{$item->id}}" type="hidden" value="{{$item->q1}}"/>
                                    <input id="q2{{$item->id}}" type="hidden" value="{{$item->q2}}"/>
                                    <input id="q3{{$item->id}}" type="hidden" value="{{$item->q3}}"/>
                                    <input id="q4{{$item->id}}" type="hidden" value="{{$item->q4}}"/>
                                    <input id="q5{{$item->id}}" type="hidden" value="{{$item->q5}}"/>
                                    <input id="q6{{$item->id}}" type="hidden" value="{{$item->q6}}"/>
                                    <input id="q7{{$item->id}}" type="hidden" value="{{$item->q7}}"/>
                                    <script>
                                      
                                      function myFunction() {
                                        var drp = document.getElementById("myDropdown");
                                        drp.style.display = "none";
                                      }

                                      function closeDropdown() {
                                        document.getElementById('myDropdown').style.display = 'none';
                                      }
                                      
                                      function selFunction{{$item->id}}() {
                                        var avlQty = "'q" + "{{auth()->user()->bv}}'"

                                        var selItem = document.getElementById("selItem{{$item->id}}");
                                        document.getElementById("mySearch").value = "{{$item->name}}";
                                        document.getElementById("myDropdown").style.display = "none";

                                        document.getElementById("item_id").value = "{{$item->id}}"
                                        document.getElementById("item_no").value = "{{$item->item_no}}"
                                        document.getElementById("name").value = "{{$item->name}}"
                                        document.getElementById("cost_price").value = "{{$item->price}}"
                                        document.getElementById("price").value = document.getElementById("b{{auth()->user()->bv}}{{$item->id}}").value;
                                        document.getElementById("amt").value = "Gh₵ "+document.getElementById("b{{auth()->user()->bv}}{{$item->id}}").value;
                                        // document.getElementById("rem").innerHTML = document.getElementById("q{{auth()->user()->bv}}{{$item->id}}").value;
                                        document.getElementById("qty_avl").innerHTML = document.getElementById("q{{auth()->user()->bv}}{{$item->id}}").value;

                                        document.getElementById("brand").innerHTML = " {{$item->brand}}"
                                        document.getElementById("desc").innerHTML = " {{$item->desc}}"
                                        document.getElementById("mp").style.display = "block";
                                        document.getElementById("mp2").style.display = "block";
                                        document.getElementById("mp3").style.display = "block";
                                      }
                                      
                                      function filterFunction() {
                                      
                                        document.getElementById("myDropdown").style.display = "block";
                                        
                                        var input, filter, ul, li, a, i;
                                        input = document.getElementById("mySearch");
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
                              @endif
                            
                              {{-- <select>
                                <option selected>Cement Bag</option>
                                <option>Wood Nails Pack</option>
                                <option>Pipe Tube(Size 2)</option>
                              </select>   --}}
                            <input id="item_id" name="item_id" type="hidden"/>
                            <input id="name" name="name" type="hidden"/>
                            <input id="price" name="price" type="hidden"/>
                            <input id="cost_price" name="cost_price" type="hidden"/>

                            <input class="sref" id="item_no" name="item_no" type="text" placeholder="Reference" readonly/>
                            <input class="sqty" type="number" name="qty" placeholder="Qty" value="1" min="1" />
                            <input class="sqty" type="text" id="amt" placeholder="Gh₵" readonly/>
                            <!--button type="button" id="rem" class="btn">Rem</button-->
                            @if (auth()->user()->status == 'Administrator')
                              <button type="button" class="btn btn-primary" onclick="alert('Oops...! Administrator cannot make purchase')"><i class="fa fa-plus"></i> &nbsp; Add Item</button>
                            @else
                                <button type="submit" class="btn btn-primary" name="store_action" value="add_to_cart"><i class="fa fa-plus"></i> &nbsp; Add Item</button>
                              <a href="/mpt_cart"><button type="button" class="btn btn-info" name="store_action" value="empty_cart"><i class="fa fa-trash"></i> &nbsp; Empty Cart</button></a>
                            @endif
                            
                            {{-- <button type="submit" class="btn btn-info"  data-toggle="modal" data-target="#orderModal"><i class="fa fa-users"></i> &nbsp; Add Order Details</button> --}}
                        
                          </div>

                          <div id="item_info">
                            <p id="mp3">Qty Available: &nbsp;<b id="qty_avl" class="my_b2">C</b></p>
                            <p id="mp">Brand: <b id="brand" class="my_b">A</b></p>
                            <p id="mp2">Description: <b id="desc" class="my_b">B</b></p>
                          </div>

                        </form>
                    </div>

                </div>

              <div class="card">
                <div class="card-header-primary">
                  <h4 class="card-title">Cart</h4>
                  {{-- <p class="card-category">Complete your profile here..</p> --}}
                </div>
                <div id="printarea1" class="card-body">
            
                    {{-- @if (count($orders) > 0) --}}
                      @if (count($carts) > 0)
                        <table class="table mt">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Item No.</th>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Unit Price(Gh₵)</th>
                            <th class="totAmt">Total(Gh₵)</th>
                            <th class="ryt">Actions</th>
                          </thead>
                          <tbody id="tb">

                              @foreach ($carts as $cart)
                                  
                                @if ($i%2==0)
                                  <tr class="rowColour"><td>{{$i}}</td>
                                @else
                                  <tr><td>{{$i}}</td>
                                @endif
                                  <td>{{$cart->item_no}}</td>
                                  <td>{{$cart->name}}</td>
                                  <td>{{$cart->qty}}</td>
                                  <td>{{$cart->unit_price}}</td>
                                  <td class="totAmt">{{$cart->tot}}</td>
                                  <td class="ryt">
                                    
                                    <form action="{{ action('ItemsController@update', $cart->id) }}" method="POST">
                                      <input type="hidden" name="_method" value="PUT">
                                      @csrf

                                      <a class="edit" rel="tooltip" title="Edit Record" data-toggle="modal" data-target="#changeModal_{{$cart->id}}">&nbsp;<i class="fa fa-pencil"></i>&nbsp;</a>
                                      
                                      {{-- <a href="/reporting/{{$sale->id}}/edit"><button type="button" title="Return Order" class="print_black" onclick="return confirm('Returning order will permanently delete record. Are you sure you want to return selected item?')"><i class="fa fa-mail-reply"></i></button></a> --}}
                                    
                                    </form>  

                                      <div class="modal fade" id="changeModal_{{$cart->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-edit"></i>&nbsp;&nbsp; Edit Item Quantity</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                  <span aria-hidden="true">&times;</span>
                                                </button>
                                              </div>
                                            <div class="modal-body myModalBody">
                                    
                                                <div class="row">
                                                            
                                                    <div class="col-sm-10 col-sm-offset-1">
                                                        <div class="login-form"><!--Item change form-->
                                                            <form action="{{ action('ItemsController@update', $cart->id) }}" method="POST">
                                                                <input type="hidden" name="_method" value="PUT">
                                                                <input type="hidden" name="my_url" value="/checkout1">
                                                                @csrf
                                
                                                                <div class="cartIncrease">
                                                                    <input type="hidden" min="1" name="price" value="{{$cart->unit_price}}">
                                                                    <input type="number" min="1" name="change" value="{{$cart->qty}}">
                                                                    <button class="black_btn" type="submit" name="store_action" value="qty_change"><i class="fa fa-save"></i> &nbsp; SAVE</button>
                                                                </div>
                                                                  
                                                            </form>
                                                        </div><!--/Item change form-->
                                                    
                                                    </div>
                                                    
                                                </div>        
                                                
                                                
                                            </div>
                                    
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                
                                    
                                  </td>
                                </tr>

                              @endforeach
                              
                              <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>{{$carts->sum('qty')}}</b></td>
                                <td></td>
                                <td class="totAmt"><b>{{$carts->sum('tot')}}</b></td>
                                <td class="ryt">      
                                </td>
                              </tr>
                              

                          </tbody>
                        </table>
                      @else
                        <p>Add items to start purchase</p>
                      @endif
                        {{-- <p>Total Records : <b style="color: #000000">{{count($orders)}}</b></p> --}}

                        <div style="height: 30px">
                        </div>
      

                    {{-- @else
                      <p>No Records Found</p>
                    @endif --}}
                </div>

                <div class="form-group row mb-0 searchRef2">
                  <form class="salesForm2" action="{{action('ItemsController@store')}}" method="POST">
                    @csrf
                    <select name="pay_mode">
                      <option selected>-- Mode of Payment --</option>
                      <option>Cash</option>
                      <option>Cheque</option>
                      <option>Mobile Money</option>
                      <option>Post Payment(Debt)</option>
                    </select> 
                      <input type="text" class="sref2" name="buy_name" placeholder="Buyer's Name" required/>
                      <input type="number" class="sref2" name="buy_contact" placeholder="Contact" min="0" required/>
                    <select name="del_status">
                      <option selected>-- Delivery Status --</option>
                      <option>Delivered</option>
                      <option>Not Delivered</option>
                    </select>
                    <input class="sref2" type="number" name="discount" step="any" placeholder="Discount Gh₵" min="0" />
                    <input class="sref2" type="number" name="payment" step="any" placeholder="Payment Amt." min="0" required/>
                      @if(count($carts) > 0)
                        <button type="submit" class="btn btn-primary" name="store_action" value="add_to_sales"><i class="fa fa-money"></i> &nbsp; Pay Bill</button>
                      @endif
                      {{-- <button type="submit" class="btn btn-info"  data-toggle="modal" data-target="#orderModal"><i class="fa fa-users"></i> &nbsp; Add Order Details</button> --}}
                  </form>
                </div>

              </div>
              
              <form action="{{url('/changedate')}}" method="GET">
                <div class="form-group row mb-0 searchRef">
                  <input class="sref" id="item_no" name="date_today" type="date" style="height: 37px; margin: 5px" placeholder="yyyy-mm-dd"/>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-calendar"></i> &nbsp; Change Today's Date</button>
                </div>
              </form>

              <div class="col-md-12 offset-md-0">
                <div class="card">
                  <div id="printarea1" class="card-body">
              
                    @if (count($sales) > 0)
                        <table class="table mt">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Order No.</th>
                            <th>Quantity</th>
                            <th>Pay Mode</th>
                            <th>Buyer</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Total Gh₵</th>
                            <th>Date/Time Created</th>
                            <th class="ryt actsize">Actions</th>
                          </thead>
                          <tbody id="tb">

                            @foreach ($sales as $sale)

                              @if ($sale->del == 'no')
                                
                                @if ($c%2==0)
                                  @if ($sale->del_status == 'Not Delivered')
                                    @if ($sale->paid != 'Paid' || ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid'))
                                      <tr class="debt_alert">
                                    @else
                                    <tr class="not_delivered">
                                    @endif
                                  @else
                                    @if ($sale->paid != 'Paid' || ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid'))
                                      <tr class="debt_alert">
                                    @else
                                      <tr class="rowColour">
                                    @endif
                                  @endif
                                @else
                                  @if ($sale->del_status == 'Not Delivered')
                                    @if ($sale->paid != 'Paid' || ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid'))
                                      <tr class="debt_alert">
                                    @else
                                    <tr class="not_delivered">
                                    @endif
                                  @else
                                    @if ($sale->paid != 'Paid' || ($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid'))
                                      <tr class="debt_alert">
                                    @else
                                      <tr>
                                    @endif
                                  @endif
                                @endif
                                  <td>{{$c++}}</td>
                                  <td>{{$sale->order_no}}<br><p class="small_p">User : {{$sale->user->name}}</p></td>
                                  <td>{{$sale->qty}}</td>
                                  <td>{{$sale->pay_mode}}<br>
                                    @if($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid == 'Paid')
                                      <b>{{$sale->paid}}</b>
                                      &nbsp; <i class="fa fa-check" style="color: rgb(0, 163, 0)"></i>
                                    @endif
                                  </td>  
                                  <td>{{$sale->buy_name}}<br><p class="gray_p">{{$sale->buy_contact}}</p></td>
                                  <td>Payment: {{$sale->payment}}<br><p class="gray_p">Change:&nbsp; {{$sale->change}}</p></td>
                                  <form action="{{ url('/deliverer') }}" method="GET">
                                    <td>{{$sale->del_status}}&nbsp;
                                    {{-- <button type="submit" data-toggle="modal" data-target="#pay_debt" title="Pay Debt" class="btn btn-warning"><i class="fa fa-money"></i></button> --}}
                                    
                                      @csrf
                                      @if($sale->del_status == 'Delivered')
                                        <input type="hidden" name="deliverer" value="{{$sale->id}}">
                                        <input type="hidden" name="deliverer_text" value="Not Delivered">
                                        <button type="submit" class="btn btn-success"><i class="fa fa-times"></i></button>
                                      @else
                                        <input type="hidden" name="deliverer" value="{{$sale->id}}">
                                        <input type="hidden" name="deliverer_text" value="Delivered">
                                        <button type="submit" class="btn btn-warning"><i class="fa fa-check"></i></button>
                                      @endif
                                    </td>
                                  </form>
                                  {{-- <td></td> --}}


                                  @if($sale->pay_mode == 'Post Payment(Debt)' && $sale->paid != 'Paid')
                                    <td>{{number_format($sale->tot, 2)}}<br>
                                      <p class="small_p">Bal.:&nbsp;{{number_format($sale->tot - $sale->paid_debt, 2)}}</del></p>
                                      @if ($sale->discount != 0)
                                        <p class="gray_p">Dis.:&nbsp;{{number_format($sale->discount, 2)}}</p>
                                      @endif
                                    </td>
                                  @else
                                    <td>{{number_format($sale->tot, 2)}}<br>
                                      @if ($sale->discount != 0)
                                        <p class="gray_p">Dis.:{{number_format($sale->discount, 2)}}</p>
                                      @endif
                                    </td>
                                  @endif
                                  
                                  <td>{{$sale->created_at}}<br><p style="color: #0071ce; margin: 0">{{$sale->updated_at}}</p></td>  

                                  {{-- <form action="{{ action('ItemsController@update', $sale->id) }}" method="POST">
                                    <!--input type="hidden" name="_method" value="PUT"-->
                                    @csrf --}}
                                    <td>
                                      <a href="/reporting/{{$sale->id}}"><button type="button" title="Print Order" class="print_black"><i class="fa fa-print"></i></button></a>
                                      
                                      @if ($sale->paid != 'Paid' && $sale->pay_mode == 'Post Payment(Debt)')
                                        {{-- @if ($sale->tot - ())
                                            
                                        @endif --}}
                                        <button type="submit" data-toggle="modal" data-target="#pay_debt{{$sale->id}}" title="Pay Debt" class="print_black"><i class="fa fa-money"></i></button>
                                        
                                        <div class="modal fade" id="pay_debt{{$sale->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                          <div class="modal-dialog modtop" role="document">
                                            <div id="printarea" class="modal-content">
                                              <div class="modal-header">
                                                <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-save"></i>&nbsp;&nbsp; Make Payment for {{$sale->buy_name}}</h6>
                                              </div>
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
                                      
                                                    <form action="{{ action('ItemsController@store') }}" method="POST">
                                                      @csrf
                      
                                                      <div class="cartIncrease">
                                                        <input type="hidden" name="send_id" value="{{$sale->id}}">
                                                        <input type="hidden" name="send_tot" value="{{$sale->tot}}">
                                                        <input type="number" min="1" step="any" name="amt_paid" placeholder="Amount" value="{{$sale->tot - $sale->paid_debt}}" max="{{$sale->tot - $sale->paid_debt}}">
                                                        <button class="black_btn" type="submit" name="store_action" value="pay_debt" onclick="return confirm('Are you sure you want to proceed payment?');"><i class="fa fa-money"></i> &nbsp; Pay</button>
                                                      </div>
                                                        
                                                    </form>
                                      
                                                  </div>
                                                </div>
                                            </div>
                                      
                                          </div>
                                        </div>
                                      @endif

                                      <button type="submit" data-toggle="modal" data-target="#edit_order{{ $sale->id }}" title="Edit Order" class="print_black">&nbsp;<i class="fa fa-pencil"></i>&nbsp;</button>
                                      @if (Auth()->user()->status == 'Administrator')
                                        <a href="/reporting/{{$sale->id}}/edit"><button type="button" title="Return Order" class="print_black" onclick="return confirm('Returning order will permanently delete record. Are you sure you want to return selected item?')"><i class="fa fa-mail-reply"></i></button></a>
                                      @endif
                                      <div class="modal fade" id="edit_order{{ $sale->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        
                                        <div class="modal-dialog modtop" role="document">
                                          <div id="printarea" class="modal-content">
                                            <div class="modal-header">
                                              <h6 class="modal-title" id="exampleModalLabel"><i class="fa fa-save"></i>&nbsp;&nbsp; Edit {{ $sale->buy_name }}'s order details</h6>
                                            </div>
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
                                    
                                                  <form action="{{ action('ItemsController@update', $sale->id) }}" method="POST">
                                                    <input type="hidden" name="_method" value="PUT">
                                                    @csrf
                    
                                                    <div class="my_panel">

                                                      <div class="input_div">
                                                          <p>Buyer's Name: </p>
                                                          <input type="text" class="sref2" name="buy_name" placeholder="Buyer's Name" value="{{ $sale->buy_name }}" required/>
                                                      </div>
                          
                                                      <div class="input_div">
                                                          <p>Contact: </p>
                                                          <input type="number" class="sref2" name="buy_contact" placeholder="Contact" min="0" value="{{ $sale->buy_contact }}" required/>
                                                      </div>
                          
                                                      <div class="input_div">
                                                          <select name="pay_mode">
                                                            <option selected>{{ $sale->pay_mode }}</option>
                                                            <option>Cash</option>
                                                            <option>Cheque</option>
                                                            <option>Mobile Money</option>
                                                            <option>Post Payment(Debt)</option>
                                                          </select> 
                                                      </div>

                                                      <div class="input_div">
                                                        <button type="submit" class="btn btn-info pull-left" name="store_action" value="update_sales"><i class="fa fa-save"></i> &nbsp; Update</button>
                                                      </div>

                                                    </div>

                                                  </form>
                                    
                                                </div>
                                              </div>
                                          </div>
                                    
                                        </div>
                                      </div>
                                    </td> 
                                  {{-- </form> --}}

                                </tr>

                                @if($sale->del_status == 'Not Delivered' && $sale->pay_mode == 'Post Payment(Debt)')
                                
                                  <tr id="showTR2">
                                    <td></td>
                                    <td><b>Items Delivery Status</b></td>
                                    <td></td>
                                    <td></td>
                                    <td><b>Item No.</b></td>
                                    <td><b>Name</b></td>
                                    <td>Qty.</td>
                                    <td><b>Status</b></td>
                                    <td><b>Date</b></td>
                                    <td><b>Action</b></td>  
                                  </tr>
                                  @foreach ($sale->saleshistory as $sh)
                                    <tr>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td>{{$sh->item_no}}</td>
                                      <td>{{$sh->name}}</td>
                                      <td>{{$sh->qty}}</td>
                                      <td>{{$sh->del_status}}</td>
                                      <td>{{$sh->created_at}}</td>
                                      <form action="{{ action('ItemsController@update', $sh->id) }}" method="POST">
                                        <input type="hidden" name="_method" value="PUT">
                                        @csrf
                                        <input type="hidden" name="send_sale_id" value="{{$sale->id}}">
                                        @if($sh->del_status == 'Delivered')
                                          <td><button class="btn btn-info" name="store_action" value="undeliver"><i class="fa fa-suitcase"></i> &nbsp; Undeliver</button></td>  
                                        @else
                                          <td><button class="btn btn-warning" name="store_action" value="deliver"><i class="fa fa-suitcase"></i> &nbsp; Deliver Now</button></td>  
                                        @endif
                                      </form>
                                    </tr>
                                  @endforeach
                                @else
                                    
                                @endif
                              
                              @endif

                            @endforeach

                          </tbody>
                        </table>
                        <p>Sales Count : <b style="color: #000000">{{count($sales)}}</b></p>
                        {{ $sales->links() }}

                        <div style="height: 30px">
                        </div>
      

                    @else
                      <p>No Records Found</p>
                    @endif
                  </div>
                </div>
              </div>

              <div class="container-fluid hideMe">

                <div class="row">
      
                  <div class="col-lg-3 col-md-6 col-sm-6">
                    <a data-toggle="modal" data-target="#totbreakdownModal" class="myA">
                      <div class="card card-stats">
                        <button class="btn salesBtn purple"><i class="fa fa-folder-open salesI"></i></button>
                        <h4 class='config2'>Gh₵ {{number_format($sum_inc_dbt, 2)}}</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Total Sales {{date('d/m/Y')}}</div>
                        </div>
                      </div>
                    </a>
                  </div>
      
                  <div class="col-lg-3 col-md-6 col-sm-6">
                    <a href="/expenses" class="myA">
                      <div class="card card-stats">
                        <button class="btn salesBtn pink"><i class="fa fa-money salesI"></i></button>
                        <h4 class='config2'>Gh₵ {{number_format($expenses->sum('expense_cost'), 2)}}</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Daily Expenditure Report</div>
                        </div>
                      </div>
                    </a>
                  </div>

                  <div class="col-lg-3 col-md-6 col-sm-6">
                    <a href="#" class="myA">
                      <div class="card card-stats">
                        <button class="btn salesBtn mygreen"><i class="fa fa-dollar salesI"></i></button>
                        <h4 class='config2'>Gh₵ {{number_format($debts_paid, 2)}}</h4>
                        
                        <div class="card-footer">
                          <div class="stats">Paid Debts</div>
                        </div>
                      </div>
                    </a>
                  </div>
      
                </div>

              </div>

            </div>


          </div>
        </div>
  </div>



  <div class="modal fade" id="totbreakdownModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp; Total Amount(Gh₵) Breakdown</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <table class="breakdown">
            <tr><td class="tt">Cash</td><td><b class="pr">Gh₵ {{number_format($cash, 2)}}</b></td></tr>
            <tr><td class="tt">Cheque</td><td><b class="pr">Gh₵ {{number_format($cheque, 2)}}</b></td></tr>
            <tr><td class="tt">Mobile Money</td><td><b class="pr">Gh₵ {{number_format($momo, 2)}}</b></td></tr>
            <tr><td class="tt">Post Payment(Debt)</td><td><b class="pr">Gh₵ {{number_format($sum_dbt, 2)}}</b></td></tr>
          </table>

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp; Record Order Details Here</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          
            <form action="{{action('ItemsController@store')}}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="" placeholder="Reference No/Id" type="text" class="form-control" name="ref" required autofocus>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="company_name" placeholder="From: Company Name" type="text" class="form-control" name="company_name" required>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="contact" placeholder="From: Company's Contact" type="text" class="form-control" name="contact" required>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                    <textarea name="desc" class="form-control" rows="3" placeholder="Description"></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12">
                        <input id="tot" placeholder="Total Amt. Gh₵" type="number" class="form-control" name="tot" required>
                    </div>
                </div>

                <div class="col-md-12">
                    <label class="upfiles">Upload Receipt: &nbsp; </label>
                    <input type="file" name="repfile" required>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-info" name="store_action" value="add_order"><i class="fa fa-save"></i> &nbsp; Submit</button>
                    </div>
                </div>
            </form>

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

  function showsubdet() {
    document.getElementById('showTR').style.display = 'block';
  }

</script>

@endsection