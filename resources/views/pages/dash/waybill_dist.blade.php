@extends('layouts.dashlay')

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
                <x-dash-page-header
                  title="Distribution"
                  :subtitle="'Waybill '.$waybill->bill_no.' · Add items and distribute to branches.'"
                  icon="fa fa-share-alt"
                >
                  <x-slot:actions>
                    <a href="/waybillview" class="inventory-action-btn inventory-action-btn-primary dash-tip" data-tip="Back to waybill history">
                      <i class="fa fa-arrow-left"></i>
                      <span>Back to waybills</span>
                    </a>
                  </x-slot:actions>
                </x-dash-page-header>
                <div id="printarea1" class="card-body dash-form-body">

                  <div class="dist-add-item-panel">
                    <form action="{{ action('ItemsController@store') }}" method="POST" class="dist-add-item-form" id="distAddItemForm">
                      @csrf
                      <input type="hidden" name="wb_id" value="{{ $wb_id }}">

                      <div class="dist-add-item-row">
                        <div class="dist-item-picker" id="distItemPicker">
                          <label class="inventory-edit-field dist-item-field">
                            <span class="inventory-edit-label">Item</span>
                            <div class="dist-item-search-wrap">
                              <span class="inventory-search-field-icon"><i class="fa fa-search"></i></span>
                              <input type="search" class="inventory-search-input dist-item-search" id="distItemSearch" placeholder="Search item no. or name..." autocomplete="off">
                            </div>
                            <div class="dist-item-list" id="distItemList" role="listbox" aria-label="Items">
                              @foreach ($items as $item)
                                <button
                                  type="button"
                                  class="dist-item-option"
                                  role="option"
                                  data-id="{{ $item->id }}"
                                  data-search="{{ strtolower($item->item_no.' '.$item->name.' '.($item->brand ?? '')) }}"
                                >
                                  <span class="dist-item-option-no">{{ $item->item_no }}</span>
                                  <span class="dist-item-option-name">{{ $item->name }}</span>
                                  @if ($item->brand)
                                    <span class="dist-item-option-meta">{{ $item->brand }}</span>
                                  @endif
                                </button>
                              @endforeach
                              <p class="dist-item-empty" id="distItemEmpty" hidden>No items match your search.</p>
                            </div>
                            <input type="hidden" name="item" id="distItemId" required>
                          </label>
                        </div>

                        <label class="inventory-edit-field dist-qty-field">
                          <span class="inventory-edit-label">Quantity</span>
                          <input type="number" class="inventory-edit-input" name="qty" placeholder="Qty." min="1" step="1" required>
                        </label>

                        <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary dist-add-item-btn" name="store_action" value="add_wbcontent">
                          <i class="fa fa-plus"></i> Add
                        </button>
                      </div>

                      <p class="inventory-edit-field-hint dist-item-selected-label" id="distItemSelectedLabel">Type to search and select an item.</p>
                    </form>
                  </div>
            
                    @if (count($wbcontents) > 0)
                      <div class="dist-section">
                        <div class="dist-section-header">
                          <h6 class="inventory-edit-section-title inventory-edit-section-title-spaced">
                            <i class="fa fa-list"></i> Waybill items
                          </h6>
                        </div>

                        <div class="table-responsive">
                          <table class="table mt dist-items-table">
                            <thead class="text-secondary hideMe">
                              <tr>
                                <th>#</th>
                                <th>Waybill</th>
                                <th>Item</th>
                                <th>Qty.</th>
                                <th>Rem.</th>
                                <th>Date Added</th>
                                <th class="ryt actsize">Actions</th>
                              </tr>
                            </thead>
                            <tbody id="tb">
                              @foreach ($wbcontents as $wbc)
                                @if ($wbc->del == 'no')
                                  <tr @class(['rowColour' => $c % 2 === 0])>
                                    <td>{{ $c++ }}</td>
                                    <td>
                                      {{ $wbc->waybill->bill_no }}
                                      <p class="waybill-table-meta">{{ $wbc->waybill->comp_name }}</p>
                                    </td>
                                    <td>
                                      {{ $wbc->item->item_no.' - '.$wbc->item->name }}
                                      @if ($wbc->item->brand)
                                        <p class="waybill-table-meta">{{ $wbc->item->brand }}</p>
                                      @endif
                                    </td>
                                    <td>{{ $wbc->qty }}</td>
                                    <td>{{ $wbc->qty - $wbc->qty_dist }}</td>
                                    <td>{{ date('M. d, Y', strtotime($wbc->created_at)) }}</td>
                                    <td class="ryt">
                                      <form action="{{ action('ItemsController@update', $wbc->id) }}" method="POST" class="waybill-row-actions">
                                        <input type="hidden" name="_method" value="PUT">
                                        @csrf

                                        <button type="button" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-toggle="modal" data-target="#edit_{{ $wbc->id }}" title="Edit quantity" data-tip="Edit">
                                          <i class="fa fa-pencil"></i>
                                        </button>
                                        <button type="submit" name="store_action" value="del_wbcontent" class="inventory-action-btn inventory-action-btn-icon dash-tip" title="Remove item" data-tip="Delete" onclick="return confirm('Are you sure you want to delete this item?');">
                                          <i class="fa fa-trash"></i>
                                        </button>

                                        <div class="modal fade waybill-edit-modal" id="edit_{{ $wbc->id }}" tabindex="-1" role="dialog" aria-labelledby="editWbcLabel_{{ $wbc->id }}" aria-hidden="true">
                                          <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content inventory-edit-modal">
                                              <div class="inventory-edit-header">
                                                <div class="inventory-edit-header-inner">
                                                  <span class="inventory-edit-thumb inventory-edit-thumb-placeholder" aria-hidden="true">
                                                    <i class="fa fa-cubes"></i>
                                                  </span>
                                                  <div class="inventory-edit-header-text">
                                                    <span class="inventory-edit-kicker">Edit item quantity</span>
                                                    <h4 class="inventory-edit-title" id="editWbcLabel_{{ $wbc->id }}">{{ $wbc->item->item_no }}</h4>
                                                    <p class="inventory-edit-meta">{{ $wbc->item->name }}</p>
                                                  </div>
                                                </div>
                                                <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
                                                  <i class="material-icons">close</i>
                                                </button>
                                              </div>

                                              <div class="inventory-edit-body">
                                                <label class="inventory-edit-field">
                                                  <span class="inventory-edit-label">Quantity on waybill</span>
                                                  <input type="number" class="inventory-edit-input" name="qty" min="0" value="{{ $wbc->qty }}" required>
                                                </label>
                                              </div>

                                              <div class="inventory-edit-footer">
                                                <button type="button" class="inventory-edit-btn inventory-edit-btn-muted" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" name="store_action" value="up_wbcontent">
                                                  <i class="fa fa-save"></i> Update
                                                </button>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                      </form>
                                    </td>
                                  </tr>
                                @endif
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="dist-section dist-section-branches">
                        <div class="dist-section-header dist-section-toolbar">
                          <h6 class="inventory-edit-section-title inventory-edit-section-title-spaced">
                            <i class="fa fa-sitemap"></i> Branch distribution
                          </h6>
                          <a href="/distreport" class="inventory-action-btn dash-tip" data-tip="View distribution history">
                            <i class="fa fa-history"></i>
                            <span>Distribution history</span>
                          </a>
                        </div>

                        <div class="dist-callout">
                          <i class="fa fa-info-circle" aria-hidden="true"></i>
                          <span>Enter quantities to send to each branch, then click <strong>Update</strong> on the row.</span>
                        </div>

                        @foreach ($wbcontents as $wbc)
                          @if ($wbc->del == 'no')
                            <form action="{{ action('ItemsController@update', $wbc->id) }}" method="POST" id="distBranchForm_{{ $wbc->id }}" hidden>
                              @csrf
                              <input type="hidden" name="_method" value="PUT">
                              <input type="hidden" name="tvalue" value="{{ $t++ }}">
                            </form>
                          @endif
                        @endforeach

                        <div class="table-responsive dist-branch-table-wrap">
                          <table class="table mt dist-branch-table">
                            <thead class="text-secondary hideMe">
                              <tr>
                                <th rowspan="2">#</th>
                                <th rowspan="2">Item</th>
                                @foreach ($branches as $br)
                                  <th colspan="2" class="dist-branch-group">{{ $br->tag }}</th>
                                @endforeach
                                <th rowspan="2" class="ryt actsize">Actions</th>
                              </tr>
                              <tr>
                                @foreach ($branches as $br)
                                  <th class="dist-branch-sub dist-branch-sub-avl">Avl</th>
                                  <th class="dist-branch-sub dist-branch-sub-add">Add</th>
                                @endforeach
                              </tr>
                            </thead>
                            <tbody>
                              @foreach ($wbcontents as $wbc)
                                @if ($wbc->del == 'no')
                                  <tr @class(['rowColour' => $x % 2 === 0])>
                                    <td>{{ $x++ }}</td>
                                    <td class="dist-branch-item">
                                      <span class="dist-branch-item-no">{{ $wbc->item->item_no }}</span>
                                      {{ $wbc->item->name }}
                                    </td>
                                    @for ($i = 0; $i < count($branches); $i++)
                                      @php $val = 'q'.($i + 1); @endphp
                                      <td class="ryt dist-branch-avl">{{ $cur_qtys[$x - 2]->$val ?? '—' }}</td>
                                      <td class="dist-branch-add">
                                        <input class="dist-branch-input" type="number" min="0" name="{{ $val.$wbc->item_id }}" form="distBranchForm_{{ $wbc->id }}" placeholder="0" required>
                                      </td>
                                    @endfor
                                    <td class="ryt">
                                      <button type="submit" form="distBranchForm_{{ $wbc->id }}" name="store_action" value="up_wbdist" class="inventory-action-btn inventory-action-btn-primary dist-branch-update-btn dash-tip" data-tip="Save branch quantities" onclick="return confirm('Update distribution for this item?');">
                                        <i class="fa fa-check"></i>
                                        <span>Update</span>
                                      </button>
                                    </td>
                                  </tr>
                                @endif
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    @else
                      <div class="dash-empty-state">
                        <span class="dash-empty-state-icon" aria-hidden="true"><i class="fa fa-inbox"></i></span>
                        <p class="dash-empty-state-title">No items on this waybill yet</p>
                        <p class="dash-empty-state-text">Search and add items above, then distribute them to branches.</p>
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

<script>
  (function () {
    var picker = document.getElementById('distItemPicker');
    if (!picker) {
      return;
    }

    var searchInput = document.getElementById('distItemSearch');
    var list = document.getElementById('distItemList');
    var hiddenInput = document.getElementById('distItemId');
    var selectedLabel = document.getElementById('distItemSelectedLabel');
    var emptyState = document.getElementById('distItemEmpty');
    var options = Array.prototype.slice.call(list.querySelectorAll('.dist-item-option'));
    var form = document.getElementById('distAddItemForm');

    function setListOpen(isOpen) {
      list.classList.toggle('is-open', isOpen);
    }

    function clearSelection() {
      hiddenInput.value = '';
      options.forEach(function (item) {
        item.classList.remove('is-selected');
        item.setAttribute('aria-selected', 'false');
      });
      selectedLabel.textContent = 'Type to search and select an item.';
      selectedLabel.classList.remove('is-selected');
    }

    function filterOptions() {
      var term = searchInput.value.trim().toLowerCase();

      if (term === '') {
        setListOpen(false);
        return;
      }

      setListOpen(true);

      var visibleCount = 0;
      options.forEach(function (option) {
        var matches = option.getAttribute('data-search').indexOf(term) !== -1;
        option.classList.toggle('is-hidden', !matches);
        if (matches) {
          visibleCount++;
        }
      });

      emptyState.hidden = visibleCount > 0;
    }

    function selectOption(option) {
      options.forEach(function (item) {
        item.classList.remove('is-selected');
        item.setAttribute('aria-selected', 'false');
      });

      option.classList.add('is-selected');
      option.setAttribute('aria-selected', 'true');
      hiddenInput.value = option.getAttribute('data-id');

      var itemNo = option.querySelector('.dist-item-option-no').textContent.trim();
      var itemName = option.querySelector('.dist-item-option-name').textContent.trim();
      searchInput.value = itemNo + ' - ' + itemName;
      selectedLabel.textContent = 'Selected: ' + itemNo + ' - ' + itemName;
      selectedLabel.classList.add('is-selected');
      setListOpen(false);
    }

    searchInput.addEventListener('input', function () {
      if (searchInput.value.trim() === '') {
        clearSelection();
      } else if (hiddenInput.value) {
        hiddenInput.value = '';
        selectedLabel.classList.remove('is-selected');
      }

      filterOptions();
    });

    searchInput.addEventListener('focus', function () {
      if (searchInput.value.trim() !== '') {
        filterOptions();
      }
    });

    options.forEach(function (option) {
      option.addEventListener('click', function () {
        selectOption(option);
      });
    });

    document.addEventListener('click', function (event) {
      if (!picker.contains(event.target)) {
        setListOpen(false);
      }
    });

    form.addEventListener('submit', function (event) {
      if (!hiddenInput.value) {
        event.preventDefault();
        selectedLabel.textContent = 'Please search and select an item from the list.';
        selectedLabel.classList.remove('is-selected');
        searchInput.focus();
      }
    });
  })();
</script>

@endsection