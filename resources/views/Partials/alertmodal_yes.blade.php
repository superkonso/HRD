<div class="modal fade" id="yes_modalWarning" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="yes_modalWarning" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header alert-info">
            <i class="fa fa-warning"></i> Peringatan
          </button>
        </div>
        <div class="modal-body">
            <span id="yes_msgModal"></span>
        </div>
        <div class="modal-footer">
            <button type="submit" id="btnSubmit" class="btn btn-success" onclick="closeDialog_yes('yes_modalWarning',true)">Ya</button>
            <button type="cancel" id="btnCancel" class="btn btn-danger" onclick="closeDialog_yes('yes_modalWarning',false)">Tidak</button>
            {{--  note untuk function closeDialog_yes(id,boolean) buat sendiri di view masing-masing --}}
        </div>
    </div>
  </div>
</div>