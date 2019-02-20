@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Info Tarif')

@section('content_header', 'Info Tarif')

@section('header_description', 'Info Tarif')

@section('menu_desc', 'infotarif')

@section('link_menu_desc', '/infotarif')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row font-medium">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">INFO TARIF PELAYANAN</h3>
              @if(Session::has('flash_message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
              @endif
          </div>

            <div class="input-group">
                    <div class="input-group-addon" style="background-color: #167F92; color:white;">
                      <label for="combo_control" class="control-label">Jenis Tarif</label>
                    </div>
                    <span id="">
                        <select name="status" id="status" class="form-control col-md-7 col-xs-12"> 
                              <option value="1">Tarif Operasi</option>
                              <option value="2">Tarif UGD</option>
                              <option value="3">Tarif Bersalin</option>
                              <option value="4">Tarif Radiologi</option>
                              <option value="5">Tarif Jalan</option>
                              <option value="6">Tarif Laboratorium</option>
                              <option value="7">Tarif Gigi</option>
                              <option value="8">Tarif Inap</option>
                              <option value="9">Tarif Lain</option>
                             </select>
                    </span>
                </div>

              <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
                  </div>
                  <input type="text" name="tarif" id="tarif" class="form-control pull-right" placeholder="Nama Tarif">
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

  $('#status').on('change', function(e){
    refreshData();
  });
  
   $('#tarif').on('keyup', function(e){
         refreshData();
  });


  function refreshData(){
      var isiData = '';
      var key1  = $('#status').val();
      var key2  = $('#tarif').val();

      isiData += '<table class="responstable">';

    if ((key1 == '2') || (key1=='5') || (key1=='7')){
      isiData += '<tr>'
              +'<th class="column-title">Keterangan</th>'
              +'<th class="column-title">Jalan</th>'
              +'</tr>';

        $.get('/ajax-getvtarifoperasi?key1='+key1+'&key2='+key2, function(data){

            if(data.length > 0){
              $.each(data, function(index, listopObj){
                isiData += '<tr>'
                          +'<td class="" width="15%">'+listopObj.Tarif_Nama+'</td>'
                          +'<td class="" width="10%">'+formatRibuan(listopObj.Tarif_Jalan)+'</td>'  
                          +'</td>'
                      +'</tr>';
              });

              isiData += '</table>';
            document.getElementById('tablebody1').innerHTML = isiData;
            }else{

              isiData += '<tr><td colspan="2"><i>Tidak ada Data Ditemukan</i></td></tr>';
              isiData += '<table>';
              document.getElementById('tablebody1').innerHTML = isiData;
            }
            
          });

     }else{
      isiData += '<tr>'
              +'<th class="column-title">Keterangan</th>'
              +'<th class="column-title">VIP</th>'
              +'<th class="column-title">Utama</th>'
              +'<th class="column-title">I</th>'
              +'<th class="column-title">II</th>'
              +'<th class="column-title">III</th>'
              +'<th class="column-title">Jalan</th>'
              +'</tr>';

        $.get('/ajax-getvtarifoperasi?key1='+key1+'&key2='+key2, function(data){

            if(data.length > 0){
              $.each(data, function(index, listopObj){
                isiData += '<tr>'
                          +'<td class="" width="15%">'+listopObj.Tarif_Nama+'</td>'
                          +'<td class="" width="15%">'+formatRibuan(listopObj.Tarif_VIP)+'</td>'
                          +'<td class="" width="10%">'+formatRibuan(listopObj.Tarif_Utama)+'</td>'
                          +'<td class="" width="15%">'+formatRibuan(listopObj.Tarif_Kelas1)+'</td>'
                          +'<td class="" width="15%">'+formatRibuan(listopObj.Tarif_Kelas2)+'</td>'
                          +'<td class="" width="10%">'+formatRibuan(listopObj.Tarif_Kelas3)+'</td>' 
                          +'<td class="" width="10%">'+formatRibuan(listopObj.Tarif_Jalan)+'</td>'  
                          +'</td>'
                      +'</tr>';
              });

          isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        }else{

          isiData += '<tr><td colspan="6"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }

        
      });
    }
    
    }

</script>   

@endsection