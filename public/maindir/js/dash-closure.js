(function () {
  function closeRow(row, panel) {
    row.classList.remove('is-expanded');
    row.setAttribute('aria-expanded', 'false');
    panel.hidden = true;
  }

  function openRow(row, panel) {
    row.classList.add('is-expanded');
    row.setAttribute('aria-expanded', 'true');
    panel.hidden = false;
  }

  function toggleRow(row) {
    var targetId = row.getAttribute('data-daily-close-target');
    if (!targetId) {
      return;
    }

    var panel = document.getElementById(targetId);
    if (!panel) {
      return;
    }

    var willOpen = panel.hidden;
    document.querySelectorAll('[data-daily-close-toggle].is-expanded').forEach(function (openRowEl) {
      var openId = openRowEl.getAttribute('data-daily-close-target');
      var openPanel = openId ? document.getElementById(openId) : null;
      if (openPanel) {
        closeRow(openRowEl, openPanel);
      }
    });

    if (willOpen) {
      openRow(row, panel);
    }
  }

  function initDailyCloseRows() {
    document.querySelectorAll('[data-daily-close-toggle]').forEach(function (row) {
      row.addEventListener('click', function (event) {
        if (event.target.closest('[data-daily-close-stop]')) {
          return;
        }
        event.preventDefault();
        toggleRow(row);
      });

      row.addEventListener('keydown', function (event) {
        if (event.key !== 'Enter' && event.key !== ' ') {
          return;
        }
        if (event.target.closest('[data-daily-close-stop]')) {
          return;
        }
        event.preventDefault();
        toggleRow(row);
      });
    });
  }

  document.addEventListener('DOMContentLoaded', initDailyCloseRows);
})();
