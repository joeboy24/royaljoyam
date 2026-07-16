@extends('layouts.dashlay')

@section('content')

  @php
    $salesDate = session('date_today') ?: now()->format('Y-m-d');
    $cartTotal = count($carts) > 0 ? $carts->sum('tot') : 0;
    $cartQty = count($carts) > 0 ? $carts->sum('qty') : 0;
    $netTotal = $net_total ?? ($sum_ex_dbt + $debts_paid - $expenses->sum('expense_cost'));
    $branchBv = auth()->user()->bv;
    $activeSalesFilterCount = ($filterPayMode ?? '') !== '' ? 1 : 0;
    $activeSalesFilterCount += ($filterStatus ?? '') !== '' ? 1 : 0;
    $salesHasFilters = $activeSalesFilterCount > 0;
    $posCatalog = $items->map(function ($item) use ($branchBv) {
      return [
        'id' => $item->id,
        'itemNo' => $item->item_no,
        'name' => $item->name,
        'brand' => $item->brand,
        'desc' => $item->desc,
        'barcode' => $item->barcode,
        'costPrice' => $item->price,
        'thumb' => $item->thumb_img ?: 'no_image.png',
        'branchPrice' => $item->{'b'.$branchBv},
        'branchQty' => $item->{'q'.$branchBv},
      ];
    })->values();
  @endphp

  <div class="content dash-sales-content">
    <div class="dash-sales-sticky-header">
      <div class="container-fluid">
        <div class="dash-sales-kpi-grid">
          <a href="#" class="dash-sales-kpi" data-toggle="modal" data-target="#totbreakdownModal">
            <span class="dash-sales-kpi-icon dash-sales-kpi-icon--purple"><i class="fa fa-folder-open"></i></span>
            <p class="dash-sales-kpi-value">Gh₵ {{ number_format($sum_ex_dbt, 2) }}</p>
            <p class="dash-sales-kpi-label">Daily sales · {{ $salesDate }}</p>
          </a>
          <a href="/paid_debts" class="dash-sales-kpi">
            <span class="dash-sales-kpi-icon dash-sales-kpi-icon--pink"><i class="fa fa-dollar"></i></span>
            <p class="dash-sales-kpi-value">Gh₵ {{ number_format($debts_paid, 2) }}</p>
            <p class="dash-sales-kpi-label">Paid debts</p>
          </a>
          <a href="/expenses" class="dash-sales-kpi">
            <span class="dash-sales-kpi-icon dash-sales-kpi-icon--green"><i class="fa fa-money"></i></span>
            <p class="dash-sales-kpi-value">Gh₵ {{ number_format($expenses->sum('expense_cost'), 2) }}</p>
            <p class="dash-sales-kpi-label">Daily expenditure</p>
          </a>
          <div class="dash-sales-kpi">
            <span class="dash-sales-kpi-icon dash-sales-kpi-icon--blue"><i class="fa fa-line-chart"></i></span>
            <p class="dash-sales-kpi-value">Gh₵ {{ number_format($netTotal, 2) }}</p>
            <p class="dash-sales-kpi-label">Net total</p>
          </div>
        </div>

        <div class="dash-sales-date-bar">
          <div class="dash-sales-date-info">
            <span class="dash-sales-date-icon" aria-hidden="true"><i class="fa fa-calendar-check-o"></i></span>
            <div class="dash-sales-date-copy">
              <p class="dash-sales-date-bar-label">Sales date</p>
              <p class="dash-sales-date-bar-value">{{ \Carbon\Carbon::parse($salesDate)->format('l, d M Y') }}</p>
            </div>
          </div>

          <form action="{{ url('/changedate') }}" method="GET" class="dash-sales-date-form">
            <label class="dash-sales-date-picker" for="sales_date_picker">
              <span class="sr-only">Set sales date</span>
              <input
                id="sales_date_picker"
                class="dash-sales-date-input"
                name="date_today"
                type="date"
                value="{{ $salesDate }}"
                aria-label="Set sales date"
              />
            </label>
            <button type="submit" class="inventory-action-btn inventory-action-btn-primary dash-sales-date-submit">
              <i class="fa fa-calendar"></i>
              <span>Change date</span>
            </button>
          </form>
        </div>
      </div>
    </div>

    <div class="container-fluid dash-sales-body">
      @include('inc.messages')

      <div class="card dash-sales-card">
        <x-dash-page-header
          title="Sales"
          subtitle="Search items, manage your cart, and complete orders."
          icon="fa fa-shopping-cart"
        >
          <x-slot:actions>
            <a href="/expenses" class="dash-page-header-btn inventory-action-btn dash-tip" data-tip="Go to expenditure">
              <i class="fa fa-money"></i>
              <span>Expenses</span>
            </a>
          </x-slot:actions>
        </x-dash-page-header>
        <div class="card-body dash-form-body">

          <div class="dash-sales-section-toolbar">
            <h6 class="inventory-edit-section-title"><i class="fa fa-search"></i> Add to cart</h6>
          </div>

          <form class="dash-sales-pos-panel" action="{{ url('/sales/cart') }}" method="POST">
            @csrf

            <input id="item_id" name="item_id" type="hidden"/>
            <input id="name" name="name" type="hidden"/>
            <input id="price" name="price" type="hidden"/>
            <input id="cost_price" name="cost_price" type="hidden"/>

            <div class="dash-sales-pos-toolbar">
              <div class="dash-sales-search-wrap dash-sales-toolbar-search">
                <span class="dash-sales-search-icon"><i class="fa fa-search"></i></span>
                <input
                  type="text"
                  class="dash-sales-search-input"
                  name="item_name"
                  placeholder="Search item or scan barcode..."
                  id="mySearch"
                  autocomplete="off"
                  required
                />

                @if (count($items) > 0)
                  <div id="myDropdown" class="dash-sales-dropdown dropdown_content">
                    <div class="dash-sales-dropdown-close">
                      <button type="button" data-pos-close class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Close results" aria-label="Close results">
                        <i class="fa fa-times"></i>
                      </button>
                    </div>

                    @foreach ($items as $item)
                      <button
                        type="button"
                        id="selItem{{ $item->id }}"
                        class="dash-sales-dropdown-item"
                        data-item-id="{{ $item->id }}"
                        data-barcode="{{ $item->barcode }}"
                      >
                        <span class="dash-sales-dropdown-item-inner">
                          <img class="dash-sales-dropdown-thumb" src="/storage/rjv_items/{{ $item->thumb_img ?: 'no_image.png' }}" alt="" />
                          <span>
                            <span class="dash-sales-dropdown-name">{{ $item->name }}</span>
                            <span class="dash-sales-dropdown-desc">{{ $item->desc }}</span>
                          </span>
                        </span>
                      </button>
                    @endforeach
                  </div>
                @endif
              </div>

              <input
                class="inventory-edit-input dash-sales-pos-ref-input"
                id="item_no"
                name="item_no"
                type="text"
                placeholder="Ref"
                title="Reference"
                readonly
              />
              <input
                class="inventory-edit-input dash-sales-pos-qty-input"
                type="number"
                name="qty"
                value="1"
                min="1"
                placeholder="Qty"
                title="Quantity"
              />
              <input
                class="inventory-edit-input dash-sales-pos-price-input"
                type="text"
                id="amt"
                placeholder="Gh₵"
                title="Unit price"
                readonly
              />

              @if (auth()->user()->status == 'Administrator')
                <button
                  type="button"
                  class="inventory-action-btn inventory-action-btn-primary dash-sales-toolbar-btn dash-tip"
                  data-tip="Add item"
                  title="Add item"
                  onclick="alert('Oops...! Administrator cannot make purchase')"
                >
                  <i class="fa fa-plus"></i>
                  <span>Add</span>
                </button>
              @else
                <button
                  type="submit"
                  class="inventory-action-btn inventory-action-btn-primary dash-sales-toolbar-btn dash-tip"
                  data-tip="Add item"
                  title="Add item"
                >
                  <i class="fa fa-plus"></i>
                  <span>Add</span>
                </button>
                <a
                  href="/mpt_cart"
                  class="inventory-action-btn dash-sales-toolbar-btn dash-tip"
                  data-tip="Empty cart"
                  title="Empty cart"
                >
                  <i class="fa fa-trash"></i>
                  <span>Clear</span>
                </a>
              @endif
            </div>

            <dl id="item_info" class="dash-sales-item-info">
              <div>
                <dt>Qty available</dt>
                <dd id="qty_avl">—</dd>
              </div>
              <div>
                <dt>Brand</dt>
                <dd id="brand">—</dd>
              </div>
              <div>
                <dt>Description</dt>
                <dd id="desc">—</dd>
              </div>
            </dl>
          </form>

          <div class="dash-sales-section-toolbar inventory-edit-section-title-spaced">
            <h6 class="inventory-edit-section-title"><i class="fa fa-shopping-basket"></i> Cart</h6>
            @if (count($carts) > 0)
              <span class="dash-sales-section-count">{{ $cartQty }} items · Gh₵ {{ number_format($cartTotal, 2) }}</span>
            @endif
          </div>

          @if (count($carts) > 0)
            <div class="dash-sales-table-wrap table-responsive">
              <table class="table mt">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Item No.</th>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Unit (Gh₵)</th>
                    <th class="totAmt">Total (Gh₵)</th>
                    <th class="ryt">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($carts as $cart)
                    <tr @class(['rowColour' => $loop->even])>
                      <td>{{ $loop->iteration }}</td>
                      <td>{{ $cart->item_no }}</td>
                      <td>{{ $cart->name }}</td>
                      <td>{{ $cart->qty }}</td>
                      <td>{{ $cart->unit_price }}</td>
                      <td class="totAmt">{{ $cart->tot }}</td>
                      <td class="ryt">
                        <div class="dash-sales-actions">
                          <button type="button" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-toggle="modal" data-target="#changeModal_{{ $cart->id }}" data-tip="Edit qty" title="Edit quantity">
                            <i class="fa fa-pencil"></i>
                          </button>
                          <form action="{{ url('/sales/cart/' . $cart->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Remove" title="Remove from cart" onclick="return confirm('Remove this item from the cart?');">
                              <i class="fa fa-trash"></i>
                            </button>
                          </form>
                        </div>

                        <div class="modal fade" id="changeModal_{{ $cart->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
                            <div class="modal-content inventory-edit-modal">
                              <div class="inventory-edit-header">
                                <div class="inventory-edit-header-inner">
                                  <div class="inventory-edit-header-text">
                                    <span class="inventory-edit-kicker">Cart</span>
                                    <h4 class="inventory-edit-title">Edit quantity</h4>
                                    <p class="inventory-edit-meta">{{ $cart->name }}</p>
                                  </div>
                                </div>
                                <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
                                  <i class="fa fa-times"></i>
                                </button>
                              </div>
                              <div class="inventory-edit-body">
                                <form action="{{ url('/sales/cart/' . $cart->id) }}" method="POST">
                                  @csrf
                                  @method('PUT')
                                  <input type="hidden" name="price" value="{{ $cart->unit_price }}">
                                  <label class="inventory-edit-field">
                                    <span class="inventory-edit-label">Quantity</span>
                                    <input class="inventory-edit-input" type="number" min="1" name="change" value="{{ $cart->qty }}">
                                  </label>
                                  <div class="inventory-edit-footer" style="padding: 0; border: 0; margin-top: 8px;">
                                    <button type="button" class="inventory-edit-btn inventory-edit-btn-muted" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary">
                                      <i class="fa fa-save"></i> Save
                                    </button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                  <tr class="dash-sales-cart-total-row">
                    <td colspan="3"></td>
                    <td><strong>{{ $cartQty }}</strong></td>
                    <td></td>
                    <td class="totAmt"><strong>{{ number_format($cartTotal, 2) }}</strong></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>
          @else
            <p class="dash-sales-empty">Add items above to start a purchase.</p>
          @endif

          @if (count($carts) > 0)
            <div class="dash-sales-checkout-bar">
              <div class="dash-sales-checkout-bar-summary">
                <span class="dash-sales-checkout-bar-kicker">Ready to checkout</span>
                <p class="dash-sales-checkout-bar-total">
                  <strong>{{ $cartQty }}</strong> {{ $cartQty === 1 ? 'item' : 'items' }}
                  · <strong>Gh₵ {{ number_format($cartTotal, 2) }}</strong>
                </p>
              </div>
              <button
                type="button"
                class="inventory-action-btn inventory-action-btn-primary"
                id="openCheckoutDrawer"
                aria-controls="checkoutDrawer"
                aria-expanded="false"
              >
                <i class="fa fa-credit-card"></i>
                <span>Checkout</span>
              </button>
            </div>
          @endif

        </div>
      </div>

      <div class="card dash-sales-card">
        <div class="card-body dash-form-body">
          <div class="dash-sales-section-toolbar">
            <h6 class="inventory-edit-section-title"><i class="fa fa-list"></i> Today&rsquo;s sales</h6>
            <div class="dash-sales-section-actions">
              <form method="GET" action="{{ url('/sales') }}" class="dash-sales-log-filter-form inventory-list-toolbar">
                <div class="inventory-filters-panel is-collapsed" data-collapsible-filters>
                  <button
                    type="button"
                    class="inventory-filters-toggle inventory-search-btn inventory-search-btn-muted dash-tip"
                    aria-expanded="false"
                    aria-controls="salesLogFilters"
                    data-tip="Filter sales"
                  >
                    <i class="fa fa-filter"></i>
                    @if ($salesHasFilters)
                      <span class="inventory-filters-count">{{ $activeSalesFilterCount }}</span>
                    @endif
                  </button>

                  <div class="inventory-filters-body" id="salesLogFilters">
                    <div class="inventory-filters-controls">
                      <label class="inventory-filter-field">
                        <span class="inventory-filter-field-icon"><i class="fa fa-credit-card"></i></span>
                        <select name="pay_mode" class="inventory-filter-select" title="Filter by pay mode">
                          <option value="">All pay modes</option>
                          <option value="Cash" @selected(($filterPayMode ?? '') === 'Cash')>Cash</option>
                          <option value="Cheque" @selected(($filterPayMode ?? '') === 'Cheque')>Cheque</option>
                          <option value="Mobile Money" @selected(($filterPayMode ?? '') === 'Mobile Money')>Mobile Money</option>
                          <option value="Post Payment(Debt)" @selected(($filterPayMode ?? '') === 'Post Payment(Debt)')>Post Payment (Debt)</option>
                        </select>
                      </label>

                      <label class="inventory-filter-field">
                        <span class="inventory-filter-field-icon"><i class="fa fa-truck"></i></span>
                        <select name="status" class="inventory-filter-select" title="Filter by delivery status">
                          <option value="">All statuses</option>
                          <option value="Delivered" @selected(($filterStatus ?? '') === 'Delivered')>Delivered</option>
                          <option value="Not Delivered" @selected(($filterStatus ?? '') === 'Not Delivered')>Undelivered</option>
                        </select>
                      </label>

                      <button type="submit" class="inventory-search-btn inventory-search-btn-primary inventory-filters-apply">
                        <i class="fa fa-filter"></i>
                        <span>Apply</span>
                      </button>
                    </div>
                  </div>
                </div>

                @if ($salesHasFilters)
                  <a href="{{ url('/sales') }}" class="inventory-search-btn inventory-search-btn-clear inventory-search-btn-icon dash-tip" data-tip="Clear filters">
                    <i class="fa fa-refresh"></i>
                  </a>
                @endif
              </form>

              <span class="dash-sales-section-count">{{ $sales->total() }} records</span>
            </div>
          </div>

          @if (count($sales) > 0)
            <div class="dash-sales-table-wrap table-responsive">
              <table class="table mt dash-sales-log-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Qty</th>
                    <th>Pay mode</th>
                    <th>Buyer</th>
                    <th>Notes</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th class="ryt actsize">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sales as $sale)
                    @if ($sale->del == 'no')
                      @php
                        $paymentBadge = $sale->paymentStatusBadge();
                        $notesPreview = filled($sale->notes) ? \Illuminate\Support\Str::limit($sale->notes, 40) : null;
                        $notesTruncated = filled($sale->notes) && strlen($sale->notes) > 40;
                      @endphp
                      <tr @class([
                        'rowColour' => $loop->even,
                        'dash-sales-log-row--debt' => $sale->hasOutstandingDebt(),
                        'dash-sales-log-row--undelivered' => $sale->del_status === 'Not Delivered' && ! $sale->hasOutstandingDebt(),
                      ])>
                        <td>{{ $loop->iteration + ($sales->currentPage() - 1) * $sales->perPage() }}</td>
                        <td>
                          <strong>{{ $sale->order_no }}</strong>
                          <p class="dash-sales-log-meta">User: {{ $sale->user->name }}</p>
                        </td>
                        <td>{{ $sale->qty }}</td>
                        <td>
                          <span class="dash-sales-badge {{ $sale->payModeBadgeClass() }}">{{ $sale->payModeShortLabel() }}</span>
                        </td>
                        <td>
                          {{ $sale->buy_name }}
                          <p class="dash-sales-log-meta">{{ $sale->buy_contact }}</p>
                        </td>
                        <td class="dash-sales-notes-cell">
                          @if (filled($sale->notes))
                            @if ($notesTruncated)
                              <button
                                type="button"
                                class="dash-sales-notes-link"
                                data-toggle="modal"
                                data-target="#view_notes{{ $sale->id }}"
                              >
                                {{ $notesPreview }}
                              </button>
                            @else
                              <span title="{{ $sale->notes }}">{{ $notesPreview }}</span>
                            @endif
                          @else
                            <span class="gray_p">—</span>
                          @endif
                        </td>
                        <td>
                          Gh₵ {{ $sale->payment }}
                          <p class="dash-sales-log-meta">{{ $sale->changeOrBalanceLabel() }}: {{ number_format($sale->changeOrBalanceAmount(), 2) }}</p>
                        </td>
                        <td>
                          <div class="dash-sales-status-stack">
                            @if ($sale->del_status == 'Delivered')
                              <button
                                type="submit"
                                form="delivery-form-{{ $sale->id }}"
                                name="deliverer_text"
                                value="Not Delivered"
                                class="dash-sales-status-pill dash-sales-status-pill--delivered dash-tip"
                                data-tip="Click to mark as undelivered"
                                onclick="return confirm('Mark this order as undelivered?');"
                              >
                                Delivered
                              </button>
                            @else
                              <button
                                type="submit"
                                form="delivery-form-{{ $sale->id }}"
                                name="deliverer_text"
                                value="Delivered"
                                class="dash-sales-status-pill dash-sales-status-pill--undelivered dash-tip"
                                data-tip="Click to mark as delivered"
                                onclick="return confirm('Mark this order as delivered?');"
                              >
                                Undelivered
                              </button>
                            @endif

                            @if ($paymentBadge)
                              <span class="dash-sales-payment-badge {{ $paymentBadge['class'] }}">{{ $paymentBadge['label'] }}</span>
                            @endif
                          </div>
                        </td>
                        <td>
                          <strong>Gh₵ {{ number_format($sale->tot, 2) }}</strong>
                          @if ($sale->hasOutstandingDebt())
                            <p class="dash-sales-log-meta">Bal.: Gh₵ {{ number_format($sale->debtBalance(), 2) }}</p>
                          @endif
                          @if ($sale->discount != 0)
                            <p class="dash-sales-log-meta">Dis.: Gh₵ {{ number_format($sale->discount, 2) }}</p>
                          @endif
                        </td>
                        <td>
                          {{ $sale->created_at }}
                          <p class="dash-sales-log-meta">{{ $sale->updated_at }}</p>
                        </td>
                        <td class="ryt">
                          <div class="dash-sales-actions">
                            <a href="/reporting/{{ $sale->id }}" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Print" title="Print order">
                              <i class="fa fa-print"></i>
                            </a>
                            @if ($sale->hasOutstandingDebt())
                              <button type="button" data-toggle="modal" data-target="#pay_debt{{ $sale->id }}" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Pay debt" title="Pay debt">
                                <i class="fa fa-money"></i>
                              </button>
                            @endif
                            <button type="button" data-toggle="modal" data-target="#edit_order{{ $sale->id }}" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Edit" title="Edit order">
                              <i class="fa fa-pencil"></i>
                            </button>
                            @if (Auth()->user()->status == 'Administrator')
                              <a href="/reporting/{{ $sale->id }}/edit" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Return" title="Return order" onclick="return confirm('Returning order will permanently delete record. Continue?');">
                                <i class="fa fa-mail-reply"></i>
                              </a>
                            @endif
                          </div>
                        </td>
                      </tr>

                      @if ($sale->del_status == 'Not Delivered' && $sale->pay_mode == 'Post Payment(Debt)' && $sale->saleshistory->isNotEmpty())
                        <tr class="dash-sales-line-items-row">
                          <td colspan="11">
                            <div class="dash-sales-line-items">
                              <p class="dash-sales-line-items-title"><i class="fa fa-truck"></i> Line-item delivery</p>
                              <div class="dash-sales-line-items-table-wrap">
                                <table class="dash-sales-line-items-table">
                                  <thead>
                                    <tr>
                                      <th>Item</th>
                                      <th>Name</th>
                                      <th>Qty</th>
                                      <th>Status</th>
                                      <th>Date</th>
                                      <th class="ryt">Action</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    @foreach ($sale->saleshistory as $sh)
                                      <tr>
                                        <td>{{ $sh->item_no }}</td>
                                        <td>{{ $sh->name }}</td>
                                        <td>{{ $sh->qty }}</td>
                                        <td>
                                          @if ($sh->del_status == 'Delivered')
                                            <span class="dash-sales-payment-badge dash-sales-payment-badge--paid">Delivered</span>
                                          @else
                                            <span class="dash-sales-payment-badge dash-sales-payment-badge--warn">Pending</span>
                                          @endif
                                        </td>
                                        <td>{{ $sh->created_at }}</td>
                                        <td class="ryt">
                                          @if ($sh->del_status == 'Delivered')
                                            <button type="submit" form="line-undeliver-{{ $sh->id }}" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Mark undelivered">
                                              <i class="fa fa-undo"></i>
                                            </button>
                                          @else
                                            <button type="submit" form="line-deliver-{{ $sh->id }}" class="inventory-action-btn inventory-action-btn-primary inventory-action-btn-icon dash-tip" data-tip="Mark delivered">
                                              <i class="fa fa-check"></i>
                                            </button>
                                          @endif
                                        </td>
                                      </tr>
                                    @endforeach
                                  </tbody>
                                </table>
                              </div>
                            </div>
                          </td>
                        </tr>
                      @endif
                    @endif
                  @endforeach
                </tbody>
              </table>
            </div>
            {{ $sales->links() }}

            <div class="dash-sales-log-forms" hidden aria-hidden="true">
              @foreach ($sales as $sale)
                @if ($sale->del == 'no')
                  <form id="delivery-form-{{ $sale->id }}" action="{{ url('/deliverer') }}" method="GET">
                    <input type="hidden" name="deliverer" value="{{ $sale->id }}">
                  </form>

                  @if ($sale->del_status == 'Not Delivered' && $sale->pay_mode == 'Post Payment(Debt)')
                    @foreach ($sale->saleshistory as $sh)
                      <form id="line-deliver-{{ $sh->id }}" action="{{ url('/sales/history/' . $sh->id . '/deliver') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="send_sale_id" value="{{ $sale->id }}">
                      </form>
                      <form id="line-undeliver-{{ $sh->id }}" action="{{ url('/sales/history/' . $sh->id . '/undeliver') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="send_sale_id" value="{{ $sale->id }}">
                      </form>
                    @endforeach
                  @endif
                @endif
              @endforeach
            </div>

            @foreach ($sales as $sale)
              @if ($sale->del == 'no')
                @if (filled($sale->notes) && strlen($sale->notes) > 40)
                  <div class="modal fade" id="view_notes{{ $sale->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
                      <div class="modal-content inventory-edit-modal">
                        <div class="inventory-edit-header">
                          <div class="inventory-edit-header-inner">
                            <div class="inventory-edit-header-text">
                              <span class="inventory-edit-kicker">Order {{ $sale->order_no }}</span>
                              <h4 class="inventory-edit-title">Purchase notes</h4>
                            </div>
                          </div>
                          <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                          </button>
                        </div>
                        <div class="inventory-edit-body">
                          <p class="dash-sales-notes-full">{{ $sale->notes }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif

                @if ($sale->hasOutstandingDebt())
                  @php($debtRemaining = $sale->debtBalance())
                  <div class="modal fade" id="pay_debt{{ $sale->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
                      <div class="modal-content inventory-edit-modal">
                        <div class="inventory-edit-header">
                          <div class="inventory-edit-header-inner">
                            <div class="inventory-edit-header-text">
                              <span class="inventory-edit-kicker">Debt payment</span>
                              <h4 class="inventory-edit-title">{{ $sale->buy_name }}</h4>
                              <p class="inventory-edit-meta">Balance: Gh₵ {{ number_format($debtRemaining, 2) }}</p>
                            </div>
                          </div>
                          <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                          </button>
                        </div>
                        <div class="inventory-edit-body">
                          <form action="{{ url('/sales/pay-debt') }}" method="POST">
                            @csrf
                            <input type="hidden" name="send_id" value="{{ $sale->id }}">
                            <input type="hidden" name="send_tot" value="{{ $sale->tot }}">
                            <label class="inventory-edit-field">
                              <span class="inventory-edit-label">Amount (Gh₵)</span>
                              <input class="inventory-edit-input" type="number" min="0.01" step="any" name="amt_paid" value="{{ number_format($debtRemaining, 2, '.', '') }}" max="{{ number_format($debtRemaining, 2, '.', '') }}">
                            </label>
                            <div class="inventory-edit-footer" style="padding: 0; border: 0; margin-top: 8px;">
                              <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" onclick="return confirm('Proceed with payment?');">
                                <i class="fa fa-money"></i> Pay
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif

                <div class="modal fade" id="edit_order{{ $sale->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
                    <div class="modal-content inventory-edit-modal">
                      <div class="inventory-edit-header">
                        <div class="inventory-edit-header-inner">
                          <div class="inventory-edit-header-text">
                            <span class="inventory-edit-kicker">Edit order</span>
                            <h4 class="inventory-edit-title">{{ $sale->buy_name }}</h4>
                          </div>
                        </div>
                        <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
                          <i class="fa fa-times"></i>
                        </button>
                      </div>
                      <div class="inventory-edit-body">
                        <form action="{{ url('/sales/' . $sale->id) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <label class="inventory-edit-field">
                            <span class="inventory-edit-label">Buyer&rsquo;s name</span>
                            <input class="inventory-edit-input" type="text" name="buy_name" value="{{ $sale->buy_name }}" required/>
                          </label>
                          <label class="inventory-edit-field">
                            <span class="inventory-edit-label">Contact</span>
                            <input class="inventory-edit-input" type="number" name="buy_contact" min="0" value="{{ $sale->buy_contact }}" required/>
                          </label>
                          <label class="inventory-edit-field">
                            <span class="inventory-edit-label">Pay mode</span>
                            <select class="inventory-edit-input inventory-edit-select" name="pay_mode" required>
                              <option value="{{ $sale->pay_mode }}" selected>{{ $sale->pay_mode }}</option>
                              <option value="Cash">Cash</option>
                              <option value="Cheque">Cheque</option>
                              <option value="Mobile Money">Mobile Money</option>
                              <option value="Post Payment(Debt)">Post Payment (Debt)</option>
                            </select>
                          </label>
                          <label class="inventory-edit-field">
                            <span class="inventory-edit-label">Notes</span>
                            <input class="inventory-edit-input" type="text" name="notes" maxlength="255" value="{{ $sale->notes }}" placeholder="Optional"/>
                          </label>
                          <div class="inventory-edit-footer" style="padding: 0; border: 0; margin-top: 8px;">
                            <button type="button" class="inventory-edit-btn inventory-edit-btn-muted" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary">
                              <i class="fa fa-save"></i> Update
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              @endif
            @endforeach
          @else
            <p class="dash-sales-empty">
              @if ($salesHasFilters)
                No sales match your filters for {{ $salesDate }}.
              @else
                No sales recorded for {{ $salesDate }}.
              @endif
            </p>
          @endif
        </div>
      </div>

    </div>
  </div>

  @if (count($carts) > 0)
    <div class="dash-sales-drawer-backdrop" id="checkoutDrawerBackdrop" hidden aria-hidden="true"></div>
    <aside class="dash-sales-drawer" id="checkoutDrawer" aria-hidden="true" aria-labelledby="checkoutDrawerTitle" role="dialog">
      <div class="dash-sales-drawer-header">
        <div class="dash-sales-drawer-header-text">
          <span class="inventory-edit-kicker">Checkout</span>
          <h2 class="dash-sales-drawer-title" id="checkoutDrawerTitle">Complete purchase</h2>
          <p class="dash-sales-drawer-meta">{{ $cartQty }} {{ $cartQty === 1 ? 'item' : 'items' }} · Gh₵ {{ number_format($cartTotal, 2) }}</p>
        </div>
        <button type="button" class="inventory-edit-close" id="closeCheckoutDrawer" aria-label="Close checkout">
          <i class="fa fa-times"></i>
        </button>
      </div>

      <div class="dash-sales-drawer-body">
        <form id="checkoutDrawerForm" action="{{ url('/sales/checkout') }}" method="POST" class="dash-sales-drawer-form">
          @csrf

          <div class="dash-sales-checkout-summary" id="checkoutSummary" aria-live="polite">
            <div class="dash-sales-checkout-summary-row">
              <span>Subtotal</span>
              <strong id="checkoutSubtotal">Gh₵ {{ number_format($cartTotal, 2) }}</strong>
            </div>
            <div class="dash-sales-checkout-summary-row" id="checkoutDiscountRow" hidden>
              <span>Discount</span>
              <strong id="checkoutDiscountDisplay">Gh₵ 0.00</strong>
            </div>
            <div class="dash-sales-checkout-summary-row dash-sales-checkout-summary-total">
              <span>Total due</span>
              <strong id="checkoutTotalDue">Gh₵ {{ number_format($cartTotal, 2) }}</strong>
            </div>
            <div class="dash-sales-checkout-summary-row dash-sales-checkout-summary-diff" id="checkoutDiffRow">
              <span id="checkoutDiffLabel">Change</span>
              <strong id="checkoutDiffAmount">Gh₵ 0.00</strong>
            </div>
            <p class="dash-sales-checkout-hint" id="checkoutHint" hidden></p>
          </div>

          <div class="dash-sales-checkout-grid">
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Mode of payment</span>
            <select class="inventory-edit-input inventory-edit-select" id="checkoutPayMode" name="pay_mode" required>
              <option value="" disabled selected>Select payment mode</option>
              <option value="Cash">Cash</option>
              <option value="Cheque">Cheque</option>
              <option value="Mobile Money">Mobile Money</option>
              <option value="Post Payment(Debt)">Post Payment (Debt)</option>
            </select>
          </label>
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Delivery status</span>
            <select class="inventory-edit-input inventory-edit-select" name="del_status" required>
              <option value="" disabled selected>Select delivery status</option>
              <option value="Delivered">Delivered</option>
              <option value="Not Delivered">Not Delivered</option>
            </select>
          </label>
          </div>
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Buyer&rsquo;s name</span>
            <input class="inventory-edit-input" type="text" name="buy_name" placeholder="Full name" required/>
          </label>
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Contact</span>
            <input class="inventory-edit-input" type="number" name="buy_contact" placeholder="Phone number" min="0" required/>
          </label>
          <div class="dash-sales-checkout-grid">
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Discount (Gh₵)</span>
            <input class="inventory-edit-input" id="checkoutDiscount" type="number" name="discount" step="any" min="0" placeholder="0.00" value="0"/>
          </label>
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Payment amount (Gh₵)</span>
            <input class="inventory-edit-input" id="checkoutPaymentAmount" type="number" name="payment" step="any" min="0" value="{{ number_format($cartTotal, 2, '.', '') }}" required/>
          </label>
          </div>
          <label class="inventory-edit-field dash-sales-checkout-notes">
            <span class="inventory-edit-label">Purchase notes (optional)</span>
            <input class="inventory-edit-input" type="text" name="notes" maxlength="255" placeholder="Delivery instructions, reference, etc."/>
          </label>
        </form>
      </div>

      <div class="dash-sales-drawer-footer">
        <button type="button" class="inventory-edit-btn inventory-edit-btn-muted" id="cancelCheckoutDrawer">Cancel</button>
        <button type="submit" form="checkoutDrawerForm" class="inventory-edit-btn inventory-edit-btn-primary" id="checkoutSubmitBtn">
          <i class="fa fa-money"></i>
          <span id="checkoutSubmitLabel">Pay bill · Gh₵ {{ number_format($cartTotal, 2) }}</span>
        </button>
      </div>
    </aside>
  @endif

  <div class="modal fade" id="totbreakdownModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
      <div class="modal-content inventory-edit-modal">
        <div class="inventory-edit-header">
          <div class="inventory-edit-header-inner">
            <div class="inventory-edit-header-text">
              <span class="inventory-edit-kicker">Daily sales</span>
              <h4 class="inventory-edit-title">Payment breakdown</h4>
            </div>
          </div>
          <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="inventory-edit-body">
          <table class="dash-sales-breakdown-table">
            <tr><td>Cash</td><td>Gh₵ {{ number_format($cash, 2) }}</td></tr>
            <tr><td>Cheque</td><td>Gh₵ {{ number_format($cheque, 2) }}</td></tr>
            <tr><td>Mobile Money</td><td>Gh₵ {{ number_format($momo, 2) }}</td></tr>
            <tr><td>Post Payment (Debt)</td><td>Gh₵ {{ number_format($collected_debt ?? $debts_paid, 2) }}</td></tr>
          </table>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('footer')
  <script type="text/javascript">
    $.ajaxSetup({ headers: { 'csrftoken': '{{ csrf_token() }}' } });
    window.dashSalesConfig = {
      catalog: @json($posCatalog),
      checkout: {
        cartTotal: {{ json_encode((float) $cartTotal) }},
      },
    };
  </script>
  <script src="/maindir/js/dash-sales.js?v=3"></script>
  <script src="/maindir/js/inventory-collapsible-filters.js?v=2"></script>
@endsection
