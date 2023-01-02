<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/maindir/css/login.css">
	<title>Login Form Using HTML And CSS Only</title>
</head>
<body>
	<div class="container" id="container">
		<div class="form-container log-in-container">
			<form method="POST" action="{{ route('login') }}">
                @csrf
				<h1>Login</h1>
				<!--div class="social-container">
					<a href="#" class="social"><i class="fa fa-facebook fa-2x"></i></a>
					<a href="#" class="social"><i class="fab fa fa-twitter fa-2x"></i></a>
				</div-->
				<div class="social-container">
					<a href="#" class="social"><i class="fab fa fa-unlock-alt fa-2x"></i></a>
				</div>
				<span>Provide Email & Password</span>
				<input name="email" type="email" placeholder="Email" id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus />
				@if ($errors->has('email'))
                    <span class="invalid-feedback strong" role="alert">
                        {{ $errors->first('email') }}
                    </span>
                @endif
                <input name="password" type="password" placeholder="Password" />
                @if ($errors->has('email'))
                    <span class="invalid-feedback strong" role="alert">
                        {{ $errors->first('password') }}
                    </span>
                @endif

                @if (Route::has('password.request'))
                    {{-- <a class="btn btn-link" href="{{ route('password.request') }}"> --}}
                    <a class="btn btn-link" href="#">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
                
                <button type="submit" class="btn theme_btn button_hover">
                    {{ __('Login') }}
                </button>
                
			</form>
		</div>
		<div class="overlay-container">
			<div class="overlay">
				<div class="overlay-panel overlay-right">
					<h1>Royal Joyam Ventures</h1>
					<p>Contact administrator if you have any issues with your login credentials.</p>
					{{-- <p>This login form is created using pure HTML and CSS. For social icons, FontAwesome is used.</p> --}}
				</div>
			</div>
		</div>
	</div>
</body>
</html>
