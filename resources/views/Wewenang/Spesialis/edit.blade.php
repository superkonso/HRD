@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Spesialis Edit')

@section('content_header', 'Edit Spesialis')

@section('header_description', '')

@section('menu_desc', 'Spesialis')

@section('link_menu_desc', '/spesialis')

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
	                <h3 class="box-title">Form Input Spesialis Edit</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/spesialis/{{$spesialis->id}}" method="post" novalidate>
		                {{ csrf_field() }}
		            {{method_field('PUT')}}
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Spesialis </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" name="kode" placeholder="Kode Spesialis" required="required" value="{{$spesialis->TSpesialis_Kode}}"  maxlength="6" >
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Spesialis <span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Spesialis" value="{{$spesialis->TSpesialis_Nama}}" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jenis">Spesialis Alias 
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="jenis" class="form-control col-md-7 col-xs-12" name="jenis" value="{{$spesialis->TSpesialis_Jenis}}" placeholder="Jenis Spesialis"  maxlength="1" >
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="IDRS">IDRS
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="IDRS" class="form-control col-md-7 col-xs-12" name="IDRS" value="{{$spesialis->IDRS}}" placeholder="Kode RS">
		                    </div>
		                </div>

		                <div class="ln_solid"></div>

	                    <div class="form-group">
	                        <div class="col-md-6 col-md-offset-3">
	                           <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
	                          <a href="/spesialis" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
	                        </div>
	                    </div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

@endsection