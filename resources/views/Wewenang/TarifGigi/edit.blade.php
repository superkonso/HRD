@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Edit Tarif Gigi')

@section('content_header', 'Edit Tarif Gigi')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifgigi')

@section('sub_menu_desc', 'Edit')

@section('content')

@include('Partials.message')

<div class="row">
	    <div class="col-md-12 col-sm-12 col-xs-12">
	        <div class="box box-primary">
	            <div class="box-header">
	                @if(Session::has('flash_message'))
	                    <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
	                @endif
	                <h3 class="box-title">Form Edit Tarif Gigi</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/tarifgigi/{{$tarif->id}}" method="post" novalidate>
		                {{csrf_field()}}
    				    {{method_field('PUT')}}
		           
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Tarif </label>
		                    <div class="col-md-3 col-sm-3 col-xs-6">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" readonly="readonly" name"kode" placeholder="Tarif Kode" required="required" value="{{$tarif->TTarifGigi_Kode}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Status</label>
		                    <div class="col-md-3 col-sm-3 col-xs-12">
		                     <select id="status" name="status" class="form-control col-md-7 col-xs-12">
		                     	<option value="A" @if(!empty($tarif->TTarifGigi_Status)) @if ("A"==$tarif->TTarifGigi_Status) selected="selected" @endif @endif>Aktif</option>
		                     	<option value="N" @if(!empty($tarif->TTarifGigi_Status)) @if ("N"==$tarif->TTarifGigi_Status) selected="selected" @endif @endif>Non Aktif</option>
		                     </select>
		                    </div>
		                </div>

		                {{-- Edit Combo hanya isi 1 yang dipilih --}}
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelompok">Kelompok</label>
		                    <div class="col-md-3 col-sm-3 col-xs-12">
		                     <select id="kelompok" name="kelompok" class="form-control col-md-7 col-xs-12" >
		                     	@foreach($kelompoks as $kel)
		                     		@if(!empty($tarif->TTarifVar_Kode)) 
	                          				@if ($kel->TTarifVar_Kode==$tarif->TTarifVar_Kode)  
	                          					<option value="{{$kel->TTarifVar_Kode}}" selected="selected"> 
	                          					{{$kel->TTarifVar_Nama}}</option>
	                          				@endif 
	                          			@endif
	                          	@endforeach      
		                     </select>
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Tarif Nama<span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Tarif Nama" value="{{$tarif->TTarifGigi_Nama}}" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsft">Tarif RS FT
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsft" class="form-control col-md-7 col-xs-12" name="rsft" placeholder="0" value="{{$tarif->TTarifGigi_RSFT}}" onkeyup="changeFormat(this.id, this.value);">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rspt">Tarif RS PT
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rspt" class="form-control col-md-7 col-xs-12" name="rspt" placeholder="0" value="{{$tarif->TTarifGigi_RSPT}}" onkeyup="changeFormat(this.id, this.value);">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drft">Tarif Dokter FT
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drft" class="form-control col-md-7 col-xs-12" name="drft" placeholder="0" value="{{$tarif->TTarifGigi_JasaDokterFT}}" onkeyup="changeFormat(this.id, this.value);">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drpt">Tarif Dokter PT
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drpt" class="form-control col-md-7 col-xs-12" name="drpt" placeholder="0" value="{{$tarif->TTarifGigi_JasaDokterPT}}" onkeyup="changeFormat(this.id, this.value);">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="trs">Tarif RS
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="trs" class="form-control col-md-7 col-xs-12" name="trs" placeholder="0" required="required" value="{{$tarif->TTarifGigi_Jumlah}}" onkeyup="changeFormat(this.id, this.value);">
		                    </div>
		                </div>


	                    <div class="form-group">
		                     <label class="control-label col-md-3 col-sm-3 col-xs-12" for="perkiraan">Perkiraan Kode 
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <select id="perkiraan" name="perkiraan" class="form-control col-md-7 col-xs-12">
		                     	<option value="0"> </option>
		                     	@foreach($perkkodes as $pk)
	                          		<option value="{{$pk->TPerkiraan_Kode}}" @if(!empty($tarif->TPerkiraan_Kode)) @if ($pk->TPerkiraan_Kode==$tarif->TPerkiraan_Kode) selected="selected" @endif @endif>{{$pk->TPerkiraan_Nama}}</option>
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
						          <a href="/tarifgigi" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
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
	$( document ).ready(function() {

      $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      });  
      
      changeFormat('trs',$('#trs').val());
      changeFormat('rspt',$('#rspt').val());
      changeFormat('rsft',$('#rsft').val());
      changeFormat('drpt',$('#drpt').val());
      changeFormat('drft',$('#drft').val());
	 
    });

	$('#kelompok').on('change', function(e){
	  gantiKode($('#kelompok').val());
	});

	function gantiKode(kdKel){
	  	kdKel= 'TG' +kdKel;
	 	$.get('/ajax-getautonumbertarifgigi?kelompok='+kdKel, function(data){
	    	$('#kode').val(data);
	  	});
	}
	// =================================================================================
</script>

@endsection