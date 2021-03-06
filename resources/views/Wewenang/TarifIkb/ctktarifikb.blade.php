@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | LAPORAN TARIF IKB')

@section('content_header', 'Tarif IKB')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifikb')

@section('sub_menu_desc', 'Ctk Daftar Tarif')

@section('content')

@include('Partials.message')

<div class="row">
    <div class="form-group col-md-12 col-sm-12 col-xs-12">
        <div class="box box-primary">      
            <div style="text-align: center;">
                <h3>LAPORAN DAFTAR TARIF IKB <br> <b>SMART BRIDGE</b></h3>
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
                        +'<th width="90px">Kode Tarif IKB</th>'
                        +'<th width="155">Nama Tarif IKB</th>'
                        +'<th width="75px">VIP</th>'
                        +'<th width="75px">UTAMA</th>'
                        +'<th width="75px">Kelas 1</th>'
                        +'<th width="75px">Kelas 2</th>'
                        +'<th width="75px">Kelas 3</th>'
                        +'<th width="75px">Jalan</th>'
                    +'</thead>';

    	$.get('/ajax-tarifikbprint', function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listTarifIKBObj){
	    			isiData += '<tr>'
                            +'<td>'+listTarifIKBObj.TTarifIRB_Kode+'</td>'
                                +'<td style="text-align:left;">'+listTarifIKBObj.TTarifIRB_Nama+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifIKBObj.TTarifIRB_VIP)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifIKBObj.TTarifIRB_Utama)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifIKBObj.TTarifIRB_Kelas1)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifIKBObj.TTarifIRB_Kelas2)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifIKBObj.TTarifIRB_Kelas3)+'</td>'
                                +'<td style="text-align:right;">'+formatRibuan(listTarifIKBObj.TTarifIRB_Jalan)+'</td>'
					         
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;

				window.print();
				window.window.location.href="tarifikb";
    		}else{

    			isiData += '<tr><td colspan="8"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});

    }
</script>




@endsection
