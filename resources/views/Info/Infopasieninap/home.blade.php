@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Info Pasien Inap')

@section('content_header', 'Info Pasien Inap')

@section('header_description', 'Info Pasien Inap')

@section('menu_desc', 'infopasieninap')

@section('link_menu_desc', '/infopasieninap')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row font-medium">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Info Pasien Inap</h3>
              @if(Session::has('flash_message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
             @endif
          </div>

          <div class="box-body">
               <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
                  </div>                  
                  <input type="text" name="searchkey1" id="searchkey1" class="form-control pull-right" placeholder="Nama Pasien/ No RM">
          </div>

          <select name="jenis" id="jenis" class="form-control pull-right">
                  <option value="ALL">Semua Data Pasien</option>
                  <option value="1">Pasien Sudah Pulang</option>
                  <option value="0">Masih Menginap</option>
           </select>

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
          $('#searchkey1').on('keyup', function(e){
         refreshData();
           });
    });

  $( document ).ready(function() {
    $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
        });  

        refreshData();
  });
  
    $('#jenis').on('change', function(e){
    refreshData();
  });



  function refreshData(){
      var isiData = '';

      var key   = $('#searchkey1').val();
      var key1  = $('#jenis').val();

      isiData += '<table class="responstable">';

      isiData += '<tr>'
              +'<th class="column-title">No RM</th>'
              +'<th class="column-title">Nama Pasien</th>'
              +'<th class="column-title">Umur</th>'
              +'<th class="column-title">Ruang/Kelas</th>'
              +'<th class="column-title">Kamar</th>'
              +'<th class="column-title">Alamat</th>'
              +'<th class="column-title">Kelurahan</th>'
              +'<th class="column-title">Kota</th>'
              +'<th class="column-title">Telp</th>'
              +'<th class="column-title">Tgl Masuk</th>'
              +'</tr>';

      $.get('/ajax-getvinfopasieninap?key='+key+'&key2='+key1, function(data){

        if(data.length > 0){
          $.each(data, function(index, listpoliObj){
            isiData += '<tr>'
                  +'<td class="" width="15%">'+listpoliObj.TPasien_NomorRM+'</td>'
                      +'<td class="" width="15%">'+listpoliObj.TPasien_Nama+'</td>'
                       +'<td class="" width="15%">'+listpoliObj.TRawatInap_UmurThn+'</td>'
                      +'<td class="" width="10%">'+listpoliObj.TRuang_Nama+'</td>'
                      +'<td class="" width="10%">'+listpoliObj.TTmpTidur_Nama+'</td>'
                      +'<td class="" width="5%">'+listpoliObj.TPasien_Alamat+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.kel+'</td>'
                      +'<td class="" width="5%">'+listpoliObj.Kec+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.TPasien_HP+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.TRawatInap_TglMasuk+'</td>'
                      +'</td>'
                  +'</tr>';
          });

        isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        }else{

          isiData += '<tr><td colspan="10"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }        
      });    
    }

</script>   

@endsection