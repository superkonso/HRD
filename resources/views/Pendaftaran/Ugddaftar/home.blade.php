@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Daftar UGD')

@section('content_header', 'UGD')

@section('header_description', 'Daftar UGD')

@section('menu_desc', 'Daftarugd')

@section('link_menu_desc', '/ugddaftar')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
	          	<h3 class="box-title">List Pendaftaran UGD</h3>
	          	@if(Session::has('flash_message'))
			    	<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
	        </div>

           <div class="box-body">
	        	<a href="/ugddaftar" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Daftar UGD Baru</a>
	        	<br><br>
	        	<div class="form-group">
	              <div class="input-group">
	                <div class="input-group-addon" style="background-color: #167F92;">
	                  <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
	                </div>
	                <input type="text" id="searchkey1" class="form-control pull-right" placeholder="Tanggal Transaksi" value="<?php echo date('m/d/Y'); ?>">
	              </div>
	              <div class="input-group">
	                <div class="input-group-addon" style="background-color: #167F92;">
	                  <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20"> 
	                </div>
	                <input type="text" id="searchkey2" class="form-control pull-right" placeholder="Nomor Registrasi / Nomor RM / Nama Pasien">
	              </div>
	              <div class="input-group">
	                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>
	              </div>
	            </div>
	            <div style="max-height: 400px; overflow-x: scroll; overflow-y: scroll;">
	        		<span id="tablebody1"></span>
	        	</div>
			</div> <!--div class="box-body"-->

		</div> <!--div class="box box-primary"-->
	</div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
</div> <!--div class="row"-->

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
	$(function () {
          $('#searchkey1').datepicker({
            autoclose: true
          })
          .on('changeDate', function(en) {
	          refreshData();
	        });
      });

	$('#searchkey2').on('keyup', function(e){
		refreshData();
	});

	$( document ).ready(function() {

		$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
        });  

        refreshData();

	});
	
function refreshData(){ 
    	var isiData = '';

    	var key1 	= $('#searchkey1').val();
    	var key2 	= $('#searchkey2').val();

    	isiData += '<table class="responstable">';

    	isiData += '<tr>'
    					+'<th width="125px">No.Trans</th>'
    					+'<th width="125px">Tanggal</th>'
    					+'<th width="100px">Nomor RM</th>'
    					+'<th width="100%">Nama Pasien</th>'
    				    +'<th width="250px">Dokter</th>'
    					+'<th width="100px">Jumlah</th>'
    					+'<th width="50px">Action</th>'
    				+'</tr>';

    	$.get('/ajax-getpendaftaranugd?key1='+key1+'&key2='+key2, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listugdObj){
	    			isiData += '<tr>'
	    						+'<td>'+listugdObj.TRawatUGD_NoReg+'</td>'
					            +'<td>'+listugdObj.TRawatUGD_Tanggal+'</td>'
					            +'<td>'+listugdObj.TPasien_NomorRM+'</td>'
					            +'<td style="text-align:left;">'+listugdObj.TPasien_Nama+'</td>'
					            +'<td style="text-align:left;">'+listugdObj.TPelaku_NamaLengkap+'</td>'
					            +'<td>Rp. '+formatRibuan(listugdObj.TRawatUGD_Jumlah)+'</td>'
					            +'<td>'
									+'<a href="/ugddaftar/'+listugdObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20">'
									+'</a>'
								+'</td>'
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
    		}else{

    			isiData += '<tr><td colspan="7"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});
    }

</script>	

@endsection