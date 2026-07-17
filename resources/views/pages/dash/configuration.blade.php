@extends('layouts.dashlay')

@php
  $activeBranches = $branches->where('del', 'no');
  $hasCompany = count($company) > 0;
  $comp = $hasCompany ? $company->first() : null;
  $branchLimitReached = $activeBranches->count() >= 5;
@endphp

@section('content')

  <div class="content">
    <div class="container-fluid">
      <div class="row dash-config-layout">

        <div class="col-lg-7">
          @include('inc.messages')

          <div class="card">
            <x-dash-page-header
              title="Configuration"
              subtitle="Set up company information, branches, and system options."
              icon="fa fa-cogs"
            />

            <div class="card-body dash-form-body">
              <form action="{{ action('ItemsController@store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="dash-config-form-stack">
                  <h6 class="inventory-edit-section-title"><i class="fa fa-building"></i> Company details</h6>

                  <label class="inventory-edit-field">
                    <span class="inventory-edit-label">Company name</span>
                    <input
                      type="text"
                      class="inventory-edit-input"
                      name="name"
                      value="{{ $comp->name ?? '' }}"
                      placeholder="Name of company"
                      required
                    />
                  </label>

                  <label class="inventory-edit-field">
                    <span class="inventory-edit-label">Address</span>
                    <textarea
                      name="company_add"
                      class="inventory-edit-input inventory-edit-textarea"
                      rows="4"
                      placeholder="Company address"
                      required
                    >{{ $comp->address ?? '' }}</textarea>
                  </label>

                  <label class="inventory-edit-field">
                    <span class="inventory-edit-label">Location</span>
                    <input
                      type="text"
                      class="inventory-edit-input"
                      name="loc"
                      value="{{ $comp->location ?? '' }}"
                      placeholder="City or area"
                    />
                  </label>

                  <h6 class="inventory-edit-section-title inventory-edit-section-title-spaced"><i class="fa fa-phone"></i> Contact &amp; online</h6>

                  <label class="inventory-edit-field">
                    <span class="inventory-edit-label">Contact number</span>
                    <input
                      type="text"
                      class="inventory-edit-input"
                      name="contact"
                      value="{{ $comp->contact ?? '' }}"
                      placeholder="Phone number"
                      required
                    />
                  </label>

                  <label class="inventory-edit-field">
                    <span class="inventory-edit-label">Email</span>
                    <input
                      type="email"
                      class="inventory-edit-input"
                      name="email"
                      value="{{ $comp->email ?? '' }}"
                      placeholder="Email address"
                    />
                  </label>

                  <label class="inventory-edit-field">
                    <span class="inventory-edit-label">Website</span>
                    <input
                      type="text"
                      class="inventory-edit-input"
                      name="company_web"
                      value="{{ $comp->website ?? '' }}"
                      placeholder="Website URL"
                    />
                  </label>

                  @if (! $hasCompany)
                    <label class="inventory-edit-field dash-config-file-field">
                      <span class="inventory-edit-label">Company logo</span>
                      <input type="file" class="dash-config-file-input" name="company_logo" accept="image/*" required>
                      <span class="inventory-edit-field-hint">Upload a logo for invoices and reports.</span>
                    </label>
                  @else
                    <input type="hidden" name="company_logo" value="{{ $comp->logo }}">
                    <div class="dist-callout dash-config-notice">
                      <i class="fa fa-check-circle" aria-hidden="true"></i>
                      <span>Company details are already set. Update the fields above to apply changes.</span>
                    </div>
                  @endif
                </div>

                <div class="dash-form-footer dash-config-form-footer">
                  <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" name="store_action" value="admi_config">
                    <i class="fa fa-save"></i>
                    {{ $hasCompany ? 'Update company' : 'Save company' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card dash-config-side-card">
            <div class="card-body dash-form-body dash-config-side-body">
              <div class="dist-section-header">
                <h6 class="inventory-edit-section-title"><i class="fa fa-sitemap"></i> Add branch</h6>
              </div>

              @if ($branchLimitReached)
                <div class="dist-callout dist-callout-warning">
                  <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                  <span>Maximum of 5 branches reached. Remove a branch before adding another.</span>
                </div>
              @else
                <form action="{{ action('ItemsController@store') }}" method="POST">
                  @csrf

                  <label class="inventory-edit-field">
                    <span class="inventory-edit-label">Branch name</span>
                    <input type="text" class="inventory-edit-input" name="name" placeholder="e.g. RJV Adum Branch" required>
                  </label>

                  <label class="inventory-edit-field">
                    <span class="inventory-edit-label">Location</span>
                    <input type="text" class="inventory-edit-input" name="loc" placeholder="e.g. Adum" required>
                  </label>

                  <label class="inventory-edit-field">
                    <span class="inventory-edit-label">Contact</span>
                    <input type="text" class="inventory-edit-input" name="contact" placeholder="Branch contact" required>
                  </label>

                  <div class="dash-config-side-actions">
                    <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" name="store_action" value="create_branch">
                      <i class="fa fa-plus"></i>
                      Add branch
                    </button>
                  </div>
                </form>
              @endif
            </div>
          </div>

          <div class="card dash-config-side-card">
            <div class="card-body dash-form-body dash-config-side-body">
              <div class="dist-section-toolbar">
                <h6 class="inventory-edit-section-title"><i class="fa fa-list"></i> Registered branches</h6>
                <span class="dash-config-branch-count">{{ $activeBranches->count() }} / 5</span>
              </div>

              @if ($activeBranches->count() > 0)
                <div class="table-responsive dist-branch-table-wrap dash-config-branch-table-wrap">
                  <table class="table mt dist-branch-table dash-config-branch-table">
                    <thead class="text-secondary hideMe">
                      <tr>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Contact</th>
                        <th class="ryt actsize">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($activeBranches as $branch)
                        <tr @class(['rowColour' => $loop->even])>
                          <td>
                            <span class="dash-config-branch-name">{{ $branch->name }}</span>
                            @if ($branch->tag)
                              <p class="waybill-table-meta">Tag {{ $branch->tag }}</p>
                            @endif
                          </td>
                          <td>{{ $branch->loc }}</td>
                          <td>{{ $branch->contact }}</td>
                          <td class="ryt">
                            <form action="{{ action('ItemsController@destroy', $branch->id) }}" method="POST" class="dash-config-delete-form">
                              @csrf
                              @method('DELETE')
                              <button
                                type="submit"
                                name="del_action"
                                value="branch_del"
                                class="inventory-action-btn inventory-action-btn-icon dash-config-delete-btn dash-tip"
                                data-tip="Delete branch"
                                onclick="return confirm('Are you sure you want to delete this branch?');"
                              >
                                <i class="fa fa-trash"></i>
                              </button>
                            </form>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="dash-empty-state dash-config-empty">
                  <span class="dash-empty-state-icon" aria-hidden="true"><i class="fa fa-sitemap"></i></span>
                  <p class="dash-empty-state-title">No branches yet</p>
                  <p class="dash-empty-state-text">Add your first branch using the form above.</p>
                </div>
              @endif
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

@endsection
