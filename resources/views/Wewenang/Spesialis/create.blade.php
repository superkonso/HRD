@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Spesialis Create')

@section('content_header', 'Create Spesialis')

@section('header_description', '')

@section('menu_desc', 'Spesialis')

@section('link_menu_desc', '/spesialis')

@section('sub_menu_desc', 'Create')

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
	                <h3 class="box-title">Form Input Spesialis Baru</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/spesialis" method="post" novalidate data-parsley-validate onsubmit="return checkDataSpes()">
		                {{ csrf_field() }}
		           
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Spesialis </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" name="kode" placeholder="Kode Spesialis" maxlength="6" required="required" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Spesialis <span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Spesialis" value="" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jenis">Jenis Spesialis  
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="jenis" class="form-control col-md-7 col-xs-12" name="jenis" value="" placeholder="Jenis Spesialis"  maxlength="1" >
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="IDRS">IDRS
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="IDRS" class="form-control col-md-7 col-xs-12" name="IDRS" value="1" placeholder="Kode RS">
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

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

<script type="text/javascript">

	function checkDataSpes(){
      var kode     = $('#kode').val();
      var nama     = $('#nama').val();
      var jenis    = $('#jenis').val();
     
      if(kode == ''){
        showWarning(2000, '', 'Kode Dokter Masih Kosong !', true);
        $('#kode').focus();
        return false;
      }else if (nama == ''){
      	showWarning(2000, '', 'Nama Dokter Masih Kosong !', true);
        $('#nama').focus();
        return false;
      }else if (jenis == ''){
      	showWarning(2000, '', 'Jenis Dokter Masih Kosong !', true);
        $('#jenis').focus();
        return false;
      }else{
        return true;
      }

    }
</script>


@endsection