@extends('layouts.main')

@section('title', 'HRD BRIDGE | Input Karyawan Cuti')

@section('content_header', 'Input Karyawan Cuti')

@section('header_description', '')

@section('menu_desc', 'Karyawan Cuti')

@section('link_menu_desc', '/datakaryawan')

@section('sub_menu_desc', 'Update')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); 
      $tglmasukawal = new DateTime($karyawans->TKaryawan_TglMasukAwal);
      $tglkeluar = new DateTime($karyawans->TKaryawan_TglKeluar);

     
?>

<form class="form-horizontal form-label-left" action="/updatekaryawancuti/{{$karyawans->id}}" method="post" id="formkaryawancuti" data-parsley-validate onsubmit="return checkLamaCuti()">

  {{method_field('PUT')}} 
        <!-- Token -->
  {{csrf_field()}}
  
  <div class="row font-medium">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="form-group">

           <div class="col-md-6 col-sm-6 col-xs-6">
		       	<div class="box box-primary">
		            <div class="box-body">
				        <div class="box-header">
				          <h3 class="box-title">DAFTAR CUTI TAHUNAN KARYAWAN </h3>
				        </div>
		             	
                         <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tahun Cuti </label>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="input-group date">
                           <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			               </div>
                           <select name="tahuncuti" id="tahuncuti" class="form-control col-md-7 col-xs-12">
                          <option value="">2018 </option>
                           </select>
                       		</div>
                          </div>
                        </div>

                         <div class="form-group">	
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">Berlaku</label>
			                <div class="col-md-5 col-sm-5 col-xs-5">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglberlaku" disabled="disabled" id="tglberlaku" class="form-control pull-right" >
			                   </div>
			            	</div>
			            	</div>

                            <div class="form-group">
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">Sampai Tanggal</label>
			                <div class="col-md-5 col-sm-5 col-xs-5">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglbatas" disabled="disabled" id="tglbatas" class="form-control pull-right">
			                  </div>
			                </div>
			            </div>

		                <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Maximal Cuti </label>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                            <input type="text" name="maxcuti" id="maxcuti" class="form-control col-md-7 col-xs-12" value="12" required="required">
                          </div>                   
			            </div>

			             <div class="form-group">
		                    <label class="control-label col-md-3 col-sm-3 col-xs-3">Cuti</label>
		                    <div class="col-md-2 col-sm-3 col-xs-3">
		                      <input type="text" name="awalcuti" id="awalcuti" class="form-control col-md-7 col-xs-12" readonly> Cuti Diambil
		                    </div>
		                    <div class="col-md-2 col-sm-3 col-xs-3">		   
		                        <input type="text" name="sisacuti" id="sisacuti" class="form-control col-md-7 col-xs-12" readonly> Sisa Cuti
		                    </div>
		                </div>
		        </div>
		
				<div class="box-body">				   
					<div class="form-group">
 				<div class="box-header">
				<h3 class="box-title">Riwayat Cuti </h3>
				</div>

				<div class="divscroll">
					<div style="height:140px;">
						<span id="tablebody1" >
			            </span>
					</div>			            				
			    </div>
				</div>
				</div>
				</div>
				</div>

		    <div class="col-md-6 col-sm-6 col-xs-6">
		       	<div class="box box-primary">
		            <div class="box-body">
				        <div class="box-header">
				          <h3 class="box-title">Data karyawan</h3>
				        </div>

			            <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">NIK</label>
			              <div class="col-md-4 col-sm-9 col-xs-9">
			                <input type="text" name="nik" id="nik" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Nomor}}" readonly>
			              </div>
			            </div>

			           <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama</label>
                          <div class="col-md-7 col-sm-9 col-xs-9">
                            <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Nama}}" required="required" readonly>
                          </div>
                        </div>

			            <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
			              <div class="col-md-9 col-sm-9 col-xs-9">
			                <input type="text" name="alamat" id="alamat" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Alamat}}" readonly>
			              </div>
			            </div>

		             	<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Unit / Profesi</label>
                            <div class="col-md-4 col-sm-9 col-xs-9">
                              <select name="divisi" id="divisi" class="form-control" disabled="disabled">
                               @foreach($units as $unit)
                                @if($unit->TUnit_Kode == $karyawans->TUnitPrs_id)
                                  <option value="{{$unit->TUnit_Kode}}" selected="selected">{{$unit->TUnit_Nama}}</option>
                                @else
                                  <option value="{{$unit->TUnit_Kode}}">{{$unit->TUnit_Nama}}</option>
                                @endif
                              @endforeach
                              </select>
                            </div>
                        
		                     <div class="col-md-5 col-sm-9 col-xs-9">
                              <select name="profesi" id="profesi" class="form-control" disabled="disabled">
                              <@foreach($admvars as $jabatan)
                                @if ($jabatan->TAdmVar_Seri=='PROFESI')
                                      <option value="{{$jabatan->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryVar_id_Profesi)) @if ($jabatan->TAdmVar_Kode==$karyawans->TKaryVar_id_Profesi) selected="selected" @endif @endif>{{$jabatan->TAdmVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                            </div>
                            </div>

                        <div class="form-group">	
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Masuk</label>
			                <div class="col-md-5 col-sm-5 col-xs-5">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglmasuk" id="tglmasuk" disabled="disabled" class="form-control pull-right" value="<?php echo date_format($tglmasukawal, 'm/d/Y'); ?>">
			                   </div>
			            	</div>
			            </div>

			            <div class="form-group">
			                    <label class="control-label col-md-3 col-sm-3 col-xs-3">Masa Kerja</label>
			                    <div class="col-md-2 col-sm-3 col-xs-3">
			                      <input type="text" name="masakerjathn" id="masakerjathn" class="form-control col-md-7 col-xs-12" placeholder="0" readonly> Tahun
			                    </div>
			                    <div class="col-md-2 col-sm-3 col-xs-3">
			                      <input type="text" name="masakerjabln" id="masakerjabln" class="form-control col-md-7 col-xs-12" placeholder="0" readonly> Bulan
			                    </div>
			                </div>
						</div>
		            </div>
			    </div>

			<div class="col-md-6 col-sm-6 col-xs-6">
			    <div class="box box-primary">
		            <div class="box-body">
				        <div class="box-header">
				          <h3 class="box-title">Data Baru : Pengajuan Cuti</h3>
				        </div>

				        <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">No Cuti </label>
			             <div class="col-md-4 col-sm-4 col-xs-4">
                          <input type="text" name="nocuti" id="nocuti" class="form-control col-md-7 col-xs-12" value="{{$autoNumber}}" readonly>
                        </div>     
			            </div>

				        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tahun Cuti </label>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                          <div class="input-group date">
                           <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			               </div>
                           <select name="tahuncuti2" id="tahuncuti2" class="form-control col-md-7 col-xs-12">
                          <option value="">2018 </option>
                           </select>
                       		</div>
                          </div>
                        </div>

                         <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Cuti</label>
			              <div class="col-md-4 col-sm-9 col-xs-9">
			                <select name="jeniscuti" id="jeniscuti" class="form-control" >
			                	<@foreach($karyawanvar as $jabatan)
		                        @if ($jabatan->TKaryVar_Seri=='JENISCUTI')
		                              <option value="{{$jabatan->TKaryVar_Kode}}"  @if(!empty($karyawans->TKaryVar_id_Status)) @if ($jabatan->TKaryVar_Kode==$karyawans->TKaryVar_id_Status) selected="selected" @endif @endif>{{$jabatan->TKaryVar_Nama}}</option>
		                            @endif
		                        @endforeach
		                  	</select>
			              </div>
			            </div>

			            <div class="form-group">
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Cuti</label>
			                <div class="col-md-5 col-sm-5 col-xs-5">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglcuti" id="tglcuti" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
			                  </div>
			                </div>
			            </div>

			            <div class="form-group">
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">sampai Tgl</label>
			                <div class="col-md-5 col-sm-5 col-xs-5">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglcutiakhir" id="tglcutiakhir" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
			                  </div>
			                </div>
			            </div>

			            <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Lama Cuti </label>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                            <input type="text" name="lamacuti" id="lamacuti" class="form-control col-md-7 col-xs-12" readonly>Hari
                          </div>                   
			            </div>

			              <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">Keterangan </label>
			              <div class="col-md-5 col-sm-5 col-xs-5">
			                <input type="text" name="ketcuti" id="ketcuti" class="form-control col-md-7 col-xs-12">
			              </div>
			            </div>

			        </div>
			    </div>
			</div>
	  	</div>
	</div>     

      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="col-md-12 col-md-offset-5">
              <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>

              <a href="/datakaryawan" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</a>

            </div>
          </div>
        </div>
      </div>
      </div>

  </div> {{-- <div class="row"> --}}

</form>

@include('Partials.modals.searchmodal')
@include('Partials.alertmodal')
@include('Partials.warningmodal')
@include('Partials.successmodal')
@include('Partials.alertmodal_yes')
@include('Partials.errors')

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

<!-- Auto Complete Search Asset -->
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<script src="{{ asset('js/jquery-ui.js') }}"></script>

<!-- Modal Searching Pasien Lama -->
<script src="{{ asset('js/searchData.js') }}"></script>

<!-- ============================================= End Of Content ================================================ -->

@include('Partials.errors')

<script type="text/javascript">
	var arrItem       = [];
    var today = new Date();
    var newtoday = 'm/d/Y'
      .replace('Y', today.getFullYear())
      .replace('m', today.getMonth()+1)
      .replace('d', today.getDate());

    $( document ).ready(function() {
      	$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
            $("#successAlert").slideUp(500);
          });  

      	  Cutitanggal();
      	  refreshData();
          hitungmasakerja();
          hitungmasacuti();
    });

    $(function () {
      	$('#tglkeluar').datepicker({
        	autoclose: true,
        	dateFormat: 'm/d/Y'
      	});

      	$('#tglberlaku').datepicker({
        	autoclose: true,
        	dateFormat: 'm/d/Y'
      	});

      	$('#tglbatas').datepicker({
        	autoclose: true,
        	dateFormat: 'm/d/Y'
      	});
      	
      	$('#tglcuti').datepicker({
        	autoclose: true,
        	dateFormat: 'm/d/Y'        	
      	});

      	$('#tglcutiakhir').datepicker({
        	autoclose: true,
        	dateFormat: 'm/d/Y'
      	}); 

      	$('#tglmasuk').datepicker({
        	autoclose: true,
        	dateFormat: 'm/d/Y'

      	});
	});

    $(function() {
        $("body").delegate("#tglkeluar", "focusin", function(){
            $(this).datepicker({
                autoclose: true,
                dateFormat: 'm/d/Y'
            })
            .on('changeDate', function(en) {
              refreshData();
              hitungmasakerja();
              hitungmasacuti();
            });
        });
    });

       $(function() {
        $("body").delegate("#tglcuti", "focusin", function(){
            $(this).datepicker({
                autoclose: true,
                dateFormat: 'm/d/Y'
            })
            .on('changeDate', function(en) {
              refreshData();
              hitungmasacuti();

            });
        });
    });

        $(function() {
        $("body").delegate("#tglcutiakhir", "focusin", function(){
            $(this).datepicker({
                autoclose: true,
                dateFormat: 'm/d/Y'
            })
            .on('changeDate', function(en) {
              refreshData();
              hitungmasacuti();

            });
        });
    });

     function checkLamaCuti(){
      
      var lama  = $('#lamacuti').val();
      var awalcuti2  = $('#awalcuti').val();

      if(lama == '0'){
        showWarning(2000, '', 'Silahkan pilih Batas Tanggal Cuti', true);
        return false;
      }else if(awalcuti2 < '13'){
      	showWarning(2000, '', 'Maaf, Cuti Anda Sudah Habis', true);
      	return false;
      }else{
        return true;
      }

    }

     	function Cutitanggal(){
 		 var nowDate = new Date();
 	
	      var tglmasaawal  = '01/01/'+nowDate.getFullYear();
	      var tglmasaakhir = '31/12/'+nowDate.getFullYear();     

	      $('#tglberlaku').val(tglmasaawal);
	      $('#tglbatas').val(tglmasaakhir);

	      $.get('/ajax-getlamacuti?key1='+nik, function(data){
	        $('#awalcuti').val(data);
			
		 });   	  

 	}

 	function refreshData(kd){
      var isiData = '';
      // var key1  = $('#searchkey1').val();
     
      isiData += '<table class="responstable">'
                    +'<tr>'
                      +'<th width="100px">Tanggal </th>'
                      +'<th width="100px">Sampai Tanggal </th>'
                      +'<th width="100px">Lama </th>'
                      +'<th width="100px">Jenis </th>'
                      +'</tr>';
  	
  	 	var nik = $('#nik').val();
	     
	      $.get('/ajax-getkaryawancuti?key1='+nik, function(data){
     	
   		if(data.length > 0){
	         var i = 1;
	       
	        $.each(data, function(index, listpasObj){
	        	n         = i;
	            i++;

	            isiData += '<tr>'
	                      +'<td width="100px">'+listpasObj.TCuti_TglMulai+'</td>'
	                      +'<td width="100px">'+listpasObj.TCuti_TglSelesai+'</td>'
	                      +'<td width="100px">'+listpasObj.TCuti_LamaHari+'</td>'
	                      +'<td width="100px">'+listpasObj.TAdmVar_Nama+'</td>'
	                      +'</tr>';
	                  
	          });
	        isiData += '</tr>';
	       document.getElementById('tablebody1').innerHTML = isiData;
	     
	        }else{

	          isiData += '<tr><td colspan="4"><i>Tidak ada Data Ditemukan</i></td></tr>';
	          isiData += '<table>';
	          document.getElementById('tablebody1').innerHTML = isiData;

        } // endif data.length 
   		});

	    hitungmasacuti();
    }   //end function    
 
  	function hitungmasakerja(){
	      var nowDate = new Date();
	      var nowDate = (nowDate.getMonth()+1) + '/'+nowDate.getDate() + '/'+nowDate.getFullYear();

	      var karymasuk    	= $('#tglmasuk').val();
	      var tgllapor		= nowDate;
	      karymasuk   		= (karymasuk == '' ? nowDate : new Date(karymasuk));
	      tgllapor    		= (tgllapor == '' ? nowDate : new Date(tgllapor));    

	      var timeDiff = Math.abs(tgllapor.getTime() - karymasuk.getTime());
	      var mountDiff = Math.abs(tgllapor.getMonth() - karymasuk.getMonth());
   			

	      var GetDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
	      var GetWeek   = parseInt(GetDays / 7);
	      var Getmount   = parseInt(GetDays / 30.4167);
	      var GetYears   = parseInt(GetDays / 365); 
	      
	      if (tgllapor.getMonth() < karymasuk.getMonth()) {
	        var yearsmount  = Math.abs(mountDiff % GetWeek - 12);	       
	      }else{
	        var yearsmount  = Math.abs(mountDiff % GetWeek);	        
	      }
			
		if (yearsmount==''||'null') {
			yearsmount = '0';
		}else{
			yearsmount=yearsmount;
		}

	      $('#masakerjathn').val(GetYears);
	      $('#masakerjabln').val(yearsmount);
	      hitungmasacuti();

	    }

	    	function hitungmasacuti(){

	      var nowDate = new Date();
	      var nowDate = (nowDate.getMonth()+1) + '/'+nowDate.getDate() + '/'+nowDate.getFullYear();

	      var karymasuk    	= $('#tglcuti').val();
	      var tgllapor		= $('#tglcutiakhir').val();
	      
	      karymasuk   		= (karymasuk == '' ? nowDate : new Date(karymasuk));
	      tgllapor    		= (tgllapor == '' ? nowDate : new Date(tgllapor));

	      var timeDiff = Math.abs(tgllapor.getTime() - karymasuk.getTime());
	      var mountDiff = Math.abs(tgllapor.getMonth() - karymasuk.getMonth());

	      var GetDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
	      var GetWeek   = parseInt(GetDays / 7);
	      var Getmount   = parseInt(GetDays / 30.4167); 

	      var GetYears   = parseInt(GetDays / 365); 

	      if (tgllapor.getMonth() < karymasuk.getMonth()) {
	        var yearsmount  = Math.abs(mountDiff % GetWeek - 12);
	      }else{
	        var yearsmount  = Math.abs(mountDiff % GetWeek);
	      }

	      $('#lamacuti').val(GetDays);

	      var nik = $('#nik').val();
	      
	      $.get('/ajax-getlamacuti?key1='+nik, function(data){
	        
	           	var cuti      = data;  
	           	var lamacuti  = $('#lamacuti').val();
				var maxcuti   = $('#maxcuti').val();		

		        var sisacuti2 = parseInt(cuti) + parseInt(lamacuti);
		        cutiakhir 	  = parseInt(maxcuti) - (sisacuti2);
	          	
		        console.log(lamacuti);
		        console.log(maxcuti);

				$('#awalcuti').val(sisacuti2);
			    $('#sisacuti').val(cutiakhir);
		 });   	    
		}

</script>

@endsection