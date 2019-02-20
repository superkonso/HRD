@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | LAPORAN TARIF UGD')

@section('content_header', 'Tarif UGD')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifugd')

@section('sub_menu_desc', 'Daftar')

@section('content')

@include('Partials.message')

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
	$( document ).ready(function() {
		$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      	});  

      	refreshData();

	});



		function refreshData(){
    	var isiData = '';

    	var key 	= $('#searchkey1').val();
    
    	isiData += '<table class="tablereport">';

    	isiData += '<thead>'
    					+'<th width="125px">Kode Tarif UGD</th>'
    					+'<th width="200px">Nama Tarif UGD</th>'
    					+'<th width="110px">Tarif Dokter (FT)</th>'
    					+'<th width="110px">Tarif RS</th>'
    					+'<th width="110px">Tarif UGD</th>'
    				+'</thead>';

    	$.get('/ajax-tarifugdprint', function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listTarifIGDObj){
	    			isiData += '<tr>'
	    						+'<td>'+listTarifIGDObj.TTarifIGD_Kode+'</td>'
					            +'<td style="text-align:left;">'+listTarifIGDObj.TTarifIGD_Nama+'</td>'
					      	    +'<td style="text-align:right;">'+formatRibuan(listTarifIGDObj.TTarifIGD_DokterFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifIGDObj.TTarifIGD_RSFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifIGDObj.TTarifIGD_Jalan)+'</td>'
					         
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;

				window.print();
				window.window.location.href="tarifugd";
    		}else{

    			isiData += '<tr><td colspan="11"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});

    }
</script>


<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">	   
            <div style="text-align: center;">
                <h3>LAPORAN DAFTAR TARIF UGD <br> <b>SMART BRIDGE</b></h3>
            </div>     
        	<div class="box-body" style="" id="searchkey1">
        		<span id="tablebody1"></span>
        	</div>
		</div>  
	</div> 
</div>  


@endsection
