<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Royal Joyam Ventures · Setup</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="/maindir/css/login.css?v=3">
  <link rel="stylesheet" href="/maindir/css/setup.css?v=1">
</head>
<body class="login-page setup-page">
  <div class="login-backdrop" aria-hidden="true"></div>

  <main class="login-shell setup-shell">
    <section class="login-brand" aria-label="Royal Joyam Ventures">
      <div class="login-brand-inner">
        <span class="login-brand-mark">RJV</span>
        <h1 class="login-brand-title">Royal Joyam Ventures</h1>
        <p class="login-brand-lead">First-time setup prepares your company, branches, and administrator so the system can run safely.</p>

        <ol class="setup-steps" aria-label="Setup progress">
          <li class="{{ $migrationsPending ? 'is-current' : 'is-done' }}">Initialize database</li>
          <li class="{{ $step === 'company' ? 'is-current' : ($hasCompany ? 'is-done' : '') }}">Company details</li>
          <li class="{{ $step === 'branch' ? 'is-current' : ($hasBranch ? 'is-done' : '') }}">First branch</li>
          <li class="{{ $step === 'admin' ? 'is-current' : ($hasAdministrator ? 'is-done' : '') }}">Administrator</li>
        </ol>
      </div>
    </section>

    <section class="login-panel">
      <div class="login-panel-inner">
        <header class="login-panel-header">
          <span class="login-panel-kicker">System setup</span>
          <h2 class="login-panel-title">
            @if ($step === 'migrate')
              Initialize database
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
              Run migrations once to create the required tables. This does not run on every page load.
            @elseif ($step === 'company')
              Enter the company profile used on invoices and reports.
            @elseif ($step === 'branch')
              At least one branch is required for stock and sales.
            @else
              Create a day-to-day Administrator account (separate from SuperAdmin).
            @endif
          </p>
        </header>

        @if (session('success'))
          <div class="setup-flash setup-flash-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="setup-flash setup-flash-error" role="alert">{{ session('error') }}</div>
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
              <span>Initialize database</span>
              <i class="fa fa-database" aria-hidden="true"></i>
            </button>
          </form>
        @elseif ($step === 'company')
          <form class="login-form setup-form" method="POST" action="{{ route('setup.company') }}" enctype="multipart/form-data">
            @csrf

            <label class="login-field">
              <span class="login-label">Company name</span>
              <input class="login-input" type="text" name="name" value="{{ old('name') }}" required>
            </label>

            <label class="login-field">
              <span class="login-label">Address</span>
              <textarea class="login-input setup-textarea" name="company_add" rows="3" required>{{ old('company_add') }}</textarea>
            </label>

            <label class="login-field">
              <span class="login-label">Location</span>
              <input class="login-input" type="text" name="loc" value="{{ old('loc') }}">
            </label>

            <label class="login-field">
              <span class="login-label">Contact number</span>
              <input class="login-input" type="text" name="contact" value="{{ old('contact') }}" required>
            </label>

            <label class="login-field">
              <span class="login-label">Email</span>
              <input class="login-input" type="email" name="email" value="{{ old('email') }}">
            </label>

            <label class="login-field">
              <span class="login-label">Website</span>
              <input class="login-input" type="text" name="company_web" value="{{ old('company_web') }}">
            </label>

            <label class="login-field">
              <span class="login-label">Company logo (optional)</span>
              <input class="login-input" type="file" name="company_logo" accept="image/jpeg,image/png,image/jpg">
            </label>

            <button type="submit" class="login-submit">
              <span>Save company</span>
              <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </button>
          </form>
        @elseif ($step === 'branch')
          <form class="login-form setup-form" method="POST" action="{{ route('setup.branch') }}">
            @csrf

            <label class="login-field">
              <span class="login-label">Branch name</span>
              <input class="login-input" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Main Branch" required>
            </label>

            <label class="login-field">
              <span class="login-label">Location</span>
              <input class="login-input" type="text" name="loc" value="{{ old('loc') }}" required>
            </label>

            <label class="login-field">
              <span class="login-label">Contact</span>
              <input class="login-input" type="text" name="contact" value="{{ old('contact') }}" required>
            </label>

            <button type="submit" class="login-submit">
              <span>Save branch</span>
              <i class="fa fa-arrow-right" aria-hidden="true"></i>
            </button>
          </form>
        @else
          <form class="login-form setup-form" method="POST" action="{{ route('setup.admin') }}">
            @csrf

            <label class="login-field">
              <span class="login-label">Username</span>
              <input class="login-input" type="text" name="name" value="{{ old('name') }}" required>
            </label>

            <label class="login-field">
              <span class="login-label">Email</span>
              <input class="login-input" type="email" name="email" value="{{ old('email') }}" required>
            </label>

            <label class="login-field">
              <span class="login-label">Password</span>
              <input class="login-input" type="password" name="password" required autocomplete="new-password">
            </label>

            <label class="login-field">
              <span class="login-label">Confirm password</span>
              <input class="login-input" type="password" name="password_confirmation" required autocomplete="new-password">
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
          <p>Already set up? <a href="{{ route('login') }}">Sign in</a></p>
        </footer>
      </div>
    </section>
  </main>
</body>
</html>
