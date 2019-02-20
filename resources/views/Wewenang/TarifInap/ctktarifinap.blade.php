@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | LAPORAN TARIF INAP')

@section('content_header', 'Tarif INAP')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifinap')

@section('sub_menu_desc', 'Daftar')

@section('content')

@include('Partials.message')

<div class="row">
    <div class="form-group col-md-12 col-sm-12 col-xs-12">
        <div class="box box-primary">      
            <div style="text-align: center;">
                <h3>LAPORAN DAFTAR TARIF INAP <br> <b>SMART BRIDGE</b></h3>
            </div>     
            <div class="box-body" style="" id="searchkey1">
                <span id="tablebody1"></span>
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



		function refreshData(){
    	var isiData = '';

    	var key 	= $('#searchkey1').val();
    
    	isiData += '<table class="tablereport">';

        isiData += '<thead>'
                        +'<th width="90px">Kode Tarif Inap</th>'
                        +'<th width="155px">Nama Tarif Inap</th>'
                        +'<th width="75px">VIP</th>'
                        +'<th width="75px">UTAMA</th>'
                        +'<th width="75px">Kelas 1</th>'
                        +'<th width="75px">Kelas 2</th>'
                        +'<th width="75px">Kelas 3</th>'
                        +'<th width="75px">Jalan</th>'
                    +'</thead>';

    	$.get('/ajax-tarifinapprint', function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listTarifInapObj){
	    			isiData += '<tr>'
                            +'<td>'+listTarifInapObj.TTarifInap_Kode+'</td>'
                                +'<td style="text-align:left;">'+listTarifInapObj.TTarifInap_Nama+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifInapObj.TTarifInap_VIP)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifInapObj.TTarifInap_Utama)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifInapObj.TTarifInap_Kelas1)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifInapObj.TTarifInap_Kelas2)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifInapObj.TTarifInap_Kelas3)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifInapObj.TTarifInap_Jalan)+'</td>'
					         
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;

				window.print();
				window.window.location.href="tarifinap";
    		}else{

    			isiData += '<tr><td colspan="8"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});

    }
</script>




@endsection
