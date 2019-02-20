<header class="main-header">
  <!-- Logo -->
  <a href="/home" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini"><img src="{!! asset('images/hospital-icon.png') !!}" width="40px" height="40px"></img></span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg"><img src="{!! asset('images/hospital-icon.png') !!}" width="40px" height="40px"></img> <b>SMART</b> Medisoft</span>
  </a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" role="navigation">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </a>


    {{-- <div class="collapse navbar-collapse pull-left" id="navbar-collapse" style="width: 70%;">
      <marquee><img src="{!! asset('images/icon/ambulance-icon.png') !!}"></img></marquee>
    </div> --}}


    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        
        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <img src="@if(is_null(Auth::user()->foto) or Auth::user()->foto == '') {{ asset('images/user/') }}/userdefaultimg.jpg @else {{ asset('images/user/') }}/{{ Auth::user()->foto}} @endif" class="user-image" alt="User Image">
            <span class="hidden-xs">{{ Auth::user()->first_name.' '.Auth::user()->last_name }}</span>
          </a>
          <ul class="dropdown-menu">
            <!-- User image -->
            <li class="user-header">
              <img src="@if(is_null(Auth::user()->foto) or Auth::user()->foto == '') {{ asset('images/user/') }}/userdefaultimg.jpg @else {{ asset('images/user/') }}/{{ Auth::user()->foto}} @endif" class="img-circle" alt="User Image">
              <p>
                <small>Anda Login Sebagai, </small>
                {{ Auth::user()->first_name.' '.Auth::user()->last_name }}
              </p>
            </li> 
            <!-- Menu Footer-->
            <li class="user-footer">
              <div class="pull-left">
                <a href="{{'/editprofile/'.Auth::user()->id}}" class="btn btn-default btn-flat">Update Profile</a>
              </div>
              <div class="pull-right">
                <a href="{{ route('logout') }}" class="btn btn-default btn-flat" onclick="event.preventDefault();
                             document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                  {{ csrf_field() }}
                </form>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>