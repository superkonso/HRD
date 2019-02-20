@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Wilayah Edit')

@section('content_header', 'Edit Wilayah')

@section('header_description', '')

@section('menu_desc', 'Wilayah')

@section('link_menu_desc', '/wilayah')

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
	                <h3 class="box-title">Form Edit Wilayah </h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/wilayah/{{$wilayahs->id}}" method="post" novalidate>
		                {{ csrf_field() }}
		            {{method_field('PUT')}}
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Wilayah </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" name="kode" placeholder="Kode Wilayah" required="required" value="{{$wilayahs->TWilayah2_Kode}}" maxlength="3">
		                    </div>
		                </div>

		              	<div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jenis">Jenis Wilayah</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select name="jenis" class="form-control col-md-7 col-xs-12">
	                          		<option value="1" @if(!empty($wilayahs->TWilayah2_Jenis)) @if ($wilayahs->TWilayah2_Jenis=="1") selected="selected" @endif @endif>Provinsi</option>
	                          		<option value="2" @if(!empty($wilayahs->TWilayah2_Jenis)) @if ($wilayahs->TWilayah2_Jenis=="2") selected="selected" @endif @endif>Kabupaten/Kota</option>
	                          		<option value="3"  @if(!empty($wilayahs->TWilayah2_Jenis)) @if ($wilayahs->TWilayah2_Jenis=="3") selected="selected" @endif @endif>Kecamatan</option>
	                          		<option value="4"  @if(!empty($wilayahs->TWilayah2_Jenis)) @if ($wilayahs->TWilayah2_Jenis=="4") selected="selected" @endif @endif>Kelurahan</option>
	                          </select>
	                        </div>
	                    </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Wilayah <span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Wilayah" value="{{$wilayahs->TWilayah2_Nama}}" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="IDRS">IDRS
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="IDRS" class="form-control col-md-7 col-xs-12" name="IDRS" value="{{$wilayahs->IDRS}}" placeholder="Kode RS">
		                    </div>
		                </div>

		                <div class="ln_solid"></div>

	                    <div class="form-group">
	                        <div class="col-md-6 col-md-offset-3">
	                           <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
	                          <a href="/wilayah" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
	                        </div>
	                    </div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

@endsection