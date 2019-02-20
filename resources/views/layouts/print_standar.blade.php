<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf_token" content="{{ csrf_token() }}" />

    <title>@yield('title')</title>
    <link rel="icon" href="{!! asset('images/icon/dokter-icon.png') !!}"/>

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    @include('layouts/include-style')

  </head>
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

      <div class="content-wrapper">

        <section class="content-header">
          <h3>
            
          </h3>
        </section>

        <section class="content">
              @yield('content')
        </section>

      </div>

    </div>

    @include('layouts/include-js')

  </body>
</html>
