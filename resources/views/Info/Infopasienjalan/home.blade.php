@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Info Pasien Jalan')

@section('content_header', 'Info Pasien Jalan')

@section('header_description', 'Info Pasien Jalan')

@section('menu_desc', 'infopasienjalan')

@section('link_menu_desc', '/infopasienjalan')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row font-medium">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Info Pasien Jalan</h3>
              @if(Session::has('flash_message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
        @endif
          </div>

       <div class="box-body">
           <div class="input-group">
               <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="searchkey1" id="searchkey1" class="form-control pull-right" placeholder="Tanggal Awal" value="<?php echo date('m/d/Y'); ?>">
                </div>
                <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/calender-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="searchkey2" id="searchkey2" class="form-control pull-right" placeholder="Tanggal Akhir" value="<?php echo date('m/d/Y'); ?>">
                </div>

                <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="searchkey3" id="searchkey3" class="form-control pull-right" placeholder="Nomor Registrasi / Nomor RM / Nama Pasien">
                </div>

              <div class="input-group">
                <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button>
              </div>
          </div>
          <div style="max-height: 400px; overflow-x: scroll; overflow-y: scroll;">
            <span id="tablebody1"></span>
          </div>
    </div> <!--div class="box-body"-->

    </div> <!--div class="box box-primary"-->
  </div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->
</div> <!--div class="row"-->

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>

  $(function () {
          $('#searchkey1, #searchkey2').datepicker({
            autoclose: true
          })
          .on('changeDate', function(en) {
            refreshData();
          });
      });

  $(function () {
          $('#searchkey3').on('keyup', function(e){
         refreshData();
           });

    });

  $( document ).ready(function() {
    $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
        });  

        refreshData();

  });
  

  function refreshData(){
      var isiData = '';
      var key1  = $('#searchkey1').val();
      var key2  = $('#searchkey2').val();
      var key3  = $('#searchkey3').val();
      
      isiData += '<table class="responstable">';

      isiData += '<tr>'
              +'<th class="column-title">Tanggal</th>'
              +'<th class="column-title">No Registrasi</th>'
              +'<th class="column-title">No RM</th>'
              +'<th class="column-title">Nama</th>'
              +'<th class="column-title">Alamat</th>'
              +'<th class="column-title">Unit</th>'
              +'<th class="column-title">Kota</th>'
              +'<th class="column-title">Telpon</th>'
              +'<th class="column-title">Jenis Kelamin</th>'
              +'</tr>';

      $.get('/ajax-getvinfopasienjalan?key1='+key1+'&key2='+key2+'&key3='+key3, function(data){

        if(data.length > 0){
          $.each(data, function(index, listpoliObj){ 
            isiData += '<tr>'
                      +'<td class="" width="15%">'+listpoliObj.TRawatJalan_Tanggal+'</td>'
                      +'<td class="" width="15%">'+listpoliObj.TRawatJalan_NoReg+'</td>'
                      +'<td class="" width="15%">'+listpoliObj.TPasien_NomorRM+'</td>'
                      +'<td class="" width="10%">'+listpoliObj.TPasien_Nama+'</td>'
                      +'<td class="" width="10%">'+listpoliObj.TPasien_Alamat+'</td>'
                      +'<td class="" width="5%">'+listpoliObj.TUnit_Nama+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.TWilayah2_Nama+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.TPasien_HP+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.TAdmVar_Gender+'</td>'
                      +'</td>'
                  +'</tr>';
          });

          isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        }else{

          isiData += '<tr><td colspan="8"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }

        
      });
    
    }

</script>   

@endsection