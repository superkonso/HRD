@extends('layouts.main')

@section('title', 'HRD BRIDGE | Input Jabatan Karyawan ')

@section('content_header', 'Input Jabatan Karyawan')

@section('header_description', '')

@section('menu_desc', 'Jabatan Karyawan')

@section('link_menu_desc', '/datakaryawan')

@section('sub_menu_desc', 'Update')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); 
      $tglmasukawal = new DateTime($karyawans->TKaryawan_TglMasukAwal);
      $tglkeluar = new DateTime($karyawans->TKaryawan_TglKeluar);
     
?>

<form class="form-horizontal form-label-left" action="/updatekaryawancuti/{{$karyawans->id}}" method="post" id="formkaryawancuti" data-parsley-validate>

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
				          <h3 class="box-title">INPUT JABATAN KARYAWAN </h3>
				        </div>
		             	
		             	 <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">NIK</label>
			              <div class="col-md-3 col-sm-9 col-xs-9">
			                <input type="text" name="nik" id="nik" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Nomor}}" readonly>
			              </div>
			            </div>

			           <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama</label>
                          <div class="col-md-9 col-sm-5 col-xs-5">
                            <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Nama}}" required="required">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Jabatan</label>
                          <div class="col-md-3 col-sm-5 col-xs-5">
                            <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Nama}}" required="required">
                          </div>
                        </div>

                         <div class="form-group">	
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">Periode Awal</label>
			                <div class="col-md-3 col-sm-5 col-xs-5">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglberlaku" id="tglberlaku" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
			                   </div>
			            	</div>
			            	</div>

                         <div class="form-group">
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">Periode Akhir</label>
			                <div class="col-md-3 col-sm-5 col-xs-5">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglbatas" id="tglbatas" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
			                  </div>
			                </div>
			            </div>

			            <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Jabatan</label>
                            <div class="col-md-3 col-sm-3 col-xs-3">
	                          <select name="jnsjabatan" id="jnsjabatan" class="form-control">
                               <option value="">--</option>
                               <@foreach($karyawanvar as $jabatan)
                                @if ($jabatan->TKaryVar_Seri=='JABATANJNS')
                                      <option value="{{$jabatan->TKaryVar_Kode}}"  @if(!empty($karyawansjabatans->TKaryJabatan_Jenis)) @if ($jabatan->TKaryVar_Kode==$karyawans->TKaryVar_id_Profesi) selected="selected" @endif @endif>{{$jabatan->TKaryVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                        	</div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Jabatan</label>
                            <div class="col-md-3 col-sm-3 col-xs-3">
	                          <select name="jabatan" id="jabatan" class="form-control">
                               <option value="">--</option>
                               <@foreach($karyawanvar as $jabatan)
                                @if ($jabatan->TKaryVar_Seri=='JABATAN')
                                      <option value="{{$jabatan->TKaryVar_Kode}}"  @if(!empty($karyawansjabatans->TKaryJabatan_Jenis)) @if ($jabatan->TKaryVar_Kode==$karyawans->TKaryVar_id_Profesi) selected="selected" @endif @endif>{{$jabatan->TKaryVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                        </div>
		        		</div>

		        		 <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">Keterangan Jabatan </label>
			              <div class="col-md-3 col-sm-3 col-xs-3">
			                <input type="text" name="ketjbtn" id="ketjbtn" class="form-control col-md-7 col-xs-12">
			              </div>
			         </div>

			         <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Unit </label>
                            <div class="col-md-3 col-sm-9 col-xs-9">
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
                      </div>

                     <div class="form-group">	
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal SK</label>
			                <div class="col-md-3 col-sm-5 col-xs-5">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglberlaku" id="tglberlaku" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
			                   </div>
			            	</div>
			           </div>

                        <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">No. SK </label>
			              <div class="col-md-3 col-sm-3 col-xs-3">
			                <input type="text" name="nosk" id="nosk" class="form-control col-md-7 col-xs-12">
			              </div>
			         </div>

			         <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">Catatan</label>
			              <div class="col-md-3 col-sm-3 col-xs-3">
			                <input type="text" name="catatan" id="catatan" class="form-control col-md-7 col-xs-12">
			              </div>
			         </div>
				</div> 
				</div>
				</div>

				<div class="col-md-6 col-sm-6 col-xs-6">
		       	<div class="box box-primary">
		            <div class="box-body">
				        <div class="box-header">
						<h3 class="box-title">RIWAYAT JABATAN KARYAWAN </h3>
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
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="col-md-12 col-md-offset-5">
              <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Submit</button>

              <a href="/datakaryawan" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</a>

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

 	function refreshData(kd){
      var isiData = '';
      // var key1  = $('#searchkey1').val();
     
      isiData += '<table class="responstable">'
                    +'<tr>'
                      +'<th width="100px">No urut </th>'
                      +'<th width="100px">Jabatan </th>'
                      +'<th width="100px">Keterangan Jabatan </th>'
                      +'<th width="100px">Nama Unit </th>'
                      +'</tr>';
  
    $.get('/ajax-getkaryawancuti?', function(data){
     	
   		if(data.length > 0){
	         var i = 1;
	       
	        $.each(data, function(index, listpasObj){
	        	n         = i;
	            i++;

	            isiData += '<tr>'
	                      +'<td width="100px">'+listpasObj.TCuti_TglMulai+'</td>'
	                      +'<td width="100px">'+listpasObj.TCuti_TglSelesai+'</td>'
	                      +'<td width="100px">'+listpasObj.TCuti_LamaHari+'</td>'
	                      +'<td width="100px">'+listpasObj.TKaryVar_Nama+'</td>'
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
    }   //end function    
 
  	function hitungmasakerja(){
	      var nowDate = new Date();
	      var nowDate = (nowDate.getMonth()+1) + '/'+nowDate.getDate() + '/'+nowDate.getFullYear();

	      var karymasuk    	= $('#tglmasuk').val();
	      var tgllapor		= $('#tglkeluar').val();
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
			      

	      $('#masakerjathn').val(GetYears);
	      $('#masakerjabln').val(yearsmount);
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
	          
	          	var lamacuti  = $('#lamacuti').val();
			    var maxcuti   = $('#maxcuti').val();			    
			    var cuti  = data.TCuti_Jumlah;

			    if (cuti = 'null'){
			    	cuti= '0';
			    } 
	            var sisacuti2 = parseInt(cuti) + parseInt(lamacuti);
	          	cutiakhir = parseInt(maxcuti) - (parseInt(cuti) + parseInt(lamacuti));

			    $('#awalcuti').val(sisacuti2);
			    $('#sisacuti').val(cutiakhir);

	      });
		}

</script>

@endsection