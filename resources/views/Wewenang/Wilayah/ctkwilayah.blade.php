@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | LAPORAN DAFTAR WILAYAH')

@section('content_header', 'Data Wilayah')

@section('content')

	@include('Partials.message')

	<div class="row">
		<div class="form-group col-md-12 col-sm-12 col-xs-12">
			<div class="box box-primary">
			  	<div style="text-align: center;">
                    <h3>LAPORAN DAFTAR WILAYAH <br> <b>SMART BRIDGE</b></h3>
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
	    					+'<th width="20%">Kode</th>'
	    					+'<th width="50%">Nama Wilayah</th>'
	    					+'<th width="15%">Jenis</th>'
	    					+'<th width="15%">IDRS</th>'
	    				+'</thead>';

	    	$.get('/ajax-getdatawilayahprint', function(data){

	    		if(data.length > 0){
	    			$.each(data, function(index, listwilayahObj){
	    				var wilayah = 0;
	    				if (listwilayahObj.TWilayah2_Jenis == '1') {
	    					wilayah = 'Provinsi';
	    				} else if (listwilayahObj.TWilayah2_Jenis == '2') {
	    					wilayah = 'Kabupaten/Kota';
	    				} else if (listwilayahObj.TWilayah2_Jenis == '3') {
	    					wilayah = 'Kecamatan';
	    				} else if (listwilayahObj.TWilayah2_Jenis == '4') {
	    					wilayah = 'Kelurahan';
	    				} else {
	    					wilayah = listwilayahObj.TWilayah2_Jenis;
	    				}
		    			isiData += '<tr>'
		    						+'<td>'+listwilayahObj.TWilayah2_Kode+'</td>'
						            +'<td>'+wilayah+'</td>'
						            +'<td style="text-align:left;">'+listwilayahObj.TWilayah2_Nama+'</td>'
						            +'<td>'+listwilayahObj.IDRS+'</td>'
						        +'</tr>';
		    		});

	    			isiData += '</table>';
					document.getElementById('tablebody1').innerHTML = isiData;
					window.print();
					window.window.location.href="wilayah"
	    		}else{

	    			isiData += '<tr><td colspan="4"><i>Tidak ada Data Ditemukan</i></td></tr>';
	    			isiData += '<table>';
	    			document.getElementById('tablebody1').innerHTML = isiData;
	    		}

	    		
	    	});
	    }

	</script>

@endsection