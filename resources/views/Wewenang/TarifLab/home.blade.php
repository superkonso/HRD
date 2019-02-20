@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif Lab')

@section('content_header', 'Tarif Lab')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tariflab')

@section('sub_menu_desc', 'Daftar')

@section('content')

@include('Partials.message')

<div class="row">
	<form action="/ctktariflab" method="post" data-parsley-validate >
		{{csrf_field()}}
		{{ Form::hidden('viewsaja', $viewonly, array('id' => 'viewsaja')) }}

		<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
		  	<div class="box-header">
          		@if(Session::has('flash_message'))
		    		<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
	        </div>
	        
	        <div class="box-body">
	        	@if($viewonly==0)
	        	<a href="/tariflab/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Tambah Tarif Baru</a>
	        	<br><br>
	        	@endif
	        	<div class="form-group">
	              <div class="input-group">
	                <div class="input-group-addon" style="background-color: #167F92;">
	                  <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
	                </div>
	                <input type="text" id="searchkey1" class="form-control pull-right" placeholder="Kode Tarif / Nama Tarif">
	              </div>
	              <div class="input-group">
	                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button> &nbsp;
	                <button type="submit" href="/ctktariflab" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</button>
	              </div>
	            </div>
	            <div style="overflow-x: scroll;">
	        		<span id="tablebody1"></span>
	        	</div>
			</div>  
		</div> 
	</div> 
	</form>
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
    					+'<th width="100px">Kode Tarif</th>'
    					+'<th width="100%">Nama Tarif</th>'
    					+'<th width="110px">Tarif VIP</th>'
    					+'<th width="110px">Tarif Kelas 1</th>'
    					+'<th width="110px">Tarif Kelas 3</th>'
    					+'<th width="110px">Tarif Jalan</th>'
    					+'<th width="75px">Action</th>'
    				+'</tr>';

    	$.get('/ajax-tariflabmaster?kuncicari='+key, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listTariflabObj){
	    			isiData += '<tr>'
	    						+'<td>'+listTariflabObj.TTarifLab_Kode+'</td>'
					            +'<td style="text-align:left;">'+listTariflabObj.TTarifLab_Nama+'</td>'
					      	    +'<td style="text-align:right;">'+formatRibuan(listTariflabObj.TTarifLab_VIP)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTariflabObj.TTarifLab_Kelas1)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTariflabObj.TTarifLab_Kelas3)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTariflabObj.TTarifLab_Jalan)+'</td>'
					            +'<td>'
									+(vonly=='0' ? '<a href="/tariflab/'+listTariflabObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20" title="Edit">'+'</a>' : '')
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