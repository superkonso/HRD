@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Tarif Rawat Jalan')

@section('content_header', 'Tarif Rawat Jalan')

@section('header_description', '')

@section('menu_desc', 'Tarif')

@section('link_menu_desc', '/tarifjalan')

@section('sub_menu_desc', 'Daftar')

@section('content')

@include('Partials.message')

<div class="row">
	<form action="/ctktarifjalan" method="post" data-parsley-validate >

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
		        	<a href="/tarifjalan/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Tambah Tarif Baru</a>
		        	<br><br>
		        	@endif
		        	<div class="form-group">
		              <div class="input-group">
		                <div class="input-group-addon" style="background-color: #167F92;">
		                  <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
		                </div>
		                <input type="text" name="searchkey1" id="searchkey1" class="form-control pull-right" placeholder="Kode Tarif / Nama Tarif">
		              </div>
		              <div class="input-group">
		                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button> &nbsp;
		                <button type="submit" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</button>
		                &nbsp;<label><input type="checkbox" value="1" name="searchkey2" onchange="refreshData();" id="searchkey2"> Tampilkan Semua Data Tarif</label> 
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

	
 	$('#searchkey2').on('click', function(e){  
    	refreshData();
    });
	
	function refreshData(){
    	var isiData = '';

    	var key 	= $('#searchkey1').val();
    	var key2 	= '';

    	if (document.getElementById("searchkey2").checked) {
    		key2 = 'ALL';
    	} 
    	else { 
    		key2 = 'A';
    	}

    	isiData += '<table class="responstable">';

    	isiData += '<tr>'
    					+'<th width="100px">Kode Tarif</th>'
    					+'<th width="200px">Nama Tarif</th>'
    					+'<th width="110px">Tarif Dokter (FT)</th>'
    					+'<th width="110px">Tarif RS</th>'
    					+'<th width="110px">Tarif Jalan</th>'
    					+'<th width="75px">Action</th>'
    				+'</tr>';

    	$.get('/ajax-tarifjalanmaster?kuncicari='+key+'&status='+key2, function(data){

    		if(data.length > 0){
    			$.each(data, function(index, listTarifjalanObj){
	    			isiData += '<tr>'
	    						+'<td>'+listTarifjalanObj.TTarifJalan_Kode+'</td>'
					            +'<td style="text-align:left;">'+listTarifjalanObj.TTarifJalan_Nama+'</td>'
					      	    +'<td style="text-align:right;">'+formatRibuan(listTarifjalanObj.TTarifJalan_DokterFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifjalanObj.TTarifJalan_RSFT)+'</td>'
					            +'<td style="text-align:right;">'+formatRibuan(listTarifjalanObj.TTarifJalan_Jalan)+'</td>'
					            +'<td>'+(vonly === '0' ? '<a href="/tarifjalan/'+listTarifjalanObj.id+'/edit">'+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20" title="Edit">'
									+'</a>&nbsp;' : '')	
									// +'<img src="{!! asset('images/icon/delete-icon.png') !!}" width="20" height="20" title="Hapus" onclick="deleteItem('+listTarifjalanObj.id+');">'
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

    function deleteItem(id){

	    swal({
          title     : '',
          text 		: 'Yakin Hapus Data Tarif Rawat Jalan?',
          icon      : "warning",
          buttons   : true,
          dangerMode: true
        })
        .then((willDelete) => {
          if (willDelete) {

            event.preventDefault();

	        $.ajaxSetup({
	          headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content') 
	          }
	        });

	        $.ajax({
	          type: 'DELETE',
	          url: '/tarifjalan/'+id,
	          success: function(result){
	      		swal("Belum ada proses Hapus untuk saat ini, Hanya untuk contoh!", {
	              icon: "success",
	            });
	          }
	        });

	        event.preventDefault();

          } else {
            return false;
          }
        });



    }

</script>

@endsection