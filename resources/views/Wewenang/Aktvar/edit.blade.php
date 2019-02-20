@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Akuntansi Edit')

@section('content_header', 'Edit Akuntansi')

@section('header_description', '')

@section('menu_desc', 'Akuntansi')

@section('link_menu_desc', '/aktvar')

@section('sub_menu_desc', 'Edit')

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
	                <h3 class="box-title">Form Edit Akuntansi </h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/aktvar/{{$aktvars->id}}" method="post" novalidate>
		                {{ csrf_field() }}
		            {{method_field('PUT')}}
		                {{ Form::hidden('kdKel', '', array('id' => 'kdKel')) }}
		           
		           		<div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="Seri">Seri
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                    <select id="seri" name="seri" class="form-control col-md-7 col-xs-12">
	                          			<option value="{{$aktvars->TAktVar_Seri}}">{{$aktvars->TAktVar_Seri}}</option>
		                     </select>
		                    </div>
		                </div>
		                   
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Akuntansi </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" name="kode" placeholder="Kode Akuntansi" required="required" value="{{$aktvars->TAktVar_VarKode}}" readonly="readonly" maxlength="4">
		                    </div>
		                </div>
		


		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Akuntansi
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Akuntansi: Swasta/Hindu/..." value="{{$aktvars->TAktVar_Nama}}" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="panjang">Panjang
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="panjang" class="form-control col-md-7 col-xs-12" name="panjang" value="{{$aktvars->TAktVar_Panjang}}" placeholder="Panjang Variabel: 1/2/3/4/..." maxlength="3">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nilai">Nilai
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nilai" class="form-control col-md-7 col-xs-12" name="nilai" value="{{$aktvars->TAktVar_Nilai}}" placeholder="Nilai Variabel: 2110000/4100001">
		                    </div>
		                </div>

		                <div class="ln_solid"></div>

	                    <div class="form-group">
	                        <div class="col-md-6 col-md-offset-3">
	                           <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
	                          <a href="/aktvar" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
	                        </div>
	                    </div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

@endsection