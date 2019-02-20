@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif Rawat UGD Baru')

@section('content_header', 'Input Tarif UGD Baru')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifugd')

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
	                <h3 class="box-title">Form Tarif UGD</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/tarifugd" method="post" novalidate data-parsley-validate onsubmit="return checkDataUgd()">
		                
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
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="keterangan">Tarif Keterangan
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="keterangan" class="form-control col-md-7 col-xs-12" name="keterangan" placeholder="Keterangan lengkap tarif" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rsft">Tarif RS FT
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rsft" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimalrsft" name="rsft" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="rspt">Tarif RS PT
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="rspt" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimalrspt" name="rspt" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drft">Tarif Dokter FT
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drft" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimaldrft" name="drft" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="drpt">Tarif Dokter PT
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="drpt" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimaldrpt" name="drpt" placeholder="0" value="">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="trs">Tarif RS
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="trs" onkeyup="changeFormat(this.id, this.value)" class="form-control col-md-7 col-xs-12 decimaltrs" name="trs" placeholder="0" required="required">
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
						           <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>						          <a href="/tarifugd" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
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
	  		kdKel= 'KU'+kdKel;
	  		break;
  		case $('#kelompok').val()=='02':
	  		kdKel= 'TD'+kdKel;
	  		break;
  		case $('#kelompok').val()=='03':
	  		kdKel= 'GD'+kdKel;
	  		break;
  		case $('#kelompok').val()=='04':
	  		kdKel= 'TP'+kdKel;
	  		break;
  		case $('#kelompok').val()=='05':
	  		kdKel= 'TA'+kdKel;
	  		break;
  		case $('#kelompok').val()=='06':
  			kdKel= 'AD'+kdKel;
	  		break;
	  	case $('#kelompok').val()=='07':
  			kdKel= 'TL'+kdKel;
  			break;
  		case $('#kelompok').val()=='08':
	  		kdKel= 'KR'+kdKel;
	  		break;
	  	default: 
	  		kdKel;
	  		break;
	  }

	  $.get('/ajax-getautonumbertarifugd?kelompok='+kdKel, function(data){
	    $('#kode').val(data);
	  });
	}
	// =================================================================================


	function checkDataUgd(){
      var nama         = $('#nama').val();
      var keterangan   = $('#keterangan').val();
      var rsft         = $('#rsft').val();
      var rspt         = $('#rspt').val();
      var drft         = $('#drft').val();
      var drpt         = $('#drpt').val();
      var trs          = $('#trs').val();
    
      if (nama == ''){
      	showWarning(2000, '', 'Nama Tarif Masih Kosong !', true);
        $('#nama').focus();
        return false;
      }else if (keterangan == ''){
      	showWarning(2000, '', 'Keterangan Tarif RS Masih Kosong !', true);
        $('#keterangan').focus();
        return false;
      }else if (rsft == ''){
      	showWarning(2000, '', 'Nilai Tarif RS Masih Kosong !', true);
        $('#rsft').focus();
        return false;
      }else if (rspt == ''){
      	showWarning(2000, '', 'Nilai Tarif RS Masih Kosong !', true);
        $('#rspt').focus();
        return false;
      }else if (drft == ''){
      	showWarning(2000, '', 'Nilai Tarif Dokter Masih Kosong !', true);
        $('#drft').focus();
        return false;
      }else if (drpt == ''){
      	showWarning(2000, '', 'Nilai Tarif Dokter Masih Kosong !', true);
        $('#rspt').focus();
        return false;
      }else if (trs == ''){
      	showWarning(2000, '', 'Nilai Tarif RS Masih Kosong !', true);
        $('#trs').focus();
        return false;
      }else{
        return true;
      }
  }
</script>



@endsection