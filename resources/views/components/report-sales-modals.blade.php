<div class="modal fade" id="reportEditOrderModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
    <div class="modal-content inventory-edit-modal">
      <div class="inventory-edit-header">
        <div class="inventory-edit-header-inner">
          <div class="inventory-edit-header-text">
            <span class="inventory-edit-kicker">Sales report</span>
            <h4 class="inventory-edit-title" id="reportEditOrderTitle">Edit order</h4>
          </div>
        </div>
        <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times"></i>
        </button>
      </div>
      <div class="inventory-edit-body">
        <form id="reportEditOrderForm" action="#" method="POST">
          @csrf
          @method('PUT')
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Buyer&rsquo;s name</span>
            <input class="inventory-edit-input" type="text" name="buy_name" id="reportEditBuyName" required />
          </label>
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Contact</span>
            <input class="inventory-edit-input" type="number" name="buy_contact" id="reportEditBuyContact" min="0" required />
          </label>
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Pay mode</span>
            <select class="inventory-edit-input inventory-edit-select" name="pay_mode" id="reportEditPayMode" required>
              <option value="Cash">Cash</option>
              <option value="Cheque">Cheque</option>
              <option value="Mobile Money">Mobile Money</option>
              <option value="Post Payment(Debt)">Post Payment (Debt)</option>
            </select>
          </label>
          <label class="inventory-edit-field">
            <span class="inventory-edit-label">Notes</span>
            <input class="inventory-edit-input" type="text" name="notes" id="reportEditNotes" maxlength="255" placeholder="Optional" />
          </label>
          <div class="inventory-edit-footer" style="padding: 0; border: 0; margin-top: 8px;">
            <button type="submit" class="inventory-edit-btn inventory-edit-btn-primary">
              <i class="fa fa-save"></i> Update order
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="reportNotesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog inventory-edit-dialog modal-dialog-centered" role="document">
    <div class="modal-content inventory-edit-modal">
      <div class="inventory-edit-header">
        <div class="inventory-edit-header-inner">
          <div class="inventory-edit-header-text">
            <span class="inventory-edit-kicker">Sales report</span>
            <h4 class="inventory-edit-title" id="reportNotesTitle">Purchase notes</h4>
          </div>
        </div>
        <button type="button" class="inventory-edit-close" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times"></i>
        </button>
      </div>
      <div class="inventory-edit-body">
        <p class="dash-reports-notes-full" id="reportNotesBody"></p>
      </div>
    </div>
  </div>
</div>
