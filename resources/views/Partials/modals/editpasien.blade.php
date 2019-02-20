  <div class="modal fade" id="editPasienModal" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="editPasienModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="frmPasienModal" action="#">
        <div class="modal-header alert-info">
            <img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> EDIT DATA PASIEN
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" style="max-height: 400px; overflow-x: scroll; overflow-y: scroll;">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="mpasien_id" id="mpasien_id" value="">
            {{-- {{ Form::hidden('mpasien_id', '', array('id' => 'mpasien_id')) }} --}}
            <span id="messageModal"></span>

            <div class="form-group">
              <label for="mnomorrm" class="control-label col-md-3 col-sm-3 col-xs-3">Nomor RM</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="mnomorrm" id="mnomorrm" readonly>
              </div>
            </div>
            <br>

            <div class="form-group">
              <label for="mnama" class="control-label col-md-3 col-sm-3 col-xs-3">Nama Pasien</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="mnama" id="mnama">
              </div>
            </div>
            <br>

            <div class="form-group">
              <label for="mpanggilan" class="control-label col-md-3 col-sm-3 col-xs-3">Penggilan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="mpanggilan" id="mpanggilan">
              </div>
            </div>
            <br>

            <div class="form-group">
              <label for="mjk" class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Kelamin</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mjk" id="mjk" class="form-control col-md-7 col-xs-12">
                  <option value="L">Laki-laki</option>
                  <option value="P">Perempuan</option>
                </select>
              </div>
            </div>
            <br>

            <div class="form-group">
              <label for="mtgllahir" class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal Lahir</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="mtgllahir" class="form-control col-md-7 col-xs-12" id="mtgllahir" value="">
                </div>
              </div>
            </div>
            <br>

            <div class="form-group">
              <label for="mprovinsi" class="control-label col-md-3 col-sm-3 col-xs-3">Provinsi</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mprovinsi" id="mprovinsi" class="form-control col-md-7 col-xs-12">
                  @foreach($provinsi as $prov)
                      <option value="{{$prov->TWilayah2_Kode}}">{{$prov->TWilayah2_Nama}}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <br>

            <div class="form-group">
              <label for="mkota" class="control-label col-md-3 col-sm-3 col-xs-3">Kota</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mkota" id="mkota" class="form-control col-md-7 col-xs-12">
                  <option value=""><i>Pilih Kota</i></option>
                </select>
              </div>
            </div>
            <br>
            
            <div class="form-group">
              <label for="mkecamatan" class="control-label col-md-3 col-sm-3 col-xs-3">Kecamatan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mkecamatan" id="mkecamatan" class="form-control col-md-7 col-xs-12">
                  <option value=""><i>Pilih Kecamatan</i></option>
                </select>
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="mkelurahan" class="control-label col-md-3 col-sm-3 col-xs-3">Kelurahan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mkelurahan" id="mkelurahan" class="form-control col-md-7 col-xs-12">
                  <option value=""><i>Pilih Kelurahan</i></option>
                </select>
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="malamat" class="control-label col-md-3 col-sm-3 col-xs-3">Alamat</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="malamat" id="malamat" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="mtelepon" class="control-label col-md-3 col-sm-3 col-xs-3">Telepon</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="mtelepon" id="mtelepon">
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="mtelepon" class="control-label col-md-3 col-sm-3 col-xs-3">No. HP</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="mHP" id="mHP">
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="mjenispasien" class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Pasien</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mjenispasien" id="mjenispasien" class="form-control col-md-7 col-xs-12">
                  @foreach($admvars as $jenispas)
                    @if($jenispas->TAdmVar_Seri=='JENISPAS')
                      <option value="{{$jenispas->TAdmVar_Kode}}">{{$jenispas->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="magama" class="control-label col-md-3 col-sm-3 col-xs-3">Agama</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="magama" id="magama" class="form-control col-md-7 col-xs-12">
                  @foreach($admvars as $agama)
                    @if($agama->TAdmVar_Seri=='AGAMA')
                      <option value="{{$agama->TAdmVar_Kode}}">{{$agama->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach
              </select>
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="mpendidikan" class="control-label col-md-3 col-sm-3 col-xs-3">Pendidikan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mpendidikan" id="mpendidikan" class="form-control col-md-7 col-xs-12">
                  @foreach($admvars as $pendidikan)
                    @if($pendidikan->TAdmVar_Seri=='PENDIDIKAN')
                      <option value="{{$pendidikan->TAdmVar_Kode}}">{{$pendidikan->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="mpekerjaan" class="control-label col-md-3 col-sm-3 col-xs-3">Pekerjaan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mpekerjaan" id="mpekerjaan" class="form-control col-md-7 col-xs-12">
                  @foreach($admvars as $pekerjaan)
                    @if($pekerjaan->TAdmVar_Seri=='PEKERJAAN')
                      <option value="{{$pekerjaan->TAdmVar_Kode}}">{{$pekerjaan->TAdmVar_Nama}}</option>
                    @endif
                  @endforeach
                </select>
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="mkeluarga" class="control-label col-md-3 col-sm-3 col-xs-3">Keluarga</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="mkeluarga" id="mkeluarga" value="">
              </div>
            </div>
          
        </div>
        <hr>
        <div class="modal-footer alert-dismissable">
          <button type="submit" id="savemodal" class="btn btn-primary"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</button>
        </div>

        </form>

      </div>
    </div>
  </div>