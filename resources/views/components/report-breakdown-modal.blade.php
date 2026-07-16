@props([
    'breakdown',
    'branches',
])

@php
  $branchLabel = function (int $slot) use ($branches): string {
    $branch = $branches->firstWhere('tag', (string) $slot);

    return $branch ? \Illuminate\Support\Str::limit($branch->name, 14) : 'Branch '.$slot;
  };
@endphp

<div class="modal fade dash-reports-breakdown-modal" id="totbreakdownModal" tabindex="-1" role="dialog" aria-labelledby="totbreakdownModalLabel" aria-hidden="true">
  <div class="modal-dialog inventory-edit-dialog modal-dialog-centered dash-reports-breakdown-dialog" role="document">
    <div class="modal-content inventory-edit-modal">
      <div class="inventory-edit-header dash-reports-breakdown-header">
        <div class="inventory-edit-header-inner">
          <div class="inventory-edit-header-text">
            <span class="inventory-edit-kicker">Sales report</span>
            <h4 class="inventory-edit-title" id="totbreakdownModalLabel">Total amount breakdown</h4>
          </div>
        </div>
        <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times"></i>
        </button>
      </div>

      <div class="inventory-edit-body dash-reports-breakdown-body">
        <p class="dash-reports-breakdown-lead">
          Net total = cash + cheque + mobile money + paid debts collected − expenditure.
          Cash in drawer (est.) = cash + paid debts collected − expenditure (physical cash only; excludes cheque and mobile money).
          Profits (margin) = sum of item margins (unit price − cost) on sales in this period; not reduced by expenditure.
        </p>

        <div class="dash-reports-breakdown-scroll">
          <table class="dash-reports-breakdown-table">
            <thead>
              <tr>
                <th scope="col">Payment mode (Gh₵)</th>
                @foreach ($breakdown['columns'] as $column)
                  <th scope="col">{{ $branchLabel($column['slot']) }}</th>
                @endforeach
                <th scope="col" class="is-total-col">Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($breakdown['rows'] as $row)
                <tr @class(['is-'.$row['kind'] => true])>
                  <th scope="row">{{ $row['label'] }}</th>
                  @foreach ($row['values'] as $value)
                    <td>{{ number_format($value, 2) }}</td>
                  @endforeach
                  <td class="is-total-col">{{ number_format($row['total'], 2) }}</td>
                </tr>

                @if (! empty($row['subrow']))
                  <tr class="is-subrow">
                    <th scope="row">{{ $row['subrow']['label'] }}</th>
                    @foreach ($row['subrow']['values'] as $value)
                      <td>{{ number_format($value, 2) }}</td>
                    @endforeach
                    <td class="is-total-col">{{ number_format($row['subrow']['total'], 2) }}</td>
                  </tr>
                @endif
              @endforeach
            </tbody>
            <tfoot>
              <tr class="is-net-total">
                <th scope="row">Net total</th>
                @foreach ($breakdown['columns'] as $column)
                  <td>{{ number_format($column['net'], 2) }}</td>
                @endforeach
                <td class="is-total-col">{{ number_format($breakdown['net_total'], 2) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
