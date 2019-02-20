@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif Fisio Baru')

@section('content_header', 'Tambah Tarif Fisio')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tariffisio')

@section('sub_menu_desc', 'Create Tarif')

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
	                <form class="form-horizontal form-label-left" action="/tariffisio" method="post" novalidate data-parsley-validate onsubmit="return checkDataFisio()">

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
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tvip">Tarif VIP
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="tvip" class="form-control col-md-7 col-xs-12" name="tvip" placeholder="0" value="" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tutm">Tarif Utama
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="tutm" class="form-control col-md-7 col-xs-12" name="tutm" placeholder="0" value="" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tkls1">Tarif Kelas 1
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="tkls1" class="form-control col-md-7 col-xs-12" name="tkls1" placeholder="0" required="required" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                 <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tkls2">Tarif Kelas 2
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="tkls2" class="form-control col-md-7 col-xs-12" name="tkls2" placeholder="0" required="required" onkeyup="changeFormat(this.id, this.value)">
		                    </div>
		                </div>

		                 <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="tkls3">Tarif Kelas 3
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="tkls3" class="form-control col-md-7 col-xs-12" name="tkls3" placeholder="0" required="required" onkeyup="changeFormat(this.id, this.value)">
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
						          <a href="/tariffisio" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
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
	  		kdKel= 'FI'+kdKel;
	  		break;
  		case $('#kelompok').val()=='02':
	  		kdKel= 'FI'+kdKel;
	  		break;
  		case $('#kelompok').val()=='03':
	  		kdKel= 'OT'+kdKel;
	  		break;
  		case $('#kelompok').val()=='04':
	  		kdKel= 'OP'+kdKel;
	  		break;
  		case $('#kelompok').val()=='05':
	  		kdKel= 'TW'+kdKel;
	  		break;
  		case $('#kelompok').val()=='06':
  			kdKel= 'RM'+kdKel;
	  		break;	  	
	  	default: 
	  		kdKel;
	  		break;
	  }

		$.get('/ajax-getautonumbertariffisio?kelompok='+kdKel, function(data){
	    	$('#kode').val(data);
	  	});
	}
	// =================================================================================

	function checkDataFisio(){
     var nama         = $('#nama').val();
      var tvip		   = $('#tvip').val();
      var tutm         = $('#tutm').val();
      var tkls1        = $('#tkls1').val();
      var tkls2        = $('#tkls2').val();
      var tkls3        = $('#tkls3').val();
      var tjalan       = $('#tjalan').val();
    
      if (nama == ''){
      	showWarning(2000, '', 'Nama Tarif Masih Kosong !', true);
        $('#nama').focus();
        return false;   
       }else if (tvip == ''){
      	showWarning(2000, '', 'Nilai Tarif VIP Masih Kosong !', true);
        $('#tvip').focus();
        return false;
      }else if (tutm == ''){
      	showWarning(2000, '', 'Nilai Tarif Utama Masih Kosong !', true);
        $('#tutm').focus();
        return false;
      }else if (tkls1 == ''){
      	showWarning(2000, '', 'Nilai Tarif Kelas1 Masih Kosong !', true);
        $('#tkls1').focus();
        return false;
      }else if (tkls2 == ''){
      	showWarning(2000, '', 'Nilai Tarif Kelas2 Masih Kosong !', true);
        $('#tkls2').focus();
        return false;
      }else if (tkls3 == ''){
      	showWarning(2000, '', 'Nilai Tarif Kelas3 Masih Kosong !', true);
        $('#tkls3').focus();
        return false;
      }else if (tjalan == ''){
      	showWarning(2000, '', 'Nilai Tarif Jalan Masih Kosong !', true);
        $('#tjalan').focus();
        return false;
      }else{
        return true;
      }
  }
</script>

@endsection