<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="/maindir/image/favicon.png" type="image/png">
        <title>Royal JV </title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="/maindir/css/bootstrap.css">
        <link rel="stylesheet" href="/maindir/vendors/linericon/style.css">
        <link rel="stylesheet" href="/maindir/css/font-awesome.min.css">
        <link rel="stylesheet" href="/maindir/vendors/owl-carousel/owl.carousel.min.css">
        <link rel="stylesheet" href="/maindir/vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.css">
        <link rel="stylesheet" href="/maindir/vendors/nice-select/css/nice-select.css">
        <link rel="stylesheet" href="/maindir/vendors/owl-carousel/owl.carousel.min.css">
        <!-- Gallery CSS -->
        <link rel="stylesheet" href="/maindir/css/bootstrap.min.css">
        <link rel="stylesheet" href="/maindir/css/magnific-popup.css">
        <link rel="stylesheet" href="/maindir/css/jquery-ui.css">
        <link rel="stylesheet" href="/maindir/css/owl.carousel.min.css">
        <link rel="stylesheet" href="/maindir/css/owl.theme.default.min.css">

        <link rel="stylesheet" href="/maindir/css/bootstrap-datepicker.css">

        <link rel="stylesheet" href="/maindir/fonts/flaticon/font/flaticon.css">

        <link rel="stylesheet" href="/maindir/css/aos.css">
        <link rel="stylesheet" href="/maindir/css/fancybox.min.css">

        <link rel="stylesheet" href="/maindir/css/style2.css">
        <!-- main css -->
        <link rel="stylesheet" href="/maindir/css/style.css">
        <link rel="stylesheet" href="/maindir/css/responsive.css">
    </head>
    <body> 
        <!--================Header Area =================-->
        <header class="header_area">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="offset-lg-0 aglogo2">
                        <a class="navbar-brand" href="/"><img src="/maindir/image/pivo7.png" alt="" class="homeLogo"></a>
                    </div>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
                        <ul class="nav navbar-nav menu_nav ml-auto">
                            {{-- <li class="nav-item active"><a class="nav-link" href="/">Home</a></li>  --}}
                            
                            @guest
                                <li class="userLogin">
                                    <i class="fa fa-user userIcon"></i>&nbsp;&nbsp;<a class="userLoginA" href="" data-toggle="modal" data-target="#log">{{ __('Login') }}</a>
                                </li>
                                @if (Route::has('register'))
                                    <!--li class="userLoginA">
                                    <a class="userLoginA" href="{{ route('register') }}">{{ __('Register') }}</a>
                                    </li-->
                                @endif
                            @else 

                                <li class="nav-item submenu dropdown userLogin" id="loginId">
                                        <a id="navbarDropdown" class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                {{ Auth::user()->name }} <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            @if (Auth::user()->user_type != 'useruser')
                                                <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                                            @endif

                                            <li class="nav-item"><a class="nav-link" href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                                                document.getElementById('logout-form').submit();">
                                                    {{ __('Logout') }}
                                            </a></li>

                                            <li class="nav-item"><form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                    @csrf
                                            </form></li>
                                        </ul>
                                    </li> 
                            @endguest
                        </ul>
                    </div>
                </nav>
            </div>
        </header>
        <!--================Header Area =================-->
        
        <div class="">
        
        @yield('content')
        </div>




        <div class="modal fade homelog" id="log" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;{{ __('Login') }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  


                        <div class="row justify-content-center">
                            <div class="col-md-10">
                    
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                    
                                            <div class="form-group row">
                                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                    
                                                <div class="col-md-6">
                                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>
                    
                                                    @if ($errors->has('email'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                    
                                            <div class="form-group row">
                                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                    
                                                <div class="col-md-6">
                                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                    
                                                    @if ($errors->has('password'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('password') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                    
                                            <div class="form-group row">
                                                <div class="col-md-6 offset-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    
                                                        <label class="form-check-label" for="remember">
                                                            {{ __('Remember Me') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                    
                                            <div class="form-group row mb-0">
                                                <div class="col-md-8 offset-md-4">
                                                    <button type="submit" class="btn theme_btn button_hover">
                                                        {{ __('Login') }}
                                                    </button>
                    
                                                    @if (Route::has('password.request'))
                                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                                            {{ __('Forgot Your Password?') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                            </div>
                        </div>
                    


                    

                </div>
              </div>
            </div>
        </div>


        <div class="modal fade homelog" id="reg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-user"></i>&nbsp;&nbsp;&nbsp;{{ __('Login') }}</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  


                        <div class="row justify-content-center">
                            <div class="col-md-10">
                              
                                    <div class="card-body">
                                        <form method="POST" action="{{ route('register') }}">
                                            @csrf
                    
                                            <div class="form-group row">
                                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>
                    
                                                <div class="col-md-6">
                                                    <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required autofocus>
                    
                                                    @if ($errors->has('name'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('name') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                    
                                            <div class="form-group row">
                                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                    
                                                <div class="col-md-6">
                                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
                    
                                                    @if ($errors->has('email'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('email') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                    
                                            <div class="form-group row">
                                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                    
                                                <div class="col-md-6">
                                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                    
                                                    @if ($errors->has('password'))
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $errors->first('password') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                    
                                            <div class="form-group row">
                                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>
                    
                                                <div class="col-md-6">
                                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                                </div>
                                            </div>
                    
                                            <div class="form-group row mb-0">
                                                <div class="col-md-8 offset-md-4">
                                                    <button type="submit" class="btn theme_btn button_hover">
                                                        {{ __('Register') }}
                                                    </button>
                                                </div>
                                            </div>


                                        </form>
                                    </div>


                            </div>
                        </div>
                    


                    

                </div>
              </div>
            </div>
        </div>
        
        
        
          <!--================ start footer Area  =================-->	
        <footer class="footer-area section_gap">
            {{-- <div id="myLogo"><i class="fa fa-home"></i></div> --}}
            <div class="container">
                <div class="row footer-bottom d-flex justify-content-between align-items-center">
                    <p class="col-lg-8 col-sm-12 footer-text m-0"><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | <a href="#" target="_blank">PivoApps <img src="maindir/image/pivo.png" class="pivo" /></a>
                    <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
                    <div class="col-lg-4 col-sm-12 footer-social">
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-dribbble"></i></a>
                        <a href="#"><i class="fa fa-behance"></i></a>
                    </div>
                </div>
            </div>
        </footer>
		<!--================ End footer Area  =================-->
        
        <!-- Galery Scripts -->
        <script src="maindir/js/jquery-3.3.1.min.js"></script>
        <script src="maindir/js/jquery-migrate-3.0.1.min.js"></script>
        <script src="maindir/js/jquery-ui.js"></script>
        <script src="maindir/js/popper.min.js"></script>
        <script src="maindir/js/bootstrap.min.js"></script>
        <script src="maindir/js/owl.carousel.min.js"></script>
        <script src="maindir/js/jquery.stellar.min.js"></script>
        <script src="maindir/js/jquery.countdown.min.js"></script>
        <script src="maindir/js/jquery.magnific-popup.min.js"></script>
        <script src="maindir/js/bootstrap-datepicker.min.js"></script>
        <script src="maindir/js/aos.js"></script>

        <script src="maindir/js/jquery.fancybox.min.js"></script>

        <script src="maindir/js/main.js"></script>

        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="/maindir/js/popper.js"></script>
        <script src="/maindir/js/bootstrap.min.js"></script>
        <script src="/maindir/vendors/owl-carousel/owl.carousel.min.js"></script>
        <script src="/maindir/js/jquery.ajaxchimp.min.js"></script>
        <script src="/maindir/js/mail-script.js"></script>
        <script src="/maindir/vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.js"></script>
        <script src="/maindir/vendors/nice-select/js/jquery.nice-select.js"></script>
        <script src="/maindir/js/mail-script.js"></script>
        <script src="/maindir/js/stellar.js"></script>
        <script src="/maindir/vendors/lightbox/simpleLightbox.min.js"></script>
        <script src="/maindir/js/custom.js"></script>
    </body>
</html>