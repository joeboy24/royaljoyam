@extends('layouts.dashlay')

@php
  $defaultBillNo = old('bill_no', $suggestedBillNo ?? '');
  $defaultDelDate = old('del_date', now()->format('Y-m-d'));
  $defaultStatus = old('status', 'Pending');
@endphp

@section('content')

  <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-11">

              @include('inc.messages')

                <div class="form-group row mb-0 hideMe">
                  <div class="col-md-8 offset-md-0 myTrim"></div>
                  <div class="col-md-4 offset-md-0 myTrim inventory-toolbar-actions">
                    <div class="inventory-actions-group">
                      <a href="/items" class="inventory-action-btn inventory-action-btn-icon dash-tip" data-tip="Inventory">
                        <i class="fa fa-archive"></i>
                      </a>
                      <a href="/waybillview" class="inventory-action-btn inventory-action-btn-primary dash-tip" data-tip="View saved waybills">
                        <i class="fa fa-table"></i>
                        <span>View waybills</span>
                      </a>
                    </div>
                  </div>
                </div>

              <div class="card">
                <x-dash-page-header
                  title="Waybill"
                  subtitle="Complete waybill info here. After saving, you'll go straight to distribution."
                  icon="fa fa-truck"
                />
                <div class="card-body dash-form-body">
                  <form id="waybill-create-form" action="{{ url('/waybill') }}" method="POST">
                    @csrf

                    <div class="dash-form-grid">
                      <div class="dash-form-column">
                        <h6 class="inventory-edit-section-title"><i class="fa fa-building"></i> Sender info</h6>

                        <label class="inventory-edit-field">
                          <span class="inventory-edit-label">Company name</span>
                          <input type="text" class="inventory-edit-input @error('comp_name') is-invalid @enderror" name="comp_name" value="{{ old('comp_name') }}" placeholder="Company name" required/>
                          @error('comp_name')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                        </label>

                        <label class="inventory-edit-field">
                          <span class="inventory-edit-label">Address</span>
                          <textarea class="inventory-edit-input inventory-edit-textarea @error('comp_add') is-invalid @enderror" name="comp_add" rows="4" maxlength="2000" placeholder="Company address" required>{{ old('comp_add') }}</textarea>
                          @error('comp_add')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                        </label>

                        <label class="inventory-edit-field">
                          <span class="inventory-edit-label">Contact</span>
                          <input type="text" class="inventory-edit-input @error('comp_contact') is-invalid @enderror" name="comp_contact" value="{{ old('comp_contact') }}" placeholder="Phone or email" required/>
                          @error('comp_contact')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                        </label>

                        <h6 class="inventory-edit-section-title inventory-edit-section-title-spaced"><i class="fa fa-id-card"></i> Dispatch driver</h6>

                        <label class="inventory-edit-field">
                          <span class="inventory-edit-label">Driver's name</span>
                          <input type="text" class="inventory-edit-input @error('drv_name') is-invalid @enderror" name="drv_name" value="{{ old('drv_name') }}" placeholder="Driver name" required/>
                          @error('drv_name')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                        </label>

                        <label class="inventory-edit-field">
                          <span class="inventory-edit-label">Contact</span>
                          <input type="text" class="inventory-edit-input @error('drv_contact') is-invalid @enderror" name="drv_contact" value="{{ old('drv_contact') }}" placeholder="Driver contact" required/>
                          @error('drv_contact')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                        </label>

                        <label class="inventory-edit-field">
                          <span class="inventory-edit-label">Vehicle reg. no.</span>
                          <input type="text" class="inventory-edit-input @error('vno') is-invalid @enderror" name="vno" value="{{ old('vno') }}" placeholder="Vehicle registration" required/>
                          @error('vno')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                        </label>
                      </div>

                      <div class="dash-form-column">
                        <h6 class="inventory-edit-section-title"><i class="fa fa-file-text"></i> Shipment details</h6>

                        <label class="inventory-edit-field">
                          <span class="inventory-edit-label">Waybill no.</span>
                          <input type="text" class="inventory-edit-input @error('bill_no') is-invalid @enderror" name="bill_no" value="{{ $defaultBillNo }}" placeholder="Waybill number" required/>
                          <span class="inventory-edit-field-hint">Auto-suggested — you can change it before saving.</span>
                          @error('bill_no')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                        </label>

                        <div class="inventory-edit-field-row">
                          <label class="inventory-edit-field">
                            <span class="inventory-edit-label">Weight</span>
                            <input type="number" class="inventory-edit-input @error('weight') is-invalid @enderror" name="weight" value="{{ old('weight') }}" placeholder="e.g. 12.5" min="0" step="any" inputmode="decimal"/>
                            @error('weight')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                          </label>

                          <label class="inventory-edit-field">
                            <span class="inventory-edit-label">No. of pieces</span>
                            <input type="number" class="inventory-edit-input @error('nop') is-invalid @enderror" name="nop" value="{{ old('nop') }}" placeholder="Pieces" min="0" step="1" inputmode="numeric"/>
                            @error('nop')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                          </label>
                        </div>

                        <label class="inventory-edit-field">
                          <span class="inventory-edit-label">Total quantity</span>
                          <input type="number" class="inventory-edit-input @error('tot_qty') is-invalid @enderror" name="tot_qty" value="{{ old('tot_qty') }}" placeholder="Total quantity" min="0" step="1" inputmode="numeric"/>
                          @error('tot_qty')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                        </label>

                        <div class="inventory-edit-field-row">
                          <label class="inventory-edit-field">
                            <span class="inventory-edit-label">Delivery date</span>
                            <input type="date" class="inventory-edit-input @error('del_date') is-invalid @enderror" name="del_date" value="{{ $defaultDelDate }}"/>
                            @error('del_date')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                          </label>

                          <label class="inventory-edit-field">
                            <span class="inventory-edit-label">Status</span>
                            <select name="status" class="inventory-edit-input inventory-edit-select @error('status') is-invalid @enderror">
                              @foreach (\App\Models\Waybill::statusOptions() as $statusOption)
                                <option @selected($defaultStatus === $statusOption)>{{ $statusOption }}</option>
                              @endforeach
                            </select>
                            @error('status')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                          </label>
                        </div>
                      </div>
                    </div>

                    <div class="inventory-edit-footer dash-form-footer">
                      <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary">
                        <i class="fa fa-save"></i> Save &amp; distribute
                      </button>
                    </div>
                  </form>
                </div>
              </div>

            </div>
          </div>
        </div>
  </div>

@endsection
