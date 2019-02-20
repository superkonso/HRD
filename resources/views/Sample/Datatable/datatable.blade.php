@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Sample Datatable')

@section('content_header', 'Datatable')

@section('header_description', '')

@section('menu_desc', 'datatables')

@section('link_menu_desc', '/datatables')

@section('sub_menu_desc', 'datatables')

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
	        	<a href="/pasien/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Daftar Pasien Baru</a>
	        	<br><br>
	        	<div class="form-group">
	              <div class="input-group">
	                <div class="input-group-addon" style="background-color: #167F92;">
	                  <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
	                </div>
	                <input type="text" id="searchkey1" class="form-control pull-right" placeholder="Nomor RM / Nama Pasien">
	              </div>
	              <div class="input-group">
	                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>
	              </div>
	            </div>
	            <div class="divscroll" style="max-height: 400px; overflow-x: scroll; overflow-y: scroll;">
		        	<span id="tablebody1"></span>
	        	</div>
			</div> <!--div class="box-body"-->

		</div> <!--div class="box box-primary"-->
	</div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
</div> <!--div class="row"-->



<div class="row">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
		  	<div class="box-header">
          		@if(Session::has('flash_message'))
		    		<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
	        </div>
	        <div class="box-body">
	        	<div class="divscroll" style="max-height: 700px; overflow-x: scroll; overflow-y: scroll;">
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

      	refreshData();

	});

	$('#searchkey1').on('keyup', function(e){
		refreshData();
	});

	function datatable(){
		$('#example2').DataTable({
	      "paging": true,
	      "lengthChange": true,
	      "searching": false,
	      "ordering": false,
	      "info": false,
	      "autoWidth": false
	    });
	}

	function refreshData(){
    	var isiData = '';

    	var key 	= $('#searchkey1').val();

    	isiData += '<table id="example2" class="table table-bordered table-hover responstable" style="font-size:11px;">';

    	isiData += '<thead><tr>'
    					+'<th width="100px">Nomor RM</th>'
    					+'<th width="100%">Nama Pasien</th>'
    					+'<th width="125px">Tanggal Lahir</th>'
    					+'<th width="100%">Alamat</th>'
    					+'<th width="100px">Telepon</th>'
    					+'<th width="125px">Penjamin</th>'
    					+'<th width="40px">Action</th>'
    				+'</tr></thead>';

    	isiData += '<tbody>';

    	$.get('/ajax-getdatapasien?key='+key, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listpasienObj){
	    			isiData += '<tr>'
	    						+'<td>'+listpasienObj.TPasien_NomorRM+'</td>'
					            +'<td style="text-align:left;">'+listpasienObj.TPasien_Nama+'</td>'
					            +'<td>'+listpasienObj.TPasien_TglLahir+'</td>'
					            +'<td style="text-align:left;">'+listpasienObj.TPasien_Alamat+'</td>'
					            +'<td>'+listpasienObj.TPasien_Telp+'</td>'
					            +'<td>'+listpasienObj.TAdmVar_Nama+'</td>'
					            +'<td>'
									+'<a href="/pasien/'+listpasienObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20">'
									+'</a>'
								+'</td>'
					        +'</tr>';
	    		});
    			isiData += '</tbody></table>';
				document.getElementById('tablebody1').innerHTML = isiData;
				datatable();
    		}else{

    			isiData += '<tr><td colspan="11"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '</tbody><table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    			datatable();
    		}   		
    	});
	 }
</script>

@endsection