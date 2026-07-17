@props([
    'breakdown',
    'branches',
])

@php
  $branchLabel = function (int $slot) use ($branches): string {
    $branch = $branches->firstWhere('tag', (string) $slot);

    return $branch ? \Illuminate\Support\Str::limit($branch->name, 14) : 'Branch '.$slot;
  };

  $breakdownTags = [
    'cash' => ['drawer', 'net'],
    'cheque' => ['net'],
    'momo' => ['net'],
    'debt-collected' => ['drawer', 'net'],
    'expenses' => ['drawer', 'net'],
  ];

  $renderBreakdownTags = function (array $tags) {
      foreach ($tags as $tag) {
          $label = $tag === 'drawer' ? 'Drawer' : 'Net';
          echo '<span class="dash-reports-breakdown-tag dash-reports-breakdown-tag--'.e($tag).'">'.e($label).'</span>';
      }
  };

  $renderBreakdownLabel = function (string $label, ?string $badge = null, array $tags = []) {
      if ($badge === 'drawer') {
          echo '<span class="dash-reports-breakdown-formula-badge dash-reports-breakdown-formula-badge--drawer">'.e($label).'</span>';

          return;
      }

      if ($badge === 'orange') {
          echo '<span class="dash-reports-breakdown-formula-badge dash-reports-breakdown-formula-badge--orange">'.e($label).'</span>';

          return;
      }

      echo e($label);

      if (count($tags) > 0) {
          echo '<span class="dash-reports-breakdown-tag-group">';
          foreach ($tags as $tag) {
              $tagLabel = $tag === 'drawer' ? 'Drawer' : 'Net';
              echo '<span class="dash-reports-breakdown-tag dash-reports-breakdown-tag--'.e($tag).'">'.e($tagLabel).'</span>';
          }
          echo '</span>';
      }
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
        <div class="dash-reports-breakdown-formulas">
          <div class="dash-reports-breakdown-formula is-drawer">
            <span class="dash-reports-breakdown-formula-title">Cash in drawer (est.)</span>
            <div class="dash-reports-breakdown-formula-chain">
              <span class="dash-reports-breakdown-chip">Cash</span>
              <span class="dash-reports-breakdown-op">+</span>
              <span class="dash-reports-breakdown-chip">Paid debts collected</span>
              <span class="dash-reports-breakdown-op">−</span>
              <span class="dash-reports-breakdown-chip">Expenditure</span>
              <span class="dash-reports-breakdown-op">=</span>
              <span class="dash-reports-breakdown-chip is-result is-drawer">Cash in drawer</span>
            </div>
          </div>

          <div class="dash-reports-breakdown-formula is-net">
            <span class="dash-reports-breakdown-formula-title">Net total</span>
            <div class="dash-reports-breakdown-formula-chain">
              <span class="dash-reports-breakdown-chip">Cash</span>
              <span class="dash-reports-breakdown-op">+</span>
              <span class="dash-reports-breakdown-chip">Cheque</span>
              <span class="dash-reports-breakdown-op">+</span>
              <span class="dash-reports-breakdown-chip">Mobile money</span>
              <span class="dash-reports-breakdown-op">+</span>
              <span class="dash-reports-breakdown-chip">Paid debts collected</span>
              <span class="dash-reports-breakdown-op">−</span>
              <span class="dash-reports-breakdown-chip">Expenditure</span>
              <span class="dash-reports-breakdown-op">=</span>
              <span class="dash-reports-breakdown-chip is-result is-net">Net total</span>
            </div>
          </div>
        </div>

        <p class="dash-reports-breakdown-lead">
          Profits (margin) = sum of item margins on sales in this period; not reduced by expenditure.
          Rows tagged <strong>Drawer</strong> or <strong>Net</strong> show which total they feed.
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
                  <th scope="row">
                    {!! $renderBreakdownLabel(
                      $row['label'],
                      ($row['key'] ?? '') === 'cash_at_hand' ? 'drawer' : null,
                      $breakdownTags[$row['key']] ?? []
                    ) !!}
                  </th>
                  @foreach ($row['values'] as $value)
                    <td>{{ number_format($value, 2) }}</td>
                  @endforeach
                  <td class="is-total-col">{{ number_format($row['total'], 2) }}</td>
                </tr>

                @if (! empty($row['subrow']))
                  <tr class="is-subrow">
                    <th scope="row">
                      {!! $renderBreakdownLabel($row['subrow']['label'], null, $breakdownTags['debt-collected'] ?? []) !!}
                    </th>
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
                <th scope="row">{!! $renderBreakdownLabel('Net total', 'orange') !!}</th>
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
