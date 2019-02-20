@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Daftar UGD')

@section('content_header', 'UGD')

@section('header_description', 'Daftar UGD')

@section('menu_desc', 'Ugddaftar')

@section('link_menu_desc', '/ugddaftar')

@section('sub_menu_desc', 'Create')

@section('content')

@include('Partials.message')

<div class="row">

  <form action="/lapvisitetinddokter" method="post" id="formorderpembelian" data-parsley-validate >

     {{csrf_field()}}

  <div class="form-group col-md-12 col-sm-12 col-xs-12">
    <div class="box box-primary">
      <div class="box-header">          
      </div>

          <div class="box-body">
              <div class="form-group">
                <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input type="text" name="searchkey1" id="searchkey1" class="form-control pull-right" placeholder="Tanggal Transaksi" value="<?php echo date('m/d/Y'); ?>">
                </div>
                <div class="input-group">
                  <div class="input-group-addon" style="background-color: #167F92;">
                    <i class="fa fa-search"></i>
                  </div>
                  <input type="text" name="searchkey2" id="searchkey2" class="form-control pull-right" placeholder="Nomor UGD / Nomor RM / Nama Pasien">
                </div>
                <div class="input-group">
                  <button type="button" onclick="refreshData();" class="btn btn-primary"><img src="{!! asset('images/icon/refresh-icon.png') !!}" width="20" height="20"> Refresh</button> &nbsp;

                    {{-- <button onclick="lapcetakharian()" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</button>  <br> --}}

                    <button type="submit" class="btn btn-primary"><img src="{!! asset('images/icon/print-icon.png') !!}" width="20" height="20"> Print</button>

                </div>
              </div>

              <div style="overflow-x: scroll;">
                <span id="tablebody1"></span>
              </div>

          </div> <!--div class="box-body"-->

    </div> <!--div class="box box-primary"-->
  </div> <!--div class="form-group col-md-12 col-sm-12 col-xs-12"-->

</form>

</div> <!--div class="row"-->

<!-- JQuery 1 -->
<script src="{{ asset('js/jquery.min.js') }}"></script>

<script>
  $(function () {
          $('#searchkey1').datepicker({
            autoclose: true
          })
          .on('changeDate', function(en) {
            refreshData();
          });
      });

  $('#searchkey2').on('keyup', function(e){
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

      var key1  = $('#searchkey1').val();
      var key2  = $('#searchkey2').val();

      isiData += '<table class="responstable">';

      isiData += '<tr>'
              +'<th>No.Trans</th>'
              +'<th>Tanggal</th>'
              +'<th>Nomor RM</th>'
              +'<th>Nama Pasien</th>'
              +'<th>Dokter</th>'
              +'<th>Jumlah</th>'
              +'</tr>';

      $.get('/ajax-getpendaftaranugd?key1='+key1+'&key2='+key2, function(data){

        if(data.length > 0){
          $.each(data, function(index, listugdObj){
            isiData += '<tr>'
                  +'<td width="100px">'+listugdObj.TRawatUGD_NoReg+'</td>'
                      +'<td width="100px">'+listugdObj.TRawatUGD_Tanggal+'</td>'
                      +'<td width="100px">'+listugdObj.TPasien_NomorRM+'</td>'
                      +'<td width="200px">'+listugdObj.TPasien_Nama+'</td>'
                      +'<td width="100px">'+listugdObj.TPelaku_NamaLengkap+'</td>'
                      +'<td width="100px">'+formatRibuan(listugdObj.TRawatUGD_Jumlah)+'</td>'
                       +'</tr>';
              });

          isiData += '</table>';
        document.getElementById('tablebody1').innerHTML = isiData;
        }else{

          isiData += '<tr><td colspan="7"><i>Tidak ada Data Ditemukan</i></td></tr>';
          isiData += '<table>';
          document.getElementById('tablebody1').innerHTML = isiData;
        }       
      });
    }


</script> 

@endsection