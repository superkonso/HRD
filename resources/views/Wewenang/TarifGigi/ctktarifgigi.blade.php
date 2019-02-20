@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif Gigi ')

@section('content_header', 'Tarif Gigi')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifgigi')

@section('sub_menu_desc', 'Ctk Daftar Tarif')

@section('content')

@include('Partials.message')

<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
		  	<div style="text-align: center;">
                <h3>LAPORAN DAFTAR TARIF GIGI <br> <b>SMART BRIDGE</b></h3>
            </div>
	        
	        <div class="box-body">
	   	            <div id="searchkey1"">
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
    
    	isiData += '<table class="tablereport">';

    	isiData += '<thead>'
    					+'<th width="5%">No</th>'
    					+'<th width="15%">Kode Tarif</th>'
    					+'<th width="35%">Nama Tarif</th>'
    					+'<th width="15%">Tarif Dokter (FT)</th>'
    					+'<th width="15%">Tarif RS</th>'
    					+'<th width="15%">Tarif Gigi</th>'
    				+'</thead>';


    	$.get('/ajax-tarifgigimasterprint', function(data){

    		if(data.length > 0){
    			$nomorurut = 0
    			$.each(data, function(index, listTarifgigiObj){
    				$nomorurut++;
	    			isiData += '<tr>'
	    						+'<td>'+ $nomorurut + '</td>'
	    						+'<td>'+listTarifgigiObj.TTarifGigi_Kode+'</td>'
					            +'<td style="text-align:left;">'+listTarifgigiObj.TTarifGigi_Nama+'</td>'
					      	    +'<td style="text-align:right;">'+formatRibuan(listTarifgigiObj.TTarifGigi_JasaDokterFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifgigiObj.TTarifGigi_RSFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifgigiObj.TTarifGigi_Jumlah)+'</td>'
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
				window.print();
        		window.window.location.href="tarifgigi";
    		}else{

    			isiData += '<tr><td colspan="11"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});
		
    }
</script>

@endsection