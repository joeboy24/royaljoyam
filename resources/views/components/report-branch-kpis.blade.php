@props([
    'b1' => 0,
    'b2' => 0,
    'b3' => 0,
    'b4' => 0,
    'b5' => 0,
    'b1_profits' => 0,
    'b2_profits' => 0,
    'b3_profits' => 0,
    'b4_profits' => 0,
    'b5_profits' => 0,
    'exp_b1' => 0,
    'exp_b2' => 0,
    'exp_b3' => 0,
    'exp_b4' => 0,
    'exp_b5' => 0,
    'expenses',
])

@php
  $showBranchDetail = fn (int $slot): bool => session('branch') == $slot || session('branch') == 'All Branches';

  $branchKpis = [
    ['slot' => 1, 'amount' => $b1, 'profits' => $b1_profits, 'expenses' => $exp_b1, 'name' => session('branch_A'), 'icon' => 'fa-pie-chart', 'tone' => 'teal', 'breakdown' => true],
    ['slot' => 2, 'amount' => $b2, 'profits' => $b2_profits, 'expenses' => $exp_b2, 'name' => session('branch_B'), 'icon' => 'fa-building', 'tone' => 'purple'],
    ['slot' => 3, 'amount' => $b3, 'profits' => $b3_profits, 'expenses' => $exp_b3, 'name' => session('branch_C'), 'icon' => 'fa-building-o', 'tone' => 'blue'],
    ['slot' => 4, 'amount' => $b4, 'profits' => $b4_profits, 'expenses' => $exp_b4, 'name' => session('branch_D'), 'icon' => 'fa-bank', 'tone' => 'amber'],
    ['slot' => 5, 'amount' => $b5, 'profits' => $b5_profits, 'expenses' => $exp_b5, 'name' => session('branch_E'), 'icon' => 'fa-map-marker', 'tone' => 'indigo'],
  ];
@endphp

<div class="dash-reports-kpi-grid">
  @foreach ($branchKpis as $kpi)
    @if (! empty($kpi['breakdown']))
      <a href="#" class="dash-reports-kpi" data-toggle="modal" data-target="#totbreakdownModal">
    @else
      <div class="dash-reports-kpi">
    @endif
      <span class="dash-reports-kpi-icon dash-reports-kpi-icon--{{ $kpi['tone'] }}">
        <i class="fa {{ $kpi['icon'] }}"></i>
      </span>
      <p class="dash-reports-kpi-value">Gh₵ {{ number_format($kpi['amount'], 2) }}</p>
      <p class="dash-reports-kpi-label">Branch {{ $kpi['slot'] }} · {{ \Illuminate\Support\Str::limit($kpi['name'] ?? 'Branch '.$kpi['slot'], 16) }}</p>
      @if ($showBranchDetail($kpi['slot']))
        <p class="dash-reports-kpi-meta">
          Profits Gh₵ {{ number_format($kpi['profits'], 2) }}
          · Expenses Gh₵ {{ number_format($kpi['expenses'], 2) }}
        </p>
      @endif
    @if (! empty($kpi['breakdown']))
      </a>
    @else
      </div>
    @endif
  @endforeach

  <a href="{{ \App\Support\ReportPrintQuery::url('/expensereport') }}" class="dash-reports-kpi">
    <span class="dash-reports-kpi-icon dash-reports-kpi-icon--rose">
      <i class="fa fa-money"></i>
    </span>
    <p class="dash-reports-kpi-value">Gh₵ {{ number_format($expenses->sum('expense_cost'), 2) }}</p>
    <p class="dash-reports-kpi-label">All branches expenditure</p>
  </a>
</div>
