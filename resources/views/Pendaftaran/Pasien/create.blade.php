@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Pasien')

@section('content_header', 'Input Data Pasien')

@section('header_description', '')

@section('menu_desc', 'Pasien')

@section('link_menu_desc', '/pasien')

@section('sub_menu_desc', 'Create')

@section('content')

@include('Partials.message')


<div class="row backgroundform">
  <img src="{!! asset('images/BackgroundNonlab.jpg') !!}">
</div>

<div class="row">
  <form class="form-horizontal form-label-left" action="/pasien" id="frmpasien" method="post" data-parsley-validate onsubmit="return checkFormPasien()">
    
    {{csrf_field()}}
    {{ Form::hidden('pasien_id', 0, array('id' => 'pasien_id')) }}

    <!-- ===================================== Form Pasien =========================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary"  style="opacity: 0.9;">
        <div class="box-header">
          {{-- <h3 class="box-title">Form Pasien</h3> --}}
        </div>
        <div class="box-body">

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor RM</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input type="text" id="NomorRM" class="form-control col-md-7 col-xs-12" name="NomorRM" value="{{$autoNumber}}" readonly>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">Title</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="title" id="title" class="form-control">
                  @foreach($Title as $admvar)
                    <option value="{{$admvar->TAdmVar_Kode}}">{{$admvar->TAdmVar_Nama}}</option>
                  @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="nama">Nama</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama Pasien"> 
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="panggilan">Nama Panggilan
            </label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input type="text" id="panggilan" name="panggilan" placeholder="Panggilan" class="form-control col-md-7 col-xs-12" value="" >
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="alamat">Alamat</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
             <textarea type="text" id="alamat" class="form-control" name="alamat" rows="5" style="resize:none;"></textarea>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">RT/RW </label>
            <div class="col-md-4 col-sm-4 col-xs-4">
              <div class="input-group date">
                <div class="input-group-addon">
                  RT
                </div>
                <input type="text" id="rt" class="form-control col-md-7 col-xs-12" name="rt" value="">
              </div>
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4">
              <div class="input-group date">
                <div class="input-group-addon">
                  RT
                </div>
                <input type="text" id="rw" class="form-control col-md-7 col-xs-12" name="rw" value="">
              </div>
            </div>
           </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="wilayah">Provinsi</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="wilayah" id="provinsi" class="form-control">
                @foreach($wilayah1s as $wilayah)
                <option value="{{$wilayah->TWilayah2_Kode}}">{{$wilayah->TWilayah2_Nama}}</option>
              @endforeach
              </select> 
            </div>
          </div>

           <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="wilayah2">Kota</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="wilayah2" id="kota" class="form-control">
                <option value=""> -- </option>
              </select>
            </div> 
          </div>
             
          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="wilayah3">Kecamatan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="wilayah3" id="kecamatan" class="form-control">
                <option value=""> -- </option>
              </select>
            </div> 
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="kelurahan">Kelurahan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="kelurahan" id="kelurahan" class="form-control">
                <option value=""> -- </option>
              </select>
            </div> 
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="Tmplahir">Tempat lahir</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input type="text" id="tmplahir" name="tmplahir" placeholder="Tempat lahir" class="form-control col-md-7 col-xs-12" value="">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal Lahir (MM/dd/yyyy)</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                </div>
                <input type="text" name="tgllahir" class="form-control pull-right" id="single_cal1" value="" onchange="hitungUmurInsertForm('single_cal1', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');">
              </div>
           </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">Pasien Umur </label>
            <div class="col-md-3 col-sm-3 col-xs-12">
              <input type="text" id="pasienumurthn" name="pasienumurthn" value="0" class="form-control col-md-7 col-xs-12" onfocus="hitungUmurInsertForm('single_cal1', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');"> Tahun
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
              <input type="text" id="pasienumurbln" name="pasienumurbln" value="0" class="form-control col-md-7 col-xs-12"> Bulan
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
              <input type="text" id="pasienumurhari" name="pasienumurhari" value="0" class="form-control col-md-7 col-xs-12"> Hari
            </div>
          </div>                  
                      
          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Kelamin</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <div id="jk" class="btn-group" data-toggle="buttons">
              <p>
                <input type="radio" name="jk" id="genderL" value="L" checked="checked"> Laki-laki 
                <input type="radio" name="jk" id="genderP" value="P"> Perempuan 
              </p>
              </div>
            </div>
          </div>


        </div> <!-- div class="box-body" -->
      </div> <!-- div class="box box-primary" -->
    </div> <!-- div class="col-md-6 col-sm-12 col-xs-12" -->

    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary" style="opacity: 0.9;">
        <div class="box-header">
          {{-- <h3 class="box-title">Form Pasien</h3> --}}
        </div>
        <div class="box-body">

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="jenispasien">Jenis Pasien</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="jenispasien" id="jenispasien" class="form-control">
               @foreach($jenispasienS as $jenispasien)
                <option value="{{$jenispasien->TAdmVar_Kode}}">{{$jenispasien->TAdmVar_Nama}}</option>
              @endforeach
              </select>
            </div> 
          </div>
         
          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="kawin">Kawin</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="kawin" id="kawin" class="form-control">
                @foreach($AdmVarsKwn as $AdmVarsKwn)
                   <option value="{{$AdmVarsKwn->TAdmVar_Kode}}">{{$AdmVarsKwn->TAdmVar_Nama}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="telepon" class="control-label col-md-3 col-sm-3 col-xs-3">Telepon</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="telepon" class="form-control col-md-7 col-xs-12" type="text" name="telepon" placeholder="Telepon"  value="" >
            </div>
          </div>

          <div class="form-group">
            <label for="telepon" class="control-label col-md-3 col-sm-3 col-xs-3">No. HP</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="HP" class="form-control col-md-7 col-xs-12" type="text" name="HP" placeholder="HP" value="">
            </div>
          </div>

          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="agama">Agama</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="agama" id="agama" class="form-control">
              @foreach($AdmVarsAgama as $AdmVarsAgama)
                   <option value="{{$AdmVarsAgama->TAdmVar_Kode}}">{{$AdmVarsAgama->TAdmVar_Nama}}</option>
              @endforeach
             </select>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="darah">Darah</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="darah" id="darah" class="form-control">
              @foreach($AdmVarsDarah as $AdmVarsDarah)
                   <option value="{{$AdmVarsDarah->TAdmVar_Kode}}">{{$AdmVarsDarah->TAdmVar_Nama}}</option>
              @endforeach
             </select>
            </div>
          </div>

          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-3">No KTP</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="ktp" class="form-control col-md-7 col-xs-12" type="text" name="ktp" placeholder="KTP" value="">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="pendidikan">Pendidikan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="pendidikan" id="pendidikan">
                  <option value="">--</option>
              @foreach($AdmVarsPendidikan as $AdmVarsPendidikan)
                  <option value="{{$AdmVarsPendidikan->TAdmVar_Kode}}">{{$AdmVarsPendidikan->TAdmVar_Nama}}</option>
              @endforeach
             </select>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="pekerjaan">Pekerjaan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="pekerjaan" id="pekerjaan">
                  <option value="">--</option>
              @foreach($AdmVarsPekerjaan as $AdmVarsPekerjaan)
                  <option value="{{$AdmVarsPekerjaan->TAdmVar_Kode}}">{{$AdmVarsPekerjaan->TAdmVar_Nama}}</option>
              @endforeach
             </select>
            </div>
          </div>
       
          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-3">Nama Pekerjaan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="subkerja" class="form-control col-md-7 col-xs-12" type="text" name=" subkerja" placeholder="Nama Pekerjaan" value="">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="alamatkerja">Alamat Pekerjaan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
             <textarea type="text" id="alamatkerja" class="form-control" name="alamatkerja" rows="4" style="resize:none;"></textarea>
            </div>
          </div>

          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-3">Nama Keluarga</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="namakeluarga" class="form-control col-md-7 col-xs-12" type="text" name="namakeluarga" placeholder="Nama Keluarga" value="">
            </div>
          </div>

          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-3">Alamat Keluarga</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="alamatkeluarga" class="form-control col-md-7 col-xs-12" type="text" name="alamatkeluarga" placeholder="Alamat Keluarga" value="">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="hubungankel">Hubungan Keluarga</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="hubungankel" id="hubungankel" class="form-control">
              @foreach($AdmVarsKeluarga as $AdmVarsKeluarga)
                   <option value="{{$AdmVarsKeluarga->TAdmVar_Kode}}">{{$AdmVarsKeluarga->TAdmVar_Nama}}</option>
              @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="telponkel" class="control-label col-md-3 col-sm-3 col-xs-3">Telepon Keluarga</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="telponkel" class="form-control col-md-7 col-xs-12" type="text" name="telponkel" placeholder="Telepon Keluarga" value="">
            </div>
          </div>

        </div> <!-- div class="box-body" -->
      </div> <!-- div class="box box-primary" -->
    </div> <!-- div class="col-md-6 col-sm-12 col-xs-12" -->

</div> <!-- div class="row" -->

<div class="row">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="">
      <div class="">
        <div class="col-md-12 col-md-offset-5">
          <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Save</button>
          <a href="/pasien" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Cancel</a>
        </div>
      </div>
    </div>
  </div>
</div>  

</form>

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

<!-- Auto Complete Search Asset -->
<script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
<script src="{{ asset('js/jquery-ui.js') }}"></script>

<script src="{{ asset('js/globFunction.js') }}"></script>

<script type="text/javascript">

  $( document ).ready(function() {

    $('#single_cal1').datepicker({
      autoclose: true
    });

    $('#pendidikan, #pekerjaan').selectize();

    <?php if(!empty($pasiens->TPasien_Prov)){ ?>
        var kdProv = {{$pasiens->TPasien_Prov}}

        getKota({{$pasiens->TPasien_Kota}}, {{$pasiens->TPasien_Kecamatan}}, {{$pasiens->TPasien_Kelurahan}});

    <?php } ?>

    var umurThn = $('#pasienumurthn').val();

    if(umurThn == 'NaN' || umurThn == ''){
      $('#pasienumurthn').val('0');
      $('#pasienumurbln').val('0');
      $('#pasienumurhari').val('0');
    }

  });

  hitungUmurInsertForm('single_cal1', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');

  function checkFormPasien(){
      var norm    = $('#NomorRM').val();
      var nama    = $('#nama').val();
      var tgllhr  = $('#single_cal1').val();

      if(norm == '' || norm.toString().length < 6){
        showWarning(2000, '', 'Nomor RM Masih Kosong !', true);
        $('#NomorRM').focus();
        return false;
      }else if(nama == '' || nama.toString().length < 1){
        showWarning(2000, '', 'Silahkan Isi Nama Pasien !', true);
        $('#nama').focus();
        return false;
      }else if(tgllhr == '' || tgllhr.toString().length < 10){
        showWarning(2000, '', 'Tanggal Lahir Pasien Masih Kosong !', true);
        $('#single_cal1').focus();
        return false;
      }else{
        return true;
      }
    }

</script>
     
@endsection

