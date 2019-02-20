@extends('layouts.main')

@section('title', 'HRD BRIDGE | Tambah Karyawan Baru')

@section('content_header', 'Tambah Karyawan Baru')

@section('header_description', '')

@section('menu_desc', 'Karyawan Baru')

@section('link_menu_desc', '/datakaryawan')

@section('sub_menu_desc', 'Input')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>


<form class="form-horizontal form-label-left" action="/datakaryawan" method="post" id="formdatakaryawan"enctype="multipart/form-data">

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
                        <div class="col-md-6 col-sm-6 col-xs-6">
                          <input type="text" name="nik" id="nik" class="form-control col-md-7 col-xs-12" value="{{$autoNumber}}" readonly>
                        </div>                       
                   </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="nama" id="nama" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Gelar Depan</label>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="gelardepan" id="gelardepan" class="form-control col-md-7 col-xs-12">
                          </div>
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Gelar Belakang</label>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="gelarbelakang" id="gelarbelakang" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tempat / Tgl Lahir</label>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                            <input type="text" name="tempatlahir" id="tempatlahir" class="form-control col-md-7 col-xs-12">
                          </div>
                          <div class="col-md-4 col-sm-4 col-xs-4">
                            <div class="input-group date">
                              <div class="input-group-addon">
                                <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                              </div>
                              <input type="text" name="tgllahir" id="tgllahir" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="alamat" id="alamat" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-3" for="wilayah">Provinsi</label>
                        <div class="col-md-9 col-sm-9 col-xs-9">
                          <select name="provinsi" id="provinsi" class="form-control">
                            @foreach($wilayah1s as $wilayah)
                            <option value="{{$wilayah->TWilayah2_Kode}}">{{$wilayah->TWilayah2_Nama}}</option>
                          @endforeach
                          </select> 
                        </div>
                      </div>

                       <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3" for="wilayah2">Kota</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <select name="kota" id="kota" class="form-control">
                              <option value=""> -- </option>
                            </select>
                          </div> 
                        </div>                       

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3" for="kecamatan">Kecamatan</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <select name="kecamatan" id="kecamatan" class="form-control">
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
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Kode Pos</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="kdpos" id="kdpos" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Telp/ HP</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="telp" id="telp" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Status Keluarga</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <select name="statusklg" id="statusklg" class="form-control">
                              @foreach($admvars as $statusklrg)
                                @if ($statusklrg->TAdmVar_Seri=='KAWIN')
                                  <option value="{{$statusklrg->TAdmVar_Kode}}">{{$statusklrg->TAdmVar_Nama}}</option>
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
                              <div><img width="100px" height="100px" src="{{ asset('images/karyawan/') }}/userdefaultimg.jpg"></img></div><br>
                              <input type="file" class="file" name="foto" id="foto">
                          </div>
                        </div>

                          <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor RM</label>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="nomor" id="nomor" class="form-control col-md-7 col-xs-12">
                          </div>
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Kelas</label>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                             <select name="divisi" id="divisi" class="form-control">
                                <option value="ALL">-</option>
                              <@foreach($kelas as $unit)
                                  <option value="{{$unit->TKelas_Kode}}">{{$unit->TKelas_Nama}}</option>
                              @endforeach
                              </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">KTP</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="ktp" id="ktp" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">NPWP</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="npwp" id="npwp" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">No Polis</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="polis" id="polis" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Kelamin</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="jk" id="jk" class="form-control">
                              @foreach($admvars as $jk)
                                @if ($jk->TAdmVar_Seri=='GENDER')
                                  <option value="{{$jk->TAdmVar_Kode}}">{{$jk->TAdmVar_Nama}}</option>
                                @endif
                              @endforeach
                              </select>
                            </div>
                          </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Gol Darah</label>
                          <div class="col-md-5 col-sm-5 col-xs-5">
                             <select name="goldar" id="goldar" class="form-control">
                              @foreach($admvars as $goldar)
                                @if ($goldar->TAdmVar_Seri=='DARAH')
                                  <option value="{{$goldar->TAdmVar_Kode}}">{{$goldar->TAdmVar_Nama}}</option>
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
                                  <option value="{{$agama->TAdmVar_Kode}}">{{$agama->TAdmVar_Nama}}</option>
                                @endif
                              @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Pendidikan</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="pendidikan" id="pendidikan" class="form-control">
                              <@foreach($karyawanpddk as $pddkn)
                                <option value="{{$pddkn->tkarypddk_Kode}}">{{$pddkn->tkarypddk_Nama}}</option>
                              @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Ijazah</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <input type="text" name="tglijazah" id="tglijazah" class="form-control col-md-7 col-xs-12">
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Pendidikan ket</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <input type="text" name="pddknket" id="pddknket" class="form-control col-md-7 col-xs-12">
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
                                <input type="text" name="tglmasuk" id="tglmasuk" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
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
                                <input type="text" name="tgldiangkat" id="tgldiangkat" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
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
                                <input type="text" name="tglsk" id="tglsk" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                            <div class="col-md-2 col-sm-3 col-xs-3">
                             <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor</label>
                            </div>
                            <div class="col-md-6 col-sm-3 col-xs-3">
                              <input type="text" name="nomorsk" id="nomorsk" class="form-control col-md-7 col-xs-12">
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
                                  <option value="{{$statusjabatan->TAdmVar_Kode}}">{{$statusjabatan->TAdmVar_Nama}}</option>
                                @endif
                              @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Unit/ Profesi</label>
                            <div class="col-md-4 col-sm-9 col-xs-9">
                              <select name="divisi" id="divisi" class="form-control">
                              <option value="ALL">Semua Unit</option>
                              <@foreach($unitprs as $unitprs)
                                  <option value="{{$unitprs->TUnitPrs_Kode}}">{{$unitprs->TUnitPrs_Nama}}</option>
                              @endforeach
                              </select>
                            </div>

                            <div class="col-md-5 col-sm-9 col-xs-9">
                              <select name="profesi" id="profesi" class="form-control">
                              <@foreach($admvars as $jabatan)
                                @if ($jabatan->TAdmVar_Seri=='PROFESI')
                                  <option value="{{$jabatan->TAdmVar_Kode}}">{{$jabatan->TAdmVar_Nama}}</option>
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
                                  <option value="{{$pangkat->TAdmVar_Kode}}">{{$pangkat->TAdmVar_Nama}}</option>
                                @endif
                              @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Pangkat No.SK</label>
                            <div class="col-md-4 col-sm-9 col-xs-9">
                              <input type="text" name="pangkatnosk" id="pangkatnosk" class="form-control pull-right">
                            </div>
                            <label class="control-label col-md-1 col-sm-1 col-xs-1">Tanggal</label>
                            <div class="col-md-4 col-sm-9 col-xs-9">
                               <div class="input-group date">
                                <div class="input-group-addon">
                                  <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                                </div>
                                <input type="text" name="pangkattglsk" id="pangkattglsk" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">Jabatan Struktural</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                              <select name="jbtnstruktural" id="jbtnstruktural" class="form-control">
                              <@foreach($admvars as $jabatan)
                                @if ($jabatan->TAdmVar_Seri=='JABATAN')
                                  <option value="{{$jabatan->TAdmVar_Kode}}">{{$jabatan->TAdmVar_Nama}}</option>
                                @endif
                              @endforeach
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">keterangan</label>
                            <div class="col-md-9 col-sm-9 col-xs-9">
                                <input type="text" name="ketjbtstruktural" id="ketjbtstruktural" class="form-control pull-right">
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
                            <input type="text" onkeyup="changeFormat(this.id, this.value);" name="gajipokok" id="gajipokok" class="form-control col-md-7 col-xs-12" value="0" >
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Jabatan</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" onkeyup="changeFormat(this.id, this.value);" name="tunjabatan" id="tunjabatan" class="form-control col-md-7 col-xs-12" value="0" >
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Fungsional</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" onkeyup="changeFormat(this.id, this.value);" name="tunfungsional" id="tunfungsional" class="form-control col-md-7 col-xs-12" value="0" >
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Keluarga</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" onkeyup="changeFormat(this.id, this.value);" name="tunkeluarga" id="tunkeluarga" class="form-control col-md-7 col-xs-12" value="0" >
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Khusus</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" onkeyup="changeFormat(this.id, this.value);" name="tunkhusus" id="tunkhusus" class="form-control col-md-7 col-xs-12" value="0" >
                          </div>
                        </div>
                        
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Peralihan</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" onkeyup="changeFormat(this.id, this.value);" name="tunperalihan" id="tunperalihan" class="form-control col-md-7 col-xs-12" value="0"  >
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Tunjangan Beras</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="tunberas" id="tunberas" class="form-control col-md-7 col-xs-12" value="0" > Kilogram
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Status Pph</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <select name="statuspph" id="statuspph" class="form-control">
                              <@foreach($admvars as $jabatan)
                                @if ($jabatan->TAdmVar_Seri=='PPH')
                                  <option value="{{$jabatan->TAdmVar_Kode}}">{{$jabatan->TAdmVar_Nama}}</option>
                                @endif
                              @endforeach

                              <option></option>
                            </select>
                          </div>
                        </div>


                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">No Rek Bank</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="norekbank" id="norekbank" class="form-control col-md-7 col-xs-12">
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
                            <input type="text" name="nojamsostek" id="nojamsostek" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">YDP</label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">No.Peserta</label>
                          </div>
                          <div class="col-md-7 col-sm-7 col-xs-7">
                            <input type="text" name="noydp" id="noydp" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">DPLK</label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">No.Peserta</label>
                          </div>
                          <div class="col-md-7 col-sm-7 col-xs-7">
                            <input type="text" name="nodplk" id="nodplk" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="dplkbantuan" id="dplkbantuan" class="form-control col-md-7 col-xs-12"> Bantuan
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="dplkpremi" id="dplkpremi" class="form-control col-md-7 col-xs-12"> Pot. Premi
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Idaman</label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">No.Peserta</label>
                          </div>
                          <div class="col-md-7 col-sm-7 col-xs-7">
                            <input type="text" name="noidaman" id="noidaman" class="form-control col-md-7 col-xs-12">
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3"></label>
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="idamanbantuan" id="idamanbantuan" class="form-control col-md-7 col-xs-12"> Bantuan
                          </div>
                          <div class="col-md-3 col-sm-3 col-xs-3">
                            <input type="text" name="dplkpremi" id="dplkpremi" class="form-control col-md-7 col-xs-12"> Pot. Premi
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">THT</label>
                          <div class="col-md-2 col-sm-3 col-xs-3">
                            <label class="control-label col-md-3 col-sm-3 col-xs-3">No.Peserta</label>
                          </div>
                          <div class="col-md-7 col-sm-7 col-xs-7">
                            <input type="text" name="notht" id="notht" class="form-control col-md-7 col-xs-12">
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
                              <input type="text" name="naikberkalatglsk" id="naikberkalatglsk" class="form-control pull-right" value="<?php echo date('m/d/Y'); ?>">
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor SK</label>
                          <div class="col-md-9 col-sm-9 col-xs-9">
                            <input type="text" name="naikberkalanosk" id="naikberkalanosk" class="form-control col-md-7 col-xs-12">
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
                          <input type="text" onkeyup="changeFormat(this.id, this.value);" name="jumlahpinjam" id="jumlahpinjam" class="form-control col-md-12 col-xs-12" value="0" >
                        </div>

                        <label class="control-label col-md-1 col-sm-3 col-xs-3">Lama</label>
                  
                        <div class="col-md-3 col-sm-3 col-xs-3">
                          <input type="text" name="lamapinjam" id="lamapinjam" class="form-control col-md-12 col-xs-12" value="0" >bln
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3 col-xs-3">Jumlah Angsuran</label>
                  
                        <div class="col-md-3 col-sm-3 col-xs-3">
                          <input type="text" onkeyup="changeFormat(this.id, this.value);" name="angsuran" id="angsuran" class="form-control col-md-12 col-xs-12" value="0" >
                        </div>

                        <label class="control-label col-md-1 col-sm-3 col-xs-3">ke</label>
                  
                        <div class="col-md-3 col-sm-3 col-xs-3">
                          <input type="text" name="angsuranke" id="angsuranke" class="form-control col-md-12 col-xs-12" value="0" >
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