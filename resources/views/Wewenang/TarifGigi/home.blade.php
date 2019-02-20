@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Laporan TARIF GIGI')

@section('content_header', 'Tarif Gigi')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifgigi')

@section('sub_menu_desc', 'Daftar Tarif')

@section('content')

@include('Partials.message')

<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
		  	<div class="box-header">
          		@if(Session::has('flash_message'))
		    		<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
	        </div>
	        
	        <div class="box-body">
	        	<a href="/tarifgigi/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Tambah Tarif Baru</a>
	        	<br><br>
	        	<div class="form-group">
	              <div class="input-group">
	                <div class="input-group-addon" style="background-color: #167F92;">
	                  <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
	                </div>
	                <input type="text" id="searchkey1" class="form-control pull-right" placeholder="Kode Tarif / Nama Tarif">
	              </div>
	              <div class="input-group">
	                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button> &nbsp;
	                <a type="button" href="/ctktarifgigi" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</a>
	              </div>
	            </div>
	            <div style="overflow-x: scroll;">
	        		<span id="tablebody1"></span>
	        	</div>
			</div>  
		</div> 
	</div> 
</div> 

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
	$( document ).ready(function() {
		$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      	});  

      	refreshData();

	});

	$('#searchkey1').on('keyup', function(e){
		refreshData();
	});

	function refreshData(){
    	var isiData = '';

    	var key 	= $('#searchkey1').val();
    
    	isiData += '<table class="responstable">';

    	isiData += '<tr>'
    					+'<th width="125px">Kode Tarif</th>'
    					+'<th width="200px">Nama Tarif</th>'
    					+'<th width="110px">Tarif Dokter (FT)</th>'
    					+'<th width="110px">Tarif RS</th>'
    					+'<th width="110px">Tarif Gigi</th>'
    					+'<th width="75px">Action</th>'
    				+'</tr>';

    	$.get('/ajax-tarifgigimaster?kuncicari='+key, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listTarifgigiObj){
	    			isiData += '<tr>'
	    						+'<td>'+listTarifgigiObj.TTarifGigi_Kode+'</td>'
					            +'<td style="text-align:left;">'+listTarifgigiObj.TTarifGigi_Nama+'</td>'
					      	    +'<td style="text-align:right;">'+formatRibuan(listTarifgigiObj.TTarifGigi_JasaDokterFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifgigiObj.TTarifGigi_RSFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifgigiObj.TTarifGigi_Jumlah)+'</td>'
					            +'<td>'
									+'<a href="/tarifgigi/'+listTarifgigiObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20" title="Edit">'
									+'</a>'
								+'</td>'
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
    		}else{

    			isiData += '<tr><td colspan="11"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});
		
    }
</script>

@endsection