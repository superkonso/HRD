@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | LAPORAN DAFTAR SPESIALIS')

@section('content_header', 'Data Spesialis')

@section('content')

	@include('Partials.message')

	<div class="row">
		<div class="form-group col-md-12 col-sm-12 col-xs-12">
			<div class="box box-primary">
			  	<div style="text-align: center;">
                    <h3>LAPORAN DAFTAR SPESIALIS <br> <b>SMART BRIDGE</b></h3>
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
	    					+'<th width="100px">Kode</th>'
	    					+'<th width="200px">Nama</th>'
	    					+'<th width="100px">Jenis</th>'
	    					+'<th width="100px">IDRS</th>'
	    				+'</thead>';

	    	$.get('/ajax-getdataspesialisprint', function(data){

	    		if(data.length > 0){
	    			$.each(data, function(index, listspesialisobj){
		    			isiData += '<tr>'
		    						+'<td>'+listspesialisobj.TSpesialis_Kode+'</td>'
						            +'<td>'+listspesialisobj.TSpesialis_Nama+'</td>'
						            +'<td>'+listspesialisobj.TSpesialis_Jenis+'</td>'
						            +'<td>'+listspesialisobj.IDRS+'</td>'
						        +'</tr>';
		    		});

	    			isiData += '</table>';
					document.getElementById('tablebody1').innerHTML = isiData;
					window.print();
					window.window.location.href="/spesialis"
	    		}else{

	    			isiData += '<tr><td colspan="4"><i>Tidak ada Data Ditemukan</i></td></tr>';
	    			isiData += '<table>';
	    			document.getElementById('tablebody1').innerHTML = isiData;
	    		}

	    		
	    	});
	    }

	</script>

@endsection