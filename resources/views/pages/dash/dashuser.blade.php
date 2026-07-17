@extends('layouts.dashlay')

@section('content')

  <div class="content">
    <div class="container-fluid">
      <div class="row dash-config-layout">

        <div class="col-lg-7">
          @include('inc.messages')

          <div class="card">
            <x-dash-page-header
              title="Registry"
              subtitle="Register users and categories."
              icon="fa fa-edit"
            >
              <x-slot:actions>
                <button
                  type="button"
                  class="dash-page-header-btn inventory-action-btn inventory-action-btn-primary dash-tip"
                  data-toggle="modal"
                  data-target="#usrModal"
                  data-tip="Register user"
                >
                  <i class="fa fa-user-plus"></i>
                  <span>Register user</span>
                </button>
                <button
                  type="button"
                  class="dash-page-header-btn inventory-action-btn dash-tip"
                  data-toggle="modal"
                  data-target="#catModal"
                  data-tip="Add category"
                >
                  <i class="fa fa-folder-open"></i>
                  <span>Add category</span>
                </button>
              </x-slot:actions>
            </x-dash-page-header>

            <div class="card-body dash-form-body dash-config-side-body">
              <div class="dist-section-toolbar">
                <h6 class="inventory-edit-section-title"><i class="fa fa-users"></i> Registered users</h6>
                <span class="dash-config-branch-count">{{ $users->where('created_at', '!=', '')->count() }}</span>
              </div>

              @if ($users->where('created_at', '!=', '')->count() > 0)
                <div class="table-responsive dist-branch-table-wrap dash-config-branch-table-wrap">
                  <table class="table mt dist-branch-table dash-config-branch-table">
                    <thead class="text-secondary hideMe">
                      <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="ryt actsize">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($users as $user)
                        @if ($user->created_at != '')
                          <tr @class(['rowColour' => $loop->even, 'dash-registry-user-deleted' => $user->del === 'yes'])>
                            <td>
                              <span class="dash-config-branch-name">{{ $user->name }}</span>
                              @if ($user->del === 'yes')
                                <p class="waybill-table-meta">Deleted</p>
                              @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->status }}</td>
                            <td class="ryt">
                              <form action="{{ action('ItemsController@destroy', $user->id) }}" method="POST" class="dash-config-delete-form">
                                @csrf
                                @method('DELETE')
                                @if ($user->del === 'yes')
                                  <button
                                    type="submit"
                                    name="del_action"
                                    value="usr_restore"
                                    class="inventory-action-btn inventory-action-btn-icon dash-registry-restore-btn dash-tip"
                                    data-tip="Restore user"
                                    onclick="return confirm('Are you sure you want to restore this user?');"
                                  >
                                    <i class="fa fa-reply"></i>
                                  </button>
                                @else
                                  <button
                                    type="submit"
                                    name="del_action"
                                    value="usr_del"
                                    class="inventory-action-btn inventory-action-btn-icon dash-config-delete-btn dash-tip"
                                    data-tip="Delete user"
                                    onclick="return confirm('Are you sure you want to delete this user?');"
                                  >
                                    <i class="fa fa-trash"></i>
                                  </button>
                                @endif
                              </form>
                            </td>
                          </tr>
                        @endif
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="dash-empty-state dash-config-empty">
                  <span class="dash-empty-state-icon" aria-hidden="true"><i class="fa fa-users"></i></span>
                  <p class="dash-empty-state-title">No users yet</p>
                  <p class="dash-empty-state-text">Use <strong>Register user</strong> in the header to add the first account.</p>
                </div>
              @endif

              <div class="dist-callout dash-registry-notice dash-registry-notice-spaced">
                <i class="fa fa-info-circle" aria-hidden="true"></i>
                <span>Stock items, branch pricing, and quantities are managed from <a href="/items">inventory</a>.</span>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card dash-config-side-card">
            <div class="card-body dash-form-body dash-config-side-body">
              <div class="dist-section-toolbar">
                <h6 class="inventory-edit-section-title"><i class="fa fa-folder-open"></i> Registered categories</h6>
                <span class="dash-config-branch-count">{{ count($category) }}</span>
              </div>

              @if (count($category) > 0)
                <div class="table-responsive dist-branch-table-wrap dash-config-branch-table-wrap">
                  <table class="table mt dist-branch-table dash-config-branch-table">
                    <thead class="text-secondary hideMe">
                      <tr>
                        <th>Category</th>
                        <th class="ryt actsize">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($category as $cat)
                        @php $categoryInUse = $cat->isInUse(); @endphp
                        <tr @class(['rowColour' => $loop->even])>
                          <td>
                            <span class="dash-config-branch-name">{{ $cat->name }}</span>
                            @if ($cat->desc)
                              <p class="waybill-table-meta">{{ $cat->desc }}</p>
                            @endif
                            @if ($categoryInUse)
                              <p class="waybill-table-meta">{{ $cat->linkedItemCount() }} item(s) assigned</p>
                            @endif
                          </td>
                          <td class="ryt">
                            <form action="{{ action('ItemsController@destroy', $cat->id) }}" method="POST" class="dash-config-delete-form">
                              @csrf
                              @method('DELETE')
                              <span
                                class="dash-tip dash-config-delete-tip"
                                data-tip="{{ $categoryInUse ? 'Cannot delete — category is assigned to inventory items' : 'Delete category' }}"
                              >
                                <button
                                  type="submit"
                                  name="del_action"
                                  value="cat_del"
                                  class="inventory-action-btn inventory-action-btn-icon dash-config-delete-btn{{ $categoryInUse ? ' is-disabled' : '' }}"
                                  @disabled($categoryInUse)
                                  @if (! $categoryInUse) onclick="return confirm('Are you sure you want to delete this category?');" @endif
                                >
                                  <i class="fa fa-trash"></i>
                                </button>
                              </span>
                            </form>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="dash-empty-state dash-config-empty">
                  <span class="dash-empty-state-icon" aria-hidden="true"><i class="fa fa-folder-open"></i></span>
                  <p class="dash-empty-state-title">No categories yet</p>
                  <p class="dash-empty-state-text">Use <strong>Add category</strong> in the header to create your first category.</p>
                </div>
              @endif
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- Register user --}}
  <div class="modal fade" id="usrModal" tabindex="-1" role="dialog" aria-labelledby="usrModalLabel" aria-hidden="true">
    <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
      <div class="modal-content inventory-edit-modal">
        <form action="{{ action('ItemsController@store') }}" method="POST">
          @csrf

          <div class="inventory-edit-header">
            <div class="inventory-edit-header-inner">
              <div class="inventory-edit-thumb inventory-edit-thumb-placeholder">
                <i class="fa fa-user-plus"></i>
              </div>
              <div class="inventory-edit-header-text">
                <span class="inventory-edit-kicker">New user</span>
                <h4 class="inventory-edit-title" id="usrModalLabel">Register user</h4>
                <p class="inventory-edit-meta">Create a dashboard account for a staff member or administrator.</p>
              </div>
            </div>
            <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
          </div>

          <div class="inventory-edit-body">
            <label class="inventory-edit-field">
              <span class="inventory-edit-label">Username</span>
              <input id="usr_name" type="text" class="inventory-edit-input" name="name" placeholder="Username" required autofocus>
            </label>

            <label class="inventory-edit-field">
              <span class="inventory-edit-label">Email</span>
              <input id="usr_email" type="email" class="inventory-edit-input" name="email" placeholder="Email address" required>
            </label>

            <label class="inventory-edit-field">
              <span class="inventory-edit-label">User type / branch</span>
              <select name="status" class="inventory-edit-input inventory-edit-select" required>
                <option>Administrator</option>
                @if (count($branches) > 0)
                  @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                  @endforeach
                @endif
              </select>
            </label>

            <div class="inventory-edit-field-row">
              <label class="inventory-edit-field">
                <span class="inventory-edit-label">Password</span>
                <input id="usr_password" type="password" class="inventory-edit-input" name="password" placeholder="Password" required>
              </label>
              <label class="inventory-edit-field">
                <span class="inventory-edit-label">Confirm password</span>
                <input id="usr_password_confirm" type="password" class="inventory-edit-input" name="password_confirmation" placeholder="Confirm password" required>
              </label>
            </div>
          </div>

          <div class="inventory-edit-footer">
            <button type="button" class="inventory-edit-btn inventory-edit-btn-muted" data-dismiss="modal">Cancel</button>
            <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" name="store_action" value="create_user">
              <i class="fa fa-save"></i> Add user
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Add category --}}
  <div class="modal fade" id="catModal" tabindex="-1" role="dialog" aria-labelledby="catModalLabel" aria-hidden="true">
    <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
      <div class="modal-content inventory-edit-modal">
        <form action="{{ action('ItemsController@store') }}" method="POST">
          @csrf

          <div class="inventory-edit-header">
            <div class="inventory-edit-header-inner">
              <div class="inventory-edit-thumb inventory-edit-thumb-placeholder">
                <i class="fa fa-folder-open"></i>
              </div>
              <div class="inventory-edit-header-text">
                <span class="inventory-edit-kicker">New category</span>
                <h4 class="inventory-edit-title" id="catModalLabel">Add category</h4>
                <p class="inventory-edit-meta">Group inventory items under a shared category.</p>
              </div>
            </div>
            <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
              <i class="material-icons">close</i>
            </button>
          </div>

          <div class="inventory-edit-body">
            <label class="inventory-edit-field">
              <span class="inventory-edit-label">Category name</span>
              <input type="text" class="inventory-edit-input" name="name" placeholder="Category name" required autofocus>
            </label>

            <label class="inventory-edit-field">
              <span class="inventory-edit-label">Description</span>
              <textarea name="desc" class="inventory-edit-input inventory-edit-textarea" rows="3" placeholder="Category description"></textarea>
            </label>
          </div>

          <div class="inventory-edit-footer">
            <button type="button" class="inventory-edit-btn inventory-edit-btn-muted" data-dismiss="modal">Cancel</button>
            <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" name="store_action" value="add_cat">
              <i class="fa fa-save"></i> Add category
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection
