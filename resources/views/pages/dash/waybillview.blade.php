@extends('layouts.dashlay')

@php
  $clearUrl = $showRecycle ? url('/waybillview?recycle=1') : url('/waybillview');
  $activeFilterCount = ($filterStatus !== '' ? 1 : 0)
    + ($filterDistribution !== '' ? 1 : 0)
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

                            @if (! $showRecycle)
                              <label class="inventory-filter-field">
                                <span class="inventory-filter-field-icon"><i class="fa fa-share-alt"></i></span>
                                <select name="distribution" class="inventory-filter-select" title="Filter by distribution">
                                  <option value="">All distribution</option>
                                  <option value="pending" @selected($filterDistribution === 'pending')>Pending</option>
                                  <option value="partial" @selected($filterDistribution === 'partial')>Partial</option>
                                  <option value="complete" @selected($filterDistribution === 'complete')>Complete</option>
                                </select>
                              </label>
                            @endif

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
                <div class="table-responsive waybill-table-wrap">
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
                        <th class="waybill-table-status-col">Status</th>
                        @if (! $showRecycle)
                          <th class="waybill-table-dist-col">Distribution</th>
                        @endif
                        <th class="waybill-table-date-col"><x-waybill-sort-link column="del_date" label="Delivery Date" :sort="$sort" :dir="$dir" :list-query="$listQuery" /></th>
                        <th class="ryt actsize waybill-table-actions-col">Actions</th>
                      </tr>
                    </thead>
                    <tbody id="tb">
                      @foreach ($waybills as $waybill)
                        <tr @class([
                          'rowColour' => $c % 2 === 0,
                          'waybill-recycle-row' => $showRecycle,
                          'waybill-row-dist-open' => ! $showRecycle && $waybill->canDistribute() && $waybill->hasOpenDistribution(),
                        ])>
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
                          @if (! $showRecycle)
                            <td class="waybill-table-dist-col">
                              <x-waybill-distribution-badge
                                :status="$waybill->distributionStatus()"
                                :remaining="$waybill->distributionRemaining()"
                              />
                            </td>
                          @endif
                          <td class="waybill-table-date-col">{{ $waybill->formattedDeliveryDate() }}</td>
                          <td class="ryt waybill-table-actions-col">
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
                                @if ($waybill->canDistribute())
                                  <a
                                    href="/distribution/{{ $waybill->id }}"
                                    class="inventory-action-btn inventory-action-btn-icon waybill-distribute-btn dash-tip"
                                    title="{{ $waybill->distributionRemaining() > 0 ? $waybill->distributionRemaining().' remaining to distribute' : 'Distribute' }}"
                                    data-tip="{{ $waybill->distributionRemaining() > 0 ? 'Distribute · '.$waybill->distributionRemaining().' rem.' : 'Distribute' }}"
                                  >
                                    <i class="fa fa-share-alt"></i>
                                    @if ($waybill->distributionRemaining() > 0)
                                      <span class="waybill-action-badge">{{ $waybill->distributionRemaining() }}</span>
                                    @endif
                                  </a>
                                @else
                                  <span
                                    class="inventory-action-btn inventory-action-btn-icon is-disabled dash-tip"
                                    title="Mark waybill as Delivered to distribute"
                                    data-tip="Delivered status required"
                                  >
                                    <i class="fa fa-share-alt"></i>
                                  </span>
                                @endif

                                <div class="waybill-actions-more">
                                  <button
                                    type="button"
                                    class="inventory-action-btn inventory-action-btn-icon waybill-actions-more-toggle dash-tip"
                                    aria-label="More actions"
                                    aria-haspopup="true"
                                    data-tip="More actions"
                                  >
                                    <i class="fa fa-ellipsis-h"></i>
                                  </button>

                                  <div class="waybill-actions-more-menu" role="group" aria-label="Waybill actions">
                                    <button type="button" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-toggle="modal" data-target="#edit_{{ $waybill->id }}" title="Edit waybill" data-tip="Edit">
                                      <i class="fa fa-pencil"></i>
                                    </button>

                                    <a href="/waybillprint/{{ $waybill->id }}" target="_blank" rel="noopener" class="inventory-action-btn inventory-action-btn-icon dash-tip" title="Print waybill" data-tip="Print">
                                      <i class="fa fa-print"></i>
                                    </a>

                                    <form action="{{ action('ItemsController@update', $waybill->id) }}" method="POST">
                                      @csrf
                                      <input type="hidden" name="_method" value="PUT">
                                      <button type="submit" name="store_action" value="del_waybil" class="inventory-action-btn inventory-action-btn-icon dash-tip" title="Move to recycle bin" data-tip="Delete" onclick="return confirm('Move this waybill to the recycle bin?');">
                                        <i class="fa fa-trash"></i>
                                      </button>
                                    </form>
                                  </div>
                                </div>
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
                                                <input type="number" class="inventory-edit-input" name="weight" value="{{ $waybill->weight }}" min="0" step="any" inputmode="decimal"/>
                                              </label>

                                              <label class="inventory-edit-field">
                                                <span class="inventory-edit-label">No. of pieces</span>
                                                <input type="number" class="inventory-edit-input" name="nop" value="{{ $waybill->nop }}" min="0" step="1" inputmode="numeric"/>
                                              </label>
                                            </div>

                                            <label class="inventory-edit-field">
                                              <span class="inventory-edit-label">Total quantity</span>
                                              <input type="number" class="inventory-edit-input" name="tot_qty" value="{{ $waybill->tot_qty }}" min="0" step="1" inputmode="numeric"/>
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
<script>
  (function () {
    var menus = Array.prototype.slice.call(document.querySelectorAll('.waybill-actions-more'));

    function positionMenu(wrap) {
      var toggle = wrap.querySelector('.waybill-actions-more-toggle');
      var menu = wrap.querySelector('.waybill-actions-more-menu');
      if (!toggle || !menu) {
        return;
      }

      var rect = toggle.getBoundingClientRect();
      menu.style.top = (rect.top + (rect.height / 2)) + 'px';
      menu.style.left = (rect.left - 6) + 'px';
    }

    function closeAllMenus() {
      menus.forEach(function (wrap) {
        wrap.classList.remove('is-open');
        var toggle = wrap.querySelector('.waybill-actions-more-toggle');
        var menu = wrap.querySelector('.waybill-actions-more-menu');
        if (toggle) {
          toggle.setAttribute('aria-expanded', 'false');
        }
        if (menu) {
          menu.style.top = '';
          menu.style.left = '';
        }
      });
    }

    function openMenu(wrap) {
      closeAllMenus();
      wrap.classList.add('is-open');
      positionMenu(wrap);
      var toggle = wrap.querySelector('.waybill-actions-more-toggle');
      if (toggle) {
        toggle.setAttribute('aria-expanded', 'true');
      }
    }

    function repositionOpenMenu() {
      var openWrap = document.querySelector('.waybill-actions-more.is-open');
      if (openWrap) {
        positionMenu(openWrap);
      }
    }

    menus.forEach(function (wrap) {
      var toggle = wrap.querySelector('.waybill-actions-more-toggle');
      if (!toggle) {
        return;
      }

      wrap.addEventListener('mouseenter', function () {
        openMenu(wrap);
      });

      toggle.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        if (wrap.classList.contains('is-open')) {
          closeAllMenus();
        } else {
          openMenu(wrap);
        }
      });
    });

    document.addEventListener('click', function (event) {
      if (!event.target.closest('.waybill-actions-more')) {
        closeAllMenus();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeAllMenus();
      }
    });

    window.addEventListener('scroll', repositionOpenMenu, true);
    window.addEventListener('resize', repositionOpenMenu);

    document.addEventListener('show.bs.modal', closeAllMenus);
  })();
</script>
@endsection
