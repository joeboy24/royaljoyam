@extends('layouts.dashlay')

@section('content')

  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-11">

          @include('inc.messages')

          <div class="form-group row mb-0 hideMe">
            <div class="col-md-8 offset-md-0 myTrim">
              <form method="GET" action="{{ url('/waybillview') }}" class="inventory-filter-form">
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

                      <a href="/waybillview" class="inventory-search-btn inventory-search-btn-clear" title="Clear search">
                        <i class="fa fa-refresh"></i>
                        <span>Clear</span>
                      </a>
                    </div>
                  </div>
                </div>
              </form>
            </div>

            <div class="col-md-4 offset-md-0 myTrim inventory-toolbar-actions">
              <div class="inventory-actions-group">
                <a href="/waybill" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Create waybill">
                  <i class="fa fa-arrow-left"></i>
                </a>
                <a href="/waybill" class="inventory-action-btn inventory-action-btn-primary dash-tip" data-tip="New waybill">
                  <i class="fa fa-plus"></i>
                  <span>New waybill</span>
                </a>
              </div>
            </div>
          </div>

          <div class="card">
            <x-dash-page-header
              title="Waybill History"
              subtitle="View, edit, and distribute saved waybills."
              icon="fa fa-table"
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
                        <th>Bill No.</th>
                        <th>Weight</th>
                        <th>Pieces</th>
                        <th>Qty.</th>
                        <th>Status</th>
                        <th>Delivery Date</th>
                        <th class="ryt actsize">Actions</th>
                      </tr>
                    </thead>
                    <tbody id="tb">
                      @foreach ($waybills as $waybill)
                        <tr @class(['rowColour' => $c % 2 === 0])>
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
                          <td>
                            <x-waybill-status-badge :status="$waybill->status" />
                          </td>
                          <td>{{ $waybill->formattedDeliveryDate() }}</td>
                          <td class="ryt">
                            <form action="{{ action('ItemsController@update', $waybill->id) }}" method="POST" class="waybill-row-actions">
                              @csrf
                              <input type="hidden" name="_method" value="PUT">

                              <button type="button" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-toggle="modal" data-target="#edit_{{ $waybill->id }}" title="Edit waybill" data-tip="Edit">
                                <i class="fa fa-pencil"></i>
                              </button>

                              <a href="/distribution/{{ $waybill->id }}" class="inventory-action-btn inventory-action-btn-icon dash-tip" title="Distribute" data-tip="Distribute">
                                <i class="fa fa-share-alt"></i>
                              </a>

                              <button type="submit" name="store_action" value="del_waybil" class="inventory-action-btn inventory-action-btn-icon dash-tip" title="Delete waybill" data-tip="Delete" onclick="return confirm('Are you sure you want to delete this waybill?');">
                                <i class="fa fa-trash"></i>
                              </button>
                            </form>

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
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>

                <div class="waybill-list-footer">
                  <p class="mb-0">Total: <strong>{{ $waybills->total() }}</strong></p>
                  {{ $waybills->appends(['waybillsearch' => request()->query('waybillsearch')])->links() }}
                </div>
              @else
                <div class="dash-empty-state">
                  <span class="dash-empty-state-icon"><i class="fa fa-truck"></i></span>
                  <h3 class="dash-empty-state-title">No waybills found</h3>
                  <p class="dash-empty-state-text">
                    @if ($waybillsearch !== '')
                      No records match your search. Try another term or clear the filter.
                    @else
                      Saved waybills will appear here once you create one.
                    @endif
                  </p>
                  <a href="/waybill" class="inventory-action-btn inventory-action-btn-primary">
                    <i class="fa fa-plus"></i>
                    <span>Create waybill</span>
                  </a>
                </div>
              @endif
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

@endsection
