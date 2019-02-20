@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Info Telpon')

@section('content_header', 'Info Telpon')

@section('header_description', 'List Info Telpon')

@section('menu_desc', 'infotelpon')

@section('link_menu_desc', '/infotelpon')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row font-medium">
  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
        <div class="box-header">
          <h3 class="box-title">Info Telpon</h3>
              @if(Session::has('flash_message'))
            <div class="alert alert-success"><span class="glyphicon glyphicon-ok"></span><em> {!! session('flash_message') !!}</em></div>
        @endif
          </div>

       <div class="box-body">
          <a href="/infotelpon/create" class="btn btn-success"><img src="{!! asset('images/icon/add-icon.png') !!}" width="20" height="20"> Daftar Baru</a>
          <br><br>

           <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <img src="{!! asset('images/icon/search-icon.png') !!}" width="20" height="20">
                  </div>                  
                  <input type="text" name="searchkey1" id="searchkey1" class="form-control pull-right" placeholder="Nama Pasien/ Kota">
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


  function refreshData(){
      var isiData = '';

      var key  = $('#searchkey1').val();
      
      isiData += '<table class="responstable">';

      isiData += '<tr>'
              +'<th class="column-title">Nama</th>'
              +'<th class="column-title">Alamat</th>'
              +'<th class="column-title">Kota</th>'
              +'<th class="column-title">Telpon</th>'
              +'<th class="column-title">Keterangan</th>'
              +'</tr>';

      $.get('/ajax-getInfotelpon?key='+key, function(data){

        if(data.length > 0){
          $.each(data, function(index, listpoliObj){
            isiData += '<tr>'
                  +'<td class="" width="15%">'+listpoliObj.TInformasi_Nama+'</td>'
                      +'<td class="" width="15%">'+listpoliObj.TInformasi_Alamat+'</td>'
                      +'<td class="" width="10%">'+listpoliObj.TInformasi_Kota+'</td>'
                      +'<td class="" width="5%">'+listpoliObj.TInformasi_Telepon+'</td>'
                      +'<td class="" width="25%">'+listpoliObj.TInformasi_Keterangan+'</td>'
                      +'</td>'
                  +'</tr>';
          });

          isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        }else{

          isiData += '<tr><td colspan="5"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }

        
      });
    
    }

</script>   

@endsection