<!DOCTYPE html>
<html lang="en" data-sidebar-theme="ocean">

<head> 
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="/dashdir/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/dashdir/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>Royal JV</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="/dashdir/css/material-dashboard.css?v=2.1.1" rel="stylesheet" />
  <link rel="stylesheet" href="/maindir/css/style.css">
  <link rel="stylesheet" href="/maindir/css/dash-sidebar.css?v=12">
  <link rel="stylesheet" href="/maindir/css/dash-page-header.css?v=1">
  <link rel="stylesheet" href="/maindir/css/dash-form.css?v=30">
  <link rel="stylesheet" href="/maindir/css/dash-sales.css?v=15">
  <link rel="stylesheet" href="/maindir/css/dash-reports.css?v=22">
  <link rel="stylesheet" href="/maindir/css/dash-profile.css?v=2">
  <link rel="stylesheet" href="/maindir/css/dash-flash.css?v=1">
  <link rel="stylesheet" href="/maindir/css/dash-tip.css?v=2">
  <link rel="stylesheet" href="/maindir/css/dash-topbar.css?v=2">
  {{-- <link rel="stylesheet" href="/dashdir/css/bootstrap.min.css"> --}}
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="purple" data-background-color="white" data-sidebar-theme="ocean">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
      -->
      <div class="logo dash-sidebar-brand">
        <a href="/dashboard" class="simple-text logo-normal">
          <span class="dash-sidebar-brand-title">Royal Joyam</span>
          <span class="dash-sidebar-brand-sub">Ventures</span>
        </a>
      </div>

      @include('partials.dash-sidebar')

    </div>
    <div class="main-panel">

      <!-- Navbar -->
      @php
        $topbarUser = auth()->user();
        $topbarInitials = strtoupper(substr($topbarUser->name, 0, 1));
        $topbarIsAdmin = $topbarUser->status === 'Administrator';
        $topbarOnDashboard = request()->is('dashboard');
      @endphp

      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top hideMe dash-topbar">
        <div class="container-fluid">
          @hasSection('search')
            <div class="dash-topbar-start">
              @yield('search')
            </div>
          @endif

          <div class="dash-topbar-actions">
            <ul class="navbar-nav dash-topbar-nav">
              <li class="nav-item">
                <a
                  @class(['nav-link', 'dash-tip', 'dash-topbar-link-active' => $topbarOnDashboard])
                  href="/dashboard"
                  data-tip="Dashboard"
                  @if ($topbarOnDashboard) aria-current="page" @endif
                >
                  <i class="material-icons">dashboard</i>
                </a>
              </li>

              <li class="nav-item dropdown">
                <a
                  class="nav-link dash-tip"
                  href="#"
                  id="navbarDropdownMenuLink"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                  data-tip="Notifications"
                >
                  <i class="material-icons">notifications</i>
                  <span class="notification">2</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dash-topbar-menu" aria-labelledby="navbarDropdownMenuLink">
                  <div class="dash-topbar-menu-header">
                    <div>
                      <span class="dash-topbar-menu-name">Notifications</span>
                      <span class="dash-topbar-menu-meta">System reminders</span>
                    </div>
                  </div>
                  <div class="dash-topbar-menu-divider"></div>
                  <div class="dash-topbar-notice">
                    <span class="dash-topbar-notice-icon" aria-hidden="true"><i class="fa fa-life-ring"></i></span>
                    <span class="dash-topbar-notice-text">
                      <span class="dash-topbar-notice-title">Need an upgrade?</span>
                      Contact PivoApps for feature updates and support.
                    </span>
                  </div>
                  <div class="dash-topbar-notice">
                    <span class="dash-topbar-notice-icon" aria-hidden="true"><i class="fa fa-shield"></i></span>
                    <span class="dash-topbar-notice-text">
                      <span class="dash-topbar-notice-title">Account security</span>
                      Do not share passwords. Register a new user instead.
                    </span>
                  </div>
                </div>
              </li>

              <li class="nav-item dropdown">
                <a
                  class="nav-link dash-topbar-user-trigger dash-tip"
                  href="#"
                  id="navbarDropdownProfile"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                  data-tip="Account menu"
                >
                  <span class="dash-topbar-user-avatar" aria-hidden="true">{{ $topbarInitials }}</span>
                  <i class="fa fa-chevron-down dash-topbar-user-chevron" aria-hidden="true"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right dash-topbar-menu" aria-labelledby="navbarDropdownProfile">
                  <div class="dash-topbar-menu-header">
                    <span class="dash-topbar-menu-avatar" aria-hidden="true">{{ $topbarInitials }}</span>
                    <div class="dash-topbar-menu-user">
                      <span class="dash-topbar-menu-name">{{ $topbarUser->name }}</span>
                      <span class="dash-topbar-menu-meta">{{ $topbarUser->email }}</span>
                      <span class="dash-topbar-menu-role">{{ $topbarUser->status }}</span>
                    </div>
                  </div>
                  <div class="dash-topbar-menu-divider"></div>
                  <a class="dropdown-item" href="/user_profile">
                    <i class="fa fa-user-circle"></i>
                    Profile
                  </a>
                  @if ($topbarIsAdmin)
                    <a class="dropdown-item" href="/config">
                      <i class="fa fa-cogs"></i>
                      Configuration
                    </a>
                  @endif
                  <div class="dash-topbar-menu-divider"></div>
                  <a
                    class="dropdown-item dropdown-item-danger"
                    href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                  >
                    <i class="fa fa-sign-out"></i>
                    Logout
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                  </form>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->

      @yield('content')

      <footer class="footer">
        @yield('footer')
        {{-- <p>This is the footer This is the footer This is the footer This is the footer This is the footer This is the footer</p> --}}
      </footer>
    </div>
  </div>

  <div class="fixed-plugin">
    <div class="dropdown show-dropdown">
      <a href="#" data-toggle="dropdown">
        <i class="fa fa-cog fa-2x"> </i>
      </a>
      <ul class="dropdown-menu">
        <li class="header-title p-8">Sidebar Theme</li>
        <li class="dash-sidebar-theme-picker">
          <div class="dash-sidebar-theme-group">
            <p class="dash-sidebar-theme-group-label">Dark</p>
            <div class="dash-sidebar-theme-swatches">
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="slate" data-tip="Slate" aria-label="Slate theme"></button>
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="deep-teal" data-tip="Deep Teal" aria-label="Deep Teal theme"></button>
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="midnight" data-tip="Midnight Navy" aria-label="Midnight Navy theme"></button>
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="graphite" data-tip="Graphite" aria-label="Graphite theme"></button>
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="ocean" data-tip="Ocean Cyan" aria-label="Ocean Cyan theme"></button>
            </div>
          </div>
          <div class="dash-sidebar-theme-group">
            <p class="dash-sidebar-theme-group-label">Light</p>
            <div class="dash-sidebar-theme-swatches">
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="blush" data-tip="Blush" aria-label="Blush theme"></button>
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="rose" data-tip="Rose" aria-label="Rose theme"></button>
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="coral" data-tip="Coral" aria-label="Coral theme"></button>
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="sky" data-tip="Sky" aria-label="Sky theme"></button>
              <button type="button" class="dash-sidebar-theme-swatch dash-tip switch-trigger" data-sidebar-theme-set="mint" data-tip="Mint" aria-label="Mint theme"></button>
            </div>
          </div>
        </li>
      </ul>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="/dashdir/js/core/jquery.min.js"></script>
  <script src="/dashdir/js/core/popper.min.js"></script>
  <script src="/dashdir/js/core/bootstrap-material-design.min.js"></script>
  <script src="/dashdir/js/plugins/perfect-scrollbar.jquery.min.js"></script>
  <!-- Plugin for the momentJs  -->
  <script src="/dashdir/js/plugins/moment.min.js"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="/dashdir/js/plugins/sweetalert2.js"></script>
  <!-- Forms Validations Plugin -->
  <script src="/dashdir/js/plugins/jquery.validate.min.js"></script>
  <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
  <script src="/dashdir/js/plugins/jquery.bootstrap-wizard.js"></script>
  <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="/dashdir/js/plugins/bootstrap-selectpicker.js"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="/dashdir/js/plugins/bootstrap-datetimepicker.min.js"></script>
  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
  <script src="/dashdir/js/plugins/jquery.dataTables.min.js"></script>
  <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="/dashdir/js/plugins/bootstrap-tagsinput.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="/dashdir/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
  <script src="/dashdir/js/plugins/fullcalendar.min.js"></script>
  <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
  <script src="/dashdir/js/plugins/jquery-jvectormap.js"></script>
  <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
  <script src="/dashdir/js/plugins/nouislider.min.js"></script>
  <!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
  {{-- core-js 2.4.1 full bundle throws RangeError on modern browsers; SweetAlert2 works without it --}}
  <!-- Library for adding dinamically elements -->
  <script src="/dashdir/js/plugins/arrive.min.js"></script>
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
  <!-- Chartist JS -->
  <script src="/dashdir/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="/dashdir/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="/dashdir/js/material-dashboard.js?v=2.1.1" type="text/javascript"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script src="/dashdir/demo/demo.js"></script>
  {{-- <script>
    $(document).ready(function() {
      $().ready(function() {
        $sidebar = $('.sidebar');

        $sidebar_img_container = $sidebar.find('.sidebar-background');

        $full_page = $('.full-page');

        $sidebar_responsive = $('body > .navbar-collapse');

        window_width = $(window).width();

        fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

        if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
          if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
            $('.fixed-plugin .dropdown').addClass('open');
          }

        }

        $('.fixed-plugin a').click(function(event) {
          // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
          if ($(this).hasClass('switch-trigger')) {
            if (event.stopPropagation) {
              event.stopPropagation();
            } else if (window.event) {
              window.event.cancelBubble = true;
            }
          }
        });

        $('.fixed-plugin .active-color span').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-color', new_color);
          }

          if ($full_page.length != 0) {
            $full_page.attr('filter-color', new_color);
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr('data-color', new_color);
          }
        });

        $('.fixed-plugin .background-color .badge').click(function() {
          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('background-color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-background-color', new_color);
          }
        });

        $('.fixed-plugin .img-holder').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).parent('li').siblings().removeClass('active');
          $(this).parent('li').addClass('active');


          var new_image = $(this).find("img").attr('src');

          if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            $sidebar_img_container.fadeOut('fast', function() {
              $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
              $sidebar_img_container.fadeIn('fast');
            });
          }

          if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $full_page_background.fadeOut('fast', function() {
              $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
              $full_page_background.fadeIn('fast');
            });
          }

          if ($('.switch-sidebar-image input:checked').length == 0) {
            var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
            $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
          }
        });

        $('.switch-sidebar-image input').change(function() {
          $full_page_background = $('.full-page-background');

          $input = $(this);

          if ($input.is(':checked')) {
            if ($sidebar_img_container.length != 0) {
              $sidebar_img_container.fadeIn('fast');
              $sidebar.attr('data-image', '#');
            }

            if ($full_page_background.length != 0) {
              $full_page_background.fadeIn('fast');
              $full_page.attr('data-image', '#');
            }

            background_image = true;
          } else {
            if ($sidebar_img_container.length != 0) {
              $sidebar.removeAttr('data-image');
              $sidebar_img_container.fadeOut('fast');
            }

            if ($full_page_background.length != 0) {
              $full_page.removeAttr('data-image', '#');
              $full_page_background.fadeOut('fast');
            }

            background_image = false;
          }
        });

        $('.switch-sidebar-mini input').change(function() {
          $body = $('body');

          $input = $(this);

          if (md.misc.sidebar_mini_active == true) {
            $('body').removeClass('sidebar-mini');
            md.misc.sidebar_mini_active = false;

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

          } else {

            $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar('destroy');

            setTimeout(function() {
              $('body').addClass('sidebar-mini');

              md.misc.sidebar_mini_active = true;
            }, 300);
          }

          // we simulate the window Resize so the charts will get updated in realtime.
          var simulateWindowResize = setInterval(function() {
            window.dispatchEvent(new Event('resize'));
          }, 180);

          // we stop the simulation of Window Resize after the animations are completed
          setTimeout(function() {
            clearInterval(simulateWindowResize);
          }, 1000);

        });
      });
    });
  </script> --}}

  <script>
    (function () {
      var sidebar = document.querySelector('.sidebar');
      if (!sidebar) {
        return;
      }

      var storageKey = 'rjv-sidebar-theme';
      var legacyThemes = {
        frost: 'blush',
        mist: 'rose',
        pearl: 'coral',
      };
      var swatches = document.querySelectorAll('[data-sidebar-theme-set]');

      function normalizeTheme(theme) {
        return legacyThemes[theme] || theme || 'ocean';
      }

      function applyTheme(theme) {
        var nextTheme = normalizeTheme(theme);
        document.documentElement.setAttribute('data-sidebar-theme', nextTheme);
        sidebar.setAttribute('data-sidebar-theme', nextTheme);

        swatches.forEach(function (swatch) {
          var isActive = swatch.getAttribute('data-sidebar-theme-set') === nextTheme;
          swatch.classList.toggle('is-active', isActive);
          swatch.setAttribute('aria-pressed', isActive ? 'true' : 'false');
        });
      }

      applyTheme(normalizeTheme(localStorage.getItem(storageKey) || sidebar.getAttribute('data-sidebar-theme')));

      swatches.forEach(function (swatch) {
        swatch.addEventListener('click', function (event) {
          event.stopPropagation();
          var theme = swatch.getAttribute('data-sidebar-theme-set');
          localStorage.setItem(storageKey, theme);
          applyTheme(theme);
        });
      });

      document.querySelectorAll('.fixed-plugin .switch-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function (event) {
          if (event.stopPropagation) {
            event.stopPropagation();
          }
        });
      });
    })();
  </script>
  <script src="/maindir/js/dash-tip.js?v=1"></script>
</body>

</html>
