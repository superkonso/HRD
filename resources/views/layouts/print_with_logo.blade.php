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

        <div class="col-md-12 col-sm-12 col-xs-12" style="padding-top: 10px;padding-bottom: 10px;">
          <div class="form-group">
              <div class="col-md-2 col-sm-2 col-xs-2">
                <img src="{!! asset('images/panel/'.Auth::User()->cPanel->TCpanel_LogoKecilWarna.'') !!}" width="100" height="50">
              </div>
              <div class="col-md-10 col-sm-10 col-xs-10" style="text-align:left;border-left: solid 1px black;">
                <b>{{Auth::User()->cPanel->TCpanel_NamaRS}}</b><br>
                <small>{{Auth::User()->cPanel->TCpanel_AlamatLengkap}}</small><br>
                <small>{{Auth::User()->cPanel->TCpanel_Info}}</small>
              </div>
            </div>
        </div>

        <section class="content">
              @yield('content')
        </section>

      </div>

    </div>

    @include('layouts/include-js')

  </body>
</html>
