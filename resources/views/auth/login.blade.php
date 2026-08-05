<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Company Manager · Sign in</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="/maindir/css/login.css?v=4">
  <link rel="stylesheet" href="/maindir/css/setup.css?v=2">
</head>
<body class="login-page">
  <div class="login-backdrop" aria-hidden="true"></div>

  <main class="login-shell">
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
      </div>
    </section>

    <section class="login-panel">
      <div class="login-panel-inner">
        <header class="login-panel-header">
          <span class="login-panel-kicker">Welcome back</span>
          <h2 class="login-panel-title">Sign in to your account</h2>
          <p class="login-panel-subtitle">Use your staff email or username and password to continue.</p>
        </header>

        @if (session('info'))
          <div class="setup-flash setup-flash-info" role="status">{{ session('info') }}</div>
        @endif
        @if (session('success'))
          <div class="setup-flash setup-flash-success" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="setup-flash setup-flash-error" role="alert">{{ session('error') }}</div>
        @endif

        <form class="login-form" method="POST" action="{{ route('login') }}" novalidate>
          @csrf

          <label class="login-field">
            <span class="login-label">Email or username</span>
            <span class="login-input-wrap">
              <i class="fa fa-envelope-o login-input-icon" aria-hidden="true"></i>
              <input
                id="email"
                class="login-input{{ $errors->has('email') ? ' is-invalid' : '' }}"
                type="text"
                name="email"
                value="{{ old('email') }}"
                placeholder="you@company.com or username"
                autocomplete="username"
                required
                autofocus
              />
            </span>
            @if ($errors->has('email'))
              <span class="login-error" role="alert">{{ $errors->first('email') }}</span>
            @endif
          </label>

          <label class="login-field">
            <span class="login-label">Password</span>
            <span class="login-input-wrap">
              <i class="fa fa-lock login-input-icon" aria-hidden="true"></i>
              <input
                id="password"
                class="login-input{{ $errors->has('password') ? ' is-invalid' : '' }}"
                type="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
              />
            </span>
            @if ($errors->has('password'))
              <span class="login-error" role="alert">{{ $errors->first('password') }}</span>
            @endif
          </label>

          <div class="login-form-meta">
            <label class="login-remember">
              <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <span>Remember me</span>
            </label>

            @if (Route::has('password.request'))
              <a class="login-forgot" href="{{ route('password.request') }}">Forgot password?</a>
            @endif
          </div>

          <button type="submit" class="login-submit">
            <span>Sign in</span>
            <i class="fa fa-arrow-right" aria-hidden="true"></i>
          </button>
        </form>

        <footer class="login-panel-footer">
          <p>
            @if (Route::has('setup.show'))
              New here? <a href="{{ route('setup.show') }}">Get started</a>
            @else
              Need access? Contact your administrator for login credentials.
            @endif
          </p>
        </footer>
      </div>
    </section>
  </main>
</body>
</html>
