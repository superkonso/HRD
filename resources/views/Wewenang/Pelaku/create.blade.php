@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Dokter')

@section('content_header', 'Input Dokter Baru')

@section('header_description', '')

@section('menu_desc', 'Dokter')

@section('link_menu_desc', '/dokter')

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
	                <h3 class="box-title">Form Dokter</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/dokter" method="post" novalidate data-parsley-validate onsubmit="return checkDataDokter()">
		                {{csrf_field()}}
		          {{ Form::hidden('kdKel', '', array('id' => 'kdKel')) }}

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kelompok">Kelompok</label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <select id="kelompok" name="kelompok" class="form-control col-md-7 col-xs-12">
	                          		<option value="1">Dokter</option>
	                          		<option value="2">Bidan</option>
	                          		<option value="3">Perawat</option>
	                          		<option value="4">Radiolog</option>
	                          		<option value="5">Lab</option>     
		                     </select>
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode Dokter </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" disabled="disabled" name="kode" placeholder="Kode Dokter" required="required" value="{{$autoNumber}}">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama Dokter <span class="required">*</span>
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Dokter" value="" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="namalengkap">Nama Lengkap
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="namalengkap" class="form-control col-md-7 col-xs-12" name="namalengkap" placeholder="Nama Lengkap Dokter" value="@yield('namalengkap')" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
				            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="alamat">Alamat</label>
				            <div class="col-md-6 col-sm-6 col-xs-12">
				             <textarea id="alamat" class="form-control" name="alamat" rows="2" style="resize:none;" value=''></textarea>
				            </div>
			          	</div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="alamat">Kota
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kota" class="form-control col-md-7 col-xs-12" name="kota" placeholder="Kota" value="" >
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="telepon">Telepon
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="alamat" class="form-control col-md-7 col-xs-12" name="telepon" placeholder="Telepon" value="" required="required">
		                    </div>
		                </div>

		                <div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Unit 1</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select id="unit" name="unit" class="form-control col-md-7 col-xs-12">  
	                          	@foreach($units as $unit)
	                          		<option value="{{$unit->TUnit_Kode}}">{{$unit->TUnit_Nama}}</option>
	                          	@endforeach                         	  	                           
	                          </select>
	                        </div>
	                    </div>

	                    <div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Unit 2</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select name="unit2" class="form-control col-md-7 col-xs-12">
	                          	@foreach($units as $unit2)
	                          		<option value="{{$unit2->TUnit_Kode}}">{{$unit2->TUnit_Nama}}</option>
	                          	@endforeach            
	                          </select>
	                        </div>
	                    </div>

	                    <div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Unit 3</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select name="unit3" class="form-control col-md-7 col-xs-12">
	                          	@foreach($units as $unit3)
	                          		<option value="{{$unit3->TUnit_Kode}}">{{$unit3->TUnit_Nama}}</option>
	                          	@endforeach               
	                          </select>
	                        </div>
	                    </div>

	                    <div class="form-group">
	                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Spesialis</label>
	                        <div class="col-md-6 col-sm-6 col-xs-12">
	                          <select name="spesial" class="form-control col-md-7 col-xs-12">
	                          	@foreach($spesials as $spes)
	                          		<option value="{{$spes->TSpesialis_Kode}}">{{$spes->TSpesialis_Nama}}</option>
	                          	@endforeach              
	                          </select>
	                        </div>
	                    </div>

	                    <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="jenis">Jenis</label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <!--  <input type="text" id="jenis" class="form-control col-md-7 col-xs-12" name="jenis" value="@yield('editJenis')"> -->
		                     <select name="jenis" class="form-control col-md-7 col-xs-12">
		                     	<option value="FT1">Full Time</option>
		                     	<option value="PT1">Part Time</option>
		                     </select>
		                    </div>
		                </div>

		                <div class="form-group">
		                     <label class="control-label col-md-3 col-sm-3 col-xs-12" for="perkiraan">Perkiraan Kode 
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                     <select id="perkiraan" name="perkiraan" class="form-control col-md-7 col-xs-12">
		                     	<option value="0"> </option>
		                     	@foreach($perkkodes as $pk)
	                          		<option value="{{$pk->TPerkiraan_Kode}}" @if(!empty($tarif->TPerkiraan_Kode)) @if ($pk->TPerkiraan_Kode==$tarif->TPerkiraan_Kode) selected="selected" @endif @endif>{{$pk->TPerkiraan_Kode .' - '. $pk->TPerkiraan_Nama}}</option>
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
						          <a href="/dokter" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
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

<script type="text/javascript">
$( document ).ready(function() {
	gantiKode($('#kelompok option:selected').text());
});
// ================================ auto combo kelompok tarif ===============================
	$('#kelompok').on('change', function(e){
	  gantiKode($('#kelompok option:selected').text());
	});

	function gantiKode(kdKel){
	  kdKel= kdKel.substr(0, 1) + '1';
	  $('#kdKel').val(kdKel);

	  $.get('/ajax-getautonumber?kode='+kdKel, function(data){
	    $('#kode').val(kdKel + data);
	  });
	}
	// =================================================================================

	 function checkDataDokter(){
     
      var nama  		  = $('#nama').val();
      var namalengkap     = $('#namalengkap').val();
      var alamat          = $('#alamat').val();
     	
      if(nama == ''){
        showWarning(2000, '', 'Nama Dokter Masih Kosong !', true);
        $('#nama').focus();
        return false;
       }else if(namalengkap == ''){
        showWarning(2000, '', 'Nama Lengkap Dokter Masih Kosong !', true);
        $('#namalengkap').focus();
        return false;
       }else if(alamat == ''){
        showWarning(2000, '', 'Alamat Lengkap Dokter Masih Kosong !', true);
        $('#alamat').focus();
        return false;
      }else{
        return true;
      }
    }
</script>

@endsection