@extends('layouts.main')

@section('title', 'HRD BRIDGE | Edit Data Karyawan')

@section('content_header', 'Edit Data Karyawan')

@section('header_description', '')

@section('menu_desc', 'Karyawan Edit')

@section('link_menu_desc', '/datakaryawan')

@section('sub_menu_desc', 'Update')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); 
      $tgllahir = new DateTime($karyawans->TKaryawan_TglLahir);
      $tglmasukawal = new DateTime($karyawans->TKaryawan_TglMasukAwal);
      $tglmasukdiakui = new DateTime($karyawans->TKaryawan_TglMasukAwal);
      $tgldiangkat = new DateTime($karyawans->TKaryawan_TglDiangkat);
      $tglskpangkat = new DateTime($karyawans->TKaryawan_PangkatTglSK);
      $tglnaikberkala = new DateTime($karyawans->TKaryawan_NaikBerkalaTgl);
?>
  
<form class="form-horizontal form-label-left" action="/datakaryawan/{{$karyawans->id}}" method="post" id="formdatakaryawan" enctype="multipart/form-data">

  {{method_field('PUT')}} 
        <!-- Token -->
  {{csrf_field()}}

  <div class="row">
      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_identitas" data-toggle="tab">Identitas & Kepegawaian</a></li>
              <li><a href="#tab_penggajian" data-toggle="tab">Data Penggajian</a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane active" id="tab_identitas">

              <div class="form-group">
                 <div class="col-md-6 col-sm-6 col-xs-6">
                    <div class="box box-primary">
                      <div class="box-body">
                        
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">NIK</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
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
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Gelar Depan</label>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="gelardepan" id="gelardepan" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_GelarDepan}}">
                          </div>
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Gelar Belakang</label>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="gelarbelakang" id="gelarbelakang" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_GelarBelakang}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">tmp, Tgl Lahir</label>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                            <input type="text" name="tempatlahir" id="tempatlahir" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_TmpLahir}}" required="required">
                          </div>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                            <div class="input-group date">
                              <div class="input-group-addon">
                                <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                              </div>
                              <input type="text" name="tgllahir" id="tgllahir" class="form-control pull-right"  value="<?php echo date_format($tgllahir, 'm/d/Y'); ?>">
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="alamat" id="alamat" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Alamat}}" required="required">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Provinsi</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <select name="provinsi" id="provinsi" class="form-control col-md-7 col-xs-12">
                           @foreach($wilayah1s as $wilayah)
                              @if($wilayah->TWilayah2_Kode == $karyawans->TKaryawan_Prov)
                                <option value="{{$wilayah->TWilayah2_Kode}}" selected="selected">{{$wilayah->TWilayah2_Nama}}</option>
                              @else
                                <option value="{{$wilayah->TWilayah2_Kode}}">{{$wilayah->TWilayah2_Nama}}</option>
                              @endif
                            @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Kota / Kab</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                           <select name="kota" id="kota" class="form-control col-md-7 col-xs-12">
                             @foreach($listkota as $wilayah)
                              @if($wilayah->TWilayah2_Kode == $karyawans->TKaryawan_Kota)
                                <option value="{{$wilayah->TWilayah2_Kode}}" selected="selected">{{$wilayah->TWilayah2_Nama}}</option>
                              @else
                                <option value="{{$wilayah->TWilayah2_Kode}}">{{$wilayah->TWilayah2_Nama}}</option>
                              @endif
                            @endforeach
                            </select>
                          </div>
                        </div>

                         
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Kecamatan</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <select name="kecamatan" id="kecamatan" class="form-control col-md-7 col-xs-12">
                             @foreach($listkecamatan as $wilayah)
                              @if($wilayah->TWilayah2_Kode == $karyawans->TKaryawan_Kec)
                                <option value="{{$wilayah->TWilayah2_Kode}}" selected="selected">{{$wilayah->TWilayah2_Nama}}</option>
                              @else
                                <option value="{{$wilayah->TWilayah2_Kode}}">{{$wilayah->TWilayah2_Nama}}</option>
                              @endif
                            @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Kelurahan</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                           <select name="kelurahan" id="kelurahan" class="form-control col-md-7 col-xs-12">
                             @foreach($listkelurahan as $wilayah)
                              @if($wilayah->TWilayah2_Kode == $karyawans->TKaryawan_Kel)
                                <option value="{{$wilayah->TWilayah2_Kode}}" selected="selected">{{$wilayah->TWilayah2_Nama}}</option>
                              @else
                                <option value="{{$wilayah->TWilayah2_Kode}}">{{$wilayah->TWilayah2_Nama}}</option>
                              @endif
                            @endforeach
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Kode Pos</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="kdpos" id="kdpos" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_KdPos}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Telp</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="telp" id="telp" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Telepon}}" required="required">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Status Keluarga</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <select name="statusklg" id="statusklg" class="form-control">
                              @foreach($admvars as $statusklrg)
                                @if ($statusklrg->TAdmVar_Seri=='KAWIN')
                                  <option value="{{$statusklrg->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryVar_id_Keluarga)) @if ($statusklrg->TAdmVar_Kode==$karyawans->TKaryVar_id_Keluarga) selected="selected" @endif @endif>{{$statusklrg->TAdmVar_Nama}}</option>
                                @endif
                              @endforeach
                            </select>
                          </div>
                        </div>

                      </div> {{-- <div class="box-body"> --}}
                    </div> {{-- <div class="box box-primary"> --}}

                  </div> {{-- <div class="col-md-6 col-sm-6 col-xs-6"> --}}
                  <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="box box-primary">
                        <div class="box-body">

                        <div class="item form-group">
                          <label for="accessid" class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Foto </label>
                          <div class="col-md-6 col-sm-6 col-xs-12">
                              <div><img width="100px" height="100px" src="@if(is_null($karyawansfoto->TKaryFoto_Foto) or $karyawansfoto->TKaryFoto_Foto == '') {{ asset('images/karyawan/') }}/userdefaultimg.jpg @else {{ asset('images/karyawan/') }}\{{$karyawansfoto->TKaryFoto_Foto}} @endif"></img></div><br>
                              <input type="file" class="file" name="foto" id="foto">
                          </div>
                        </div>

                          <div class="form-group">
                           <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor RM</label>
                             <div class="col-md-3 col-sm-3 col-xs-3">
                              <input type="text" name="nomor" id="nomor" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_NomorRM}}">
                            </div>
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Kelas</label>
                            <div class="col-md-3 col-sm-3 col-xs-3">
                              <select name="divisi" id="divisi" class="form-control">
                               @foreach($kelas as $kelas)
                                @if($kelas->TKelas_Kode == $karyawans->TKaryawan_KelasInap)
                                  <option value="{{$kelas->TKelas_Kode}}" selected="selected">{{$kelas->TKelas_Nama}}</option>
                                @else
                                  <option value="{{$kelas->TKelas_Kode}}">{{$kelas->TKelas_Nama}}</option>
                                @endif
                              @endforeach
                              </select>
                            </div>
                        </div>  

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">KTP</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="ktp" id="ktp" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Ktp}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">NPWP</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="npwp" id="npwp" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_Npwp}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">No Polis</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="npwp" id="npwp" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_NoPolis}}">
                          </div>
                        </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Kelamin</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="jk" id="jk" class="form-control">
                                @foreach($admvars as $jk)
                                  @if ($jk->TAdmVar_Seri=='GENDER')
                                    <option value="{{$jk->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryawan_Gender)) @if ($jk->TAdmVar_Kode==$karyawans->TKaryawan_Gender) selected="selected" @endif @endif>{{$jk->TAdmVar_Nama}}</option>
                                  @endif
                                @endforeach
                              </select>
                            </div>
                          </div>

                           <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Gol Darah</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="goldar" id="goldar" class="form-control">
                                @foreach($admvars as $jk)
                                  @if ($jk->TAdmVar_Seri=='DARAH')
                                    <option value="{{$jk->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryawan_GolDar)) @if ($jk->TAdmVar_Kode==$karyawans->TKaryawan_GolDar) selected="selected" @endif @endif>{{$jk->TAdmVar_Nama}}</option>
                                  @endif
                                @endforeach
                              </select>
                            </div>
                          </div>
                        
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Agama</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="agama" id="agama" class="form-control">
                                <@foreach($admvars as $agama)
                                    @if ($agama->TAdmVar_Seri=='AGAMA')
                                      <option value="{{$agama->TAdmVar_Kode}}"  @if(!empty($karyawans->karyagama)) @if ($agama->TAdmVar_Kode==$karyawans->karyagama) selected="selected" @endif @endif>{{$agama->TAdmVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Pendidikan</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="pendidikan" id="pendidikan" class="form-control">
                                <@foreach($admvars as $pddkn)
                                  @if ($pddkn->TAdmVar_Seri=='PENDIDIKAN')
                                      <option value="{{$pddkn->TAdmVar_Kode}}"  @if(!empty($karyawans->karypddk)) @if ($pddkn->TAdmVar_Kode==$karyawans->karypddk) selected="selected" @endif @endif>{{$pddkn->TAdmVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Ijazah</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <input type="text" name="tglijazah" id="tglijazah" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_TglIjasah}}">
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Pendidikan ket</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <input type="text" name="pddknket" id="pddknket" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_PddKet}}">
                            </div>
                          </div>


                        </div> {{-- <div class="box-body"> --}}
                  </div> {{-- <div class="box box-primary"> --}}

                  </div> {{-- <div class="col-md-6 col-sm-6 col-xs-6"> --}}

                    <div class="col-md-12 col-sm-12 col-xs-12">
                      <div class="box box-primary">

                      <div class="box-header">
                        <h3 class="box-title">Status Kepegawaian</h3>
                      </div>

                      <div class="col-md-6 col-sm-6 col-xs-6">
                        <div class="box-body">
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Masuk</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <div class="input-group date">
                                <div class="input-group-addon">
                                  <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                                </div>
                                <input type="text" name="tglmasuk" id="tglmasuk" class="form-control pull-right" value="<?php echo date_format($tglmasukawal, 'm/d/Y'); ?>">
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Diakui</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <div class="input-group date">
                                <div class="input-group-addon">
                                  <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                                </div>
                                <input type="text" name="tglmskdiakui" id="tglmskdiakui" class="form-control pull-right" value="<?php echo date_format($tglmasukdiakui, 'm/d/Y'); ?>">
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Diangkat</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <div class="input-group date">
                                <div class="input-group-addon">
                                  <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                                </div>
                                <input type="text" name="tgldiangkat" id="tgldiangkat" class="form-control pull-right" value="<?php echo date_format($tgldiangkat, 'm/d/Y'); ?>">
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

                          <div class="form-group">

                            <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">SK</label>
                            <div class="col-md-2 col-sm-3 col-xs-3">
                             <label class="control-label col-md-3 col-sm-3 col-xs-3">Pejabat</label>
                            </div>
                            <div class="col-md-6 col-sm-3 col-xs-3">
                             <select name="skpejabat" id="skpejabat" class="form-control">
                                  <option></option>
                              </select>
                            </div>
                            </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                            <div class="col-md-2 col-sm-3 col-xs-3">
                             <label class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal</label>
                            </div>
                            <div class="col-md-6 col-sm-3 col-xs-3">
                             <div class="input-group date">
                                <div class="input-group-addon">
                                  <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                                </div>
                                <input type="text" name="tglsk" id="tglsk" class="form-control pull-right" value="<?php echo date_format($tglskpangkat, 'm/d/Y'); ?>">
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                            <div class="col-md-2 col-sm-3 col-xs-3">
                             <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor</label>
                            </div>
                            <div class="col-md-6 col-sm-3 col-xs-3">
                              <input type="text" name="nomorsk" id="nomorsk" class="form-control col-md-7 col-xs-12"  value="{{$karyawans->TKaryawan_SKNoPengangkatan}}">
                            </div>
                          </div>
                        </div>

                        </div>{{-- <div class="box-body"> --}}
                      </div> 

                      <div class="col-md-6 col-sm-6 col-xs-6">
                        <div class="box-body">
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Status</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="statusjabatan" id="statusjabatan" class="form-control">
                              <@foreach($admvars as $statusjabatan)
                                @if ($statusjabatan->TAdmVar_Seri=='STATUSKAR')
                                      <option value="{{$statusjabatan->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryVar_id_Status)) @if ($statusjabatan->TAdmVar_Kode==$karyawans->TKaryVar_id_Status) selected="selected" @endif @endif>{{$statusjabatan->TAdmVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Unit / Profesi</label>
                            <div class="col-md-4 col-sm-9 col-xs-9">
                              <select name="divisi" id="divisi"  class="form-control">
                               @foreach($unitprs as $unit)
                                @if($unit->TUnitPrs_Kode == $karyawans->TUnitPrs_Kode)
                                  <option value="{{$unit->TUnitPrs_Kode}}" selected="selected">{{$unit->TUnitPrs_Nama}}</option>
                                @else
                                  <option value="{{$unit->TUnitPrs_Kode}}">{{$unit->TUnitPrs_Nama}}</option>
                                @endif
                              @endforeach
                              </select>
                          </div>

                            <div class="col-md-5 col-sm-9 col-xs-9">
                              <select name="profesi" id="profesi" class="form-control">
                              <@foreach($admvars as $jabatan)
                                @if ($jabatan->TAdmVar_Seri=='PROFESI')
                                      <option value="{{$jabatan->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryVar_id_Profesi)) @if ($jabatan->TAdmVar_Kode==$karyawans->TKaryVar_id_Profesi) selected="selected" @endif @endif>{{$jabatan->TAdmVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Pangkat</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="pangkat" id="pangkat" class="form-control">
                              <@foreach($admvars as $pangkat)
                                @if ($pangkat->TAdmVar_Seri=='PANGKAT')
                                      <option value="{{$pangkat->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryawan_Pangkat)) @if ($pangkat->TAdmVar_Kode==$karyawans->TKaryawan_Pangkat) selected="selected" @endif @endif>{{$pangkat->TAdmVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Pangkat No.SK</label>
                            <div class="col-md-4 col-sm-9 col-xs-9">
                              <input type="text" name="pangkatnosk" id="pangkatnosk" class="form-control pull-right" value="{{$karyawans->TKaryawan_PangkatNoSK}}">
                            </div>
                            <label class="control-label col-md-1 col-sm-1 col-xs-1">Tanggal</label>
                            <div class="col-md-4 col-sm-9 col-xs-9">
                               <div class="input-group date">
                                <div class="input-group-addon">
                                  <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                                </div>
                                <input type="text" name="pangkattglsk" id="pangkattglsk" class="form-control pull-right" value="<?php echo date_format($tglskpangkat, 'm/d/Y'); ?>">
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Jabatan Struktural</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="jbtnstruktural" id="jbtnstruktural" class="form-control">
                              <@foreach($admvars as $jabatan)
                                @if ($jabatan->TAdmVar_Seri=='JABATAN')
                                      <option value="{{$jabatan->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryawan_JbtStruktural)) @if ($jabatan->TAdmVar_Kode==$karyawans->TKaryawan_JbtStruktural) selected="selected" @endif @endif>{{$jabatan->TAdmVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">keterangan</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                                <input type="text" name="ketjbtstruktural" id="ketjbtstruktural" class="form-control pull-right" value="{{$karyawans->TKaryawan_JbtStrukturalKet}}" >
                            </div>
                          </div>


                          </div>{{-- <div class="box-body"> --}}
                      </div> 


                    </div> {{-- <div class="box box-primary"> --}}

                  </div> {{-- <div class="col-md-6 col-sm-6 col-xs-6"> --}}

                </div> {{-- <div class="form-group"> --}}
            </div> {{-- <div class="tab-pane active" id="tab_perawatan"> --}}

            <div class="tab-pane" id="tab_penggajian">
              <div class="form-group">
                 <div class="col-md-6 col-sm-6 col-xs-6">
                    <div class="box box-primary">

                      <div class="box-header">
                        <h3 class="box-title">ANGKA KOMPONEN GAJI</h3>
                      </div>

                      <div class="box-body">
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Gaji Pokok</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="gajipokok" id="gajipokok" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_AKGajiPokok}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Jabatan</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="tunjabatan" id="tunjabatan" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_AKJabatan}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Funsional</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="tunfungsional" id="tunfungsional" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_AKFungsi}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Keluarga</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="tunkeluarga" id="tunkeluarga" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_AKKeluarga}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Khusus</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="tunkhusus" id="tunkhusus" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_AKKhusus}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Peralihan</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="tunperalihan" id="tunperalihan" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_TunjPeralihan}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Beras</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="tunberas" id="tunberas" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_TunjBeras}}"> Kilogram
                          </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Status Pph</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="statuspph" id="statuspph" class="form-control">
                              <@foreach($admvars as $jabatan)
                                @if ($jabatan->TAdmVar_Seri=='PPH')
                                      <option value="{{$jabatan->TAdmVar_Kode}}"  @if(!empty($karyawans->TKaryVar_id_StatusPPH)) @if ($jabatan->TAdmVar_Kode==$karyawans->TKaryVar_id_StatusPPH) selected="selected" @endif @endif>{{$jabatan->TAdmVar_Nama}}</option>
                                    @endif
                                @endforeach
                              </select>
                            </div>
                          </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">No Rek Bank</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="norekbank" id="norekbank" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_RekBank}}">
                          </div>
                        </div>

                      </div> {{-- <div class="box-body"> --}}
                    </div> {{-- <div class="box box-primary"> --}}

                  </div> {{-- <div class="col-md-6 col-sm-6 col-xs-6"> --}}
                  <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="box box-primary">

                      <div class="box-header">
                        <h3 class="box-title">ASURANSI DANA PENSIUN</h3>
                      </div>

                      <div class="box-body">
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Jamsostek</label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">No.Peserta</label>
                          </div>
                          <div class="col-md-7 col-sm-7 col-xs-7">
                            <input type="text" name="nojamsostek" id="nojamsostek" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_JamsostekNo}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">YDP</label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">No.Peserta</label>
                          </div>
                          <div class="col-md-7 col-sm-7 col-xs-7">
                            <input type="text" name="noydp" id="noydp" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_YDPNo}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">DPLK</label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">No.Peserta</label>
                          </div>
                          <div class="col-md-7 col-sm-7 col-xs-7">
                            <input type="text" name="nodplk" id="nodplk" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_DPLKNo}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="dplkbantuan" id="dplkbantuan" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_DPLKBantuan}}"> Bantuan
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="dplkpremi" id="dplkpremi" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_DPLKPremi}}"> Pot. Premi
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Idaman</label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">No.Peserta</label>
                          </div>
                          <div class="col-md-7 col-sm-7 col-xs-7">
                            <input type="text" name="noidaman" id="noidaman" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_IdamanNo}}">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="idamanbantuan" id="idamanbantuan" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_IdamanBantuan}}"> Bantuan
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="dplkpremi" id="dplkpremi" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_DPLKPremi}}"> Pot. Premi
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">THT</label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">No.Peserta</label>
                          </div>
                          <div class="col-md-7 col-sm-7 col-xs-7">
                            <input type="text" name="notht" id="notht" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_THTNo}}">
                          </div>
                        </div>

                      </div> {{-- <div class="box-body"> --}}
                    </div> {{-- <div class="box box-primary"> --}}

                  </div> {{-- <div class="col-md-6 col-sm-6 col-xs-6"> --}}

                  <div class="col-md-12 col-sm-6 col-xs-6"></div>
                  <div class="col-md-6 col-sm-6 col-xs-6">
                    <div class="box box-primary">

                      <div class="box-header">
                        <h3 class="box-title">KENAIKAN BERKALA (Terakhir) </h3>
                      </div>

                      <div class="box-body">
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal</label>
                          <div class="col-md-6 col-sm-3 col-xs-3">
                           <div class="input-group date">
                              <div class="input-group-addon">
                                <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                              </div>
                              <input type="text" name="naikberkalatglsk" id="naikberkalatglsk" class="form-control pull-right"  value="<?php echo date_format($tglnaikberkala, 'm/d/Y'); ?>">
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor SK</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="naikberkalanosk" id="naikberkalanosk" class="form-control col-md-7 col-xs-12" value="{{$karyawans->TKaryawan_NaikBerkalaNoSK}}">
                          </div>
                        </div>

                      </div> {{-- <div class="box-body"> --}}
                    </div> {{-- <div class="box box-primary"> --}}

                  </div> {{-- <div class="col-md-6 col-sm-6 col-xs-6"> --}}
                  <div class="col-md-6 col-sm-6 col-xs-6">
                      <div class="box box-primary">

                      <div class="box-header">
                        <h3 class="box-title">ANGSURAN PINJAMAN BANK</h3>
                      </div>

                      <div class="box-body">
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-3">Jumlah Pinjaman</label>
                  
                        <div class="col-md-3 col-sm-3 col-xs-3">
                          <input type="text" name="jumlahpinjam" id="jumlahpinjam" class="form-control col-md-12 col-xs-12" value="{{$karyawans->TKaryawan_Bank1Pinjam}}">
                        </div>

                        <label class="control-label col-md-1 col-sm-3 col-xs-3">Lama</label>
                  
                        <div class="col-md-3 col-sm-3 col-xs-3">
                          <input type="text" name="lamapinjam" id="lamapinjam" class="form-control col-md-12 col-xs-12" value="{{$karyawans->TKaryawan_Bank1Lama}}">bln
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-3">Jumlah Angsuran</label>
                  
                        <div class="col-md-3 col-sm-3 col-xs-3">
                          <input type="text" name="angsuran" id="angsuran" class="form-control col-md-12 col-xs-12" value="{{$karyawans->TKaryawan_Bank1Terakhir}}">
                        </div>

                        <label class="control-label col-md-1 col-sm-3 col-xs-3">ke</label>
                  
                        <div class="col-md-3 col-sm-3 col-xs-3">
                          <input type="text" name="angsuranke" id="angsuranke" class="form-control col-md-12 col-xs-12" value="{{$karyawans->TKaryawan_Bank1Terakhir}}">
                        </div>
                      </div>

                      </div> {{-- <div class="box-body"> --}}
                    </div> {{-- <div class="box box-primary"> --}}

                  </div> {{-- <div class="col-md-6 col-sm-6 col-xs-6"> --}}

                </div> {{-- <div class="form-group"> --}}
            </div> {{-- <div class="tab-pane" id="tab_diagnostik"> --}}

          </div> {{-- <div class="tab-content"> --}}
        </div>
      </div>

      <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="col-md-12 col-md-offset-5">
              <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20">Simpan</button>
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
          $('#tgllahir, #tglmasuk, #tglmskdiakui, #tgldiangkat, #naikberkalatglsk, #pangkattglsk, #tglsk').datepicker({
            autoclose: true,
            dateFormat: 'm/d/Y'
          });
      });

      $(function() {
        $("body").delegate("#tglmasuk", "focusin", function(){
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

      var karymasuk    = $('#tglmasuk').val();;
      var tgllapor= newtoday; 
      karymasuk   = (karymasuk == '' ? nowDate : new Date(karymasuk));
      tgllapor    = (tgllapor == '' ? nowDate : new Date(tgllapor));

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
</script>

@endsection