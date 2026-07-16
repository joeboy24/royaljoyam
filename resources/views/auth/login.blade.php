<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Royal Joyam Ventures · Sign in</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="/maindir/css/login.css?v=3">
</head>
<body class="login-page">
  <div class="login-backdrop" aria-hidden="true"></div>

  <main class="login-shell">
    <section class="login-brand" aria-label="Royal Joyam Ventures">
      <div class="login-brand-inner">
        <span class="login-brand-mark">RJV</span>
        <h1 class="login-brand-title">Royal Joyam Ventures</h1>
        <p class="login-brand-lead">Manage inventory, sales, and daily branch operations from one secure dashboard.</p>

        <ul class="login-brand-points">
          <li><i class="fa fa-check-circle" aria-hidden="true"></i> Real-time stock and sales tracking</li>
          <li><i class="fa fa-check-circle" aria-hidden="true"></i> Branch-aware pricing and fulfillment</li>
          <li><i class="fa fa-check-circle" aria-hidden="true"></i> Reports, waybills, and daily closure</li>
        </ul>
      </div>
    </section>

    <section class="login-panel">
      <div class="login-panel-inner">
        <header class="login-panel-header">
          <span class="login-panel-kicker">Welcome back</span>
          <h2 class="login-panel-title">Sign in to your account</h2>
          <p class="login-panel-subtitle">Use your staff email and password to continue.</p>
        </header>

        <form class="login-form" method="POST" action="{{ route('login') }}" novalidate>
          @csrf

          <label class="login-field">
            <span class="login-label">Email address</span>
            <span class="login-input-wrap">
              <i class="fa fa-envelope-o login-input-icon" aria-hidden="true"></i>
              <input
                id="email"
                class="login-input{{ $errors->has('email') ? ' is-invalid' : '' }}"
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="you@company.com"
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
          <p>Need access? Contact your administrator for login credentials.</p>
        </footer>
      </div>
    </section>
  </main>
</body>
</html>
