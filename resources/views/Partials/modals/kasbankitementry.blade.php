<div class="modal fade" id="jurnalkasbankModal" tabindex="-1" role="dialog" aria-labelledby="jurnalkasbankModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

      <div class="modal-header alert-info">
            <img src="{!! asset('images/icon/stdket-icon.png') !!}" width="25" height="25"></img> FORM ENTRY ITEM JURNAL
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      </div>
      
      <div class="modal-body">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="mpasien_id" id="mpasien_id" value="">

              <div class="form-group">
                <label for="mperkkode" class="control-label">Perk.Kode</label>
                <div class="">
                  <input type="text" class="form-control" name="mperkkode" id="mperkkode" value="">
                </div>
              </div>

              <div class="form-group">
                <label for="mperkket" class="control-label">Keterangan</label>
                <div class="">
                  <input type="text" class="form-control" name="mperkket" id="mperkket" value="">
                </div>
              </div>

              <div class="form-group">
                <label for="mkaskredit" class="control-label">Jumlah</label>
                <div class="">
                  <input type="text" class="form-control" name="mkaskredit" id="mkaskredit" value="0" onkeyup="changeFormat(this.id, this.value);">
                </div>
              </div>
      </div> <!--  <div class="modal-body"> -->

      <div class="modal-footer alert-dismissable">
          <button type="button" id="savemodalkas" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>