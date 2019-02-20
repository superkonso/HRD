@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Menu Item')

@section('content_header', 'Daftar Menu Item')

@section('header_description', '')

@section('menu_desc', 'Menu Item')

@section('link_menu_desc', '/menuitem')

@section('sub_menu_desc', 'Daftar Menu Item')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
		  	<div class="box-header">
		  		<h3 class="box-title">List Menu Item</h3>
	          	@if(Session::has('flash_message'))
			    	<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
	        </div>

	    <div class="box-body">
        	<a href="/menuitem/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Menu Item Baru</a>
        	
          	<div class="input-group">
	            <div class="input-group-addon" style="background-color: #167F92;">
	              <img src="{!! asset('images/icon/menu-icon.png') !!}" width="20" height="20">
	            </div>
	            <select id="searchkey1" name="menu" class="form-control">  
		      		@foreach($menus as $menu)
		      			<option value="{{$menu->TMenu_Kode}}">{{$menu->TMenu_Nama}}</option>
		      		@endforeach                          	  	                           
		      	</select>
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
	$( document ).ready(function() {
		$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      	});  

      	refreshData();

	});

	$('#searchkey1').on('change', function(e){
		refreshData();
	});

	function refreshData(){
    	var isiData = '';

    	var key 	= $('#searchkey1').val();
    
    	isiData += '<table class="responstable">';

    	isiData += '<tr>'
    					+'<th width="100px">Kelompok Kode</th>'
    					+'<th width="100px">Menu Kode</th>'
    					+'<th width="350px">Menu Nama</th>'
    					+'<th width="300px">Menu Item Link</th>'
    					+'<th width="105px">Menu Item Jenis</th>'
    					+'<th width="105px">Action</th>'
    				+'</tr>';

    	$.get('/ajax-getmenuitem?key='+key, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listitemObj){
	    			isiData += '<tr>'
	    						+'<td>'+listitemObj.TMenu_Kode+'</td>'
					            +'<td>'+listitemObj.TMenuItem_Item+'</td>'
					      	    +'<td style="text-align:left;">'+listitemObj.TMenuItem_Nama+'</td>'
					            +'<td style="text-align:left;">'+listitemObj.TMenuItem_Link+'</td>'
					            +'<td>'+listitemObj.TMenuItem_Jenis+'</td>'
					            +'<td>'
									+'<a href="/menuitem/'+listitemObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20" title="Edit">'
									+'</a>'
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