@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Edit Tarif Inap')

@section('content_header', 'Edit Tarif Inap')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifinap')

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
	                <h3 class="box-title">Form Edit Tarif Inap</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/tarifinap/{{$tarif->id}}" method="post" novalidate>
		                {{csrf_field()}}
    				    {{method_field('PUT')}}
		           
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Tarif </label>
		                    <div class="col-md-3 col-sm-3 col-xs-6">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" readonly="readonly" name"kode" placeholder="Tarif Kode" required="required" value="{{$tarif->TTarifInap_Kode}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="status">Status</label>
		                    <div class="col-md-3 col-sm-3 col-xs-12">
		                     <select id="status" name="status" class="form-control col-md-7 col-xs-12">
		                     	<option value="A" @if(!empty($tarif->TTarifInap_Status)) @if ("A"==$tarif->TTarifInap_Status) selected="selected" @endif @endif>Aktif</option>
		                     	<option value="N" @if(!empty($tarif->TTarifInap_Status)) @if ("N"==$tarif->TTarifInap_Status) selected="selected" @endif @endif>Non Aktif</option>
		                     </select>
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelompok">Kelompok</label>
		                    <div class="col-md-3 col-sm-3 col-xs-12">
		                     <select id="kelompok" name="kelompok" class="form-control col-md-7 col-xs-12" readonly="readonly">
		                     	<option value="0"></option>
		                     	@foreach($kelompoks as $kel)
		                     		@if ($kel->TTarifVar_Kode==$tarif->TTarifVar_Kode) 
	                          		<option value="{{$kel->TTarifVar_Kode}}" selected="selected">{{$kel->TTarifVar_Nama}}</option>
	                          		 @endif 
	                          	@endforeach      
		                     </select>
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Tarif Nama<span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Tarif Nama" value="{{$tarif->TTarifInap_Nama}}" required="required">
		                    </div>
		                </div>


		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftvip">Tarif RS FT VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsftvip" class="form-control col-md-7 col-xs-12" name="rsftvip" placeholder="0" value="{{$tarif->TTarifInap_RSFTVIP}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptvip">Tarif RS PT VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsptvip" class="form-control col-md-7 col-xs-12" name="rsptvip" placeholder="0" value="{{$tarif->TTarifInap_RSPTVIP}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftvip">Tarif Dokter FT VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drftvip" class="form-control col-md-7 col-xs-12" name="drftvip" placeholder="0" value="{{$tarif->TTarifInap_DokterFTVIP}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptvip">Tarif Dokter PT VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drptvip" class="form-control col-md-7 col-xs-12" name="drptvip" placeholder="0" value="{{$tarif->TTarifInap_DokterPTVIP}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="vip">Tarif VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="vip" class="form-control col-md-7 col-xs-12" name="vip" placeholder="0" required="required" value="{{$tarif->TTarifInap_VIP}}">
		                    </div>
		                </div>
		                
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftutama">Tarif RS FT Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsftutama" class="form-control col-md-7 col-xs-12" name="rsftutama" placeholder="0" value="{{$tarif->TTarifInap_RSFTUtama}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptutama">Tarif RS PT Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsptutama" class="form-control col-md-7 col-xs-12" name="rsptutama" placeholder="0" value="{{$tarif->TTarifInap_RSPTUtama}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftutama">Tarif Dokter FT Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drftutama" class="form-control col-md-7 col-xs-12" name="drftutama" placeholder="0" value="{{$tarif->TTarifInap_DokterFTUtama}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptutama">Tarif Dokter PT Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drptutama" class="form-control col-md-7 col-xs-12" name="drptutama" placeholder="0" value="{{$tarif->TTarifInap_DokterPTUtama}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="utama">Tarif Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="utama" class="form-control col-md-7 col-xs-12" name="utama" placeholder="0" required="required" value="{{$tarif->TTarifInap_Utama}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftkelas1">Tarif RS FT Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsftkelas1" class="form-control col-md-7 col-xs-12" name="rsftkelas1" placeholder="0" value="{{$tarif->TTarifInap_RSFTKelas1}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptkelas1">Tarif RS PT Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsptkelas1" class="form-control col-md-7 col-xs-12" name="rsptkelas1" placeholder="0" value="{{$tarif->TTarifInap_RSPTKelas1}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftkelas1">Tarif Dokter FT Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drftkelas1" class="form-control col-md-7 col-xs-12" name="drftkelas1" placeholder="0" value="{{$tarif->TTarifInap_DokterFTKelas1}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptkelas1">Tarif Dokter PT Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drptkelas1" class="form-control col-md-7 col-xs-12" name="drptkelas1" placeholder="0" value="{{$tarif->TTarifInap_DokterPTKelas1}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelas1">Tarif Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="kelas1" class="form-control col-md-7 col-xs-12" name="kelas1" placeholder="0" required="required" value="{{$tarif->TTarifInap_Kelas1}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftkelas2">Tarif RS FT Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsftkelas2" class="form-control col-md-7 col-xs-12" name="rsftkelas2" placeholder="0" value="{{$tarif->TTarifInap_RSFTKelas2}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptkelas2">Tarif RS PT Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsptkelas2" class="form-control col-md-7 col-xs-12" name="rsptkelas2" placeholder="0" value="{{$tarif->TTarifInap_RSPTKelas2}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftkelas2">Tarif Dokter FT Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drftkelas2" class="form-control col-md-7 col-xs-12" name="drftkelas2" placeholder="0" value="{{$tarif->TTarifInap_DokterFTKelas2}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptkelas2">Tarif Dokter PT Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drptkelas2" class="form-control col-md-7 col-xs-12" name="drptkelas2" placeholder="0" value="{{$tarif->TTarifInap_DokterPTKelas2}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelas2">Tarif Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="kelas2" class="form-control col-md-7 col-xs-12" name="kelas2" placeholder="0" required="required" value="{{$tarif->TTarifInap_Kelas2}}">
		                    </div>
		                </div>
		                
		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsftkelas3">Tarif RS FT Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsftkelas3" class="form-control col-md-7 col-xs-12" name="rsftkelas3" placeholder="0" value="{{$tarif->TTarifInap_RSFTKelas3}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsptkelas3">Tarif RS PT Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="rsptkelas3" class="form-control col-md-7 col-xs-12" name="rsptkelas3" placeholder="0" value="{{$tarif->TTarifInap_RSPTKelas3}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drftkelas3">Tarif Dokter FT Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drftkelas3" class="form-control col-md-7 col-xs-12" name="drftkelas3" placeholder="0" value="{{$tarif->TTarifInap_DokterFTKelas3}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drptkelas3">Tarif Dokter PT Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="drptkelas3" class="form-control col-md-7 col-xs-12" name="drptkelas3" placeholder="0" value="{{$tarif->TTarifInap_DokterPTKelas3}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelas3">Tarif Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="kelas3" class="form-control col-md-7 col-xs-12" name="kelas3" placeholder="0" required="required" value="{{$tarif->TTarifInap_Kelas3}}">
		                    </div>
		                </div>

		              	<div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jalan">Tarif Jalan
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input onkeyup="changeFormat(this.id, this.value)" type="text" id="jalan" class="form-control col-md-7 col-xs-12" name="jalan" placeholder="0" required="required" value="{{$tarif->TTarifInap_Jalan}}">
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
						          <a href="/tarifinap" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
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
	  switch (true){
	  	case $('#kelompok').val()=='01':
	  		kdKel= 'VD'+kdKel;
	  		break;
  		case $('#kelompok').val()=='02':
	  		kdKel= 'TD'+kdKel;
	  		break;
  		case $('#kelompok').val()=='03':
	  		kdKel= 'TD'+kdKel;
	  		break;
  		case $('#kelompok').val()=='04':
	  		kdKel= 'TP'+kdKel;
	  		break;
  		case $('#kelompok').val()=='05':
	  		kdKel= 'AD'+kdKel;
	  		break;
  		case $('#kelompok').val()=='06':
  			kdKel= 'KR'+kdKel;
	  		break;
	  	default: 
	  		kdKel;
	  		break;
	  }

	  $.get('/ajax-getautonumbertarifinap?kelompok='+kdKel, function(data){
	    $('#kode').val(data);
	  });
	}
	// =================================================================================

	$( document ).ready(function() {

      $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      });  
      
      changeFormat('vip',$('#vip').val());
      changeFormat('rsptvip',$('#rsptvip').val());
      changeFormat('rsftvip',$('#rsftvip').val());
      changeFormat('drptvip',$('#drptvip').val());
      changeFormat('drftvip',$('#drftvip').val());

      changeFormat('utama',$('#utama').val());
      changeFormat('rsptutama',$('#rsptutama').val());
      changeFormat('rsftutama',$('#rsftutama').val());
      changeFormat('drptutama',$('#drptutama').val());
      changeFormat('drftutama',$('#drftutama').val());

      changeFormat('kelas1',$('#kelas1').val());
      changeFormat('rsptkelas1',$('#rsptkelas1').val());
      changeFormat('rsftkelas1',$('#rsftkelas1').val());
      changeFormat('drptkelas1',$('#drptkelas1').val());
      changeFormat('drftkelas1',$('#drftkelas1').val());

      changeFormat('kelas2',$('#kelas2').val());
      changeFormat('rsptkelas2',$('#rsptkelas2').val());
      changeFormat('rsftkelas2',$('#rsftkelas2').val());
      changeFormat('drptkelas2',$('#drptkelas2').val());
      changeFormat('drftkelas2',$('#drftkelas2').val());

      changeFormat('kelas3',$('#kelas3').val());
      changeFormat('rsptkelas3',$('#rsptkelas3').val());
      changeFormat('rsftkelas3',$('#rsftkelas3').val());
      changeFormat('drptkelas3',$('#drptkelas3').val());
      changeFormat('drftkelas3',$('#drftkelas3').val());

      changeFormat('jalan',$('#jalan').val());
	 
    });
</script>

@endsection