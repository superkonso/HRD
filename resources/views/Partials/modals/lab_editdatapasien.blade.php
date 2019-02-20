  <div class="modal fade" id="lab_edit" data-backdrop="false" tabindex="-1" role="dialog" aria-labelledby="lab_edit" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form id="frmlab_edit" action="#">
        <div class="modal-header alert-info">
            <img src="{!! asset('images/icon/pasien-icon.png') !!}" width="20" height="20"> Edit Identitas Pasien Laboratorium
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>
        <div class="modal-body" style="max-height: 400px; overflow-x: scroll; overflow-y: scroll;">
        
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="ind" id="ind" value="">
            <input type="hidden" name="arrUpdateData" id="arrUpdateData" value="">
            <input type="hidden" name="nmTemp" id="nmTemp" value="">
            <input type="hidden" name="databaru" id="databaru" value="1">

              <div class="row font-medium">
                <div class="form-group col-md-12 col-sm-12 col-xs-12">
                    <div class="input-group" style="margin-bottom: 5px;">
                      <div class="input-group-addon" style="background-color: #167F92;">
                                <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                      </div>
                      <input type="text" name="mtgl" class="form-control pull-right" id="mtgl" value="<?php echo date('m/d/Y'); ?>" readonly>
                      <div class="input-group-addon" style="background-color: #167F92;color:white;">
                                 <label for="mlabnomor" class="control-label">Lab</label>
                      </div>
                      <input type="text" name="mlabnomor" class="form-control pull-right" id="mlabnomor" value="<?php echo date('m/d/Y'); ?>" readonly>
                    </div>
                    
                    <div class="input-group" style="margin-bottom: 5px;">
                     <div class="input-group-addon" style="background-color: #167F92;color:white;">
                                <label for="mnorm" class="control-label">RM</label>
                      </div>
                      <input type="text" name="mnorm" class="form-control pull-right" id="mnorm" value="" readonly>
                      <div class="input-group-addon" style="background-color: #167F92;color:white;">
                                 <label for="mnoreg" class="control-label">Reg</label>
                      </div>
                      <input type="text" name="mnoreg" class="form-control pull-right" id="mnoreg" value="" readonly>
                    </div>
                    
                    <div class="input-group" style="margin-bottom: 5px;">
                      <div class="input-group-addon" style="background-color: #167F92;color:white;">
                                 <label for="mnama" class="control-label">Nama</label>
                      </div>
                      <input type="text" name="mnama" class="form-control pull-right" id="mnama" value="">
                    </div>
                    
                    <div class="input-group" style="margin-bottom: 5px;">
                      <div class="input-group-addon" style="background-color: #167F92;color:white;">
                                 <label for="mgender" class="control-label">L/P</label>
                      </div>
                      <input type="text" name="mgender" class="form-control pull-right" id="mgender" value="">
                      <div class="input-group-addon" style="background-color: #167F92;color:white;">
                                 <label for="mumur" class="control-label">Umur</label>
                      </div>
                      <input type="text" name="mtahun" class="form-control pull-right" id="mtahun" value="12">
                      <div class="input-group-addon" style="">
                                 <label for="mtahun" class="control-label">th</label>
                      </div>
                      <input type="text" name="mbulan" class="form-control pull-right" id="mbulan" value="3">
                      <div class="input-group-addon" style="">
                                 <label for="mbulan" class="control-label">bl</label>
                      </div>
                      <input type="text" name="mhari" class="form-control pull-right" id="mhari" value="5">
                      <div class="input-group-addon" style="">
                                 <label for="mhari" class="control-label">hr</label>
                      </div>
                     
                    </div>
                    
                     <div class="input-group" style="margin-bottom: 5px;">
                      <div class="input-group-addon" style="background-color: #167F92;color:white;">
                                 <label for="malamat" class="control-label">Alamat</label>
                      </div>
                      <input type="text" name="malamat" class="form-control pull-right" id="malamat" value="">
                    </div>
                    
                     <div class="input-group" style="margin-bottom: 5px;">
                      <div class="input-group-addon" style="background-color: #167F92;color:white;">
                                 <label for="mkota" class="control-label">Kota</label>
                      </div>
                      <input type="text" name="mkota" class="form-control pull-right" id="mkota" value="">
                    </div>
                    
                     <div class="input-group" style="margin-bottom: 5px;">
                      <div class="input-group-addon" style="background-color: #167F92;color:white;">
                                 <label for="mpengirim" class="control-label">Pengirim</label>
                      </div>
                      <select name="mpengirim" id="mpengirim" class="form-control" > 
                          @foreach($pelakus as $pelaku) 
                            <option value="{{$pelaku->TPelaku_Kode}}">
                            {{$pelaku->TPelaku_NamaLengkap}}</option>              
                          @endforeach 
                      </select>
                    </div>
                    
                </div> {{-- Form Group --}}  
              </div>  {{-- Row --}}  
      
      </div>  {{-- Modal Body --}}  


        <div class="modal-footer alert-dismissable">
         <span id="lab_Btnpilih"></span>
         <button type="submit" id="savemodal" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan </button>
          <button type="button" class="btn btn-danger" data-dismiss="modal"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</button>
        </div>
      </form> 
      </div>
    </div>
  </div>