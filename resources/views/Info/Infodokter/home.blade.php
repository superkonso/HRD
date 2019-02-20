@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Info Dokter')

@section('content_header', 'Tarif dan Jadwal Dokter')

@section('header_description', 'Info Dokter')

@section('menu_desc', 'Infotarif')

@section('link_menu_desc', '/Infotarif')

@section('sub_menu_desc', 'Infotarif')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row font-medium">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Info Dokter</h3>
              @if(Session::has('flash_message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
        @endif
          </div>

       <div class="box-body">
               <select name="Daftar" id="Daftar" class="form-control">
                   @foreach($units as $units)
                      <option value="{{$units->TUnit_Kode}}">{{$units->TUnit_Nama}}</option>
                   @endforeach
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

  $('#Daftar').on('change', function(e){
         refreshData();
  });

  $( document ).ready(function() {
    $("#successAlert").fadeTo(2000, 500).slideUp(500, function(){
          $("#successAlert").slideUp(500);
        });  

        refreshData();
  });
  

  function refreshData(){
      var isiData = '';

      var key  = $('#Daftar').val();
      
      isiData += '<table class="responstable">';

      isiData += '<tr>'
              +'<th class="column-title">No</th>'
              +'<th class="column-title">Dokter</th>'
              +'<th class="column-title">Tarif</th>'
              +'<th class="column-title">Senin</th>'
              +'<th class="column-title">Selasa</th>'
              +'<th class="column-title">Rabu</th>'
              +'<th class="column-title">Kamis</th>'
              +'<th class="column-title">Jumat</th>'
              +'<th class="column-title">Sabtu</th>'
              +'<th class="column-title">Minggu</th>'
              +'</tr>';

      $.get('/ajax-getvinfodokter?key='+key, function(data){

        if(data.length > 0){
          var i = 1;
          $.each(data, function(index, listpoliObj){
            n         = i;
            i++;
            isiData += '<tr>'
                  +'<td class="" width="15%">'+n+'</td>'
                      +'<td class="" width="15%">'+listpoliObj.TPelaku_NamaLengkap+'</td>'
                      +'<td class="" width="10%">'+listpoliObj.TPelaku_Tarif+'</td>'
                      +'<td class="" width="10%">'+listpoliObj.Senin+'</td>'
                      +'<td class="" width="5%">'+listpoliObj.Selasa+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.Rabu+'</td>'
                      +'<td class="" width="5%">'+listpoliObj.Kamis+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.Jumat+'</td>'
                      +'<td class="" width="5%">'+listpoliObj.Sabtu+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.Minggu+'</td>'
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