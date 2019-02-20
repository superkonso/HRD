@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Poli')

@section('content_header', 'Edit Pendaftaran Poli')

@section('header_description', '')

@section('menu_desc', 'Poli')

@section('link_menu_desc', '/poli')

@section('sub_menu_desc', 'Edit')

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

  <!-- ========================================== Content ============================================= -->
  <div class="row font-medium">

    <form class="form-horizontal form-label-left" action="/poli/{{$polis->id}}" method="post" id="formpoli" data-parsley-validate onsubmit="return checkFormPoli();">

      {{method_field('PUT')}}

      <!-- Token -->
      {{csrf_field()}}

      {{ Form::hidden('transID', $polis->id, array('id' => 'transID')) }}
      {{ Form::hidden('pasbaru', $polis->TRawatJalan_PasBaru, array('id' => 'pasbaru')) }}
      {{ Form::hidden('tempUnit', $polis->TUnit_Kode, array('id' => 'tempUnit')) }}
      {{ Form::hidden('tempNoRM', $polis->TPasien_NomorRM, array('id' => 'tempNoRM')) }}
      {{ Form::hidden('tempNoTrans', $polis->TRawatJalan_NoReg, array('id' => 'tempNoTrans')) }}
      {{ Form::hidden('tempNoUrut', $polis->TRawatJalan_NoUrut, array('id' => 'tempNoUrut')) }}
      {{ Form::hidden('tempjnspas', $polis->TAdmVar_Jenis, array('id' => 'tempjnspas')) }}
      {{ Form::hidden('prshkode', $polis->TPerusahaan_id, array('id' => 'prshkode')) }}
      {{ Form::hidden('editNoUrut', 'Y', array('id' => 'editNoUrut')) }}


    <!-- ===================================== Data Pasien =========================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary">
          <div class="box-header">
            <h3 class="box-title">Data Pasien</h3>
          </div>
        <div class="box-body">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor Trans</label>
              <div class="col-md-6 col-sm-6 col-xs-6">
                <input type="text" name="notrans" id="notrans" class="form-control col-md-7 col-xs-12" value="{{$polis->TRawatJalan_NoReg}}" readonly>
              </div>
              <div class="col-md-3 col-sm-3 col-xs-3">
                <input type="text" name="noantri" id="noantri" class="form-control col-md-7 col-xs-12" value="{{$polis->TRawatJalan_NoUrut}}" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <a href="#formsearch" id="caripasienlama" onclick="cariPasienLama();" class="btn btn-primary" data-toggle="modal"><img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Pasien Lama</a>
              <a href="/pasien/create" class="btn btn-success"><img src="{!! asset('images/icon/pasien-baru-icon.png') !!}" width="20" height="20"> Pasien Baru</a>

              <a href="#formsearch" id="caripasienapp" onclick="cariPasienApp();" class="btn btn-primary" data-toggle="modal"><img src="{!! asset('images/icon/pasien-appointment-icon.png') !!}" width="20" height="20"> Pasien Appointment</a>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor RM</label>
              <div class="col-md-6 col-sm-6 col-xs-6">
                <input type="text" name="pasiennorm" id="pasiennorm" class="form-control col-md-7 col-xs-12" value="{{$polis->TPasien_NomorRM}}">
              </div>
              <div class="col-md-2 col-sm-2 col-xs-2">
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
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Klinik Dituju</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="unit" id="unit" class="form-control col-md-7 col-xs-12">

                  @foreach($units as $unit)
                    @if($unit->TUnit_Kode == $polis->TUnit_Kode)
                      <option value="{{$unit->TUnit_Kode}}" selected="selected">{{$unit->TUnit_Nama}}</option>
                    @else
                      <option value="{{$unit->TUnit_Kode}}">{{$unit->TUnit_Nama}}</option>
                    @endif
                  @endforeach
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Dokter</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="dokter" id="dokter" class="form-control col-md-7 col-xs-12">

                  @foreach($pelakus as $dokter)
                    @if($dokter->TPelaku_Kode == $polis->TPelaku_Kode)
                      <option value="{{$dokter->TPelaku_Kode}}" selected="selected">{{$dokter->TPelaku_NamaLengkap}}</option>
                    @else
                      <option value="{{$dokter->TPelaku_Kode}}">{{$dokter->TPelaku_NamaLengkap}}</option>
                    @endif
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
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Rujukan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="rujukan" id="rujukan" class="form-control col-md-7 col-xs-12">

                    @foreach($admvars as $rujukan)
                      @if ($rujukan->TAdmVar_Seri=='MASUKCARA')
                        @if($rujukan->TAdmVar_Kode == $polis->TRawatJalan_AsalPasien)
                          <option value="{{$rujukan->TAdmVar_Kode}}" selected="selected">{{$rujukan->TAdmVar_Nama}}</option>
                        @else
                          <option value="{{$rujukan->TAdmVar_Kode}}">{{$rujukan->TAdmVar_Nama}}</option>
                        @endif
                      @endif
                    @endforeach
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Keterangan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="ketrujukan" id="ketrujukan" class="form-control col-md-7 col-xs-12" value="{{$polis->TRawatJalan_RujukanDari}}">
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
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Penjamin</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="penjamin" id="penjamin" class="form-control col-md-7 col-xs-12">

                  @foreach($prsh as $penjamin)
                    @if($penjamin->TPerusahaan_Kode == $polis->TPerusahaan_Kode)
                      <option value="{{$penjamin->TPerusahaan_Kode}}" selected="selected">{{$penjamin->TPerusahaan_Nama}}</option>
                    @else
                      <option value="{{$penjamin->TPerusahaan_Kode}}">{{$penjamin->TPerusahaan_Nama}}</option>
                    @endif
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
                  <input type="radio" name="daftar" id="dftBaru" value="B" @if($polis->TRawatJalan_PasBaru == 'B') checked="checked" @endif> Baru 
                  <input type="radio" name="daftar" id="dftLama" value="L" @if($polis->TRawatJalan_PasBaru != 'B') checked="checked" @endif> Lama
                </div>
                <div class="col-md-7 col-sm-7 col-xs-12" style="padding-left: 0px; padding-right: 0px;">
                  <input type="text" name="biayadft" id="biayadft" class="form-control col-md-7 value="{{$polis->TRawatJalan_Daftar}}" col-xs-12" readonly>
                </div>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Biaya Kartu</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <div class="col-md-5 col-sm-5 col-xs-12">
                  <input type="checkbox" name="kartu" id="kartu" @if($polis->TRawatJalan_Kartu > 0) checked="checked" @endif> Kartu Pasien 
                </div>
                <div class="col-md-7 col-sm-7 col-xs-12" style="padding-left: 0px; padding-right: 0px;">
                  <input type="text" name="krtpasien" id="krtpasien" class="form-control col-md-7 col-xs-12" value="{{$polis->TRawatJalan_Kartu}}" readonly>
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
  </div>


  <!-- ================== -->
  <div class="row font-medium">
    <div class="form-group col-md-12 col-sm-12 col-xs-12">
      <div class="box">
        <div class="box-body">
          <div class="col-md-12 col-md-offset-5">
            <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
            <a href="/poli/show" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</a>
            
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

  <script type="text/javascript">

    function cariPasienLama(){

      var formSearch = '';

      formSearch += '<div class="modal fade" id="formsearch" tabindex="-1" role="dialog" aria-labelledby="modalWarning" aria-hidden="true">';
      formSearch += '<div class="modal-dialog" role="document">';
      formSearch += '<div class="modal-content">';
      formSearch += '<div class="modal-header alert-info">';
      formSearch += '<img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> DATA PASIEN LAMA';
      formSearch += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
      formSearch += '</div>';
      formSearch += '<div class="modal-body">';
      formSearch += '<div class="input-group">';
      formSearch += '<div class="input-group-addon" style="background-color: #167F92;">';
      formSearch += '<img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">';
      formSearch += '</div>';
      formSearch += '<input type="text" id="cdatapasien" class="form-control pull-right" onkeyup="cdatapasienKU(this.value)" placeholder="Nomor RM / Nama Pasien">';
      formSearch += '</div>';
      formSearch += '<div style="overflow-x: scroll;">';
      formSearch += '<div id="hasil" style="max-height: 400px; overflow-x: scroll; overflow-y: scroll;"></div>';
      formSearch += '</div>';
      formSearch += '</div>';
      formSearch += '<div class="modal-footer">';
      formSearch += '<button type="button" class="btn btn-success" data-dismiss="modal" onclick="pilihPasien()"><img src="{!! asset('images/icon/checklist-icon.png') !!}" width="20" height="20"> Pilih</button>';
      formSearch += '</div></div></div>';

      document.getElementById('searchFrm').innerHTML = formSearch;

      cPasienLama();
    }  
  </script>

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
            autoclose: true
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
          type: 'POST',
          url: '/editpasien',
          data: {
            //'_token': $('input[name=_token]').val(),
            'pasienID': pasienID,
            'pasienNoRM': pasienNoRM,
            'pasienNama': pasienNama,
            'pasienPanggilan': pasienPanggilan,
            'pasienJK': pasienJK,
            'pasienTglLahir': pasienTglLahir,
            'prov': prov,
            'kota': kota,
            'kec': kec,
            'kel': kel,
            'pasienAlamat': pasienAlamat,
            'pasienTelp': pasienTelp,
            'pasienHP': pasienHP,
            'pasienJenis': pasienJenis,
            'pasienAgama': pasienAgama,
            'pasienPend': pasienPend,
            'pasienPek': pasienPek,
            'pasienKel': pasienKel,
          },
          success: function(result){

          }
        });

        event.preventDefault();
        $('#editPasienModal').modal('hide');

        fillPasien();
      });

      // =========================================== End Edit Pasien =====================================

        $('#ditanggung').val('S');

        $('#biayadft').val({{$polis->TRawatJalan_Daftar}});

        $('#kartu').on('change', function(e){
   
          if($("#kartu").prop('checked') == true){
              $('#krtpasien').val(CDB);
          }else{
              $('#krtpasien').val(0);
          }

          hitungBiaya();

        });

        fillPasien();

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

            }
        });

        $('#pasiennorm').on('change', function(e){
          fillPasien();

        });

        function fillPasien(){
          var pasiennorm = $('#pasiennorm').val();
          var notrans     = $('#notrans').val();

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

                  //checkPasienLamaBaru(pasienObj.id);   

                  hitungBiaya();  

                });

              });

            } // else if(data > 0){
          }); // $.get('/ajax-checkpasienjalan?norm='+pasiennorm, function(data){
        }

        function fillPasienModal(){
          var pasiennorm = $('#pasiennorm').val();

          $.get('/ajax-pasienbynorm?pasiennorm='+pasiennorm, function(data){

            $.each(data, function(index, pasienObj){
              var tgl       = new Date(pasienObj.TPasien_TglLahir);
              var tgllahir  = (tgl.getMonth()+1)+'/'+tgl.getDate()+'/'+tgl.getFullYear();

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

      //alert(tempUnit);

        if(kdUnit == tempUnit){
          $('#noantri').val(tempNoUrut);
          $('#editNoUrut').val('N');
        }else{
          $.get('/ajax-getautonumbertrans?unit_kode='+kdUnit, function(data){
            $('#noantri').val(data);
            $('#editNoUrut').val('Y');
          });
        }
    }
    // =================================================================================

    // ================================ Auto Combo Penjamin ===============================
        $('#ditanggung').on('change', function(e){
          
          hitungBiaya();

        });


        function changePenjamin(){
          var jenispas = $('#tempjnspas').val();

          $.get('/ajax-tperusahaan?jenispas='+jenispas, function(data){

            $('#penjamin').empty();

            $.each(data, function(index, penjaminObj){
              if(penjaminObj.TPerusahaan_Kode == $('#prshkode').val()){
                $('#penjamin').append('<option value="'+penjaminObj.TPerusahaan_Kode+'" selected="selected">'+penjaminObj.TPerusahaan_Nama+'</option>');
              }else{
                $('#penjamin').append('<option value="'+penjaminObj.TPerusahaan_Kode+'">'+penjaminObj.TPerusahaan_Nama+'</option>');
              }
            });

          });
        }
    // =================================================================================

    var unit_id = $('#unit').val();

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