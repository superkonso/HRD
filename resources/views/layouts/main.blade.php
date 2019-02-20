<!DOCTYPE html>
<html>
  <head>
{{--     <meta charset="utf-8"> --}}
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf_token" content="{{ csrf_token() }}" />

    <title>@yield('title')</title>
    <link rel="icon" href="{!! asset('images/icon/dokter-icon.png') !!}"/>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    
    <meta charset="utf-8">   
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    @include('layouts/include-style')

  </head>
  <body class="sidebar-collapse hold-transition skin-blue-light  sidebar-mini">
    <div class="wrapper" style="max-height: auto; overflow-y: auto;">

      @include('layouts/top-nav')
      @include('layouts/left-menu')

      <div class="content-wrapper" style="max-height: auto; overflow-y: auto;">

        <section class="content-header" style="position: relative; z-index: 1;">
          <h3>
            @yield('content_header') 
            @yield('sub_content_header')
            <small>@yield('header_description')</small>
          </h3>
          <ol class="breadcrumb">
            <li><a href="{{ url('/') }}"><img src="{!! asset('images/icon/home-icon.png') !!}" width="20" height="20"> Home</a></li>
            <li><a href="@yield('link_menu_desc')">@yield('menu_desc')</a></li>
            <li class="active">@yield('sub_menu_desc')</li>
          </ol>
        </section>

        <!-- ===== Main content ===== -->
        <section class="content font-medium" style="max-height: auto;overflow-y: auto; overflow-x: hidden;">
              @yield('content')
        </section>

      </div>
      
      @include('layouts/footer')

      <!-- disini control sidebar -->
    </div>

    @include('layouts/include-js')
    <!-- sweet alert-->

    @include('sweetalert::alert')
  </body>
</html>
