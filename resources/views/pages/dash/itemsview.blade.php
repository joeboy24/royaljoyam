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
      <li class="nav-item active2">
        <a class="nav-link" href="/items">
          <i class="fa fa-archive"></i>
          <p>Inventory</p>
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
                      @if ($showRecycle)
                        <input type="hidden" name="recycle" value="1">
                      @endif
                      <div class="input-group no-border">
                        {{-- <input type="text" value="" class="form-control search_field" id="search" name="search" placeholder="Search Records...">
                        <button type="submit" class="btn btn-white btn-round my_bt">
                          <i class="material-icons">search</i>
                          <div class="ripple-container"></div>
                        </button> --}}

                          <input type="search" value="{{ $itemsearch }}" class="form-control search_field" id="itemsearch" name="itemsearch" placeholder="Search Records...">
                           
                          <button type="submit" class="btn btn-white btn-round my_bt" title="Search">
                            <i class="material-icons">search</i>
                            <div class="ripple-container"></div>
                          </button>

                          <a href="{{ $showRecycle ? url('/items?recycle=1') : url('/items') }}" class="refresh_a" title="Clear search"><button type="button" class="btn btn-success btn-round" id="mb">
                            <i class="fa fa-refresh"></i>
                            <div class="ripple-container"></div>
                          </button></a>
                          
                      </div>
                    </form>
                      
                  </div>
                  <div class="col-md-7 offset-md-0 myTrim inventory-toolbar-actions">
                    @unless ($showRecycle)
                      <button type="button" class="btn btn-info pull-right" data-toggle="modal" data-target="#addItemModal" title="Add Item">
                        <i class="fa fa-plus"></i> Add Item
                      </button>
                      <a href="{{ url('/items?recycle=1') }}"><button type="button" class="btn btn-white pull-right" title="Recycle Bin"><i class="fa fa-trash"></i></button></a>
                    @endunless
                    <a href="{{ $showRecycle ? url('/items') : url('/dashuser') }}"><button type="button" class="btn btn-white pull-right" title="{{ $showRecycle ? 'Back to Inventory' : 'Registry' }}"><i class="fa fa-arrow-left"></i></button></a>
                  </div>

                </div>

              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">{{ $showRecycle ? 'Recycle Bin' : 'Inventory' }}</h4>
                  <p class="card-category" style="color: rgba(255,255,255,0.8);">
                    @if ($showRecycle)
                      Deleted inventory items can be restored here.
                    @else
                      Inventory records are managed separately from registry and configuration.
                    @endif
                  </p>
                </div>
                <div id="printarea1" class="card-body">
            
                    @if (count($items) > 0)
                        <!-- @unless ($showRecycle)
                          <p class="stock-badge-legend mb-3">
                            <span class="stock-badge stock-badge-ok">In stock</span> (above {{ $lowStockThreshold }})
                            <span class="stock-badge stock-badge-low">Low stock</span> (1-{{ $lowStockThreshold }})
                            <span class="stock-badge stock-badge-out">Out of stock</span> (0)
                          </p>
                        @endunless -->
                        <table class="table mt">
                          <thead class=" text-secondary hideMe">
                            <th>#</th>
                            <th>Item No.</th>
                            <th>Name</th>
                            {{-- <th>Description</th> --}}
                            <th>Category</th>
                            {{-- <th>Barcode</th> --}}
                            <th>Total Qty.</th>
                            <th>Base Price (Gh₵)</th>
                            {{-- <th>Thumbnail</th> --}}
                            <th>Date</th>
                            <th class="ryt">Actions</th>
                          </thead>
                          <tbody id="tb">

                            @foreach ($items as $item)

                                @php $rowClass = ($c % 2 == 0) ? 'rowColour' : ''; @endphp
                                <tr class="{{ trim($rowClass . (count(session('compbranch')) > 0 ? ' item-row-expandable' : '')) }}"
                                    @if (count(session('compbranch')) > 0)
                                    id="item-row-{{ $item->id }}"
                                    data-item-id="{{ $item->id }}"
                                    aria-expanded="false"
                                    aria-controls="branch-detail-{{ $item->id }}"
                                    @endif
                                >
                                  <td>
                                    @if (count(session('compbranch')) > 0)
                                      <i class="fa fa-chevron-down item-row-chevron" id="branch-icon-{{ $item->id }}"></i>
                                    @endif
                                    {{ $c++ }}
                                  </td>
                                  <td>{{ $item->item_no }}</td>
                                  <td>{{$item->name}}</td>
                                  {{-- <td>{{$item->desc}}</td> --}}
                                  <td>{{$item->cat}}</td>
                                  {{-- <td>{{$item->barcode}}</td> --}}
                                  <td>
                                    <b style="font-weight: 600">{{ $item->qty }}</b>
                                    <span class="stock-badge stock-badge-{{ $item->stockLevel($lowStockThreshold) }}">{{ $item->stockBadgeLabel($lowStockThreshold) }}</span>
                                  </td>
                                  <td><b style="font-weight: 600">{{ number_format((float) $item->price, 2) }}</b></td>
                                  {{-- <td>{{$item->thumb_img}}</td> --}}
                                  <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d M Y') : '—' }}</td>
                                  <td class="ryt item-row-actions">
                                    @if ($showRecycle)
                                      <form action="{{ action('ItemsController@update', $item->id) }}" method="POST" class="item-restore-form" onclick="event.stopPropagation()">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" name="store_action" value="restore_item" rel="tooltip" title="Restore Item" class="close2 color10" onclick="return confirm('Restore this item to inventory?');"><i class="fa fa-reply"></i></button>
                                      </form>
                                    @else
                                      <form action="{{ action('ItemsController@update', $item->id) }}" method="POST" class="item-delete-form" onclick="event.stopPropagation()">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" name="store_action" value="del_item" rel="tooltip" title="Delete Item" class="close2" onclick="return confirm('Are you sure you want to delete selected item?');"><i class="fa fa-close"></i></button>
                                      </form>
                                      <button type="button" title="Edit Record" class="print_black item-edit-btn" data-target="#edit_{{ $item->id }}" onclick="event.stopPropagation(); openItemEditModal('edit_{{ $item->id }}');">&nbsp;<i class="fa fa-pencil"></i>&nbsp;</button>
                                    @endif
                                  </td>
                                </tr>

                                @if (count(session('compbranch')) > 0)
                                <tr class="branch-detail-row" id="branch-detail-{{ $item->id }}" style="display: none;">
                                  <td colspan="8">
                                    <table class="table table-sm branch-breakdown-table mb-0">
                                      <thead>
                                        <tr>
                                          <th>Branch</th>
                                          <th>Qty</th>
                                          <th>Price (Gh₵)</th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        @for ($i = 0; $i < count(session('compbranch')); $i++)
                                          @php
                                            $branch = session('compbranch')[$i];
                                            $qField = 'q'.($i + 1);
                                            $bField = 'b'.($i + 1);
                                            $branchQty = (int) ($item->$qField ?? 0);
                                            $branchPrice = $item->$bField ?? 0;
                                          @endphp
                                          <tr>
                                            <td>{{ $branch->name }}</td>
                                            <td class="{{ $branchQty === 0 ? 'branch-qty-zero' : '' }}">{{ $branchQty }}</td>
                                            <td>{{ ($branchPrice !== '' && $branchPrice != 0) ? number_format((float) $branchPrice, 2) : '—' }}</td>
                                          </tr>
                                        @endfor
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                                @endif

                            @endforeach

                          </tbody>
                        </table>

                        @unless ($showRecycle)
                        @foreach ($items as $item)
                            <div class="modal fade item-edit-modal" id="edit_{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $item->id }}" aria-hidden="true">
                              <div class="modal-dialog modtop" role="document">
                                <div class="modal-content">
                                  <form action="{{ action('ItemsController@update', $item->id) }}" method="POST" enctype="multipart/form-data" data-item-id="{{ $item->id }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="card card-profile">
                                      <div class="card-avatar">
                                        <img class="img" src="/storage/rjv_items/{{ $item->thumb_img }}" alt="{{ $item->name }}" />
                                      </div>
                                      <div class="card-body">
                                        <h4 class="card-category text-gray">Item No: {{ $item->item_no }}</h4>
                                        <h6 class="card-title">Created by: {{ $item->user->name }}</h6>

                                        <table class="user_view_tbl">
                                          <tbody>
                                            <tr class="tbl_tr"><td class="tl">Item Name</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="name" placeholder="Item Name" value="{{ $item->name }}" required/>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Description</td><td class="tr">
                                              <div class="form-group">
                                                <textarea class="form-control" rows="4" name="desc" placeholder="Description" required>{{ $item->desc }}</textarea>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Category</td><td class="tr">
                                              <div class="form-group">
                                                <select name="cat" class="form-control">
                                                  <option selected>{{ $item->cat }}</option>
                                                  @foreach ($cats as $cat)
                                                    @if ($cat->del != 'yes' && $cat->name != $item->cat)
                                                      <option>{{ $cat->name }}</option>
                                                    @endif
                                                  @endforeach
                                                </select>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Brand</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="brand" placeholder="Brand" value="{{ $item->brand }}"/>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl">Barcode</td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" placeholder="Barcode" name="barcode" value="{{ $item->barcode }}"/>
                                              </div>
                                            </td></tr>

                                            <tr class="tbl_tr"><td class="tl"><b>Gen. Quantity</b></td><td class="tr">
                                              <div class="form-group">
                                                <input type="number" class="form-control" name="qty" id="qty{{ $item->id }}" placeholder="Quantity" value="{{ $item->qty }}" min="0" oninput="validateBranchQty({{ $item->id }}, {{ count(session('compbranch')) }})" required/>
                                              </div>
                                              <p class="small_p" id="branch_status_{{ $item->id }}">Branch totals must not exceed the General Quantity.</p>
                                            </td></tr>

                                            @for ($i = 0; $i < count(session('compbranch')); $i++)
                                              @php
                                                $branch = session('compbranch')[$i];
                                                $qq = 'q'.($i + 1);
                                              @endphp
                                              <tr class="tbl_tr"><td class="tl">{{ $branch->name }} Qty.</td><td class="tr">
                                                <div class="form-group">
                                                  <input type="number" class="form-control" name="q{{ $i + 1 }}" id="q{{ $i + 1 }}_{{ $item->id }}" placeholder="Quantity" value="{{ $item->$qq }}" min="0" oninput="validateBranchQty({{ $item->id }}, {{ count(session('compbranch')) }})" required/>
                                                </div>
                                              </td></tr>
                                            @endfor

                                            <tr class="tbl_tr"><td class="tl"><b>Cost Price</b></td><td class="tr">
                                              <div class="form-group">
                                                <input type="text" class="form-control" name="price" placeholder="Price" value="{{ $item->price }}" required/>
                                              </div>
                                            </td></tr>

                                            @for ($i = 0; $i < count(session('compbranch')); $i++)
                                              @php
                                                $branch = session('compbranch')[$i];
                                                $bb = 'b'.($i + 1);
                                              @endphp
                                              <tr class="tbl_tr"><td class="tl">{{ $branch->name }} Price</td><td class="tr">
                                                <div class="form-group">
                                                  <input type="number" class="form-control" name="b{{ $i + 1 }}" placeholder="Price" value="{{ $item->$bb }}" step="0.01" min="0" required/>
                                                </div>
                                              </td></tr>
                                            @endfor
                                          </tbody>
                                        </table>
                                      </div>
                                    </div>

                                    <div class="modal-footer">
                                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                      <button type="submit" class="btn btn-info" name="store_action" value="update_item"><i class="fa fa-save"></i> &nbsp; Update Record</button>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                        @endforeach
                        @endunless

                        <p class="gray_p">
                          @if ($items->total() > 0)
                            @if ($itemsearch !== '')
                              Showing <b>{{ $items->firstItem() }}-{{ $items->lastItem() }}</b> of <b>{{ $items->total() }}</b> {{ $items->total() === 1 ? 'match' : 'matches' }} ({{ $totalItemCount }} total {{ $showRecycle ? 'deleted items' : 'items' }})
                            @else
                              Showing <b>{{ $items->firstItem() }}-{{ $items->lastItem() }}</b> of <b>{{ $items->total() }}</b> {{ $showRecycle ? 'deleted items' : 'items' }}
                            @endif
                          @elseif ($itemsearch !== '')
                            No matches for <b>"{{ $itemsearch }}"</b> ({{ $totalItemCount }} total {{ $showRecycle ? 'deleted items' : 'items' }})
                          @else
                            <b>0</b> {{ $showRecycle ? 'deleted items' : 'items' }}
                          @endif
                        </p>

                        {{-- {{ Auth::user()->name }}
                        {{ auth()->user()->email }}

                        @foreach ($ITM as $IT)
                          <p>{{$IT->item_id}} - {{$IT->item->name}}</p>
                        @endforeach

                        @foreach ($items as $item)
                          <p>{{$item->name}} - {{$item->itemimage->item_id}}</p>
                        @endforeach--}}

                         {{ $items->links() }} 

                        <div style="height: 30px">
                        </div>
      

                    @else
                      <p class="gray_p">
                        @if ($itemsearch !== '')
                          No matches for <b>"{{ $itemsearch }}"</b> ({{ $totalItemCount }} total {{ $showRecycle ? 'deleted items' : 'items' }})
                        @else
                          <b>0</b> {{ $showRecycle ? 'deleted items' : 'items' }}
                        @endif
                      </p>
                    @endif
                    
                </div>
              </div>
            </div>
          </div>
        </div>

  </div>

  <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modtop" role="document">
      <div class="modal-content">
        <form action="{{ action('ItemsController@store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="return_to" value="items">

          <div class="modal-header">
            <h5 class="modal-title" id="addItemModalLabel"><i class="fa fa-plus-circle"></i>&nbsp;&nbsp; Add Stock Item</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <div class="form-group">
              <input type="text" class="form-control" name="name" placeholder="Item Name" required/>
            </div>

            <div class="form-group">
              <textarea name="desc" class="form-control" rows="3" placeholder="Item Description" required></textarea>
            </div>

            <div class="form-group">
              <label class="col-form-label">Category</label>
              <select name="cat" class="form-control" required>
                @foreach ($cats as $cat)
                  @if ($cat->del != 'yes')
                    <option>{{ $cat->name }}</option>
                  @endif
                @endforeach
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

            <div class="form-group">
              <input type="number" class="form-control" min="0" step="0.01" name="price" placeholder="Cost Price" required/>
            </div>

            <div class="form-group">
              <label class="upfiles">Upload Image(s):</label>
              <input type="file" name="items[]" multiple>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-info" name="store_action" value="add_item"><i class="fa fa-save"></i> &nbsp; Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>


@endsection

@section('footer')

<style>
  .inventory-toolbar-actions .btn.pull-right {
    margin-left: 8px;
  }
  .stock-badge {
    display: inline-block;
    font-size: 10px;
    font-weight: 600;
    line-height: 1.2;
    padding: 4px 10px;
    border-radius: 999px;
    margin-left: 6px;
    vertical-align: middle;
    white-space: nowrap;
  }
  .stock-badge-ok {
    background: #e8f5e9;
    color: #2e7d32;
  }
  .stock-badge-low {
    background: #fff8e1;
    color: #f57f17;
  }
  .stock-badge-out {
    background: #ffebee;
    color: #c62828;
  }
  .stock-badge-legend {
    font-size: 12px;
    color: #666;
  }
  .stock-badge-legend .stock-badge {
    font-size: 11px;
    padding: 4px 12px;
  }
  .item-row-expandable {
    cursor: pointer;
  }
  .item-row-expandable:hover {
    background-color: rgba(156, 39, 176, 0.06) !important;
  }
  .item-row-expanded {
    background-color: rgba(156, 39, 176, 0.08) !important;
  }
  .item-row-chevron {
    font-size: 11px;
    color:rgb(211, 211, 211);
    margin-right: 4px;
  }
  .branch-detail-row td {
    padding-top: 0 !important;
    padding-bottom: 12px !important;
    border-top: none !important;
    border-bottom: 1px solid rgb(231, 231, 231) !important;
    background: #fff !important;
  }
  .branch-breakdown-table {
    background: transparent;
    border-radius: 0;
  }
  .branch-breakdown-table th,
  .branch-breakdown-table td {
    background: transparent !important;
    border-top: none;
  }
  .branch-breakdown-table th {
    font-size: 12px;
    font-weight: 600;
    color: #666;
  }
  .branch-breakdown-table td {
    font-size: 13px;
  }
  .branch-qty-zero {
    color: #999;
  }
  .item-row-actions .item-delete-form,
  .item-row-actions .item-restore-form {
    display: inline;
  }
</style>

<script type="text/javascript">
  function validateBranchQty(itemId, branchCount) {
    var generalQty = Number(document.getElementById('qty' + itemId).value || 0);
    var branchTotal = 0;
    var status = document.getElementById('branch_status_' + itemId);

    for (var i = 1; i <= branchCount; i++) {
      branchTotal += Number(document.getElementById('q' + i + '_' + itemId).value || 0);
    }

    if (!status) {
      return branchTotal <= generalQty;
    }

    if (branchTotal > generalQty) {
      status.textContent = 'Branch totals exceed General Quantity.';
      status.style.color = '#d9534f';
    } else {
      status.textContent = 'Branch totals are within General Quantity.';
      status.style.color = '#28a745';
    }

    return branchTotal <= generalQty;
  }

  function toggleBranchDetail(itemId) {
    var row = document.getElementById('branch-detail-' + itemId);
    var itemRow = document.getElementById('item-row-' + itemId);
    var icon = document.getElementById('branch-icon-' + itemId);
    if (!row) {
      return;
    }

    var isHidden = row.style.display === 'none' || row.style.display === '';

    row.style.display = isHidden ? 'table-row' : 'none';
    if (itemRow) {
      itemRow.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
      itemRow.classList.toggle('item-row-expanded', isHidden);
    }
    if (icon) {
      icon.classList.toggle('fa-chevron-down', !isHidden);
      icon.classList.toggle('fa-chevron-up', isHidden);
    }
  }

  function openItemEditModal(modalId) {
    $('#' + modalId).modal('show');
  }

  $(document).on('submit', '.item-edit-modal form', function(e) {
    var itemId = this.getAttribute('data-item-id');
    var branchCount = {{ count(session('compbranch')) }};

    if (itemId && !validateBranchQty(itemId, branchCount)) {
      e.preventDefault();
      alert('Sum of branch quantities cannot exceed General Quantity.');
    }
  });

  $('#tb').on('click', 'tr.item-row-expandable', function(e) {
    if ($(e.target).closest('.item-row-actions').length) {
      return;
    }
    toggleBranchDetail(this.getAttribute('data-item-id'));
  });

  $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

@endsection