@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Appointment')

@section('content_header', 'Appointment')

@section('header_description', 'Janji Jalan Pemeriksaan')

@section('menu_desc', 'Appointment')

@section('link_menu_desc', '/appointment')

@section('sub_menu_desc', 'Create')

@section('content')

@include('Partials.message')

<?php
	date_default_timezone_set("Asia/Bangkok"); 

	$JLB = 0;
	$JLL = 0;
	$CDB = 0;
?>

@if(!empty($tarifvars))
	@foreach($tarifvars as $tarif)
		@if($tarif->TTarifVar_Seri = 'GENERAL' && $tarif->TTarifVar_Kode = 'JLB') 
			$JLB = $tarif->TTarifVar_Nilai; 
		@elseif($tarif->TTarifVar_Seri = 'GENERAL' && $tarif->TTarifVar_Kode = 'JLL')
			$JLL = $tarif->TTarifVar_Nilai; 
		@elseif($tarif->TTarifVar_Seri = 'GENERAL' && $tarif->TTarifVar_Kode = 'CDB')
			$CDB = $tarif->TTarifVar_Nilai; 
		@else
			
		@endif
	@endforeach
@endif

<div class="row font-medium">

	<form class="form-horizontal form-label-left" action="/appointment" id="appointment" method="post" data-parsley-validate onsubmit="return checkFormAppointment()">
	
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
	          	@if(Session::has('flash_message'))
				    <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
				<h3 class="box-title">Data Pasien</h3>
	        </div>

		    <div class="box-body">
	 			
	 			{{csrf_field()}}

	 			{{ Form::hidden('tempNoRM', '', array('id' => 'tempNoRM')) }}

		    	<div class="form-group">
                	<label class="control-label col-md-3 col-sm-3 col-xs-12">Nomor Trans </label>
                	<div class="col-md-9 col-sm-9 col-xs-12">
                		<input type="text" id="nojan" class="form-control col-md-7 col-xs-12" name="nojan" value="{{$autoNumber}}" readonly>
                	</div>
                </div>

                <div class="form-group">
	              <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
	              <div class="col-md-9 col-sm-9 col-xs-12">
	                <a href="#formsearch" id="caripasienlama" onclick="cariPasienLama();" class="btn btn-primary" data-toggle="modal"><img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Pasien Lama</a>
	              <a href="/pasien/create" class="btn btn-success"><img src="{!! asset('images/icon/pasien-baru-icon.png') !!}" width="20" height="20"> Pasien Baru</a>
	              </div>
	            </div>

				<div class="item form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Pasien No.RM </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
						<input type="text" name="pasiennorm" id="pasiennorm" class="form-control col-md-7 col-xs-12" placeholder="Nomor RM">
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Pasien Nama </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
						<input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12" placeholder="Nama Pasien">
					</div>
				</div>

				<div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Jenis Kelamin</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <div name="jk" id="jk" class="btn-group" data-toggle="buttons">
	                      <p>
	                        <input type="radio" name="jk" id="genderL" value="L"> Laki-laki 
	                        <input type="radio" name="jk" id="genderP" value="P"> Perempuan 
	                      </p>
                      </div>
                    </div>
                  </div>

				<div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Lahir (MM/dd/yyyy)</label>
					<div class="col-md-9 col-sm-9 col-xs-12">
						<div class="input-group date">
							<div class="input-group-addon">
			                	<img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                </div>
							<input type="text" name="tgllahir" id="datepicker" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>" onchange="hitungUmurInsertForm('datepicker', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');">
	                    </div>
	                </div>
				</div>

				<div class="item form-group">

					<label class="control-label col-md-3 col-sm-3 col-xs-12">Pasien Umur </label>
					<div class="col-md-3 col-sm-3 col-xs-12">
						<input type="text" name="pasienumurthn" id="pasienumurthn" class="form-control col-md-7 col-xs-12" value="0" onfocus="hitungUmurInsertForm('datepicker', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');"> Tahun
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12">
						<input type="text" name="pasienumurbln" id="pasienumurbln" class="form-control col-md-7 col-xs-12" value="0"> Bulan
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12">
						<input type="text" name="pasienumurhari" id="pasienumurhari" class="form-control col-md-7 col-xs-12" value="0"> Hari
					</div>

				</div>

				
				<div class="item form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Telepon </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
						<input type="text" name="telepon" id="telepon" class="form-control col-md-7 col-xs-12" placeholder="Nomor Telepon" value="">
					</div>
				</div>

				<div class="item form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Alamat </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea name="alamat" id="alamat" class="form-control" rows="5" style="resize:none;"></textarea>
                	</div>
				</div>			
		</div>

		</div> <!-- <div class="x_panel"> -->
	</div> <!-- <div class="col-md-6 col-sm-12 col-xs-12"> -->

	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="box box-danger">
			<div class="box-header">
				<h3 class="box-title">Janji Jalan</h3>
	        </div>
		    <div class="box-body">

		    	<div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Tanggal Janji (MM/dd/yyyy)</label>
					<div class="col-md-9 col-sm-9 col-xs-12">
						<div class="input-group date">
							<div class="input-group-addon">
			                	<img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                </div>
							<input type="text" name="tgljanji" id="datepicker2" class="form-control has-feedback-left" value="<?php echo date('m/d/Y'); ?>" onchange="checkTglJanji();">
	                    </div>
	                </div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Jam Janji </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
						<div class="input-group">
							<span class="input-group-addon">
		                        <img src="{!! asset('images/icon/time-icon.png') !!}" width="20" height="20">
		                    </span>
	                    	<input type="text" name="jamjanji" id="jamjanji" class="form-control" value="<?php echo date('H:i'); ?>" placeholder="00:00">
		                </div>
					</div>
				</div>

				<div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Klinik Dituju </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <select name="unit" id="unit" class="form-control col-md-7 col-xs-12" onchange="changeDokter();">

                      	@foreach($units as $unit)
                      		<option value="{{$unit->TUnit_Kode}}">{{$unit->TUnit_Nama}}</option>
                      	@endforeach
                       
                      </select>
                    </div>
                  </div>

                <div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Dokter </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <select name="pelaku" id="pelaku" class="form-control col-md-7 col-xs-12">
                      	<option value="">--</option>
                      </select>
                    </div>
                  </div>

                <div class="item form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea name="keterangan" id="keterangan" class="form-control" rows="5" style="resize:none;"></textarea>
                	</div>
				</div>
			
				<div class="ln_solid"></div>

		    </div>
		</div>
	</div>	
	
</div> <!-- <div class="row"> --> 

<div class="row font-medium">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box">
			<div class="box-body">
				<div class="col-md-12 col-md-offset-5">
					<button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
                    <a href="/appointment" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</a>
				</div>
			</div>
		</div>
	</div>
</div>    

</form>

@include('Partials.errors')

<div id="searchFrm">
	<span></span>
</div>

@include('Partials.alertmodal')

<?php
	$search_title 	= 'Pasien';
	$search1		= 'Nama';
	$search2		= '';
	$search3		= '';
?> 

<!-- JQuery 1 -->
{{-- <script src="{{ asset('js/jquery.min.js') }}"></script> --}}
<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

<!-- Auto Complete Search Asset -->
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<script src="{{ asset('js/jquery-ui.js') }}"></script>

<!-- Modal Searching Pasien Lama -->
<script src="{{ asset('js/searchData.js') }}"></script>

<script type="text/javascript">

	$(function () {
	    $('#datepicker').datepicker({
	      autoclose: true
	    });

	    $('#datepicker2').datepicker({
	      autoclose: true
	    });
	});

	$( document ).ready(function() {

      changeDokter();

    });


    function cariPasienLama(){

      	var formSearch = '';

      	formSearch += '<div class="modal fade" id="formsearch" tabindex="-1" role="dialog" aria-labelledby="modalWarning" aria-hidden="true">';
    	formSearch += '<div class="modal-dialog" role="document">';
      	formSearch += '<div class="modal-content">';
        formSearch += '<div class="modal-header alert-info">';
        formSearch += '<img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> DATA PASIEN LAMA';
        formSearch += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
        formSearch += '</div>';
        formSearch += '<div class="modal-body">';
        formSearch += '<div class="input-group">';
        formSearch += '<div class="input-group-addon" style="background-color: #167F92;">';
        formSearch += '<img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">';
        formSearch += '</div>';
        formSearch += '<input type="text" id="cdatapasien" class="form-control pull-right" onkeyup="cdatapasienKU(this.value)" placeholder="Nomor RM / Nama Pasien">';
        formSearch += '</div>';
        formSearch += '<div style="overflow-x: scroll;">';
      	formSearch += '<div id="hasil" style="max-height: 400px; overflow-x: scroll; overflow-y: scroll;"></div>';
      	formSearch += '</div>';
        formSearch += '</div>';
        formSearch += '<div class="modal-footer">';
        formSearch += '<button type="button" class="btn btn-success" data-dismiss="modal" onclick="pilihPasien()"><img src="{!! asset('images/icon/checklist-icon.png') !!}" width="20" height="20"> Pilih</button>';
        formSearch += '</div></div></div>';

      	document.getElementById('searchFrm').innerHTML = formSearch;

      	cPasienLama();
    }

	function changeDokter(){
		var unit_kode = $('#unit').val();

		// == ajax prosess ===================================
		$.get('/ajax-pelaku?unit_kode='+unit_kode, function(data){

			$('#pelaku').empty();

			$.each(data, function(index, pelakuObj){
				$('#pelaku').append('<option value="'+pelakuObj.TPelaku_Kode+'">'+pelakuObj.TPelaku_NamaLengkap+'</option>');
			});

		});
	}
	// =================================================================================


	// ============================= Auto Complete Search by Nomor RM ==================
	$( "#pasiennorm" ).autocomplete({
		  source: '{!!URL::route('autocompletepasienbynorm')!!}',
		  minLength: 1,
		  autoFocus:true,
		  select: function(event, ui) {
	  		$('#pasiennorm').val(ui.item.value);
	  	}
	});

	$('#pasiennorm').on('change', function(e){
		fillPasien();

	});

	function fillPasien(){
		var pasiennorm = $('#pasiennorm').val();

		$.get('/ajax-pasienbynorm?pasiennorm='+pasiennorm, function(data){

			$.each(data, function(index, pasienObj){
				var tgl 		= new Date(pasienObj.TPasien_TglLahir);
				var tgllahir 	= (tgl.getMonth()+1)+'/'+tgl.getDate()+'/'+tgl.getFullYear();

				//document.getElementById('pasien_id').value = pasienObj.id;
				document.getElementById('nama').value = pasienObj.TPasien_Nama;
				document.getElementById('telepon').value = pasienObj.TPasien_Telp;
				document.getElementById('alamat').value = pasienObj.TPasien_Alamat;

				document.getElementById('datepicker').value = tgllahir;

				if(pasienObj.TAdmVar_Gender == 'L'){
					$('#genderL').prop('checked', true);
					
				}else{
					document.getElementById('genderP').checked=true;
					$('#genderP').prop('checked', true);
				}

				hitungUmurInsertForm('datepicker', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');

			});

		});
	}
	// ======================================= End AutoComplete ======================================


	function checkTglJanji()
	{
		var nowDate 		= new Date();
		var newData 		= new Date();
		var tanggalJanji 	= new Date($('#datepicker2').val());

		newData.setDate(nowDate.getDate()-1);

		if (tanggalJanji < newData){
			showDialog('modalWarning', 'Tanggal Transaksi Lebih Kecil dari Hari ini !');
			document.getElementById('datepicker2').value = nowDate.toLocaleDateString();
		}else{
			
		}
	}

	function checkFormAppointment(){
      var nama    	= $('#nama').val();
      var tgljanji 	= $('#datepicker2').val();

      if(nama == '' || nama.toString().length < 1){
        showWarning(2000, '', 'Silahkan Isi Nama Pasien !', true);
        $('#nama').focus();
        return false;
      }else if(tgljanji = '' || tgljanji.toString().length < 10){
        showWarning(2000, '', 'Tanggal Lahir Pasien Masih Kosong !', true);
        $('#datepicker2').focus();
        return false;
      }else{
        return true;
      }
    }

</script>

@endsection