@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Appointment')

@section('content_header', 'Appointment')

@section('header_description', 'Janji Jalan Pemeriksaan')

@section('menu_desc', 'Appointment')

@section('link_menu_desc', '/appointment')

@section('sub_menu_desc', 'Createappointment')

@section('content')

@include('Partials.message')

<?php
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

<div class="row">

	<form class="form-horizontal form-label-left" action="/appointment/@yield('editId')" method="post" data-parsley-validate id="appointment">
	
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

	 			@section('editMethod')
	 			@show

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
	              <a href="/pasien" class="btn btn-success"><img src="{!! asset('images/icon/pasien-baru-icon.png') !!}" width="20" height="20"> Pasien Baru</a>
	              </div>
	            </div>

				<div class="item form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Pasien No.RM </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
						<input type="text" id="pasiennorm" class="form-control col-md-7 col-xs-12" name="pasiennorm" placeholder="Nomor RM" value="@yield('pasiennorm')" required="required">
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Pasien Nama </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
						<input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Pasien" value="@yield('nama')" required="required">
					</div>
				</div>

				<div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Jenis Kelamin</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <div id="jk" class="btn-group" data-toggle="buttons">
	                      <p>

	                      	<!-- sementara tidak pake class="flat" -->
	                      	<!-- class="flat" javascript tidak jalan -->
	                        <input type="radio" name="jk" id="genderL" value="L" @if(!empty($items->TJanjiJalan_Gender)) @if($items->TJanjiJalan_Gender == "L") checked="checked" @endif @else checked="checked" @endif> Laki-laki 
	                        <input type="radio" name="jk" id="genderP" value="P" @if(!empty($items->TJanjiJalan_Gender)) @if($items->TJanjiJalan_Gender != "L") checked="checked" @endif @endif> Perempuan 
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
							<input type="text" name="tgllahir" class="form-control pull-right" id="datepicker" value="@yield('tgllahir')" onchange="hitungUmurInsertForm('datepicker', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');">
	                    </div>
	                </div>
				</div>

				<div class="item form-group">

					<label class="control-label col-md-3 col-sm-3 col-xs-12">Pasien Umur </label>
					<div class="col-md-3 col-sm-3 col-xs-12">
						<input type="text" id="pasienumurthn" class="form-control col-md-7 col-xs-12" name="pasienumurthn" value="@yield('pasienumurthn')" onfocus="hitungUmurInsertForm('datepicker', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');"> Tahun
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12">
						<input type="text" id="pasienumurbln" class="form-control col-md-7 col-xs-12" name="pasienumurbln" value="@yield('pasienumurbln')"> Bulan
					</div>
					<div class="col-md-3 col-sm-3 col-xs-12">
						<input type="text" id="pasienumurhari" class="form-control col-md-7 col-xs-12" name="pasienumurhari" value="@yield('pasienumurhari')"> Hari
					</div>

				</div>

				
				<div class="item form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Telepon </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
						<input type="text" id="telepon" class="form-control col-md-7 col-xs-12" name="telepon" placeholder="Nomor Telepon" value="@yield('telepon')">
					</div>
				</div>

				<div class="item form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Alamat </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea id="alamat" class="form-control" name="alamat" rows="5" style="resize:none;">@yield('alamat')</textarea>
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
							<input type="text" name="tgljanji" class="form-control has-feedback-left" id="datepicker2" value="@yield('tgljanji')" onchange="checkTglJanji();">
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
	                    	<input type="text" name="jamjanji" id="jamjanji" class="form-control" value="@yield('jamjanji')" placeholder="00:00">
		                </div>
					</div>
				</div>

				<div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Klinik Dituju </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <select name="unit" id="unit" class="form-control col-md-7 col-xs-12">

                      	@foreach($units as $unit)
                      		<option value="{{$unit->TUnit_Kode}}" @if(!empty($items->TUnit_Kode)) @if($unit->TUnit_Kode == $items->TUnit_Kode) selected="selected" @endif @endif>{{$unit->TUnit_Nama}}</option>
                      	@endforeach
                       
                      </select>
                    </div>
                  </div>

                <div class="item form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Dokter </label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                      <select name="pelaku" id="pelaku" class="form-control col-md-7 col-xs-12">

                      	@foreach($pelakus as $dokter)
                      		<option value="{{$dokter->TPelaku_Kode}}" @if(!empty($items->TPelaku_Kode)) @if($dokter->TPelaku_Kode == $items->TPelaku_Kode) selected="selected" @endif @endif>{{$dokter->TPelaku_NamaLengkap}}</option>
                      	@endforeach
                       
                      </select>
                    </div>
                  </div>

                <div class="item form-group">
					<label class="control-label col-md-3 col-sm-3 col-xs-12">Keterangan </label>
					<div class="col-md-9 col-sm-9 col-xs-12">
					<textarea id="keterangan" class="form-control" name="keterangan" rows="5" style="resize:none;">@yield('keterangan')</textarea>
                	</div>
				</div>
			
				<div class="ln_solid"></div>

		    </div>
		</div>
	</div>	
	
</div> <!-- <div class="row"> --> 

<div class="row">
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
		//Date picker
	    $('#datepicker').datepicker({
	      autoclose: true
	    });

	    $('#datepicker2').datepicker({
	      autoclose: true
	    });
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


	// ================================ autoComboDokter ===============================
	$('#unit').on('change', function(e){

		var unit_kode = $('#unit').val();

		// == ajax prosess ===================================
		$.get('/ajax-pelaku?unit_kode='+unit_kode, function(data){

			$('#pelaku').empty();

			$.each(data, function(index, pelakuObj){
				$('#pelaku').append('<option value="'+pelakuObj.TPelaku_Kode+'">'+pelakuObj.TPelaku_NamaLengkap+'</option>');
			});

		});
	});
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

</script>

@endsection