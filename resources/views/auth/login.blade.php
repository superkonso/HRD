<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIMRS') }}</title>

    <!-- Bootstrap 3.3.5 -->
    <link href="{{ asset('bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Styles -->
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
    <link href="{{ asset('css/login-style.css') }}" rel="stylesheet">
</head>
<body>

    <?php
        date_default_timezone_set("Asia/Bangkok");
        $jam = date('H');
        $shift = 1;

        if($jam>=6 and $jam <=13)
            $shift = 1;
        elseif($jam>=14 and $jam <=21)
            $shift = 2;
        else
            $shift = 3;
    ?>

    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-12 col-xs-12">
                <div class="col-md-5 col-md-offset-3">
                    <div class="panel panel-default">
                        <div class="panel-heading"> 
                            <strong class="title-login">Selamat Datang Di Sistem Manajemen Administrasi Rumahsakit Terpadu - SMART MEDISOFT</strong>
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" role="form" method="POST" action="{{ route('login') }}">
                            {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                    <label for="username" class="col-sm-3 control-label">Username</label>
                                    <div class="col-sm-9">
                                        {{-- <input type="email" class="form-control" id="inputEmail3" placeholder="Email" required=""> --}}
                                        <input type="text" id="username" class="form-control" name="username" value="{{ old('username') }}" required autofocus>

                                        @if ($errors->has('username'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('username') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <label for="password" class="col-sm-3 control-label">Password</label>
                                    <div class="col-sm-9">
                                        {{-- <input type="password" class="form-control" id="inputPassword3" placeholder="Password" required=""> --}}

                                        <input type="password" id="password" class="form-control" name="password" required>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="shift" class="col-sm-3 control-label">Shift</label>
                                    <div class="col-sm-9">
                                        <select id="shift" name="shift" class="form-control">
                                            <option value="1" @if($shift == 1) selected="selected" @endif>Shift Pagi</option>
                                            <option value="2" @if($shift == 2) selected="selected" @endif>Shift Siang</option>
                                            <option value="3" @if($shift == 3) selected="selected" @endif>Shift Malam</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group last">
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <button type="submit" class="btn btn-primary">
                                        Login
                                        </button>

                                        {{-- <a class="btn btn-link" href="{{ route('password.request') }}">
                                            Forgot Your Password?
                                        </a> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> <!-- <div class="col-md-5 col-md-offset-4"> -->

                <div class="col-md-5 col-md-offset-4">
                    <div class="row">

                    </div>
                </div>

            </div> <!-- <div class="col-md-12 col-lg-12 col-xs-12"> -->
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <!-- jQuery 2.1.4 -->
    <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script>
</body>
</html>