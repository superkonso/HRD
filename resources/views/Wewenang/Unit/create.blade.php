@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Unit Create')

@section('content_header', 'Create Unit')

@section('header_description', '')

@section('menu_desc', 'Unit')

@section('link_menu_desc', '/unit')

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
	                <h3 class="box-title">Form Input Unit Baru</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/unit" method="post" novalidate data-parsley-validate onsubmit="return checkDataUnit()">
		                {{ csrf_field() }}
		           
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Unit </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" name="kode" placeholder="Kode Unit" required="required" value="" maxlength="3">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Unit <span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Unit" value="" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Grup Unit</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select name="grup" class="form-control col-md-7 col-xs-12">

	                          	@foreach($grups as $grup)
	                          		<option value="{{$grup->TGrup_Kode}}">{{$grup->TGrup_Nama}}</option>
	                          	@endforeach
	                           
	                          </select>
	                        </div>
	                    </div>

	                    <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="alias">Unit Grup 
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="unitgrup" class="form-control col-md-7 col-xs-12" name="unitgrup" value="" placeholder="Unit Grup" maxlength="6">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="alias">Unit Alias 
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="alias" class="form-control col-md-7 col-xs-12" name="alias" value="" placeholder="Unit Alias" maxlength="6">
		                    </div>
		                </div>

		               	<div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="inisial">Inisial Unit 
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="inisial" class="form-control col-md-7 col-xs-12" name="inisial" value="" placeholder="Inisial Unit" maxlength="2">
		                    </div>
		                </div>



		                <div class="ln_solid"></div>

	                    <div class="form-group">
	                        <div class="col-md-6 col-md-offset-3">
	                          <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
	                          <a href="/unit" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
	                        </div>
	                    </div>

	                </form>
	            </div>
	        </div>
	    </div>
	</div>

  @include('Partials.errors')

<script type="text/javascript">

    function checkFormPasien(){
      var noRM        = $('#pasiennorm').val();
      var namaPasien  = $('#nama').val();

      if(namaPasien == ''){
        showWarning(2000, '', 'Silahkan lengkapi Data Poli/Pasien Terlebih Dahulu !', true);
        return false;
      }else if(arrItem.length < 1){
        showWarning(2000, '', 'Data Transaksi Lain Masih Kosong !', true);
        return false;
      }else{
        return true;
      }
    }
 
 	function checkDataUnit(){
    
      var nama         = $('#nama').val();
      var kode         = $('#kode').val();
      
      if (kode == ''){
      	showWarning(2000, '', 'Kode Tarif Masih Kosong !', true);
        $('#kode').focus();
        return false;
      }else if (nama == ''){
      	showWarning(2000, '', 'Nama Tarif Masih Kosong !', true);
        $('#nama').focus();
        return false;
      }else{
        return true;
      }
  }
</script>

@endsection