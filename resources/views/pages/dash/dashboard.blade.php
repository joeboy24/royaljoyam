@extends('layouts.dashlay')

@section('content')

  <div class="content dash-home-content">
    <div class="container-fluid">

      @include('inc.messages')

      @php
        $user = auth()->user();
        $isAdmin = $user->status === 'Administrator';
        $companyName = optional(session('company'))->name ?? 'Royal Joyam Ventures';
        $branch = collect(session('compbranch', []))->firstWhere('id', (int) $user->company_branch_id);
        $branchName = $branch->name ?? null;
        $heroChips = array_values(array_unique(array_filter([
          $branchName,
          ($isAdmin || ! $branchName || strcasecmp($branchName, $user->status) !== 0) ? $user->status : null,
        ])));
        $salesDate = session('date_today') ?: now()->format('Y-m-d');
        $salesDateLabel = \Carbon\Carbon::parse($salesDate)->format('D, d M Y');
        $initials = strtoupper(substr($user->name, 0, 1));

        $dashSections = [
          [
            'title' => 'Operations',
            'subtitle' => 'Daily stock, sales, logistics, and reporting',
            'tiles' => [
              [
                'url' => '/sales',
                'icon' => 'fa-shopping-basket',
                'title' => 'Sales',
                'desc' => 'POS, cart, checkout, and daily totals',
                'modifier' => 'featured',
              ],
              [
                'url' => '/items',
                'icon' => 'fa-archive',
                'title' => 'Inventory',
                'desc' => 'Stock levels, search, filters, and exports',
                'modifier' => 'inventory',
              ],
              [
                'url' => '/waybill',
                'icon' => 'fa-truck',
                'title' => 'Waybill',
                'desc' => 'Dispatch, distribution, and delivery',
                'modifier' => '',
              ],
              [
                'url' => '/expenses',
                'icon' => 'fa-money',
                'title' => 'Expenditure',
                'desc' => 'Record and review branch expenses',
                'modifier' => '',
              ],
              [
                'url' => '/reporting',
                'icon' => 'fa-file-text',
                'title' => 'Reports',
                'desc' => 'Sales, stock, debts, and exports',
                'modifier' => '',
              ],
              [
                'url' => '/closure_page',
                'icon' => 'fa-calendar-check-o',
                'title' => 'Closure',
                'desc' => 'Month-end open and close tasks',
                'modifier' => '',
              ],
            ],
          ],
          [
            'title' => 'Administration',
            'subtitle' => 'Company setup and user registry',
            'admin_only' => true,
            'tiles' => [
              [
                'url' => '/config',
                'icon' => 'fa-cogs',
                'title' => 'Configuration',
                'desc' => 'Company profile, branches, and options',
                'modifier' => '',
              ],
              [
                'url' => '/dashuser',
                'icon' => 'fa-users',
                'title' => 'Registry',
                'desc' => 'Users, roles, and categories',
                'modifier' => '',
              ],
            ],
          ],
        ];
      @endphp

      <section class="dash-home-hero" aria-label="Welcome">
        <div class="dash-home-hero-copy">
          <p class="dash-home-kicker">Dashboard</p>
          <h1 class="dash-home-title">Welcome back, {{ $user->name }}</h1>
          <p class="dash-home-subtitle">{{ $companyName }}</p>
          @if (count($heroChips) > 0)
            <div class="dash-home-hero-chips">
              @foreach ($heroChips as $chip)
                <span class="dash-home-chip">{{ $chip }}</span>
              @endforeach
            </div>
          @endif
        </div>

        <div class="dash-home-hero-panel">
          <span class="dash-home-hero-avatar" aria-hidden="true">{{ $initials }}</span>
          <div class="dash-home-hero-stats">
            <div class="dash-home-stat">
              <span class="dash-home-stat-label">Today's date</span>
              <span class="dash-home-stat-value">{{ $salesDateLabel }}</span>
            </div>
            <a href="/sales" class="dash-home-stat-link">Change date on Sales <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
          </div>
        </div>
      </section>

      <div class="dash-home-panel dash-home-quick-panel">
        <header class="dash-home-panel-head">
          <h2 class="dash-home-panel-title">Quick actions</h2>
          <p class="dash-home-panel-subtitle">Jump straight into common tasks</p>
        </header>
        <div class="dash-home-quick" aria-label="Quick actions">
          <a href="/sales" class="dash-home-quick-btn dash-home-quick-btn-primary">
            <i class="fa fa-shopping-basket" aria-hidden="true"></i>
            <span>Open sales</span>
          </a>
          <a href="/items" class="dash-home-quick-btn">
            <i class="fa fa-archive" aria-hidden="true"></i>
            <span>Inventory</span>
          </a>
          <a href="/reporting" class="dash-home-quick-btn">
            <i class="fa fa-file-text" aria-hidden="true"></i>
            <span>Reports</span>
          </a>
          <a href="/user_profile" class="dash-home-quick-btn">
            <i class="fa fa-user-circle" aria-hidden="true"></i>
            <span>My profile</span>
          </a>
        </div>
      </div>

      @foreach ($dashSections as $section)
        @if (! empty($section['admin_only']) && ! $isAdmin)
          @continue
        @endif

        <section class="dash-home-section">
          <div class="dash-home-panel">
            <header class="dash-home-section-head">
              <h2 class="dash-home-section-title">{{ $section['title'] }}</h2>
              <p class="dash-home-section-subtitle">{{ $section['subtitle'] }}</p>
            </header>

            <nav class="dash-home-grid" aria-label="{{ $section['title'] }} modules">
            @foreach ($section['tiles'] as $tile)
              <a
                href="{{ $tile['url'] }}"
                @class([
                  'dash-home-card',
                  'dash-home-card--' . $tile['modifier'] => $tile['modifier'] !== '',
                ])
              >
                <span class="dash-home-card-icon" aria-hidden="true">
                  <i class="fa {{ $tile['icon'] }}"></i>
                </span>
                <span class="dash-home-card-body">
                  <h3>{{ $tile['title'] }}</h3>
                  <p>{{ $tile['desc'] }}</p>
                </span>
                <span class="dash-home-card-arrow" aria-hidden="true">
                  <i class="fa fa-arrow-right"></i>
                </span>
              </a>
            @endforeach
          </nav>
          </div>
        </section>
      @endforeach

    </div>
  </div>

@endsection

@section('footer')
  <link rel="stylesheet" href="/maindir/css/dash-dashboard.css?v=11">
@endsection
