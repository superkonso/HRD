@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Poli')

@section('content_header', 'Pendaftaran Poliklinik')

@section('header_description', '')

@section('menu_desc', 'Poli')

@section('link_menu_desc', '/poli')

@section('sub_menu_desc', 'Datapoli')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row font-medium">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
	          	<h3 class="box-title">List Pendaftaran Poli</h3>
	          	@if(Session::has('flash_message'))
			    	<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
	        </div>

	        <div class="box-body">
	        	<a href="/poli" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Daftar Poli Baru</a>
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
	                <input type="text" id="searchkey2" class="form-control pull-right" placeholder="Nomor Poli / Nomor RM / Nama Pasien">
	              </div>
	              <div class="input-group">
	                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>
	              </div>
	            </div>

	            <div class="divscroll">
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
    					+'<th width="75px">No.Trans</th>'
    					+'<th width="75px">Tanggal</th>'
    					+'<th width="75px">Nomor RM</th>'
    					+'<th width="150px">Nama Pasien</th>'
    					+'<th width="100px">Unit</th>'
    					+'<th width="150px">Dokter</th>'
    					+'<th width="75px">Jumlah</th>'
    					+'<th width="50px">Action</th>'
    				+'</tr>';

    	$.get('/ajax-getpendaftaranpoli?key1='+key1+'&key2='+key2, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listpoliObj){
	    			isiData += '<tr>'
	    						+'<td>'+listpoliObj.TRawatJalan_NoReg+'</td>'
					            +'<td>'+listpoliObj.TRawatJalan_Tanggal+'</td>'
					            +'<td>'+listpoliObj.TPasien_NomorRM+'</td>'
					            +'<td style="text-align:left;">'+listpoliObj.TPasien_Nama+'</td>'
					            +'<td>'+listpoliObj.TUnit_Nama+'</td>'
					            +'<td style="text-align:left;">'+listpoliObj.TPelaku_NamaLengkap+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listpoliObj.TRawatJalan_Jumlah)+'</td>'
					            +'<td>'
									+'<a href="/poli/'+listpoliObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20" title="Edit">'
									+'</a>'
								+'</td>'
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

</script>	
 

@endsection