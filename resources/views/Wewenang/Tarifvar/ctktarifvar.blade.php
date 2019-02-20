@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | LAPORAN DAFTAR TARIF')

@section('content_header', 'Data Tarif')

@section('content')

	@include('Partials.message')

	<div class="row">
		<div class="form-group col-md-12 col-sm-12 col-xs-12">
			<div class="box box-primary">
			  	<div style="text-align: center;">
                    <h3>LAPORAN DAFTAR TARIF <br> <b>SMART BRIDGE</b></h3>
                </div>

			    <div class="box-body">
			        <div id="searchkey">
			        	<span id="tablebody1"></span>
			        </div>
				</div> <!--div class="box-body"-->

			</div> <!--div class="box box-primary"-->
		</div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
	</div> <!--div class="row"-->

	<!-- JQuery 1 -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>

	<script>
		$( document ).ready(function() {
			$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
	          $("#successAlert").slideUp(500);
	      	});  

	      	refreshData();

		});

		$('#searchkey').on('keyup', function(e){
			refreshData();
		});

		function refreshData(){
	    	var isiData = '';

	    	isiData += '<table class="tablereport">';

	    	isiData += '<thead>'
	    					+'<th width="100px">Seri</th>'
	    					+'<th width="50px">Kode</th>'
	    					+'<th width="100px">Nama</th>'
	    					+'<th width="50px">Panjang</th>'
	    					+'<th width="50px">Nilai</th>'
	    					+'<th width="50px">Kelompok</th>'
	    					+'<th width="50px">Nilai Lama</th>'
	    					+'<th width="100px">IDRS</th>'
	    				+'</thead>';

	    	$.get('/ajax-getdatatarifvarprint', function(data){

	    		if(data.length > 0){
	    			$.each(data, function(index, listtarifobj){
		    			isiData += '<tr>'
		    						+'<td style="text-align:left;">'+listtarifobj.TTarifVar_Seri+'</td>'
						            +'<td style="text-align:left;">'+listtarifobj.TTarifVar_Kode+'</td>'
						            +'<td style="text-align:left;">'+listtarifobj.TTarifVar_Nama+'</td>'
						            +'<td>'+listtarifobj.TTarifVar_Panjang+'</td>'
						            +'<td>'+listtarifobj.TTarifVar_Nilai+'</td>'
						            +'<td>'+listtarifobj.TTarifVar_Kelompok+'</td>'
						            +'<td>'+listtarifobj.TTarifVar_NilaiLama+'</td>'
						            +'<td>'+listtarifobj.IDRS+'</td>'
						        +'</tr>';
		    		});

	    			isiData += '</table>';
					document.getElementById('tablebody1').innerHTML = isiData;
					window.print();
					window.window.location.href="/tarifvar"
	    		}else{

	    			isiData += '<tr><td colspan="9"><i>Tidak ada Data Ditemukan</i></td></tr>';
	    			isiData += '<table>';
	    			document.getElementById('tablebody1').innerHTML = isiData;
	    		}

	    		
	    	});
	    }

	</script>

@endsection