@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif Edit')

@section('content_header', 'Edit Tarif')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifvar')

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
	                <h3 class="box-title">Form Edit Tarif </h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/tarifvar/{{$tarifvars->id}}" method="post" novalidate>
		                {{ csrf_field() }}
		            {{method_field('PUT')}}
		                {{ Form::hidden('kdKel', '', array('id' => 'kdKel')) }}
		           
		           		<div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="Seri">Seri
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                    <select id="seri" name="seri" class="form-control col-md-7 col-xs-12">
	                          			<option value="{{$tarifvars->TTarifVar_Seri}}">{{$tarifvars->TTarifVar_Seri}}</option>
		                     </select>
		                    </div>
		                </div>
		                   
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Tarif </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" name="kode" placeholder="Kode Tarif" required="required" value="{{$tarifvars->TTarifVar_Kode}}" readonly="readonly" maxlength="4">
		                    </div>
		                </div>
		


		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Tarif
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Tarif: Swasta/Hindu/..." value="{{$tarifvars->TTarifVar_Nama}}" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="panjang">Panjang
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="panjang" class="form-control col-md-7 col-xs-12" name="panjang" value="{{$tarifvars->TTarifVar_Panjang}}" placeholder="Panjang Variabel: 1/2/3/4/..." maxlength="3">
		                    </div>
		                </div>

		                 <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nilai">Nilai
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nilai" class="form-control col-md-7 col-xs-12" name="nilai" value="{{$tarifvars->TTarifVar_Nilai}}" placeholder="Nilai">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelompok">Kelompok
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kelompok" class="form-control col-md-7 col-xs-12" name="kelompok" value="{{$tarifvars->TTarifVar_Kelompok}}" placeholder="Kelompok Tarif">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nilailama">Nilai Lama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nilailama" class="form-control col-md-7 col-xs-12" name="nilailama" value="{{$tarifvars->TTarifVar_NilaiLama}}" placeholder="Nilai Lama">
		                    </div>
		                </div>



		                <div class="ln_solid"></div>

	                    <div class="form-group">
	                        <div class="col-md-6 col-md-offset-3">
	                        	<button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
	                          <a href="/tarifvar" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
	                        </div>
	                    </div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

@endsection