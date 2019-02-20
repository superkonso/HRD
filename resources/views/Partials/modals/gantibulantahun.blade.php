<?php 
  date_default_timezone_set("Asia/Bangkok");
  $tahunawal = date('Y');
  $tahunahir = $tahunawal+100;
  $bulannow  = date('m');
  $bulan = array (1 =>   'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober', 'November','Desember');
?>

  <div class="modal fade" id="gantibulantahun" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="gantibulantahun" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="frmgantibulan" action="#">
        <div class="modal-header alert-info">
            <img src="{!! asset('images/icon/stdket-icon.png') !!}" width="25" height="25"></img> Ganti Bulan
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body">           
          <div class="form-group">
            <label class="control-label col-md-4 col-sm-3 col-xs-12 " for="jenis">Bulan</label>
            <div class="col-md-4 col-sm-3 col-xs-12">
                <select name="mbulan" ID="mbulan" class="form-control col-md-4 col-xs-12">
                  @foreach($bulan as $key => $value)
                   <option value="{{$key}}" @if($key == $bulannow) selected="selected" @endif> {{$value}} </option>
                  @endforeach
                </select>
            </div>
          </div>
          <br><br>
          <div class="form-group">
            <label  class="control-label col-md-4 col-sm-3 col-xs-12" for="perkiraandebet">Tahun</label>
             <div class="col-md-4 col-sm-3 col-xs-12">
                  <input type="number" id="mtahun" name="mtahun" min="{{$tahunawal}}" max="{{$tahunahir}}" class="form-control col-md-7 col-xs-12" value="{{$tahunawal}}">
              </div>
          </div>
          <br><br>
        </div> <!--  <div class="modal-body"> -->

        <div class="modal-footer alert-dismissable">
          <button type="button" id="savemodal" class="btn btn-primary" onclick="chooseBulan()" data-dismiss="modal">Simpan</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
        </div>

        </form>

      </div>
    </div>
  </div>