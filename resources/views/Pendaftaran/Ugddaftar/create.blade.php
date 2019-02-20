@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Pendaftaran UGD')

@section('content_header', 'Pendaftaran UGD')

@section('header_description', '')

@section('menu_desc', 'Ugddaftar')

@section('link_menu_desc', '/ugddaftar')

@section('sub_menu_desc', 'Create')

@section('content')

@include('Partials.message')

  <?php
    $UGB = 0;
    $UGL = 0;
    $CDB = 0;

    if(!empty($tarifvars)){

      foreach ($tarifvars as $tarif) {

        if($tarif->TTarifVar_Seri == 'GENERAL' && $tarif->TTarifVar_Kode == 'UGB'){
          $UGB = $tarif->TTarifVar_Nilai;
        }elseif($tarif->TTarifVar_Seri == 'GENERAL' && $tarif->TTarifVar_Kode == 'UGL'){
          $UGL = $tarif->TTarifVar_Nilai;
        }elseif($tarif->TTarifVar_Seri == 'GENERAL' && $tarif->TTarifVar_Kode == 'CDB'){
          $CDB = $tarif->TTarifVar_Nilai;
        }
      }
    }
  ?>

  <!-- ========================================== Content ================================================= -->
  <div class="row">
    <form class="form-horizontal form-label-left" action="/ugddaftar" id="frmdaftarugd" method="post" data-parsley-validate onsubmit="return checkFormDaftarUGD()">

      <!-- Token -->
      {{csrf_field()}}

      <!-- {{ Form::hidden('id', '0', array('id' => 'pasien_id')) }} -->
      {{ Form::hidden('transID', '0', array('id' => 'transID')) }}
      {{ Form::hidden('pasbaru', 'L', array('id' => 'pasbaru')) }}
      {{ Form::hidden('tempNoRM', $tempNoRM, array('id' => 'tempNoRM')) }}
      {{ Form::hidden('tempNoTrans', '', array('id' => 'tempNoTrans')) }}
      {{ Form::hidden('temptStatus', 'C', array('id' => 'temptStatus')) }}
      {{ Form::hidden('tempUnit', '', array('id' => 'tempUnit')) }}
      {{ Form::hidden('tempNoUrut', '', array('id' => 'tempNoUrut')) }}
      {{ Form::hidden('tempjnspas', '', array('id' => 'tempjnspas')) }}

    <!-- ===================================== Data Pasien =========================================== -->
   <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Data Pasien</h3>
        </div>
        <div class="box-body">

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor Trans</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="notrans" id="notrans" class="form-control col-md-7 col-xs-12" value="{{$autoNumber}}" readonly>
              </div>
            </div>
       
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <a href="#formsearch" id="caripasienlama" onclick="cariPasienLama();" class="btn btn-primary" data-toggle="modal"><img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Pasien Lama</a>
              <a href="/pasienu/create" class="btn btn-success"><img src="{!! asset('images/icon/pasien-baru-icon.png') !!}" width="20" height="20"> Pasien Baru</a>
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
                <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
        
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Panggilan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="panggilan" id="panggilan" class="form-control col-md-7 col-xs-12" value="">
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
                    <input type="text" name="pasienumurthn" id="pasienumurthn" class="form-control col-md-7 col-xs-12" value="0" onfocus=""> Tahun
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12" style="padding-left: 0px;">
                    <input type="text" name="pasienumurbln" id="pasienumurbln" class="form-control col-md-7 col-xs-12" value="0"> Bulan
                  </div>
                  <div class="col-md-4 col-sm-4 col-xs-12" style="padding-left: 0px;">
                    <input type="text" name="pasienumurhari" id="pasienumurhari" class="form-control col-md-7 col-xs-12" value="0"> Hari
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
                <textarea name="alamat" id="alamat" class="form-control" rows="3" style="resize:none;">@yield('keterangan')</textarea>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Telepon</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="telepon" id="telepon" class="form-control col-md-7 col-xs-12" value="">
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
                <input type="text" name="keluarga" id="keluarga" class="form-control col-md-7 col-xs-12" value="">
              </div>
            </div>
 
            <div class="form-group">
              <div class="col-md-12 col-sm-12 col-xs-12">
              <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#editPasienModal" onclick="fillPasienModal();"><img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Edit Pasien</button>
              </div>
            </div>

        </div> <!-- <div class="box-body"> -->
      </div> <!-- <div class="box box-primary"> -->
    </div> <!-- <div class="col-md-6 col-sm-12 col-xs-12"> -->

    <!-- ====================================== End Data Pasien ==================================== -->

      <div class="col-md-6 col-sm-12 col-xs-12">
      <!-- ====================================== Klinik dan Dokter ================================== -->
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Klinik & Dokter</h3>
        </div>
        <div class="box-body">
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

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alasan Datang</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="alasan" id="alasan" class="form-control col-md-7 col-xs-12">
                  @foreach($admvars as $alasan)
                    @if ($alasan->TAdmVar_Seri=='UGDALASAN')
                      <option value="{{$alasan->TAdmVar_Kode}}">{{$alasan->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach         
                </select>
              </div>
            </div> <!-- <div class="form-group"> -->
         </div> <!-- <div class="box-body"> -->
      </div> <!-- <div class="box-primary"> -->
      <!-- ======================================= End Klinik dan Dokter ========================================== -->

      <!-- ============================================ Rujukan =================================================== -->
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
                    @if ($rujukan->TAdmVar_Seri=='MASUKUGD')
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

        </div> <!-- div class="box-body" -->
      </div> <!-- <div class="box-primary"> -->
      <!-- ========================================= End Rujukan ================================================== -->

      <!-- ======================================= Biaya Pemeriksaan ============================================== -->
      <div class="box box-primary">
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

        </div> <!-- <div class="box-body"> -->
      </div> <!-- <div class="box-primary"> -->
      <!-- ============================================ End Biaya Pemeriksaan ================================== -->

      <!-- ========================================== Jumlah Biaya Pemeriksaan ====================================== -->
      <div class="box box-primary">
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
        </div> <!-- div class="box-body" -->
      </div> <!-- <div class="box-primary"> -->
      <!-- ======================================== End Jumlah Biaya Pemeriksaan ================================= -->
        <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">KEGAWATAN</h3>
        </div>
        <div class="box-body">
          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">Darurat</label>
            <div class="control-label col-md-9 col-sm-9 col-xs-9">
              <select name="darurat" id="darurat" class="form-control" onchange="changeKegawatan();">
                  @foreach($admvars as $darurat)
                    @if ($darurat->TAdmVar_Seri=='UGDGAWAT')
                      <option value="{{$darurat->TAdmVar_Kode}}" 
                        @if($darurat->TAdmVar_Kode == '0') 
                          style="background-color: green;color:white;" 
                        @elseif($darurat->TAdmVar_Kode == '1') 
                          style="background-color: yellow;color:black;" 
                        @elseif($darurat->TAdmVar_Kode == '2') 
                          style="background-color: #FFCC00;color:black;"  
                        @elseif($darurat->TAdmVar_Kode == '3') 
                          style="background-color: red;color:white;"  
                        @elseif($darurat->TAdmVar_Kode == '4') 
                          style="background-color: black;color:white;" 
                        @endif>{{$darurat->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach
             </select>
            </div>
          </div>
        </div> <!-- div class="box-body" -->
      </div> <!-- <div class="box-primary"> -->
    </div> <!-- <div class="col-md-6 col-sm-12 col-xs-12"> -->      
  </div> <!-- div class="row" -->

  <!-- ================== -->
  <div class="row">
    <div class="form-group col-md-12 col-sm-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-body">
          <div class="col-md-12 col-md-offset-5">
            <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Save</button>
            <a href="/ugddaftar/show" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20"> Edit</a>
            <a href="" class="btn btn-danger" onclick="resetForm();"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Clear Form</a>
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
      formSearch += '<i class="fa fa-wheelchair"></i> DATA PASIEN LAMA';
      formSearch += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
      formSearch += '</div>';
      formSearch += '<div class="modal-body">';
      formSearch += '<div class="input-group">';
      formSearch += '<div class="input-group-addon" style="background-color: #167F92;">';
      formSearch += '<i class="fa fa-search"></i>';
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

  <?php
    $search_title   = 'Pasien';
    $search1        = 'Nama';
    $search2        = '';
    $search3        = '';
  ?> 
  @include('search')

  <!-- JQuery 1 -->
  <script src="{{ asset('js/jquery.min.js') }}"></script>

  <!-- Auto Complete Search Asset -->
  <script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

  <!-- Modal Searching Pasien Lama -->
  <script src="{{ asset('js/searchData.js') }}"></script>

  <!-- ==================================================== End Of Content ============================================================= -->

  @include('Partials.errors')

  <script type="text/javascript">

    $("#frmdaftarugd").keypress(function (e) {
      if (e.which == 13 || e.keyCode == 13) {

        return false;
      }
    });

  // ---------------------------- Ready Function ---------------------------------------
    $( document ).ready(function() {

      var UGB = 0;
      var UGL = 0;
      var CDB = 0;

      UGB = <?php echo $UGB; ?>;
      UGL = <?php echo $UGL; ?>;
      CDB = <?php echo $CDB; ?>;

      UGB = formatRibuan(parseInt(UGB));
      UGL = formatRibuan(parseInt(UGL));
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

        // var pasienID        = $('#mpasien_id').val();
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
          url: 'editpasien',
          data: {
            //'_token': $('input[name=_token]').val(),
            // 'pasienID': pasienID,
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

      $('#biayadft').val(UGB);

      $('#kartu').on('change', function(e){
 
        if($("#kartu").prop('checked') == true){
            $('#krtpasien').val(CDB);
        }else{
            $('#krtpasien').val(0);
        }

        hitungBiaya();

      });

      $('#jenispas').on('change', function(e){

        hitungBiaya();

      });

      $('#dftBaru').on('change', function(e){
        $('#biayadft').val(UGB);
        hitungBiaya();
      });

      $('#dftLama').on('change', function(e){
        $('#biayadft').val(UGL);
        hitungBiaya();
      });

      hitungBiaya();

      changeKegawatan();

    });

    function changeKegawatan(){
      var status = $('#darurat').val();

      if(status == '0'){
        document.getElementById("darurat").style.color = "white";
        document.getElementById("darurat").style.backgroundColor = "green";
      }else if(status == '1'){
        document.getElementById("darurat").style.color = "black";
        document.getElementById("darurat").style.backgroundColor = "yellow";
      }else if(status == '2'){
        document.getElementById("darurat").style.color = "black";
        document.getElementById("darurat").style.backgroundColor = "#FFCC00";
      }else if(status == '3'){
        document.getElementById("darurat").style.color = "white";
        document.getElementById("darurat").style.backgroundColor = "red";
      }else if(status == '4'){
        document.getElementById("darurat").style.color = "white";
        document.getElementById("darurat").style.backgroundColor = "black";
      }else{
        document.getElementById("darurat").style.color = "white";
        document.getElementById("darurat").style.backgroundColor = "green";
      }
    }

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
      
      var pasiennorm = $('#pasiennorm').val();
     // check transaksi pasien untuk ugd
      $.get('/ajax-checkpasienugd?norm='+pasiennorm, function(data){
        if(data > 0){
          showDialog('modalWarning', 'Pasien sudah terdaftar pada Transaksi UGD !');
          $('#pasiennorm').val('');
        }else{

      // --ajax proses--
         $.get('/ajax-pasienbynorm?pasiennorm='+pasiennorm, function(data){

         $.each(data, function(index, pasienObj){
          // $('#pelaku').append('<option value="'+pelakuObj.id+'">'+pelakuObj.TPelaku_NamaLengkap+'</option>');

          var tgl       = new Date(pasienObj.TPasien_TglLahir);
         
          // document.getElementById('pasien_id').value = pasienObj.id;
          document.getElementById('nama').value = pasienObj.TPasien_Nama;
          document.getElementById('panggilan').value = pasienObj.TPasien_Panggilan;
          document.getElementById('alamat').value = pasienObj.TPasien_Alamat;

          document.getElementById('kelurahan').value = pasienObj.Kelurahan;
          document.getElementById('kecamatan').value = pasienObj.Kecamatan;
          document.getElementById('kota').value = pasienObj.Kota;
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
           document.getElementById('tempjnspas').value = (pasienObj.TAdmVar_Jenis == null ? '0' : pasienObj.TAdmVar_Jenis); 
         document.getElementById('jk').value = (pasienObj.JK == null ? 'Laki-laki' : pasienObj.JK); 
    
            document.getElementById('provinsi').value = pasienObj.Provinsi;
          getKota(pasienObj.TPasien_Kota, pasienObj.TPasien_Kecamatan, pasienObj.TPasien_Kelurahan)

           document.getElementById('agama').value = (pasienObj.Agama == null ? 'Islam' : pasienObj.Agama);
           document.getElementById('pendidikan').value = (pasienObj.Pendidikan == null ? 'Tidak Sekolah' : pasienObj.Pendidikan);
              document.getElementById('pekerjaan').value = (pasienObj.Pekerjaan == null ? 'Tidak Bekerja' : pasienObj.Pekerjaan);
              document.getElementById('jenispas').value = pasienObj.JenisPasien;

          changePenjamin();
          checkPasienLamaBaru(pasienObj.TPasien_NomorRM);     
          // changeGawat();
        });
        });
        }
      });

    }
      function fillPasienModal(){
      var pasiennorm = $('#pasiennorm').val();

      // --ajax proses--
      $.get('/ajax-pasienbynorm?pasiennorm='+pasiennorm, function(data){
        $.each(data, function(index, pasienObj){
          var tgl       = new Date(pasienObj.TPasien_TglLahir);
          var tgllahir  = (tgl.getMonth()+1)+'/'+tgl.getDate()+'/'+tgl.getFullYear();

          // document.getElementById('mpasien_id').value = pasienObj.id;
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
          var UGL = 0;
          UGL = <?php echo $UGL; ?>;
          UGL = formatRibuan(parseInt(UGL));

          document.getElementById('pasbaru').value = 'L';
          document.getElementById('dftLama').checked = true; 
          $('#biayadft').val(UGL);
          hitungBiaya();
        }else{
          var UGB = 0;
          UGB = <?php echo $UGB; ?>;
          UGB = formatRibuan(parseInt(UGB));

          document.getElementById('pasbaru').value = 'B';
          document.getElementById('dftBaru').checked = true;
          $('#biayadft').val(UGB);
          hitungBiaya();
        }
      });
    }

    function checkLB(){
      if($('#dftBaru').checked=true){
        $('#biayadft').val(UGB);
        hitungBiaya();
      }

      if($('#dftLama').checked=true){
        $('#biayadft').val(UGL);
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
      var unit_id = $('#unit').val(); // bisa juga --> e.target.value;

      $.get('/ajax-pelaku?unit_id='+unit_id, function(data){

        $('#dokter').empty();

        $.each(data, function(index, pelakuObj){
          $('#dokter').append('<option value="'+pelakuObj.id+'">'+pelakuObj.TPelaku_NamaLengkap+'</option>');
        });
      });

      $.get('/ajax-getautonumbertrans?unit_id='+unit_id, function(data){
        $('#noantri').val(data);

      });

    });
    // =================================================================================

    // ================================ Auto Combo Penjamin ===============================
    $('#jenispas').on('change', function(e){
      
      changePenjamin();

    });

     $('#darurat').on('change', function(e){ 
      // changeGawat();
    });

    $('#ditanggung').on('change', function(e){
        hitungBiaya();
    });

   function changePenjamin(){
      var jenispas = $('#tempjnspas').val();

      $.get('/ajax-tperusahaan?jenispas='+jenispas, function(data){

        $('#penjamin').empty();

        $.each(data, function(index, penjaminObj){
          $('#penjamin').append('<option value="'+penjaminObj.TPerusahaan_Kode+'">'+penjaminObj.TPerusahaan_Nama+'</option>');
        });

      });
    }

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

  function checkFormDaftarUGD(){
      var norm  = $('#pasiennorm').val();
      var nama  = $('#nama').val();

      if(norm == '' || norm.toString().length < 6){
        showWarning(2000, '', 'Nomor RM Pasien Masih Kosong !', true);
        $('#pasiennorm').focus();
        return false;
      }else if(nama = '' || nama.toString().length < 1){
        showWarning(2000, '', 'Nama Pasien Belum Diisi !', true);
        $('#nama').focus();
        return false;
      }else{
        return true;
      }
    }

</script>

@endsection