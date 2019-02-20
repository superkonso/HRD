@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | INFO APPOINTMENT PER DOKTER')

@section('content_header', 'INFORMASI APPOINTMENT PER DOKTER')

@section('header_description', '')

@section('menu_desc', 'Appointment')

@section('link_menu_desc', '/appointment')

@section('sub_menu_desc', 'Create')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

	<div class="row font-medium">
	<form action="/ctkinfoappointmentdokter" method="put" id="formappointmendokter" data-parsley-validate >
		
   {{ Form::hidden('Kode', '', array('id' => 'Kode', 'name' => 'Kode')) }}
   {{ Form::hidden('Nama', '', array('id' => 'Nama', 'name' => 'Nama')) }}

		<div class="form-group col-md-12 col-sm-12 col-xs-12">
	    <div class="box box-primary">
	      	<div class="box-header">
	            <h3 class="box-title">Pencarian</h3>
	            @if(Session::has('flash_message'))
	            	<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
	        	@endif
	    	</div>

	         <div class="box-body">
	            <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="searchkey1" id="searchkey1" class="form-control pull-right" placeholder="Tanggal Awal" value="<?php echo date('m/d/Y'); ?>">
                </div>

                <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="searchkey2" id="searchkey2" class="form-control pull-right" placeholder="Tanggal Akhir" value="<?php echo date('m/d/Y'); ?>">
                </div>

	            <div class="input-group">
	              <div class="input-group-addon" style="background-color: #167F92;">
	              	<img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
	               	</div>
	              
	                <input type="text" name="kuncicari" id="kuncicari" class="form-control pull-right" placeholder="Cari berdasarkan nama dokter">
	            </div>
	       		
	       		  <!-- <input type="text" name="Nama" id="Nama" class="form-control pull-right"> -->
	          <!--    <select name="Daftar" id="Daftar" class="form-control">
                   @foreach($admvars as $admvar)
                      <option value="{{$admvar->TAdmVar_Kode}}">{{$admvar->TAdmVar_Nama}}</option>
                   @endforeach
                  </select>
 				</div> -->

	            <div class="input-group">
	                <div class="input-group">
	                  <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>
	                </div>
	            </div>

{{-- 	             <div style="overflow-x: scroll;" >
	        		<span id="tablebody1"></span>
	        		>
	        	</div> --}}
	      	</div> <!--div class="box-body"-->
	    </div> <!--div class="box box-primary"-->
	  </div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->

	<div class="row">
		<!-- <span id="formaction"> -->
	<form class="form-horizontal form-label-left" action="/appointment" method="post" name="formappointment" id="formappointment" data-parsley-validate onsubmit="return">
	<!-- </span> -->
      	{{method_field('PUT')}} 
	      <!-- Token -->
    	{{csrf_field()}}

      <?php date_default_timezone_set("Asia/Bangkok"); ?>
	    <!-- ===================================== Data Pasien =========================================== -->
	    <div class="col-md-12 col-sm-12 col-xs-12">
		<div class="col-md-4 col-sm-12 col-xs-12">
			<div class="box box-success">
				<div class="box-header with-border">
	              <h3 class="box-title">Daftar Dokter</h3>
	            </div>
	            <!-- /.box-header -->
				<div class="box-body" >
			            <div class="form-group">
			            	<div class="divscroll" >
	            				<span id="tablebody1">
	            				
	            				</span>
	            			</div>
			            </div>	        
			    </div>  {{-- box Body --}}
			</div> {{-- Box Success --}}
		</div> {{--Col 6--}}

		    <!-- ===================================== Data Pasien =========================================== -->
		<div class="col-md-8 col-sm-12 col-xs-12">
			<div class="box box-success">
				<div class="box-body">
			   	           <div class="form-group">
				            <div class="divscroll">
	            				<span id="tablebodyhasil">
	            		
	            				</span>
	            			</div>
			            </div>
			    </div>  {{-- box Body --}}
			</div> {{-- Box Success --}}
		</div> {{--Col 6--}}
		</div>
	</form>
	</div>
	<!-- ================== -->
    <div class="row">
      <div class="form-group col-md-12 col-sm-12 col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="col-md-12 col-md-offset-5">
                <button type="submit" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</button>
              <a href="/appointment" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</a>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ================== -->

	@include('Partials.modals.searchmodal')
  	@include('Partials.alertmodal')

	<!-- JQuery 1 -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

	<!-- Auto Complete Search Asset -->
	<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
	<script src="{{ asset('js/jquery-ui.js') }}"></script>

	<!-- Modal Searching Pasien Lama -->
	<script src="{{ asset('js/searchData.js') }}"></script>
	<script type="text/javascript">

	    var arrTrans = [];
	    var arrTransDetil = [];
	    var arrRiwayat = [];
	    
	    var indTrans  = 0;
	    var indTransDetil = 0;
	    var indRiwayat = 0;

	    var indGrid   = 0;

 	    $(function () {
	        $('#searchkey1').datepicker({
	          autoclose: true,
	          dateFormat: 'm/d/Y'
	        });
	        
	        $('#searchkey2').datepicker({
	          autoclose: true,
	          dateFormat: 'm/d/Y'
	        });
	    });

    $('#searchkey1').on('keyup', function(e){
		refreshData();
		refreshDataHasil();
		});

	$('#searchkey2').on('keyup', function(e){
		refreshData();
		refreshDataHasil();
		});

	$('#kuncicari').on('keyup', function(e){
		refreshData();
		refreshDataHasil();
		});

	$( document ).ready(function() {
		$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      	});  

      	refreshData();
      	refreshDataHasil();
     	});

	function refreshData(){
    	var isiData = '';
    	var key 	= $('#kuncicari').val();
    
    	isiData += '<table class="responstable">';

    	isiData += '<tr>'
    					+'<th width="35%">Kode Dokter </th>'
    					+'<th width="55%">Nama Dokter </th>'
    				+'</tr>';
 	
      	$.get('/ajax-getdokter?key='+key, function(data){
    		if(data.length > 0){
    			$.each(data, function(index, listPelaku){
	    			isiData += '<tr onclick="refreshDataHasil(\''+listPelaku.TPelaku_Kode+'\');">'
	    						+'<td style="text-align:left;">'+listPelaku.TPelaku_Kode+'</td>'
					            +'<td style="text-align:left;">'+listPelaku.TPelaku_Nama+'</td>'
					        +'</tr>';
					      
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
    		}else{

    			isiData += '<tr><td colspan="8"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});
   }

    function refreshDataHasil(Kd){
    	var isiData = '';
    	var key1 	= $('#searchkey1').val();
    	var key2 	= $('#searchkey2').val();
    	var key3 	= Kd;

	$.get('/ajax-getdokter2?key='+Kd, function(data){
    		if(data.length > 0){
    			$.each(data, function(index, listPelaku){
	    		$('#Nama').val(listPelaku.TPelaku_NamaLengkap);
				$('#Kode').val(listPelaku.TPelaku_Kode);	      
	    			});
	    	 		}
    	});

    	isiData += '<table class="responstable">';

    	isiData += '<tr>'
    					+'<th width="17%">No Appointment</th>' 
    					+'<th width="10%">RM </th>'
    					+'<th width="20%">Nama Pasien </th>'
    					+'<th width="10%">Tanggal Janji</th>' 
    					+'<th width="8%">Jam Janji </th>'
    					+'<th width="5%">Gender </th>'
    					+'<th width="15%">Telpon</th>' 
    					+'<th width="15%">Tanggal Lahir </th>'
    				+'</tr>';

    	$.get('/ajax-getappointment?key1='+key1+'&key2='+key2+'&key3='+Kd, function(data){
    		if(data.length > 0){
    			$.each(data, function(index, listpasien){
	    			isiData += '<tr>'
	    						+'<td style="text-align:left;">'+listpasien.TJanjiJalan_NoJan+'</td>'
	    						+'<td style="text-align:left;">'+listpasien.TPasien_NomorRM+'</td>'
	    						+'<td style="text-align:left;">'+listpasien.TJanjiJalan_Nama+'</td>'
	    						+'<td style="text-align:left;">'+listpasien.TJanjiJalan_TglJanji+'</td>'
	    						+'<td style="text-align:left;">'+listpasien.TJanjiJalan_JamJanji+'</td>'
	    						+'<td style="text-align:left;">'+listpasien.TJanjiJalan_Gender+'</td>'
	    						+'<td style="text-align:left;">'+listpasien.TJanjiJalan_PasienTelp+'</td>'
	    						+'<td style="text-align:left;">'+listpasien.TPasien_TglLahir+'</td>'
					            +'</tr>';
					            
					   		});

    				isiData += '</table>';
				document.getElementById('tablebodyhasil').innerHTML = isiData;
    		}else{

    			isiData += '<tr><td colspan="8"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebodyhasil').innerHTML = isiData;
    		}   		
    	});		
    }
 
	</script>
@endsection