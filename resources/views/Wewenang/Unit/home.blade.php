@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Unit')

@section('content_header', 'Data Unit')

@section('content')

@include('Partials.message')

	<div class="row">
		<div class="form-group col-md-12 col-sm-12 col-xs-12">
			<div class="box box-primary">
			  	<div class="box-header">
	          		@if(Session::has('flash_message'))
			    		<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
					@endif
		        </div>

		      <div class="box-body">
	        	@if($viewonly==0)
	        	<a href="unit/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Tambah Unit</a>
	        	<br><br>
        		@endif	
	        	<div class="form-group">
	              <div class="input-group">
	                <div class="input-group-addon" style="background-color: #167F92;">
	                  <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
	                </div>
	                <input type="text" id="searchkey" class="form-control pull-right" placeholder="Kode / Nama Unit">
	              </div>
	              <div class="input-group">
	                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>&nbsp;
	                 <a type="button" href="/ctkunit" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</a>
	              </div>
	            </div>
	            <div style="overflow-x: scroll;">
	        		<span id="tablebody1"></span>
	        	</div>
			  </div> <!--div class="box-body"-->

			</div> <!--div class="box box-primary"-->
		</div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
	</div> <!--div class="row"-->

	<!-- JQuery 1 -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>

	<script>
		var vonly = '{{$viewonly}}';

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

	    	var key 	= $('#searchkey').val();

	    	isiData += '<table class="responstable">';

	    	isiData += '<tr>'
	    					+'<th width="100px">Kode</th>'
	    					+'<th width="200px">Nama Unit</th>'
	    					+'<th width="100px">Group</th>'
	    					+'<th width="100px">Alias</th>'
	    					+'<th width="100px">Initial</th>'
	    					+'<th width="100px">Kode Tarif</th>'
	    					+'<th width="75px">Action</th>'
	    				+'</tr>';

	    	$.get('/ajax-getdataunit?key='+key, function(data){

	    		if(data.length > 0){
	    			$.each(data, function(index, listunitObj){
		    			isiData += '<tr>'
		    						+'<td>'+listunitObj.TUnit_Kode+'</td>'
						            +'<td style="text-align:left;">'+listunitObj.TUnit_Nama+'</td>'
						            +'<td>'+listunitObj.TUnit_Grup+'</td>'
						            +'<td>'+listunitObj.TUnit_Alias+'</td>'
						            +'<td>'+listunitObj.TUnit_Inisial+'</td>'
						            +'<td>'+listunitObj.TGrup_id_trf+'</td>'
						            +'<td>'+(vonly === '0' ? '<a href="/unit/'+listunitObj.id+'/edit">'
											+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20" title="Edit">'
										+'</a>' : '')										
									+'</td>'
						        +'</tr>';
		    		});

	    			isiData += '</table>';
					document.getElementById('tablebody1').innerHTML = isiData;
	    		}else{

	    			isiData += '<tr><td colspan="11"><i>Tidak ada Data Ditemukan</i></td></tr>';
	    			isiData += '<table>';
	    			document.getElementById('tablebody1').innerHTML = isiData;
	    		}

	    		
	    	});
	    }

	</script>

@endsection