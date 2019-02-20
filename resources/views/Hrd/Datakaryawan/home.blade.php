@extends('layouts.main')

@section('title', 'HRD BRIDGE | Data Karyawan')

@section('content_header', 'Data Karyawan')

@section('header_description', '')

@section('menu_desc', 'data karyawan')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">
<form class="form-horizontal form-label-left" action="/datakaryawan" method="post" id="formrad" data-parsley-validate onsubmit="return checkFormrad()">
	<div class="form-group col-md-12 col-sm-12 col-xs-12">
		<div class="box box-primary">
			<div class="box-header">
	          	<h3 class="box-title">Daftar Kayawan</h3>
	          	@if(Session::has('flash_message'))
			    	<div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
				@endif
	        </div>

	        <div class="box-body">
	        	<a href="datakaryawan/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Tambah Karyawan Baru</a>
	        	<br><br>

	        	<div class="col-md-4 col-sm-12 col-xs-12" style="border-right: 1px solid #DDD; border-left: 1px solid #DDD;">
		            <div class="form-group">
		            	<label class="control-label col-md-2 col-sm-2 col-xs-2">Divisi</label>
		            	<div class="col-md-10 col-sm-10 col-xs-10">
		            		<select name="unit" id="unit" class="form-control col-md-7 col-xs-12" readonly>
		            		<option value="">Semua Divisi</option>
			                <@foreach($unitprs as $unitprs)
                               <option value="{{$unitprs->TUnitPrs_Kode}}">{{$unitprs->TUnitPrs_Nama}}</option>
                            @endforeach
			              	</select>
		            	</div>
		            </div>
		        </div>

		        <div class="col-md-4 col-sm-12 col-xs-12" style="border-right: 1px solid #DDD; border-left: 1px solid #DDD;">
		             <div class="form-group">
		            	<label class="control-label col-md-2 col-sm-2 col-xs-2">Status</label>
		            	<div class="col-md-10 col-sm-10 col-xs-10">		           	
		            		<select name="statuskaryawan" id="statuskaryawan" class="form-control col-md-7 col-xs-12" readonly>
		            		  <option value="0">Semua Karyawan </option>
		            		  <option value="1">Aktif </option>
		            		  <option value="2">Pensiun </option>
		            		  <option value="3">Keluar </option>
			              	</select>
		            	</div>
		            </div>
		        </div>

		        <div class="col-md-4 col-sm-12 col-xs-12" style="border-right: 1px solid #DDD; border-left: 1px solid #DDD;">
		             <div class="form-group" style="padding-left: 20px;">
		            	<div class="col-md-1 col-sm-1 col-xs-1" style="width: 33px; height: 33px; padding: 6px; text-align: center; border-radius: 7px; background-color: #167F92;">
			                  <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
			            </div>
		            	<div class="col-md-7 col-sm-7 col-xs-7">
			                <input type="text" id="searchkey1" class="form-control pull-right" placeholder="NIK / Nama Karyawan">
		            	</div>
			            <div class="col-md-3 col-sm-3 col-xs-3">
			                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>
			            </div>
		            </div>
		        </div>
			</div> <!--div class="box-body"-->


			<div class="box-body">
	            <div class="divscroll" style="max-height: 400px;">
		        	<span id="tablebody1"></span>
	        	</div>
	        </div>


          {{-- <div class="box-body" style="text-align: right;">
            <div class="col-md-12">
              <button id="simpan" name="simpan" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20">Kary keluar</button>
              <a onclick="showList();" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20"> Cuti</a>
              <a onclick="showList();" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20"> Jabatan</a>
              <a onclick="showList();" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20"> Pelatihan</a>
              <a onclick="showList();" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20"> Prestasi</a>
              <a onclick="showList();" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20"> Sangsi</a>

            </div>
          </div> --}}
	    </div>
	</div>
</form>
</div>

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
	var today = new Date();

    var newtoday = 'm/d/Y'
      .replace('Y', today.getFullYear())
      .replace('m', today.getMonth()+1)
      .replace('d', today.getDate());

	$( document ).ready(function() {
		$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      	});  

      	refreshData();
	});

	$('#searchkey1').on('keyup', function(e){
		refreshData();
	});

	$('#unit').on('change', function(e){
		refreshData();
	});

	$('#statuskaryawan').on('change', function(e){
		refreshData();
	});

	function deleteConfirm()
	{
	    var confrm = confirm("Hapus Data?");
	    if (confrm){
	        return true;
	    } 
	    else{
	        return false;
	    }
	}

	function refreshData(){
    	var isiData = '';
    	var key1 	= $('#searchkey1').val();
    	var divisi 	= $('#unit').val();
    	var statuskary 	= $('#statuskaryawan').val();
        
    	isiData += '<table class="responstable">';

    	isiData += '<tr>'
    					+'<th width="10%">NIK</th>'
    					+'<th width="20%">Nama Karyawan</th>'
    					+'<th width="15%">Tgl masuk</th>'
    					+'<th width="15%">Masa Kerja</th>'
    					+'<th width="5%">Edit</th>'
    					+'<th width="5%">Keluar</th>'
    					+'<th width="5%">Cuti</th>'
    					+'<th width="5%">Jabatan</th>'
    					+'<th width="6%">Peralihan</th>'
    					+'<th width="5%">Prestasi</th>'
    					+'<th width="5%">Sangsi</th>'
    				+'</tr>';

    	$.get('/ajax-getdatakaryawan?key1='+key1+'&key2='+divisi+'&key3='+statuskary, function(data){
    		if(data.length > 0){
    			$.each(data, function(index, listkaryawanObj){
    							   
				    if ((listkaryawanObj.TKaryVar_id_Status != '98') && (listkaryawanObj.TKaryVar_id_Status != '99')){
					    var nowDate = new Date();
	      				var nowDate = (nowDate.getMonth()+1) + '/'+nowDate.getDate() + '/'+nowDate.getFullYear();

		    	        var karymasuk    = listkaryawanObj.TKaryawan_TglMasukAwal;
				      	var tgllapor= newtoday; 
				      	karymasuk   = (karymasuk == '' ? nowDate : new Date(karymasuk));
				      	tgllapor    = (tgllapor == '' ? nowDate : new Date(tgllapor));

				    	var timeDiff = Math.abs(tgllapor.getTime() - karymasuk.getTime());
				    	var mountDiff = Math.abs(tgllapor.getMonth() - karymasuk.getMonth());
				    	var hitmountdiff    = (mountDiff == 0 ? 12 : new Date(mountDiff));

        				var GetDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
				        var GetWeek   = parseInt(GetDays / 7);
				        var Getmount   = parseInt(GetDays / 30.4167); 

				        if (tgllapor.getMonth() < karymasuk.getMonth()) {
				        var yearsmount  = Math.abs(mountDiff % GetWeek - 12);
				      }else{
				        var yearsmount  = Math.abs(mountDiff % GetWeek);
				      }

				        var GetYears   = parseInt(GetDays / 365); 
				        var yearsmount  = Math.abs(yearsmount);
    				}

    				if (yearsmount==''||'null') {
						yearsmount = '0';
					}else{
						yearsmount=yearsmount;
					}
		
    				if (GetYears != null) {
    					var masakerja = GetYears + " Tahun " + yearsmount + " Bulan";
    				}else{
    					if (listkaryawanObj.TKaryVar_id_Status == '98') {
    						masakerja = "Pensiun";
    					}else{
    						masakerja = "Keluar";
    					}
    				}

	    			isiData += '<tr>'
	    						+'<td>'+listkaryawanObj.TKaryawan_Nomor+'</td>'
					            +'<td style="text-align:left;">'+listkaryawanObj.TKaryawan_Nama+'</td>'
					            +'<td>'+listkaryawanObj.TKaryawan_TglMasukAwal+'</td>'
					            +'<td style="text-align:left;">'+masakerja+'</td>'
					            +'<td>'
									+'<a href="/datakaryawan/'+listkaryawanObj.TKaryawan_Nomor+'/edit">'
										+'<img src="{!! asset('images/icon/edit2-icon.png') !!}" width="20" height="20">'
									+'</a>'
									//  +'<a href="/datakaryawan/'+listkaryawanObj.karynomor+'">'
								
									// +'<img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20">'
									// +'</a>'
									
								+'</td>'
								+'<td>'
									+'<a href="/karyawankeluar/'+listkaryawanObj.TKaryawan_Nomor+'">'
										+'<img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20">'
									+'</a>'
								+'</td>'
								+'<td>'
									+'<a href="/karyawancuti/'+listkaryawanObj.TKaryawan_Nomor+'">'
										+'<img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20">'
									+'</a>'
								+'</td>'
								+'<td>'
								+'<a href="/karyawanjabatan/'+listkaryawanObj.TKaryawan_Nomor+'"">'
										+'<img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20">'
									+'</a>'
								+'</td>'
								+'<td>'
									+'<a href="/datakaryawan/'+listkaryawanObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20">'
									+'</a>'
								+'</td>'
								+'<td>'
									+'<a href="/datakaryawan/'+listkaryawanObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20">'
									+'</a>'
								+'</td>'
								+'<td>'
									+'<a href="/datakaryawan/'+listkaryawanObj.id+'/edit">'
										+'<img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20">'
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