@extends('layouts.print_standar')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif Lab')

@section('content_header', 'Tarif Lab')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tariflab')

@section('sub_menu_desc', 'Daftar')

@section('content')

@include('Partials.message')

<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
	         <div style="text-align: center;">
                <h3> LAPORAN DAFTAR TARIF LAB <br> <b>SMART BRIDGE</b></h3>
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

    	$.get('/ajax-tariflabmasterprint', function(data){

    		if(data.length > 0){
    			$nomorurut = 0
    			$.each(data, function(index, listTariflabObj){
    				$nomorurut++;
	    			isiData += '<tr>'
	    						+'<td>'+ $nomorurut + '</td>'
	    						+'<td>'+listTariflabObj.TTarifLab_Kode+'</td>'
					            +'<td style="text-align:left;">'+listTariflabObj.TTarifLab_Nama+'</td>'
					      	    +'<td style="text-align:right;">'+formatRibuan(listTariflabObj.TTarifLab_VIP)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTariflabObj.TTarifLab_Kelas1)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTariflabObj.TTarifLab_Kelas3)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTariflabObj.TTarifLab_Jalan)+'</td>'
				
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