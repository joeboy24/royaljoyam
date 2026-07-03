(function () {
  function initCollapsibleFilters(panel) {
    var toggle = panel.querySelector('.inventory-filters-toggle');
    var body = panel.querySelector('.inventory-filters-body');
    var form = panel.closest('form');

    if (!toggle || !body) {
      return;
    }

    function isExpanded() {
      return panel.classList.contains('is-expanded');
    }

    function setExpanded(expanded) {
      panel.classList.toggle('is-collapsed', !expanded);
      panel.classList.toggle('is-expanded', expanded);
      toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    }

    function collapse() {
      setExpanded(false);
    }

    function expand() {
      setExpanded(true);
    }

    toggle.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();

      if (isExpanded()) {
        collapse();
      } else {
        expand();
      }
    });

    body.addEventListener('click', function (event) {
      event.stopPropagation();
    });

    if (form) {
      form.addEventListener('submit', function (event) {
        var submitter = event.submitter;

        if (submitter && submitter.classList.contains('inventory-filters-apply')) {
          collapse();
        }
      });
    }

    document.addEventListener('click', function () {
      if (isExpanded()) {
        collapse();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && isExpanded()) {
        collapse();
      }
    });

    collapse();
  }

  document.querySelectorAll('[data-collapsible-filters]').forEach(initCollapsibleFilters);
})();
