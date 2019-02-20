@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Laporan Daftar Tarif Rawat Jalan')

@section('content_header', 'Tarif Rawat Jalan')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/ctktarifjalan')

@section('sub_menu_desc', 'Daftar')

@section('content')

@include('Partials.message')

<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
			<div style="text-align: center;">
                <h3>LAPORAN DAFTAR TARIF RAWAT JALAN <br> <b>SMART BRIDGE</b></h3>
            </div>
	        <div class="box-body">
	            <div id="searchkey1">
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

	
 	$('#searchkey2').on('click', function(e){  
    	refreshData();
    });
	
	function refreshData(){
    	var isiData = '';

    	var key 	= '{{$searchkey1}}';
    	var key2 	= '';
    	if ('{{$searchkey2}}' == '1') {
    		key2 = 'ALL';
    	} 
    	else { 
    		key2 = 'A';
    	}

    	isiData += '<table class="tablereport">';

    	isiData += '<thead>'
    					+'<th width="5%">No</th>'
    					+'<th width="15%">Kode Tarif</th>'
    					+'<th width="35%">Nama Tarif</th>'
    					+'<th width="15%">Tarif Dokter (FT)</th>'
    					+'<th width="15%">Tarif RS</th>'
    					+'<th width="15%">Tarif Jalan</th>'
    				+'</thead>';

    	$.get('/ajax-tarifjalanmasterprint?kuncicari='+key+'&status='+key2, function(data){

    		if(data.length > 0){
    			$nomorurut = 0
    			$.each(data, function(index, listTarifjalanObj){
    				$nomorurut++;
	    			isiData += '<tr>'
	    						+'<td>'+ $nomorurut + '</td>'
	    						+'<td>'+listTarifjalanObj.TTarifJalan_Kode+'</td>'
					            +'<td style="text-align:left;">'+listTarifjalanObj.TTarifJalan_Nama+'</td>'
					      	    +'<td style="text-align:right;">'+formatRibuan(listTarifjalanObj.TTarifJalan_DokterFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifjalanObj.TTarifJalan_RSFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifjalanObj.TTarifJalan_Jalan)+'</td>'
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
				window.print();
        		window.window.location.href="{{$link}}";
    		}else{

    			isiData += '<tr><td colspan="11"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});
    }

</script>

@endsection