  <div class="modal fade" id="stdKeteranganModal" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="StdKeteranganModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="frmStdKeterangan" action="#">
        <div class="modal-header alert-info">
            <img src="{!! asset('images/icon/stdket-icon.png') !!}" width="25" height="25"></img> FORM STD.KETERANGAN
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="mpasien_id" id="mpasien_id" value="">

              <div class="form-group">
                <label for="mstdkode" class="control-label">Kode</label>
                <div class="">
                  <input type="text" class="form-control" name="mstdkode" id="mstdkode" value="" readonly>
                </div>
              </div>

              <div class="form-group">
                <label for="mstdnama" class="control-label">Nama</label>
                <div class="">
                  <input type="text" class="form-control" name="mstdnama" id="mstdnama" value="">
                </div>
              </div>

              <div class="form-group">
                <label for="mstdketerangan" class="control-label">Keterangan</label>
                <div class="">
                  <textarea class="form-control" name="mstdketerangan" id="mstdketerangan" rows="7" style="resize:none;"></textarea>
                </div>
              </div>


        </div> <!--  <div class="modal-body"> -->

        <div class="modal-footer alert-dismissable">
          <button type="submit" id="savemodal" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>

        </form>

      </div>
    </div>
  </div>