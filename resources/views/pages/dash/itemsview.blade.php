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

                  <div class="col-md-8 offset-md-0 myTrim">

                    <form method="GET" action="{{ url('/items') }}" class="inventory-filter-form">
                      @if ($showRecycle)
                        <input type="hidden" name="recycle" value="1">
                      @endif

                      <div class="inventory-filter-row">
                        <div class="inventory-search-panel">
                          <div class="inventory-search-heading">
                            <span class="inventory-search-label">
                              <i class="material-icons">search</i>
                              Search
                              @if ($itemsearch !== '')
                                <span class="inventory-search-active-dot" title="Search active"></span>
                              @endif
                            </span>
                          </div>

                          <div class="inventory-search-controls">
                            <label class="inventory-search-field">
                              <span class="inventory-search-field-icon"><i class="fa fa-search"></i></span>
                              <input type="search" value="{{ $itemsearch }}" class="inventory-search-input" id="itemsearch" name="itemsearch" placeholder="Search by name...">
                            </label>

                            <button type="submit" class="inventory-search-btn inventory-search-btn-primary" title="Search">
                              <i class="material-icons">search</i>
                              <span>Search</span>
                            </button>

                            <a href="{{ $showRecycle ? url('/items?recycle=1') : url('/items') }}" class="inventory-search-btn inventory-search-btn-clear" title="Clear filters">
                              <i class="fa fa-refresh"></i>
                              <span>Clear</span>
                            </a>
                          </div>
                        </div>

                        @php
                          $activeFilterCount = ($filterCategory !== '' ? 1 : 0)
                            + ($filterStock !== '' ? 1 : 0)
                            + ($perPage !== 10 ? 1 : 0);
                        @endphp

                        <div class="inventory-filters-panel">
                          <div class="inventory-filters-heading">
                            <span class="inventory-filters-label">
                              <i class="fa fa-filter"></i>
                              Filters
                              @if ($activeFilterCount > 0)
                                <span class="inventory-filters-count">{{ $activeFilterCount }}</span>
                              @endif
                            </span>
                          </div>

                          <div class="inventory-filters-controls">
                            <label class="inventory-filter-field">
                              <span class="inventory-filter-field-icon"><i class="fa fa-folder-open"></i></span>
                              <select name="category" class="inventory-filter-select" onchange="this.form.submit()" title="Filter by category">
                                <option value="">All categories</option>
                                @foreach ($filterCategories as $categoryName)
                                  <option value="{{ $categoryName }}" @if ($filterCategory === $categoryName) selected @endif>{{ $categoryName }}</option>
                                @endforeach
                              </select>
                            </label>

                            @unless ($showRecycle)
                              <label class="inventory-filter-field">
                                <span class="inventory-filter-field-icon"><i class="fa fa-signal"></i></span>
                                <select name="stock" class="inventory-filter-select" onchange="this.form.submit()" title="Filter by stock status">
                                  <option value="">All stock levels</option>
                                  <option value="low" @if ($filterStock === 'low') selected @endif>Low / out of stock</option>
                                  <option value="has_branch" @if ($filterStock === 'has_branch') selected @endif>Has branch stock</option>
                                </select>
                              </label>
                            @endunless

                            <label class="inventory-filter-field inventory-filter-field-compact">
                              <span class="inventory-filter-field-icon"><i class="fa fa-list-ol"></i></span>
                              <select name="per_page" class="inventory-filter-select inventory-per-page-select" onchange="this.form.submit()" title="Rows per page">
                                @foreach ([10, 25, 50] as $pageSize)
                                  <option value="{{ $pageSize }}" @if ($perPage === $pageSize) selected @endif>{{ $pageSize }} / page</option>
                                @endforeach
                              </select>
                            </label>
                          </div>
                        </div>
                      </div>
                    </form>

                  </div>
                  <div class="col-md-4 offset-md-0 myTrim inventory-toolbar-actions">
                    <div class="inventory-actions-group">
                      <a href="{{ $showRecycle ? url('/items') : url('/dashuser') }}" class="inventory-action-btn inventory-action-btn-icon inventory-tip" data-tip="{{ $showRecycle ? 'Back to inventory' : 'Registry' }}">
                        <i class="fa fa-arrow-left"></i>
                      </a>
                      @unless ($showRecycle)
                        <a href="{{ url('/items?recycle=1') }}" class="inventory-action-btn inventory-action-btn-icon inventory-tip" data-tip="Recycle bin">
                          <i class="fa fa-trash"></i>
                        </a>
                        <button type="button" class="inventory-action-btn inventory-action-btn-primary inventory-tip" data-tip="Add a new stock item" data-toggle="modal" data-target="#addItemModal">
                          <i class="fa fa-plus"></i>
                          <span>Add item</span>
                        </button>
                      @endunless
                    </div>
                  </div>

                </div>

              <div class="card">
                <div class="card-header card-header-primary inventory-card-header">
                  <div class="inventory-card-header-main">
                    <div class="inventory-card-title-row">
                      <span class="inventory-card-icon">
                        <i class="fa {{ $showRecycle ? 'fa-trash' : 'fa-archive' }}"></i>
                      </span>
                      <div>
                        <h4 class="card-title inventory-card-title">{{ $showRecycle ? 'Recycle Bin' : 'Inventory' }}</h4>
                        <p class="inventory-card-subtitle">
                          @if ($showRecycle)
                            Deleted inventory items can be restored here.
                          @else
                            Inventory records are managed separately from registry and configuration.
                          @endif
                        </p>
                      </div>
                    </div>
                  </div>
                  @if (count(session('compbranch')) > 0 && ! $showRecycle)
                    <div class="inventory-branch-controls inventory-header-actions">
                      <button type="button" class="inventory-branch-control-btn" id="toggleAllBranches" aria-expanded="false">
                        <i class="fa fa-angle-double-down"></i>
                        <span>Expand all</span>
                      </button>
                    </div>
                  @endif
                </div>
                <div id="printarea1" class="card-body">
            
                    @if (count($items) > 0)
                        <table class="table inventory-table mt-0">
                          <thead class="inventory-table-head hideMe">
                            <tr>
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
                            </tr>
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
                                    <div class="inventory-row-actions">
                                      @if ($showRecycle)
                                        <form action="{{ action('ItemsController@update', $item->id) }}" method="POST" class="item-restore-form" onclick="event.stopPropagation()">
                                          @csrf
                                          @method('PUT')
                                          <button type="submit" name="store_action" value="restore_item" class="inventory-row-action-btn inventory-row-action-btn-restore inventory-tip" data-tip="Restore item" onclick="return confirm('Restore this item to inventory?');"><i class="fa fa-reply"></i></button>
                                        </form>
                                      @else
                                        <button type="button" class="inventory-row-action-btn inventory-row-action-btn-edit item-edit-btn inventory-tip" data-tip="Edit record" data-target="#edit_{{ $item->id }}" onclick="event.stopPropagation(); openItemEditModal('edit_{{ $item->id }}');"><i class="fa fa-pencil"></i></button>
                                        <form action="{{ action('ItemsController@update', $item->id) }}" method="POST" class="item-delete-form" onclick="event.stopPropagation()">
                                          @csrf
                                          @method('PUT')
                                          <button type="submit" name="store_action" value="del_item" class="inventory-row-action-btn inventory-row-action-btn-delete inventory-tip" data-tip="Delete item" onclick="return confirm('Are you sure you want to delete selected item?');"><i class="fa fa-close"></i></button>
                                        </form>
                                      @endif
                                    </div>
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
                              <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
                                <div class="modal-content inventory-edit-modal">
                                  <form action="{{ action('ItemsController@update', $item->id) }}" method="POST" enctype="multipart/form-data" data-item-id="{{ $item->id }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="inventory-edit-header">
                                      <div class="inventory-edit-header-inner">
                                        <img class="inventory-edit-thumb" src="/storage/rjv_items/{{ $item->thumb_img }}" alt="{{ $item->name }}" />
                                        <div class="inventory-edit-header-text">
                                          <span class="inventory-edit-kicker">Edit item</span>
                                          <h4 class="inventory-edit-title" id="editModalLabel{{ $item->id }}">{{ $item->name }}</h4>
                                          <p class="inventory-edit-meta">Item No. {{ $item->item_no }} &middot; Created by {{ $item->user->name }}</p>
                                        </div>
                                      </div>
                                      <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
                                        <i class="material-icons">close</i>
                                      </button>
                                    </div>

                                    <div class="inventory-edit-body">
                                      <div class="inventory-edit-columns">
                                        <div class="inventory-edit-col">
                                          <h6 class="inventory-edit-section-title"><i class="fa fa-info-circle"></i> Details</h6>

                                          <label class="inventory-edit-field">
                                            <span class="inventory-edit-label">Item name</span>
                                            <input type="text" class="inventory-edit-input" name="name" placeholder="Item name" value="{{ $item->name }}" required/>
                                          </label>

                                          <label class="inventory-edit-field">
                                            <span class="inventory-edit-label">Description</span>
                                            <textarea class="inventory-edit-input inventory-edit-textarea" rows="3" name="desc" placeholder="Description" required>{{ $item->desc }}</textarea>
                                          </label>

                                          <label class="inventory-edit-field">
                                            <span class="inventory-edit-label">Category</span>
                                            <select name="cat" class="inventory-edit-input inventory-edit-select">
                                              <option selected>{{ $item->cat }}</option>
                                              @foreach ($cats as $cat)
                                                @if ($cat->del != 'yes' && $cat->name != $item->cat)
                                                  <option>{{ $cat->name }}</option>
                                                @endif
                                              @endforeach
                                            </select>
                                          </label>

                                          <div class="inventory-edit-field-row">
                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Brand</span>
                                              <input type="text" class="inventory-edit-input" name="brand" placeholder="Brand" value="{{ $item->brand }}"/>
                                            </label>
                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Barcode</span>
                                              <input type="text" class="inventory-edit-input" name="barcode" placeholder="Barcode" value="{{ $item->barcode }}"/>
                                            </label>
                                          </div>
                                        </div>

                                        <div class="inventory-edit-col">
                                          <h6 class="inventory-edit-section-title"><i class="fa fa-cubes"></i> Stock</h6>

                                          <label class="inventory-edit-field">
                                            <span class="inventory-edit-label">General quantity</span>
                                            <input type="number" class="inventory-edit-input" name="qty" id="qty{{ $item->id }}" placeholder="Quantity" value="{{ $item->qty }}" min="0" oninput="validateBranchQty({{ $item->id }}, {{ count(session('compbranch')) }})" required/>
                                          </label>
                                          <p class="inventory-edit-hint" id="branch_status_{{ $item->id }}">Branch totals must not exceed the general quantity.</p>

                                          @if (count(session('compbranch')) > 0)
                                            <div class="inventory-edit-branch-grid">
                                              @for ($i = 0; $i < count(session('compbranch')); $i++)
                                                @php
                                                  $branch = session('compbranch')[$i];
                                                  $qq = 'q'.($i + 1);
                                                @endphp
                                                <label class="inventory-edit-field inventory-edit-field-compact">
                                                  <span class="inventory-edit-label">{{ $branch->name }}</span>
                                                  <input type="number" class="inventory-edit-input" name="q{{ $i + 1 }}" id="q{{ $i + 1 }}_{{ $item->id }}" placeholder="Qty" value="{{ $item->$qq }}" min="0" oninput="validateBranchQty({{ $item->id }}, {{ count(session('compbranch')) }})" required/>
                                                </label>
                                              @endfor
                                            </div>
                                          @endif

                                          <h6 class="inventory-edit-section-title inventory-edit-section-title-spaced"><i class="fa fa-money"></i> Pricing</h6>

                                          <label class="inventory-edit-field">
                                            <span class="inventory-edit-label">Cost price (Gh₵)</span>
                                            <input type="text" class="inventory-edit-input" name="price" placeholder="Price" value="{{ $item->price }}" required/>
                                          </label>

                                          @if (count(session('compbranch')) > 0)
                                            <div class="inventory-edit-branch-grid">
                                              @for ($i = 0; $i < count(session('compbranch')); $i++)
                                                @php
                                                  $branch = session('compbranch')[$i];
                                                  $bb = 'b'.($i + 1);
                                                @endphp
                                                <label class="inventory-edit-field inventory-edit-field-compact">
                                                  <span class="inventory-edit-label">{{ $branch->name }} price</span>
                                                  <input type="number" class="inventory-edit-input" name="b{{ $i + 1 }}" placeholder="Price" value="{{ $item->$bb }}" step="0.01" min="0" required/>
                                                </label>
                                              @endfor
                                            </div>
                                          @endif
                                        </div>
                                      </div>
                                    </div>

                                    <div class="inventory-edit-footer">
                                      <button type="button" class="inventory-edit-btn inventory-edit-btn-muted" data-dismiss="modal">Cancel</button>
                                      <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" name="store_action" value="update_item">
                                        <i class="fa fa-save"></i> Update record
                                      </button>
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
                              Showing <b>{{ $items->firstItem() }}-{{ $items->lastItem() }}</b> of <b>{{ $items->total() }}</b> {{ $items->total() === 1 ? 'match' : 'matches' }} ({{ $grandTotalCount }} total {{ $showRecycle ? 'deleted items' : 'items' }})
                            @else
                              Showing <b>{{ $items->firstItem() }}-{{ $items->lastItem() }}</b> of <b>{{ $items->total() }}</b> {{ $showRecycle ? 'deleted items' : 'items' }}
                            @endif
                          @elseif ($itemsearch !== '')
                            No matches for <b>"{{ $itemsearch }}"</b> ({{ $grandTotalCount }} total {{ $showRecycle ? 'deleted items' : 'items' }})
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
                          No matches for <b>"{{ $itemsearch }}"</b> ({{ $grandTotalCount }} total {{ $showRecycle ? 'deleted items' : 'items' }})
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
    <div class="modal-dialog inventory-edit-dialog inventory-add-dialog modal-dialog-centered" role="document">
      <div class="modal-content inventory-edit-modal inventory-add-modal">
        <form action="{{ action('ItemsController@store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="return_to" value="items">

          <div class="inventory-edit-header">
            <div class="inventory-edit-header-inner">
              <div class="inventory-edit-thumb inventory-edit-thumb-placeholder">
                <i class="fa fa-plus"></i>
              </div>
              <div class="inventory-edit-header-text">
                <span class="inventory-edit-kicker">New item</span>
                <h4 class="inventory-edit-title" id="addItemModalLabel">Add stock item</h4>
                <p class="inventory-edit-meta">Create a new inventory record for the catalogue.</p>
              </div>
            </div>
            <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
          </div>

          <div class="inventory-edit-body">
            <h6 class="inventory-edit-section-title"><i class="fa fa-info-circle"></i> Details</h6>

            <label class="inventory-edit-field">
              <span class="inventory-edit-label">Item name</span>
              <input type="text" class="inventory-edit-input" name="name" placeholder="Item name" required/>
            </label>

            <label class="inventory-edit-field">
              <span class="inventory-edit-label">Description</span>
              <textarea name="desc" class="inventory-edit-input inventory-edit-textarea" rows="3" placeholder="Item description" required></textarea>
            </label>

            <div class="inventory-edit-field-row inventory-add-field-row-3">
              <label class="inventory-edit-field">
                <span class="inventory-edit-label">Category</span>
                <select name="cat" class="inventory-edit-input inventory-edit-select" required>
                  @foreach ($cats as $cat)
                    @if ($cat->del != 'yes')
                      <option>{{ $cat->name }}</option>
                    @endif
                  @endforeach
                </select>
              </label>
              <label class="inventory-edit-field">
                <span class="inventory-edit-label">Brand</span>
                <input type="text" class="inventory-edit-input" name="brand" placeholder="Brand / manufacturer"/>
              </label>
              <label class="inventory-edit-field">
                <span class="inventory-edit-label">Barcode</span>
                <input type="text" class="inventory-edit-input" name="barcode" placeholder="Barcode"/>
              </label>
            </div>

            <h6 class="inventory-edit-section-title inventory-edit-section-title-spaced"><i class="fa fa-cubes"></i> Stock &amp; pricing</h6>

            <div class="inventory-edit-field-row">
              <label class="inventory-edit-field">
                <span class="inventory-edit-label">Quantity</span>
                <input type="number" class="inventory-edit-input" min="0" name="qty" placeholder="Quantity" required/>
              </label>
              <label class="inventory-edit-field">
                <span class="inventory-edit-label">Cost price (Gh₵)</span>
                <input type="number" class="inventory-edit-input" min="0" step="0.01" name="price" placeholder="Cost price" required/>
              </label>
            </div>

            <h6 class="inventory-edit-section-title inventory-edit-section-title-spaced"><i class="fa fa-image"></i> Images</h6>

            <label class="inventory-edit-field inventory-edit-file-field">
              <span class="inventory-edit-label">Upload image(s)</span>
              <input type="file" name="items[]" multiple class="inventory-edit-file">
              <span class="inventory-edit-file-hint">Optional — you can add one or more product photos.</span>
            </label>
          </div>

          <div class="inventory-edit-footer">
            <button type="button" class="inventory-edit-btn inventory-edit-btn-muted" data-dismiss="modal">Cancel</button>
            <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" name="store_action" value="add_item">
              <i class="fa fa-save"></i> Save item
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>


@endsection

@section('footer')

<style>
  .content,
  #addItemModal,
  .item-edit-modal {
    --inv-accent: #00acc1;
    --inv-accent-dark: #0097a7;
    --inv-accent-rgb: 0, 172, 193;
  }
  .inventory-filter-row {
    display: flex;
    flex-wrap: wrap;
    align-items: stretch;
    gap: 10px;
  }
  .inventory-search-panel,
  .inventory-filters-panel {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 10px 14px;
    padding: 10px 14px;
    background: linear-gradient(180deg, #ffffff 0%, #f3fafb 100%);
    border: 1px solid rgba(var(--inv-accent-rgb), 0.18);
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(var(--inv-accent-rgb), 0.08);
  }
  .inventory-search-panel {
    flex: 1 1 320px;
    min-width: 300px;
  }
  .inventory-search-heading,
  .inventory-filters-heading {
    flex: 0 0 auto;
  }
  .inventory-search-label,
  .inventory-filters-label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--inv-accent);
    white-space: nowrap;
  }
  .inventory-search-label .material-icons,
  .inventory-filters-label .fa-filter {
    font-size: 15px;
  }
  .inventory-search-active-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: var(--inv-accent);
    box-shadow: 0 0 0 3px rgba(var(--inv-accent-rgb), 0.18);
  }
  .inventory-search-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    flex: 1 1 auto;
  }
  .inventory-search-field {
    position: relative;
    margin: 0;
    flex: 1 1 180px;
    min-width: 160px;
  }
  .inventory-search-field-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--inv-accent);
    font-size: 12px;
    pointer-events: none;
    z-index: 1;
    opacity: 0.85;
  }
  .inventory-search-input {
    width: 100%;
    height: 38px;
    padding: 0 14px 0 34px;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 999px;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .inventory-search-input:hover {
    border-color: rgba(var(--inv-accent-rgb), 0.35);
  }
  .inventory-search-input:focus {
    outline: none;
    border-color: var(--inv-accent);
    box-shadow: 0 0 0 3px rgba(var(--inv-accent-rgb), 0.12);
  }
  .inventory-search-field:focus-within .inventory-search-field-icon {
    opacity: 1;
  }
  .inventory-search-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 38px;
    padding: 0 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
    text-decoration: none !important;
    border: 1px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .inventory-search-btn .material-icons {
    font-size: 16px;
  }
  .inventory-search-btn-primary {
    background: var(--inv-accent);
    color: #fff;
    box-shadow: 0 2px 6px rgba(var(--inv-accent-rgb), 0.24);
  }
  .inventory-search-btn-primary:hover {
    background: var(--inv-accent-dark);
    color: #fff;
  }
  .inventory-search-btn-clear {
    background: #fff;
    color: #2e7d32;
    border-color: rgba(46, 125, 50, 0.25);
  }
  .inventory-search-btn-clear:hover {
    background: #f1f8f2;
    color: #1b5e20;
    border-color: rgba(46, 125, 50, 0.4);
  }
  .inventory-filters-panel {
    flex: 1 1 420px;
  }
  .inventory-filters-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 999px;
    background: var(--inv-accent);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    line-height: 1;
  }
  .inventory-filters-controls {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    flex: 1 1 auto;
  }
  .inventory-filter-field {
    position: relative;
    margin: 0;
    flex: 1 1 150px;
    min-width: 140px;
    max-width: 190px;
  }
  .inventory-filter-field-compact {
    flex: 0 1 120px;
    min-width: 110px;
    max-width: 120px;
  }
  .inventory-filter-field-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--inv-accent);
    font-size: 12px;
    pointer-events: none;
    z-index: 1;
    opacity: 0.85;
  }
  .inventory-filter-select {
    width: 100%;
    height: 38px;
    padding: 0 30px 0 34px;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    background-color: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 999px;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%2300acc1' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    cursor: pointer;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .inventory-filter-select:hover {
    border-color: rgba(var(--inv-accent-rgb), 0.35);
  }
  .inventory-filter-select:focus {
    outline: none;
    border-color: var(--inv-accent);
    box-shadow: 0 0 0 3px rgba(var(--inv-accent-rgb), 0.12);
  }
  .inventory-filter-field:focus-within .inventory-filter-field-icon {
    opacity: 1;
  }
  .inventory-toolbar-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }
  .inventory-actions-group {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 8px;
  }
  .inventory-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 38px;
    padding: 0 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
    text-decoration: none !important;
    border: 1px solid #e0e0e0;
    background: #fff;
    color: #666;
    cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
  }
  .inventory-action-btn:hover {
    background: #f8f8f8;
    color: #444;
    border-color: rgba(var(--inv-accent-rgb), 0.25);
  }
  .inventory-action-btn-icon {
    width: 38px;
    padding: 0;
    font-size: 14px;
  }
  .inventory-action-btn-primary {
    background: var(--inv-accent, #00acc1);
    color: #fff;
    border-color: transparent;
    box-shadow: 0 2px 6px rgba(var(--inv-accent-rgb, 0, 172, 193), 0.24);
  }
  .inventory-action-btn-primary:hover {
    background: var(--inv-accent-dark, #0097a7);
    color: #fff;
    border-color: transparent;
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
    background-color: rgba(var(--inv-accent-rgb), 0.06) !important;
  }
  .item-row-expanded {
    background-color: rgba(var(--inv-accent-rgb), 0.08) !important;
  }
  .item-row-chevron {
    font-size: 11px;
    color:rgb(211, 211, 211);
    margin-right: 4px;
  }
  .inventory-branch-controls {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 12px;
  }
  .inventory-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    flex-wrap: wrap;
    padding: 1.5rem 1.875rem 0.75rem 1rem !important;
    position: relative;
  }
  .inventory-card-title-row {
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
  }
  .inventory-card-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    margin-top: 0.125rem;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255, 255, 255, 0.28);
    color: #fff;
    font-size: 17px;
    flex-shrink: 0;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.12);
  }
  .inventory-card-header .card-title,
  .inventory-card-title {
    margin: 0 0 0.25rem;
    padding-top: 0.125rem;
    font-size: 1.25rem;
    font-weight: 600;
    line-height: 1.2;
  }
  .inventory-card-subtitle {
    margin: 0;
    max-width: 520px;
    font-size: 0.8125rem;
    line-height: 1.35;
    color: rgba(255, 255, 255, 0.82);
  }
  .inventory-card-header-main {
    flex: 1 1 auto;
    min-width: 220px;
  }
  .inventory-header-actions {
    margin-bottom: 0;
    flex: 0 0 auto;
    align-self: center;
  }
  .inventory-table {
    margin-bottom: 0;
  }
  .inventory-table-head th {
    background: #f5f5f5;
    color: #757575;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    padding: 0.875rem 0.75rem;
    border-top: none !important;
    border-bottom: 2px solid #e0e0e0 !important;
    white-space: nowrap;
  }
  .inventory-table-head th.ryt {
    text-align: right;
  }
  .inventory-table tbody td {
    vertical-align: middle;
    padding: 0.875rem 0.75rem;
  }
  #printarea1.card-body {
    padding: 0.9375rem 1.875rem;
  }
  .inventory-branch-control-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 34px;
    padding: 0 14px;
    border-radius: 999px;
    border: 1px solid rgba(var(--inv-accent-rgb, 0, 172, 193), 0.22);
    background: #fff;
    color: var(--inv-accent, #00acc1);
    font-size: 12px;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease;
  }
  .inventory-header-actions .inventory-branch-control-btn {
    height: 36px;
    padding: 0 16px;
    border-color: rgba(255, 255, 255, 0.38);
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(4px);
  }
  .inventory-branch-control-btn:hover {
    background: rgba(var(--inv-accent-rgb, 0, 172, 193), 0.08);
    border-color: var(--inv-accent, #00acc1);
    color: var(--inv-accent-dark, #0097a7);
  }
  .inventory-header-actions .inventory-branch-control-btn:hover {
    background: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.62);
    color: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
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
    display: inline-flex;
    margin: 0;
  }
  .inventory-row-actions {
    display: inline-flex;
    align-items: center;
    justify-content: flex-end;
    gap: 6px;
  }
  .inventory-row-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    padding: 0;
    border-radius: 999px;
    border: 1px solid #e0e0e0;
    background: #fff;
    font-size: 13px;
    line-height: 1;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
  }
  .inventory-row-action-btn-edit {
    color: var(--inv-accent, #00acc1);
    border-color: rgba(var(--inv-accent-rgb, 0, 172, 193), 0.22);
  }
  .inventory-row-action-btn-edit:hover {
    background: rgba(var(--inv-accent-rgb, 0, 172, 193), 0.08);
    border-color: var(--inv-accent, #00acc1);
    color: var(--inv-accent-dark, #0097a7);
  }
  .inventory-row-action-btn-delete {
    color: #c62828;
    border-color: rgba(198, 40, 40, 0.2);
  }
  .inventory-row-action-btn-delete:hover {
    background: #ffebee;
    border-color: rgba(198, 40, 40, 0.35);
    color: #b71c1c;
  }
  .inventory-row-action-btn-restore {
    color: #2e7d32;
    border-color: rgba(46, 125, 50, 0.22);
  }
  .inventory-row-action-btn-restore:hover {
    background: #e8f5e9;
    border-color: rgba(46, 125, 50, 0.35);
    color: #1b5e20;
  }
  .item-row-actions {
    overflow: visible;
    position: relative;
  }
  #tb tr:hover .item-row-actions {
    z-index: 3;
  }
  #tb td.item-row-actions {
    overflow: visible;
  }
  .inventory-tip {
    position: relative;
  }
  .inventory-tip::after {
    content: attr(data-tip);
    position: absolute;
    bottom: calc(100% + 9px);
    left: 50%;
    transform: translateX(-50%) translateY(3px);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    padding: 6px 11px;
    border-radius: 8px;
    background: #1a4a52;
    color: #fff;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.02em;
    line-height: 1.2;
    white-space: nowrap;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.16);
    transition: opacity 0.1s ease, transform 0.1s ease, visibility 0.1s ease;
    z-index: 2000;
  }
  .inventory-tip::before {
    content: '';
    position: absolute;
    bottom: calc(100% + 3px);
    left: 50%;
    transform: translateX(-50%) translateY(3px);
    border: 5px solid transparent;
    border-top-color: #1a4a52;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.1s ease, transform 0.1s ease, visibility 0.1s ease;
    z-index: 2000;
  }
  .inventory-tip:hover::after,
  .inventory-tip:focus-visible::after,
  .inventory-tip:hover::before,
  .inventory-tip:focus-visible::before {
    opacity: 1;
    visibility: visible;
    transform: translateX(-50%) translateY(0);
  }

  .inventory-edit-dialog {
    width: calc(100% - 2rem);
    max-width: 960px;
    margin: 1.75rem auto;
  }
  .inventory-add-dialog {
    max-width: 720px;
  }
  .inventory-add-field-row-3 {
    grid-template-columns: repeat(3, minmax(0, 1fr));
  }
  .inventory-add-modal .inventory-edit-body {
    max-height: calc(100vh - 220px);
  }
  .inventory-edit-modal {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 18px 48px rgba(0, 0, 0, 0.16);
  }
  .inventory-edit-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    padding: 22px 24px;
    background: linear-gradient(135deg, var(--inv-accent, #00acc1) 0%, var(--inv-accent-dark, #0097a7) 100%);
    color: #fff;
  }
  .inventory-edit-header-inner {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
  }
  .inventory-edit-thumb {
    width: 72px;
    height: 72px;
    border-radius: 14px;
    object-fit: cover;
    border: 3px solid rgba(255, 255, 255, 0.35);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    flex-shrink: 0;
    background: #fff;
  }
  .inventory-edit-thumb-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    color: var(--inv-accent);
    background: rgba(255, 255, 255, 0.95);
  }
  .inventory-edit-kicker {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    opacity: 0.9;
    margin-bottom: 4px;
  }
  .inventory-edit-title {
    margin: 0 0 6px;
    font-size: 22px;
    font-weight: 600;
    line-height: 1.25;
    color: #fff;
  }
  .inventory-edit-meta {
    margin: 0;
    font-size: 13px;
    opacity: 0.88;
  }
  .inventory-edit-close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    padding: 0;
    border: none;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.16);
    color: #fff;
    cursor: pointer;
    flex-shrink: 0;
    transition: background-color 0.15s ease;
  }
  .inventory-edit-close:hover {
    background: rgba(255, 255, 255, 0.28);
  }
  .inventory-edit-close .material-icons {
    font-size: 20px;
  }
  .inventory-edit-body {
    padding: 24px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbfc 100%);
    max-height: calc(100vh - 260px);
    overflow-y: auto;
  }
  .inventory-edit-columns {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 28px;
  }
  .inventory-edit-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 14px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--inv-accent);
  }
  .inventory-edit-section-title-spaced {
    margin-top: 22px;
  }
  .inventory-edit-section-title .fa {
    font-size: 13px;
  }
  .inventory-edit-field {
    display: block;
    margin-bottom: 14px;
  }
  .inventory-edit-field-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }
  .inventory-edit-branch-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }
  .inventory-edit-field-compact {
    margin-bottom: 0;
  }
  .inventory-edit-label {
    display: block;
    margin-bottom: 6px;
    font-size: 12px;
    font-weight: 600;
    color: #666;
  }
  .inventory-edit-input {
    display: block;
    width: 100%;
    height: 40px;
    padding: 0 14px;
    font-size: 13px;
    font-weight: 500;
    color: #333;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .inventory-edit-textarea {
    height: auto;
    min-height: 88px;
    padding: 10px 14px;
    resize: vertical;
  }
  .inventory-edit-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding-right: 34px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%2300acc1' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    cursor: pointer;
  }
  .inventory-edit-input:hover {
    border-color: rgba(var(--inv-accent-rgb), 0.35);
  }
  .inventory-edit-input:focus {
    outline: none;
    border-color: var(--inv-accent);
    box-shadow: 0 0 0 3px rgba(var(--inv-accent-rgb), 0.12);
  }
  .inventory-edit-hint {
    margin: -6px 0 14px;
    font-size: 12px;
    color: #888;
  }
  .inventory-edit-file-field {
    margin-bottom: 0;
  }
  .inventory-edit-file {
    display: block;
    width: 100%;
    padding: 10px 14px;
    font-size: 13px;
    color: #555;
    background: #fff;
    border: 1px dashed rgba(var(--inv-accent-rgb), 0.35);
    border-radius: 10px;
    cursor: pointer;
    transition: border-color 0.15s ease, background-color 0.15s ease;
  }
  .inventory-edit-file:hover {
    border-color: var(--inv-accent);
    background: rgba(var(--inv-accent-rgb), 0.04);
  }
  .inventory-edit-file:focus {
    outline: none;
    border-color: var(--inv-accent);
    box-shadow: 0 0 0 3px rgba(var(--inv-accent-rgb), 0.12);
  }
  .inventory-edit-file-hint {
    display: block;
    margin-top: 8px;
    font-size: 12px;
    color: #888;
  }
  .inventory-edit-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    padding: 16px 24px;
    background: #fff;
    border-top: 1px solid #eee;
    flex-shrink: 0;
  }
  .inventory-edit-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    height: 40px;
    padding: 0 18px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
    line-height: 1;
    border: 1px solid transparent;
    cursor: pointer;
    flex-shrink: 0;
    transition: background-color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
  }
  .inventory-edit-btn-muted {
    background: #f5f5f5;
    color: #666;
    border-color: #e0e0e0;
  }
  .inventory-edit-btn-muted:hover {
    background: #ececec;
    color: #444;
  }
  .inventory-edit-btn-primary {
    background: var(--inv-accent, #00acc1);
    color: #fff;
    box-shadow: 0 2px 8px rgba(var(--inv-accent-rgb, 0, 172, 193), 0.24);
  }
  .inventory-edit-btn-primary:hover {
    background: var(--inv-accent-dark, #0097a7);
    color: #fff;
  }
  @media (max-width: 767px) {
    .inventory-edit-dialog {
      max-width: 100%;
    }
    .inventory-edit-columns,
    .inventory-edit-field-row,
    .inventory-edit-branch-grid,
    .inventory-add-field-row-3 {
      grid-template-columns: 1fr;
    }
    .inventory-edit-header {
      padding: 18px;
    }
    .inventory-edit-body {
      padding: 18px;
    }
    .inventory-edit-footer {
      padding: 14px 18px;
      flex-wrap: wrap;
    }
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

  var INVENTORY_BRANCH_STORAGE_KEY = 'inventoryExpandedBranchIds';

  function getExpandedBranchIds() {
    try {
      var stored = sessionStorage.getItem(INVENTORY_BRANCH_STORAGE_KEY);
      return stored ? JSON.parse(stored) : [];
    } catch (e) {
      return [];
    }
  }

  function setExpandedBranchIds(ids) {
    sessionStorage.setItem(INVENTORY_BRANCH_STORAGE_KEY, JSON.stringify(ids));
  }

  function isBranchDetailExpanded(itemId) {
    return getExpandedBranchIds().indexOf(String(itemId)) !== -1;
  }

  function setBranchDetailExpanded(itemId, expanded, persist) {
    if (typeof persist === 'undefined') {
      persist = true;
    }

    var row = document.getElementById('branch-detail-' + itemId);
    var itemRow = document.getElementById('item-row-' + itemId);
    var icon = document.getElementById('branch-icon-' + itemId);
    if (!row) {
      return;
    }

    row.style.display = expanded ? 'table-row' : 'none';
    if (itemRow) {
      itemRow.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      itemRow.classList.toggle('item-row-expanded', expanded);
    }
    if (icon) {
      icon.classList.toggle('fa-chevron-down', !expanded);
      icon.classList.toggle('fa-chevron-up', expanded);
    }

    if (!persist) {
      return;
    }

    var ids = getExpandedBranchIds();
    var key = String(itemId);
    var index = ids.indexOf(key);

    if (expanded && index === -1) {
      ids.push(key);
    } else if (!expanded && index !== -1) {
      ids.splice(index, 1);
    }

    setExpandedBranchIds(ids);
  }

  function toggleBranchDetail(itemId) {
    setBranchDetailExpanded(itemId, !isBranchDetailExpanded(itemId));
    updateToggleAllBranchesButton();
  }

  function areAllVisibleBranchesExpanded() {
    var visibleIds = getVisibleExpandableItemIds();

    if (visibleIds.length === 0) {
      return false;
    }

    return visibleIds.every(function(itemId) {
      return isBranchDetailExpanded(itemId);
    });
  }

  function updateToggleAllBranchesButton() {
    var button = document.getElementById('toggleAllBranches');
    if (!button) {
      return;
    }

    var allExpanded = areAllVisibleBranchesExpanded();
    var icon = button.querySelector('i');
    var label = button.querySelector('span');

    if (allExpanded) {
      if (icon) {
        icon.className = 'fa fa-angle-double-up';
      }
      if (label) {
        label.textContent = 'Collapse all';
      }
      button.setAttribute('aria-expanded', 'true');
    } else {
      if (icon) {
        icon.className = 'fa fa-angle-double-down';
      }
      if (label) {
        label.textContent = 'Expand all';
      }
      button.setAttribute('aria-expanded', 'false');
    }
  }

  function toggleAllBranchDetails() {
    if (areAllVisibleBranchesExpanded()) {
      collapseAllBranchDetails();
    } else {
      expandAllBranchDetails();
    }

    updateToggleAllBranchesButton();
  }

  function getVisibleExpandableItemIds() {
    var ids = [];

    document.querySelectorAll('#tb tr.item-row-expandable').forEach(function(row) {
      var itemId = row.getAttribute('data-item-id');
      if (itemId) {
        ids.push(String(itemId));
      }
    });

    return ids;
  }

  function expandAllBranchDetails() {
    getVisibleExpandableItemIds().forEach(function(itemId) {
      setBranchDetailExpanded(itemId, true, false);
    });

    var ids = getExpandedBranchIds();
    getVisibleExpandableItemIds().forEach(function(itemId) {
      if (ids.indexOf(itemId) === -1) {
        ids.push(itemId);
      }
    });
    setExpandedBranchIds(ids);
  }

  function collapseAllBranchDetails() {
    var visibleIds = {};

    getVisibleExpandableItemIds().forEach(function(itemId) {
      visibleIds[itemId] = true;
      setBranchDetailExpanded(itemId, false, false);
    });

    setExpandedBranchIds(getExpandedBranchIds().filter(function(itemId) {
      return !visibleIds[itemId];
    }));
  }

  function restoreBranchDetailState() {
    getVisibleExpandableItemIds().forEach(function(itemId) {
      setBranchDetailExpanded(itemId, isBranchDetailExpanded(itemId), false);
    });
    updateToggleAllBranchesButton();
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

  $('#toggleAllBranches').on('click', toggleAllBranchDetails);
  restoreBranchDetailState();

  $.ajaxSetup({ headers: { 'csrftoken' : '{{ csrf_token() }}' } });
</script>

@endsection