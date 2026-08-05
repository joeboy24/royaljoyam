<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Company Manager · Setup</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="/maindir/css/login.css?v=4">
  <link rel="stylesheet" href="/maindir/css/setup.css?v=4">
</head>
<body class="login-page setup-page">
  <div class="login-backdrop" aria-hidden="true"></div>

  <main class="login-shell setup-shell">
    <section class="login-brand" aria-label="Company Manager by PivoApps">
      <div class="login-brand-inner">
        <span class="login-brand-mark">CM</span>
        <h1 class="login-brand-title">Company Manager</h1>
        <p class="login-brand-by">by PivoApps</p>
        <p class="login-brand-lead">One place to run inventory, sales, expenses, and multi-branch operations.</p>

        <ul class="login-brand-points">
          <li><i class="fa fa-check-circle" aria-hidden="true"></i> Inventory tracking with branch stock</li>
          <li><i class="fa fa-check-circle" aria-hidden="true"></i> Sales, debts, and daily close</li>
          <li><i class="fa fa-check-circle" aria-hidden="true"></i> Expenses and reporting</li>
          <li><i class="fa fa-check-circle" aria-hidden="true"></i> Waybills and distribution</li>
          <li><i class="fa fa-check-circle" aria-hidden="true"></i> Month-end closure and staff registry</li>
        </ul>

        <ol class="setup-steps" aria-label="Setup progress">
          <li class="{{ $migrationsPending ? 'is-current' : 'is-done' }}">Get started</li>
          <li class="{{ $step === 'company' ? 'is-current' : ($hasCompany ? 'is-done' : '') }}">Company details</li>
          <li class="{{ $step === 'branch' ? 'is-current' : ($hasBranch ? 'is-done' : '') }}">First branch</li>
          <li class="{{ $step === 'admin' ? 'is-current' : ($hasAdministrator ? 'is-done' : '') }}">Administrator</li>
        </ol>
      </div>
    </section>

    <section class="login-panel">
      <div class="login-panel-inner">
        <header class="login-panel-header">
          <span class="login-panel-kicker">Get started</span>
          <h2 class="login-panel-title">
            @if ($step === 'migrate')
              Prepare your workspace
            @elseif ($step === 'company')
              Company details
            @elseif ($step === 'branch')
              Add first branch
            @else
              Create administrator
            @endif
          </h2>
          <p class="login-panel-subtitle">
            @if ($step === 'migrate')
              Prepare your workspace so you can add your company and start managing operations.
            @elseif ($step === 'company')
              Enter the company profile used on invoices and reports.
            @elseif ($step === 'branch')
              At least one branch is required for stock and sales.
            @else
              Create a day-to-day Administrator account for your team.
            @endif
          </p>
        </header>

        @if (session('success'))
          <div class="setup-flash setup-flash-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="setup-flash setup-flash-error" role="alert">{{ session('error') }}</div>
        @endif
        @if (session('info'))
          <div class="setup-flash setup-flash-info" role="status">{{ session('info') }}</div>
        @endif

        @if ($errors->any())
          <div class="setup-errors" role="alert">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if ($step === 'migrate')
          <form class="login-form" method="POST" action="{{ route('setup.migrate') }}">
            @csrf
            <button type="submit" class="login-submit">
              <span>Get started</span>
              <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </button>
          </form>
        @elseif ($step === 'company')
          <form class="login-form setup-form" method="POST" action="{{ route('setup.company') }}" enctype="multipart/form-data">
            @csrf
            <p class="setup-required-note"><span class="setup-required">*</span> Required fields</p>

            <label class="login-field">
              <span class="login-label">Company name <span class="setup-required" aria-hidden="true">*</span></span>
              <input class="login-input" type="text" name="name" value="{{ old('name') }}" required aria-required="true">
            </label>

            <label class="login-field">
              <span class="login-label">Address <span class="setup-required" aria-hidden="true">*</span></span>
              <textarea class="login-input setup-textarea" name="company_add" rows="3" required aria-required="true">{{ old('company_add') }}</textarea>
            </label>

            <label class="login-field">
              <span class="login-label">Location <span class="setup-optional">(optional)</span></span>
              <input class="login-input" type="text" name="loc" value="{{ old('loc') }}">
            </label>

            <label class="login-field">
              <span class="login-label">Contact number <span class="setup-required" aria-hidden="true">*</span></span>
              <input class="login-input" type="text" name="contact" value="{{ old('contact') }}" required aria-required="true">
            </label>

            <label class="login-field">
              <span class="login-label">Email <span class="setup-optional">(optional)</span></span>
              <input class="login-input" type="email" name="email" value="{{ old('email') }}">
            </label>

            <label class="login-field">
              <span class="login-label">Website <span class="setup-optional">(optional)</span></span>
              <input class="login-input" type="text" name="company_web" value="{{ old('company_web') }}">
            </label>

            <div class="login-field">
              <span class="login-label">Company logo <span class="setup-optional">(optional)</span></span>
              <label class="setup-file" for="company_logo">
                <input
                  id="company_logo"
                  class="setup-file-input"
                  type="file"
                  name="company_logo"
                  accept="image/jpeg,image/png,image/jpg"
                >
                <span class="setup-file-body">
                  <span class="setup-file-media" aria-hidden="true">
                    <span class="setup-file-icon"><i class="fa fa-image"></i></span>
                    <img src="" alt="" class="setup-file-preview-img" hidden>
                  </span>
                  <span class="setup-file-copy">
                    <span class="setup-file-title">Upload logo</span>
                    <span class="setup-file-hint">PNG or JPG, up to 5MB</span>
                    <span class="setup-file-name">No file chosen</span>
                  </span>
                </span>
              </label>
            </div>

            <button type="submit" class="login-submit">
              <span>Continue</span>
              <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </button>
          </form>
        @elseif ($step === 'branch')
          <form class="login-form setup-form" method="POST" action="{{ route('setup.branch') }}">
            @csrf
            <p class="setup-required-note"><span class="setup-required">*</span> Required fields</p>

            <label class="login-field">
              <span class="login-label">Branch name <span class="setup-required" aria-hidden="true">*</span></span>
              <input class="login-input" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Main Branch" required aria-required="true">
            </label>

            <label class="login-field">
              <span class="login-label">Location <span class="setup-required" aria-hidden="true">*</span></span>
              <input class="login-input" type="text" name="loc" value="{{ old('loc') }}" required aria-required="true">
            </label>

            <label class="login-field">
              <span class="login-label">Contact <span class="setup-required" aria-hidden="true">*</span></span>
              <input class="login-input" type="text" name="contact" value="{{ old('contact') }}" required aria-required="true">
            </label>

            <button type="submit" class="login-submit">
              <span>Continue</span>
              <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </button>
          </form>
        @else
          <form class="login-form setup-form" method="POST" action="{{ route('setup.admin') }}">
            @csrf
            <p class="setup-required-note"><span class="setup-required">*</span> Required fields</p>

            <label class="login-field">
              <span class="login-label">Username <span class="setup-required" aria-hidden="true">*</span></span>
              <input class="login-input" type="text" name="name" value="{{ old('name') }}" required aria-required="true">
            </label>

            <label class="login-field">
              <span class="login-label">Email <span class="setup-required" aria-hidden="true">*</span></span>
              <input class="login-input" type="email" name="email" value="{{ old('email') }}" required aria-required="true">
            </label>

            <label class="login-field">
              <span class="login-label">Password <span class="setup-required" aria-hidden="true">*</span></span>
              <input class="login-input" type="password" name="password" required aria-required="true" autocomplete="new-password">
            </label>

            <label class="login-field">
              <span class="login-label">Confirm password <span class="setup-required" aria-hidden="true">*</span></span>
              <input class="login-input" type="password" name="password_confirmation" required aria-required="true" autocomplete="new-password">
            </label>

            <button type="submit" class="login-submit">
              <span>Finish setup</span>
              <i class="fa fa-check" aria-hidden="true"></i>
            </button>
          </form>

          <form class="setup-skip-form" method="POST" action="{{ route('setup.skip-admin') }}">
            @csrf
            <button type="submit" class="setup-skip-btn">Skip for now — sign in instead</button>
          </form>
        @endif

        <footer class="login-panel-footer">
          <p>Already set up? <a href="{{ route('setup.signin') }}">Sign in</a></p>
        </footer>
      </div>
    </section>
  </main>

  <script>
    (function () {
      var input = document.getElementById('company_logo');
      if (!input) return;

      var root = input.closest('.setup-file');
      var nameEl = root.querySelector('.setup-file-name');
      var titleEl = root.querySelector('.setup-file-title');
      var hintEl = root.querySelector('.setup-file-hint');
      var iconEl = root.querySelector('.setup-file-icon');
      var previewImg = root.querySelector('.setup-file-preview-img');
      var objectUrl = null;

      input.addEventListener('change', function () {
        var file = input.files && input.files[0];

        if (objectUrl) {
          URL.revokeObjectURL(objectUrl);
          objectUrl = null;
        }

        if (!file) {
          root.classList.remove('has-file');
          nameEl.textContent = 'No file chosen';
          titleEl.textContent = 'Upload logo';
          hintEl.hidden = false;
          iconEl.hidden = false;
          previewImg.hidden = true;
          previewImg.removeAttribute('src');
          return;
        }

        objectUrl = URL.createObjectURL(file);
        root.classList.add('has-file');
        nameEl.textContent = file.name;
        titleEl.textContent = 'Logo selected';
        hintEl.hidden = true;
        iconEl.hidden = true;
        previewImg.hidden = false;
        previewImg.src = objectUrl;
        previewImg.alt = file.name;
      });
    })();
  </script>
</body>
</html>
