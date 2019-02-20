@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Info Telpon')

@section('content_header', 'Create Info Telpon')

@section('header_description', '')

@section('menu_desc', 'infotelpon')

@section('link_menu_desc', '/infotelpon')

@section('sub_menu_desc', 'Create')

@section('content')

@include('Partials.message')

<?php 
  date_default_timezone_set("Asia/Bangkok"); 
?> 

 <form class="form-horizontal form-label-left" action="/infotelpon" method="post" id="forminfotelp" data-parsley-validate onsubmit="return checkFormLain()">
  <div class="row font-medium">
      <!-- Token -->
      {{csrf_field()}}

    <div class="col-md-12 col-sm-12 col-xs-12">
      <div class="box box-primary">
        {{-- <div class="box-header">
          <h3 class="box-title">Info Telpon</h3>
          	@if(Session::has('flash_message'))
			    	<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
        </div> --}}
        <div class="box-body">
		 
           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
              <div class="col-md-4 col-sm-4 col-xs-4" style="padding-left:0px;">
                <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
           </div>
           
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
              <div class="col-md-4 col-sm-4 col-xs-4" style="padding-left:0px;">
                <input type="text" name="alamat" id="alamat" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
           </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kota</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
              <div class="col-md-4 col-sm-4 col-xs-4" style="padding-left:0px;">
                <input type="text" name="kota" id="kota" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
           </div>

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Telpon</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
              <div class="col-md-4 col-sm-4 col-xs-4" style="padding-left:0px;">
                <input type="text" name="telp" id="telp" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
           </div>

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Keterangan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
               <div class="col-md-4 col-sm-4 col-xs-4" style="padding-left:0px;">
                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" style="resize:none;">@yield('keterangan')</textarea>
              </div>
            </div>
           </div>           
       
      <input type="hidden" name="arrItem" id="arrItem" value="">

      <div class="form-group col-md-12 col-sm-12 col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="col-md-12 col-md-offset-3">
              <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
              <a href="/infotelpon" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</a>
            </div>
          </div>
        </div>
      </div>
    <!-- ================== -->
  </form>

  @include('Partials.modals.searchmodal')

  @include('Partials.alertmodal')

  <!-- JQuery 1 -->
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

  <!-- Auto Complete Search Asset -->
  <script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

  <!-- Modal Searching Pasien Lama -->
  <script src="{{ asset('js/searchData.js') }}"></script>

  <!-- ============================================= End Of Content ================================================ -->

  @include('Partials.errors')

  <script type="text/javascript">   
  
  	function checkFormLain(){
      var noRM        = $('#nama').val();
      var namaPasien  = $('#telp').val();

      if(noRM == '' || namaPasien == ''){
        showWarning(2000, '', 'Silahkan lengkapi Data Terlebih Dahulu!', true);
        return false;
      }else if(arrItem.length < 1){
        showWarning(2000, '', 'Data Transaksi Masih Kosong!', true);
        return false;
      }else{
        return true;
      }
    }
  </script>

@endsection