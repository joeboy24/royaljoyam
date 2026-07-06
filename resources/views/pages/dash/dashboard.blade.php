
@extends('layouts.dashlay')

@section('content')

  <div class="content dash-home-content">
    <div class="container-fluid">

      @include('inc.messages')

      @php
        $companyName = optional(session('company'))->name ?? 'Royal Joyam Ventures';
        $dashTiles = [
          [
            'url' => '/config',
            'icon' => 'fa-cogs',
            'title' => 'Configuration',
            'desc' => 'Administrator setup and system options',
            'modifier' => '',
          ],
          [
            'url' => '/dashuser',
            'icon' => 'fa-edit',
            'title' => 'Registry',
            'desc' => 'Register users and categories',
            'modifier' => '',
          ],
          [
            'url' => '/items',
            'icon' => 'fa-archive',
            'title' => 'Inventory',
            'desc' => 'View stock, search, filters, and reports',
            'modifier' => 'inventory',
          ],
          [
            'url' => '/waybill',
            'icon' => 'fa-truck',
            'title' => 'Waybill',
            'desc' => 'Waybill and distribution management',
            'modifier' => '',
          ],
          [
            'url' => '/sales',
            'icon' => 'fa-shopping-basket',
            'title' => 'Sales',
            'desc' => 'Manage sales and transaction records',
            'modifier' => '',
          ],
          [
            'url' => '/reporting',
            'icon' => 'fa-file-text',
            'title' => 'Reports',
            'desc' => 'Sales, stock, debts, and other reports',
            'modifier' => '',
          ],
          [
            'url' => '/expenses',
            'icon' => 'fa-money',
            'title' => 'Expenditure',
            'desc' => 'Track and manage business expenses',
            'modifier' => '',
          ],
          [
            'url' => '/closure_page',
            'icon' => 'fa-calendar',
            'title' => 'Closure',
            'desc' => 'Daily closure and end-of-day tasks',
            'modifier' => '',
          ],
        ];
      @endphp

      <header class="dash-home-header">
        <p class="dash-home-kicker">Dashboard</p>
        <h1 class="dash-home-title">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="dash-home-subtitle">{{ $companyName }} · {{ now()->format('l, d M Y') }}</p>
      </header>

      <nav class="dash-home-grid" aria-label="Dashboard modules">
        @foreach ($dashTiles as $tile)
          <a
            href="{{ $tile['url'] }}"
            class="dash-home-card{{ $tile['modifier'] !== '' ? ' dash-home-card--' . $tile['modifier'] : '' }}"
          >
            <span class="dash-home-card-icon" aria-hidden="true">
              <i class="fa {{ $tile['icon'] }}"></i>
            </span>
            <span class="dash-home-card-body">
              <h3>{{ $tile['title'] }}</h3>
              <p>{{ $tile['desc'] }}</p>
            </span>
            <span class="dash-home-card-arrow" aria-hidden="true">
              <i class="fa fa-angle-right"></i>
            </span>
          </a>
        @endforeach
      </nav>

    </div>
  </div>

@endsection

@section('footer')
  <link rel="stylesheet" href="/maindir/css/dash-dashboard.css?v=7">
@endsection
