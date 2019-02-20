@extends('layouts.main')

@section('title', 'SIMRS | Create User')

@section('content_header', 'Create User')

@section('header_description', 'register user baru')

@section('menu_desc', 'user')

@section('link_menu_desc', '/user')

@section('sub_menu_desc', 'createuser')

@section('content')

@include('Partials.message')

@section('content')

<div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box box-primary">
            <div class="box-header">
                @if(Session::has('flash_message'))
                    <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
                @endif
                <h3 class="box-title">Data User</h3>
            </div>
            <div class="box-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <label for="first_name" class="control-label col-md-3 col-sm-3 col-xs-12">First Name</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required autofocus>

                        @if ($errors->has('first_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('first_name') }}</strong>
                            </span>
                        @endif

                    </div>
                </div>

                <div class="form-group">
                    <label for="last_name" class="control-label col-md-3 col-sm-3 col-xs-12">Last Name</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}">

                        @if ($errors->has('last_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('last_name') }}</strong>
                            </span>
                        @endif
                        
                    </div>
                </div>

                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                    <label for="username" class="control-label col-md-3 col-sm-3 col-xs-12">Username </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required autofocus>

                            @if ($errors->has('username'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('username') }}</strong>
                                </span>
                            @endif
                    </div>
                </div>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email" class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="item form-group">
                    <label for="unit" class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Unit </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <select name="unit" id="unit" class="form-control col-md-7 col-xs-12">

                        @foreach($units as $unit)
                            <option value="{{$unit->TUnit_Kode}}">{{$unit->TUnit_Nama}}</option>
                        @endforeach
                       
                      </select>
                    </div>
                </div>

                <div class="item form-group">
                    <label for="unit" class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Kode Pelaku </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <select name="pelaku" id="pelaku">
                        <option value=""></option>
                        @foreach($pelakus as $data)
                            <option value="{{$data->TPelaku_Kode}}">{{$data->TPelaku_NamaLengkap}}</option>
                        @endforeach
                       
                      </select>
                    </div>
                </div>

                <div class="item form-group">
                    <label for="accessid" class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Level User </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <select name="accessid" id="accessid" class="form-control col-md-7 col-xs-12">

                        @foreach($access as $akses)
                            <option value="{{$akses->TAccess_Code}}">{{$akses->TAccess_Name}}</option>
                        @endforeach
                       
                      </select>
                    </div>
                </div>

                <div class="item form-group">
                    <label for="accessid" class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Profil Picture </label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                      <input type="file" class="file" name="foto" id="foto">
                    </div>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password" class="control-label col-md-3 col-sm-3 col-xs-12">Password</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="password" type="password" class="form-control" name="password" required>
                        @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="password-confirm" class="control-label col-md-3 col-sm-3 col-xs-12">Confirm Password</label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password-confirm" class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
                        <a href="/user" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

<!-- Auto Complete Search Asset -->
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<script src="{{ asset('js/jquery-ui.js') }}"></script>

<script src="{{ asset('js/globFunction.js') }}"></script>

<script type="text/javascript">

  $( document ).ready(function() {

    $('#pelaku').selectize();

  });

</script>

@endsection
