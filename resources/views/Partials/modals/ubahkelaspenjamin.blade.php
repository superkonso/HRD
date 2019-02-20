<div class="modal fade" id="ubahkelaspenjaminmodal" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="ubahkelaspenjaminmodal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header alert-info">
                <span><img src="{!! asset('images/icon/penjamin-icon.png') !!}" width="20" height="20"><label> &nbsp; Ubah Kelas / Penjamin Pasien</label></span>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y: scroll;">

                <div class="form-group">
                  <label for="mspesial" class="control-label col-md-3 col-sm-3 col-xs-3">Penjamin</label>
                  <div class="col-md-9 col-sm-9 col-xs-9">
                    <select name="mpenjaminasal" id="mpenjaminasal" class="form-control col-md-7 col-xs-12" disabled>    
                        @foreach($penjamin as $data) 
                            <option value="{{$data->TPerusahaan_Kode}}">{{$data->TPerusahaan_Nama}}</option>
                        @endforeach
                    </select>
                  </div>
                </div>

                <br><br>

                <div class="form-group">
                  <label for="mspesial" class="control-label col-md-3 col-sm-3 col-xs-3">Ganti Penjamin</label>
                  <div class="col-md-9 col-sm-9 col-xs-9">
                    <select name="mpenjaminganti" id="mpenjaminganti" class="form-control col-md-7 col-xs-12">
                        @foreach($penjamin as $data) 
                            <option value="{{$data->TPerusahaan_Kode}}">{{$data->TPerusahaan_Nama}}</option>
                        @endforeach
                    </select>
                  </div>
                </div>

                <br><br>

                <div class="form-group">
                  <label for="mspesial" class="control-label col-md-3 col-sm-3 col-xs-3">Kelas</label>
                  <div class="col-md-9 col-sm-9 col-xs-9">
                    <select name="mkelasganti" id="mkelasganti" class="form-control col-md-7 col-xs-12">

                        <option value="S">Sesuai Kelas</option>

                        @foreach($kelas as $data) 
                            <option value="{{$data->TKelas_Kode}}">{{$data->TKelas_Keterangan}}</option>
                        @endforeach
                    </select>
                  </div>
                </div>

            </div>
            <div class="modal-footer alert-dismissable">
                <span id="mbtnHitungUlang">
                    <button type="button" class="btn btn-success" onclick="prosesHitungUlang();">
                        <img src="{!! asset('images/icon/note-icon.png') !!}" width="20" height="20"> Hitung Ulang
                    </button>
                </span>
                <button type="button" class="btn btn-danger" onclick="hideubahpenjamin();">
                    <img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal
                </button>
            </div>
        </div>
    </div>
</div>