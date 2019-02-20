@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif Fisio')

@section('content_header', 'Tarif Fisio')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tariffisio')

@section('sub_menu_desc', 'Daftar Tarif')

@section('content')

@include('Partials.message')

<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
	         <div style="text-align: center;">
                <h3> LAPORAN DAFTAR TARIF FISIO <br> <b>SMART BRIDGE</b></h3>
            </div>
	        <div class="box-body">
	            <div id="searchkey1" >
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
    					+'<th width="40%">Nama Tarif</th>'
    					+'<th width="15%">Tarif VIP</th>'
    					+'<th width="15%">Tarif Kelas 1</th>'
    					+'<th width="15%">Tarif Kelas 3</th>'
    					+'<th width="15%">Tarif Jalan</th>'
    				+'</thead>';

    	$.get('/ajax-tariffisiomasterprint', function(data){

    		if(data.length > 0){
    			$nomorurut = 0
    			$.each(data, function(index, listTariffisioObj){
    				$nomorurut++;
	    			isiData += '<tr>'
	    						+'<td>'+ $nomorurut + '</td>'
	    						+'<td>'+listTariffisioObj.TTarifFisio_Kode+'</td>'
					            +'<td style="text-align:left;">'+listTariffisioObj.TTarifFisio_Nama+'</td>'
					      	    +'<td style="text-align:right;">'+formatRibuan(listTariffisioObj.TTarifFisio_VIP)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTariffisioObj.TTarifFisio_Kelas1)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTariffisioObj.TTarifFisio_Kelas3)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTariffisioObj.TTarifFisio_Jalan)+'</td>'
				
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
				window.print();
				window.window.location.href="{{$link}}";	
    		}else{

    			isiData += '<tr><td colspan="7"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});
		
    }
</script>

@endsection