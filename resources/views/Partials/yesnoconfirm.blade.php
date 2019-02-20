<div class="modal fade" id="YesNoModalConfirm" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="YesNoModalConfirm" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header alert-info">
            <i class="fa fa-warning"></i> Konfirmasi
          </button>
        </div>
        <div class="modal-body">
            <span id="YesNoModalConfirm_msg"></span>
        </div>
        <div class="modal-footer">
            <div id="confirmButtons">
                <a class="btn btn-success" onclick="returnTrueConfirm();"><img src="{!! asset('images/icon/checklist-icon.png') !!}" width="20" height="20"> Ya<span></span></a>
                <a class="btn btn-danger" onclick="returnFalseConfirm();"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Tidak<span></span></a>
            </div>
        </div>
    </div>
  </div>
</div>