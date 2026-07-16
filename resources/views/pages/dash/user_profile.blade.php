@extends('layouts.dashlay')

@php
  $initials = strtoupper(substr($user->name, 0, 1));
  $isAdmin = $user->status === 'Administrator';
@endphp

@section('content')

  <div class="content">
    <div class="container-fluid">
      <div class="row dash-profile-layout dash-config-layout">

        <div class="col-lg-7">
          @include('inc.messages')

          <div class="card">
            <x-dash-page-header
              title="My profile"
              subtitle="Update your login details and password."
              icon="fa fa-user-circle"
            />

            <div class="card-body dash-form-body dash-profile-form-body">
              <form action="{{ route('user_profile.update') }}" method="POST" class="dash-profile-form-stack">
                @csrf
                @method('PUT')

                <div class="dash-profile-identity">
                  <div class="dash-profile-avatar" aria-hidden="true">{{ $initials }}</div>
                  <div class="dash-profile-identity-text">
                    <p class="dash-profile-display-name">{{ $user->name }}</p>
                    <p class="dash-profile-display-email">{{ $user->email }}</p>
                    <span @class(['dash-profile-role-badge', 'is-admin' => $isAdmin])>{{ $user->status }}</span>
                  </div>
                </div>

                <h6 class="inventory-edit-section-title"><i class="fa fa-id-card"></i> Account details</h6>

                <label class="inventory-edit-field">
                  <span class="inventory-edit-label">Username</span>
                  <input
                    id="name"
                    type="text"
                    class="inventory-edit-input @error('name') is-invalid @enderror"
                    name="name"
                    value="{{ old('name', $user->name) }}"
                    placeholder="Username"
                    required
                    autofocus
                  />
                  @error('name')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                </label>

                <label class="inventory-edit-field">
                  <span class="inventory-edit-label">Email</span>
                  <input
                    id="email"
                    type="email"
                    class="inventory-edit-input @error('email') is-invalid @enderror"
                    name="email"
                    value="{{ old('email', $user->email) }}"
                    placeholder="Email address"
                    required
                  />
                  @error('email')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                </label>

                <div class="dash-profile-divider"></div>

                <h6 class="inventory-edit-section-title"><i class="fa fa-lock"></i> Change password</h6>
                <p class="dash-profile-security-note">Leave both password fields blank to keep your current password.</p>

                <label class="inventory-edit-field">
                  <span class="inventory-edit-label">New password</span>
                  <input
                    id="password"
                    type="password"
                    class="inventory-edit-input @error('password') is-invalid @enderror"
                    name="password"
                    placeholder="At least 8 characters"
                    autocomplete="new-password"
                  />
                  @error('password')<span class="inventory-edit-error">{{ $message }}</span>@enderror
                </label>

                <label class="inventory-edit-field">
                  <span class="inventory-edit-label">Confirm new password</span>
                  <input
                    id="password-confirm"
                    type="password"
                    class="inventory-edit-input"
                    name="password_confirmation"
                    placeholder="Repeat new password"
                    autocomplete="new-password"
                  />
                </label>

                <div class="inventory-edit-footer dash-profile-form-footer">
                  <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary">
                    <i class="fa fa-save"></i>
                    Save changes
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-5">
          <div class="card dash-config-side-card dash-profile-side-card">
            <div class="card-body dash-form-body dash-config-side-body">
              <h6 class="inventory-edit-section-title"><i class="fa fa-info-circle"></i> Account summary</h6>

              <ul class="dash-profile-info-list">
                <li class="dash-profile-info-item">
                  <span class="dash-profile-info-label">Role</span>
                  <span class="dash-profile-info-value">{{ $user->status }}</span>
                </li>
                <li class="dash-profile-info-item">
                  <span class="dash-profile-info-label">Branch</span>
                  <span class="dash-profile-info-value">{{ $branch->name ?? 'Not assigned' }}</span>
                  @if ($branch && $branch->loc)
                    <span class="dash-profile-info-meta">{{ $branch->loc }}</span>
                  @endif
                </li>
                <li class="dash-profile-info-item">
                  <span class="dash-profile-info-label">Member since</span>
                  <span class="dash-profile-info-value">{{ $user->created_at?->format('M j, Y') ?? '—' }}</span>
                </li>
              </ul>
            </div>
          </div>

          <div class="card dash-config-side-card dash-profile-side-card">
            <div class="card-body dash-form-body dash-config-side-body">
              <div class="dist-callout dash-config-notice">
                <i class="fa fa-shield" aria-hidden="true"></i>
                <span>Only you can update this profile. Branch assignment and user roles are managed by an administrator from <a href="/dashuser">registry</a>.</span>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

@endsection
