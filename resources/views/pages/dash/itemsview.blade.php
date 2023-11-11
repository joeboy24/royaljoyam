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
            <div class="col-md-11">

              @include('inc.messages')

                <div class="form-group row mb-0 hideMe">

                  <div class="col-md-5 offset-md-0 myTrim">

                    <form style="width: 400px" method="GET" action="{{ url('/items') }}">
                      @csrf
                      <div class="input-group no-border">
                        {{-- <input type="text" value="" class="form-control search_field" id="search" name="search" placeholder="Search Records...">
                        <button type="submit" class="btn btn-white btn-round my_bt">
                          <i class="material-icons">search</i>
                          <div class="ripple-container"></div>
                        </button> --}}

                          <input type="search" value="" class="form-control search_field" id="itemsearch" name="itemsearch" placeholder="Search Records...">
                           
                          <button type="submit" class="btn btn-white btn-round my_bt">
                            <i class="material-icons">search</i>
                            <div class="ripple-container"></div>
                          </button>

                          <a href="/items" class="refresh_a"><button type="submit" class="btn btn-success btn-round" id="mb">
                            <i class="fa fa-refresh"></i>
                            <div class="ripple-container"></div>
                          </button></a>
                          
                      </div>
                    </form>
                      
                  </div>
                  <div class="col-md-7 offset-md-0 myTrim">
                    <a href="#"><button type="submit" class="btn btn-white pull-right" title="Recycle Bin"><i class="fa fa-trash"></i></button></a>
                    <a href="/dashuser"><button type="submit" class="btn btn-white pull-right" ><i class="fa fa-arrow-left"></i></button></a>
                    {{-- <a href="/students"><button type="submit" class="btn btn-white pull-right" ><i class="fa fa-refresh"></i></button></a> --}}
                  </div>

                </div>

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Stock View</h4>
                  {{-- <p class="card-category">Complete your profile here..</p> --}}
                </div>
                <div id="printarea1" class="card-body">
            
                    @if (count($items) > 0)
                        <table class="table mt">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Item No.</th>
                            <th>Name</th>
                            {{-- <th>Description</th> --}}
                            <th>Category</th>
                            {{-- <th>Barcode</th> --}}
                            <th>Total Qty.</th>
                            <th>Price (Gh₵)</th>
                            {{-- <th>Thumbnail</th> --}}
                            <th>Date Created</th>
                            <th class="ryt">Actions</th>
                          </thead>
                          <tbody id="tb">

                            @foreach ($items as $item)

                              @if ($item->del == 'no')
                                
                                @if ($c%2==0)
                                  <tr class="rowColour"><td>{{$c++}}</td>
                                @else
                                  <tr><td>{{$c++}}</td>
                                @endif
                                  <td>{{$item->item_no}}<br><p class="small_p">{{$item->thumb_img}}</p></td>
                                  <td>{{$item->name}}</td>
                                  {{-- <td>{{$item->desc}}</td> --}}
                                  <td>{{$item->cat}}</td>
                                  {{-- <td>{{$item->barcode}}</td> --}}
                                  <td><b style="font-weight: 600">
                                          <!--{{$item->qty}}-->
                                          {{$item->q1 + $item->q2 + $item->q3 + $item->q4 + $item->q5}}
                                      </b>
                                    @for ($i = 0; $i < count(session('compbranch')); $i++)
                                      <input type="hidden" value="{{$q = 'q'.$i+1}}">
                                      @if ($item->$q != 0 || $item->$q != '')
                                      / {{$item->$q}}
                                      @endif
                                    @endfor
                                  </td>
                                  <td><b style="font-weight: 600">{{$item->price}}</b>
                                    @for ($i = 0; $i < count(session('compbranch')); $i++)
                                      <input type="hidden" value="{{$b = 'b'.$i+1}}">
                                      @if ($item->$b != 0 || $item->$b != '')
                                      / {{$item->$b}}
                                      @endif
                                    @endfor
                                  </td>
                                  {{-- <td>{{$item->thumb_img}}</td> --}}
                                  <td>{{$item->created_at}}</td>
                                  <td class="ryt">
                                    
                                    <form action="{{ action('ItemsController@update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                      <input type="hidden" name="_method" value="PUT">
                                      @csrf

                                      <button type="submit" name="store_action" value="del_item" rel="tooltip" title="Delete Item" class="close2" onclick="return confirm('Are you sure you want to delete selected item?');"><i class="fa fa-close"></i></button>
                                      <button type="button" data-toggle="modal" data-target="#edit_{{ $item->id }}" title="Edit Record" class="print_black">&nbsp;<i class="fa fa-pencil"></i>&nbsp;</button>
                                      

                                      <div class="modal fade" id="edit_{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modtop" role="document">
                                          <div class="modal-content">
                                              
                                              <div class="card card-profile">
                                                <div class="card-avatar">
                                                  <a href="#pablo">
                                                  <img class="img" src="/storage/rjv_items/{{$item->thumb_img}}" />
                                                  </a>
                                                </div>
                                                <div class="card-body">
                                                  <h4 class="card-category text-gray">Item N0: {{$item->item_no}}&nbsp;</h4>
                                                  <h6 class="card-title">Created by: {{$item->user->name}}</h6>




                                                  <table class="user_view_tbl">
                                                    <tbody>

                                                      <tr class="tbl_tr"><td class="tl">Item Name</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" name="name" placeholder="Item Name" value="{{$item->name}}" required/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Description</td><td class="tr">
                                                        <div class="form-group">
                                                          <textarea type="text" class="form-control" rows="4" name="desc" placeholder="Description" required>{{$item->desc}}</textarea>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Category</td><td class="tr">
                                                        <div class="form-group">
                                                          <select name="cat" class="form-control" id="cat">
                                                            <option selected>{{$item->cat}}</option>
                                                            @if(count($cats) > 0)
                                                            @foreach ($cats as $cat)
                                                              @if($cat->del != 'yes')
                                                                <option>{{$cat->name}}</option>
                                                              @endif
                                                            @endforeach
                                                          @endif
                                                          </select>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Brand</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" name="brand" placeholder="Brand" value="{{$item->brand}}"/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Barcode</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type='text' class="form-control" placeholder="Barcode" name="barcode" value="{{$item->barcode}}"/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl"><b>Gen. Quantity</b></td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="number" class="form-control" name="qty" id="qty{{$item->id}}" placeholder="Quantity" value="{{$item->qty}}" readonly/>
                                                        </div>
                                                      </td></tr>

                                                      @for ($i = 0; $i < count(session('compbranch')); $i++)
                                                        <tr class="tbl_tr"><td class="tl">Branch {{$i+1}} Qty.</td><td class="tr">
                                                          <div class="form-group">
                                                            <input type="hidden" value="{{$qq = 'q'.$i+1}}">
                                                            <input type="number" class="form-control" name="q{{$i+1}}" id="q{{$item->id}}" placeholder="Quantity" value="{{$item->$qq}}" onchange="qty_sum{{$item->id}}()" required/>
                                                          </div>
                                                        </td></tr>
                                                      @endfor

                                                      {{-- <tr class="tbl_tr"><td class="tl">Branch 2 Qty.</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="number" class="form-control" name="q2" id="q2{{$item->id}}" placeholder="Quantity" value="{{$item->q2}}" onchange="qty_sum{{$item->id}}()" required/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Branch 3 Qty.</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="number" class="form-control" name="q3" id="q3{{$item->id}}" placeholder="Quantity" value="{{$item->q3}}" onchange="qty_sum{{$item->id}}()" required/>
                                                        </div>
                                                      </td></tr> --}}

                                                      <tr class="tbl_tr"><td class="tl"><b>Cost Price</b></td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" name="price" placeholder="Price" value="{{$item->price}}" required/>
                                                        </div>
                                                      </td></tr>

                                                      @for ($i = 0; $i < count(session('compbranch')); $i++)
                                                        <tr class="tbl_tr"><td class="tl">Branch {{$i+1}} Price.</td><td class="tr">
                                                          <div class="form-group">
                                                            <input type="hidden" value="{{$bb = 'b'.$i+1}}">
                                                            <input type="number" class="form-control" name="b{{$i+1}}" placeholder="Price" value="{{$item->$bb}}" required/>
                                                          </div>
                                                        </td></tr>
                                                      @endfor

                                                      {{-- <tr class="tbl_tr"><td class="tl">Branch {{$i+1}} Price.</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="number" class="form-control" name="b{{$i+1}}" placeholder="Price" value="{{$item->bb}}" required/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Branch 2 Price.</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="number" class="form-control" name="b2" placeholder="Price" value="{{$item->b2}}" required/>
                                                        </div>
                                                      </td></tr>

                                                      <tr class="tbl_tr"><td class="tl">Branch 3 Price.</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="number" class="form-control" name="b3" placeholder="Price" value="{{$item->b3}}" required/>
                                                        </div>
                                                      </td></tr> --}}

                                                      {{-- <tr class="tbl_tr"><td class="tl">Thumbnail</td><td class="tr">
                                                        <div class="form-group">
                                                          <input type="text" class="form-control" name="thumb_img" placeholder="Thumbnail" value="{{$item->thumb_img}}"/>
                                                        </div>
                                                      </td></tr> --}}
                                                      
                                                      
                                                      {{-- <tr class="tbl_tr"><td class="tl">Image(s)</td><td class="tr">
                                                        
                                                        <div class="tmb_hold">
                                                          
                                                            <div class="row">
                                                                @for ($i = 1; $i < 5; $i++)
                                                                  <p style="display: none">{{$img = 'img'.$i}}<p>

                                                                    <div class="column cl">
                                                                      <img src="/storage/ss_imgs/{{$item->itemimage->$img}}" onclick="setValue{{$i}}()">
                                                                    </div>

                                                                        <script>
                                                                          function setValue{{$i}}() {
                                                                            // var imgVal = document.getElementById('imgVal{{$i}}').value

                                                                            var newval = document.getElementById('imgVal{{$i}}').value
                                                                            document.getElementById('tb_img').value = newval;
                                                                            alert('Click Working {{$i}} '+newval);
                                                                          }
                                                                        </script>

                                                                @endfor
                                                            </div>

                                                      </td></tr> --}}

                                                        {{-- <input type="hidden" name="photo" value="{{$item->photo}}"/> --}}

                                                    </tbody>
                                                  </table>


                                                                   

                                                </div>
                                              </div>
                                              
                                              <div class="modal-footer">
                                                <button type="submit" class="btn btn-info" name="store_action" value="update_item"><i class="fa fa-save"></i> &nbsp; Update Record</button>
                                              </div>

                                          </div>
                                    
                                        </div>
                                      </div>

                                    </form>                  
                                    
                                  </td>
                                </tr>

                                <script type="text/javascript">
                                
                                  function qty_sum{{$item->id}}() {
                                    q1 = Number(document.getElementById('q1{{$item->id}}').value);
                                    q2 = Number(document.getElementById('q2{{$item->id}}').value);
                                    q3 = Number(document.getElementById('q3{{$item->id}}').value);
                                    qty = document.getElementById('qty{{$item->id}}');
                                    qty.value = q1 + q2 + q3;
                                  }
                                  
                                </script>
                              
                              @endif

                            @endforeach

                          </tbody>
                        </table>
                        <p>Total : <b style="color: #000000">{{count($items)}}</b></p>

                        {{-- {{ Auth::user()->name }}
                        {{ auth()->user()->email }}

                        @foreach ($ITM as $IT)
                          <p>{{$IT->item_id}} - {{$IT->item->name}}</p>
                        @endforeach

                        @foreach ($items as $item)
                          <p>{{$item->name}} - {{$item->itemimage->item_id}}</p>
                        @endforeach--}}

                         {{ $items->appends(['itemsearch' => request()->query('itemsearch')])->links() }} 

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
          url : '{{URL::to('searchstudent')}}',
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