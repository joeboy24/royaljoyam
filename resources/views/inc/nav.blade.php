


<!-- Right Side Of Navbar -->
        <!-- Authentication Links -->
        @guest
            <li class="userLogin">
                <i class="fa fa-user userIcon"></i>&nbsp;&nbsp;<a class="userLoginA" href="{{ route('login') }}">{{ __('Login') }}</a>
            </li>
            @if (Route::has('register'))
                <!--li class="userLoginA">
                <a class="userLoginA" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li-->
            @endif
        @else 
            <li class="nav-item submenu dropdown" id="loginId">
                    <a id="navbarDropdown" class="dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->fname }} <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        @if (Auth::user()->user_type != 'useruser')
                            <li class="nav-item"><a class="nav-link" href="/reserves">Dashboard</a></li>
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