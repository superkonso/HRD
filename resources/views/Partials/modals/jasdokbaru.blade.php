
<div class="modal fade" id="jasdokbaru" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="jasdokbaru" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">

        <div class="modal-header alert-info">
            <img src="{!! asset('images/icon/stdket-icon.png') !!}" width="25" height="25"></img> Jasa Dokter Baru
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>

        <div class="modal-body">         
          <input type="hidden" name="isnew" id="isnew" value="0">  
          <div class="form-group">
            <label class="control-label col-md-4 col-sm-3 col-xs-12 " for="jenis">Bulan</label>
            <div class="col-md-4 col-sm-3 col-xs-12">
              <input type="text" name="jbbulan" id="jbbulan" class="form-control col-md-7 col-xs-12" readonly="1">
            </div>
          </div>

          <br><br>
          <div class="form-group">
            <label  class="control-label col-md-4 col-sm-3 col-xs-12" >Dokter</label>
            <div class="col-md-8 col-sm-3 col-xs-12">
              <input type="text" name="jbdokter" id="jbdokter" class="form-control col-md-12 col-xs-12" readonly>
            </div>
          </div>

          <br><br>
          <div class="form-group">
            <label  class="control-label col-md-4 col-sm-3 col-xs-12" >Tanggal</label>
           <div class="col-md-4 col-sm-3 col-xs-12">
              <input type="text" name="jbtanggal" class="form-control col-md-7 col-xs-12" id="jbtanggal" value="">
            </div>
          </div>

          <br><br>
          <div class="form-group">
            <label  class="control-label col-md-4 col-sm-3 col-xs-12" >RM</label>
            <div class="col-md-8 col-sm-3 col-xs-12">
              <input type="text" name="jbrm" id="jbrm" class="form-control col-md-7 col-xs-12" placeholder=" cari nomor rm / nama pasien">
            </div>
          </div>

          <br><br>
          <div class="form-group">
            <label  class="control-label col-md-4 col-sm-3 col-xs-12" >Pasien</label>
            <div class="col-md-8 col-sm-3 col-xs-12">
              <input type="text" name="jbpasien" id="jbpasien" class="form-control col-md-12 col-xs-12">
            </div>
          </div>

          <br><br>
          <div class="form-group">
            <label  class="control-label col-md-4 col-sm-3 col-xs-12" >Jasa</label>
            <div class="col-md-4 col-sm-3 col-xs-12">
              <input type="text" name="jbjasa" id="jbjasa" class="form-control col-md-7 col-xs-12" onchange="changeFormat(this.id, this.value);" onkeyup="changeFormat(this.id, this.value)">
            </div>
          </div>

          <br><br>
          <div class="form-group">
            <label  class="control-label col-md-4 col-sm-3 col-xs-12" >Keterangan</label>
            <div class="col-md-8 col-sm-3 col-xs-12">
              <input type="text" name="jbketerangan" id="jbketerangan" class="form-control col-md-12 col-xs-12">
            </div>
          </div>
          <br><br>
        </div> <!--  <div class="modal-body"> -->

        <div class="modal-footer alert-dismissable">
          <button type="button" id="savemodal" class="btn btn-primary" onclick="simpan($('#isnew').val())" data-dismiss="modal">Simpan</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
        </div>

      </div>
    </div>
  </div>
