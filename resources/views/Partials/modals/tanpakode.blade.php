
  <div class="modal fade" id="tanpakode" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="tanpakode" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header alert-info">
            <img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Tarif Tanpa Kode
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" style="max-height: 400px; overflow-x: scroll; overflow-y: scroll;">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <span id="messageModal"></span>

            <div class="form-group">
              <label for="mspesial" class="control-label col-md-3 col-sm-3 col-xs-3">Keterangan</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="mketerangantanpakode" id="mketerangantanpakode" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label for="mjnsoperasi" class="control-label col-md-3 col-sm-3 col-xs-3">Jumlah</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="mjumlahtanpakode" id="mjumlahtanpakode" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            <br>
            
        </div> 

        <div class="modal-footer alert-dismissable">
         <span id="btnpilihtanpakode"></span>
          <button type="button" class="btn btn-danger" data-dismiss="modal"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</button>
        </div>
      </div>
    </div>
  </div>