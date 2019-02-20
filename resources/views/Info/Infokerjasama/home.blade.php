@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | INFO KERJA SAMA')

@section('content_header', 'DAFTAR PERUSAHAAN')

@section('header_description', '')

@section('menu_desc', 'Kerjasama')

@section('link_menu_desc', '/infokerjasama')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

	<div class="row">
		<!-- <span id="formaction"> -->
	<form class="form-horizontal form-label-left" method="post" name="formruang" id="formkerjasama" data-parsley-validate >
	<!-- </span> -->
      	{{method_field('PUT')}} 
	      <!-- Token -->
    	{{csrf_field()}}

      <?php date_default_timezone_set("Asia/Bangkok"); ?>
	    <!-- ===================================== Data Pasien =========================================== -->
		<div class="col-md-4 col-sm-12 col-xs-12">
			<div class="box box-success">
				<div class="box-header with-border">
	              <h3 class="box-title">Daftar Kerja Sama</h3>
	            </div>
	            <!-- /.box-header -->
				<div class="box-body">
			            <div class="form-group">
			                  		<div style="height:450px;" >
		            				<span id="tablebody1">
		      <!--  <ul id="treemenu1" class="treeview">
			  <li><a href="#tab_pelayanan" data-toggle="tab">Lingkup Pelayanan</a></li>
              <li><a href="#tab_nonlayan" data-toggle="tab">Tidak Di Tanggung</a></li> -->
		            				</span>
	            			</div>
			            </div>	        
			    </div>  {{-- box Body --}}
			</div> {{-- Box Success --}}
		</div> {{--Col 6--}}

		    <!-- ===================================== Data Perusahaan=========================================== -->
		<div class="col-md-8 col-sm-12 col-xs-12">
			<div class="box box-success">
			 <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_identitas" data-toggle="tab">Identitas</a></li>
              <li><a href="#tab_pelayanan" data-toggle="tab">Lingkup Pelayanan</a></li>
              <li><a href="#tab_nonlayan" data-toggle="tab">Tidak Di Tanggung</a></li>
              <li><a href="#tab_prosedur" data-toggle="tab">Prosedur</a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane active" id="tab_identitas">
				<div class="box-body">
			            <div class="form-group">
			            	<label class="control-label col-md-2 col-sm-2 col-xs-2">Perusahaan</label>
			            	<div class="col-md-3 col-sm-3 col-xs-3">
			                  	<input type="text" name="prsh" id="prsh" class="form-control" value="" required="required" readonly>
			                </div>
			                <label class="control-label col-md-2 col-sm-2 col-xs-2">Kode</label>
			            	<div class="col-md-2 col-sm-2 col-xs-2">
			                  	<input type="text" name="kode" id="kode" class="form-control" value="" required="required" readonly>
			                </div>
			               </div>

			            <div class="form-group">
			            	<label class="control-label col-md-2 col-sm-2 col-xs-2">Alamat</label>
			            	<div class="col-md-7 col-sm-7 col-xs-7">
			                  	<input type="text" name="alamat" id="alamat" class="form-control" value=""  readonly>
			                </div>
			            </div>

			              <div class="form-group">
			            	<label class="control-label col-md-2 col-sm-2 col-xs-2">Kontak</label>
			            	<div class="col-md-7 col-sm-7 col-xs-7">
			                  	<input type="text" name="kontak" id="kontak" class="form-control" value=""  readonly>
			                </div>
			            </div>

			         <div class="form-group">
			        <label class="col-md-7 col-sm-7 col-xs-7">Catatan</label>				   
			        <textarea style="height: 200px;" name="catatan" id="catatan" class="form-control" rows="3" style="resize:none;" readonly>
			       	</textarea>
			         </div>
			         </div>
			     </div>{{-- <div class="tab-pane" id="tab_identitas"> --}}
			     
		<div class="tab-pane " id="tab_pelayanan">
              <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">

                  <div class="box box-primary">
                      <div class="box-header">
                        <h3 class="box-title">Ruang Lingkup Pelayanan</h3>
                      </div>
                      <div class="box-body">

                       <div class="form-group">			   
			        <textarea style="height: 200px;" name="catatan" id="catatan" class="form-control" rows="3" style="resize:none;" readonly>
			       	</textarea>
			         </div>
                        

                      </div> {{-- <div class="box-body"> --}}
                  </div> {{-- <div class="box box-primary"> --}}
                  
                </div>
              </div>
            </div> {{-- <div class="tab-pane" id="tab_pelayanan"> --}}

            <div class="tab-pane " id="tab_nonlayan">
              <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">

                  <div class="box box-primary">
                      <div class="box-header">
                        <h3 class="box-title">Pelayanan Tidak Ditanggung</h3>
                      </div>
                      <div class="box-body">

                       <div class="form-group">			   
			        <textarea style="height: 200px;" name="catatan" id="catatan" class="form-control" rows="3" style="resize:none;" readonly>
			       	</textarea>
			         </div>
                        

                      </div> {{-- <div class="box-body"> --}}
                  </div> {{-- <div class="box box-primary"> --}}
                  
                </div>
              </div>
            </div> {{-- <div class="tab-pane" id="tab_nonlayan"> --}}

             <div class="tab-pane " id="tab_prosedur">
              <div class="form-group">
                <div class="col-md-12 col-sm-12 col-xs-12">

                  <div class="box box-primary">
                      <div class="box-header">
                        <h3 class="box-title">Prosedur Pelayanan</h3>
                      </div>
                      <div class="box-body">

                       <div class="form-group">			   
			        <textarea style="height: 200px;" name="catatan" id="catatan" class="form-control" rows="3" style="resize:none;" readonly>
			       	</textarea>
			         </div>
                        

                      </div> {{-- <div class="box-body"> --}}
                  </div> {{-- <div class="box box-primary"> --}}
                  
                </div>
              </div>
            </div> {{-- <div class="tab-pane" id="tab_prosedur"> --}}
			          
			    </div>  {{-- box Body --}}
			    </div>
			</div> {{-- Box Success --}}
		</div> {{--Col 6--}}
	</form>

	</div>
	
	<!-- JQuery 1 -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

	<!-- Auto Complete Search Asset -->
	<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
	<script src="{{ asset('js/jquery-ui.js') }}"></script>

	<!-- Modal Searching Pasien Lama -->
	<script src="{{ asset('js/searchData.js') }}"></script>
	<script type="text/javascript">
	    var arrTrans = [];
	    var arrTransDetil = [];
	    var arrHasil = [];
	
	$( document ).ready(function() {
		$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      	});  

      	refreshData();
	});

	function refreshData(){
    	var isiData = '';
    	var key ='';

    	isiData += '<table class="table table-bordered">';

    	isiData += '<tr>'    					
    					+'<th width="30%">Kode</th>'
    					+'<th width="70%">Perusahaan</th>'
    				+'</tr>';
		$.get('/ajax-infoprsh?key='+key, function(data){
    		var i=0;
    		if(data.length > 0){
    			$.each(data, function(index, listprshLab){

    				// Isi Array
    				arrTrans.push({
			            kode: listprshLab.TPerusahaan_Kode,
			            status: listprshLab.TPerusahaan_Status,
			            prsh: listprshLab.TPerusahaan_Nama
			     	});

   	    			isiData += 
		    			'<tr onclick="InfoPrsh(\''+listprshLab.TPerusahaan_Kode+'\');">'
    					
    						+'<td id="KdPrsh'+i+'" style="text-align:left;">'+listprshLab.TPerusahaan_Kode+'</td>'
				            +'<td style="text-align:left;">'+listprshLab.TPerusahaan_Nama+'</td>'
				        +'</tr>';
		    		
					i++;
    			});

    			isiData += '</table>';
				document.getElementById('tablebody1').innerHTML = isiData;
    		}else{
    			isiData += '<tr><td colspan="2"><i>Tidak ada Data Ditemukan</i></td></tr>';
    			isiData += '<table>';
    			document.getElementById('tablebody1').innerHTML = isiData;
    		}  

    	});
    }

       function InfoPrsh(Kode){
   
    	arrTransDetil = [];

    	$.get('/ajax-infoprshdet?key='+Kode, function(data){
    		if(data.length > 0){
    			$.each(data, function(index, value){
    				arrTransDetil.push({
		              kode: value.TPerusahaan_Kode,
		              Prsh: value.TPerusahaan_Nama,
		              alamat: value.TPerusahaan_Alamat1, 
		              kontak: value.TPerusahaan_Kontak, 
		              catatan: value.TPerusahaan_Prosedur, 
		             
		            });		            

					$('#prsh').val(value.TPerusahaan_Nama);
	    			$('#kode').val(value.TPerusahaan_Kode);
	    			$('#alamat').val(value.TPerusahaan_Alamat1);
	    			$('#kontak').val(value.TPerusahaan_Kontak);	    			
	    			$('#catatan').val(value.TPerusahaan_Prosedur); 
				});
    		}    		
       	});

    }
	</script>
@endsection