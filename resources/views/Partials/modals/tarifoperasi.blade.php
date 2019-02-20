
  <div class="modal fade" id="tindakanOp" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="tindakanOp" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header alert-info">
            <img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Jenis / Nama Operasi
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" style="max-height: 400px; overflow-x: scroll; overflow-y: scroll;">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="ind" id="ind" value="">
            <input type="hidden" name="kdTemp" id="kdTemp" value="">
            <input type="hidden" name="nmTemp" id="nmTemp" value="">
            <input type="hidden" name="databaru" id="databaru" value="1">
            <input type="hidden" id="opke" name="opke" value="1">

            <span id="messageModal"></span>

            <div class="form-group">
              <label for="mspesial" class="control-label col-md-3 col-sm-3 col-xs-3">Spesialisasi</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mspesial" id="mspesial" class="form-control col-md-7 col-xs-12">
                  <option value=""><i>Pilih Spesialisasi</i></option>                  
                  @foreach($rmvar as $anas) 
                    @if ($anas->TRMVar_Seri=='OPSPEC')
                      <option value="{{$anas->TRMVar_Kode}}">{{$anas->TRMVar_Nama}}</option>
                    @endif 
                  @endforeach
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="mjnsoperasi" class="control-label col-md-3 col-sm-3 col-xs-3">Jenis Operasi</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mjnsoperasi" id="mjnsoperasi" class="form-control col-md-7 col-xs-12">
                  <option value=""><i>Jenis Operasi</i></option>
                  @foreach($rmvar as $anas) 
                    @if ($anas->TRMVar_Seri=='OPJENIS')
                      <option value="{{$anas->TRMVar_Kode}}">{{$anas->TRMVar_Nama}}</option>
                    @endif 
                  @endforeach
                </select>
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="mcito" class="control-label col-md-3 col-sm-3 col-xs-3">Cito / Elektif</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <select name="mcito" id="mcito" class="form-control col-md-7 col-xs-12">
                  <option value="1"><i>CITO</i></option>
                  <option value="0"><i>ELEKTIF</i></option>
                </select>
              </div>
            </div>
            <br>
            <br>
            <div class="form-group">
              <label for="mtgloperasi" class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="mtgloperasi" class="form-control col-md-7 col-xs-12" id="mtgloperasi" value="<?php echo date('m/d/Y'); ?>">
                </div>
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="jamop" class="control-label col-md-3 col-sm-3 col-xs-3">Jam</label>
              <div class="col-md-3 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="jamop1" id="jamop1" value="<?php echo date('H:i'); ?>">
              </div>
              <label for="jamop" class="control-label col-md-3 col-sm-3 col-xs-3" style="text-align: center;">s/d</label>
              <div class="col-md-3 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="jamop2" id="jamop2" value="<?php echo date('H:i', strtotime('+1 hour')); ?>">
              </div>
            </div>
            <br>

            <div class="form-group">
              <label for="mkode" class="control-label col-md-3 col-sm-3 col-xs-3">Kode</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="mkode" id="mkode" placeholder="cari kode operasi">
              </div>
            </div>
            <br>

            <div class="form-group">
              <label for="mnamaoperasi" class="control-label col-md-3 col-sm-3 col-xs-3">Nama Operasi</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" class="form-control col-md-7 col-xs-12" name="mnamaoperasi" id="mnamaoperasi" placeholder="cari nama operasi">
              </div>
            </div>
            <br>          
    
            <div class="form-group">
              <label for="mcatatan" class="control-label col-md-3 col-sm-3 col-xs-3">Catatan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <textarea class="form-control col-md-7 col-xs-12" name="mcatatan" id="mcatatan" ></textarea> 
              </div>
            </div>
            <br>
            <div class="form-group">
              <label for="operasike" class="control-label col-md-3 col-sm-3 col-xs-3">Operasi Ke:</label>
              <div class="col-md-3 col-sm-9 col-xs-9">
                <input type="number" id="opke" name="opke" min="1" max="1000"  value="1">
              </div>
            </div>
          <br>
        </div> 

        <div class="modal-footer alert-dismissable">
         <span id="Btnpilih"></span>
          <button type="button" class="btn btn-danger" data-dismiss="modal"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</button>
        </div>
      </div>
    </div>
  </div>