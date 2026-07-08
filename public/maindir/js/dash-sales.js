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

  function initCheckoutDrawer() {
    var drawer = document.getElementById('checkoutDrawer');
    var backdrop = document.getElementById('checkoutDrawerBackdrop');
    var openBtn = document.getElementById('openCheckoutDrawer');
    var closeBtn = document.getElementById('closeCheckoutDrawer');
    var cancelBtn = document.getElementById('cancelCheckoutDrawer');

    if (!drawer || !backdrop || !openBtn) {
      return;
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

    openBtn.addEventListener('click', openCheckoutDrawer);
    closeBtn.addEventListener('click', closeCheckoutDrawer);
    cancelBtn.addEventListener('click', closeCheckoutDrawer);
    backdrop.addEventListener('click', closeCheckoutDrawer);

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && drawer.classList.contains('is-open')) {
        closeCheckoutDrawer();
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initPosSearch(window.dashSalesConfig || {});
    initCheckoutDrawer();
  });
})();
