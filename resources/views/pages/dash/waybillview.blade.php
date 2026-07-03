@extends('layouts.dashlay')

@php
  $clearUrl = $showRecycle ? url('/waybillview?recycle=1') : url('/waybillview');
  $activeFilterCount = ($filterStatus !== '' ? 1 : 0)
    + ($dateFrom ? 1 : 0)
    + ($dateTo ? 1 : 0)
    + ($perPage !== 10 ? 1 : 0);
@endphp

@section('content')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-11">

          @include('inc.messages')

          <div class="form-group row mb-0 hideMe">
            <div class="col-md-8 offset-md-0 myTrim">
              <form method="GET" action="{{ url('/waybillview') }}" class="inventory-filter-form inventory-list-toolbar">
                @if ($showRecycle)
                  <input type="hidden" name="recycle" value="1">
                @endif

                <div class="inventory-filter-row">
                  <div class="inventory-search-panel">
                    <div class="inventory-search-heading">
                      <span class="inventory-search-label">
                        <i class="material-icons">search</i>
                        Search
                        @if ($waybillsearch !== '')
                          <span class="inventory-search-active-dot" title="Search active"></span>
                        @endif
                      </span>
                    </div>

                    <div class="inventory-search-controls">
                      <label class="inventory-search-field">
                        <span class="inventory-search-field-icon"><i class="fa fa-search"></i></span>
                        <input type="search" value="{{ $waybillsearch }}" class="inventory-search-input" id="waybillsearch" name="waybillsearch" placeholder="Company, bill no., stock no., driver...">
                      </label>

                      <button type="submit" class="inventory-search-btn inventory-search-btn-primary" title="Search">
                        <i class="material-icons">search</i>
                        <span>Search</span>
                      </button>

                      <div class="inventory-filters-panel is-collapsed" data-collapsible-filters>
                        <button
                          type="button"
                          class="inventory-filters-toggle inventory-search-btn inventory-search-btn-muted dash-tip"
                          aria-expanded="false"
                          aria-controls="waybillFiltersControls"
                          data-tip="Filters"
                        >
                          <i class="fa fa-filter"></i>
                          @if ($activeFilterCount > 0)
                            <span class="inventory-filters-count">{{ $activeFilterCount }}</span>
                          @endif
                        </button>

                        <div class="inventory-filters-body" id="waybillFiltersControls">
                          <div class="inventory-filters-controls">
                            <label class="inventory-filter-field">
                              <span class="inventory-filter-field-icon"><i class="fa fa-flag"></i></span>
                              <select name="status" class="inventory-filter-select" title="Filter by status">
                                <option value="">All statuses</option>
                                @foreach (\App\Models\Waybill::statusOptions() as $statusOption)
                                  <option value="{{ $statusOption }}" @selected($filterStatus === $statusOption)>{{ $statusOption }}</option>
                                @endforeach
                              </select>
                            </label>

                            <label class="inventory-filter-field inventory-filter-field-compact">
                              <span class="inventory-filter-field-icon"><i class="fa fa-calendar"></i></span>
                              <input type="date" class="inventory-date-input" name="date_from" value="{{ $dateFrom ?? '' }}" title="Delivery from"/>
                            </label>

                            <span class="inventory-date-separator">to</span>

                            <label class="inventory-filter-field inventory-filter-field-compact">
                              <span class="inventory-filter-field-icon"><i class="fa fa-calendar"></i></span>
                              <input type="date" class="inventory-date-input" name="date_to" value="{{ $dateTo ?? '' }}" title="Delivery to"/>
                            </label>

                            <label class="inventory-filter-field inventory-filter-field-compact">
                              <span class="inventory-filter-field-icon"><i class="fa fa-list-ol"></i></span>
                              <select name="per_page" class="inventory-filter-select inventory-per-page-select" title="Rows per page">
                                @foreach ([10, 25, 50] as $pageSize)
                                  <option value="{{ $pageSize }}" @selected($perPage === $pageSize)>{{ $pageSize }} / page</option>
                                @endforeach
                              </select>
                            </label>

                            @if ($sort)
                              <input type="hidden" name="sort" value="{{ $sort }}">
                            @endif
                            @if ($dir !== 'desc')
                              <input type="hidden" name="dir" value="{{ $dir }}">
                            @endif

                            <button type="submit" class="inventory-search-btn inventory-search-btn-primary inventory-filters-apply">
                              <i class="fa fa-filter"></i>
                              <span>Apply</span>
                            </button>
                          </div>
                        </div>
                      </div>

                      <a href="{{ $clearUrl }}" class="inventory-search-btn inventory-search-btn-clear inventory-search-btn-icon dash-tip" data-tip="Clear filters">
                        <i class="fa fa-refresh"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <div class="col-md-4 offset-md-0 myTrim inventory-toolbar-actions">
              <div class="inventory-actions-group">
                @if ($showRecycle)
                  <a href="/waybillview" class="inventory-action-btn inventory-action-btn-primary dash-tip" data-tip="Back to waybill history">
                    <i class="fa fa-arrow-left"></i>
                    <span>Back to history</span>
                  </a>
                @else
                  <a href="/waybill" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Create waybill">
                    <i class="fa fa-arrow-left"></i>
                  </a>
                  <a href="{{ url('/waybillview?' . http_build_query(array_merge($listQuery, ['recycle' => '1']))) }}" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Recycle bin">
                    <i class="fa fa-trash"></i>
                  </a>
                  <a href="/waybill" class="inventory-action-btn inventory-action-btn-primary dash-tip" data-tip="New waybill">
                    <i class="fa fa-plus"></i>
                    <span>New waybill</span>
                  </a>
                @endif
              </div>
            </div>
          </div>

          <div class="card">
            <x-dash-page-header
              :title="$showRecycle ? 'Waybill Recycle Bin' : 'Waybill History'"
              :subtitle="$showRecycle ? 'Restore deleted waybills or keep them archived here.' : 'View, edit, distribute, and print saved waybills.'"
              icon="{{ $showRecycle ? 'fa fa-trash' : 'fa fa-table' }}"
            />

            <div id="printarea1" class="card-body">
              @if ($waybills->total() > 0)
                <div class="table-responsive">
                  <table class="table mt">
                    <thead class="text-secondary hideMe">
                      <tr>
                        <th>#</th>
                        <th>Stock No.</th>
                        <th>Company</th>
                        <th>Driver</th>
                        <th><x-waybill-sort-link column="bill_no" label="Bill No." :sort="$sort" :dir="$dir" :list-query="$listQuery" /></th>
                        <th>Weight</th>
                        <th>Pieces</th>
                        <th>Qty.</th>
                        <th class="waybill-table-status-col"><x-waybill-sort-link column="status" label="Status" :sort="$sort" :dir="$dir" :list-query="$listQuery" /></th>
                        <th class="waybill-table-date-col"><x-waybill-sort-link column="del_date" label="Delivery Date" :sort="$sort" :dir="$dir" :list-query="$listQuery" /></th>
                        <th class="ryt actsize">Actions</th>
                      </tr>
                    </thead>
                    <tbody id="tb">
                      @foreach ($waybills as $waybill)
                        <tr @class(['rowColour' => $c % 2 === 0, 'waybill-recycle-row' => $showRecycle])>
                          <td>{{ $c++ }}</td>
                          <td>
                            {{ $waybill->stock_no }}
                            <p class="waybill-table-meta">User: {{ $waybill->user->name }}</p>
                          </td>
                          <td>
                            {{ $waybill->comp_name }}, {{ $waybill->comp_add }}
                            <p class="waybill-table-meta">{{ $waybill->comp_contact }}</p>
                          </td>
                          <td>
                            {{ $waybill->drv_name }}<br>{{ $waybill->drv_contact }}
                            <p class="waybill-table-meta">{{ $waybill->vno }}</p>
                          </td>
                          <td>{{ $waybill->bill_no }}</td>
                          <td>{{ $waybill->weight ?: '—' }}</td>
                          <td>{{ $waybill->nop ?: '—' }}</td>
                          <td>{{ $waybill->tot_qty ?: '—' }}</td>
                          <td class="waybill-table-status-col">
                            <x-waybill-status-badge :status="$waybill->status" />
                          </td>
                          <td class="waybill-table-date-col">{{ $waybill->formattedDeliveryDate() }}</td>
                          <td class="ryt">
                            @if ($showRecycle)
                              <form action="{{ action('ItemsController@update', $waybill->id) }}" method="POST" class="waybill-row-actions">
                                @csrf
                                <input type="hidden" name="_method" value="PUT">
                                <button type="submit" name="store_action" value="restore_waybill" class="inventory-action-btn inventory-action-btn-primary dash-tip" title="Restore waybill" data-tip="Restore" onclick="return confirm('Restore this waybill?');">
                                  <i class="fa fa-reply"></i>
                                  <span>Restore</span>
                                </button>
                              </form>
                            @else
                              <div class="waybill-row-actions">
                                <button type="button" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-toggle="modal" data-target="#edit_{{ $waybill->id }}" title="Edit waybill" data-tip="Edit">
                                  <i class="fa fa-pencil"></i>
                                </button>

                                <a href="/distribution/{{ $waybill->id }}" class="inventory-action-btn inventory-action-btn-icon dash-tip" title="Distribute" data-tip="Distribute">
                                  <i class="fa fa-share-alt"></i>
                                </a>

                                <a href="/waybillprint/{{ $waybill->id }}" target="_blank" rel="noopener" class="inventory-action-btn inventory-action-btn-icon dash-tip" title="Print waybill" data-tip="Print">
                                  <i class="fa fa-print"></i>
                                </a>

                                <form action="{{ action('ItemsController@update', $waybill->id) }}" method="POST" style="display:inline;">
                                  @csrf
                                  <input type="hidden" name="_method" value="PUT">
                                  <button type="submit" name="store_action" value="del_waybil" class="inventory-action-btn inventory-action-btn-icon dash-tip" title="Move to recycle bin" data-tip="Delete" onclick="return confirm('Move this waybill to the recycle bin?');">
                                    <i class="fa fa-trash"></i>
                                  </button>
                                </form>
                              </div>

                              <div class="modal fade waybill-edit-modal" id="edit_{{ $waybill->id }}" tabindex="-1" role="dialog" aria-labelledby="editWaybillLabel_{{ $waybill->id }}" aria-hidden="true">
                                <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
                                  <div class="modal-content inventory-edit-modal">
                                    <form action="{{ action('ItemsController@update', $waybill->id) }}" method="POST">
                                      @csrf
                                      <input type="hidden" name="_method" value="PUT">

                                      <div class="inventory-edit-header">
                                        <div class="inventory-edit-header-inner">
                                          <span class="inventory-edit-thumb inventory-edit-thumb-placeholder" aria-hidden="true">
                                            <i class="fa fa-truck"></i>
                                          </span>
                                          <div class="inventory-edit-header-text">
                                            <span class="inventory-edit-kicker">Edit waybill</span>
                                            <h4 class="inventory-edit-title" id="editWaybillLabel_{{ $waybill->id }}">{{ $waybill->bill_no }}</h4>
                                            <p class="inventory-edit-meta">{{ $waybill->comp_name }} · {{ $waybill->stock_no }}</p>
                                          </div>
                                        </div>
                                        <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
                                          <i class="material-icons">close</i>
                                        </button>
                                      </div>

                                      <div class="inventory-edit-body">
                                        <div class="dash-form-grid">
                                          <div class="dash-form-column">
                                            <h6 class="inventory-edit-section-title"><i class="fa fa-building"></i> Sender info</h6>

                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Company name</span>
                                              <input type="text" class="inventory-edit-input" name="comp_name" value="{{ $waybill->comp_name }}" required/>
                                            </label>

                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Address</span>
                                              <textarea class="inventory-edit-input inventory-edit-textarea" name="comp_add" rows="4" maxlength="2000" required>{{ $waybill->comp_add }}</textarea>
                                            </label>

                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Contact</span>
                                              <input type="text" class="inventory-edit-input" name="comp_contact" value="{{ $waybill->comp_contact }}" required/>
                                            </label>

                                            <h6 class="inventory-edit-section-title inventory-edit-section-title-spaced"><i class="fa fa-id-card"></i> Dispatch driver</h6>

                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Driver's name</span>
                                              <input type="text" class="inventory-edit-input" name="drv_name" value="{{ $waybill->drv_name }}" required/>
                                            </label>

                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Contact</span>
                                              <input type="text" class="inventory-edit-input" name="drv_contact" value="{{ $waybill->drv_contact }}" required/>
                                            </label>

                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Vehicle reg. no.</span>
                                              <input type="text" class="inventory-edit-input" name="vno" value="{{ $waybill->vno }}" required/>
                                            </label>
                                          </div>

                                          <div class="dash-form-column">
                                            <h6 class="inventory-edit-section-title"><i class="fa fa-file-text"></i> Shipment details</h6>

                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Waybill no.</span>
                                              <input type="text" class="inventory-edit-input" name="bill_no" value="{{ $waybill->bill_no }}" required/>
                                            </label>

                                            <div class="inventory-edit-field-row">
                                              <label class="inventory-edit-field">
                                                <span class="inventory-edit-label">Weight</span>
                                                <input type="text" class="inventory-edit-input" name="weight" value="{{ $waybill->weight }}"/>
                                              </label>

                                              <label class="inventory-edit-field">
                                                <span class="inventory-edit-label">No. of pieces</span>
                                                <input type="text" class="inventory-edit-input" name="nop" value="{{ $waybill->nop }}"/>
                                              </label>
                                            </div>

                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Total quantity</span>
                                              <input type="text" class="inventory-edit-input" name="tot_qty" value="{{ $waybill->tot_qty }}"/>
                                            </label>

                                            <div class="inventory-edit-field-row">
                                              <label class="inventory-edit-field">
                                                <span class="inventory-edit-label">Delivery date</span>
                                                <input type="date" class="inventory-edit-input" name="del_date" value="{{ $waybill->del_date }}"/>
                                              </label>

                                              <label class="inventory-edit-field">
                                                <span class="inventory-edit-label">Status</span>
                                                <select name="status" class="inventory-edit-input inventory-edit-select">
                                                  @foreach (\App\Models\Waybill::statusOptions() as $statusOption)
                                                    <option @selected($waybill->status === $statusOption)>{{ $statusOption }}</option>
                                                  @endforeach
                                                </select>
                                              </label>
                                            </div>
                                          </div>
                                        </div>
                                      </div>

                                      <div class="inventory-edit-footer">
                                        <button type="button" class="inventory-edit-btn inventory-edit-btn-muted" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" name="store_action" value="update_waybill">
                                          <i class="fa fa-save"></i> Update record
                                        </button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <div class="waybill-list-footer">
                  <p class="mb-0">Total: <strong>{{ $waybills->total() }}</strong></p>
                  {{ $waybills->links() }}
                </div>
              @else
                <div class="dash-empty-state">
                  <span class="dash-empty-state-icon"><i class="fa {{ $showRecycle ? 'fa-trash' : 'fa-truck' }}"></i></span>
                  <h3 class="dash-empty-state-title">
                    {{ $showRecycle ? 'Recycle bin is empty' : 'No waybills found' }}
                  </h3>
                  <p class="dash-empty-state-text">
                    @if ($showRecycle)
                      Deleted waybills will appear here until you restore them.
                    @elseif ($waybillsearch !== '' || $filterStatus !== '' || $dateFrom || $dateTo)
                      No records match your filters. Try adjusting search or date range.
                    @else
                      Saved waybills will appear here once you create one.
                    @endif
                  </p>
                  @unless ($showRecycle)
                    <a href="/waybill" class="inventory-action-btn inventory-action-btn-primary">
                      <i class="fa fa-plus"></i>
                      <span>Create waybill</span>
                    </a>
                  @endunless
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
<script src="/maindir/js/inventory-collapsible-filters.js?v=2"></script>
@endsection
