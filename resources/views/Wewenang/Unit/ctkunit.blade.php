@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | LAPORAN DAFTAR UNIT')

@section('content_header', 'Data Unit')

@section('content')

	@include('Partials.message')

	<div class="row">
		<div class="form-group col-md-12 col-sm-12 col-xs-12">
			<div class="box box-primary">
			  	<div style="text-align: center;">
                    <h3>LAPORAN DAFTAR UNIT <br> <b>SMART BRIDGE</b></h3>
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
	    					+'<th width="15%">Kode</th>'
	    					+'<th width="35%">Nama Unit</th>'
	    					+'<th width="15%">Group</th>'
	    					+'<th width="15%">Alias</th>'
	    					+'<th width="10%">Initial</th>'
	    					+'<th width="10%">Kode Tarif</th>'
	    				+'</thead>';

	    	$.get('/ajax-getdataunitprint', function(data){

	    		if(data.length > 0){
	    			$.each(data, function(index, listunitObj){
		    			isiData += '<tr>'
		    						+'<td>'+listunitObj.TUnit_Kode+'</td>'
						            +'<td style="text-align:left;">'+listunitObj.TUnit_Nama+'</td>'
						            +'<td>'+listunitObj.TUnit_Grup+'</td>'
						            +'<td>'+listunitObj.TUnit_Alias+'</td>'
						            +'<td>'+listunitObj.TUnit_Inisial+'</td>'
						            +'<td>'+listunitObj.TGrup_id_trf+'</td>'
						        +'</tr>';
		    		});

	    			isiData += '</table>';
					document.getElementById('tablebody1').innerHTML = isiData;
					window.print();
					window.window.location.href="unit"
	    		}else{

	    			isiData += '<tr><td colspan="11"><i>Tidak ada Data Ditemukan</i></td></tr>';
	    			isiData += '<table>';
	    			document.getElementById('tablebody1').innerHTML = isiData;
	    		}

	    		
	    	});
	    }

	</script>

@endsection