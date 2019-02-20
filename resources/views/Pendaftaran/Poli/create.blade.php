@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Poli')

@section('content_header', 'Create Pendaftaran Poli')

@section('header_description', '')

@section('menu_desc', 'Poli')

@section('link_menu_desc', '/poli')

@section('sub_menu_desc', 'Create')

@section('content')
  @include('Partials.message')

  <?php
    $JLB = 0;
    $JLL = 0;
    $CDB = 0;

    if(!empty($tarifvars)){

      foreach ($tarifvars as $tarif) {

        if($tarif->TTarifVar_Seri == 'GENERAL' && $tarif->TTarifVar_Kode == 'JLB'){
          $JLB = $tarif->TTarifVar_Nilai;
        }elseif($tarif->TTarifVar_Seri == 'GENERAL' && $tarif->TTarifVar_Kode == 'JLL'){
          $JLL = $tarif->TTarifVar_Nilai;
        }elseif($tarif->TTarifVar_Seri == 'GENERAL' && $tarif->TTarifVar_Kode == 'CDB'){
          $CDB = $tarif->TTarifVar_Nilai;
        }
      }
    }
  ?>

  <!-- ============================================== Content ================================================== -->


    <form class="form-horizontal form-label-left" action="/poli" method="post" id="formpoli" data-parsley-validate onsubmit="return checkFormPoli();">

      <!-- Token -->
      {{csrf_field()}}

      {{ Form::hidden('transID', '0', array('id' => 'transID')) }}
      {{ Form::hidden('pasbaru', 'L', array('id' => 'pasbaru')) }}
      {{ Form::hidden('tempNoRM', $tempNoRM, array('id' => 'tempNoRM')) }}
      {{ Form::hidden('tempNoTrans', '', array('id' => 'tempNoTrans')) }}
      {{ Form::hidden('temptStatus', 'C', array('id' => 'temptStatus')) }}
      {{ Form::hidden('tempUnit', '', array('id' => 'tempUnit')) }}
      {{ Form::hidden('tempNoUrut', '', array('id' => 'tempNoUrut')) }}
      {{ Form::hidden('tempjnspas', '', array('id' => 'tempjnspas')) }}

    <!-- ===================================== Data Pasien =========================================== -->
  <div class="row font-medium">
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Data Pasien</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor Trans</label>
              <div class="col-md-6 col-sm-6 col-xs-6">
                <input type="text" name="notrans" id="notrans" class="form-control col-md-7 col-xs-12" value="{{$autoNumber}}" readonly>
              </div>
              <div class="col-md-3 col-sm-3 col-xs-3">
                <input type="text" name="noantri" id="noantri" class="form-control col-md-7 col-xs-12" value="UM-01" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <a href="#formsearch" id="caripasienlama" onclick="cariPasienLama();" class="btn btn-primary" data-toggle="modal"><img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Pasien Lama</a>
              <a href="/pasienp/create" class="btn btn-success"><img src="{!! asset('images/icon/pasien-baru-icon.png') !!}" width="20" height="20"> Pasien Baru</a>

               <a href="#formsearch" id="caripasienapp" onclick="cariPasienApp();" class="btn btn-primary" data-toggle="modal"><img src="{!! asset('images/icon/pasien-appointment-icon.png') !!}" width="20" height="20"> Pasien Appointment</a>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor RM</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="pasiennorm" id="pasiennorm" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama Pasien</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>
        
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Panggilan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="panggilan" id="panggilan" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Kelamin</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="jk" id="jk" class="form-control col-md-7 col-xs-12" value="Laki-laki" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Umur</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                  <div class="col-md-4 col-sm-4 col-xs-12" style="padding-left: 0px;">
                    <input type="text" name="pasienumurthn" id="pasienumurthn" class="form-control col-md-7 col-xs-12" value="0" readonly> Tahun
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12" style="padding-left: 0px;">
                    <input type="text" name="pasienumurbln" id="pasienumurbln" class="form-control col-md-7 col-xs-12" value="0" readonly> Bulan
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12" style="padding-left: 0px;">
                    <input type="text" name="pasienumurhari" id="pasienumurhari" class="form-control col-md-7 col-xs-12" value="0" readonly> Hari
                  </div>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Provinsi</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="provinsi" id="provinsi" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kota</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kota" id="kota" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kecamatan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kecamatan" id="kecamatan" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kelurahan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kelurahan" id="kelurahan" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <textarea name="alamat" id="alamat" class="form-control" rows="3" style="resize:none;" readonly>@yield('keterangan')</textarea>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Telepon</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="telepon" id="telepon" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Pasien</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="jenispas" id="jenispas" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Agama</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="agama" id="agama" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Pendidikan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="pendidikan" id="pendidikan" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Pekerjaan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="pekerjaan" id="pekerjaan" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>
            
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Keluarga</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="keluarga" id="keluarga" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-12 col-sm-12 col-xs-12">
              <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#editPasienModal" onclick="fillPasienModal();"><img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Edit Pasien</button>
              </div>
            </div>

        </div>
      </div>
    </div>

    <!-- ====================================== End Data Pasien ==================================== -->

    <!-- ====================================== Klinik dan Dokter ================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Klinik dan Dokter</h3>
        </div>
        <div class="box-body">
          <br />
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Klinik Dituju</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="unit" id="unit" class="form-control col-md-7 col-xs-12">

                  @foreach($units as $unit)
                    <option value="{{$unit->TUnit_Kode}}">{{$unit->TUnit_Nama}}</option>
                  @endforeach
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Dokter</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="dokter" id="dokter" class="form-control col-md-7 col-xs-12">

                  @foreach($pelakus as $dokter)
                    <option value="{{$dokter->TPelaku_Kode}}">{{$dokter->TPelaku_NamaLengkap}}</option>
                  @endforeach
                 
                </select>
              </div>
            </div>
          </div>
      </div>
    </div>

    <!-- ======================================= End Klinik dan Dokter ========================================== -->

    <!-- ============================================ Rujukan =================================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Rujukan</h3>
        </div>
        <div class="box-body">
          <br />
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Rujukan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="rujukan" id="rujukan" class="form-control col-md-7 col-xs-12">

                    @foreach($admvars as $rujukan)
                      @if ($rujukan->TAdmVar_Seri=='MASUKCARA')
                        <option value="{{$rujukan->TAdmVar_Kode}}">{{$rujukan->TAdmVar_Nama}}</option>
                      @endif
                    @endforeach
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Keterangan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="ketrujukan" id="ketrujukan" class="form-control col-md-7 col-xs-12" value="">
              </div>
            </div>
          </div>
      </div>
    </div>
    <!-- ========================================= End Rujukan ================================================== -->

    <!-- ======================================= Biaya Pemeriksaan ============================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-danger">
        <div class="box-header">
          <h3 class="box-title">Biaya Pemeriksaan</h3>
        </div>

        <div class="box-body">
          <br />
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Penjamin</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="penjamin" id="penjamin" class="form-control col-md-7 col-xs-12">

                  @foreach($prsh as $penjamin)
                      <option value="{{$penjamin->TPerusahaan_Kode}}">{{$penjamin->TPerusahaan_Nama}}</option>
                  @endforeach
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Ditanggung</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="ditanggung" id="ditanggung" class="form-control col-md-7 col-xs-12">

                  @foreach($admvars as $tanggung)
                    @if ($tanggung->TAdmVar_Seri=='DAFTJENIS')
                      <option value="{{$tanggung->TAdmVar_Kode}}">{{$tanggung->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Pendaftaran</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <input type="radio" name="daftar" id="dftBaru" value="B" checked="checked"> Baru 
                  <input type="radio" name="daftar" id="dftLama" value="L"> Lama
                </div>
                <div class="col-md-7 col-sm-7 col-xs-12" style="padding-left: 0px; padding-right: 0px;">
                  <input type="text" name="biayadft" id="biayadft" class="form-control col-md-7 col-xs-12" readonly>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Biaya Kartu</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <input type="checkbox" name="kartu" id="kartu"> Kartu Pasien 
                </div>
                <div class="col-md-7 col-sm-7 col-xs-12" style="padding-left: 0px; padding-right: 0px;">
                  <input type="text" name="krtpasien" id="krtpasien" class="form-control col-md-7 col-xs-12" value="0" readonly>
                </div>
              </div>
            </div>
        </div>
      </div>
    </div>
    <!-- ============================================ End Biaya Pemeriksaan ======================================= -->

    <!-- ========================================== Jumlah Biaya Pemeriksaan ====================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-danger">
        <div class="box-header">
          <h3 class="box-title">Jumlah Biaya Pemeriksaan</h3>
        </div>
        <div class="box-body">
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-3">Ditanggung</label>
                <div class="col-md-9 col-sm-9 col-xs-9">
                  <input type="text" name="jmlditanggung" id="jmlditanggung" class="form-control col-md-7 col-xs-12" value="0" readonly>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-3">Bayar Sendiri</label>
                <div class="col-md-9 col-sm-9 col-xs-9">
                  <input type="text" name="jmlpribadi" id="jmlpribadi" class="form-control col-md-7 col-xs-12" value="0" readonly>
                </div>
              </div>
        </div>
      </div>
    </div>
    <!-- ======================================== End Jumlah Biaya Pemeriksaan ==================================== -->
    </div> <!-- <div class="row"> -->

    <!-- ================== -->
    <div class="row font-medium">
      <div class="form-group col-md-12 col-sm-12 col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="col-md-12 col-md-offset-5">
              <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
              <a href="/poli/show" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20"> Edit</a>
              <a href="" class="btn btn-danger" onclick="resetForm();"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Bersihkan Form</a>
            </div>
            <div class="col-md-12 col-md-offset-5">
              
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- ================== -->

    </form>

  <div id="searchFrm">
    <span></span>
  </div>

  @include('Partials.modals.editpasien')

  @include('Partials.modals.searchmodal')

  @include('Partials.alertmodal')

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
  
    $(function () {
        $('#mtgllahir').datepicker({
          autoclose: true,
          dateFormat: 'm/d/Y'
        });
    });

    function checkFormPoli(){
        var noRM        = $('#pasiennorm').val();
        var namaPasien  = $('#nama').val();

        if(noRM == ''){
          showWarning(2000, '', 'Pasien NomorRM masih Kosong!', true);
          $('#pasiennorm').focus();
          return false;
        }else if(namaPasien == ''){
          showWarning(2000, '', 'Nama Pasien masih Kosong!', true);
          $('#pasiennorm').focus();
          return false;
        }else{
          return true;
        }
      }

    function cariPasienLama(){

      document.getElementById('searchmodal_Title').innerHTML = 'DATA PASIEN';

      document.getElementById('searchmodal_Logo').innerHTML = '<img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20">';

      document.getElementById('searchmodal_Textsearch').innerHTML = '<input type="text" id="searchkey1" class="form-control pull-right" onkeyup="cdatapasienKU(this.value)" placeholder="Nomor RM / Nama Pasien">';

      document.getElementById('searchmodal_Btnpilih').innerHTML = '<button type="button" class="btn btn-success" data-dismiss="modal" onclick="pilihPasien()"><img src="{!! asset('images/icon/checklist-icon.png') !!}" width="20" height="20"> Pilih</button>';

      cPasienLama();
    }

     function cariPasienApp(){

      document.getElementById('searchmodal_Title').innerHTML = 'DATA PASIEN APPOINTMENT';

      document.getElementById('searchmodal_Logo').innerHTML = '<img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20">';

      document.getElementById('searchmodal_Textsearch').innerHTML = '<input type="text" id="searchkey2" class="form-control pull-right" onkeyup="cdatapasienKUApp(this.value)" placeholder="Nomor RM / Nama Pasien">';

      document.getElementById('searchmodal_Btnpilih').innerHTML = '<button type="button" class="btn btn-success" data-dismiss="modal" onclick="pilihPasienApp()"><img src="{!! asset('images/icon/checklist-icon.png') !!}" width="20" height="20"> Pilih</button>';

      cPasienApp();
    }


    // ---------------------------- Ready Function ---------------------------------------
    $( document ).ready(function() {

      $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      });  

      var JLB = 0;
      var JLL = 0;
      var CDB = 0;

      JLB = <?php echo $JLB; ?>;
      JLL = <?php echo $JLL; ?>;
      CDB = <?php echo $CDB; ?>;

      JLB = formatRibuan(parseInt(JLB));
      JLL = formatRibuan(parseInt(JLL));
      CDB = formatRibuan(parseInt(CDB));

      if ($('#tempNoRM').val()!='') {
        $('#pasiennorm').val($('#tempNoRM').val());
        fillPasien();
      }

      // ============================================= Edit Pasien ========================================

      $("#frmPasienModal").submit(function (e) {

        event.preventDefault();

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content') 
          }
        });

        var pasienID        = $('#mpasien_id').val();
        var pasienNoRM      = $('#mnomorrm').val();
        var pasienNama      = $('#mnama').val();
        var pasienPanggilan = $('#mpanggilan').val();
        var pasienJK        = $('#mjk').val();
        var pasienTglLahir  = $('#mtgllahir').val();

        var prov            = $('#mprovinsi').val();
        var kota            = $('#mkota').val();
        var kec             = $('#mkecamatan').val();
        var kel             = $('#mkelurahan').val();

        var pasienAlamat    = $('#malamat').val();
        var pasienTelp      = $('#mtelepon').val();
        var pasienHP        = $('#mHP').val();
        var pasienJenis     = $('#mjenispasien').val();
        var pasienAgama     = $('#magama').val();
        var pasienPend      = $('#mpendidikan').val();
        var pasienPek       = $('#mpekerjaan').val();
        var pasienKel       = $('#mkeluarga').val();

        $.ajax({
          type  : 'POST',
          url   : 'editpasien',
          data  : {
            'pasienID'        : pasienID,
            'pasienNoRM'      : pasienNoRM,
            'pasienNama'      : pasienNama,
            'pasienPanggilan' : pasienPanggilan,
            'pasienJK'        : pasienJK,
            'pasienTglLahir'  : pasienTglLahir,
            'prov'            : prov,
            'kota'            : kota,
            'kec'             : kec,
            'kel'             : kel,
            'pasienAlamat'    : pasienAlamat,
            'pasienTelp'      : pasienTelp,
            'pasienHP'        : pasienHP,
            'pasienJenis'     : pasienJenis,
            'pasienAgama'     : pasienAgama,
            'pasienPend'      : pasienPend,
            'pasienPek'       : pasienPek,
            'pasienKel'       : pasienKel,
          },
          success: function(result){

          }
        });

        event.preventDefault();
        $('#editPasienModal').modal('hide');

        fillPasien();
      });


      // =========================================== End Edit Pasien =====================================

      var kdUnit = $('#unit').val();

      $.get('/ajax-getautonumbertrans?unit_kode='+kdUnit, function(data){
        $('#noantri').val(data);
      });

      $('#ditanggung').val('S');

      $('#biayadft').val(JLB);

      $('#kartu').on('change', function(e){
 
        if($("#kartu").prop('checked') == true){
            $('#krtpasien').val(CDB);
        }else{
            $('#krtpasien').val(0);
        }

        hitungBiaya();

      });

      $('#dftBaru').on('change', function(e){
        $('#biayadft').val(JLB);
        hitungBiaya();
      });

      $('#dftLama').on('change', function(e){
        $('#biayadft').val(JLL);
        hitungBiaya();
      });

      hitungBiaya();

    });

    // ---------------------------------------------------------------------------------

    // ============================= Auto Complete Search by Nomor RM ==================
    $( "#pasiennorm" ).autocomplete({
        source: '{!!URL::route('autocompletepasienbynorm')!!}',
        minLength: 1,
        autoFocus:true,
        select: function(e, ui) {
          //alert(ui);
      }
    });

    $('#pasiennorm').on('change', function(e){
      fillPasien();
    });

    function fillPasien(){
      var pasiennorm  = $('#pasiennorm').val();
      var notrans     = $('#notrans').val();

      // check transaksi pasien untuk poli
      $.get('/ajax-checkpasienjalan?norm='+pasiennorm+'&notrans='+notrans, function(data){
        if(data > 0){
          showWarning(2000, '', 'Pasien sudah terdaftar pada Transaksi Rawat Jalan !', true);
          $('#pasiennorm').val('');
        }else{

          $.get('/ajax-pasienbynorm?pasiennorm='+pasiennorm, function(data){
            $.each(data, function(index, pasienObj){
              var tgl       = new Date(pasienObj.TPasien_TglLahir);
              var tgllahir  = (tgl.getMonth()+1)+'/'+tgl.getDate()+'/'+tgl.getFullYear();

              document.getElementById('nama').value = pasienObj.TPasien_Nama;
              document.getElementById('panggilan').value = pasienObj.TPasien_Panggilan;
              document.getElementById('alamat').value = pasienObj.TPasien_Alamat;

              document.getElementById('kelurahan').value = pasienObj.Kelurahan;
              document.getElementById('kecamatan').value = pasienObj.Kecamatan;
              document.getElementById('kota').value = pasienObj.Kota;
              document.getElementById('provinsi').value = pasienObj.Provinsi;
              document.getElementById('telepon').value = pasienObj.TPasien_Telp;
              
              document.getElementById('pendidikan').value = pasienObj.TAdmVar_Pendidikan;
              document.getElementById('pekerjaan').value = pasienObj.TPasien_Kerja;
              document.getElementById('keluarga').value = pasienObj.TPasien_KlgNama;

              var umurPasien = hitungUmur(pasienObj.TPasien_TglLahir);

              if(isNaN(umurPasien['years'])){ umurPasien['years']   = 0; }
              if(isNaN(umurPasien['months'])){ umurPasien['months']   = 0; }
              if(isNaN(umurPasien['days'])){ umurPasien['days']   = 0; }

              document.getElementById('pasienumurthn').value  = umurPasien['years'];
              document.getElementById('pasienumurbln').value  = umurPasien['months'];
              document.getElementById('pasienumurhari').value = umurPasien['days'];

              document.getElementById('jk').value = (pasienObj.JK == null ? 'Laki-laki' : pasienObj.JK); 
              document.getElementById('tempjnspas').value = (pasienObj.TAdmVar_Jenis == null ? '0' : pasienObj.TAdmVar_Jenis); 
              document.getElementById('agama').value = (pasienObj.Agama == null ? 'Islam' : pasienObj.Agama);
              document.getElementById('pendidikan').value = (pasienObj.Pendidikan == null ? 'Tidak Sekolah' : pasienObj.Pendidikan);
              document.getElementById('pekerjaan').value = (pasienObj.Pekerjaan == null ? 'Tidak Bekerja' : pasienObj.Pekerjaan);
              document.getElementById('jenispas').value = pasienObj.JenisPasien;

              getKota(pasienObj.TPasien_Kota, pasienObj.TPasien_Kecamatan, pasienObj.TPasien_Kelurahan)

              changePenjamin();

              checkPasienLamaBaru(pasienObj.TPasien_NomorRM);     

            });
          });

        }

      });

    } //function fillPasien(){

    function fillPasienApp(){
      var pasiennorm  = $('#pasiennorm').val();
      var notrans     = $('#notrans').val();

        // check transaksi pasien untuk poli
      $.get('/ajax-checkpasienjalan?norm='+pasiennorm+'&notrans='+notrans, function(data){
        if(data > 0){
          showWarning(2000, '', 'Pasien sudah terdaftar pada Transaksi Rawat Jalan !', true);
          $('#pasiennorm').val('');
        }else{

          $.get('/ajax-pasienjanjijalanbynorm?pasiennorm='+pasiennorm, function(data){
            $.each(data, function(index, pasienObj){
              var tgl       = new Date(pasienObj.TPasien_TglLahir);
              var tgllahir  = (tgl.getMonth()+1)+'/'+tgl.getDate()+'/'+tgl.getFullYear();

              document.getElementById('nama').value = pasienObj.TPasien_Nama;
              document.getElementById('panggilan').value = pasienObj.TPasien_Panggilan;
              document.getElementById('alamat').value = pasienObj.TPasien_Alamat;

              document.getElementById('kelurahan').value = pasienObj.Kelurahan;
              document.getElementById('kecamatan').value = pasienObj.Kecamatan;
              document.getElementById('kota').value = pasienObj.Kota;
              document.getElementById('provinsi').value = pasienObj.Provinsi;
              document.getElementById('telepon').value = pasienObj.TPasien_Telp;
              
              document.getElementById('pendidikan').value = pasienObj.TAdmVar_Pendidikan;
              document.getElementById('pekerjaan').value = pasienObj.TPasien_Kerja;
              document.getElementById('keluarga').value = pasienObj.TPasien_KlgNama;

              var umurPasien = hitungUmur(pasienObj.TPasien_TglLahir);

              if(isNaN(umurPasien['years'])){ umurPasien['years']   = 0; }
              if(isNaN(umurPasien['months'])){ umurPasien['months']   = 0; }
              if(isNaN(umurPasien['days'])){ umurPasien['days']   = 0; }

              document.getElementById('pasienumurthn').value  = umurPasien['years'];
              document.getElementById('pasienumurbln').value  = umurPasien['months'];
              document.getElementById('pasienumurhari').value = umurPasien['days'];

              document.getElementById('jk').value = (pasienObj.JK == null ? 'Laki-laki' : pasienObj.JK); 
              document.getElementById('tempjnspas').value = (pasienObj.TAdmVar_Jenis == null ? '0' : pasienObj.TAdmVar_Jenis); 
              document.getElementById('agama').value = (pasienObj.Agama == null ? 'Islam' : pasienObj.Agama);
              document.getElementById('pendidikan').value = (pasienObj.Pendidikan == null ? 'Tidak Sekolah' : pasienObj.Pendidikan);
              document.getElementById('pekerjaan').value = (pasienObj.Pekerjaan == null ? 'Tidak Bekerja' : pasienObj.Pekerjaan);
              document.getElementById('jenispas').value = pasienObj.JenisPasien;
              document.getElementById('unit').value = pasienObj.TUnit_Kode  ;
              document.getElementById('dokter').value = pasienObj.TPelaku_Kode;
             
              getKota(pasienObj.TPasien_Kota, pasienObj.TPasien_Kecamatan, pasienObj.TPasien_Kelurahan)

              changePenjamin();

              checkPasienLamaBaru(pasienObj.TPasien_NomorRM);     

            });
          });

        }

      });

    } //function fillPasien(){

    function fillPasienModal(){
      var pasiennorm = $('#pasiennorm').val();

      // --ajax proses--
      $.get('/ajax-pasienbynorm?pasiennorm='+pasiennorm, function(data){

        $.each(data, function(index, pasienObj){
          var tgl       = new Date(pasienObj.TPasien_TglLahir);
          var tgllahir  = (tgl.getMonth()+1)+'/'+tgl.getDate()+'/'+tgl.getFullYear();

          document.getElementById('mpasien_id').value = pasienObj.id;
          document.getElementById('mnomorrm').value = pasienObj.TPasien_NomorRM;
          document.getElementById('mnama').value = pasienObj.TPasien_Nama;
          document.getElementById('mpanggilan').value = pasienObj.TPasien_Panggilan;
          document.getElementById('mjk').value = (pasienObj.TAdmVar_Gender == null ? 'L' : pasienObj.TAdmVar_Gender); 
          document.getElementById('mtgllahir').value = pasienObj.TPasien_TglLahir;
          document.getElementById('mprovinsi').value = pasienObj.TPasien_Prov;
          document.getElementById('malamat').value = pasienObj.TPasien_Alamat;
          document.getElementById('mjenispasien').value = pasienObj.TAdmVar_Jenis;
          document.getElementById('mtelepon').value = pasienObj.TPasien_Telp;
          document.getElementById('mHP').value = pasienObj.TPasien_HP;
          
          document.getElementById('magama').value = (pasienObj.tadmvar_agama == null ? '1' : pasienObj.tadmvar_agama);
          document.getElementById('mpendidikan').value = (pasienObj.TAdmVar_Pendidikan == null ? '1' : pasienObj.TAdmVar_Pendidikan);
          document.getElementById('mpekerjaan').value = (pasienObj.TAdmVar_Pekerjaan == null ? '1' : pasienObj.TAdmVar_Pekerjaan);  
          document.getElementById('mkeluarga').value = pasienObj.TPasien_KlgNama;   

          getKota2(pasienObj.TPasien_Kota, pasienObj.TPasien_Kecamatan, pasienObj.TPasien_Kelurahan);

        });

      });
    }
    // ======================================= End AutoComplete ======================================

    

    function checkPasienLamaBaru(norm){

      $.get('/ajax-checkpasienbarulama?norm='+norm, function(data){

        if(data > 0){

          var JLL = 0;

          JLL = <?php echo $JLL; ?>;
          JLL = formatRibuan(parseInt(JLL));

          document.getElementById('pasbaru').value = 'L';
          document.getElementById('dftLama').checked = true; 
          $('#biayadft').val(JLL);

          hitungBiaya();

        }else{

          var JLB = 0;

          JLB = <?php echo $JLB; ?>;
          JLB = formatRibuan(parseInt(JLB));

          document.getElementById('pasbaru').value = 'B';
          document.getElementById('dftBaru').checked = true;
          $('#biayadft').val(JLB);
          
          hitungBiaya();

        }

      });

    }

    function checkLB(){
      if($('#dftBaru').checked=true){
        $('#biayadft').val(JLB);
        hitungBiaya();
      }

      if($('#dftLama').checked=true){
        $('#biayadft').val(JLL);
        hitungBiaya();
      }
    }

    $('#penjamin').on('change', function(e){
      hitungBiaya();
    });

    $('#ditanggung').on('change', function(e){
      hitungBiaya();
    });


    // ================================ autoComboDokter ===============================
    $('#unit').on('change', function(e){
      autoPelakuByUnit($('#unit').val());

    });

    function autoPelakuByUnit(kdUnit, kdPelaku=0){
      $.get('/ajax-pelaku?unit_kode='+kdUnit, function(data){

        $('#dokter').empty();

        $.each(data, function(index, pelakuObj){
          if(kdPelaku>0){
            if(pelakuObj.id == kdPelaku){
              $('#dokter').append('<option value="'+pelakuObj.TPelaku_Kode+'" selected="selected">'+pelakuObj.TPelaku_NamaLengkap+'</option>');
            }else{
              $('#dokter').append('<option value="'+pelakuObj.TPelaku_Kode+'">'+pelakuObj.TPelaku_NamaLengkap+'</option>');
            }
          }else{
            $('#dokter').append('<option value="'+pelakuObj.TPelaku_Kode+'">'+pelakuObj.TPelaku_NamaLengkap+'</option>');
          }
        });

      });

      var statusEdit  = $('#temptStatus').val();
      var tempUnit    = $('#tempUnit').val();
      var tempNoUrut  = $('#tempNoUrut').val();

      $.get('/ajax-getautonumbertrans?unit_kode='+kdUnit, function(data){
        $('#noantri').val(data);
      });
    }
    // =================================================================================

    // ================================ Auto Combo Penjamin ===============================
    $('#ditanggung').on('change', function(e){
      
      hitungBiaya();

    });

    function changeUpdate(){
      var tipe  = $('#temptStatus').val(); 

      if(tipe == 'E'){
        // var form  = document.getElementById('formpoli');
         var id    = $('#transID').val();

        //form.id     = id;  
        form.action = '/poli/'+id;
        form.method = 'PUT';

        //alert(form.method);

        form.submit();
      }else{
        form      = document.getElementById('formpoli');
        var id    = $('#transID').val();

        form.id   = id;      
        form.action = '/poli';
        form.method = 'POST';

        //alert(form.action);
        form.submit();        
      }
    }


    function changePenjamin(){
      var jenispas = $('#tempjnspas').val();

      $.get('/ajax-tperusahaan?jenispas='+jenispas, function(data){

        $('#penjamin').empty();

        $.each(data, function(index, penjaminObj){
          $('#penjamin').append('<option value="'+penjaminObj.TPerusahaan_Kode+'">'+penjaminObj.TPerusahaan_Nama+'</option>');
        });

      });
    }
    // =================================================================================

    var unit_id = $('#unit').val();

    $.get('/ajax-getautonumbertrans?unit_id='+unit_id, function(data){

        $('#noantri').val(data);

      });

    function hitungBiaya(){
      var isPribadi     = true;
      var jmlpribadi    = 0;
      var jmlditanggung = 0;

      if($('#tempjnspas').val() > 0 && $('#ditanggung').val() == 'S'){ 

        isPribadi = false; 
        jmlpribadi      = 0;
        jmlditanggung   = parseInt($('#biayadft').val().replace(',', ''))+parseInt($('#krtpasien').val().replace(',', ''));


      }else{ 
        isPribadi = true; 
        jmlditanggung   = 0;
        jmlpribadi      = parseInt($('#biayadft').val().replace(',', ''))+parseInt($('#krtpasien').val().replace(',', ''));
      }

      $('#jmlpribadi').val(formatRibuan(jmlpribadi));
      $('#jmlditanggung').val(formatRibuan(jmlditanggung));
    }

  </script>

@endsection