@extends('layouts.main')

@section('title', Auth::User()->cPanel->TCpanel_AppName.' | Poli')

@section('content_header', 'Laporan Harian Rawat Jalan')

@section('header_description', '')

@section('menu_desc', 'Poli')

@section('link_menu_desc', '/poli')

@section('sub_menu_desc', 'View')

@section('content')

@include('Partials.message')

<?php date_default_timezone_set("Asia/Bangkok"); ?>

<div class="row">

  <form action="/ctklapharianjalan" method="post" id="formorderpembelian" data-parsley-validate >

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
                  <input type="text" name="searchkey2" id="searchkey2" class="form-control pull-right" placeholder="Nomor Poli / Nomor RM / Nama Pasien">
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
  <script src="{{ asset('plugins/jQuery/jquery-2.2.3.min.js') }}"></script>

  <!-- Auto Complete Search Asset -->
  <script src="{{ asset('js/jquery-1.10.2.js') }}"></script>
  <script src="{{ asset('js/jquery-ui.js') }}"></script>

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
                  +'<th class="column-title" width="10%">No.Trans</th>'
                  +'<th class="column-title" width="10%">Tanggal</th>'
                  +'<th class="column-title" width="15%">Nomor RM</th>'
                  +'<th class="column-title" width="20%">Nama Pasien</th>'
                  +'<th class="column-title" width="15%">Unit</th>'
                  +'<th class="column-title" width="20%">Dokter</th>'
                  +'<th class="column-title" width="10%">Jumlah</th>'
                +'</tr>';

          $.get('/ajax-getpendaftaranpoli?key1='+key1+'&key2='+key2, function(data){

            if(data.length > 0){
              $.each(data, function(index, listpoliObj){
                isiData += '<tr>'
                      +'<td class="" width="10%">'+listpoliObj.TRawatJalan_NoReg+'</td>'
                          +'<td class="" >'+listpoliObj.TRawatJalan_Tanggal+'</td>'
                          +'<td class="" >'+listpoliObj.TPasien_NomorRM+'</td>'
                          +'<td class="" >'+listpoliObj.TPasien_Nama+'</td>'
                          +'<td class="" >'+listpoliObj.TUnit_Nama+'</td>'
                          +'<td class="" >'+listpoliObj.TPelaku_NamaLengkap+'</td>'
                          +'<td class="" >'+formatRibuan(listpoliObj.TRawatJalan_Jumlah)+'</td>'
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