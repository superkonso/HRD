@extends('layouts.main')

@section('title', 'HRD BRIDGE | Input Karyawan Keluar')

@section('content_header', 'Input Karyawan Keluar')

@section('header_description', '')

@section('menu_desc', 'Karyawan Keluar')

@section('link_menu_desc', '/datakaryawan')

@section('sub_menu_desc', 'Update')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); 
      $tglmasukawal = new DateTime($karyawans->TKaryawan_TglMasukAwal);
      $tglkeluar = new DateTime($karyawans->TKaryawan_TglKeluar);
?>

<form class="form-horizontal form-label-left" action="/updatekaryawankeluar/{{$karyawans->id}}" method="post" id="formdatakaryawan" data-parsley-validate>

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
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Nama}}" required="required">
                          </div>
                        </div>


			            <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
			              <div class="col-md-9 col-sm-9 col-xs-9">
			                <input type="text" name="alamat" id="alamat" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Alamat}}" readonly>
			              </div>
			            </div>

			            <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">Telp</label>
			              <div class="col-md-9 col-sm-9 col-xs-9">
			                <input type="text" name="telp" id="telp" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Telepon}}" readonly>
			              </div>
			            </div>

		             	<div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Unit / Profesi</label>
                            <div class="col-md-4 col-sm-9 col-xs-9">
                              <select name="divisi" id="divisi" disabled="disabled" class="form-control">
                               @foreach($units as $unit)
                                @if($unit->TUnit_Kode == $karyawans->TUnitPrs_Kode)
                                  <option value="{{$unit->TUnit_Kode}}" selected="selected">{{$unit->TUnit_Nama}}</option>
                                @else
                                  <option value="{{$unit->TUnit_Kode}}">{{$unit->TUnit_Nama}}</option>
                                @endif
                              @endforeach
                              </select>
                        </div>

		                     <div class="col-md-5 col-sm-9 col-xs-9">
                              <select name="profesi" id="profesi" class="form-control">
                              <option value="">--</option>
                              <@foreach($profs as $profesi)
                                @if ($profesi->TAdmVar_Seri=='PROFESI')
                                      <option value="{{$profesi->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryVar_id_Profesi)) @if ($profesi->TAdmVar_Kode==$karyawans->TKaryVar_id_Profesi) selected="selected" @endif @endif>{{$profesi->TAdmVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                            </div>
                          </div>
		            </div>
		        </div>
			</div>

			<div class="col-md-6 col-sm-6 col-xs-6">
			    <div class="box box-primary">
		            <div class="box-body">
				        <div class="box-header">
				          <h3 class="box-title">Input Status Karyawan Keluar</h3>
				        </div>

			        	<div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">Status</label>
			              <div class="col-md-4 col-sm-9 col-xs-9">
			                <select name="statuskeluar" id="statuskeluar" class="form-control" >
			                    <option value="">--</option>
			                	<@foreach($admvars as $jabatan)
		                        @if ($jabatan->TAdmVar_Seri=='STATUSKAR')
		                              <option value="{{$jabatan->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryVar_id_Status)) @if ($jabatan->TAdmVar_Kode==$karyawans->TKaryVar_id_Status) selected="selected" @endif @endif>{{$jabatan->TAdmVar_Nama}}</option>
		                            @endif
		                        @endforeach
		                  	</select>
			              </div>
			            </div>

			            <div class="form-group">
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Keluar</label>
			                <div class="col-md-9 col-sm-9 col-xs-9">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglkeluar" id="tglkeluar" class="form-control pull-right" value="<?php echo date_format($tglkeluar, 'm/d/Y'); ?>">
			                  </div>
			                </div>
			            </div>

			            <div class="form-group">
			              <label class="control-label col-md-3 col-sm-3 col-xs-3">Keterangan</label>
			              <div class="col-md-9 col-sm-9 col-xs-9">
			                <input type="text" name="ketkeluar" id="ketkeluar" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_KetKeluar}}">
			              </div>
			            </div>

			            <div class="form-group">
			                <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Masuk</label>
			                <div class="col-md-9 col-sm-9 col-xs-9">
			                  <div class="input-group date">
			                    <div class="input-group-addon">
			                      <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
			                    </div>
			                    <input type="text" name="tglmasuk" id="tglmasuk" class="form-control pull-right" value="<?php echo date_format($tglmasukawal, 'm/d/Y'); ?>" readonly>
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
    var today = new Date();
    var newtoday = 'm/d/Y'
      .replace('Y', today.getFullYear())
      .replace('m', today.getMonth()+1)
      .replace('d', today.getDate());

    $( document ).ready(function() {
      	$("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
            $("#successAlert").slideUp(500);
          });  

          hitungmasakerja();
    });

    $(function () {
      	$('#tglkeluar').datepicker({
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
              hitungmasakerja();
            });
        });
    });

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

	      if (yearsmount==''||'null') {
			yearsmount = '0';
			}else{
			yearsmount=yearsmount;
		   }

	      $('#masakerjathn').val(GetYears);
	      $('#masakerjabln').val(yearsmount);
	    }
</script>

@endsection