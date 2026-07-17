<div class="modal fade" id="reportPayDebtModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
    <div class="modal-content inventory-edit-modal">
      <div class="inventory-edit-header">
        <div class="inventory-edit-header-inner">
          <div class="inventory-edit-header-text">
            <span class="inventory-edit-kicker">Debts report</span>
            <h4 class="inventory-edit-title" id="reportPayDebtTitle">Collect payment</h4>
          </div>
        </div>
        <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times"></i>
        </button>
      </div>
      <div class="inventory-edit-body">
        <form id="reportPayDebtForm" action="{{ url('/sales/pay-debt') }}" method="POST">
          @csrf
          <input type="hidden" name="send_id" id="reportPayDebtSaleId">
          <input type="hidden" name="send_tot" id="reportPayDebtSaleTot">
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Amount to pay (Gh₵)</span>
            <input
              class="inventory-edit-input"
              type="number"
              min="0.01"
              step="any"
              name="amt_paid"
              id="reportPayDebtAmount"
              required
            />
          </label>
          <div class="inventory-edit-footer" style="padding: 0; border: 0; margin-top: 8px;">
            <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary" onclick="return confirm('Are you sure you want to proceed with this payment?');">
              <i class="fa fa-money"></i> Pay debt
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
