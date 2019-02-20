@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif Rad Baru')

@section('content_header', 'Tambah Tarif Rad')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifrad')

@section('sub_menu_desc', 'Create')

@section('content')

@include('Partials.message')

<div class="row">
	    <div class="col-md-12 col-sm-12 col-xs-12">
	        <div class="box box-primary">
	            <div class="box-header">
	                @if(Session::has('flash_message'))
	                    <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
	                @endif
	                <h3 class="box-title">Form Tarif</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/tarifrad" method="post" novalidate data-parsley-validate onsubmit="return checkDataRad()">
		                {{csrf_field()}}
    				       
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Tarif </label>
		                    <div class="col-md-3 col-sm-3 col-xs-6">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" readonly="readonly" name="kode" placeholder="Tarif Kode" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelompok">Kelompok</label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <select id="kelompok" name="kelompok" class="form-control col-md-7 col-xs-12">
		                     	<option value="0"></option>
		                     	@foreach($kelompoks as $kel)
	                          		<option value="{{$kel->TTarifVar_Kode}}">{{$kel->TTarifVar_Nama}}</option>
	                          	@endforeach      
		                     </select>
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Tarif Nama<span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Tarif Nama" value="@yield('nama')" required="required">
		                    </div>
		                </div>

						<div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftvip">Tarif RS FT VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsftvip" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsftvip" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptvip">Tarif RS PT VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsptvip" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsptvip" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftvip">Tarif Dokter FT VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drftvip" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drftvip" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptvip">Tarif Dokter PT VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drptvip" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drptvip" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="vip">Tarif VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="vip" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="vip" placeholder="0" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftutama">Tarif RS FT Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsftutama" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsftutama" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptutama">Tarif RS PT Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsptutama" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsptutama" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftutama">Tarif Dokter FT Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drftutama" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drftutama" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptutama">Tarif Dokter PT Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drptutama" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drptutama" placeholder="0" value="">	
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="utama">Tarif Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="utama" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="utama" placeholder="0" required="required">
		                    </div>
		                </div>
		               	
		               	<div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftkelas1">Tarif RS FT Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsftkelas1" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsftkelas1" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptkelas1">Tarif RS PT Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsptkelas1" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsptkelas1" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftkelas1">Tarif Dokter FT Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drftkelas1" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drftkelas1" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptkelas1">Tarif Dokter PT Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drptkelas1" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drptkelas1" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelas1">Tarif Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kelas1" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="kelas1" placeholder="0" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftkelas2">Tarif RS FT Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsftkelas2" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsftkelas2" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptkelas2">Tarif RS PT Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsptkelas2" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsptkelas2" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftkelas2">Tarif Dokter FT Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drftkelas2" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drftkelas2" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptkelas2">Tarif Dokter PT Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drptkelas2" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drptkelas2" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelas2">Tarif Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kelas2" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="kelas2" placeholder="0" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftkelas3">Tarif RS FT Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsftkelas3" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsftkelas3" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptkelas3">Tarif RS PT Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsptkelas3" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="rsptkelas3" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftkelas3">Tarif Dokter FT Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drftkelas3" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drftkelas3" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptkelas3">Tarif Dokter PT Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drptkelas3" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="drptkelas3" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelas3">Tarif Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kelas3" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimal" name="kelas3" placeholder="0" required="required">
		                    </div>
		                </div>

		                 <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tjalan">Tarif Jalan
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="tjalan" class="form-control col-md-7 col-xs-12" name="tjalan" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="film1">Tarif Film 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="film1" class="form-control col-md-7 col-xs-12" name="film1" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="film2">Tarif Film 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="film2" class="form-control col-md-7 col-xs-12" name="film2" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="film3">Tarif Film 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="film3" class="form-control col-md-7 col-xs-12" name="film3" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="film4">Tarif Film 4
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="film4" class="form-control col-md-7 col-xs-12" name="film4" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="film5">Tarif Film 5
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="film5" class="form-control col-md-7 col-xs-12" name="film5" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="film6">Tarif Film 6
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="film6" class="form-control col-md-7 col-xs-12" name="film6" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="film7">Tarif Film 7
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="film7" class="form-control col-md-7 col-xs-12" name="film7" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tmbfilm">Tarif Tambah Film
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="tmbfilm" class="form-control col-md-7 col-xs-12" name="tmbfilm" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jasadokter">Tarif Jasa Dokter
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="jasadokter" class="form-control col-md-7 col-xs-12" name="jasadokter" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tindakandokter">Tarif Tindakan Dokter
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="tindakandokter" class="form-control col-md-7 col-xs-12" name="tindakandokter" placeholder="0" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

	                     <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="perkiraan">Perkiraan Kode</label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <select id="perkiraan" name="perkiraan" class="form-control col-md-7 col-xs-12">
		                     	<option value="0"> </option>
		                     	@foreach($perkkodes as $pk)
	                          		<option value="{{$pk->TPerkiraan_Kode}}">{{$pk->TPerkiraan_Nama}}</option>
	                          	@endforeach      
		                     </select>
		                    </div>
		                </div>

		                <div class="ln_solid"></div>

	                    <div class="row">
						    <div class="col-md-12 col-sm-12 col-xs-12">
						    <div class="form-group">
						      <div class="box-body">
						        <div class="col-md-12 col-md-offset-5">
						           <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
						          <a href="/tarifrad" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
						        </div>
						      </div>
						    </div>
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

<!-- Modal Searching Pasien Lama -->
<script src="{{ asset('js/searchData.js') }}"></script>

<script type="text/javascript">
// ================================ auto combo kelompok tarif ===============================
	$('#kelompok').on('change', function(e){
	  gantiKode($('#kelompok').val());
	});

	function gantiKode(kdKel){
	 	kdKel= 'RO'+kdKel;

		$.get('/ajax-getautonumbertarifrad?kelompok='+kdKel, function(data){
	    	$('#kode').val(data);
	  	});
	}
	// =================================================================================

	function checkDataRad(){
       var nama         = $('#nama').val();
      var rsftvip         = $('#rsftvip').val();
      var rsptvip         = $('#rsptvip').val();
      var drftvip         = $('#drftvip').val();
      var drptvip         = $('#drptvip').val();
      var vip             = $('#vip').val();
      
      var rsftutama       = $('#rsftutama').val();
      var rsptutama       = $('#rsptutama').val();
      var drftutama       = $('#drftutama').val();
      var drptutama       = $('#drptutama').val();
      var utama           = $('#utama').val();

      var rsftkelas1      = $('#rsftkelas1').val();
      var rsptkelas1      = $('#rsptkelas1').val();
      var drftkelas1      = $('#drftkelas1').val();
      var drptkelas1      = $('#drptkelas1').val();
      var kelas1          = $('#kelas1').val();

      var rsftkelas2      = $('#rsftkelas2').val();
      var rsptkelas2      = $('#rsptkelas2').val();
      var drftkelas2      = $('#drftkelas2').val();
      var drptkelas2      = $('#drptkelas2').val();
      var kelas2          = $('#kelas2').val();

      var rsftkelas3      = $('#rsftkelas3').val();
      var rsptkelas3      = $('#rsptkelas3').val();
      var drftkelas3      = $('#drftkelas3').val();
      var drptkelas3      = $('#drptkelas3').val();
      var kelas3          = $('#kelas3').val();

      var jalan          = $('#jalan').val();

      var film1          = $('#film1').val();
      var film2          = $('#film2').val();
      var film3          = $('#film3').val();
      var film4          = $('#film4').val();
      var film5          = $('#film5').val();
      var film6          = $('#film6').val();
      var film7          = $('#film7').val();   

      var tmbfilm        = $('#tmbfilm').val();
      var jasadokter     = $('#jasadokter').val();
      var tindakandokter = $('#tindakandokter').val(); 

      if (nama == ''){
      	showWarning(2000, '', 'Nama Tarif Masih Kosong !', true);
        $('#nama').focus();
        return false;
      }else if (rsftvip == ''){
      	showWarning(2000, '', 'Nilai Tarif VIP Masih Kosong !', true);
        $('#rsftvip').focus();
        return false;
      }else if (rsptvip == ''){
      	showWarning(2000, '', 'Nilai Tarif VIP Masih Kosong !', true);
        $('#rsptvip').focus();
        return false;
      }else if (drftvip == ''){
      	showWarning(2000, '', 'Nilai Tarif VIP Masih Kosong !', true);
        $('#drftvip').focus();
        return false;
      }else if (drptvip == ''){
      	showWarning(2000, '', 'Nilai Tarif VIP Masih Kosong !', true);
        $('#drptvip').focus();
        return false;
      }else if (vip == ''){
      	showWarning(2000, '', 'Nilai Tarif VIP Masih Kosong !', true);
        $('#vip').focus();
        return false;
      }else if (rsftutama == ''){
      	showWarning(2000, '', 'Nilai Tarif Utama Masih Kosong !', true);
        $('#rsftutama').focus();
        return false;
      }else if (rsptutama == ''){
      	showWarning(2000, '', 'Nilai Tarif Utama Masih Kosong !', true);
        $('#rsptutama').focus();
        return false;
      }else if (drftutama == ''){
      	showWarning(2000, '', 'Nilai Tarif Utama Masih Kosong !', true);
        $('#drftutama').focus();
        return false;
      }else if (drptutama == ''){
      	showWarning(2000, '', 'Nilai Tarif Utama Masih Kosong !', true);
        $('#drptutama').focus();
        return false;
      }else if (rsptkelas1 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas1 Masih Kosong !', true);
        $('#rsptkelas1').focus();
        return false;
      }else if (drftkelas1 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas1 Masih Kosong !', true);
        $('#drftkelas1').focus();
        return false;
      }else if (drftkelas1 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas1 Masih Kosong !', true);
        $('#drftkelas1').focus();
        return false;
      }else if (drptkelas1 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas1 Masih Kosong !', true);
        $('#drptkelas1').focus();
        return false;
      }else if (kelas1 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas1 Masih Kosong !', true);
        $('#kelas1').focus();
        return false;
      }else if (rsptkelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#rsptkelas2').focus();
        return false;
      }else if (drftkelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#drftkelas2').focus();
        return false;
      }else if (drftkelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#drftkelas2').focus();
        return false;
      }else if (drptkelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#drptkelas2').focus();
        return false;
      }else if (kelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#kelas2').focus();
        return false;
      }else if (rsptkelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#rsptkelas2').focus();
        return false;
      }else if (drftkelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#drftkelas2').focus();
        return false;
      }else if (drftkelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#drftkelas2').focus();
        return false;
      }else if (drptkelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#drptkelas2').focus();
        return false;
      }else if (kelas2 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas2 Masih Kosong !', true);
        $('#kelas2').focus();
        return false;
      }else if (rsptkelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#rsptkelas3').focus();
        return false;
      }else if (drftkelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#drftkelas3').focus();
        return false;
      }else if (drftkelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#drftkelas3').focus();
        return false;
      }else if (drptkelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#drptkelas3').focus();
        return false;
      }else if (kelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#kelas3').focus();
        return false;
      }else if (rsptkelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#rsptkelas3').focus();
        return false;
      }else if (drftkelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#drftkelas3').focus();
        return false;
      }else if (drftkelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#drftkelas3').focus();
        return false;
      }else if (drptkelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#drptkelas3').focus();
        return false;
      }else if (kelas3 == ''){
      	showWarning(2000, '', 'Nilai Tarif kelas3 Masih Kosong !', true);
        $('#kelas3').focus();
        return false;
      }else if (jalan == ''){
      	showWarning(2000, '', 'Nilai Tarif Jalan Masih Kosong !', true);
        $('#jalan').focus();
        return false;

       }else if (film1 == ''){
      	showWarning(2000, '', 'Nilai Tarif Film1 Masih Kosong !', true);
        $('#film1').focus();
        return false;
      }else if (film2 == ''){
      	showWarning(2000, '', 'Nilai Tarif Film2 Masih Kosong !', true);
        $('#film2').focus();
        return false;
      }else if (film3 == ''){
      	showWarning(2000, '', 'Nilai Tarif Film3 Masih Kosong !', true);
        $('#film1').focus();
        return false;
      }else if (film4 == ''){
      	showWarning(2000, '', 'Nilai Tarif Film4 Masih Kosong !', true);
        $('#film4').focus();
        return false;
      }else if (film5 == ''){
      	showWarning(2000, '', 'Nilai Tarif Film5 Masih Kosong !', true);
        $('#film5').focus();
        return false;
      }else if (film6 == ''){
      	showWarning(2000, '', 'Nilai Tarif Film6 Masih Kosong !', true);
        $('#film6').focus();
        return false;
      }else if (film7 == ''){
      	showWarning(2000, '', 'Nilai Tarif Film7 Masih Kosong !', true);
        $('#film7').focus();
        return false;
      }else if (tmbfilm == ''){
      	showWarning(2000, '', 'Nilai Tarif Film Tambahan Masih Kosong !', true);
        $('#tmbfilm').focus();
        return false;
      }else if (jasadokter == ''){
      	showWarning(2000, '', 'Nilai Tarif Jasa Dokter Masih Kosong !', true);
        $('#jasadokter').focus();
        return false;
      }else if (tindakandokter == ''){
      	showWarning(2000, '', 'Nilai Tarif Tindakan Dokter Masih Kosong !', true);
        $('#tindakandokter').focus();
        return false;
      }else{
        return true;
      }
  }
</script>

@endsection