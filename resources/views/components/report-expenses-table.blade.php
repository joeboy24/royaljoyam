@props(['expenses'])

@php
  $allExpenses = session('expenses', collect());
  $grandTotal = (float) $allExpenses->sum('expense_cost');
@endphp

@if ($expenses->count() > 0)
  <div class="dash-reports-table-wrap table-responsive">
    <table class="table mt dash-reports-data-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Title</th>
          <th>Description</th>
          <th>Branch</th>
          <th class="ryt">Cost</th>
          <th>Date</th>
          <th class="ryt actsize">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($expenses as $expense)
          @if ($expense->del !== 'no')
            @continue
          @endif

          @php
            $rowNumber = $loop->iteration + ($expenses->currentPage() - 1) * $expenses->perPage();
            $branchName = session('branch_'.$expense->companybranch_id) ?: '—';
          @endphp

          <tr @class(['rowColour' => $loop->even])>
            <td>{{ $rowNumber }}</td>
            <td><strong>{{ $expense->title }}</strong></td>
            <td class="dash-reports-cell-desc">{{ $expense->desc ?: '—' }}</td>
            <td>
              <span class="dash-reports-branch-pill">{{ $branchName }}</span>
            </td>
            <td class="ryt">
              <strong class="dash-reports-amount-negative">Gh₵ {{ number_format((float) $expense->expense_cost, 2) }}</strong>
            </td>
            <td>
              {{ $expense->created_at }}
            </td>
            <td class="ryt">
              <form action="{{ action('ItemsController@destroy', $expense->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button
                  type="submit"
                  name="del_action"
                  value="expense_del"
                  class="inventory-action-btn inventory-action-btn-icon inventory-action-btn-danger dash-tip"
                  data-tip="Delete"
                  title="Delete expense"
                  onclick="return confirm('Deleting this record will credit the main account with Gh₵ {{ number_format((float) $expense->expense_cost, 2) }}. Continue?');"
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

  @if (session('compbranch') && session('compbranch')->count() > 0)
    <div class="dash-reports-branch-totals">
      @foreach (session('compbranch') as $cbr)
        @php $branchKey = 'exp_b'.$cbr->tag; @endphp
        <span class="dash-reports-branch-total">
          Branch {{ $cbr->tag }}:
          <strong>Gh₵ {{ number_format((float) session($branchKey, 0), 2) }}</strong>
        </span>
      @endforeach
    </div>
  @endif

  <div class="dash-reports-table-summary">
    <span><strong>{{ number_format($expenses->total()) }}</strong> records</span>
    <span>Total expenditure: <strong class="dash-reports-amount-negative">Gh₵ {{ number_format($grandTotal, 2) }}</strong></span>
  </div>

  {{ $expenses->appends([
    'date_from' => request()->query('date_from'),
    'date_to' => request()->query('date_to'),
    'branch' => request()->query('branch'),
  ])->links() }}
@else
  <x-report-empty-state icon="fa fa-suitcase" message="No expenses found for the selected filters." />
@endif
