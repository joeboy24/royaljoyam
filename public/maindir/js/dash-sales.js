(function () {
  'use strict';

  function initPosSearch(config) {
    var searchInput = document.getElementById('mySearch');
    var dropdown = document.getElementById('myDropdown');

    if (!searchInput || !dropdown) {
      return;
    }

    var catalogById = {};
    (config.catalog || []).forEach(function (item) {
      catalogById[String(item.id)] = item;
    });

    var fields = {
      itemId: document.getElementById('item_id'),
      itemNo: document.getElementById('item_no'),
      name: document.getElementById('name'),
      price: document.getElementById('price'),
      costPrice: document.getElementById('cost_price'),
      amt: document.getElementById('amt'),
      qtyAvl: document.getElementById('qty_avl'),
      brand: document.getElementById('brand'),
      desc: document.getElementById('desc'),
      info: document.getElementById('item_info'),
    };

    function closeDropdown() {
      dropdown.classList.remove('is-open');
    }

    function openDropdown() {
      if ((config.catalog || []).length > 0) {
        dropdown.classList.add('is-open');
      }
    }

    function filterItems() {
      var filter = searchInput.value.trim().toUpperCase();
      var items = dropdown.querySelectorAll('.dash-sales-dropdown-item');

      items.forEach(function (element) {
        var text = (element.textContent || '').toUpperCase();
        element.hidden = filter !== '' && text.indexOf(filter) === -1;
      });
    }

    function selectItem(itemId) {
      var item = catalogById[String(itemId)];

      if (!item) {
        return;
      }

      searchInput.value = item.name;

      if (fields.itemId) {
        fields.itemId.value = item.id;
      }

      if (fields.itemNo) {
        fields.itemNo.value = item.itemNo;
      }

      if (fields.name) {
        fields.name.value = item.name;
      }

      if (fields.costPrice) {
        fields.costPrice.value = item.costPrice;
      }

      if (fields.price) {
        fields.price.value = item.branchPrice;
      }

      if (fields.amt) {
        fields.amt.value = 'Gh\u20B5 ' + item.branchPrice;
      }

      if (fields.qtyAvl) {
        fields.qtyAvl.textContent = item.branchQty;
      }

      if (fields.brand) {
        fields.brand.textContent = item.brand || '\u2014';
      }

      if (fields.desc) {
        fields.desc.textContent = item.desc || '\u2014';
      }

      if (fields.info) {
        fields.info.classList.add('is-visible');
      }

      closeDropdown();
    }

    searchInput.addEventListener('input', function () {
      openDropdown();
      filterItems();
    });

    searchInput.addEventListener('focus', function () {
      openDropdown();
      filterItems();
    });

    dropdown.addEventListener('click', function (event) {
      var itemTrigger = event.target.closest('[data-item-id]');

      if (itemTrigger) {
        event.preventDefault();
        selectItem(itemTrigger.getAttribute('data-item-id'));
        return;
      }

      if (event.target.closest('[data-pos-close]')) {
        event.preventDefault();
        closeDropdown();
      }
    });

    document.addEventListener('click', function (event) {
      var wrap = document.querySelector('.dash-sales-search-wrap');

      if (wrap && !wrap.contains(event.target)) {
        closeDropdown();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeDropdown();
      }
    });
  }

  function formatMoney(amount) {
    return 'Gh\u20B5 ' + (Math.max(0, amount) || 0).toFixed(2);
  }

  function initCheckoutDrawer(config) {
    var drawer = document.getElementById('checkoutDrawer');
    var backdrop = document.getElementById('checkoutDrawerBackdrop');
    var openBtn = document.getElementById('openCheckoutDrawer');
    var closeBtn = document.getElementById('closeCheckoutDrawer');
    var cancelBtn = document.getElementById('cancelCheckoutDrawer');
    var form = document.getElementById('checkoutDrawerForm');

    if (!drawer || !backdrop || !openBtn || !form) {
      return;
    }

    var checkoutConfig = config.checkout || {};
    var cartTotal = parseFloat(checkoutConfig.cartTotal) || 0;
    var paymentInput = document.getElementById('checkoutPaymentAmount');
    var discountInput = document.getElementById('checkoutDiscount');
    var payModeSelect = document.getElementById('checkoutPayMode');
    var submitLabel = document.getElementById('checkoutSubmitLabel');
    var discountRow = document.getElementById('checkoutDiscountRow');
    var discountDisplay = document.getElementById('checkoutDiscountDisplay');
    var totalDueEl = document.getElementById('checkoutTotalDue');
    var subtotalEl = document.getElementById('checkoutSubtotal');
    var diffRow = document.getElementById('checkoutDiffRow');
    var diffLabel = document.getElementById('checkoutDiffLabel');
    var diffAmount = document.getElementById('checkoutDiffAmount');
    var hint = document.getElementById('checkoutHint');

    function isDebtMode() {
      return payModeSelect && payModeSelect.value === 'Post Payment(Debt)';
    }

    function getDiscount() {
      return Math.max(0, parseFloat(discountInput && discountInput.value) || 0);
    }

    function getTotalDue() {
      return Math.max(0, cartTotal - getDiscount());
    }

    function getPayment() {
      return Math.max(0, parseFloat(paymentInput && paymentInput.value) || 0);
    }

    function syncPaymentToTotalDue() {
      if (!paymentInput || isDebtMode()) {
        return;
      }

      paymentInput.value = getTotalDue().toFixed(2);
    }

    function updateCheckoutSummary() {
      var discount = getDiscount();
      var totalDue = getTotalDue();
      var payment = getPayment();
      var diff = payment - totalDue;
      var debtMode = isDebtMode();

      if (subtotalEl) {
        subtotalEl.textContent = formatMoney(cartTotal);
      }

      if (discountRow && discountDisplay) {
        var showDiscount = discount > 0;
        discountRow.hidden = !showDiscount;
        if (showDiscount) {
          discountDisplay.textContent = '- ' + formatMoney(discount);
        }
      }

      if (totalDueEl) {
        totalDueEl.textContent = formatMoney(totalDue);
      }

      if (diffRow && diffLabel && diffAmount) {
        diffRow.classList.remove('is-change', 'is-balance', 'is-shortfall');

        if (debtMode && diff < 0) {
          diffLabel.textContent = 'Balance';
          diffAmount.textContent = formatMoney(Math.abs(diff));
          diffRow.classList.add('is-balance');
        } else if (diff >= 0) {
          diffLabel.textContent = 'Change';
          diffAmount.textContent = formatMoney(diff);
          diffRow.classList.add('is-change');
        } else {
          diffLabel.textContent = 'Shortfall';
          diffAmount.textContent = formatMoney(Math.abs(diff));
          diffRow.classList.add('is-shortfall');
        }
      }

      if (hint) {
        hint.hidden = true;
        hint.classList.remove('is-error');

        if (discount > cartTotal) {
          hint.hidden = false;
          hint.classList.add('is-error');
          hint.textContent = 'Discount cannot be greater than the cart total.';
        } else if (!debtMode && payment < totalDue) {
          hint.hidden = false;
          hint.classList.add('is-error');
          hint.textContent = 'Amount paid must cover the total due, or select Post Payment (Debt).';
        } else if (debtMode && payment <= 0) {
          hint.hidden = false;
          hint.textContent = 'No payment now — the full amount will remain as debt.';
        }
      }

      if (submitLabel) {
        submitLabel.textContent = debtMode && payment < totalDue
          ? 'Complete · ' + formatMoney(payment) + ' now'
          : 'Pay bill · ' + formatMoney(totalDue);
      }
    }

    function openCheckoutDrawer() {
      backdrop.hidden = false;

      requestAnimationFrame(function () {
        backdrop.classList.add('is-visible');
        drawer.classList.add('is-open');
      });

      drawer.setAttribute('aria-hidden', 'false');
      openBtn.setAttribute('aria-expanded', 'true');
      document.body.classList.add('dash-sales-drawer-open');

      if (payModeSelect) {
        payModeSelect.focus();
      }

      updateCheckoutSummary();
    }

    function closeCheckoutDrawer() {
      backdrop.classList.remove('is-visible');
      drawer.classList.remove('is-open');
      drawer.setAttribute('aria-hidden', 'true');
      openBtn.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('dash-sales-drawer-open');

      window.setTimeout(function () {
        if (!drawer.classList.contains('is-open')) {
          backdrop.hidden = true;
        }
      }, 280);
    }

    if (payModeSelect) {
      payModeSelect.addEventListener('change', function () {
        syncPaymentToTotalDue();
        updateCheckoutSummary();
      });
    }

    if (discountInput) {
      discountInput.addEventListener('input', function () {
        syncPaymentToTotalDue();
        updateCheckoutSummary();
      });
    }

    if (paymentInput) {
      paymentInput.addEventListener('input', updateCheckoutSummary);
    }

    form.addEventListener('submit', function (event) {
      var totalDue = getTotalDue();
      var payment = getPayment();
      var discount = getDiscount();

      if (discount > cartTotal || (!isDebtMode() && payment < totalDue)) {
        event.preventDefault();
        updateCheckoutSummary();
      }
    });

    openBtn.addEventListener('click', openCheckoutDrawer);
    closeBtn.addEventListener('click', closeCheckoutDrawer);
    cancelBtn.addEventListener('click', closeCheckoutDrawer);
    backdrop.addEventListener('click', closeCheckoutDrawer);

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && drawer.classList.contains('is-open')) {
        closeCheckoutDrawer();
      }
    });

    updateCheckoutSummary();
  }

  document.addEventListener('DOMContentLoaded', function () {
    var config = window.dashSalesConfig || {};
    initPosSearch(config);
    initCheckoutDrawer(config);
  });
})();
