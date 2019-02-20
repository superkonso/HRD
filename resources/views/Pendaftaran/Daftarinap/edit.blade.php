@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Inap')

@section('content_header', 'Edit Pendaftaran Inap')

@section('header_description', '')

@section('menu_desc', 'Daftarinap')

@section('link_menu_desc', '/daftarinap')

@section('sub_menu_desc', 'Edit')

@section('content')

@include('Partials.message')

  <?php
    date_default_timezone_set("Asia/Bangkok");

    $tglMasuk = new DateTime($datatrans->TRawatInap_TglMasuk);

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
  <div class="row font-medium">

    <form class="form-horizontal form-label-left" action="/daftarinap/{{$datatrans->id}}" id="formdaftarinap" name="formdaftarinap" method="post" data-parsley-validate onsubmit="return checkFormDaftarInap()">

      <!-- Token -->
      {{csrf_field()}}
      {{method_field('PUT')}}

      {{ Form::hidden('pasien_id', '0', array('id' => 'pasien_id')) }}
      {{ Form::hidden('transID', '0', array('id' => 'transID')) }}
      {{ Form::hidden('tempNoRM', '', array('id' => 'tempNoRM')) }}
      {{ Form::hidden('tempNoTrans', '', array('id' => 'tempNoTrans')) }}
      {{ Form::hidden('temptStatus', 'C', array('id' => 'temptStatus')) }}
      {{ Form::hidden('tempjnspas', '', array('id' => 'tempjnspas')) }}

    <div class="col-md-6 col-sm-12 col-xs-12">
      <!-- ===================================== Data Pasien =========================================== -->
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Data Pasien</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor Trans</label>
              <div class="col-md-5 col-sm-5 col-xs-5">
                <input type="text" name="notrans" id="notrans" class="form-control col-md-7 col-xs-12" readonly>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-4">
                <a href="#formsearch" id="caripasienlama" onclick="cariPasienLama();" class="btn btn-primary" data-toggle="modal"><img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Pasien Lama</a>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal Trans</label>
              <div class="col-md-5 col-sm-5 col-xs-5">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="tgltrans" id="tgltrans" class="form-control pull-right">
                </div>
              </div>
              <div class="col-md-4 col-sm-4 col-xs-4">
                <div class="input-group">
                    <input type="text" name="jamtrans" id="jamtrans" class="form-control">
                    <span class="input-group-addon">
                        <img src="{!! asset('images/icon/time-icon.png') !!}" width="20" height="20">
                    </span>
                </div>
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
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <textarea name="alamat" id="alamat" class="form-control" rows="3" style="resize:none;" readonly></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kota</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kota" id="kota" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Kelamin</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="jk" id="jk" class="form-control col-md-7 col-xs-12" readonly>
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
            <hr>
            <div class="form-group">
              <div class="col-md-12 col-sm-12 col-xs-12">
              <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#editPasienModal" onclick="fillPasienModal();"><img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Edit Pasien</button>
              </div>
            </div>
        </div>
      </div>
      <!-- ====================================== End Data Pasien ==================================== -->

      <!-- ====================================== Penanggung Jawab ================================== -->
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Penanggung Jawab</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="pjbnama" id="pjbnama" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <textarea name="pjbalamat" id="pjbalamat" class="form-control" rows="3" style="resize:none;"></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Telepon</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="pjbtelp" id="pjbtelp" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
          </div>
      </div>
      <!-- ================================== End Penanggung Jawab ============================= -->
    </div>

    <div class="col-md-6 col-sm-12 col-xs-12">
      <!-- ====================================== Keluarga Terdekat ================================== -->
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Keluarga Terdekat</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kelnama" id="kelnama" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <textarea name="kelalamat" id="kelalamat" class="form-control" rows="3" style="resize:none;"></textarea>
              </div>
            </div>
          </div>
      </div>
      <!-- ================================== End Keluarga Terdekat ======================================= -->

      <!-- ====================================== Penjamin dan Hak Akses ================================== -->
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Penjamin & Hak Akses</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Pasien</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="jenispas" id="jenispas" class="form-control col-md-7 col-xs-12" readonly>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Penjamin</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="penjamin" id="penjamin" class="form-control col-md-7 col-xs-12">
                  @foreach($admvars as $penjamin)
                    @if($penjamin->TAdmVar_Seri == 'JENISPAS')
                      <option value="{{$penjamin->TAdmVar_Kode}}">{{$penjamin->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Hak Kelas</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="kelas" id="kelas" class="form-control col-md-7 col-xs-12">
                    <option value="S">Sesuai Kelas</option>

                    @foreach($kelas as $hakkelas)
                      <option value="{{$hakkelas->TKelas_Kode}}">{{$hakkelas->TKelas_Keterangan}}</option>
                    @endforeach

                </select>
              </div>
            </div>
          </div>
      </div>
      <!-- ================================== End Penjamin dan Hak Akses ============================= -->

      <!-- ================================== Prosedur Masuk & Dokter ==================================== -->
      <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Prosedur Masuk & Dokter</h3>
        </div>
        <div class="box-body">
           
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Prosedur</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="prosedur" id="prosedur" class="form-control col-md-7 col-xs-12">
                  @foreach($admvars as $masukpros)
                    @if($masukpros->TAdmVar_Seri == 'MASUKPROS')
                      <option value="{{$masukpros->TAdmVar_Kode}}" @if(!empty($datatrans->TRawatInap_ProsMasuk)) @if($datatrans->TRawatInap_ProsMasuk == $masukpros->TAdmVar_Kode) selected="selected" @endif @endif>{{$masukpros->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Dokter Utama</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="pelaku" id="pelaku" class="form-control col-md-7 col-xs-12">
                  @foreach($pelakus as $dokter)
                      <option value="{{$dokter->TPelaku_Kode}}" @if(!empty($datatrans->TPelaku_Kode)) @if($datatrans->TPelaku_Kode == $dokter->TPelaku_Kode) selected="selected" @endif @endif>{{$dokter->TPelaku_NamaLengkap}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            
        </div>
      </div>
      <!-- ================================== End Prosedur Masuk & Dokter =============================== -->

      <!-- ====================================== Ruang Perawatan ================================== -->
      <div class="box box-warning">
        <div class="box-header">
          <h3 class="box-title">Ruang Perawatan</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Ruang</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="ruang" id="ruang" class="form-control col-md-7 col-xs-12">

                  <?php 
                    $kdruang = '';

                    if(!empty($datatrans->TTmpTidur_Kode)){
                      $kdruang = substr($datatrans->TTmpTidur_Kode, 0, 3);
                    }
                  ?>

                  @foreach($ruangs as $ruang)
                    <option value="{{$ruang->TRuang_Kode}}" @if($kdruang == $ruang->TRuang_Kode) selected="selected" @endif>{{$ruang->TRuang_Nama}}</option>
                  @endforeach
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor TT</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="tmptidur" id="tmptidur" class="form-control col-md-7 col-xs-12">
                  <option value=""><i>--Silahkan Pilih Ruang--</i></option>
                  option                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kamar</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kamar" id="kamar" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#bedlayoutModal"><img src="{!! asset('images/icon/bed-icon.png') !!}" width="25" height="25"> Lihat Bed</button>
              </div>
            </div>
        </div>
      </div>
      <!-- ======================================= End Ruang Perawatan ========================================== -->
    </div> <!--div class="col-md-6 col-sm-12 col-xs-12"-->

  </div> <!-- row -->

  <!-- ================== -->

  <div class="modal fade" id="bedlayoutModal" tabindex="-1" role="dialog" aria-labelledby="bedlayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog font-small" role="document">
      <div class="modal-content">
        <div class="form-group col-md-12 col-sm-12 col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Denah Kamar</h3>
            </div>
            <div class="box-body">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="col-md-4 col-sm-4 col-xs-12">

                  @foreach($ruangs as $ruang)
                    <button type="button" class="btn btn-primary" style="margin-top: 2px;" onclick="refreshBed('{{$ruang->TRuang_Kode}}');">{{$ruang->TRuang_Nama}}</button>
                  @endforeach

                </div>
                <span id="layoutKmr"></span>
                
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    
  <!-- ================== -->
  <div class="row font-medium">
    <div class="form-group col-md-12 col-sm-12 col-xs-12">
      <div class="box">
        <div class="box-body">
          <div class="col-md-12 col-md-offset-4">
            <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
            <a href="/daftarinap/show" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</a>
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
        formSearch += '<div style="overflow-x: scroll; overflow-y:scroll;max-height:400px;">';
        formSearch += '<div id="hasil"></div>';
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
  @include('Partials.alertmodal')

  <!-- JQuery 1 -->
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

  <!-- Auto Complete Search Asset -->
  <script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

  <!-- Modal Searching Pasien Lama -->
  <script src="{{ asset('js/searchData.js') }}"></script>

  <!-- ============================================= End Of Content ====================================== -->

  @include('Partials.errors')

  <script type="text/javascript">

    $(function () {
        $('#mtgllahir').datepicker({
          autoclose: true
        });
    });

    $( document ).ready(function() {

      $("#layoutBed").hide();

      $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
      });          

      $('#tgltrans').datepicker({
        autoclose: true
      });

      fillData();

      $('#ruang').on('change', function(e){
        changeRuang();
      });

      $('#tmptidur').on('change', function(e){
        changeTTNomor();
      });

      changeRuang();

    });

    // ---------------------------------------------------------------------------------

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
        var pasienJenis     = $('#mjenispasien').val();
        var pasienAgama     = $('#magama').val();
        var pasienPend      = $('#mpendidikan').val();
        var pasienPek       = $('#mpekerjaan').val();
        var pasienKel       = $('#mkeluarga').val();

        $.ajax({
          type: 'POST',
          url: '/editpasien',
          data: {
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


    // -- Get TTmpTidur dan Kamar ------------------------------------------------------

    function changeRuang()
    {
      var kodeRuang = $('#ruang').val();
      
      $.get('/ajax-getttmptidurbyruang?kdruang='+kodeRuang, function(data){

        $('#tmptidur').empty();

        $.each(data, function(index, tmptidurObj){
          if(tmptidurObj.TTmpTidur_Nomor == '{{$datatrans->TTmpTidur_Kode}}'){
            $('#tmptidur').append('<option value="'+tmptidurObj.TTmpTidur_Nomor+'" selected="selected">'+tmptidurObj.TTmpTidur_Nama+'</option>');
          }else{
            $('#tmptidur').append('<option value="'+tmptidurObj.TTmpTidur_Nomor+'">'+tmptidurObj.TTmpTidur_Nama+'</option>');
          }
          
        });

        changeTTNomor();

      });
    }

    function changeTTNomor()
    {
      var kdtmptidur = $('#tmptidur').val();
      kdtmptidur = (kdtmptidur == null ? '000' : kdtmptidur.substring(5,8)); 

      $.get('/ajax-getkamarnama?ttnomor='+kdtmptidur, function(data){

        $('#kamar').val(data);

      });
    }

    // -- End Get TTmpTidur dan Kamar --------------------------------------------------


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

    function changePenjamin(jenispas){
      $.get('/ajax-tperusahaan?jenispas='+jenispas, function(data){

        $('#penjamin').empty();

        $.each(data, function(index, penjaminObj){
          $('#penjamin').append('<option value="'+penjaminObj.TPerusahaan_Kode+'">'+penjaminObj.TPerusahaan_Nama+'</option>');
        });

      });
    }

    function fillData(){

        $('#notrans').val('{{$datatrans->TRawatInap_NoAdmisi}}');
        $('#tgltrans').val('{{$tglMasuk->format('m/d/Y')}}');
        $('#jamtrans').val('{{$tglMasuk->format('H:i')}}');
        $('#pasiennorm').val('{{$datatrans->TPasien_NomorRM}}');
        $('#nama').val('{{$datatrans->TPasien_Nama}}');
        $('#alamat').val('{{$datatrans->TPasien_Alamat}}');
        $('#kota').val('{{$datatrans->TWilayah2_Nama}}');
        $('#jk').val('{{$datatrans->TAdmVar_Gender}}');
        $('#pasienumurthn').val('{{$datatrans->TRawatInap_UmurThn}}');
        $('#pasienumurbln').val('{{$datatrans->TRawatInap_UmurBln}}');
        $('#pasienumurhari').val('{{$datatrans->TRawatInap_UmurHr}}');
        $('#pjbnama').val('{{$datatrans->TRawatInap_PjawabNama}}');
        $('#pjbalamat').val('{{$datatrans->TRawatInap_PjawabAlamat}}');
        $('#pjbtelp').val('{{$datatrans->TRawatInap_PjawabKTP}}');
        $('#kelnama').val('{{$datatrans->TRawatInap_KlgNama}}');
        $('#kelalamat').val('{{$datatrans->TRawatInap_KlgAlamat}}');
        $('#jenispas').val('{{$datatrans->TAdmVar_Nama}}');

        changePenjamin('{{$datatrans->TAdmVar_Kode}}');

        $('#penjamin').val('{{$datatrans->TAdmVar_Kode}}');
    }

    function fillPasien(){
      var pasiennorm = $('#pasiennorm').val();
      var nomortrans = $('#notrans').val();

      $.get('/ajax-checkpasieninap?norm='+pasiennorm+'&notrans='+nomortrans, function(data){
        if(data > 0){

          showDialog('modalWarning', 'Pasien Masih Terdaftar Sebagai Pasien Rawat Inap !');
          $('#pasiennorm').val('');

        }else{

          $.get('/ajax-pasienbynorm?pasiennorm='+pasiennorm, function(data){

            $.each(data, function(index, pasienObj){
              var tgl       = new Date(pasienObj.TPasien_TglLahir);
              var tgllahir  = (tgl.getMonth()+1)+'/'+tgl.getDate()+'/'+tgl.getFullYear();

              document.getElementById('pasien_id').value = pasienObj.id;
              document.getElementById('nama').value = pasienObj.TPasien_Nama;
              document.getElementById('alamat').value = pasienObj.TPasien_Alamat;

              document.getElementById('kota').value = pasienObj.Kota;

              var umurPasien = hitungUmur(pasienObj.TPasien_TglLahir);

              if(isNaN(umurPasien['years'])){ umurPasien['years']   = 0; }
              if(isNaN(umurPasien['months'])){ umurPasien['months']   = 0; }
              if(isNaN(umurPasien['days'])){ umurPasien['days']   = 0; }

              document.getElementById('pasienumurthn').value  = umurPasien['years'];
              document.getElementById('pasienumurbln').value  = umurPasien['months'];
              document.getElementById('pasienumurhari').value = umurPasien['days'];

              document.getElementById('jk').value = (pasienObj.JK == null ? 'Laki-laki' : pasienObj.JK); 
              document.getElementById('tempjnspas').value = (pasienObj.TAdmVar_Jenis == null ? '0' : pasienObj.TAdmVar_Jenis); 
              document.getElementById('jenispas').value = pasienObj.JenisPasien;

              changePenjamin(pasienObj.TAdmVar_Jenis);   

            });
          });

        } // else if(data > 0){

      });

    }

    function fillPasienModal(){
      var pasiennorm = $('#pasiennorm').val();

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
          
          document.getElementById('magama').value = (pasienObj.tadmvar_agama == null ? '1' : pasienObj.tadmvar_agama);
          document.getElementById('mpendidikan').value = (pasienObj.TAdmVar_Pendidikan == null ? '1' : pasienObj.TAdmVar_Pendidikan);
          document.getElementById('mpekerjaan').value = (pasienObj.TAdmVar_Pekerjaan == null ? '1' : pasienObj.TAdmVar_Pekerjaan);  
          document.getElementById('mkeluarga').value = pasienObj.TPasien_KlgNama;   

          getKota2(pasienObj.TPasien_Kota, pasienObj.TPasien_Kecamatan, pasienObj.TPasien_Kelurahan);

          changePenjamin(pasienObj.TAdmVar_Jenis);

        });

      });
    }
    // ======================================= End AutoComplete ======================================

    function openLayoutBed(){
      $("#layoutBed").show();
    }

    function closeLayoutBed(kdtempat){
      $('#bedlayoutModal').modal('hide');
      $('#tmptidur').val(kdtempat);
      changeTTNomor();
    }

    function refreshBed(kdruang){

      var kodeRuang = kdruang;

      $('#ruang').val(kdruang);

      changeRuang();
      
      $.get('/ajax-getttmptidurbyruang?kdruang='+kodeRuang, function(data){

        var innrruang = '<div class="col-md-8 col-sm-8 col-xs-12">';

        $.each(data, function(index, tmptidurObj){

          if(tmptidurObj.TTmpTidur_InapNoAdmisi.length > 0){
            innrruang += '<button type="button" class="btn btn-danger" style="margin-top: 2px;margin-right: 2px;" title="Terisi : '+tmptidurObj.TTmpTidur_InapNoAdmisi+'">'+tmptidurObj.TTmpTidur_Nama+'</button>';
          }else{
            innrruang += '<button type="button" class="btn btn-success" style="margin-top: 2px;margin-right: 2px;" onclick="closeLayoutBed(\''+tmptidurObj.TTmpTidur_Nomor+'\');">'+tmptidurObj.TTmpTidur_Nama+'</button>';
          }

        });

        innrruang += '</div>';

        document.getElementById('layoutKmr').innerHTML = innrruang;

      });
    }

    function checkFormDaftarInap(){
      var norm  = $('#pasiennorm').val();
      var nama  = $('#nama').val();
      var pj    = $('#pjbnama').val();

      if(norm == '' || norm.toString().length < 6){
        showWarning(2000, '', 'Nomor RM Pasien Masih Kosong !', true);
        $('#pasiennorm').focus();
        return false;
      }else if(nama = '' || nama.toString().length < 1){
        showWarning(2000, '', 'Nama Pasien Belum Diisi !', true);
        $('#nama').focus();
        return false;
      }else if(pj = '' || pj.toString().length < 1){
        showWarning(2000, '', 'Penanggung Jawab Pasien Belum Ada !', true);
        $('#pjbnama').focus();
        return false;
      }else{
        return true;
      }
    }

  </script>

@endsection