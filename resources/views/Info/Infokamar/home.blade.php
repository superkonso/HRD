@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Info Kamar')

@section('content_header', 'Info Ruang Perawatan')

@section('header_description', 'Info Kamar')

@section('menu_desc', 'infokamar')

@section('link_menu_desc', '/infokamar')

@section('sub_menu_desc', 'infokamar')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row font-medium">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Info Ruang Perawatan</h3>
              @if(Session::has('flash_message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
              @endif
          </div>

       <div class="box-body">
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

  $( document ).ready(function() {
    $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
        });  

        refreshData();

  });
  

  function refreshData(){
      var isiData = '';
      var key1 ='';
      
      isiData += '<table class="responstable">';

      isiData += '<tr>'
              +'<th class="column-title">Kamar/ Kelas</th>'
              +'<th class="column-title">Harga</th>'
              +'<th class="column-title">Fasilitas</th>'
              +'</tr>';

      $.get('/ajax-getvinforuang?key1='+key1, function(data){

        if(data.length > 0){
          $.each(data, function(index, listruangObj){
            isiData += '<tr>'
                  +'<td class="" width="15%">'+listruangObj.Kamar+'</td>'
                      +'<td class="" width="15%">'+formatRibuan(listruangObj.HargaKamar)+'</td>'
                      +'<td class="" width="10%">'+listruangObj.TKelas_Keterangan+'</td>'
                      +'</td>'
                  +'</tr>';
          });

          isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        }else{

          isiData += '<tr><td colspan="3"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }

        
      });
    
    }

</script>   

@endsection