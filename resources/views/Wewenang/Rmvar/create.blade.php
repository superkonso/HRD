@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | RM Create')

@section('content_header', 'Create RM')

@section('header_description', '')

@section('menu_desc', 'RM')

@section('link_menu_desc', '/rmvar')

@section('sub_menu_desc', 'Create')

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
	                <h3 class="box-title">Form Input RM Baru</h3>
	            </div>
	            <div class="box-body">
	                <form class="form-horizontal form-label-left" action="/rmvar" method="post" novalidate data-parsley-validate onsubmit="return checkDataRmVar()">
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
	                          			<option value="{{$seri->TRMVar_Seri}}">{{$seri->TRMVar_Seri}}</option>
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
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="kode">Kode RM </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="kode" class="form-control col-md-7 col-xs-12" name="kode" placeholder="Kode RM" required="required" value="" maxlength="4">
		                    </div>
		                </div>

		                <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="nama">Nama RM
		                    </label>
		                    <div class="col-md-6 col-sm-6 col-xs-12">
		                      <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama RM: Swasta/Hindu/..." value="" required="required">
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
	                          <a href="/rmvar" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
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
	  		$("#isbaru").val('1');
	  } else {
	  		$("#divseribaru").hide();
	  		$("#isbaru").val('0');
	  }
	});

	// =================================================================================

	function checkDataRmVar(){
      var nama         = $('#nama').val();
      var kode         = $('#kode').val();
      var panjang      = $('#panjang').val();
    
      if (kode == ''){
      	showWarning(2000, '', 'Kode Tarif Masih Kosong !', true);
        $('#kode').focus();
        return false;
      }else if (nama == ''){
      	showWarning(2000, '', 'Nama Tarif Masih Kosong !', true);
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