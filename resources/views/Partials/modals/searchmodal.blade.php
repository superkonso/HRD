<div class="modal fade" id="formsearch" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="modalSearch" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header alert-info">
                <span id="searchmodal_Logo"></span> <span id="searchmodal_Title"></span>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <div class="input-group-addon" style="background-color: #167F92; color:white;">
                        <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
                    </div>
                    <span id="searchmodal_Textsearch"></span>
                </div>
                <span id="searchmodal_Search2"></span>
                <span id="searchmodal_Search3"></span>
                <span id="searchmodal_Search4"></span>
                <span id="searchmodal_Search5"></span>
                <div style="max-height: 300px; overflow-x: scroll; overflow-y: scroll;">
                    <div id="hasil"></div>
                </div>
            </div>
            <div class="modal-footer">
                <span id="searchmodal_Btnpilih"></span>
            </div>
        </div>
    </div>
</div>