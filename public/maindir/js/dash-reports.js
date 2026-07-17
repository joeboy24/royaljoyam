(function () {
  function initReportEditModal() {
    var modal = document.getElementById('reportEditOrderModal');
    var form = document.getElementById('reportEditOrderForm');

    if (!modal || !form) {
      return;
    }

    document.querySelectorAll('.report-edit-trigger').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        form.action = trigger.getAttribute('data-action') || '#';
        document.getElementById('reportEditBuyName').value = trigger.getAttribute('data-buy-name') || '';
        document.getElementById('reportEditBuyContact').value = trigger.getAttribute('data-buy-contact') || '';
        document.getElementById('reportEditNotes').value = trigger.getAttribute('data-notes') || '';

        var payMode = trigger.getAttribute('data-pay-mode') || 'Cash';
        document.getElementById('reportEditPayMode').value = payMode;

        var orderNo = trigger.getAttribute('data-order') || 'order';
        document.getElementById('reportEditOrderTitle').textContent = 'Edit ' + orderNo;
      });
    });
  }

  function initReportNotesModal() {
    var notesBody = document.getElementById('reportNotesBody');
    var notesTitle = document.getElementById('reportNotesTitle');

    if (!notesBody || !notesTitle) {
      return;
    }

    document.querySelectorAll('[data-target="#reportNotesModal"]').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        notesBody.textContent = trigger.getAttribute('data-notes') || '';
        notesTitle.textContent = 'Notes · ' + (trigger.getAttribute('data-order') || 'Order');
      });
    });
  }

  function initReportFilterValidation() {
    document.querySelectorAll('[data-report-filter-form]').forEach(function (form) {
      var dateFrom = form.querySelector('[data-report-date-from]');
      var dateTo = form.querySelector('[data-report-date-to]');
      var errorEl = form.querySelector('[data-report-filter-error]');

      if (!dateFrom || !dateTo || !errorEl) {
        return;
      }

      function hideError() {
        errorEl.hidden = true;
        dateFrom.classList.remove('is-invalid');
        dateTo.classList.remove('is-invalid');
      }

      function showError() {
        errorEl.hidden = false;
        dateFrom.classList.add('is-invalid');
        dateTo.classList.add('is-invalid');
        dateFrom.focus();
      }

      [dateFrom, dateTo].forEach(function (input) {
        input.addEventListener('input', hideError);
        input.addEventListener('change', hideError);
      });

      form.addEventListener('submit', function (event) {
        if (dateTo.value && !dateFrom.value) {
          event.preventDefault();
          showError();
        }
      });
    });
  }

  function initReportPayDebtModal() {
    var modal = document.getElementById('reportPayDebtModal');
    var form = document.getElementById('reportPayDebtForm');
    var amountInput = document.getElementById('reportPayDebtAmount');
    var saleIdInput = document.getElementById('reportPayDebtSaleId');
    var saleTotInput = document.getElementById('reportPayDebtSaleTot');
    var titleEl = document.getElementById('reportPayDebtTitle');

    if (!modal || !form || !amountInput || !saleIdInput || !saleTotInput || !titleEl) {
      return;
    }

    document.querySelectorAll('.report-pay-debt-trigger').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        var balance = trigger.getAttribute('data-balance') || '0';
        var buyer = trigger.getAttribute('data-buyer') || 'customer';

        saleIdInput.value = trigger.getAttribute('data-sale-id') || '';
        saleTotInput.value = trigger.getAttribute('data-sale-tot') || '';
        amountInput.value = balance;
        amountInput.max = balance;
        titleEl.textContent = 'Collect payment · ' + buyer;
      });
    });
  }

  initReportEditModal();
  initReportNotesModal();
  initReportPayDebtModal();
  initReportFilterValidation();
})();
