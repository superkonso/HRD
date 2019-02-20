@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | RS Panel')

@section('content_header', 'RS Panel')

@section('header_description', '')

@section('menu_desc', 'Rspanel')

@section('link_menu_desc', '/rspanel')

@section('sub_menu_desc', 'Edit')

@section('content')

@include('Partials.message')

<?php 
  date_default_timezone_set("Asia/Bangkok"); 
   
 ?> 
    @foreach($trs as $trs)
        
    @endforeach
  
<form class="form-horizontal form-label-left" action="/rspanel/{{$trs->id}}" method="post" id="formrspanel">

{{method_field('PUT')}}

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
                <input type="text" name="plg" id="plg" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_StsPenyelenggara}}">
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
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="tglregis" id="tglregis" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_IjinTanggal}}">
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
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="tglijin" id="tglijin" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_IjinTanggal}}">
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
                    <input type="text" name="sft" id="sft" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_Akreditasi}}">
              </div>
        </div>

        <div class="form-group">
          <label class="control-label col-md-3 col-sm-3 col-xs-3">Status Akreditasi</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="sft" id="sft" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_AkreditStatus}}" >
              </div>
        </div>

        <div class="form-group">
         <label class="control-label col-md-3 col-sm-3 col-xs-3">Tanggal Akreditasi</label>
           <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="tglakred" id="tglakred" class="form-control col-md-7 col-xs-12" value="{{$trs->TRS_AkreditTanggal}}">
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
              
            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Logo Besar</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="logobsr" id="logobsr" class="form-control col-md-7 col-xs-12">
              </div>
            </div>

            <div class="form-group">
              <label class="control-label col-md-3 col-sm-3 col-xs-3">Logo Kecil</label>
              <div class="col-md-8 col-sm-9 col-xs-9">
                <input type="text" name="logokcl" id="logokcl" class="form-control col-md-7 col-xs-12">
              </div>
            </div>
            </div>            
        </div>
     </div>

    <input type="hidden" name="arrItem" id="arrItem" value="">
      <div class="form-group col-md-12 col-sm-12 col-xs-12">
        <div class="box">
          <div class="box-body">
            <div class="col-md-12 col-md-offset-3">
              <button type="submit" class="btn btn-success"><img src="{!! asset('images/icon/save-icon.png') !!}" width="20" height="20"> Simpan</button>
              <a href="/rspanel" class="btn btn-primary"><img src="{!! asset('images/icon/edit-icon.png') !!}" width="20" height="20"> Edit</a>
              <a href="/rspanel" class="btn btn-danger"><img src="{!! asset('images/icon/cancel-icon.png') !!}" width="20" height="20"> Batal</a>
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

    function fillData(){

      
    }

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