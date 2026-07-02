(function () {
  var layer = null;
  var activeTrigger = null;

  function getLayer() {
    if (!layer) {
      layer = document.createElement('div');
      layer.className = 'dash-tip-layer';
      layer.setAttribute('role', 'tooltip');
      document.body.appendChild(layer);
    }

    return layer;
  }

  function hideTip() {
    activeTrigger = null;

    if (!layer) {
      return;
    }

    layer.classList.remove('is-visible', 'dash-tip-layer--bottom');
    layer.textContent = '';
  }

  function showTip(trigger) {
    var text = trigger.getAttribute('data-tip');

    if (!text) {
      hideTip();
      return;
    }

    activeTrigger = trigger;
    var tip = getLayer();
    var isBottom = trigger.classList.contains('dash-tip--bottom');

    tip.textContent = text;
    tip.classList.toggle('dash-tip-layer--bottom', isBottom);
    tip.style.top = '-9999px';
    tip.style.left = '-9999px';
    tip.classList.add('is-visible');

    var rect = trigger.getBoundingClientRect();
    var tipRect = tip.getBoundingClientRect();
    var gap = 9;
    var top = isBottom ? rect.bottom + gap : rect.top - tipRect.height - gap;
    var left = rect.left + rect.width / 2 - tipRect.width / 2;
    var maxLeft = window.innerWidth - tipRect.width - 8;

    if (left < 8) {
      left = 8;
    } else if (left > maxLeft) {
      left = Math.max(8, maxLeft);
    }

    if (top < 8) {
      top = rect.bottom + gap;
      tip.classList.add('dash-tip-layer--bottom');
    }

    tip.style.top = top + 'px';
    tip.style.left = left + 'px';
  }

  function repositionTip() {
    if (activeTrigger) {
      showTip(activeTrigger);
    }
  }

  document.addEventListener(
    'mouseover',
    function (event) {
      var trigger = event.target.closest('.dash-tip[data-tip]');

      if (trigger) {
        showTip(trigger);
      }
    },
    true
  );

  document.addEventListener(
    'mouseout',
    function (event) {
      var trigger = event.target.closest('.dash-tip[data-tip]');

      if (!trigger) {
        return;
      }

      var related = event.relatedTarget;

      if (!related || !trigger.contains(related)) {
        hideTip();
      }
    },
    true
  );

  document.addEventListener(
    'focusin',
    function (event) {
      var trigger = event.target.closest('.dash-tip[data-tip]');

      if (trigger) {
        showTip(trigger);
      }
    },
    true
  );

  document.addEventListener(
    'focusout',
    function (event) {
      var trigger = event.target.closest('.dash-tip[data-tip]');

      if (!trigger) {
        return;
      }

      var related = event.relatedTarget;

      if (!related || !trigger.contains(related)) {
        hideTip();
      }
    },
    true
  );

  window.addEventListener('scroll', hideTip, true);
  window.addEventListener('resize', hideTip);
})();
