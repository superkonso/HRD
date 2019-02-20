@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | User')

@section('content_header', 'User')

@section('header_description', 'List User')

@section('menu_desc', 'User')

@section('link_menu_desc', '/user')

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
        	<a href="{{ route('register') }}" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Tambah User</a>
        	<br><br>
        	<div class="form-group">
              <div class="input-group">
                <div class="input-group-addon" style="background-color: #167F92;">
                  <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
                </div>
                <input type="text" id="searchkey" class="form-control pull-right" placeholder="Nama / Username">
              </div>
              <div class="input-group">
                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>
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
    					+'<th width="120px">Nama Depan</th>'
    					+'<th width="120px">Nama Belakang</th>'
    					+'<th width="100px">Username</th>'
    					+'<th width="200px">Email</th>'
    					+'<th width="120px">Akses</th>'
    					+'<th width="125px">Unit</th>'
    					+'<th width="120px">Tanggal Create</th>'
    					+'<th width="50px">Action</th>'
    				+'</tr>';

    	$.get('/ajax-getdatausers?key='+key, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listuserObj){
	    			isiData += '<tr>'
	    						+'<td style="text-align:left;">'+listuserObj.first_name+'</td>'
					            +'<td style="text-align:left;">'+listuserObj.last_name+'</td>'
					            +'<td style="text-align:left;">'+listuserObj.username+'</td>'
					            +'<td style="text-align:left;">'+listuserObj.email+'</td>'
					            +'<td>'+listuserObj.TAccess_Name+'</td>'
					            +'<td>'+listuserObj.TUnit_Nama+'</td>'
					            +'<td>'+listuserObj.created_at+'</td>'
					            +'<td>'
									+'<a href="/user/'+listuserObj.id+'/edit">'
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