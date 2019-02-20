@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Dokter')

@section('content_header', 'Dokter')

@section('header_description', 'Data Dokter')

@section('menu_desc', 'Dokter')

@section('link_menu_desc', '/dokter')

@section('sub_menu_desc', 'View')

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
		        	<a href="/dokter/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Daftar Dokter Baru</a>
	        	<br><br>
	        	@endif
	        	<div class="form-group">
	              <div class="input-group">
	                <div class="input-group-addon" style="background-color: #167F92;">
	                  <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
	                </div>
	                <input type="text" id="searchkey1" class="form-control pull-right" placeholder="Kode Dokter / Nama Dokter">
	              </div>
	              <div class="input-group">
	                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button> &nbsp;
	                 <a type="button" href="/ctkpelaku" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</a>
	              </div>
	            </div>
	            <div style="overflow-x: scroll;">
	        		<span id="tablebody1"></span>
	        	</div>
			</div>  
		</div> 
	</div> 
</div> 
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

	$('#searchkey1').on('keyup', function(e){
		refreshData();
	});

	function refreshData(){
    	var isiData = '';

    	var key 	= $('#searchkey1').val();

    	isiData += '<table class="responstable">';

    	isiData += '<tr>'
    					+'<th width="90px">Kode Dokter</th>'
    					+'<th width="250px">Nama Dokter</th>'
    					+'<th width="250px">Alamat</th>'
    					+'<th width="100px">Telepon</th>'
    					+'<th width="125px">Unit</th>'
    					+'<th width="75px">Action</th>'
    				+'</tr>';

    	$.get('/ajax-getdatapelaku?key='+key, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listpelakuObj){
	    			isiData += '<tr>'
	    						+'<td>'+listpelakuObj.TPelaku_Kode+'</td>'
					            +'<td style="text-align:left;">'+listpelakuObj.TPelaku_NamaLengkap+'</td>'
					            +'<td style="text-align:left;">'+listpelakuObj.TPelaku_Kota+'</td>'
					            +'<td style="text-align:left;">'+listpelakuObj.TPelaku_Telepon+'</td>'
					            +'<td>'+listpelakuObj.TSpesialis_Nama+'</td>'
					            +'<td>'+(vonly === '0' ? '<a href="/dokter/'+listpelakuObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20" title="Edit">'
									+'</a>' : '')
								+'</td>'
					        +'</tr>';
	    		});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
    		}else{

    			isiData += '<tr><td colspan="6"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}   		
    	});
		
    }
</script>

@endsection