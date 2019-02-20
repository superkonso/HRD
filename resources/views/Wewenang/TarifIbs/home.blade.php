@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif IBS')

@section('content_header', 'Tarif IBS')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifibs')

@section('sub_menu_desc', 'View')

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
	        	<a href="/tarifibs/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Tambah Tarif IBS Baru</a>
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

	                <a type="button" href="/ctktarifibs" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</a>
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
    					+'<th width="125px">Kode Tarif IBS</th>'
    					+'<th width="200px">Nama Tarif IBS</th>'
    					+'<th width="75px">VIP</th>'
    					+'<th width="75px">UTAMA</th>'
    					+'<th width="75px">Kelas 1</th>'
    					+'<th width="75px">Kelas 2</th>'
    					+'<th width="75px">Kelas 3</th>'
    					+'<th width="75px">Jalan</th>'
    					+'<th width="75px">Action</th>'
    				+'</tr>';

    	$.get('/ajax-tarifibsmaster?kuncicari='+key, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listTarifIBSObj){
	    			isiData += '<tr>'
	    						+'<td>'+listTarifIBSObj.TTarifIBS_Kode+'</td>'
					            +'<td style="text-align:left;">'+listTarifIBSObj.TTarifIBS_Nama+'</td>'
					      	    +'<td style="text-align:right;">'+formatRibuan(listTarifIBSObj.TTarifIBS_VIP)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifIBSObj.TTarifIBS_Utama)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifIBSObj.TTarifIBS_Kelas1)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifIBSObj.TTarifIBS_Kelas2)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifIBSObj.TTarifIBS_Kelas3)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifIBSObj.TTarifIBS_Jalan)+'</td>'
					            +'<td>'
									+'<a href="/tarifibs/'+listTarifIBSObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20" title="Edit">'
									+'</a>'
								+'</td>'
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
    		}else{

    			isiData += '<tr><td colspan="9"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});
    }
</script>

@endsection