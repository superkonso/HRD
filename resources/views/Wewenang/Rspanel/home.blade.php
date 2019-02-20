@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | RS Panel')

@section('content_header', 'RS Panel')

@section('header_description', '')

@section('menu_desc', 'Rspanel')

@section('link_menu_desc', '/rspanel')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

@foreach($panel as $panel)
       
@endforeach

@foreach($trs as $trs)
       
@endforeach

<?php 
  date_default_timezone_set("Asia/Bangkok");     
  $tglregis   = new DateTime($trs->TRS_TglRegistrasi);
  $tglijin     = new DateTime($trs->TRS_IjinTanggal);
  $tglakred2     = new DateTime($trs->TRS_AkreditTanggal);
 ?> 

<form class="form-horizontal form-label-left" action="/rspanel/{{$trs->id}}" method="post" id="formrspanel">

{{method_field('PUT')}}

{{csrf_field()}}

    {{ Form::hidden('id', $trs->id, array('id' => 'id')) }}
    {{csrf_field()}}

    {{ Form::hidden('tglakred', $trs->TRS_TglRegistrasi, array('id' => 'tgltransaksi')) }}

    <div class="col-md-6 col-sm-12 col-xs-12">
      <div class="box box-primary">        
        <div class="box-body">		 

		     <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kode RS</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kdrs" id="kdrs" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_kode}}">
              </div>
            </div>
         
          <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama Lengkap</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
              <input type="text" name="nmlkp" id="nmlkp" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_NamaLengkap}}">
              </div>
           </div>

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nama Aplikasi</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
              <input type="text" name="nmapp" id="nmapp" class="form-control col-md-7 col-xs-12" value="{{$panel->TCpanel_AppName}}">
              </div>
           </div>    

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat Lengkap</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="textarea" name="alamatpjg" id="alamatpjg" class="form-control" rows="3" value="{{$trs->TRS_AlmLengkap}}">
              </div>
            </div>  
       
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Alamat Pendek</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="textarea" name="alamatpdk" id="alamatpdk" class="form-control" rows="3" value="{{$trs->TRS_AlmPnd}}">
              </div>
            </div> 

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kabupaten</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kbptn" id="kbptn" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Kabupaten}}">      
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kode Pos</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="kdpos" id="kdpos" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_KodePos}}">         
              </div>
            </div>

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Telepon</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="tlp" id="tlp" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Telepon}}">      
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Fax</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="fax" id="fax" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Fax}}">  
              </div>
            </div>

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Telpon Humas</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="tlphumas" id="tlphumas" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Humas}}">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Email</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="email" id="email" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Email}}">
              </div>
            </div>

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Webiste</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="web" id="web" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Website}}">
              </div>
            </div>

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Direktur</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="dirut" id="dirut" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Direktur}}">
              </div>
            </div>

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Penyelenggara</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="plg" id="plg" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Penyelenggara}}">
              </div>
            </div>

             <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Status Penyelenggara</label>
              <div class="col-md-9 col-sm-9 col-xs-9">
                <input type="text" name="sttsplg" id="sttsplg" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_StsPenyelenggara}}">
              </div>
            </div>    

            <div class="item form-group">
                <label for="accessid" class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Logo Besar Warna</label>
                 <div class="col-md-6 col-sm-6 col-xs-12">
                        <div><img width="100px" height="100px" src="@if(is_null($panel->TCpanel_LogoBesarWarna) or $panel->TCpanel_LogoBesarWarna == '') {{ asset('images/panel/') }}/rs-logo-default.png @else {{ asset('images/panel/') }}\{{$panel->TCpanel_LogoBesarWarna}} @endif"></img></div><br>
                 <input type="file" class="file" name="foto" id="foto">
                </div>
            </div>

             <div class="item form-group">
                <label for="accessid" class="control-label col-md-3 col-sm-3 col-xs-12" for="grup"></label>
                 <div class="col-md-6 col-sm-6 col-xs-12">
                        <div><img width="100px" height="100px" src="@if(is_null($panel->TCpanel_LogoBesarBW) or $panel->TCpanel_LogoBesarBW == '') {{ asset('images/panel/') }}/rs-logo-default.png @else {{ asset('images/panel/') }}\{{$panel->TCpanel_LogoBesarBW}} @endif"></img></div><br>
                 <input type="file" class="file" name="logobesarbw" id="logobesarbw">
                </div>
            </div>

        </div> <!-- <div class="box-body"> -->
      </div> <!-- <div class="box box-primary"> -->
    </div> <!-- <div class="col-md-6 col-sm-12 col-xs-12"> -->
    
    <div class="col-md-6 col-sm-12 col-xs-12">
      <!-- ==================== Klinik dan Dokter ================================== -->

      <div class="box box-primary">
      <div class="box-header">  
    
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Kelas RS</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="klsrs" id="klsrs" class="form-control col-md-7 col-xs-12" value="D" >
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Tgl Registrasi RS</label>
              <div class="col-md-5 col-sm-9 col-xs-9" style="margin-right: 0px; padding-right: 0px;">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="tglregis" id="tglregis" class="form-control pull-right" value="{{date_format($tglregis, 'm/d/Y')}}"">
                </div>
              </div>
          </div>

           <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Jenis RS</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="jns" id="jns" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Jenis}}">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Nomor Ijin RS</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="noijin" id="noijin" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_IjinNomor}}">
              </div>
            </div>    
            
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal Ijin</label>
              <div class="col-md-5 col-sm-9 col-xs-9" style="margin-right: 0px; padding-right: 0px;">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div> 
                  <input type="text" name="tglijin" id="tglijin" class="form-control pull-right" " value="{{date_format($tglijin, 'm/d/Y')}}"">
                </div>
              </div>
          </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Ijin Berlaku</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="ijinlaku" id="ijinlaku" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_IjinMasaBerlaku}}">
              </div>
            </div>

          <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Ijin Oleh</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="ijinoleh" id="ijinoleh" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_IjinOleh}}">
              </div>
            </div>
            
         <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Sifat Penetapan</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="sft" id="sft" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_IjinSifat}}">
              </div>
         </div>
         
        <div class="form-group">
            <label class="control-label col-md-3 col-sm-3 col-xs-3">Akreditasi</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                    <input type="text" name="akred" id="akred" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Akreditasi}}">
              </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-3">Status Akreditasi</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="sttsakred" id="sttsakred" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_AkreditStatus}}" >
              </div>
        </div>

        <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal Akreditasi</label>
              <div class="col-md-5 col-sm-9 col-xs-9" style="margin-right: 0px; padding-right: 0px;">
                <div class="input-group date">
                  <div class="input-group-addon">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20"> 
                  </div>
                  <input type="text" name="tglakred2" id="tglakred2" class="form-control pull-right" " value="{{date_format($tglakred2, 'm/d/Y')}}"">
                </div>
              </div>
          </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Tahap Akreditasi</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="tahap" id="tahap" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_AkreditTahap}}" >
              </div>
            </div>
            
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Luas Tanah</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="tnh" id="tnh" class="form-control col-md-7 col-xs-12"  value="{{$trs->TRS_LuasRSTanah}}" >
              </div>
            </div>
            
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Luas Bangunan</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="bangunan" id="bangunan" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_LuasRSBangunan}}">
              </div>
            </div>

            <div class="item form-group">
                <label for="accessid" class="control-label col-md-3 col-sm-3 col-xs-12" for="grup">Logo Kecil</label>
                 <div class="col-md-6 col-sm-6 col-xs-12">
                        <div><img width="100px" height="100px" src="@if(is_null($panel->TCpanel_LogoKecilWarna) or $panel->TCpanel_LogoKecilWarna == '') {{ asset('images/panel/') }}/rs-logo-default.png @else {{ Asset('images/panel/') }}\{{$panel->TCpanel_LogoKecilWarna}} @endif"></img></div><br>
                 <input type="file" class="file" name="logokcl" id="logokcl">
                </div>
            </div>

            <div class="item form-group">
                <label for="accessid" class="control-label col-md-3 col-sm-3 col-xs-12" for="grup"></label>
                 <div class="col-md-6 col-sm-6 col-xs-12">
                        <div><img width="100px" height="100px" src="@if(is_null($panel->TCpanel_LogoKecilBW) or $panel->TCpanel_LogoKecilBW == '') {{ asset('images/panel') }}/rs-logo-default.png @else {{ asset('images/panel/') }}\{{$panel->TCpanel_LogoKecilBW}} @endif"></img></div><br>
                 <input type="file" class="file" name="logokclbw" id="logokclbw">
           </div>            
           </div>
          </div>
         </div>
        </div>            
      </div>
   </div>

    <input type="hidden" name="arrItem" id="arrItem" value="">
      <div class="form-group col-md-12 col-sm-12 col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="col-md-12 col-md-offset-6">
              <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
            </div>
          </div>
        </div>
      </div>

    <!-- ================== -->
  </form>

  @include('Partials.modals.searchmodal')

  @include('Partials.alertmodal')

  <!-- JQuery 1 -->
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

  <!-- Auto Complete Search Asset -->
  <script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

  <!-- Modal Searching Pasien Lama -->
  <script src="{{ asset('js/searchData.js') }}"></script>

  <!-- ============================================= End Of Content ================================================ -->

  @include('Partials.errors')

  <script type="text/javascript">   
  
      $( document ).ready(function() {

    });

    $(function () {

        $('#tglijin').datepicker({
          autoclose: true,
          dateFormat: 'Y-m-d'
        }); 

        $('#tglregis').datepicker({
          autoclose: true,
          dateFormat: 'Y-m-d'
        });  

         $('#tglakred2').datepicker({
          autoclose: true,
          dateFormat: 'Y-m-d'
        });
    }); 

  	function checkFormLain(){
      var noRM        = $('#nama').val();
      var namaPasien  = $('#keterangan').val();

      if(noRM == '' || namaPasien == ''){
        showWarning(2000, '', 'Silahkan lengkapi Data Terlebih Dahulu !', true);
        return false;
      }else if(arrItem.length < 1){
        showWarning(2000, '', 'Data Transaksi Masih Kosong !', true);
        return false;
      }else{
        return true;
      }
    }
  </script>

@endsection