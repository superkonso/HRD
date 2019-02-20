@extends('layouts.main')

@section('title', 'SMART BRIDGE - Poliklinik')

@section('content_header', 'Pendaftaran Poliklinik')

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
    } // if(!empty($tarifvars)){
  ?>

  <!-- ====================================================== Content ================================================================== -->
  <div class="row">
    <form class="form-horizontal form-label-left" action="/poli" method="post" data-parsley-validate>

      <!-- Token -->
      {{csrf_field()}}
      {{ Form::hidden('pasien_id', '0', array('id' => 'pasien_id')) }}
      {{ Form::hidden('pasbaru', 'L', array('id' => 'pasbaru')) }}
      {{ Form::hidden('tempNoRM', '', array('id' => 'tempNoRM')) }}
      {{ Form::hidden('tempNoTrans', '', array('id' => 'tempNoTrans')) }}
      {{ Form::hidden('temptStatus', 'C', array('id' => 'temptStatus')) }}
      {{ Form::hidden('tempUnit', '', array('id' => 'tempUnit')) }}
      {{ Form::hidden('tempNoUrut', '', array('id' => 'tempNoUrut')) }}

    <!-- ===================================== Data Pasien =========================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="x_panel">
         <div class="x_title">
          <h4>Data Pasien <small></small></h4>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">
          <br />

            <fieldset>
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
                <a href="#formsearch" id="caripasienlama" onclick="cariPasienLama();" class="btn btn-primary" data-toggle="modal"><i class="fa fa-bed"></i> Pasien Lama</a>
              <a href="/pasien" class="btn btn-success"><i class="fa fa-bed"></i> Pasien Baru</a>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor RM</label>
              <div class="col-md-6 col-sm-6 col-xs-6">
                <input type="text" name="pasiennorm" id="pasiennorm" class="form-control col-md-7 col-xs-12" value="">
              </div>
              <div class="col-md-2 col-sm-2 col-xs-2">
                {{-- <input type="text" name="jnspasien" id="jnspasien" class="form-control col-md-7 col-xs-12" value="L"> --}}
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama Pasien</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12" required="required">
              </div>
            </div>
        
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Panggilan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="panggilan" id="panggilan" class="form-control col-md-7 col-xs-12" value="">
              </div>
            </div>
            {{-- <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Kelamin</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="jk" id="jk" class="form-control col-md-7 col-xs-12">

                  @foreach($admvars as $gender)
                    @if ($gender->TAdmVar_Seri=='GENDER')
                      <option value="{{$gender->TAdmVar_Kode}}" @if($gender->TAdmVar_Kode=='L') selected @endif>{{$gender->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach

                </select>
              </div>
            </div> --}}

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
                <select name="provinsi" id="provinsi" class="form-control col-md-7 col-xs-12">

                  @foreach($provinsi as $prov)
                      <option value="{{$prov->TWilayah2_Kode}}">{{$prov->TWilayah2_Nama}}</option>
                  @endforeach

                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kota</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="kota" id="kota" class="form-control col-md-7 col-xs-12">
                  <option value="">-</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kecamatan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="kecamatan" id="kecamatan" class="form-control col-md-7 col-xs-12">
                  <option value="">-</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kelurahan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="kelurahan" id="kelurahan" class="form-control col-md-7 col-xs-12">
                  <option value="">-</option>
                </select>
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
                <select name="jenispas" id="jenispas" class="form-control col-md-7 col-xs-12">
                  @foreach($admvars as $jenispas)
                    @if ($jenispas->TAdmVar_Seri=='JENISPAS')
                      <option value="{{$jenispas->TAdmVar_Kode}}" @if($jenispas->TAdmVar_Kode=='0') selected @endif>{{$jenispas->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Agama</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="agama" id="agama" class="form-control col-md-7 col-xs-12">

                  @foreach($admvars as $agama)
                    @if ($agama->TAdmVar_Seri=='AGAMA')
                      <option value="{{$agama->TAdmVar_Kode}}" selected="">{{$agama->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach

                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Pendidikan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="pendidikan" id="pendidikan" class="form-control col-md-7 col-xs-12">

                  @foreach($admvars as $pendidikan)
                    @if ($pendidikan->TAdmVar_Seri=='PENDIDIKAN')
                      <option value="{{$pendidikan->TAdmVar_Kode}}" selected="">{{$pendidikan->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach

                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Pekerjaan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="pekerjaan" id="pekerjaan" class="form-control col-md-7 col-xs-12">

                  @foreach($admvars as $pekerjaan)
                    @if ($pekerjaan->TAdmVar_Seri=='PEKERJAAN')
                      <option value="{{$pekerjaan->TAdmVar_Kode}}" selected="">{{$pekerjaan->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach

                </select>
              </div>
            </div>
            
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Keluarga</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="keluarga" id="keluarga" class="form-control col-md-7 col-xs-12" value="">
              </div>
            </div>
            </fieldset>
        </div> <!-- <div class="x_content"> -->
      </div> <!-- <div class="x_panel"> -->
    </div> <!-- <div class="col-md-6 col-sm-12 col-xs-12"> -->

    <!-- ====================================== End Data Pasien ==================================== -->

    <!-- ====================================== Klinik dan Dokter ================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="x_panel">

        <div class="x_title">
          <h4>Klinik dan Dokter <small></small></h4>
          <div class="clearfix"></div>
        </div>
        
        <div class="x_content">
          <br />
            <fieldset>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Klinik Dituju</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="unit" id="unit" class="form-control col-md-7 col-xs-12">

                  @foreach($units as $unit)
                    <option value="{{$unit->id}}">{{$unit->TUnit_Nama}}</option>
                  @endforeach
                 
                </select>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Dokter</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="dokter" id="dokter" class="form-control col-md-7 col-xs-12">

                  @foreach($pelakus as $dokter)
                    <option value="{{$dokter->id}}">{{$dokter->TPelaku_NamaLengkap}}</option>
                  @endforeach
                 
                </select>
              </div>
            </div>
            </fieldset>
        </div> <!-- <div class="x_content"> -->

      </div> <!-- <div class="x_panel"> -->
    </div> <!-- <div class="col-md-6 col-sm-12 col-xs-12"> -->

    <!-- ======================================= End Klinik dan Dokter ========================================== -->

    <!-- ============================================ Rujukan =================================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="x_panel">

        <div class="x_title">
          <h4>Rujukan <small></small></h4>
          <div class="clearfix"></div>
        </div>

        <div class="x_content">
          <br />
            <fieldset>
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
        </fieldset>
      </div> <!-- <div class="x_panel"> -->
    </div> <!-- <div class="col-md-6 col-sm-12 col-xs-12"> -->
    <!-- ========================================= End Rujukan ================================================== -->

    <!-- ======================================= Biaya Pemeriksaan ============================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="x_title">
          <h4>Biaya Pemeriksaan <small></small></h4>
          
          <div class="clearfix"></div>
        </div>

        <div class="x_content">
          <br />
            <fieldset>
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Penjamin</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="penjamin" id="penjamin" class="form-control col-md-7 col-xs-12">

                  @foreach($admvars as $penjamin)
                    @if ($penjamin->TAdmVar_Seri=='JENISPAS')
                      <option value="{{$penjamin->TAdmVar_Kode}}">{{$penjamin->TAdmVar_Nama}}</option>
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
            </fieldset>
        </div> <!-- <div class="x_content"> -->
      </div> <!-- <div class="x_panel"> -->
    </div>
    <!-- ============================================ End Biaya Pemeriksaan ======================================= -->

    <!-- ========================================== Jumlah Biaya Pemeriksaan ====================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="x_panel">

        <div class="x_title">
          <h4>Jumlah Biaya Pemeriksaan <small></small></h4>
          <div class="clearfix"></div>
        </div>

        <div class="x_content">
          <fieldset>
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
          </fieldset>
        </div>
      </div>
    </div>
    <!-- ======================================== End Jumlah Biaya Pemeriksaan ==================================== -->

    <!-- ================== -->
    <div class="form-group col-md-12 col-sm-12 col-xs-12">
      <div class="x_panel">
        <div class="row">
          <div class="col-md-12 col-md-offset-5">
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
            <a href="#formsearch" class="btn btn-primary" onclick="cariDataPoli();" data-toggle="modal"><i class="fa fa-edit"></i> Edit</a>
            <a href="" class="btn btn-danger" onclick="resetForm();"><i class="fa fa-close"></i> Clear Form</a>
          </div>
        </div>
      </div>
    </div>
    <!-- ================== -->
    </form>
  </div>
  <div id="searchFrm">
    <span></span>
  </div>

  <script type="text/javascript">
    function cariDataPoli(){
      var formSearch = '';

      formSearch += '<div class="modal fade" id="formsearch" role="dialog">';
      formSearch += '<div class="modal-dialog">';
      formSearch += '<div class="modal-content">';
      formSearch += '<div class="modal-header">';
      formSearch += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
      formSearch += '<h4 class="modal-title">Data Transaksi Poli</h4>';
      formSearch += '</div>';
      formSearch += '<div class="modal-body">';
      formSearch += '<div class="pull-right"> Cari : <input type="text" id="cdatapoli" name="cdatapoli" onkeyup="cdatapoliKU(this.value)"></input></div>';
      formSearch += '<div>';
      formSearch += '<div id="hasil"></div>';
      formSearch += '</div>';
      formSearch += '</div>';
      formSearch += '<div class="modal-footer">';
      formSearch += '<button type="button" class="btn btn-default" data-dismiss="modal" onclick="pilihPoli()"><img src="{!! asset('images/icon/checklist-icon.png') !!}" width="20" height="20"> Pilih</button>';
      formSearch += '</div></div></div></div>';

      document.getElementById('searchFrm').innerHTML = formSearch;

      cTransPoli();
    }

    function cariPasienLama(){

      var formSearch = '';

      formSearch += '<div class="modal fade" id="formsearch" role="dialog">';
      formSearch += '<div class="modal-dialog">';
      formSearch += '<div class="modal-content">';
      formSearch += '<div class="modal-header">';
      formSearch += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
      formSearch += '<h4 class="modal-title">Data Pasien</h4>';
      formSearch += '</div>';
      formSearch += '<div class="modal-body">';
      formSearch += '<div class="pull-right"> Cari : <input type="text" id="cdatapasien" name="cdatapasien" onkeyup="cdatapasienKU(this.value)"></input></div>';
      formSearch += '<div>';
      formSearch += '<div id="hasil"></div>';
      formSearch += '</div>';
      formSearch += '</div>';
      formSearch += '<div class="modal-footer">';
      formSearch += '<button type="button" class="btn btn-default" data-dismiss="modal" onclick="pilihPasien()"><img src="{!! asset('images/icon/checklist-icon.png') !!}" width="20" height="20"> Pilih</button>';
      formSearch += '</div></div></div></div>';

      document.getElementById('searchFrm').innerHTML = formSearch;

      cPasienLama();
    }

  </script>

  {{-- @include('search') --}}

  <!-- JQuery 1 -->
  <script src="{{ asset('js/jquery.min.js') }}"></script>

  <!-- Auto Complete Search Asset -->
  <script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

  <!-- Modal Searching Pasien Lama -->
  <script src="{{ asset('js/searchData.js') }}"></script>

  <!-- ============================================= End Of Content ================================================ -->

  @include('Partials.errors')

  <script type="text/javascript">

  // ---------------------------- Ready Function ---------------------------------------
    function changePenjamin(){
      var jenispas = $('#jenispas').val();

      $.get('/ajax-tperusahaan?jenispas='+jenispas, function(data){

        $('#penjamin').empty();

        $.each(data, function(index, penjaminObj){
          $('#penjamin').append('<option value="'+penjaminObj.id+'">'+penjaminObj.TPerusahaan_Nama+'</option>');
        });

      });
    }

    $( document ).ready(function() {

      var JLB = 0;
      var JLL = 0;
      var CDB = 0;

      JLB = <?php echo $JLB; ?>;
      JLL = <?php echo $JLL; ?>;
      CDB = <?php echo $CDB; ?>;

      JLB = formatRibuan(parseInt(JLB));
      JLL = formatRibuan(parseInt(JLL));
      CDB = formatRibuan(parseInt(CDB));

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

      $('#jenispas').on('change', function(e){

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

      }
    });

    $('#pasiennorm').on('change', function(e){
      fillPasien();

    });

    function fillTransPoli(notrans){

      $.get('/ajax-polibynotrans?notrans='+notrans, function(data){
      $.each(data, function(index, poliObj){

          document.getElementById('temptStatus').value = 'E';
          document.getElementById('tempUnit').value = poliObj.TUnit_id;
          document.getElementById('tempNoUrut').value = poliObj.TRawatJalan_NoUrut;

          document.getElementById('notrans').value    = poliObj.TRawatJalan_NoReg;
          document.getElementById('noantri').value    = poliObj.TRawatJalan_NoUrut;
          document.getElementById('pasien_id').value  = poliObj.TPasien_id;
          document.getElementById('pasiennorm').value = poliObj.TPasien_NomorRM;
          document.getElementById('nama').value       = poliObj.TPasien_Nama;
          document.getElementById('panggilan').value  = poliObj.TPasien_Panggilan;
          document.getElementById('alamat').value     = poliObj.TPasien_Alamat;

          document.getElementById('kelurahan').value  = poliObj.TPasien_Kelurahan;
          document.getElementById('kecamatan').value  = poliObj.TPasien_Kecamatan;
          document.getElementById('kota').value       = poliObj.TPasien_Kota;
          document.getElementById('telepon').value    = poliObj.TPasien_Telp;
          
          document.getElementById('pendidikan').value = poliObj.TAdmVar_Pendidikan;
          document.getElementById('pekerjaan').value  = poliObj.TPasien_Kerja;
          document.getElementById('keluarga').value   = poliObj.TPasien_KlgNama;

          var umurPasien = hitungUmur(poliObj.TPasien_TglLahir);

          if(isNaN(umurPasien['years'])){ umurPasien['years']   = 0; }
          if(isNaN(umurPasien['months'])){ umurPasien['months']   = 0; }
          if(isNaN(umurPasien['days'])){ umurPasien['days']   = 0; }

          document.getElementById('pasienumurthn').value  = umurPasien['years'];
          document.getElementById('pasienumurbln').value  = umurPasien['months'];
          document.getElementById('pasienumurhari').value = umurPasien['days'];

          document.getElementById('jk').value         = poliObj.TAdmVar_Gender.trim();  
          document.getElementById('agama').value      = (poliObj.tadmvar_agama == null ? 1 : poliObj.tadmvar_agama);
          document.getElementById('pendidikan').value = (poliObj.TAdmVar_Pendidikan == null ? '1' : poliObj.TAdmVar_Pendidikan);
          document.getElementById('pekerjaan').value  = (poliObj.TPasien_Kerja == null ? '1' : poliObj.TAdmVar_Pekerjaan);
          document.getElementById('jenispas').value   = poliObj.TAdmVar_Jenis;

          document.getElementById('provinsi').value   = poliObj.TPasien_Prov;
          document.getElementById('unit').value       = poliObj.TUnit_id;
          document.getElementById('rujukan').value    = poliObj.TRawatJalan_AsalPasien;
          document.getElementById('ketrujukan').value = poliObj.TRawatJalan_RujukanDari;

          autoPelakuByUnit(poliObj.TUnit_id, poliObj.TPelaku_id);

          getKota(poliObj.TPasien_Kota, poliObj.TPasien_Kecamatan, poliObj.TPasien_Kelurahan)

          changePenjamin();

          checkPasienLamaBaru(poliObj.TPasien_NomorRM); 

        });

      });

    }

    function fillPasien(){

      var pasiennorm = $('#pasiennorm').val();

      // --ajax proses--
      $.get('/ajax-pasienbynorm?pasiennorm='+pasiennorm, function(data){

        $.each(data, function(index, pasienObj){
          var tgl       = new Date(pasienObj.TPasien_TglLahir);
          var tgllahir  = Date.parse(tgl).toString("MM/dd/yyyy");

          document.getElementById('pasien_id').value = pasienObj.id;
          document.getElementById('nama').value = pasienObj.TPasien_Nama;
          document.getElementById('panggilan').value = pasienObj.TPasien_Panggilan;
          document.getElementById('alamat').value = pasienObj.TPasien_Alamat;

          document.getElementById('kelurahan').value = pasienObj.TPasien_Kelurahan;
          document.getElementById('kecamatan').value = pasienObj.TPasien_Kecamatan;
          document.getElementById('kota').value = pasienObj.TPasien_Kota;
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

          document.getElementById('jk').value = pasienObj.TAdmVar_Gender.trim();  
          document.getElementById('agama').value = (pasienObj.tadmvar_agama == null ? 1 : pasienObj.tadmvar_agama);
          document.getElementById('pendidikan').value = (pasienObj.TAdmVar_Pendidikan == null ? '1' : pasienObj.TAdmVar_Pendidikan);
          document.getElementById('pekerjaan').value = (pasienObj.TPasien_Kerja == null ? '1' : pasienObj.TAdmVar_Pekerjaan);
          document.getElementById('jenispas').value = pasienObj.TAdmVar_Jenis;

          document.getElementById('provinsi').value = pasienObj.TPasien_Prov;

          getKota(pasienObj.TPasien_Kota, pasienObj.TPasien_Kecamatan, pasienObj.TPasien_Kelurahan)

          changePenjamin();

          checkPasienLamaBaru(pasienObj.TPasien_NomorRM);     

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
      //var unit_id = $('#unit').val();
      autoPelakuByUnit($('#unit').val());

    });

    function autoPelakuByUnit(kdUnit, kdPelaku=0){
      $.get('/ajax-pelaku?unit_id='+kdUnit, function(data){

        $('#dokter').empty();

        $.each(data, function(index, pelakuObj){
          if(kdPelaku>0){
            if(pelakuObj.id == kdPelaku){
              $('#dokter').append('<option value="'+pelakuObj.id+'" selected="selected">'+pelakuObj.TPelaku_NamaLengkap+'</option>');
            }else{
              $('#dokter').append('<option value="'+pelakuObj.id+'">'+pelakuObj.TPelaku_NamaLengkap+'</option>');
            }
          }else{
            $('#dokter').append('<option value="'+pelakuObj.id+'">'+pelakuObj.TPelaku_NamaLengkap+'</option>');
          }
        });

      });

      var statusEdit  = $('#temptStatus').val();
      var tempUnit    = $('#tempUnit').val();
      var tempNoUrut  = $('#tempNoUrut').val();

      if(statusEdit == 'E'){
        if(kdUnit == tempUnit){
          $('#noantri').val(tempNoUrut);
        }else{
          $.get('/ajax-getautonumbertrans?unit_id='+kdUnit, function(data){
            $('#noantri').val(data);
          });
        }
      }else{
        $.get('/ajax-getautonumbertrans?unit_id='+kdUnit, function(data){
          $('#noantri').val(data);
        });
      }
    }
    // =================================================================================

    $('#jenispas').on('change', function(e){
      
      changePenjamin();

    });

    $('#ditanggung').on('change', function(e){
      
      hitungBiaya();

    });

    var unit_id = $('#unit').val();

    $.get('/ajax-getautonumbertrans?unit_id='+unit_id, function(data){

        $('#noantri').val(data);

      });

    function hitungBiaya(){
      var isPribadi     = true;
      var jmlpribadi    = 0;
      var jmlditanggung = 0;

      if($('#jenispas').val() > 0 && $('#ditanggung').val() == 'S'){ 
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