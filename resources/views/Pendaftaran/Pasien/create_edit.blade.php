@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Pasien')

@section('content_header', 'Create Pasien')

@section('header_description', '')

@section('menu_desc', 'pasien')

@section('link_menu_desc', '/pasien')

@section('sub_menu_desc', 'create')

@section('content')

@include('Partials.message')


<div class="row">
  <form class="form-horizontal form-label-left" action="/pasien/@yield('editId')" id="frmpasien" method="post" novalidate>
    {{csrf_field()}}
    {{ Form::hidden('pasien_id', 0, array('id' => 'pasien_id')) }}
       
    @section('editMethod')
      @show


    <!-- ===================================== Form Pasien =========================================== -->
    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary">
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
              <select name="title" class="form-control col-md-7 col-xs-12">
                  @foreach($Title as $admvar)
                    <option value="{{$admvar->TAdmVar_Kode}}" @if(!empty($pasiens->TPasien_Title)) @if($admvar->TAdmVar_Kode == trim($pasiens->TPasien_Title)) selected="selected" @endif @endif>{{$admvar->TAdmVar_Nama}}</option>
                  @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="nama">Nama</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input type="text" id="nama" class="form-control col-md-7 col-xs-12" name="nama" placeholder="Nama" value="@yield('nama')" required="required"> 
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="panggilan">Nama Panggilan
            </label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input type="text" id="panggilan" name="panggilan" placeholder="Panggilan" class="form-control col-md-7 col-xs-12" value="@yield('panggilan')" >
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="alamat">Alamat</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
             <textarea id="alamat" class="form-control" name="alamat" rows="5" style="resize:none;">@yield('alamat')</textarea>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">RT/RW </label>
            <div class="col-md-4 col-sm-4 col-xs-4">
              <input type="rt" id="rt" class="form-control col-md-7 col-xs-12" name="rt" value="@yield('rt')");"> RT
            </div>
            <div class="col-md-4 col-sm-4 col-xs-4">
              <input type="rw" id="rw" class="form-control col-md-7 col-xs-12" name="rw" value="@yield('rw')"> RW
            </div>
           </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="wilayah">Provinsi</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="wilayah" id="provinsi" class="form-control col-md-7 col-xs-12" >
                @foreach($wilayah1s as $wilayah)
                <option value="{{$wilayah->TWilayah2_Kode}}" @if(!empty($pasiens->TPasien_Prov)) @if($wilayah->TWilayah2_Kode == $pasiens->TPasien_Prov) selected="selected" @endif @endif>{{$wilayah->TWilayah2_Nama}}</option>
              @endforeach
              </select> 
            </div>
          </div>

           <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="wilayah2">Kota</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="wilayah2" id="kota" class="form-control col-md-7 col-xs-12">

              <option value=""> -- </option>

                {{-- @foreach($wilayah2s as $wilayah2)
                  <option value="{{$wilayah2->TWilayah2_Kode}}" @if(!empty($pasiens->TPasien_Kota)) @if($wilayah2->TWilayah2_Kode == $pasiens->TPasien_Kota) selected="selected" @endif @endif>{{$wilayah2->TWilayah2_Nama}}</option>
                @endforeach --}}
              </select>
            </div> 
          </div>
             
          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="wilayah3">Kecamatan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="wilayah3" id="kecamatan" class="form-control col-md-7 col-xs-12">
                <option value=""> -- </option>
               {{-- @foreach($wilayah3s as $wilayah3)
                <option value="{{$wilayah3->TWilayah2_Kode}}" @if(!empty($pasiens->TPasien_Kecamatan)) @if($wilayah3->TWilayah2_Kode == $pasiens->TPasien_Kecamatan) selected="selected" @endif @endif>{{$wilayah3->TWilayah2_Nama}}</option>
                @endforeach --}}
              </select>
            </div> 
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="kelurahan">Kelurahan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="kelurahan" id="kelurahan" class="form-control col-md-7 col-xs-12">
                <option value=""> -- </option>
              {{-- @foreach($wilayah4s as $wilayah4)
                  <option value="{{$wilayah4->TWilayah2_Kode}}" @if(!empty($pasiens->TPasien_Kelurahan)) @if($wilayah4->TWilayah2_Kode == $pasiens->TPasien_Kelurahan) selected="selected" @endif @endif>{{$wilayah4->TWilayah2_Nama}}</option>
                @endforeach --}}
              </select>
            </div> 
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="Tmplahir">Tempat lahir</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input type="text" id="tmplahir" name="tmplahir" placeholder="Tempat lahir" class="form-control col-md-7 col-xs-12" value="@yield('tmplahir')">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal Lahir (MM/dd/yyyy)</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                </div>
                <input type="text" name="tgllahir" class="form-control pull-right" id="single_cal1" value="@yield('tgllahir')" onchange="hitungUmurInsertForm('single_cal1', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');">
              </div>
           </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-12">Pasien Umur </label>
            <div class="col-md-3 col-sm-3 col-xs-12">
              <input type="text" id="pasienumurthn" class="form-control col-md-7 col-xs-12" name="pasienumurthn" value="@yield('pasienumurthn')" onfocus="hitungUmurInsertForm('single_cal1', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');"> Tahun
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
              <input type="text" id="pasienumurbln" class="form-control col-md-7 col-xs-12" name="pasienumurbln" value="@yield('pasienumurbln')"> Bulan
            </div>
            <div class="col-md-3 col-sm-3 col-xs-12">
              <input type="text" id="pasienumurhari" class="form-control col-md-7 col-xs-12" name="pasienumurhari" value="@yield('pasienumurhari')"> Hari
            </div>
          </div>                  
                      
          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Kelamin</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <div id="jk" class="btn-group" data-toggle="buttons">
              <p>
                <input type="radio" name="jk" id="genderL" value="L" @if(!empty($pasiens->TAdmVar_Gender)) @if($pasiens->TAdmVar_Gender == "L") checked="checked" @endif @else checked="checked" @endif> Laki-laki 
                <input type="radio" name="jk" id="genderP" value="P" @if(!empty($pasiens->TAdmVar_Gender)) @if($pasiens->TAdmVar_Gender != "L") checked="checked" @endif @endif> Perempuan 
              </p>
              </div>
            </div>
          </div>


        </div> <!-- div class="box-body" -->
      </div> <!-- div class="box box-primary" -->
    </div> <!-- div class="col-md-6 col-sm-12 col-xs-12" -->

    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary">
        <div class="box-header">
          {{-- <h3 class="box-title">Form Pasien</h3> --}}
        </div>
        <div class="box-body">

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="jenispasien">Jenis Pasien</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="jenispasien" class="form-control col-md-7 col-xs-12">
               @foreach($jenispasienS as $jenispasien)
                <option value="{{$jenispasien->TAdmVar_Kode}}" @if(!empty($pasiens->TAdmVar_Jenis)) @if($jenispasien->TAdmVar_Kode == trim($pasiens->TAdmVar_Jenis)) selected="selected" @endif @endif>{{$jenispasien->TAdmVar_Nama}}</option>
              @endforeach
              </select>
            </div> 
          </div>
         
          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="kawin">Kawin</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="kawin" class="form-control col-md-7 col-xs-12">
                @foreach($AdmVarsKwn as $AdmVarsKwn)
                   <option value="{{$AdmVarsKwn->TAdmVar_Kode}}" @if(!empty($pasiens->TAdmVar_Kawin)) @if($AdmVarsKwn->TAdmVar_Kode == trim($pasiens->TAdmVar_Kawin)) selected="selected" @endif @endif>{{$AdmVarsKwn->TAdmVar_Nama}}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="telepon" class="control-label col-md-3 col-sm-3 col-xs-3">Telepon</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="telepon" class="form-control col-md-7 col-xs-12" type="text" name="telepon" placeholder="Telepon"  value="@yield('telepon')" >
            </div>
          </div>

          <div class="form-group">
            <label for="telepon" class="control-label col-md-3 col-sm-3 col-xs-3">No. HP</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="HP" class="form-control col-md-7 col-xs-12" type="text" name="HP" placeholder="HP"  value="@yield('HP')" >
            </div>
          </div>

          <div class="item form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="agama">Agama</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="agama" class="form-control col-md-7 col-xs-12">
              @foreach($AdmVarsAgama as $AdmVarsAgama)
                   <option value="{{$AdmVarsAgama->TAdmVar_Kode}}" @if(!empty($pasiens->tadmvar_agama))  @if($AdmVarsAgama->TAdmVar_Kode == trim($pasiens->tadmvar_agama))selected="selected" @endif @endif>{{$AdmVarsAgama->TAdmVar_Nama}}</option>
              @endforeach
             </select>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="darah">Darah</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="darah" class="form-control col-md-7 col-xs-12">
              @foreach($AdmVarsDarah as $AdmVarsDarah)
                   <option value="{{$AdmVarsDarah->TAdmVar_Kode}}" @if(!empty($pasiens->TAdmVar_Darah))  @if($AdmVarsDarah->TAdmVar_Kode == trim($pasiens->TAdmVar_Darah))selected="selected" @endif @endif>{{$AdmVarsDarah->TAdmVar_Nama}}</option>
              @endforeach
             </select>
            </div>
          </div>

          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-3">No KTP</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="ktp" class="form-control col-md-7 col-xs-12" type="text" name="ktp" placeholder="KTP" value="@yield('ktp')">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="pendidikan">Pendidikan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="pendidikan" class="form-control col-md-7 col-xs-12">
              @foreach($AdmVarsPendidikan as $AdmVarsPendidikan)
                   <option value="{{$AdmVarsPendidikan->TAdmVar_Kode}}" @if(!empty($pasiens->TAdmVar_Pendidikan))  @if($AdmVarsPendidikan->TAdmVar_Kode == trim($pasiens->TAdmVar_Pendidikan))selected="selected" @endif @endif>{{$AdmVarsPendidikan->TAdmVar_Nama}}</option>
              @endforeach
             </select>
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="pekerjaan">Pekerjaan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="pekerjaan" class="form-control col-md-7 col-xs-12">
              @foreach($AdmVarsPekerjaan as $AdmVarsPekerjaan)
                   <option value="{{$AdmVarsPekerjaan->TAdmVar_Kode}}" @if(!empty($pasiens->TPasien_Kerja))  @if($AdmVarsPekerjaan->TAdmVar_Kode == trim($pasiens->TPasien_Kerja))selected="selected" @endif @endif>{{$AdmVarsPekerjaan->TAdmVar_Nama}}</option>
              @endforeach
             </select>
            </div>
          </div>
       
          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-3">Nama Pekerjaan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="subkerja" class="form-control col-md-7 col-xs-12" type="text" name=" subkerja" placeholder="Nama Pekerjaan" value="@yield('subkerja')">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="alamatkerja">Alamat Pekerjaan</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
             <textarea id="alamatkerja" class="form-control" name="alamatkerja" rows="3" style="resize:none;">@yield('alamatkerja')</textarea>
            </div>
          </div>

          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-3">Nama Keluarga</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="namakeluarga" class="form-control col-md-7 col-xs-12" type="text" name="namakeluarga" placeholder="Nama Keluarga" value="@yield('namakeluarga')">
            </div>
          </div>

          <div class="form-group">
            <label for="middle-name" class="control-label col-md-3 col-sm-3 col-xs-3">Alamat Keluarga</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="alamatkeluarga" class="form-control col-md-7 col-xs-12" type="text" name="alamatkeluarga" placeholder="Alamat Keluarga" value="@yield('alamatkeluarga')">
            </div>
          </div>

          <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3" for="hubungankel">Hubungan Keluarga</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <select name="hubungankel" class="form-control col-md-7 col-xs-12">
              @foreach($AdmVarsKeluarga as $AdmVarsKeluarga)
                   <option value="{{$AdmVarsKeluarga->TAdmVar_Kode}}" @if(!empty($pasiens->TAdmVar_Keluarga))  @if($AdmVarsKeluarga->TAdmVar_Kode == trim($pasiens->TAdmVar_Keluarga))selected="selected" @endif @endif>{{$AdmVarsKeluarga->TAdmVar_Nama}}</option>
              @endforeach
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="telponkel" class="control-label col-md-3 col-sm-3 col-xs-3">Telepon Keluarga</label>
            <div class="col-md-9 col-sm-9 col-xs-9">
              <input id="telponkel" class="form-control col-md-7 col-xs-12" type="text" name="telponkel" placeholder="Telepon Keluarga" value="@yield('telponkel')">
            </div>
          </div>

        </div> <!-- div class="box-body" -->
      </div> <!-- div class="box box-primary" -->
    </div> <!-- div class="col-md-6 col-sm-12 col-xs-12" -->

</div> <!-- div class="row" -->

<div class="row">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box">
      <div class="box-body">
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

    // ===
      <?php if(!empty($pasiens->TPasien_Prov)){ ?>
        var kdProv = {{$pasiens->TPasien_Prov}}

        getKota({{$pasiens->TPasien_Kota}}, {{$pasiens->TPasien_Kecamatan}}, {{$pasiens->TPasien_Kelurahan}});
      <?php } ?>
    // ===

    var umurThn = $('#pasienumurthn').val();

    if(umurThn == 'NaN' || umurThn == ''){
      $('#pasienumurthn').val('0');
      $('#pasienumurbln').val('0');
      $('#pasienumurhari').val('0');
    }

  });

  hitungUmurInsertForm('single_cal1', 'pasienumurthn', 'pasienumurbln', 'pasienumurhari');

</script>
     
@endsection

