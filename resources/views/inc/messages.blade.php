<div class="dash-flash-list">
  @if (count($errors) > 0)
    @foreach ($errors->all() as $error)
      <x-flash-alert type="error" :message="$error" />
    @endforeach
  @endif

  @if (session('success'))
    <x-flash-alert type="success" :message="session('success')" />
  @endif

  @if (session('error'))
    <x-flash-alert type="error" :message="session('error')" />
  @endif

  @if (session('warning'))
    <x-flash-alert type="warning" :message="session('warning')" />
  @endif

  @if (session('info'))
    <x-flash-alert type="info" :message="session('info')" />
  @endif

  @if (session('status'))
    <x-flash-alert type="info" :message="session('status')" />
  @endif
</div>

@once
  <script>
    (function () {
      function dismissFlash(flash) {
        if (!flash || flash.classList.contains('is-hiding')) {
          return;
        }
        flash.classList.add('is-hiding');
        window.setTimeout(function () {
          flash.remove();
        }, 280);
      }

      document.addEventListener('click', function (event) {
        var closeBtn = event.target.closest('[data-dash-flash-close]');
        if (closeBtn) {
          dismissFlash(closeBtn.closest('[data-dash-flash]'));
        }
      });

      document.querySelectorAll('[data-dash-flash]').forEach(function (flash) {
        window.setTimeout(function () {
          dismissFlash(flash);
        }, 7000);
      });
    })();
  </script>
@endonce
