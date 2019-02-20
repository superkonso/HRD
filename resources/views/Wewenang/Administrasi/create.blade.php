@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Administrasi Create')

@section('content_header', 'Create Administrasi')

@section('header_description', '')

@section('menu_desc', 'Administrasi')

@section('link_menu_desc', '/administrasi')

@section('sub_menu_desc', 'Tambah Administrasi')

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
	                <h3 class="box-title">Form Input Administrasi Baru</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/administrasi" method="post" novalidate data-parsley-validate onsubmit="return checkDataAdm()">
		                {{ csrf_field() }}
		                {{ Form::hidden('kdKel', '', array('id' => 'kdKel')) }}
		                {{ Form::hidden('isbaru', '1', array('id' => 'isbaru')) }}
		           
		           		<div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="Seri">Seri
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                    <select id="seri" name="seri" class="form-control col-md-7 col-xs-12">
	                          		<option value="0">Seri Baru</option>
	                         		@foreach($seris as $seri)
	                          			<option value="{{$seri->TAdmVar_Seri}}">{{$seri->TAdmVar_Seri}}</option>
	                          		@endforeach    
		                     </select>
		                    </div>
		                </div>

		                <div id="divseribaru" class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="Seri">Seri Baru
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     	<input type="text" id="seribaru" class="form-control col-md-7 col-xs-12" name="seribaru" value="" placeholder="Masukkan Seri Variabel Baru" maxlength="10">
		                    </div>
		                </div>
		                   
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Administrasi </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" name="kode" placeholder="Kode Administrasi" required="required" value="{{$autoNumber}}" readonly="readonly" maxlength="4">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Administrasi
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Administrasi: Swasta/Hindu/..." value="" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="panjang">Panjang
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="panjang" class="form-control col-md-7 col-xs-12" name="panjang" value="" placeholder="Panjang Variabel: 1/2/3/4/...">
		                    </div>
		                </div>
		                <div class="ln_solid"></div>

	                    <div class="form-group">
	                        <div class="col-md-6 col-md-offset-3">
	                          <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
	                          <a href="/administrasi" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
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
// ================================ auto combo kelompok tarif ===============================
	$( document ).ready(function() {

      $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      });  
      

    });
	$('#seri').on('change', function(e){
	  if ($('#seri option:selected').val()==0){
	  		$("#divseribaru").show();
	  		$('#kode').val('1');
	  		$('#kode').attr("readonly", "readonly");
	  } else {
	  		$("#divseribaru").hide();
	  		gantiKode($('#seri option:selected').text())
	  }
	});

	function gantiKode(seri){
	  $.get('/ajax-getautonumberadmvar?seri='+seri, function(data){
	  	if (isNaN(data)) {
	  		$('#kode').val('');
	  		$('#kode').attr("placeholder", "Isi dengan teks, contoh: " + data);
	  		$('#kode').removeAttr("readonly");
	  		$('#isbaru').val('1');
	  	} else {
	  		$('#kode').val(+data + 1);
	  		$('#kode').attr("readonly", "readonly");
	  		$('#isbaru').val('0');
	  	}
	    
	  });
	}
	// =================================================================================

	function checkDataAdm(){
      var seribaru     = $('#seribaru').val();
      var nama     = $('#nama').val();
      var panjang  = $('#panjang').val();

     if (nama == ''){
      	showWarning(2000, '', 'Nama Administrasi Masih Kosong !', true);
        $('#nama').focus();
        return false;
      }else if (panjang == ''){
      	showWarning(2000, '', 'Panjang Variabel Masih Kosong !', true);
        $('#panjang').focus();
        return false;
      }else{
        return true;
      }
  }
</script>

@endsection
@endsection